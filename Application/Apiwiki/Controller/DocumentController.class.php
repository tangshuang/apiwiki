<?php

namespace Apiwiki\Controller;

class DocumentController extends __ {
    public function __construct() {
        parent::__construct();
        parent::__check_and_set();
    }

    public function index($pid) {
        $items = M('document')->where(array('project_id' => $pid,'status' => 1))->select();
        $this->title = '文档列表';
        $this->assign('items',$items);
        $this->display();
    }

    public function add($pid) {
        if(IS_POST) {
            $DocumentModel = M('document');
            $DocumentModel->create();
            $DocumentModel->create_time = NOW_DATETIME;
            $DocumentModel->status = 1;
            $DocumentModel->add();
            $this->success('添加成功。',U('index',array('pid' => $pid)));
            exit;
        }
        $this->title = '添加文档';
        $this->display('edit');
    }

    public function edit($pid,$id) {
        if(IS_POST) {
            $DocumentModel = M('document');
            $DocumentModel->create();
            $DocumentModel->save();
            if($result === false)
                $this->error($DocumentModel->getError());
            else
                $this->success('更新成功',U('index',array('pid' => $pid)));
            exit;
        }
        $document = M('document')->where(array('id' => $id))->find();
        $this->title = '编辑文档';
        $this->assign('document',$document);
        $this->display();
    }

    public function delete($pid,$id) {
        $DocumentModel = M('document');
        $result = $DocumentModel->where(array('id' => $id))->delete();

        if($result === false)
            $this->error($DocumentModel->getError());
        else
            $this->success('删除成功');
    }
}