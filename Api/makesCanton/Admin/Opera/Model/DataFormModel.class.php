<?php
namespace Opera\Model;
use Think\Model;

//流程model，对流程的操作
class DataFormModel extends Model
{

    protected $tableName = 'data_form';

    
    //获取列表
    public function formList($list_data){
        
        $search = trim($list_data['search']);  
        $id = (int)$list_data['id'];  
        $num = (int)$list_data['num'];
        $page = (int)$list_data['page'];
        if(empty($num))  return array('status' => 102, 'msg' => '查询数量未指定');
       
        //存在papge,代表要查找分页数据。
        $search_where = array();
        if(!empty($search)){ 
          $search_where['name'] = array('like', '%' . $search . '%');   
          $search_where['_logic'] = 'OR';
        }else{
           $search_where = '1=1';
        }
        $map['_complex'] = $search_where;
        if($id){ 
            $map = array();
            $map['id'] = $id; 
        }  
        $map['status'] = 1;       
       
        if(!empty($page)){
         $first = $num*($page-1);
         $res = $this->where($map)->limit($first,$num)->order("id desc")->select(); 
         $count = $this->where($map)->count();
         $page_data['page'] = $page;
         $page_data['count_page'] = ceil($count/$num);
         $page_data['count'] = $count;
        }else{
          $res = $this->where($map)->limit($num)->order('id desc')->select();
        }
       if($res){ 
           //获取公共停用，启用状态
           $db_enum_dictionary = new \Home\Model\EnumDictionaryModel();   
           $type_data_return = $db_enum_dictionary->enumCall(array('value'=>'public_enabled')); 
           $type_data = $type_data_return['value']['detail'];
           foreach($res as  $key=>$val){ 
            $res[$key]['enabled_value'] = $type_data[$val['enabled']];   
           } 
         return array('status'=>100,'value'=>$res,'page'=>$page_data);    
       }else{
         return array('status'=>102,'msg'=>'数据不存在');     
       }
       
    }
    
    
    //添加
    public function formAdd($uid,$add_data){
        
        //变量存在判断
        $isset_arr = array('enabled','name','remark');
        foreach($isset_arr as $vol){
            //如果不存在这个变量，就返回false
            if($add_data[$vol] == ''){
             return array('status' => 102, 'msg' => $vol.' 不能为空');        
            }
        }
        //变量为空判断 is_null if 变量 == ‘’ 返回FALSE
        
        $data = array();
        $data['enabled'] = (int)$add_data['enabled'];//状态 
        $data['name'] = trim($add_data['name']); //表单名
        $data['remark'] = trim($add_data['remark']); //备注
        //判断是否有重复的
        $check_data = $this->checkName($data['name']);
        if($check_data['status'] == 102){
        return array('status' => 102, 'msg' =>'表名已存在');          
        }
        
        $data['creator_id'] = $uid;
        $data['created_time'] = date('Y-m-d',time());
        $res = $this->add($data);
        if($res){
            return array('status' => 100);
        }else{
            return array('status' => 102, 'msg' => '添加失败');
        }
    }
    
   
    
    
    //编辑
    public function formEdit($uid,$add_data){
        //什么字段是必须的，什么字段是非必须的
        $data = array();
        $data['name'] = trim($add_data['name']);
        $data['remark'] = trim($add_data['remark']);   
        $data['enabled'] = (int)$add_data['enabled'];  
        $data['id'] = (int)$add_data['id'];  
        //全部不能为空，所以可以去掉为空的，
        foreach($data as $key=> $val){
           //如果不存在这个变量，就返回false
            if($val == ''){
                unset($data[$key]);     
            }
        }
        //关闭必须填备注，必须要有id
        if(!$data['id']) return array('status' => 102, 'msg' => 'id不存在');
        if($data['enabled'] == 0 && !isset($data['remark'])){
            return array('status' => 102, 'msg' => '关闭必须要备注');
        }        
        $data['creator_id'] = $uid;  
        $data['modified_time'] = date('Y-m-d H:i:s',time());  
        
        $res = $this->where(array('id'=>$data['id']))->save($data);
        if($res){
            return array('status' => 100);
        }else{
            return array('status' => 102, 'msg' => '编辑失败');
        }
    }
    
    
    //关闭
    public function formClose($id){
         
        $res = $this->where(array('id'=>$id))->save(array('enabled'=>0));
        if($res){
            return array('status' => 100);
        }else{
            return array('status' => 102, 'msg' => '关闭失败');
        }
        
    }
    
    
     
    //表名验证
    public function checkName($name){
        if($this->where(array('enabled'=>1,'name'=>$name))->find()){
            return array('status' => 102, 'msg' => '表名已存在');
        }else{
            return array('status' => 100);
        }
    }
    
    
    
    

}