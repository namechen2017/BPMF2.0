<?php

namespace Home\Controller;

use Think\Controller;

/*
 * 权限节点对应的具体访问方法
 */

class AuthConfController extends BaseController {

    //展示此表内容和上级权限表内容
    public function getAuthConf() {

        if (IS_POST) {
            $search = trim(I('search'));
            $num = trim(I('num'));
            $page = trim(I('page'));
            $where = array();
            if (!empty($search)) {
                //查询上级权限表看有没有问题
             //   $where1['auth_address'] = array('like', '%' . $search . '%');
                $where1['name'] = array('like', '%' . $search . '%');
                $where1['_logic'] = 'OR';
                $wheres['status'] = 1;
                $rule_ids = M('auth_rule')->where(array($where1,$wheres))->getField('id', true);
                if (!empty($rule_ids)) {
                    $where['uid'] = array('in', $rule_ids);
                } else {
                    $where['request_address'] = array('like', '%' . $search . '%');
                }
            }

            if (empty($page))
                $page = 1;
            if (empty($num))
                $num = 10;
            $first = $num * ($page - 1);
            $conf_data = M('auth_conf')->where($where)->limit($first, $num)->order("uid desc,id asc")->select();

            if(!empty($conf_data)){
            //获取父级节点,插入父级节点数据
            $uid_ids = TwoArrayValue($conf_data, 'uid');
            $rule_data = M('auth_rule')->where(array('id' => array('in', $uid_ids),'status'=>1))->select();
            foreach ($conf_data as $key => $val) {
                foreach ($rule_data as $vol) {
                    if ($val['uid'] == $vol['id']) {
                        $conf_data[$key]['auth_address'] = $vol['auth_address'];
                        $conf_data[$key]['name'] = $vol['name'];
                    }
                }
            }
            }
            $count = M('auth_conf')->where($where)->count();
            $page_data['page'] = $page;
            $page_data['count_page'] = ceil($count / $num);
            if ($conf_data) {
                $this->response(['status' => 100, 'value' => $conf_data, 'page' => $page_data], 'json');
            } else {
                $this->response(['status' => 102, 'msg' => '内容不存在'], 'json');
            }
        } else {
            $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        }
    }
    
    //父级权限节点的获取
    public function parentAuth(){
        
        if (IS_POST) {
          $search = trim($_POST['search']);  
          $where1['auth_address'] = array('like', '%' . $search . '%');
          $where1['name'] = array('like', '%' . $search . '%');
          $where1['_logic'] = 'OR';
          $wheres['status'] = 1;  
          $wheres['enabled'] = 1; 
          $where = array($where1,$wheres);
        }else{
           $where = array();
           $where['status'] = 1;
           $where['enabled'] = 1; 
        }
       
        $res = M('auth_rule')->where($where)->field("id,auth_address,name")->select();
        if ($res) {
            $this->response(['status' => 100, 'value' => $res], 'json');
        } else {
            $this->response(['status' => 102, 'msg' => '内容不存在'], 'json');
        }
        
    }
    

    //单条查询
    public function findAuthConf() {

        if (IS_POST) {
            $id = I('id');
            $conf_data = M('auth_conf')->where(array('id' => $id))->find();
            $rule_data = M('auth_rule')->where(array('id' => $conf_data['uid'],'status'=>1))->find();
            $conf_data['auth_address'] = $rule_data['auth_address'];
            $conf_data['name'] = $rule_data['name'];
            if ($conf_data) {
                $this->response(['status' => 100, 'value' => $conf_data], 'json');
            } else {
                $this->response(['status' => 102, 'msg' => '内容不存在'], 'json');
            }
        } else {
            $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        }
    }

    //添加的时候必须选择上级权限节点名称，
    public function insertAuthConf() {
        if (IS_POST) {
            $uid = I('uid');
            $r_url = I('request_address');
            if (empty($uid) || empty($r_url))
                $this->response(['status' => 102, 'msg' => '资料不齐全'], 'json');
            $data['uid'] = $uid;
            $data['request_address'] = $r_url;

            if (!M('auth_conf')->where(array('request_address' => $r_url))->find()) {
                $res = M('auth_conf')->add($data);
                if($res){
                     //更新缓存    
                  $memcacheCache = new \Think\Product\MemcacheCache();
                $memcacheCache->flushCache('auth_conf_field');
                $this->response(['status' => 100], 'json');    
                }else{
                  $this->response(['status' => 102, 'msg' => '操作失败'], 'json');
                }
            } else {
                $this->response(['status' => 101, 'msg' => '链接已存在'], 'json');
            }
        } else {
            $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        }
    }

    //可以变更父级的权限节点内容
    public function editAuthConf() {
        if (IS_POST) {
            $data = array();
            $data['uid'] = I('uid');
            $data['request_address'] = I('request_address');
            $data['id'] = I('id');
            foreach ($data as $key => $val) {
                if (empty($val)) {
                    $this->response(['status' => 101, 'msg' => $key . ' 参数不存在'], 'json');
                }
            }
            if (!M('auth_conf')->where(array('request_address' => $data['request_address'], array('id' => array('neq', $data['id']))))->find()) {
                $res = M('auth_conf')->where(array('id' => $data['id']))->save($data);
                if ($res) {
                    //更新缓存    
                    $memcacheCache = new \Think\Product\MemcacheCache();
                    $memcacheCache->flushCache('auth_conf_field');
                    $this->response(['status' => 100], 'json');
                } else {
                    $this->response(['status' => 104, 'msg' => '操作失败'], 'json');
                }
            } else {
                $this->response(['status' => 102, 'msg' => '链接已存在'], 'json');
            }
        } else {
            $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        }
    }

    //可以真实删除，因为这个基本没什么关联
    public function deleteAuthConf() {
        $id = I('id');
        if ($id) {
            $res = M('auth_conf')->where(array('id' => $id))->delete();
            if ($res) {
                //更新缓存    
                $memcacheCache = new \Think\Product\MemcacheCache();
                $memcacheCache->flushCache('auth_conf_field');
                $this->response(['status' => 100], 'json');
            } else {
                $this->response(['status' => 101, 'msg' => '操作失败'], 'json');
            }
        } else {
            $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        }
    }

}
