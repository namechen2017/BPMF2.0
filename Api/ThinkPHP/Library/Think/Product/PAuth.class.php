<?php
namespace Think\Product;
/**
 * 权限认证类
 */

class PAuth
{
    /**
     * 检查权限
     * @param url string    需要验证的规则列表
     * @param uid  int      认证用户的id
     * @return boolean      通过验证返回true;失败返回false
     */
    public function check($url, $uid)
    {   
        // 查询访问的url是在哪个权限节点下
        $authUrl_data = $this->GetUrlOwner($url);
        if(!$authUrl_data) return false;
        // 校验
        $authRoleCheck = $this->checkRoleAuth($authUrl_data,$uid);
        if(!$authRoleCheck) return false;
        return true;
    }

    /*
     * @param action 控制器操作
     * 传入控制器里的方法，读取该方法属于什么模块里增删改查的类别
     * */
    public function GetUrlOwner($action)
    {   
        //具体权限路径
        $auth_conf_field_data = MemcacheCache::authConfField();
         //权限目录级别
        $auth_rule_field_data = MemcacheCache::authRuleField();
        foreach($auth_conf_field_data as $key =>$val){
            if(strtolower($action) == strtolower($key)){
                //如果在权限目录不存在，也是不行的，比如权限目录的内容删除了。
                if($auth_rule_field_data[$auth_conf_field_data[$key]]){
                return array('url'=>$auth_rule_field_data[$auth_conf_field_data[$key]]['auth_address'],'id'=>$auth_conf_field_data[$key]); 
                }
            }
        }
        return false;
    }

    /*
     * @param action 控制器操作
     * uid 用户id 查询到用户所在角色组
     * */
    public function GetUserRole($uid)
    {
        $user_roles = M()
            ->table("tbl_auth_role_user u,tbl_auth_role r")
            ->where("u.`role_id`=r.`id` AND u.`user_id`=$uid AND r.enabled=1")
            ->field("r.id,r.`name`,r.`permissions`,u.`user_id`")->select();
        return $user_roles;
    }

    /*
     * @param   authUrl 请求的url
     * authIds  拥有所有权限节点的id
     * 匹配
     * */
    protected function checkRoleAuth($authUrl_data,$uid)
    {
        //已知URL和用户ID，查找用户是否有权限
        //权限节点表
        $auth_role_field_data = MemcacheCache::authRoleField();
        //权限用户映射表
        $auth_role_user_field_data = MemcacheCache::authRoleUserField();
        //获取当前拥有的权限节点,如果停用，就不能通过
        if($auth_role_field_data[$auth_role_user_field_data[$uid]]['enabled'] ==0) return false; 
        $permissions = $auth_role_field_data[$auth_role_user_field_data[$uid]]['permissions'];
        $permissions_data = explode(",",$permissions);
        //查看是否存在
        if(in_array($authUrl_data['id'], $permissions_data)){
            return true;
        }else{
           return false; 
        }
    }

    /*
     * 用户登陆成功调用 缓存用户权限信息
     * @param user_id 用户id
     * */
    public function cacheUserAuth($uid){
        // 权限缓存初始化 Memcache
        $cache = \Think\Cache::getInstance('Memcache');
        // 查询用户角色
        $roles = $this->GetUserRole($uid);
        // 节点没找到
        if(!$roles) return false;
        $auth  = [];
        $auths = '';
        foreach($roles as $key => $val){
            if(empty($val['permissions']) || $val['permissions'] == null || $val['permissions'] == "")
                continue;
            $auth[] = $val['permissions'];
        }
        // 把所有权限放进一维数组
        foreach($auth as $v){
            $auths .= $v.',';
        }
        if(!$auths) return false;
        $cacheUserAuth = serialize(trim($auths, ','));
        $cache->set('user_'.$uid, $cacheUserAuth);
        return $cacheUserAuth;
    }

    /*
     * 用户登陆之后验证key 和userid
     * @param strid   用户id
     * @param strkey  加密key
     * */
    public function checkKey($strid, $strkey)
    {
        $ids = base64_decode($strid);
        $key = base64_decode($strkey);

        $uids = authcode($ids,'DECODE',md5(C('ENCODE_USERID')));
        if(!$uids){
            return false;
        }
        $keys = explode("_" ,authcode($key,'DECODE',md5(C('ENCODE_KEY'))));
        $login_key = M('auth_user')->where('id = %d',[$uids])->find();
        $token = $login_key['login_key'];
        // 第一个参数加密code 第二时间戳 第三用户id 三重验证
        if($keys[0] != C('ENCODE_KEY_CODE') || $keys[1] < time() || $keys[2] != $uids || $keys[3] != $token){
            return false;
        }
        return $uids;
    }
}