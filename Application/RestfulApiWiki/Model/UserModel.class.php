<?php

namespace RestfulApiwiki\Model;
use Think\Model;

class UserModel extends Model {

    protected $_validate = array(
        array('user_name', 'require', '用户名不能为空', self::MUST_VALIDATE,'',self::MODEL_INSERT),
        array('user_name', '5,30', '用户名必须大于5个字符，小于30个字符', self::EXISTS_VALIDATE, 'length'),

        array('user_email', 'require', '邮箱不能为空', self::MUST_VALIDATE,'',self::MODEL_INSERT),
        array('user_email', 'email', '邮箱格式不正确', self::EXISTS_VALIDATE),
        array('user_email', '8,32', '邮箱长度必须大于8个字符，小于32个字符', self::EXISTS_VALIDATE, 'length'),

        array('password', 'require', '密码不能为空', self::MUST_VALIDATE,'',self::MODEL_INSERT),
        array('password', '6,30', '密码必须大于6个字符，小于30个字符', self::EXISTS_VALIDATE, 'length'),

        array('user_name', '', '用户名被占用，请使用其他用户名', self::EXISTS_VALIDATE, 'unique'),
        array('user_email', '', '邮箱被占用，请换一个邮箱', self::EXISTS_VALIDATE, 'unique'),
    );

    protected $_auto = array(
        array('create_time',NOW_DATETIME, self::MODEL_INSERT),
        array('password','data_md5', self::MODEL_INSERT, 'function'),
        array('password','data_md5',self::MODEL_UPDATE,'function'),
        array('password','',self::MODEL_UPDATE,'ignore'),
    );

    public function login($email,$password) {
        $user = $this->where(array('user_email' => $email,'password' => data_md5($password),'status' => array('egt',0)))->field('password',true)->find();
        if(!$user) {
            $this->error = '用户名或密码不对';
            return false;
        }
        $this->where(array('id' => $user['id']))->setField('last_login_time',NOW_TIME);
        return $user;
    }

    public function register($username,$email,$password) {
        $user = array(
            'user_name' => $username,
            'user_email' => $email,
            'password' => $password,
            'create_time' => NOW_DATETIME,
            'status' => 1
        );
        $data = $this->create($user);
        if(!$data) {
            return false;
        }
        return $this->add();
    }

    public function update($id,$password,$data) {
        $user = $this->where(array('id' => $id,'password' => data_md5($password),'status' => array('egt',0)))->find();
        if(!$user) {
            $this->error = '原密码不对';
            return false;
        }
        $data['id'] = $id;
        $data = $this->create($data,self::MODEL_UPDATE);

        if(!$data) {
            return false;
        }
        return $this->save();
    }

    public function get_user_location() {
        $user = array();
        $userCookie = cookie('user');
        if($userCookie) {
            $userCookie = data_decrypt($userCookie);
            $userCookie = json_decode($userCookie,true);
            $user['cookie'] = $userCookie;
        }
        $userSession = session('user');
        if($userSession) {
            $user['session'] = $userSession;
        }
        return $user;
    }

    /**
     * 设置用户的信息缓存机制，cookie和session，仅包括用户的id,user_name,user_email,password(加密后，数据库中存储的),last_login_time这几个字段
     * @param $user
     */
    public function set_user_location($data,$cookie = true) {
        $user = array(
            'id' => $data['id'],
            'user_name' => $data['user_name'],
            'user_email' => $data['user_email'],
            'password' => $data['password'],
            'last_login_time' => NOW_TIME
        );
        session('user',$user);
        if($cookie) {
            $user = json_encode($user);
            $user = data_encrypt($user);
            cookie('user',$user);
        }
    }

}