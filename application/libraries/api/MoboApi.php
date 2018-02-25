<?php

/**
 * Created by Ivoglent Nguyen.
 * User: longnv
 * Date: 11/22/13
 * Time: 1:51 PM
 * Project : services
 * File : capi.php
 */
define("DS", DIRECTORY_SEPARATOR);
define("MOBO_OAUTH_ADDRESS", 'http://oauth.mobo.vn/');
define("GOMOBI_PAYMENT_ADDRESS", 'http://api.gomobi.vn/?control=payment&func=get_pay');
define("PAYMENT_ADDRESS", "http://api.3t.mobo.vn/");
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "ApiResponse.php");
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "ApiRequest.php");

class MoboApi {

    public $config = array();
    public $response;
    public $request;
    public $app = "3t";
    public $private_key = "3t!@#t3";

    public function init() {
        
    }

    public function setAuth($app, $key) {
        $this->app = $app;
        $this->private_key = $key;
        return $this;
    }

    public function __construct() {
        $api_config = array(
            "log" => false,
            "logs_path" => LOG_PATH,
        );
        $this->config = array_merge($this->config, $api_config);
        //require_once("ApiRequest.php");
        //require_once("ApiResponse.php");
        $this->response = new ApiResponse($api_config);
        $this->request = new ApiRequest($api_config);
    }

    public function get_request_token($params) {
        $temp = $params;
        if (isset($temp["func"]))
            unset($temp["func"]);
        if (isset($temp["control"]))
            unset($temp["control"]);
        if (isset($temp["app"]))
            unset($temp["app"]);
        if (isset($temp["token"]))
            unset($temp["token"]);
        $temp = http_build_query($temp);
        $temp = str_replace("&", "", $temp);
        return md5($temp . $this->private_key);
    }

    public function verify_mobo_token($token, $bol = true) {
        if (YII_DEBUG)
            return "ivoglent";
        $url = MOBO_OAUTH_ADDRESS . "?control=oauth&func=valid_access_token&access_token=" . $token . "&app=" . $this->app . "&token=" . md5($token . $this->private_key);
        $result = $this->request->get($url);
        if ($result == null || $result->code != 50) {
            if ($bol)
                return false;
            $this->response->code(404);
            $this->response->assign("message", "Unverified");
        }
        else {
            if ($bol)
                return $result->data->phone;
            $this->response->code(200);
            $this->response->assign("mobo_account", $result->data->phone);
            $this->response->assign("message", "Verified");
        }
        $this->response->output();
    }

    public function mobo_id_logout($account) {
        $url = "http://id.mobo.vn/api/logout/" . $account . "/" . md5($account . "longnv@mecorp");
        $result = $this->request->get($url);
        if ($result == null || $result->error != 0) {
            $this->response->code(404);
            $this->response->assign("message", "Can not longout");
        } else {

            $this->response->code(200);
        }
        $this->response->output();
    }

    public function get_list_character($game_account, $server_id = null) {
        $param = array(
            "game_account" => is_array($game_account) ? implode(",", $game_account) : $game_account,
            "server_id" => $server_id
        );

        $token = $this->get_request_token($param);
		
        return $this->request->get("http://api.3t.mobo.vn/?control=api&func=get_list_account&" . http_build_query($param) . "&app=3t&token=" . $token);
    }

    public function get_server_list() {
        return $this->request->get("http://api.3t.mobo.vn/?control=api&func=get_list_server&app=3t&token=" . md5($this->private_key));
    }
    
    public function get_api_all_character($game_account, $server_id = null) {
        $param = array(
            "game_account" => is_array($game_account) ? implode(",", $game_account) : $game_account,
            "server_id" => $server_id
        );

        $token = $this->get_request_token($param);
        return $this->request->get("http://api.3t.mobo.vn/?control=api&func=get_list_account&" . http_build_query($param) . "&app=3t&token=" . $token);
    }
	
	public function get_facebook_name($id){
        $fb_graph_url="https://graph.facebook.com/";
        $result= $this->request->get($fb_graph_url.$id);
        if(!empty($result) && is_array($result)) return $result['name'];
        else return null;
    }
    

}