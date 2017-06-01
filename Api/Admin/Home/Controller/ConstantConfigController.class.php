<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model;
/**
* 常量配置控制器
*/
class ConstantConfigController extends BaseController{

    public $table = 'enum_dictionary';
    
    //常量调用接口
    public function getConstant(){
        //传type_code he key_name两个过来，其中type_code是必填项目
        if($_POST){
          $db_enum_dictionary = new \Home\Model\EnumDictionaryModel();
          $res = $db_enum_dictionary->enumCall($_POST);
          $this->response($res,'json');  
        }else{
           $this->response(['status' => 103, 'msg' => '请求失败'],'json');
        }
    }
    
    //获取一个
    public function ConstantFind(){
        //传type_code he key_name两个过来，只有一个也可以，然后把type_code查询写入
        if($_POST){
            $where['id'] = $_POST['id'];
            $where['status'] = 1;
            $res = M($this->table)->where($where)->find();
            if($res){
               $this->response(['status' => 100,'value'=>$res],'json');   
            }else{
               $this->response(['status' => 102,'msg'=>'数据不存在'],'json');   
            }
        }else{
           $this->response(['status' => 103, 'msg' => '请求失败'],'json');
        }
    }
   
    
    //常量列表
    public function constantList(){
        
        if($_POST){
         $db_enum_dictionary = new \Home\Model\EnumDictionaryModel();
         $res = $db_enum_dictionary->enumList($_POST);
         $this->response($res,'json');     
        }else{
         $this->response(['status' => 103, 'msg' => '请求失败'],'json');   
        } 
        
    }
    
    //常量分类列表，查询有哪些类型的常量
    public function constantType(){
         
         $db_enum_dictionary = new \Home\Model\EnumDictionaryModel();
         if($_POST['search']){
         $res = $db_enum_dictionary->enumGroup($_POST['search']);    
         }else{
         $res = $db_enum_dictionary->enumGroup();    
         }
         $this->response($res,'json');     
    }
    
    
    
    //常量编辑
    public function constantEdit(){
        
        if($_POST){
         $db_enum_dictionary = new \Home\Model\EnumDictionaryModel();
         $res = $db_enum_dictionary->enumEdit($_POST);
         $this->response($res,'json');     
        }else{
         $this->response(['status' => 103, 'msg' => '请求失败'],'json');   
        } 
    }
    
    
    //常量添加
    //客户可以模糊搜索调用，同时加入ID，如果是自己填写，就没有ID
    public function constantAdd(){
        if($_POST){
        $db_enum_dictionary = new \Home\Model\EnumDictionaryModel();
        $res = $db_enum_dictionary->enumAdd($_POST);
        $this->response($res,'json');  
        }else{
        $this->response(['status' => 103, 'msg' => '请求失败'],'json'); 
        }
    }
    
    
    //常量删除
    public function constantDelete(){
        if($_POST['id']){
            $db_enum_dictionary = new \Home\Model\EnumDictionaryModel();
            $res = $db_enum_dictionary->enumClose((int)$_POST['id']);
            $this->response($res,'json');   
        }else{
            $this->response(['status' => 103, 'msg' => '请求失败'],'json');     
        }
    }
    
    
    
    
    
}
