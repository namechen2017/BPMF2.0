<?php
namespace Home\Model;
use Think\Model;

class EnumDictionaryModel extends Model
{

    protected $tableName = 'enum_dictionary';
    
    //优化常量的处理
    
    //常量的获取
    //编号列表读取,做模糊搜索该如何做？
    public function enumList($list_data){
        
          $search = trim($list_data['search']);  
          $id = (int)$list_data['id'];  
          $value = trim($list_data['value']); 
          $key_name = trim($list_data['key_name']);
          $num = (int)$list_data['num'];
          $page = (int)$list_data['page'];
          if(empty($num))  return array('status' => 102, 'msg' => '查询数量未指定');
          //if(empty($page))  return array('status' => 102, 'msg' => '第几页未指定');
          //存在papge,代表要查找分页数据。
          $search_where = array();
          if(!empty($search)){
            $search_where['name'] = array('like', '%' . $search . '%');   
            $search_where['value'] = array('like', '%' . $search . '%');   
            $search_where['key_name'] = array('like', '%' . $search . '%'); 
            $search_where['key_value'] = array('like', '%' . $search . '%');  
            $search_where['remark'] = array('like', '%' . $search . '%');   
            $search_where['_logic'] = 'OR';
          }else{
             $search_where = '1=1'; 
          }
          //如果存在就调用
          $map['_complex'] = $search_where;
          if($id){
              $map['id'] = $id;
          }
          if($value){
             $map['value'] = $value; 
          }
          if($key_name){
             $map['key_name'] = $key_name; 
          }
          $map['enabled'] = 1;       
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
             $data = array();
             foreach($res as $key =>$val){
                 
               $data[$val['value']]['name'] = $val['name'];
               $data[$val['value']]['value'] = $val['value'];
               $data[$val['value']]['detail'][] = $val;  
               
             }
           return array('status'=>100,'value'=>$data,'page'=>$page_data);    
         }else{
           return array('status'=>102,'msg'=>'数据不存在');     
         }
    }
    
    //常量的外部调用,重新定义，调用模块编码，传输键和值的数组过去
    public function enumCall($list_data){
          //调用无数量限制
          $key_name = trim($list_data['key_name']);  
          $value = trim($list_data['value']); 
          
          //调用缓存
          $memcache = new \Think\Product\MemcacheCache();
          $data = $memcache->enumGroup();
          //判断数据
          if($list_data['type'] == 'call'){
            if(!empty($value) && $key_name != ''){
                
            $res = $data[$value]['detail'][$key_name];  
            $res = $data[$value]['detail'][$key_name];    
            foreach($data[$value]['detail'] as $val){
                if($val['key_name'] == $key_name){
                    $key_value = $val['key_value'];
                }
            }
            $res = $key_value;
            
            }else if(!empty($value)){
            $res = $data[$value];    
            }
          }else{
            if(!empty($value) && $key_name != ''){
            $res = $data[$value]['detail'][$key_name];    
            foreach($data[$value]['detail'] as $val){
                if($val['key_name'] == $key_name){
                    $key_value = $val['key_value'];
                }
            }
            $res = $key_value;
            }else if(!empty($value)){
            $res = $data[$value];    
            $detail = array();
                foreach($res['detail'] as $val){
                    $detail[$val['key_name']] = $val['key_value'];
                }
            $res['detail'] = $detail;
            }
          }
          
         if($res){ 
           $this->statConstant($data[$value]['value']); 
           return array('status'=>100,'value'=>$res);    
         }else{
           return array('status'=>102,'msg'=>'数据不存在');     
         }
    }
    
    
    //数据统计
    private function statConstant($value){
        //新增记录
        $data['value'] = $value;
        $data['time'] = date('Y-m-d H:i:s',time());
        M('constant_count')->add($data); 
    }
    
    
    //分类接口
    public function enumGroup($search = ''){
        //调用无数量限制 
        if($search){
          $where['name'] = array('like','%'.$search.'%'); 
          $where['value'] = array('like','%'.$search.'%'); 
          $where['_logic'] = 'OR';
        }else{
          $where = '1=1';  
        }
        $map['_complex'] = $where;
        $map['enabled'] = 1; 
        $res = $this->where($map)->field("id,name,value")->group('value')->select();
         if($res){ 
           return array('status'=>100,'value'=>$res);    
         }else{
           return array('status'=>102,'msg'=>'数据不存在');     
         }
    }
    
    //添加接口
    public function enumAdd($add_data){
        
        //哪些字段可以编辑，什么程度的可以编辑
        $id = (int)$add_data['id'];
        $data = array();
        $data['name'] = trim($add_data['name']); //模块名
        $data['value'] = trim($add_data['value']);//模块号
        $data['key_name'] = trim($add_data['key_name']);//键名
        $data['key_value'] = trim($add_data['key_value']);//键值
        //判断是否存在
        foreach($data as $key=>$val){
            if(is_null($val)){
                return array('status' => 102, 'msg' => $key.' 数据不存在');
            }
        }
        $data['sort'] = (int)$add_data['sort'];//排序
        $data['remark'] = trim($add_data['remark']);//备注  
        
        if(!empty($id)){
        $where['value']  = $data['value'];
        $where['key_name']  = $data['key_name'];
        }else{
        $where['value']  = $data['value'];
        }
        $where['enabled'] = 1;
        $check = $this->where($where)->find();   
        if($check){
        return array('status' => 102, 'msg' => '此常量已存在,或此类常量已存在'); 
        }
        
        $res = $this->add($data);
        if($res){
            return array('status' => 100);
        }else{
            return array('status' => 102, 'msg' => '添加失败');
        }
    }
    
    //编辑接口
    public function enumEdit($add_data){
        
        //哪些字段可以编辑，什么程度的可以编辑
        $id = (int)$add_data['id'];
        if(!$id) return array('status' => 102, 'msg' => '请输入ID');
        $data = array();
        $data['name'] = trim($add_data['name']); //模块名
        $data['value'] = trim($add_data['value']);//模块号
        $data['key_name'] = trim($add_data['key_name']);//键名
        $data['key_value'] = trim($add_data['key_value']);//键值
        $data['sort'] = (int)$add_data['sort'];//排序
        $data['remark'] = trim($add_data['remark']);//备注  
        
        //查看是否与其他的冲突
        if(empty($data['value']) || empty($data['key_name'])){
            return array('status' => 102, 'msg' => 'value 和 key_name 必须存在'); 
        }else{
            $check = $this->where(array('id'=>array('neq',$id),'value'=>$data['value'],'key_name'=>$data['key_name'],'enabled'=>1))->find();
            if($check){
            return array('status' => 102, 'msg' => '此常量已存在'); 
            }
        }
        //去除为空数据
        foreach($data as $key=>$val){
            if(is_null($val)){
                unset($data[$key]);
            }
        }
        $res = $this->where(array('id'=>$id))->save($data);
        if($res){
            return array('status' => 100);
        }else{
            return array('status' => 102, 'msg' => '编辑失败');
        }
    }
    
    
    //关闭
    public function enumClose($id){
        
        if(empty($id)) return array('status' => 102, 'msg' => '请输入ID');
        $res = $this->where(array('id'=>$id))->setField('enabled',0);
        if($res){
            return array('status' => 100);
        }else{
            return array('status' => 102, 'msg' => '操作失败');
        }
        
    }
    
    
    
}