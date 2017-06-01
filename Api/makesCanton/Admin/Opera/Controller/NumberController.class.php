<?php
namespace Opera\Controller;
use Think\Controller;
use Opera\Model;

/**
 * 编码生成控制器
 * @author name_chen
 * @modify 2017/05/17
 */
class NumberController extends BaseController
{
   
    //实现真正的MVC的框架技术，控制器只处理数据调用的问题。能够在MODEL层解决的就放在model层解决
    
    //获取当前是哪种规则，调用规则和当前规则的最大编号
    public function listNumberRule(){
        //接收前缀，时间格式，+后三位
        if(IS_POST){
            //检查数据是否正常
            $db_number_rule = new \Opera\Model\SysRule4noModel();
            $res = $db_number_rule->ruleList($_POST);
            $this->response($res, 'json');
        }else{
             $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        } 
    }
    
    //编码规则生成
    public function createNumberRule(){
        //接收前缀，时间格式，+后三位
        if(IS_POST){
            //检查数据是否正常
            $db_number_rule = new \Opera\Model\SysRule4noModel();
            $res = $db_number_rule->ruleAdd($_POST);
            $this->response($res, 'json');
            
        }else{
             $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
            
        } 
    }
    
    //修改编码规则，当有数据生成的时候，编码规则不能修改
    public function editNumberRule(){
        //接收前缀，时间格式，+后三位
        if(IS_POST){
            //检查数据是否正常
            $db_number_rule = new \Opera\Model\SysRule4noModel();
            $res = $db_number_rule->ruleEdit($_POST);
            $this->response($res, 'json');
        }else{
             $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
            
        } 
    }

    //删除编码规则
    public function deleteNumberRule(){
        //接收前缀，时间格式，+后三位
        if(IS_POST){
            //检查数据是否正常
            $db_number_rule = new \Opera\Model\SysRule4noModel();
            $res = $db_number_rule->ruleDelete($this->loginid,$_POST['id']);
            $this->response($res, 'json');
        }else{
             $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        } 
    }
    
    
    
    
}