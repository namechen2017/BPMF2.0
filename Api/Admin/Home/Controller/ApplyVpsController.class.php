<?php

namespace Home\Controller;

use Think\Controller;

/**
 * 优惠券控制器
 * @author cxl,lrf
 * @modify 2016/12/22
 */
class ApplyVpsController extends BaseController {

    //列表查询，首次编辑（新增），再次编辑（修改），详情页面查询，删除（隐藏），调用当前的登录人信息
    //个人查询
    public function authDetail() {

        $data = $this->getAuth();
        $auth_data = array('name' => $data['user_data'][$this->loginid], 'org' => $data['org_data'][$data['role_org_data'][$data['role_data'][$this->loginid]]]);
        $this->response(['status' => 100, 'value' => $auth_data], 'json');
    }

    public function getCustom() {

        if ($_POST) {
            $name = $_POST['name'];
            $custom_data = $this->customList($name);
        } else {
            $custom_data = $this->customList();
        }
        $data = array();
        foreach ($custom_data as $key => $val) {

            $arr['key'] = $key;
            $arr['value'] = $val;
            $data[] = $arr;
        }
        if ($data) {
            $this->response(['status' => 100, 'value' => $data], 'json');
        } else {
            $this->response(['status' => 101, 'msg' => '无此内容'], 'json');
        }
    }

    //公司结构
    private function customList($name = '') {
        if ($name) {
            $where['custom_name'] = array('like', '%' . $name . '%');
        }
        $custom_data = M('customer')->where($where)->getField('id,custom_name');
        return $custom_data;
    }

    //查看此用户的姓名和部门
    private function getAuth() {
        //内容可缓存
        //人员列表
        $where = array('status'=>1);
        $user_data = M('auth_user')->where($where)->getField('id,real_name');
        //部门列表
        $org_data = M('auth_org')->where($where)->getField('id,name');
        
        
        //人员对应组表
        $role_data = M('auth_role_user')->getField('user_id,role_id');
        //组对应部门ID表
        $role_org_data = M('auth_role_org')->getField('role_id,org_id');
        
        
        $data['user_data']  = $user_data;
        $data['org_data']  = $org_data; 
        $data['role_data']  = $role_data;
        $data['role_org_data']  = $role_org_data;
        
        return $data;

    }

    //信息添加 
    public function vpsAdd() {
        if ($_POST) {
            //获取内容，一旦有一个不存在，就返回
            $data = array();
            $data['deadline_time'] = $_POST['deadline_time'];
            $data['assign_region'] = trim($_POST['assign_region']);
            $data['duration'] = $_POST['duration'];
            $data['customer_id'] = trim($_POST['customer_id']);
            $data['contact_people'] = trim($_POST['contact_people']);
            $data['tel_phone'] = trim($_POST['tel_phone']);
            $data['use'] = trim($_POST['use']);
            $data['milieu'] = trim($_POST['milieu']);

            $date_time = date('Y-m-d H:i:s', time());
            $data['apply_id'] = $this->loginid; //创建者
            $data['created_time'] = $date_time; //  
            foreach ($data as $val) {
                if (empty($val)) {
                    $this->response(['status' => 101, 'msg' => '数据不全', 'value' => $data], 'json');
                    break;
                }
            }
            $res = M('apply_vps')->add($data);
            if ($res) {
                $this->response(['status' => 100], 'json');
            } else {
                $this->response(['status' => 102, 'msg' => '操作失败'], 'json');
            }
        } else {
            $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        }
    }

    //信息修改，二次添加
    public function vpsEdit() {

        if ($_POST) {
            //获取内容，一旦有一个不存在，就返回
            $id = $_POST['id'];
            $vps_data = M('apply_vps')->where(array('id' => $id))->find();
            if (empty($vps_data) || !empty($vps_data['executor_id'])) {
                $this->response(['status' => 104, 'msg' => '数据错误，不可编辑'], 'json');
            }
            $data = array();
            $data['com_time'] = $_POST['com_time'];
            $data['region'] = trim($_POST['region']);
            $data['expiry_time'] = $_POST['expiry_time'];
            $data['milieu_conf'] = trim($_POST['milieu_conf']);

            foreach ($data as $val) {
                if (empty($val)) {
                    $this->response(['status' => 101, 'msg' => '数据不全', 'value' => $data], 'json');
                    break;
                }
            }
            $date_time = date('Y-m-d H:i:s', time());
            $data['executor_id'] = $this->loginid; //创建者
            $data['execut_time'] = $date_time; //  

            $res = M('apply_vps')->where(array('id' => $id))->save($data);
            if ($res) {
                $this->response(['status' => 100], 'json');
            } else {
                $this->response(['status' => 102, 'msg' => '操作失败'], 'json');
            }
        } else {
            $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        }
    }

    //单个的内容
    public function vpsFind() {


        if ($_POST) {
            $id = $_POST['id'];
            $res = M('apply_vps')->where(array('id' => $id, 'status' => 1))->find();
            if ($res) {  
                $data = $this->getAuth();
                $res['apply_name'] = $data['user_data'][$res['apply_id']];
                $res['apply_org'] = $data['org_data'][$data['role_org_data'][$data['role_data'][$res['apply_id']]]];
                $res['executor_name'] = $data['user_data'][$res['executor_id']];
                $res['executor_org'] = $data['org_data'][$data['role_org_data'][$data['role_data'][$res['executor_id']]]];
                $custom_data = $this->customList();
                $res['custom_name'] = $custom_data[$res['customer_id']];
                $this->response(['status' => 100, 'value' => $res], 'json');
            } else {
                $this->response(['status' => 102, 'msg' => '内容不存在'], 'json');
            }
        } else {
            $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        }
    }

    //列表的展示
    public function vpsList() {

        //     $_POST['issuant_status'] = 2;
//       $_POST['search'] = '郭';
        //    $_POST['num'] = 5;

        if ($_POST) {
            //模糊搜索和分页内容
            $type = $_POST['status'];
            $search = trim($_POST['search']);
            $num = $_POST['num'];
            $page = $_POST['page'];


            //设置条件 
            if (!empty($type)) {
                //为1时是申请中的，为2是完成的    
                if ($type == 1) {
                    $where2['executor_id'] = array('exp', 'is null');
                } else {
                    $where2['executor_id'] = array('exp', 'is not null');
                }
            }
            $where2['status'] = 1;

            $where1 = array();
            if (!empty($search)) {
                //模糊搜索数组
                $custom_ids = M('customer')->where(array('custom_name' => array('like', '%' . $search . '%')))->getField('id', true);
                if ($custom_ids) {
                    $where1['customer_id'] = array('in', $custom_ids);
                }
                $where1['assign_region'] = array('like', '%' . $search . '%');
                $where1['duration'] = array('like', '%' . $search . '%');
                $where1['contact_people'] = array('like', '%' . $search . '%');
                $where1['tel_phone'] = array('like', '%' . $search . '%');
                $where1['region'] = array('like', '%' . $search . '%');
                $where1['milieu_conf'] = array('like', '%' . $search . '%');
                $where1['_logic'] = 'OR';

                $where = array($where1, $where2);
            } else {
                $where = $where2;
            }


            if (empty($page)) {
                $page = 1;
            }
            if (empty($num)) {
                $num = 10;
            }
            $first = $num * ($page - 1);

            $vps_list_data = M('apply_vps')->where($where)->limit($first, $num)->order("id desc")->select();

            $count = M('apply_vps')->where($where)->count();
            $page_data['page'] = $page;
            $page_data['count_page'] = ceil($count / $num);


            $custom_data = $this->customList();
            
            $data = $this->getAuth();
            foreach ($vps_list_data as $key => $res) {
                $res['custom_name'] = $custom_data[$res['customer_id']];
                $res['apply_name'] = $data['user_data'][$res['apply_id']];
                $res['apply_org'] = $data['org_data'][$data['role_org_data'][$data['role_data'][$res['apply_id']]]];
                $res['executor_name'] = $data['user_data'][$res['executor_id']];
                $res['executor_org'] = $data['org_data'][$data['role_org_data'][$data['role_data'][$res['executor_id']]]];
                $vps_list_data[$key] = $res;
            }
            if ($vps_list_data) {
                $this->response(['status' => 100, 'value' => $vps_list_data, 'page' => $page_data], 'json');
            } else {
                $this->response(['status' => 102, 'msg' => '内容不存在'], 'json');
            }
        } else {
            $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        }
    }

    //使它不展示,只有编辑者和超级管理员可以删除
    public function vpsDelete() {
        //编辑者和管理员
        if ($_POST) {
            $id = $_POST['id'];
            //查询用户判断权限
            $apply_id = M('apply_vps')->where(array('id' => $id))->getField('apply_id');
            if ($apply_id == $this->loginid) {
                $res = M('apply_vps')->where(array('id' => $id))->setField('status', 0);
                if ($res) {
                    $this->response(['status' => 100], 'json');
                } else {
                    $this->response(['status' => 101, 'msg' => '操作失败'], 'json');
                }
            }
            $role_id = M('auth_role_user')->where(array('user_id' => $this->loginid))->getField('role_id');
            if ($role_id == 2) {
                $res = M('apply_vps')->where(array('id' => $id))->setField('status', 0);
                if ($res) {
                    $this->response(['status' => 100], 'json');
                } else {
                    $this->response(['status' => 101, 'msg' => '操作失败'], 'json');
                }
            } else {
                $this->response(['status' => 102, 'msg' => '无此权限'], 'json');
            }
        } else {
            $this->response(['status' => 103, 'msg' => '请求失败'], 'json');
        }
    }

}
