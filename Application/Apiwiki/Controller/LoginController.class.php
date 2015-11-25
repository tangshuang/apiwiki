<?php

/**
 * 登录操作相关：注册、登录、登出
 */

namespace Apiwiki\Controller;
use Think\Controller;

class LoginController extends Controller {
    public function __construct() {
        parent::__construct();
    }
    public function index() {
        $this->display();
    }
    public function verify($email,$password,$remeber = 1) {
        if(!IS_AJAX) {
            $this->error('请求非法');
        }
        $UserModel = D('User');
        $result = $UserModel->login($email,$password);
        if($result) {
            $UserModel->set_user_location($result,$remeber);
            $this->success('登录成功');
        }
        $this->error('登录名或密码不对');
    }
    public function register() {
        $this->display();
    }
    public function sign($username,$email,$password,$repassword) {
        if(!IS_AJAX) {
            $this->error('请求非法');
        }
        if($password != $repassword) {
            $this->error('两次输入的密码不一样');
        }
        $UserModel = D('User');
        $result = $UserModel->register($username,$email,$password);
        if($result) {
            $this->success('注册成功');
        }
        $this->error($UserModel->getError());
    }
    public function logout() {
        session('user',null);
        cookie('user',null);
        $this->success('注销成功','index');
    }
}