<?php

namespace Apiwiki\Controller;

class UserController extends __ {
    public function __construct() {
        parent::__construct();
        parent::__check_and_set();
    }
    public function setting() {
        if(IS_POST) {
            $UserModel = D('User');
            $params = I('post.');
            $user = array();
            if($params['user_name']) $user['user_name'] = $params['user_name'];
            if($params['password']) $user['password'] = $params['password'];

            if(empty($user))
                $this->error('提交的数据有误');

            $result = $UserModel->update($this->user['id'],$params['old_password'],$user);

            if($result === false) {
                $this->error($UserModel->getError());
            }
            else {
                $user = array_merge($this->user,array('user_name' => $user['user_name'],'password' => data_md5($user['password'])));
                $UserModel->set_user_location($user);
                $this->success('更新成功');
            }
            exit;
        }
        $this->title = '编辑用户资料';
        $this->display();
    }
}