<?php
namespace Opera\Controller;
use Think\Controller;
use Opera\Model;

/**
 * 工作任务单控制器
 * @author name_chen
 * @modify 2017/05/17
 */
class WorkTaskController extends BaseController
{
    
    //工作任务单列表
    public function workList(){
         if (IS_POST) {
           $db_work_task = new \Opera\Model\WorkTaskModel();
           $res = $db_work_task->workList($_POST);
           $this->response($res, 'json');
        } else {
            $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        }
    }
    
    //工作任务单添加
    public function workAdd(){
        
        if (IS_POST) {
           $db_work_task = new \Opera\Model\WorkTaskModel();
           $res = $db_work_task->workAdd($this->loginid,$_POST);
           $this->response($res, 'json');
        } else {
            $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        }
    }
    
    
    //工作任务单编辑
    public function workEdit(){
        
        if (IS_POST) {
           $db_work_task = new \Opera\Model\WorkTaskModel();
           $res = $db_work_task->workdEdit($this->loginid,$_POST);
           $this->response($res, 'json');
        } else {
            $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        }
    }
    
   
    //工作任务单的关闭
    public function workClose(){
        if (IS_POST) {
           $db_work_task = new \Opera\Model\WorkTaskModel();
           $res = $db_work_task->workClose($this->loginid,$_POST);
           $this->response($res, 'json');
        } else {
            $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        }
    }
    
    
    
    
    
    
    
    
    
    
    




    
    
    
    
}