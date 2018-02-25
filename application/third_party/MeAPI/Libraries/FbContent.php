<?php

class FbContent {

    private $CI;

    public function __construct() {

        $this->CI = & get_instance();
    }

    public function share() {

        return array(
            'link' => 'http://mt.mobo.vn',
            'message' => 'Hello',
            'picture' => 'http://mt.mobo.vn/frontend/assets/user/teaser/thuvien/full/01.jpg',
        );
    }

}