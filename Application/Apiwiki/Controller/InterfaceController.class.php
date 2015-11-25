<?php

/**
 * 接口操作：接口列表、添加接口、编辑接口、删除接口；接口参数的相关操作：添加、修改、删除
 */

namespace Apiwiki\Controller;

class InterfaceController extends __ {
    public function __construct() {
        parent::__construct();
        parent::__check_and_set();
    }

    public function index($pid) {
        $InterfaceModel = M('interface');
        $items = $InterfaceModel->where(array('project_id' => $pid,'status' => 1))->order('sort desc')->select();

        $this->title = '项目接口管理';
        $this->assign('items',$items);
        $this->display();
    }

    public function add($pid) {
        if(IS_POST) {
            $InterfaceModel = M('interface');
            $data = $InterfaceModel->create();
            $InterfaceModel->create_time = NOW_DATETIME;
            $InterfaceModel->user_id = $this->user['id'];
            $InterfaceModel->status = 1;
            $id = $InterfaceModel->add();
            $this->success('添加接口成功',U('edit',array('pid' => $pid,'id' => $id)));
            exit;
        }
        $this->title = '添加接口';
        $this->display('edit');
    }

    public function edit($pid,$id) {
        $InterfaceModel = M('interface');

        if(IS_POST) {
            $InterfaceModel->create();
            $InterfaceModel->save();
            $this->success('更新接口成功',U('index',array('pid' => $pid)));
            exit;
        }

        $interface = $InterfaceModel->where(array('id' => $id))->find();

        $items = M('interface_parameter')->where(array('interface_id' => $id))->order('id asc')->select(); // 通过ID排序的原因：1.id再数据库中，不一定完全是升序；2.将结果按插入数据库顺序选出，才能正确构建树状结构
        if($items) {
            foreach($items as &$item) {
                $item['required_text'] = $item['required'] == 1 ? '是' : '否';
            }

            $items = array_tree($items); // 形成树状结构
        }
        $interParam = $items;

        $items = M('interface_data')->where(array('interface_id' => $id))->order('id asc')->select();
        if($items) {
            foreach($items as &$item) {
                $item['required_text'] = $item['required'] == 1 ? '是' : '否';
            }

            $items = array_tree($items); // 形成树状结构
        }
        $returnData = $items;

        unset($items);

        $errorData = M('interface_error')->where(array('interface_id' => $id))->order('sort desc')->select();

        $this->title = '编辑接口';
        $this->assign('interface',$interface);
        $this->assign('interParam',$interParam);
        $this->assign('returnData',$returnData);
        $this->assign('errorData',$errorData);
        $this->display();
    }

    public function dataTree($items,$deep,$type) {
        // 对元素进行排序
        if(count($items) > 1) usort($items, function($a, $b) {
            if($a['sort'] == $b['sort'])
                return 0;
            return ($a['sort'] > $b['sort']) ? -1 : 1;
        });

        $this->assign('items',$items);
        $this->assign('deep',$deep);
        $this->assign('type',$type);
        $this->display('dataTree');
    }

    public function delete($pid,$id) {
        $InterfaceModel = M('interface');
        $InterfaceModel->where(array('id' => $id))->setField('status',-1);
        $this->success('删除接口成功');
    }

    public function addData($pid,$type) {
        if(!IS_AJAX)
            $this->error('请求非法');

        if($type == 'parameter') {
            $Model = M('interface_parameter');
        }
        elseif($type == 'return_data') {
            $Model = M('interface_data');
        }
        elseif($type == 'error') {
            $Model = M('interface_error');
        }
        else {
            $this->error('请求错误');
        }

        $data = $Model->create();
        $data['id'] = $Model->add();

        if(isset($data['required'])) $data['required_text'] = $data['required'] == 1 ? '是' : '否';

        $this->success($data);
    }

    public function editData($pid,$type) {
        if(!IS_AJAX)
            $this->error('请求非法');

        if($type == 'parameter') {
            $Model = M('interface_parameter');
        }
        elseif($type == 'return_data') {
            $Model = M('interface_data');
        }
        elseif($type == 'error') {
            $Model = M('interface_error');
        }
        else {
            $this->error('请求错误');
        }

        $data = $Model->create();
        $result = $Model->save();

        if($result === false)
            $this->error($Model->getError());
        else
            $this->success($data);
    }

    public function deleteData($pid,$type,$id) {
        if(!IS_AJAX)
            $this->error('请求非法');

        if($type == 'parameter') {
            $Model = M('interface_parameter');
        }
        elseif($type == 'return_data') {
            $Model = M('interface_data');
        }
        elseif($type == 'error') {
            $Model = M('interface_error');
        }
        else {
            $this->error('请求错误');
        }

        $result = $Model->where(array('id' => $id))->delete();

        if($result === false)
            $this->error($Model->getError());
        else
            $this->success('删除参数成功');
    }

    public function errorCodes($pid) {
        $errorData = M('interface_error')->where(array('project_id' => $pid))->order('code asc')->select();

        $this->title = '错误码管理';
        $this->assign('errorData',$errorData);
        $this->display();
    }


}