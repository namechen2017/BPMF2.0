<?php
namespace Opera\Controller;
use Think\Controller;
use Opera\Model;
/**
 * 表单编辑控制器
 * @author name_chen
 * @modify 2017/05/26
 */

//采用THINKPHP 的模拟视图方式，而不是mysql的视图
//采用视图查询 ，创建视图 CREATE VIEW tbl_data_recordset_view AS SELECT tbl_data_recordset.*,tbl_data_form.`name` as form_name,tbl_data_component.com_type,tbl_data_component.data_type,tbl_data_component.`precision` FROM tbl_data_recordset LEFT JOIN tbl_data_form on tbl_data_recordset.form_id = tbl_data_form.id LEFT JOIN tbl_data_component on tbl_data_recordset.com_id=tbl_data_component.id
 
class PredefinedController extends BaseController
{   
    //数据表单列表
    public function formList(){
        
       if(IS_POST){
         $db_data_form = new \Opera\Model\DataFormModel();   
         $res = $db_data_form->formList($_POST);  
         $this->response($res,'json');    
       }else{
          $this->response(['status' => 102,'msg'=>'没有内容'],'json');   
       }
    } 
    
    //数据表单添加
    public function editAdd(){
       if(IS_POST){
        $db_data_form = new \Opera\Model\DataFormModel();   
        $res = $db_data_form->formAdd($this->loginid,$_POST);     
        $this->response($res,'json');   
       }else{
        $this->response(['status' => 103, 'msg' => '请求失败'],'json');   
       } 
    }
    
    //数据表单修改
    public function editModify(){
       if(IS_POST){
        $db_data_form = new \Opera\Model\DataFormModel();   
        $res = $db_data_form->formEdit($this->loginid,$_POST);     
        $this->response($res,'json');   
       }else{
        $this->response(['status' => 103, 'msg' => '请求失败'],'json');   
       }  
    }
    
    //数据表单失效
    public function editDelete(){
       if(IS_POST){   
        $db_data_form = new \Opera\Model\DataFormModel();   
        $res = $db_data_form->formClose((int)$_POST['id']);     
        $this->response($res,'json');     
       }else{
        $this->response(['status' => 103, 'msg' => '请求失败'],'json');   
       }
    }
       
    //表名验证
    public function editNameCheck(){
        if(IS_POST){
        $db_data_form = new \Opera\Model\DataFormModel();   
        $res = $db_data_form->checkName(trim($_POST['name']));    
        $this->response($res,'json');   
       }else{
        $this->response(['status' => 103, 'msg' => '请求失败'],'json');   
       } 
    }
 
    //修改一个表单的属性排序，传入ID过来，然后倒叙排序
    public function editSort(){
        if(IS_POST){
            $db_data_component = new \Opera\Model\DataComponentModel();
            $res = $db_data_component->sortEdit(trim($_POST['ids']));
            $this->response($res,'json');          
        }else{
            $this->response(['status' => 103, 'msg' => '请求失败'],'json');    
        }  
        
    }

    //编辑器数据
    public function editAttrList(){

        if(IS_POST){
            $db_data_component = new \Opera\Model\DataComponentModel();
            $res = $db_data_component->componentList($_POST);
            $this->response($res,'json');          
        }else{
            $this->response(['status' => 103, 'msg' => '请求失败'],'json');    
        }  
    }
    

    //编辑器内容属性录入
    public function editAttrInster(){
        if(IS_POST){   
        $db_data_component = new \Opera\Model\DataComponentModel();   
        $res = $db_data_component->componentAdd($this->loginid,$_POST);     
        $this->response($res,'json');     
       }else{
        $this->response(['status' => 103, 'msg' => '请求失败'],'json');   
       }
    }
    
    //编辑属性修改
    public function editAttrModify(){
      //获取内容，插入
        if(IS_POST){   
        $db_data_component = new \Opera\Model\DataComponentModel();   
        $res = $db_data_component->componentEdit($this->loginid,$_POST);     
        $this->response($res,'json');     
       }else{
        $this->response(['status' => 103, 'msg' => '请求失败'],'json');   
       }
    }
    
    //编辑属性关闭和开启，
    public function editAttrSwitch(){
        if(IS_POST){   
        $db_data_component = new \Opera\Model\DataComponentModel();   
        $res = $db_data_component->componentClose((int)$_POST['id']);     
        $this->response($res,'json');     
       }else{
        $this->response(['status' => 103, 'msg' => '请求失败'],'json');   
       }       
    }
    
    




    
    
    
    
}