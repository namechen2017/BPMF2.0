<?php

namespace Opera\Model;

use Think\Model;

//表单元素的操作,采用mysql 原生视图来处理
class DataRecordsetViewModel extends Model {

//    public $viewFields = array(
//     'DataRecordset'=>array('id','form_id','com_id','value_varchar','value_int','value_datetime','value_decimal','enabled','creator_id','created_time','modified_time','groupid','_type'=>'LEFT'),
//     'DataComponent'=>array('name'=>'com_name','com_type'=>'com_type','data_type'=>'data_type','precision'=>'precisions','sort'=>'sort', '_on'=>'DataRecordset.com_id=DataComponent.id','_type'=>'LEFT'),
//     'DataForm'=>array('name'=>'form_name', '_on'=>'DataRecordset.form_id=DataForm.id'),
//    );
    //创建mysql视图  
    //create view tbl_data_recordset_view as SELECT tbl_data_recordset.*,tbl_data_form.`name` as form_name,tbl_data_component.name as com_name,tbl_data_component.com_type,tbl_data_component.data_type,tbl_data_component.precision as precisions,tbl_data_component.sort FROM `tbl_data_recordset` LEFT JOIN tbl_data_form on tbl_data_recordset.form_id = tbl_data_form.id LEFT JOIN tbl_data_component on tbl_data_recordset.com_id = tbl_data_component.id 
    protected $tableName = 'data_recordset_view';

    //数据列表
    public function contentList($list_data) {

        //输入分组id,输入表单ID。输出资料
        $dataset_id = (int) $list_data['dataset_id'];
        $form_id = (int) $list_data['form_id'];
        if (empty($dataset_id) || empty($form_id)) {
            return array('status' => 102, 'msg' => '资料不完善');
        }
        $where = array();
        $where['enabled'] = 1;
        $where['dataset_id'] = $dataset_id;
        $where['form_id'] = $form_id;
        $res = $this->where($where)->order('sort desc')->select();
        
        if ($res) {
            //对数据进行处理
            foreach ($res as $key => $val) {
                //数据类型是小数时，对数据进行精度解析，四舍五入。
                switch ($val['data_type']) {
                    case 1:
                        $val['value'] = $val['value_varchar'];
                        break;
                    case 2:
                        $val['value'] = $val['value_int'];

                        break;
                    case 3:
                        $val['value'] = round($val['value_decimal'], $val['precisions']);
                        break;
                    case 4:
                        $val['value'] = $val['value_datetime'];
                        break;
                }
                //数据类型是多选时，对数据进行序列化解析
                switch ($val['com_type']) {
                    case 3:
                        $timestamp = strtotime($val['value']);
                        $val['value'] = date('H:i:s', $timestamp);
                        break;
                    case 4:
                        $timestamp = strtotime($val['value']);
                        $val['value'] = date('Y-m-d', $timestamp);
                        break;
                    case 9:
                        $val['value'] = unserialize($val['value']);
                        break;
                }
                $res[$key]['value'] = $val['value'];
            }
            return array('status' => 100, 'value' => $res);
        } else {
            return array('status' => 102, 'msg' => '数据不存在');
        }
    }

}
