<?php

/**
 * 网站入口，现在因为要求所有用户必须登录，所以必须引入Common\Controller\__，如果后期需要公开显示，则不再引用，这样就可以直接显示页面
 */

namespace Apiwiki\Controller;

class IndexController extends __ {
    public function __construct() {
        parent::__construct();
        // 检查权限
        $pid = I('pid');
        if($pid) {
            if(!$pid && !is_numeric($pid))
                $this->error('项目信息错误');

            $project = M('project')->where(array('id' => $pid,'status' => 1))->find();


            if(!$project)
                $this->error('不存在该项目');

            $members = M('project_member')->where(array('project_id' => $pid))->getField('user_id',true);

            if($project['read_level'] == 2) {
                if($project['user_id'] != $this->user['id'])
                    $this->error('没有阅读权限');
            }
            elseif($project['read_level'] == 1) {
                if($project['user_id'] != $this->user['id'] && !in_array($this->user['id'],$members))
                    $this->error('没有阅读权限');
            }

            $this->project = $project;
        }
    }

    public function detail($pid) {
        $items = M('interface')->where(array('project_id' => $pid,'status' => 1))->order('sort desc')->select();
        $docs = M('document')->where(array('project_id' => $pid,'status' => 1))->order('sort desc')->select();

        $this->title = $this->project['title'];
        $this->assign('items',$items);
        $this->assign('docs',$docs);
        $this->display();
    }

    public function item($pid,$id) {
        $InterfaceModel = M('interface');

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

        $errorData = M('interface_error')->where(array('interface_id' => $id))->order('sort desc')->select();

        $items = $InterfaceModel->where(array('project_id' => $pid,'status' => 1))->order('sort desc')->select();
        $docs = M('document')->where(array('project_id' => $pid,'status' => 1))->order('sort desc')->select();

        $this->title = $interface['title'];
        $this->assign('list',$items);
        $this->assign('interface',$interface);
        $this->assign('interParam',$interParam);
        $this->assign('returnData',$returnData);
        $this->assign('errorData',$errorData);
        $this->assign('docs',$docs);
        $this->display();
    }

    public function dataTree($items,$deep) {
        // 对元素进行排序
        if(count($items) > 1) usort($items, function($a, $b) {
            if($a['sort'] == $b['sort'])
                return 0;
            return ($a['sort'] > $b['sort']) ? -1 : 1;
        });

        $this->assign('items',$items);
        $this->assign('deep',$deep);
        $this->display('dataTree');
    }

    public function code($pid) {
        $errors = M('interface_error')->where(array('project_id' => $pid))->order('code asc')->select();
        $items = M('interface')->where(array('project_id' => $pid,'status' => 1))->order('sort desc')->select();
        $docs = M('document')->where(array('project_id' => $pid,'status' => 1))->order('sort desc')->select();

        $this->title = $this->project['title'].'的错误码列表';
        $this->assign('list',$items);
        $this->assign('docs',$docs);
        $this->assign('errors',$errors);
        $this->display();
    }

    public function doc($id) {
        $document = M('document')->where(array('id' => $id))->find();

        $this->title = $document['title'];
        $this->assign('document',$document);
        $this->display();
    }
}