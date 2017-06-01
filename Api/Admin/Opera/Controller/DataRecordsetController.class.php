<?php
namespace Opera\Controller;
use Think\Controller;
use Opera\Model;
/**
 * 表单数据控制器
 * @author name_chen
 * @modify 2017/05/26
 */

 //查看使用mysql视图，写入直接写入数据库中
class DataRecordsetController extends BaseController
{  
    
    
    //表单数据列表
    public function recordList(){
        
       if(IS_POST){
         $db_data_form = new \Opera\Model\DataRecordsetViewModel();   
         $res = $db_data_form->contentList($_POST);  
         $this->response($res,'json');    
       }else{
          $this->response(['status' => 102,'msg'=>'没有内容'],'json');   
       }
    } 
    
    //数据的写入，是数组型的。需要表单ID，组件名称，组件值，然后查询指定的表组件，判断是否符合要求
    public function recordAdd(){
        
       if(IS_POST){
         $db_data_form = new \Opera\Model\DataRecordsetModel();   
         $res = $db_data_form->contentAdd($this->loginid,$_POST);  
         $this->response($res,'json');    
       }else{
          $this->response(['status' => 102,'msg'=>'没有内容'],'json');   
       }
    } 
    
    
    
   
    
    
    
    
}