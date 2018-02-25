<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once APPPATH . 'third_party/MeAPI/Autoloader.php';
require_once APPPATH . '/core/EI_Controller.php';

/**
 * Description of social
 *
 * @author vietbl
 */
class social extends EI_Controller {

    protected $data;
    public $request_data = array();
    public $menu_data = array();
    public $arraylist = array('115.78.161.124', '115.78.161.88', '118.69.76.212', '14.161.5.226');
    public $arrayimage = array('ico-1.png', "ico-2.png", "ico-3.png", "ico-4.png", "ico-5.png", "ico-6.png");

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        MeAPI_Autoloader::register();
    }

    public function index() {
        //var_dump($_SERVER);
        $params = $this->input->get();
//        if(strtolower($params["platform"]) == "ios"){
//            echo $c = $this->load->view("event/ios", array(), true);
//            die;
//        }
        // verify link 
        $token = trim($params['token']);
        unset($params['token']);
        $valid = md5(implode('', $params) . $this->private_key);
        if ($valid != $token) {
            echo "Truy cập không hợp lệ!";
            die;
        }
        if (empty($params["info"]) || $params["info"] == "(null)") {
            echo $c = $this->load->view("none", $assigns, true);
            die;
        }
        //assigned data
        $querystring = $this->get_query_string();

        $this->NAVIGATION();
        return;
    }

    public function temp() {
        $params = $this->input->get();
        // verify link 
        $token = trim($params['token']);
        unset($params['token']);
        $valid = md5(implode('', $params) . $this->private_key);
        if ($valid != $token) {
            echo "Truy cập không hợp lệ!";
            die;
        }
        if (empty($params["info"]) || $params["info"] == "(null)") {
            echo $c = $this->load->view("none", $assigns, true);
            die;
        }
        //assigned data
        $querystring = $this->get_query_string();
        $this->request_data = $params;
        $this->request_data["info"] = json_decode($params["info"], true);
        $this->request_data["querystring"] = $querystring;

        //$this->write_log($querystring);

        $this->NAVIGATION();
        return;
    }

    //START---NAVIGATION
    FUNCTION NAVIGATION() {
        if (isset($_POST['ACTION'])) {
            $ACTION = $_POST['ACTION'];
            if (is_callable(array($this, "$ACTION"))) {
                $this->{$ACTION}();
            } else {
                die('Sự Kiện Không Tồn tại');
            }
            die;
        }
        $this->load->model('cms/navigation_model');
        $assigns['SERVICES'] = $this->navigation_model->onGets();
        $this->load->library('GameFullAPI');
        $api = new GameFullAPI();

        $assigns["ip"] = $api->get_remote_ip();
        $assigns["controler"] = $this;

        //Get Total    
        $user = $this->get_info();
        $this->load->model('events/m_tichluy', "tichluy", false);
        $tournament_list = $this->tichluy->tournament_totaldate_list();
        if (count($tournament_list) == 0) {
            $this->data["server_total"] = 0;
        } else {
            $tournament_filter = array();

            foreach ($tournament_list as $key => $value) {
                $server_list = preg_replace('/\s+/', '', $value["tournament_server_list"]);
                $server_list = explode(";", $server_list);
                if (in_array($user->server_id, $server_list)) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "reward_percent" => $value["reward_percent"]));
                }
            }

            foreach ($tournament_filter as $key => $value) {
                //Get Money
                $getmoney_api = $api->get_money($this->service_name, NULL, $user->server_id, $value["tournament_date_start"], $value["tournament_date_end"], 2);
                $percent_server = ($getmoney_api["amount"] / 100) * $value["reward_percent"];
            }

            $assigns["server_total"] = ceil($percent_server / 100);
        }

        //Get Total LoiDai
        $this->load->model('events/m_toploidai', "toploidai", false);
        $tournament_loidai = $this->toploidai->tournament_list();
        if (count($tournament_loidai) > 0) {
            $server_list_loidai = preg_replace('/\s+/', '', $tournament_loidai[0]["tournament_server_list"]);
            $server_list_loidai = explode(";", $server_list_loidai);
            if (in_array($user->server_id, $server_list_loidai)) {
                $getmoney_api_loidai = $api->get_money($this->service_name, NULL, $server_list_loidai, $tournament_loidai[0]["tournament_date_start"], $tournament_loidai[0]["tournament_date_end"], 2);
                $percent_server_loidai = ($getmoney_api_loidai["amount"] / 100) * $tournament_loidai[0]["reward_percent"];    
                $assigns["server_total_loidai"] = ceil($percent_server_loidai / 100);
            }
            else{
                 $assigns["server_total_loidai"] = 0;
            }
        }
        else{
            $assigns["server_total_loidai"] = 0;
        }

        echo $c = $this->load->view("event/NAVIGATION", $assigns, true);
        die;
    }

    public function local_filter() {
        $this->local_ip = array("115.78.161.124", "171.254.24.192", "27.66.149.151", "101.99.44.169", "113.187.17.122", "14.169.162.131", "118.69.76.212", "115.78.161.88", "127.0.0.1", "14.161.5.226", "113.161.71.8", "118.69.76.21", "113.161.77.69", "113.161.78.101", "123.22.27.48");
        $this->remote_addr = $_SERVER['REMOTE_ADDR'];
        if (in_array($this->remote_addr, $this->local_ip)) {
            return true;
        }
        return false;
    }

    protected function assigned($name, $value) {
        $this->data[$name] = $value;
    }

    // get full request url
    protected function get_query_string() {
        return $_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'] : '';
    }

    //put your code here
}
