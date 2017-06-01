<?php
namespace Opera\Model;
use Think\Model;

class WorkTaskModel extends Model
{

    protected $tableName = 'work_task';

    
    //获取列表
    public function workList($list_data){
        
        $search = trim($list_data['search']);  
        $id = (int)$list_data['id'];  
        $num = (int)$list_data['num'];
        $page = (int)$list_data['page'];
        if(empty($num))  return array('status' => 102, 'msg' => '查询数量未指定');
       
        //存在papge,代表要查找分页数据。
        $search_where = array();
        if(!empty($search)){
//          $search_where['channel'] = array('like', '%' . $search . '%');     
//          $search_where['type'] = array('like', '%' . $search . '%'); 
//          $search_where['priority'] = array('like', '%' . $search . '%');   
//          
//          //针对常量类型的模糊搜索，获取type_code,获取
//          $search_where['customer_id'] = array('like', '%' . $search . '%');
          $search_where['no'] = array('like', '%' . $search . '%');   
          $search_where['contract_no'] = array('like', '%' . $search . '%');   
          $search_where['title'] = array('like', '%' . $search . '%'); 
          $search_where['content'] = array('like', '%' . $search . '%');
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
        
        $custom_ids = TwoArrayValue($res,'customer_id');
        $custom_data = M('customer')->where(array('id'=>array('in',$custom_ids)))->getField('id,custom_name',true);
        
        //获取常量的内容
        $db_enum_dictionary = new \Home\Model\EnumDictionaryModel();    
        $type_data_return = $db_enum_dictionary->enumCall(array('value'=>'work_type')); 
        $channel_data_return = $db_enum_dictionary->enumCall(array('value'=>'work_channel')); 
        $priority_data_return = $db_enum_dictionary->enumCall(array('value'=>'work_priority')); 
        $status_data_return = $db_enum_dictionary->enumCall(array('value'=>'work_status')); 
        
        
       
        
        $type_data = $type_data_return['value']['detail'];
        $channel_data = $channel_data_return['value']['detail'];
        $priority_data = $priority_data_return['value']['detail'];
        $status_data = $status_data_return['value']['detail'];
        
      
        foreach($res as $key=>$val){
            $res[$key]['type_value'] =  $type_data[$val['type']];  
            $res[$key]['priority_value'] =  $priority_data[$val['priority']];  
            $res[$key]['channel_value'] =  $channel_data[$val['channel']];  
            $res[$key]['status_value'] =  $status_data[$val['status']];  
            $res[$key]['customer_value'] =  $custom_data[$val['customer_id']];  
        }   
         return array('status'=>100,'value'=>$res,'page'=>$page_data);    
       }else{
         return array('status'=>102,'msg'=>'数据不存在');     
       }
       
    }
    
    
    //工单添加
    public function workAdd($uid,$add_data){
        
        $data = array();
        $data['channel'] = $add_data['channel']; //销售渠道
        $data['type'] = $add_data['type'];//产品类型
        $data['customer_id'] = $add_data['customer_id'];//客户id
        $data['priority'] = $add_data['priority'];//优先级
        $data['contract_no'] = trim($add_data['contract_no']);//合同编号
        $data['title'] = trim($add_data['title']);//主题
        $data['content'] = trim($add_data['content']);//任务说明
        $data['create_time'] = date('Y-m-d',time());//任务说明
        $data['creator_id'] = $uid;//创建人
        $data['status'] = 1;//状态

        foreach($data as $key=>$val){
            if(empty($val)){
             return array('status' => 102, 'msg' => $key.' 内容不能为空');    
            }
        }         
        //调用事务，处理关联数据库操作
        $this->startTrans(); 
        $db_sys_rule4no = new \Opera\Model\SysRule4noModel();
        $code_data = $db_sys_rule4no->numberCreate(1); 
        $data['no'] = '';
        if($code_data['status'] == 100){
        $data['no'] =  $code_data['code'];
        }else{
         $this->rollback();
         return array('status' => 102, 'msg' => 'no 调用错误');  
        }
        $res = $this->add($data);
        if($res){
            $this->commit();
            return array('status' => 100);
        }else{
            $this->rollback();
            return array('status' => 102, 'msg' => '添加失败');
        }
    }
    
    
    //编辑工单
    public function workdEdit($uid,$add_data){
        //哪些字段可以编辑，什么程度的可以编辑，
        $id = (int)$add_data['id'];
        $work_task_data = $this->where(array('id'=>$id,'creator_id'=>$uid))->find();
        if(empty($work_task_data)) return array('status' => 102, 'msg' => '非本人，无编辑权限');
        $data = array();
        $data['channel'] = $add_data['channel']; //销售渠道
        $data['type'] = $add_data['type'];//产品类型
        $data['customer_id'] = $add_data['customer_id'];//客户id
        $data['priority'] = $add_data['priority'];//优先级
        $data['contract_no'] = trim($add_data['contract_no']);//合同编号
        $data['title'] = trim($add_data['title']);//主题
        $data['content'] = trim($add_data['content']);//任务说明
        $data['status'] = trim($add_data['status']);//状态
        $data['receive_id'] = (int)$add_data['receive_id'];//分配任务
        $data['assign_remark'] = trim($add_data['assign_remark']);//分配说明
        $data['target_time'] = $add_data['target_time'];//分配时间限定
        $data['remark'] = trim($add_data['remark']);//关闭说明
        
        $data['end_time_star'] = trim($add_data['end_time_star']);//完成时间好评
        $data['speed_star'] = $add_data['speed_star'];//响应速度好评
        $data['quality_star'] = trim($add_data['quality_star']);//完成质量好评
        $data['comment'] = trim($add_data['comment']);//评价文字
        
        
        //去除为空数据
        foreach($data as $key=>$val){
            if(is_null($val)){
                unset($data[$key]);
            }
        }     
        //工单委派,存在委派人，就添加委派时间
        if($data['receive_id']){
         $data['assign_time'] = date('Y-m-d',time());//状态     
        }
        $res = $this->where(array('id'=>$id))->save($data);
        if($res){
            return array('status' => 100,'post'=>$add_data);
        }else{
            return array('status' => 102, 'msg' => '编辑失败','post'=>$add_data);
        }
    }
    
    
    //关闭工单
    public function workClose($uid,$close_data){
        
        $id = (int)$close_data['id'];
        $remark = trim($close_data['remark']);
        if(empty($id) || empty($remark)){
         return array('status' => 102, 'msg' => '数据不全');    
        }
        $work_task_data = $this->where(array('id'=>$id,'creator_id'=>$uid))->find();
        if(empty($work_task_data)) return array('status' => 102, 'msg' => '非本人，无关闭权限');
        $res = $this->where(array('id'=>$id))->save(array('status'=>0,'remark'=>$remark));
        
        if($res){
            return array('status' => 100);
        }else{
            return array('status' => 102, 'msg' => '添加失败');
        }
        
    }
    
    
    
    
    
    
    

}