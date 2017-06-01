<?php
namespace Opera\Model;
use Think\Model;

//表单元素的操作
class DataComponentModel extends Model
{

    protected $tableName = 'data_component';

    //获取列表,列表展示，如果是ID就展示此类的所有
    public function componentList($list_data){
        
        $search = trim($list_data['search']);  
        $id = (int)$list_data['id'];  
        $num = (int)$list_data['num'];
        $page = (int)$list_data['page'];
        $form_id = (int)$list_data['form_id'];
        //设置最大返回200条数据
        if(!$num) $num = 200;
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
        if($form_id){ 
            $map['form_id'] = $form_id; 
        }  
        $map['enabled'] = 1;       
       
        if(empty($form_id)){
         if(empty($page)) $page = 1;   
         $first = $num*($page-1);
         $res = $this->where($map)->limit($first,$num)->order("sort desc")->select(); 
         $count = $this->where($map)->count();
         $page_data['page'] = $page;
         $page_data['count_page'] = ceil($count/$num);
         $page_data['count'] = $count;
        }else{
          $res = $this->where($map)->order('sort desc')->select();
        }
       if($res){         
           $db_enum_dictionary = new \Home\Model\EnumDictionaryModel();    
           $component_type = $db_enum_dictionary->enumCall(array('value'=>'form_component_type')); 
           $data_type = $db_enum_dictionary->enumCall(array('value'=>'form_data_type')); 
           $validation_regulation = $db_enum_dictionary->enumCall(array('value'=>'form_validation_regulation')); 
           
        //预定义内容需要转化,类型展示需要修改
        foreach($res as $key=>$val){
            $res[$key]['predefined'] = unserialize($val['predefined_value']);
            $res[$key]['com_value'] = $component_type['value']['detail'][$val['com_type']];
            $res[$key]['validation_value'] = $validation_regulation['value']['detail'][$val['validation']];
            $res[$key]['data_value'] = $data_type['value']['detail'][$val['data_type']];
        }  
         return array('status'=>100,'value'=>$res,'page'=>$page_data);    
       }else{
         return array('status'=>102,'msg'=>'数据不存在');     
       }
       
    }
    
    //添加
    public function componentAdd($uid,$add_data){
        //变量存在判断
        $isset_arr = array('form_id','com_type','name','is_require','remark');
        foreach($isset_arr as $vol){
            //如果不存在这个变量，就返回false,
            if($add_data[$vol] == ''){
             return array('status' => 102, 'msg' => $vol.' 不能为空');        
            }
        }
        
        $data = array();
        $data['form_id'] = (int)$add_data['form_id']; //表单名
        //判断表单名是否存在
        if(!M('data_form')->where(array('id'=>$data['form_id'],'enabled'=>1))->find())  return array('status' => 102, 'msg' => '表单ID错误');    
        $data['com_type'] = (int)$add_data['com_type'];//组件类型
        $data['name'] = trim($add_data['name']);//表单组件名
        $data['is_require'] = (int)$add_data['is_require'];//表单组件名
        $data['remark'] = trim($add_data['remark']);//表单组件名
        
        //对组件类型的判断，长度，精度,数据类型等的判定
        $length = true;
        $data['length'] = (int)$add_data['length'];
        $data['default_value'] = trim($add_data['default_value']);//默认值
        switch ($data['com_type']) {
            case 1:
                $data['data_type'] = 1;
                if($data['length'] > 50 || $data['length'] == 0) $length = false;
                break;
            case 2:
                $data['data_type'] = 1;
                if($data['length'] > 30 || $data['length'] == 0) $length = false;
                if(empty($add_data['predefined_value'])) return array('status' => 102, 'msg' => '请输入预定义内容'); 
                //判断预定义的是否存在默认值
                if(!empty($data['default_value'])){
                    //检查默认值是否在预定义内容中
                    $default_value_check = false; 
                    foreach ($add_data['predefined_value'] as $vol){
                        if($data['default_value'] == $vol['value']){
                         $default_value_check = true; 
                        }
                    }
                    if($default_value_check == false) return array('status' => 102, 'msg' => '默认值不在预定义内容中');    
                }
                $data['predefined_value'] = serialize($add_data['predefined_value']);//预定义内容
                break;
            case 3:
                $data['data_type'] = 4;
                break;
            case 4:
                $data['data_type'] = 4;
                break;
            case 5:
                $data['data_type'] = 4;
                break;
            case 6:
                $data['data_type'] = 3;
                if($data['length'] > 10 || $data['length'] == 0) $length = false;
                $data['precision'] = (int)$add_data['precision'];
                if($data['precision'] > 6 || $data['precision'] == '') return array('status' => 102, 'msg' => '精度不能为空，且小数不能大于6位');    
                break;
            case 7:
                $data['data_type'] = 2;
                if($data['length'] > 11 || $data['length'] == 0) $length = false;
                break;
            case 8:
                $data['data_type'] = 1;
                if($data['length'] > 30 || $data['length'] == 0) $length = false;
                if(empty($add_data['predefined_value'])) return array('status' => 102, 'msg' => '请输入预定义内容');    
                if(!empty($data['default_value'])){
                    //检查默认值是否在预定义内容中
                    $default_value_check = false; 
                    foreach ($add_data['predefined_value'] as $vol){
                        if($data['default_value'] == $vol['value']){
                         $default_value_check = true; 
                        }
                    }
                    if($default_value_check == false) return array('status' => 102, 'msg' => '默认值不在预定义内容中');    
                }
                $data['predefined_value'] = serialize($add_data['predefined_value']);//预定义内容
                break;
            case 9:
                $data['data_type'] = 1;
                if($data['length'] > 30 || $data['length'] == 0) $length = false;
                if(empty($add_data['predefined_value'])) return array('status' => 102, 'msg' => '请输入预定义内容');  
                if(!empty($data['default_value'])){
                    //检查默认值是否在预定义内容中
                    $default_value_check = false; 
                    foreach ($add_data['predefined_value'] as $vol){
                        if($data['default_value'] == $vol['value']){
                         $default_value_check = true; 
                        }
                    }
                    if($default_value_check == false) return array('status' => 102, 'msg' => '默认值不在预定义内容中');    
                }
                $data['predefined_value'] = serialize($add_data['predefined_value']);//预定义内容
                break;
             case 10:
                $data['data_type'] = 1;
                if($data['length'] > 250 || $data['length'] == 0) $length = false; 
                break;
            default:
                return array('status' => 102, 'msg' => '无此选项');        
                break;
        }
        if($length == false){
         return array('status' => 102, 'msg' => '长度设置错误');      
        }
        
        $data['validation'] = trim($add_data['validation']);//表单组件的验证规则
        $data['sort'] = (int)$add_data['sort'];//排序
        $data['creator_id'] = $uid;
        $data['created_time'] = date('Y-m-d',time());
        $data['enabled'] = 1;//状态
        
        
        $res = $this->add($data);
        if($res){
            return array('status' => 100);
        }else{
            return array('status' => 102, 'msg' => '添加失败');
        }
    }
    
    
    //编辑，设定可编辑的字段，全部可编辑
    public function componentEdit($uid,$add_data){
                //变量存在判断
        $isset_arr = array('form_id','com_type','name','is_require','remark');
        foreach($isset_arr as $vol){
            //如果不存在这个变量，就返回false,
            if($add_data[$vol] == ''){
             return array('status' => 102, 'msg' => $vol.' 不能为空','post'=>$_REQUEST);        
            }
        }
        $data = array();
        $data['form_id'] = (int)$add_data['form_id']; //表单名
        //判断表单名是否存在
        if(!M('data_form')->where(array('id'=>$data['form_id'],'enabled'=>1))->find())  return array('status' => 102, 'msg' => '表单ID错误');    
        $data['com_type'] = (int)$add_data['com_type'];//组件类型
        $data['name'] = trim($add_data['name']);//表单组件名
        $data['is_require'] = (int)$add_data['is_require'];//表单组件名
        $data['remark'] = trim($add_data['remark']);//表单组件名
        
        //对组件类型的判断，长度，精度,数据类型等的判定
        $length = true;
        $data['length'] = (int)$add_data['length'];
        $data['default_value'] = trim($add_data['default_value']);//默认值
        switch ($data['com_type']) {
            case 1:
                $data['data_type'] = 1;
                if($data['length'] > 50 || $data['length'] == 0) $length = false;
                break;
            case 2:
                $data['data_type'] = 1;
                if($data['length'] > 30 || $data['length'] == 0) $length = false;
                if(empty($add_data['predefined_value'])) return array('status' => 102, 'msg' => '请输入预定义内容'); 
                //判断预定义的是否存在默认值
                if(!empty($data['default_value'])){
                    //检查默认值是否在预定义内容中
                    $default_value_check = false; 
                    foreach ($add_data['predefined_value'] as $vol){
                        if($data['default_value'] == $vol['value']){
                         $default_value_check = true; 
                        }
                    }
                    if($default_value_check == false) return array('status' => 102, 'msg' => '默认值不在预定义内容中');    
                }
                $data['predefined_value'] = serialize($add_data['predefined_value']);//预定义内容
                break;
            case 3:
                $data['data_type'] = 4;
                break;
            case 4:
                $data['data_type'] = 4;
                break;
            case 5:
                $data['data_type'] = 4;
                break;
            case 6:
                $data['data_type'] = 3;
                if($data['length'] > 10 || $data['length'] == 0) $length = false;
                $data['precision'] = (int)$add_data['precision'];
                if($data['precision'] > 6 || $data['precision'] == '') return array('status' => 102, 'msg' => '精度不能为空，且小数不能大于6位');    
                break;
            case 7:
                $data['data_type'] = 2;
                if($data['length'] > 11 || $data['length'] == 0) $length = false;
                break;
            case 8:
                $data['data_type'] = 1;
                if($data['length'] > 30 || $data['length'] == 0) $length = false;
                if(empty($add_data['predefined_value'])) return array('status' => 102, 'msg' => '请输入预定义内容');    
                if(!empty($data['default_value'])){
                    //检查默认值是否在预定义内容中
                    $default_value_check = false; 
                    foreach ($add_data['predefined_value'] as $vol){
                        if($data['default_value'] == $vol['value']){
                         $default_value_check = true; 
                        }
                    }
                    if($default_value_check == false) return array('status' => 102, 'msg' => '默认值不在预定义内容中');    
                }
                $data['predefined_value'] = serialize($add_data['predefined_value']);//预定义内容
                break;
            case 9:
                $data['data_type'] = 1;
                if($data['length'] > 30 || $data['length'] == 0) $length = false;
                if(empty($add_data['predefined_value'])) return array('status' => 102, 'msg' => '请输入预定义内容');  
                if(!empty($data['default_value'])){
                    //检查默认值是否在预定义内容中
                    $default_value_check = false; 
                    foreach ($add_data['predefined_value'] as $vol){
                        if($data['default_value'] == $vol['value']){
                         $default_value_check = true; 
                        }
                    }
                    if($default_value_check == false) return array('status' => 102, 'msg' => '默认值不在预定义内容中');    
                }
                $data['predefined_value'] = serialize($add_data['predefined_value']);//预定义内容
                break;
             case 10:
                $data['data_type'] = 1;
                if($data['length'] > 250 || $data['length'] == 0) $length = false; 
                break;
            default:
                return array('status' => 102, 'msg' => '无此选项');        
                break;
        }
        if($length == false){
         return array('status' => 102, 'msg' => '长度设置错误');      
        }
        
        if(isset($add_data['validation']))$data['validation'] = trim($add_data['validation']);//表单组件的验证规则
        if(isset($add_data['validation']))$data['sort'] = (int)$add_data['sort'];//排序
        $data['creator_id'] = $uid;
        $data['modified_time'] = date('Y-m-d',time());
        if(isset($add_data['validation']))$data['enabled'] = (int)$add_data['enabled'];//状态
        
        //哪些字段可以编辑，什么程度的可以编辑，
        $id = (int)$add_data['id'];
        if(empty($id))return array('status' => 102, 'msg' => 'id不存在');

        $res = $this->where(array('id'=>$id))->save($data);
        
        if($res){
            return array('status' => 100);
        }else{
            return array('status' => 102, 'msg' => '编辑失败');
        }
    }
    
    
    //关闭
    public function componentClose($id){
        $res = $this->where(array('id'=>$id))->save(array('enabled'=>0)); 
        if($res){
            return array('status' => 100);
        }else{
            return array('status' => 102, 'msg' => '关闭失败');
        }
        
    }
    
    
    //排序
    public  function sortEdit($ids)
    {
        //转成数组
        $ids_data = explode(',', $ids);
        $length = count($ids_data);
        $i = $length;
        foreach ($ids_data as $val){
           $this->where(array('id'=>$val))->setField('sort',$i);   
         $i--;   
        }  
        return array('status' => 100,'post'=>$ids);
        
    } 
    
   
}