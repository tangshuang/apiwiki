<?php

namespace Apiwiki\Controller;
use Think\Controller;

class __ extends Controller {
    public function __construct() {
        parent::__construct();
        $user = session('user');
        if(!$user) {
            // 如果用户之前选择了记住登录，那么通过cookie进行再次判断
            $user = cookie('user');
            if($user) {
                $user = json_decode(data_decrypt($user),true); // cookie是加密保存的，发过来之后，要进行解密，而且cookie存储之前进行过json_encode所以现在要反编译
                if(!$user['last_login_time'] || $user['last_login_time'] < strtotime('-7 days')) { // 如果cookie登录信息是7天以前的，就去登录呗
                    $this->redirect('Login/index');
                }
                $UserModel = D('User');
                $result = $UserModel->where(array('user_email' => $user['user_email'],'password' => $user['password']))->find();
                if($result) {
                    $UserModel->set_user_location($result);
                    $this->redirect('Home/index');
                }
            }
            // 去登录
            $this->redirect('Login/index');
        }
        // 记录一个全局USER
        $this->user = $user;
    }
    public function uploadPicture($field_name){
        $upload             = new \Think\Upload();
        $upload->maxSize    = 3145728;
        $upload->exts       = array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath   = './Uploads/Pictures/';
        $upload->savePath   = '';
        $upload->subName    = array('date','Y-m-d');
        $upload->replace    = false;
        $upload->hash       = true;

        $info   =   $upload->uploadOne($_FILES[$field_name]);
        if($info) {
            $info['rootpath'] = '/Uploads/Pictures/';
        }
        return $info;
    }

    protected function __check_and_set() {
        // 如果存在pid，那么对这个对应的项目进行检查，如果这个参数本身有问题，返回项目信息错误提示，如果该项目的创建者不是当前用户，那么提示权限不够，通过本步骤，下面的所有操作中，无需再检查权限，无需再使用用户id进行检索
        $pid = I('pid');
        if($pid) {
            if(!$pid && !is_numeric($pid))
                $this->error('项目信息错误');
            $project = M('project')->where(array('id' => $pid))->find();
            if($project['user_id'] != $this->user['id'])
                $this->error('权限不够');
            $this->project = $project;
        }
    }
}