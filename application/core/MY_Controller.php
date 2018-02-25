<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MY_Controller
 *
 * @property Util $util 
 * @property MY_Input $input 
 * 
 * @author phuongnt
 */
//require_once 'My_Input.php';

class MY_Controller extends CI_Controller {

    var $_api = 'http://service.bth.mobo.vn/api?app=api&';
    //var $_api = 'http://push.local/pushApi/';

//    var $_url_api_check = 'api?control=PushFace&func=check_access_token&app=api';
//    var $_url_api_insert = 'api?control=pushnoti&func=facebook&app=api';
//    var $_url_api_check_like = 'api?control=PushFace&func=checkLikeFanPage&app=api';
//    var $_url_api_insert_list_invite = 'api?control=PushFace&func=insertlistinvite&app=api';
//    var $_url_api_get_list_invite = 'api?control=PushFace&func=getlistinvite&app=api';
//    var $_url_api_accept_invite = 'api?control=PushFace&func=acceptinvite&app=api';
//    var $_url_api_post_wall = 'api?control=PushFace&func=postwallevent&app=api';
    var $_token_key_api = 'a7aa593d176e2287c606daac47b3de51';

    function __construct() {
        parent::__construct();
        //$CI =& get_instance();   
        $param_fb = $this->input->get_param();
        //log::param($param_fb['character_name']);
    }

//    function get_param($field_name, $default = NULL){
//        $value = $this->get_post($field_name);
//        
//        return empty($value) ? $default : $value;
//    }
    public function get_to_url($url) {
        //var_dump($url);
        //die;
        $this->benchmark->mark('code_start');
        $this->load->library('curl');
        $return = $this->curl->get($url);
        $this->benchmark->mark('code_end');
        $time = $this->benchmark->elapsed_time('code_start', 'code_end');
        //var_dump($return);
        //die;
        //log::write('[ '.$url.' ] '.$return.' ['.$time.' ]');
        return $return;
    }

    public function get_param_url($action = 0, $control = FALSE, $access_token = '') {
        $param_fb = $this->input->get_param();
        unset($param_fb['app']);
//       if (!empty($param_fb['info'])) {
//           $info = json_decode($param_fb['info'], true);           
//            unset($param_fb['info']);
//            $param_fb['character_id'] = sprintf("%.0f",  $info['character_id']);
//            $param_fb['character_name'] = $info['character_name'];
//            $param_fb['server_id'] = $info['server_id'];
//        }
        if (!empty($param_fb["token"])) {
            $token = $param_fb["token"];
            unset($param_fb["token"]);
        }
        if (!empty($param_fb['code'])) {
            unset($param_fb['code']);
        }
        if (!empty($param_fb['state'])) {
            unset($param_fb['state']);
        }

        if ($control) {
            unset($param_fb['control']);
        }
        if (!empty($access_token)) {
            $param_fb['fb_access_token'] = $access_token;
        }
        foreach ($param_fb as $key => $value) {
            $pa .= '&' . $key . '=' . $value;
        }
        $token = md5(implode('', $param_fb) . $this->_token_key_api);

        if (!empty($token)) {
            $pa .= "&token=" . $token;
        }

        if ($action != 0) {
            $pa[0] = '?';
        }
        //var_dump($pa);
        //die;
        $return = array(
            'url' => $pa,
            'token' => $token
        );

        return $return;
    }

}
