<?php

/**
 * 用户登录之后的面板，用以展示后台首页，以及一些数据操作
 */

namespace Apiwiki\Controller;

class HomeController extends __ {
    public function __construct() {
        parent::__construct();
    }
    public function index() {
        $ProjectModel = M('project');
        $MemberModel = M('project_member');
        $InterfaceModel = M('interface');

        $myList = $ProjectModel->where(array('user_id' => $this->user['id'],'status' => 1))->select();
        if($myList) foreach($myList as &$item) {
            $item['member_count'] = $MemberModel->where(array('project_id' => $item['id']))->count();
            $item['interface_count'] = $InterfaceModel->where(array('project_id' => $item['id'],'status' => 1))->count();
        }

        $inList = $MemberModel->where(array('user_id' => $this->user['id']))->getField('project_id',true);
        if($inList) {
            $inList = $ProjectModel->where(array('id' => array('in',$inList),'status' => 1))->select();
            foreach($inList as &$item) {
                $item['member_count'] = $MemberModel->where(array('project_id' => $item['id']))->count();
                $item['interface_count'] = $InterfaceModel->where(array('project_id' => $item['id'],'status' => 1))->count();
            }
        }

        $this->title = 'Api Wiki 管理面板';
        $this->assign('myList',$myList);
        $this->assign('inList',$inList);
        $this->display();
    }
}