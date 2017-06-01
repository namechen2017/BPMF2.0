<?php
namespace Opera\Controller;
use Think\Controller;
use Opera\Model;
/**
 * 流程控制器
 * @author name_chen
 * @modify 2017/05/17
 */
class ProcessController extends BaseController
{
    //实现真正的MVC的框架技术，控制器只处理数据调用的问题。能够在MODEL层解决的就放在model层解决
    //流程列表
    public function formList(){

          if (IS_POST) {
           $db_process = new \Opera\Model\ProcessModel();
           $res = $db_process->processList($_POST);
           $this->response($res, 'json');
        } else {
            $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        }
        
    }
    
    
    //流程添加
    public function formAdd(){
       if (IS_POST) {
           $db_process = new \Opera\Model\ProcessModel();
           $res = $db_process->processAdd($_POST);
           $this->response($res, 'json');
        } else {
            $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        } 
    }
    
    //流程编辑
    public function formEdit(){
       if (IS_POST) {
           $db_process = new \Opera\Model\ProcessModel();
           $res = $db_process->processEdit($_POST);
           $this->response($res, 'json');
        } else {
            $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        } 
        
    }
    
    //流程业务的添加
    public function formClose(){
        if (IS_POST) {
           $db_process = new \Opera\Model\ProcessModel();
           $res = $db_process->processClose($_POST);
           $this->response($res, 'json');
        } else {
            $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        }
    }
    
    //^^^^^^^^^^流程节点的处理^^^^^^^^^^^^^^
    
    //流程节点列表
    public function nodeList(){
        if (IS_POST) {
           $db_process = new \Opera\Model\ProcessNodeModel();
           $res = $db_process->nodeList($_POST);
           $this->response($res, 'json');
        } else {
            $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        }
        
        
    } 
    
    
    //流程节点添加
    public function nodeAdd(){
        
        
        
    }
    
    
    //流程节点编辑
    public function nodeEdit(){
        
        
    }
    
    
    
    
    
    
    
    
    
    
    
    
    




    
    
    
    
}