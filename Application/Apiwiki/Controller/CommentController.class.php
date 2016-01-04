<?php

namespace Apiwiki\Controller;

class CommentController extends __ {
    public function __construct() {
        parent::__construct();
    }
    public function index() {
        $comments = M('comment')->where(array('user_id' => $this->user['id'],'status' => 1))->select();

    }
}