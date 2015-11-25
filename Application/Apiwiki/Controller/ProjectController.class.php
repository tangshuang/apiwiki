<?php

/**
 * 项目相关：我的项目列表、添加项目、项目概览、项目设置、项目删除
 */

namespace Apiwiki\Controller;

class ProjectController extends __ {
    public function __construct() {
        parent::__construct();
        parent::__check_and_set();
    }

    public function myList() {
        $ProjectModel = M('project');
        $InterfaceModel = M('interface');
        $projects = $ProjectModel->where(array('user_id' => $this->user['id'],'status' => 1))->field('id,title,logo_url,amount')->order('create_time desc')->select();
        if($projects) foreach($projects as &$project) {
            $project['amount'] = $InterfaceModel->where(array('project_id' => $project['id'],'status' => 1))->count();
        }

        $this->title = '我的项目列表';
        $this->assign('projects',$projects);
        $this->assign('isMine',1);
        $this->display('list');
    }

    public function inList() {
        $ProjectModel = M('project');
        $ProjectMemberModel = M('project_member');
        $inProjects = $ProjectMemberModel->where(array('user_id' => $this->user['id']))->getField('project_id',true);
        if($inProjects) {
            $projects = $ProjectModel->where(array('id' => array('in',$inProjects),'status' => 1))->field('id,title,logo_url,amount')->order('create_time desc')->select();
            $this->assign('projects',$projects);
        }
        $this->title = '参与的项目列表';
        $this->display('list');
    }

    public function out($project_id) {
        $ProjectMemberModel = M('project_member');
        $ProjectMemberModel->where(array('project_id' => $project_id,'user_id' => $this->user['id']))->delete();
        $this->success('退出成功');
    }

    public function add() {
        if(IS_POST) {
            $info = $this->uploadPicture('logo');
            if($info) {
                $_POST['logo_url'] = $info['rootpath'].$info['savepath'].$info['savename'];
            }
            $ProjectModel = M('project');
            $ProjectModel->create();
            $ProjectModel->create_time = NOW_DATETIME;
            $ProjectModel->user_id = $this->user['id'];
            $ProjectModel->status = 1;
            $pid = $ProjectModel->add();
            $this->success('添加项目成功',U('setting',array('pid' => $pid)));
            exit;
        }
        $this->title = '添加项目';
        $this->display('edit');
    }

    public function index($pid) {
        $items = M('interface')->where(array('project_id' => $pid,'status' => 1))->order('update_time desc')->limit(5)->select();
        $docs = M('document')->where(array('project_id' => $pid,'status' => 1))->order('update_time desc')->limit(5)->select();

        $this->title = '项目概览';
        $this->assign('project',$this->project);
        $this->assign('items',$items);
        $this->assign('docs',$docs);
        $this->display();
    }

    public function setting($pid) {
        if(IS_POST) {
            $info = $this->uploadPicture('logo');
            if($info) {
                $_POST['logo_url'] = $info['rootpath'].$info['savepath'].$info['savename'];
            }
            $ProjectModel = M('project');
            $ProjectModel->create();
            $ProjectModel->save();
            $this->success('更新项目成功',U('setting',array('pid' => $pid)));
            exit;
        }
        $this->title = '项目设置';
        $this->assign('project',$this->project);
        $this->display('edit');
    }

    public function delete($pid) {
        // TODO:删除项目的分组
        // TODO:删除项目所有接口
        // TODO:删除与项目接口相关的传入参数、返回值、错误码

        M('project')->where(array('id' => $pid))->setField('status',-1);
        $this->success('删除项目成功',U('myList'));
    }

    public function member($pid) {
        $members = M('project_member')->where(array('project_id' => $pid))->getField('user_id',true);
        if($members) $members = M('user')->where(array('id' => array('in',$members)))->select();

        $this->title = '成员管理';
        $this->assign('items',$members);
        $this->display();
    }

    public function addMember($pid,$user_email) {
        if(!IS_AJAX)
            $this->error('请求错误');

        $UserModel = M('user');
        $user_id = $UserModel->where(array('user_email' => $user_email))->getField('id');
        if(!$user_id)
            $this->error('不存在该用户');

        $RelationModel = M('project_member');
        $data = array(
            'user_id' => $user_id,
            'project_id' => $pid,
            'role' => 1
        );
        $result = $RelationModel->data($data)->add();

        if($result === false)
            $this->error($RelationModel->getError());
        else
            $this->success('添加成员成功');
    }

    public function deleteMember($pid,$uid) {
        if(!IS_AJAX)
            $this->error('请求错误');

        $UserModel = M('project_member');
        $result = $UserModel->where(array('project_id' => $pid,'user_id' => $uid))->delete();

        if($result === false)
            $this->error($UserModel->getError());
        else
            $this->success('删除成功');
    }


}