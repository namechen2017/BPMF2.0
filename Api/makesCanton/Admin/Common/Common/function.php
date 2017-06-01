<?php


//创建一个常量调用方法,
function get_constant($data){
    if(empty($data)) return false;
    //传type_code he key_name两个过来，其中type_code是必填项目
    foreach($data as $key =>$val){
        if($val){    
        $where[$key] = $val; 
        }
    }
    $where['status'] = 1;
    $res = M($this->table)->where($where)->select();
    return $res;        
}


/**
 * 
 * @param type $arr   需要取值的数组
 * @param type $value 需要取值的键
 */
function TwoArrayValue($arr,$value='id'){
    $arr_data = array();
    foreach($arr as $val){
        $arr_data[] = $val[$value]; 
    }
    return $arr_data; 
}












?>