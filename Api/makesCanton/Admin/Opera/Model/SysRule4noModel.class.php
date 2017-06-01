<?php
namespace Opera\Model;
use Think\Model;

class SysRule4noModel extends Model
{

    protected $tableName = 'sys_rule4no';
    
    public $p_rule = '/^[A-Z]{3,20}$/';
    public $s_rule = '/^[1-9]{1}$/';

    //根据条件获取内容
    
    
    
    
    //编号列表读取,做模糊搜索该如何做？
    public function ruleList($list_data){
        
        
          $search = trim($list_data['search']);  
          $id = (int)$list_data['id'];  
          $num = (int)$list_data['num'];
          $page = (int)$list_data['page'];
          if(empty($num))  return array('status' => 102, 'msg' => '查询数量未指定');
          //if(empty($page))  return array('status' => 102, 'msg' => '第几页未指定');
          
          //存在papge,代表要查找分页数据。
          $search_where = array();
          if(!empty($search)){
            $search_where['title'] = array('like', '%' . $search . '%');   
            $search_where['prefix'] = array('like', '%' . $search . '%');   
            $search_where['serial'] = array('like', '%' . $search . '%'); 
            $search_where['data_type'] = array('like', '%' . $search . '%');   
            $search_where['_logic'] = 'OR';
            
          }
          
          if($id){ 
              $search_where = array();
              $search_where['id'] = $id;   
          }
          if($search_where){
           $search_where = array($search_where,array('status'=>1));    
          }else{
          $search_where['status'] = 1;        
          }
          
          
          if(!empty($page)){
           $first = $num*($page-1);
           $res = $this->where($search_where)->limit($first,$num)->order("id desc")->select(); 
           $count = $this->where($search_where)->count();
           $page_data['page'] = $page;
           $page_data['count_page'] = ceil($count/$num);
           $page_data['count'] = $count;
          }else{
              
            $res = $this->where($search_where)->limit($num)->order('id desc')->select();
          }
         if($res){ 
           return array('status'=>100,'value'=>$res,'page'=>$page_data);    
         }else{
           return array('status'=>102,'msg'=>'数据不存在');     
         }
    }
    
    //编号规则生成
    public function ruleAdd($add_data){
        $data = array();
        $data['title'] = trim($add_data['title']);
        $data['prefix'] = trim($add_data['prefix']);
        $data['date_type'] = trim($add_data['date_type']);
        $data['serial'] = (int)$add_data['serial'];
        
       
        foreach($data as $key=>$val){
            if(empty($val)){
             return array('status' => 102, 'msg' => $key.' 内容不能为空');    
            }
        }
        if(!preg_match($this->p_rule, $data['prefix'])){
             return array('status' => 102, 'msg' => '请输入3-20位大写字符');
        }
        if(!preg_match($this->s_rule, $data['serial'])){
            return array('status' => 102, 'msg' => '序号位数只能是1-9位');
        }
        if(!in_array($data['date_type'],array('YYYY','YYYYMM','YYYYMMDD'))){
            return array('status' => 102, 'msg' => '只能选择指定的三种日期格式');
        }
        $res = $this->add($data);
        if($res){
            return array('status' => 100);
        }else{
            return array('status' => 102, 'msg' => '添加失败');
        }
    }
    
    //编号规则修改
    public function ruleEdit($edit_data){
        $data = array();
        $data['title'] = trim($edit_data['title']);
        $data['prefix'] = trim($edit_data['prefix']);
        $data['date_type'] = trim($edit_data['date_type']);
        $data['serial'] = (int)$edit_data['serial'];
        $data['id'] = (int)$edit_data['id'];
        
        //查看最大编号是否存在，已经存在的就不能修改了
        $check_code = $this->where(array('id'=>$data['id']))->getField('serial_code');
        if(!empty($check_code)){
          return array('status' => 102, 'msg' => '已生成过编号的规则不可修改');   
        }
        foreach($data as $key=>$val){
            if(empty($val)){  
            return array('status' => 102, 'msg' => $key.' 内容不能为空');  
            }
        }
        if(!preg_match($this->p_rule, $data['prefix'])){
             return array('status' => 102, 'msg' => '请输入3-20位大写字符');
        }
        if(!in_array($data['date_type'],array('YYYY','YYYYMM','YYYYMMDD'))){
            return array('status' => 102, 'msg' => '只能选择指定的三种日期格式');
        }
        if(!preg_match($this->s_rule, $data['serial'])){
            return array('status' => 102, 'msg' => '序号位数只能是1-9位');
        }
        
        $res = $this->where(array('id'=>$data['id']))->save($data);
        if($res){
            return array('status' => 100);
        }else{
            return array('status' => 102, 'msg' => '修改失败');
        }
    }
    
    
    //删除，只能是管理员才能操作
    public function ruleDelete($uid,$id){
        $id = (int)$id;
        if(!$id) return array('status' => 102, 'msg' => 'ID不存在'); 
        $auth_cache = new \Think\Product\MemcacheCache();
        $auth_role = $auth_cache->authRoleUserField();
        if($auth_role[$uid] != 2) return array('status' => 102, 'msg' => '只有管理员才可以删除'); 
        $res = $this->where(array('id'=>$id))->setField('status',0);
        if($res){
         return array('status' => 100);    
        }else{
         return array('status' => 102, 'msg' => '操作失败');     
        }
        
        
    }
    
    //生成编号
    public function numberCreate($id){
        
        $id = (int)$id;
        if(!$id) return array('status' => 102, 'msg' => 'ID不存在'); 
        //根据规则生成
        $number_rule_data = $this->where(array('id'=>$id,'status'=>1))->find();
        if(empty($number_rule_data))return array('status' => 102, 'msg' => '规则不存在'); 
        
        $prefix = $number_rule_data['prefix'];
        if($number_rule_data['date_type'] == 'YYYYMMDD'){
        $time = date('Ymd',time());    
        }else if($number_rule_data['date_type'] == 'YYYYMM'){
        $time = date('Ym',time());        
        }else{
        $time = date('Y',time());        
        }  
        //获取编号
        $num_code = $number_rule_data['serial']; 
        if(empty($number_rule_data['serial_code'])){
        $num = 1;   
        }else{
        $num = $number_rule_data['serial_code'] +1;     
        }
        //生成左侧补0的编号，如果位数已经用完，就自动添加位数
        $code = sprintf("%0{$num_code}d", $num);
        $number_code = $prefix.$time.$code;
        $res = $this->where(array('id'=>$id,'status'=>1))->setField('serial_code',$num);
        if($res){
           return array('status' => 100, 'code' => $number_code);     
        }else{
          return array('status' => 102, 'msg' => '操作失败');   
        }
    }
    
    
    
    
    
    
}