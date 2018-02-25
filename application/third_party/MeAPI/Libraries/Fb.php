<?php

require_once APPPATH . 'third_party/Facebook/facebook.php';

class Fb extends Facebook {

    private $CI;
    public $facebook_config;

    public function __construct() {
        $this->CI = & get_instance();
        $facebook_config = MeAPI_Config_Game::facebook();
        $this->facebook_config = $facebook_config;
        parent::__construct(array(
            'appId' => $facebook_config['client_id'],
            'secret' => $facebook_config['client_secret'],
            'fileUpload' => FALSE
        ));
    }

    public function is_like($page_id) {
        try {
            $res = $this->api('/me/likes/' . $page_id);
            if ($res['data'] || $res['paging']) {
                return TRUE;
            }
            return FALSE;
        } catch (Exception $exc) {
            return FALSE;
        }
    }

    public function post_wall($params) {
        try {
            $res = $this->api('/me/feed', 'POST', $params);
            return $res;
        } catch (Exception $exc) {
            return FALSE;
        }
    }
}