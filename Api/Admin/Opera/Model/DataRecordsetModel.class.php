<?php
namespace Opera\Model;
use Think\Model;

//表单元素的操作
class DataRecordsetModel extends Model
{

    protected $tableName = 'data_recordset';

   
    
   //表单内容提交
   public function contentAdd($uid,$list_data){
        
        //传入表单ID，传入组件ID，传入值。
        //$add_data['form_id'],$add_data['componet']数组
        //获取键值模式的数据
        $add_data  =array();
        foreach($list_data['componet'] as $row){
           $add_data[$row['com_id']] =  $row['value'];
        }
        
        //获取表单数据和组件数据
        $form_id = (int)$list_data['form_id'];
        //检测表单
        if(!M('data_form')->where(array('enabled'=>1,'id'=>$form_id))->find()) return array('status' => 102, 'msg' =>'使用表单不存在');  
        
        //查询组件内容
        $component = M('data_component')->where(array('enabled'=>1,'form_id'=>$form_id))->order('sort desc')->select();
        if(empty($component))  return array('status' => 102, 'msg' =>'组件表单不存在');  
        
        //用一个表来做？创建一个新的ID  
         //事务判断
        $this->startTrans();
        $form_data = array();
        $form_data['form_id'] = $form_id;
        $form_data['creator_id'] = $uid;
        $form_data['created_time'] = date('Y-m-d H:i:s',time());
        $id = M('data_dataset')->add($form_data);
        if($id){
          $dataset_id = $id;  
        }else{
          return array('status' => 102, 'msg' =>'分组id生成错误');      
        }
        $check = true;
        foreach($component as $key =>$val){
            $data = array();
            //必填内容不能为空
            if($val['is_require'] == 1 && empty($add_data[$val['id']])){
                return array('status' => 102, 'msg' => $val['name'].' 数据不存在');  
            }
            
            
        if(!empty($add_data[$val['id']])){
            //对长度进行判断，对正则进行判定,对数组进行判断，是否是预定义的内容里面的值？ 对时间日期也要对格式进行判断处理。
            //获取预定义数组的元素值，用来对输入数据的判断
            
            if($val['com_type'] == 9){
                //对数据进行解析
                //$add_data[$val['id']] = unserialize($add_data[$val['id']]);
                foreach($add_data[$val['id']] as $vol){
                    if($val['validation']){
                        if (!preg_match($val['validation'], $vol['value'])) {
                        return array('status' => 102, 'msg' => $val['name'].' 验证不通过');  
                        }
                    }
                    //长度判断，
                    if($val['length']){
                        if(strlen($vol) > $val['length']){
                            return array('status' => 102, 'msg' => $val['name'].' 长度存在问题');
                        }
                    } 
                    
                    
                    
                }
                
                $data['value'] =  unserialize($add_data[$val['id']]);
            }else{
                //正则判断，
                if($val['validation']){
                    if (!preg_match($val['validation'], $add_data[$val['id']])) {
                    return array('status' => 102, 'msg' => $val['name'].' 验证不通过');  
                    }
                }  
                if($val['com_type'] == 6){
                    //长度判断，小数会多一位出来
                    if($val['length']){
                        if(strlen($add_data[$val['id']]) >= $val['length']){
                            return array('status' => 102, 'msg' => $val['name'].' 长度存在问题');
                        }
                    }
                }else{
                   //长度判断，
                   if($val['length']){
                        if(strlen($add_data[$val['id']]) > $val['length']){
                            return array('status' => 102, 'msg' => $val['name'].' 长度存在问题');
                        }
                   }
                   //精度判断
                   $precision = $this->getFloatLength($add_data[$val['id']]);
                   if($precision > $val['precision']){
                    return array('status' => 102, 'msg' => $val['name'].' 精度存在问题');   
                   }
                }
                
               $data['value'] =  $add_data[$val['id']];
            }
        }else{
            //当empty的时候的情况判断,空数组，为0，为空
            if($add_data[$val['id']] == 0){
             $data['value'] = 0;   
            }else{
             $data['value'] = '';   
            }
        }   
            //6小数，7整数，3，4，5，时间
            if($val['com_type'] == 6){
            $data['value_decimal'] =  (float)$data['value'];        
            }else if($val['com_type'] == 7){
            $data['value_int'] =  (int)$data['value'];        
            }else if(in_array($val['com_type'],array(3,4,5))){
            $data['value_datetime'] =  $data['value'];        
            }else{
            $data['value_varchar'] =  trim($data['value']);    
            }
            unset($data['value']);
            $data['form_id'] = $form_id;
            $data['com_id'] =  $val['id'];
            $data['dataset_id'] =  $dataset_id;
            $data['creator_id'] =  $uid;
            $data['created_time'] =  time('Y-m-d H:i:s',time());

            $res = $this->add($data);
            if(!$res){
             $check = false;
            } 
            
        }
        
        if($check == false){
                $this->rollback();  
                return array('status'=>102,'msg'=>'数据插入失败');     
            }else{
                $this->commit(); 
                return array('status'=>100);    
            }
        
   }
   
   //表单数据编辑
   public function contentEdit($uid,$list_data){
        
        //传入表单ID，传入组件ID，传入值。传入要修改的ID
        //$add_data['form_id'],$add_data['componet']数组
        //获取键值模式的数据
        $add_data  =array();
        foreach($list_data['componet'] as $row){
           $add_data[$row['com_id']]['value'] =  $row['value'];
           $add_data[$row['com_id']]['id'] =  $row['id'];
        }
        
        //获取表单数据和组件数据
        $form_id = (int)$list_data['form_id']; 
        //检测表单
        if(!M('data_form')->where(array('enabled'=>1,'id'=>$form_id))->find()) return array('status' => 102, 'msg' =>'使用表单不存在');  
        
        //查询组件内容
        $component = M('data_component')->where(array('enabled'=>1,'form_id'=>$form_id))->order('sort desc')->select();
        if(empty($component))  return array('status' => 102, 'msg' =>'组件表单不存在');  
        
        //用一个表来做？创建一个新的ID  
         //事务判断
        $this->startTrans();
        $check = true;
        foreach($component as $key =>$val){
            
             //  isset()  判断，如存在，就要判断，不存在的就算了
            if(isset($add_data[$val['id']]['value'])){
            $data = array();
            //必填内容不能为空
            if($val['is_require'] == 1 && empty($add_data[$val['id']]['value'])){
                return array('status' => 102, 'msg' => $val['name'].' 数据不存在');  
            }
            //对长度进行判断，对正则进行判定,对数组进行判断，是否是预定义的内容里面的值？
            if($val['com_type'] == 9){
                //对数据进行解析
                foreach($add_data[$val['id']]['value'] as $vol){
                    if($val['validation']){
                        if (!preg_match($val['validation'], $vol['value'])) {
                        return array('status' => 102, 'msg' => $val['name'].' 验证不通过');  
                        }
                    }
                    //长度判断，
                    if($val['length']){
                        if(strlen($vol) > $val['length']){
                            return array('status' => 102, 'msg' => $val['name'].' 长度存在问题');
                        }
                    } 
                }
                $data['value'] =  unserialize($add_data[$val['id']]['value']);
            }else{
                //正则判断，
                if($val['validation']){
                    if (!preg_match($val['validation'], $add_data[$val['id']]['value'])) {
                    return array('status' => 102, 'msg' => $val['name'].' 验证不通过');  
                    }
                }  
                if($val['com_type'] == 6){
                    //长度判断，小数会多一位出来
                    if($val['length']){
                        if(strlen($add_data[$val['id']]['value']) >= $val['length']){
                            return array('status' => 102, 'msg' => $val['name'].' 长度存在问题');
                        }
                    }
                }else{
                   //长度判断，
                   if($val['length']){
                        if(strlen($add_data[$val['id']]['value']) > $val['length']){
                            return array('status' => 102, 'msg' => $val['name'].' 长度存在问题');
                        }
                   }
                   //精度判断
                   $precision = $this->getFloatLength($add_data[$val['id']]['value']);
                   if($precision > $val['precision']){
                    return array('status' => 102, 'msg' => $val['name'].' 精度存在问题');   
                   }
                }
               
                //6小数，7整数，3，4，5，时间
                $data['value'] =  $add_data[$val['id']]['value'];
                //6小数，7整数，3，4，5，时间
                if($val['com_type'] == 6){
                $data['value_decimal'] =  (float)$data['value'];        
                }else if($val['com_type'] == 7){
                $data['value_int'] =  (int)$data['value'];        
                }else if(in_array($val['com_type'],array(3,4,5))){
                $data['value_datetime'] =  $data['value'];        
                }else{
                $data['value_varchar'] =  trim($data['value']);    
                }
                unset($data['value']);
                $data['creator_id'] =  $uid;
                $data['modified_time'] =  time('Y-m-d H:i:s',time());
                $res = $this->where(array('id'=>$add_data[$val['id']]['id']))->save($data);
                if(!$res){
                 $check = false;
                }
              }
                 
            }
            
        }
        
        if($check == false){
                $this->rollback();  
                return array('status'=>102,'msg'=>'数据插入失败');     
            }else{
                $this->commit(); 
                return array('status'=>100);    
            }
            

   }
   
   
   
   private function getFloatLength($num) {
    $count = 0;

    $temp = explode ( '.', $num );

    if (sizeof ( $temp ) > 1) {
    $decimal = end ( $temp );
    $count = strlen ( $decimal );
    }

    return $count;
    }
   
    

}