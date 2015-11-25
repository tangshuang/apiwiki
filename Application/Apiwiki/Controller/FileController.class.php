<?php

namespace Apiwiki\Controller;

class FileController extends __ {
    public function __construct() {
        parent::__construct();
    }
    public function uploadPicture() {
        $return = array('error' => 'filetype');
        $result = parent::uploadPicture('file');
        if($result) {
            $return = array(
                'error' => false,
                'path' => site_url($result['rootpath'].$result['savepath'].$result['savename'])
            );
        }
        else {
            $return = array('error' => 'unknown');
        }
        $this->ajaxReturn($return);
    }
}