<?php
namespace Opera\Model;
use Think\Model;

//流程model，对流程的操作
class ProcessNodeModel extends Model
{

    protected $tableName = 'process_node';

    
    //获取列表
    public function nodeList($list_data){
        
        $search = trim($list_data['search']);  
        $id = (int)$list_data['id'];  
        $num = (int)$list_data['num'];
        $page = (int)$list_data['page'];
        $template_id = (int)$list_data['template_id'];
        if(empty($template_id))  return array('status' => 102, 'msg' => '流程未指定');
        
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
        $map['template_id'] = $template_id;       
        $map['status'] = 1;       
       
        if(!empty($page)){
         $first = $num*($page-1);
         $res = $this->where($map)->limit($first,$num)->order("sort desc")->select(); 
         $count = $this->where($map)->count();
         $page_data['page'] = $page;
         $page_data['count_page'] = ceil($count/$num);
         $page_data['count'] = $count;
        }else{
          $res = $this->where($map)->limit($num)->order('sort desc')->select();
        }
       if($res){ 
         return array('status'=>100,'value'=>$res,'page'=>$page_data);    
       }else{
         return array('status'=>102,'msg'=>'数据不存在');     
       }
       
    }
    
    
    //添加
    public function nodeAdd($add_data){
        
        $data = array();
        $data['name'] = trim($add_data['name']); //销售渠道
        $data['status'] = 1;//状态

        foreach($data as $key=>$val){
            if(empty($val)){
             return array('status' => 102, 'msg' => $key.' 内容不能为空');    
            }
        }         
        $res = $this->add($data);
        if($res){
            return array('status' => 100);
        }else{
            return array('status' => 102, 'msg' => '添加失败');
        }
    }
    
    
    //编辑
    public function nodeEdit($add_data){
        //哪些字段可以编辑，什么程度的可以编辑，
        $id = (int)$add_data['id'];
        $data = array();
        $data['name'] = trim($add_data['name']);
        //去除为空数据
        foreach($data as $key=>$val){
            if(is_null($val)){
                unset($data[$key]);
            }
        }     
        if(empty($data))return array('status' => 102, 'msg' => '编辑内容不存在');
        $res = $this->where(array('id'=>$id))->save($data);
        if($res){
            return array('status' => 100,'post'=>$add_data);
        }else{
            return array('status' => 102, 'msg' => '编辑失败','post'=>$add_data);
        }
    }
    
    
    //关闭
    public function nodeClose($uid,$close_data){
        
        $id = (int)$close_data['id'];
//        $remark = trim($close_data['remark']);
//        if(empty($id) || empty($remark)){
//         return array('status' => 102, 'msg' => '数据不全');    
//        }
//        $work_task_data = $this->where(array('id'=>$id,'creator_id'=>$uid))->find();
//        if(empty($work_task_data)) return array('status' => 102, 'msg' => '非本人，无关闭权限');
        $res = $this->where(array('id'=>$id))->save(array('status'=>0));
        
        if($res){
            return array('status' => 100);
        }else{
            return array('status' => 102, 'msg' => '关闭失败');
        }
        
    }
    
    
    
    
    
    
    

}