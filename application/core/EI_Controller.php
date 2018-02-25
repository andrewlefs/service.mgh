<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once APPPATH . 'third_party/MeAPI/Mq.php';
require_once APPPATH . 'controllers/grash/GraphShare/Definition.php';

class userinfo {

    public $character_id;
    public $character_name;
    public $mobo_service_id;
    public $mobo_id;
    public $server_id;
    public $server_name;
    public $device_id;

}

class EI_Controller extends CI_Controller {

    protected $account_info;
    protected $CI;
    protected $private_key = "dd627a5b58d2d7fb4528b59070dd923d";
    public $base_url;
    public $session_token = "";
    private $_config = array();
    protected $root_folder = "";
    protected $data = array();
    protected $event_key = "";
    protected $token;
    protected $content = '';
    private $template = array();
    protected $idgame = 150;
    protected $service_name = "150";
    public $gameapi;
    protected $tokenside = "7fe109s62d15c61g1f937deae1dc3d";
    protected $memcache;
    protected $memcache_status;

    public function __construct() {
        parent::__construct();
        $this->load->library('GameFullAPI');
        $this->base_url = $this->config->config['base_url'];
        $this->load->library('Mobile_Detect');

        //load memcache config
        if ($_SERVER['REMOTE_ADDR'] == "127.0.0.1") {
            $this->_config["memcache"] = array("host" => "127.0.0.1", "port" => 11211);
        } else {
            $this->_config["memcache"] = array("host" => "10.10.20.121", "port" => 11211);
        }
        $this->gameapi = new GameFullAPI();
        $this->memcache = new Memcache();

        $host = $this->_config["memcache"]["host"];
        $port = $this->_config["memcache"]["port"];
        $this->memcache_status = @$this->memcache->connect($host, $port);
    }

    public function write_log($message) {
        $this->config->load('mq_setting');
        $mq_config = $this->config->item('mq');
        $config['routing'] = $mq_config['mq_routing'];
        $config['exchange'] = $mq_config['mq_exchange'];
        $ddd = MEAPI_Mq::push_rabbitmq($config, $message);
        return $ddd;
    }

    public function is_tester($mobo_id, $service_id) {
        $data = $this->get_mobo_account($mobo_id);
        $mobo = $this->parse_mobo($data, $service_id);
        if (!empty($mobo)) {
            $phone = $mobo[0]["phone"];
            return (strpos($phone, "19006611") !== false && strpos($phone, "19006611") == 0);
        }
        return false;
    }

    public function get_event() {
        return $this->event_key;
    }

    //Load layout
    public function layout($disheader = true) {

        $this->data["controler"] = $this;
        // making temlate and send data to view.
        if ($disheader) {
            $this->template['header'] = $this->load->view('event/layout/header', $this->data, true);
        }
        $this->template['content'] = $this->load->view($this->content, $this->data, true);
        $this->template['footer'] = $this->load->view('event/layout/footer', $this->data, true);
        echo $this->load->view('event/layout/index', $this->template, true);
        exit;
    }

    public function get_info_v2() {
        $data = json_decode($_SESSION['linkinfo']['info'], true);
        $access_token = $_SESSION['linkinfo']["access_token"];
        $access_token_debase64 = base64_decode($access_token);
        $access_token_dejson = json_decode($access_token_debase64, true);
        $data = array_merge($data, $access_token_dejson);
        $this->user = new userinfo();
        $this->user->character_id = $data["character_id"];
        $this->user->character_name = $data["character_name"];
        $this->user->mobo_id = $data["mobo_id"];
        $this->user->mobo_service_id = $data["mobo_service_id"];
        $this->user->server_id = $data["server_id"];
        $this->user->server_name = $data["server_name"];
        $this->user->device_id = $_SESSION['linkinfo']['device_id'];
        $this->user->lang_id = strtolower($data['lang_id']);
        return $this->user;
    }

    protected function get_info($inputs = null) {

        if ($inputs == true) {
            $params = $inputs;
        } else {
            $params = $_GET;
        }

        $data = json_decode($params['info'], true);
        $access_token = $params["access_token"];
        $access_token_debase64 = base64_decode($access_token);
        $access_token_dejson = json_decode($access_token_debase64, true);
        if ($data == true && $access_token_dejson == true)
            $data = array_merge($data, $access_token_dejson);
        if ($data == false) {
            $data = $access_token_dejson;
        }
        $user = new userinfo();
        $user->character_id = $data["character_id"];
        $user->character_name = $data["character_name"];
        $user->mobo_id = $data["mobo_id"];
        $user->mobo_service_id = $data["mobo_service_id"];
        $user->server_id = $data["server_id"];
        $user->device_id = $data["device_id"];
        $user->server_name = $data["server_name"];
        $user->device_id = $_GET["device_id"];
        return $user;
    }

    public function time_unique($ticker = 1) {
        $second = round(intval(date("s", time())) / $ticker);
        return (date("YmdHi", time()) . $second);
    }

    public function convert_date($create_time) {
        $mil = $create_time;
        $seconds = (int) ($mil / 1000);
        $date = date("Y-m-d H:i:s", $seconds);
        $currentTime = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s", time()));
        $createTime = DateTime::createFromFormat("Y-m-d H:i:s", $date);
        $interval = $currentTime->diff($createTime);
        $day = $value;
        $markday = $interval->d + ($interval->h / 24) + (($interval->i / 60) / 24) + ((($interval->s / 60) / 60) / 24);
        return $markday;
    }

    protected function get_secret_key_by_game_id($game_id) {
        switch ($game_id) {
            case 103: return "7d7668e12333e9546091954fbad0ebe2";
            case 106: return "16e511dc950d7e661e5c4a75548ddc69";
            case 107: return "47120942a61d7cecd88a8588891c6ea5";
            case 108: return "869c15e0367e557be4a2bb4feb057839";
            case 113: return "e0ba906078a64485cde6b77c8697e0bd";
            case 119: return "5bfef511de26f632735e4f22fab377c0";
            case 118: return "iwin";
            case 125: return "4aac3047abd6bb4f6232d21567a7472c";
            case 128: return "08d33e34438bcc264cb04b298828e3ba";
            case 130: return "bb6c1e44ce23cb4f156810229095bcbe";
            case 133: return "fc6aef35aeee422d83d91ef10eee2737";
            case 114: return "7fe109fa62e15c6191f9377eae1ddc3d";
            case 101: return "b5c8f633cec4bf7b7c3d5995189786f0";
            case 129: return "45d0e1c45f21f18ce79375d54d2717b6";
            case 138: return "18f9f0e62716973b2852b3585dd2e42f";
            case 139: return "5b60526724106275eb5600c806f9bb07";
            case 140: return "3af2e86cf9f7862f809d7b1bc060d146";
            case 142: return "e639e61d0118112d0f53d148d828e841";
            case 143: return "3f386df18f1960fa3e92b004123317a4";
            case 146: return "71a5ef7ecb7b29f86a2613a5e9c629e4";
            case 149: return "2dc718cb8fefcb17ed2485de09c7f3e3";
            default: return $this->private_key;
        }
    }

    public function get_service_name($service_id) {
        switch ($service_id) {
            case 59: return "gopet";
            case 103: return "eden";
            case 106: return "monggiangho";
            case 107: return "aow";
            case 108: return "bog";
            case 113: return "hiepkhach";
            case 119: return "phongthan";
            case 118: return "iwin";
            case 125: return "125";
            case 128: return "128";
            case 130: return "130";
            case 133: return "133";
            case 114: return "tethien3d";
            case 101: return "skylight";
            case 129: return "129";
            case 138: return "138";
            case 139: return "139";
            case 140: return "140";
            case 143: return "143";
            case 142: return "142";
            case 146: return "146";
            default: return "game";
        }
    }

    public function get_host_name($service_id) {
        switch ($service_id) {
            case 59: return "gopet";
            case 103: return "eden";
            case 105: return "naruto";
            case 106: return "http://service.mgh.mobo.vn/social/";
            case 107: return "/aow/index.php/social/";
            case 108: return "/bog/index.php/social/";
            case 113: return "/hiepkhach/index.php/social/";
            case 119: return "/phongthan/index.php/social/";
            case 118: return "iwin";
            case 125: return "/giangma/index.php/social/";
            case 128: return "/lol/index.php/social/";
            case 130: return "/tieuhiep/index.php/social/";
            case 133: return "/acdau/index.php/social/";
            case 114: return "/tethien/index.php/social/";
            case 129: return "/nghichtd/index.php/social/";
            case 138: return "/moba/index.php/social/";
            case 139: return "/onepiece/index.php/social/";
            case 140: return "/doden/index.php/social/";
            case 143: return "/lucgioi/index.php/social/";
            case 146: return "/mathan/index.php/social/";
            case 142: return "/wow/index.php/social/";
            case 101: return "skylight";
            default: return "game";
        }
    }

    public function get_link_css($service_id) {
        switch ($service_id) {
            case 59: return "gopet";
            case 103: return "eden";
            case 105: return "naruto";
            case 106:
                if ($_SERVER['REMOTE_ADDR'] == "127.0.0.1") {
                    return "http://test.acdau.mobo.vn/acdau";
                } else {
                    return "http://service.mgh.mobo.vn";
                }
            case 107: return "http://game.mobo.vn/aow";
            case 108:
                if ($_SERVER['REMOTE_ADDR'] == "127.0.0.1") {
                    return "http://test.acdau.mobo.vn/acdau";
                } else {
                    return "http://game.mobo.vn/bog";
                }
            case 113: return "http://game.mobo.vn/hiepkhach";
            case 119: return "http://game.mobo.vn/phongthan";
            case 118: return "iwin";
            case 125: return "http://game.mobo.vn/giangma";
            case 128: return "http://game.mobo.vn/lol";
            case 130: return "http://game.mobo.vn/tieuhiep";
            case 133: return "http://game.mobo.vn/acdau";
            case 114: return "http://game.mobo.vn/tethien";
            case 129: return "http://game.mobo.vn/nghichtd";
            case 138: return "http://game.mobo.vn/moba";
            case 139: return "http://game.mobo.vn/onepiece";
            case 140: return "http://game.mobo.vn/deden";
            case 143: return "http://game.mobo.vn/lucgioi";
            case 146: return "http://game.mobo.vn/mathan";
            case 142: return "http://game.mobo.vn/wow";
            case 101: return "skylight";
            default: return "game";
        }
    }

    public function parse_server_name($service_id, $server_id) {
        $serverlist = $this->get_server_list($service_id);
        if ($serverlist == FALSE)
            return NULL;
        foreach ($serverlist as $key => $value) {
            if ($value["server_id"] == $server_id)
                return $value;
        }
    }

    public function get_server_list($service_name) {        
        $api = new GameFullAPI();
        $sv_keycache = $this->service_name . "svls_" . $service_name;
        $serverlist = $this->getMemcache($sv_keycache);
        if ($serverlist == false) {
            $result = $api->get_list_server($service_name);
            if ($result["code"] == 500102) {
                $serverlist = $result["data"];
                $this->saveMemcache($sv_keycache, $serverlist, 3 * 3600);
            } else {
                $this->saveMemcache($sv_keycache, null);
            }
        }
        return $serverlist;
    }

    protected function isDevice() {
        $mobile = new Mobile_Detect();
        if ($mobile->isTablet()) {
            if ($mobile->is("iOS")) {
                return "iPad - OS " . $mobile->version('iPad');
            } else {
                return "Android - OS " . $mobile->version('Android');
            }
        } else if ($mobile->isMobile()) {
            if ($mobile->is("iOS")) {
                return "iPhone - OS "; // . $mobile->version('iPhone');
            } else {
                return "Android - OS " . $mobile->version('Android');
            }
        } else {
            return "Web Browser";
        }
    }

    private $_control = 'inside';
    private $_getinfo_func = 'search_graph';
    private $_app = 'skylight';
    private $_api_url = 'https://graph.mobo.vn/';
    private $_api_cs = 'http://s4-graph.mobo.vn/';
    protected $service_id = 108;
    protected $_key = "QEOODZHBTPE6ZJI7";

    public function verify_access_token($access_token) {
        if (empty($access_token)) {
            return false;
        }
        //http://graph.mobo.vn/?control=user&func=verify_access_token&access_token=

        $params['control'] = "user";
        $params['func'] = "verify_access_token";
        $params["access_token"] = $access_token;
        $url = $this->_api_url . '?' . http_build_query($params);

        if ($_SERVER['REMOTE_ADDR'] == "127.0.0.1") {
            $response = '{"code":500040,"desc":"VERIFY_ACCESS_TOKEN_SUCCESS","data":{"mobo_id":"128147013","mobo_service_id":"1251517070127511375","data":"eyJ1c2VyX2FnZW50IjoiRGFsdmlrXC8xLjYuMCAoTGludXg7IFU7IEFuZHJvaWQgNC4yLjI7IERyb2lkNFgtV0lOIEJ1aWxkXC9KRFEzOUUpfCBNb2JvIFNESyAyLjMuMS44LjIwMTUxMjA5IiwicGxhdGZvcm0iOiJhbmRyb2lkIiwidGVsY28iOiJDSElOQSBNT0JJTEUiLCJjaGFubmVsIjoiM3xtZXwxLjcuNnxHUHxtc3ZfMTAwX3N0b3JlIiwibW9ib19zZXJ2aWNlX2lkIjoiMTI1MTUxNzA3MDEyNzUxMTM3NSIsInNlcnZpY2VfaWQiOiIxMjUiLCJkZXZpY2VfaWQiOiJlMDM5LTRhZmItYWIxZS05MDhkLTk5ZDYtMDNhOS1iYzQzLWIyYzEiLCJsYW5nIjoidmkiLCJ2ZXJzaW9uIjoiMS43LjYiLCJhcHAiOiIxMjUiLCJpZGVudGlmeSI6IjU4ODc1MzEyODE0NzAxMyIsInBhY2thZ2VfbmFtZSI6InF1eS5oYXUudnVvbmcuZnVsbC5nYW1lLm1vYm8ifQ==","active":true,"linked":{"facebook":"","google":""},"fullname":"S\u00e1u Ngh\u0129a","service_id":"125","approving":{"ios":["msv_2","msv_4","msv_6","msv_8","msv_10","msv_12","msv_18","msv_22"],"android":["msv_2","msv_24"]}},"message":"Chu\u1ed7i ch\u1ee9ng th\u1ef1c h\u1ee3p l\u1ec7"}';
        } else {
            $response = $this->get($url);
        }
        if ($response == false) {
            return false;
        }
        $result = json_decode($response, TRUE);
        if (isset($result["code"]) && intval($result["code"]) == 500040) {
            $database64 = $result["data"]["data"];
            $datadebase64 = base64_decode($database64);
            $datadejson = json_decode($datadebase64, true);
            return $datadejson;
        } else {
            return false;
        }
    }

    public function get_mobo_account($mobo_id) {
        $this->load->library('GeneralOTPCode');
        $otp = GeneralOTPCode::getCode($this->_key);
        $params['control'] = $this->_control;
        $params['func'] = $this->_getinfo_func;
        $params['app'] = $this->_app;
        $params['otp'] = $otp;
        $params['mobo'] = $mobo_id;
        $params['user_agent'] = "empty";
        $params['channel'] = "1";
        $params['ip_user'] = "";

        $needle = array('control', 'func', 'access_token', 'user_agent', 'app', 'otp');
        $params['token'] = md5(implode('', $params) . $this->_key);
        $url = $this->_api_url . '?' . http_build_query($params);
        if ($_SERVER['REMOTE_ADDR'] == "127.0.0.1") {
            $response = '{"code":900000,"desc":"SEARCH_GRAPH_SUCCESS","data":{"114":[{"mobo_id":"128147013","mobo_service_id":"1141517733500197568","fullname":"S\u00e1u Ngh\u0129a","device_id":"c562d37e8027017feb50c410f717ca903881332a","channel":"2|me|3.0.2|Appstore|msv_6_store","date_create":"2015-11-13 21:03:23","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"107":[{"mobo_id":"128147013","mobo_service_id":"1071500395737754192","fullname":"S\u00e1u Ngh\u0129a","device_id":"1646-e619-0eb1-a8d1-0000-0030-00f0-0ca8","channel":"3|me|1.1.1|GP|msv_10_store","date_create":"2015-05-06 12:15:54","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"},{"mobo_id":"128147013","mobo_service_id":"1071500395973232102","fullname":"S\u00e1u Ngh\u0129a","device_id":"1646-e619-0eb1-a8d1-0000-0030-00f0-0ca8","channel":"3|me|1.1.1|GP|msv_10_store","date_create":"2015-05-06 12:15:55","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"103":[{"mobo_id":"128147013","mobo_service_id":"1031504929525518311","fullname":"S\u00e1u Ngh\u0129a","device_id":"34cf08865d7ed7495a322ba23c0afd8d9b0e6482","channel":"3|me|2.3.3|GP|msv_19_store","date_create":"2015-06-25 13:10:36","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"113":[{"mobo_id":"128147013","mobo_service_id":"1131509086362929219","fullname":"S\u00e1u Ngh\u0129a","device_id":"c562d37e8027017feb50c410f717ca903881332a","channel":"2|me|1.0.0|Appstore|msv_2_store","date_create":"2015-08-10 10:21:45","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"119":[{"mobo_id":"128147013","mobo_service_id":"1191512347633980396","fullname":"S\u00e1u Ngh\u0129a","device_id":"40dec09a1c45cfe0ca0cd044fce870c369877830","channel":"2|me|1.0.0|Appstore|msv_2_store","date_create":"2015-09-15 10:18:16","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"108":[{"mobo_id":"128147013","mobo_service_id":"1081513196980337692","fullname":"S\u00e1u Ngh\u0129a","device_id":"7f3b1315c9c0521d5de968c01c0f3e429ac3ec97","channel":"1|me|1.0.172|Ent|msv_5_file","date_create":"2015-09-24 19:18:15","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"106":[{"mobo_id":"128147013","mobo_service_id":"1061495878891844701","fullname":"S\u00e1u Ngh\u0129a","device_id":"34cf08865d7ed7495a322ba23c0afd8d9b0e6482","channel":"1|me|1.0.2|Ent|msv_1","date_create":"2015-03-17 15:34:39","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"125":[{"mobo_id":"128147013","mobo_service_id":"1251517070127511375","fullname":"S\u00e1u Ngh\u0129a","device_id":"3a48-d0a8-ab1e-908d-99d6-03a9-bc43-b2c1","channel":"google-play","date_create":"2015-11-06 13:19:39","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"128":[{"mobo_id":"128147013","mobo_service_id":"1281516923955322915","fullname":"S\u00e1u Ngh\u0129a","device_id":"7f3b1315c9c0521d5de968c01c0f3e429ac3ec97","channel":"google-play","date_create":"2015-11-04 22:36:22","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"122":[{"mobo_id":"128147013","mobo_service_id":"1221521950014915448","fullname":"S\u00e1u Ngh\u0129a","device_id":"e039-4afb-ab1e-908d-99d6-03a9-bc43-b2c1","channel":"1|me|1.0.7.100|Ent|msv_1_file","date_create":"2015-12-30 10:04:00","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"121":[{"mobo_id":"128147013","mobo_service_id":"1211510104875769335","fullname":"S\u00e1u Ngh\u0129a","device_id":"34cf08865d7ed7495a322ba23c0afd8d9b0e6482","channel":"1|me|1.0.0|Ent|msv_1_file","date_create":"2015-08-21 16:10:35","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"130":[{"mobo_id":"128147013","mobo_service_id":"1301519878916265247","fullname":"S\u00e1u Ngh\u0129a","device_id":"ae0f-a08a-ab1e-908d-99d6-03a9-bc43-b2c1","channel":"1|me|1.1.0|Ent|msv_1_file","date_create":"2015-12-07 13:22:54","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"133":[{"mobo_id":"128147013","mobo_service_id":"1331522674905955760","fullname":"S\u00e1u Ngh\u0129a","device_id":"e039-4afb-ab1e-908d-99d6-03a9-bc43-b2c1","channel":"1|me|10.2.2|Ent|msv_1_file","date_create":"2016-01-07 10:05:50","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"137":[{"mobo_id":"128147013","mobo_service_id":"1371528293251190256","fullname":"S\u00e1u Ngh\u0129a","device_id":"591361796bd528ebfe02376170fdd2d29122bc8d","channel":"1|me|1.0|Ent|msv_1_file","date_create":"2016-03-09 10:27:01","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"152":[{"mobo_id":"128147013","mobo_service_id":"1521542968042047804","fullname":"S\u00e1u Ngh\u0129a","device_id":"f4aaf937d35230b8f5a8cf9ea3adb4ecd4a6c369","channel":"1|me|1.0.0|Ent|msv_1_file","date_create":"2016-08-18 09:56:32","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"149":[{"mobo_id":"128147013","mobo_service_id":"1491537720525386025","fullname":"S\u00e1u Ngh\u0129a","device_id":"f9b3-9d66-2d2a-ba4e-99d6-03a9-bc43-b299","channel":"1|me|1.0.0.0|Ent|msv_1_file","date_create":"2016-06-21 11:49:30","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"148":[{"mobo_id":"128147013","mobo_service_id":"1481535171662510933","fullname":"S\u00e1u Ngh\u0129a","device_id":"ae0f-a08a-ab1e-908d-99d6-03a9-bc43-b2c1","channel":"","date_create":"2016-05-24 08:36:25","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"146":[{"mobo_id":"128147013","mobo_service_id":"1461538550573164759","fullname":"S\u00e1u Ngh\u0129a","device_id":"f9b3-9d66-2d2a-ba4e-99d6-03a9-bc43-b299","channel":"1|me|1.4.0.0|Ent|msv_1_file","date_create":"2016-06-30 15:42:46","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"144":[{"mobo_id":"128147013","mobo_service_id":"1441535101792494634","fullname":"S\u00e1u Ngh\u0129a","device_id":"ccdb24365ee1d49ed1d170e991ba429b188b3cf0","channel":"1|me||Ent|msv_1_file","date_create":"2016-05-23 14:05:52","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"143":[{"mobo_id":"128147013","mobo_service_id":"1431532213882189831","fullname":"S\u00e1u Ngh\u0129a","device_id":"f9b3-9d66-2d2a-ba4e-99d6-03a9-bc43-b299","channel":"1|me|1.1.8|Ent|msv_1_file","date_create":"2016-04-21 17:03:46","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"142":[{"mobo_id":"128147013","mobo_service_id":"1421538641599065148","fullname":"S\u00e1u Ngh\u0129a","device_id":"3ef3108eef426df933437afc7e2a6d79496b4182","channel":"1|me|1.2.6|Ent|msv_5_file","date_create":"2016-07-01 15:49:35","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"141":[{"mobo_id":"128147013","mobo_service_id":"1411529028944089386","fullname":"S\u00e1u Ngh\u0129a","device_id":"ae0f-a08a-ab1e-908d-99d6-03a9-bc43-b2c1","channel":"","date_create":"2016-03-17 13:20:33","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"},{"mobo_id":"128147013","mobo_service_id":"0","fullname":"S\u00e1u Ngh\u0129a","device_id":"ae0f-a08a-ab1e-908d-99d6-03a9-bc43-b2c1","channel":"","date_create":"2016-03-17 13:20:28","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"139":[{"mobo_id":"128147013","mobo_service_id":"1391530730693793987","fullname":"S\u00e1u Ngh\u0129a","device_id":"591361796bd528ebfe02376170fdd2d29122bc8d","channel":"1|me|1.0.6|Ent|msv_3_file","date_create":"2016-04-05 08:09:08","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"109":[{"mobo_id":"128147013","mobo_service_id":"1091507928318924879","fullname":"S\u00e1u Ngh\u0129a","device_id":"1646-e619-0eb1-a8d1-0000-0030-00f0-0ca8","channel":"1|me|1.0.0","date_create":"2015-07-28 15:35:08","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"105":[{"mobo_id":"128147013","mobo_service_id":"1051490237959950717","fullname":"S\u00e1u Ngh\u0129a","device_id":"1646-e619-0eb1-a8d1-0000-0030-00f0-0ca8","channel":"empty","date_create":"2015-01-14 09:12:35","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"},{"mobo_id":"128147013","mobo_service_id":"1051496965128263014","fullname":"Giang H\u1ed3","device_id":"3c534d15cdbd360b353483a8c1f044e4b3886b6c","channel":"2|me|1.0.4|Appstore|msv_2","date_create":"2015-03-29 15:19:55","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"102":[{"mobo_id":"128147013","mobo_service_id":"1021492806371063290","fullname":"server 2","device_id":"933957fe427b847ce321c53f4878044012b25577","channel":"2|me|2.0.3|Appstore","date_create":"2015-02-11 17:35:52","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"},{"mobo_id":"128147013","mobo_service_id":"1021492771838807056","fullname":"S\u00e1u Ngh\u0129a","device_id":"933957fe427b847ce321c53f4878044012b25577","channel":"2|me|2.0.3|Appstore","date_create":"2015-02-11 08:27:00","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"118":[{"mobo_id":"128147013","mobo_service_id":"1181516427896518077","fullname":"S\u00e1u Ngh\u0129a","device_id":"3a48-d0a8-ab1e-908d-99d6-03a9-bc43-b2c1","channel":"3|me|1.0.0|GP|msv_5_store","date_create":"2015-10-30 11:11:57","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"116":[{"mobo_id":"128147013","mobo_service_id":"1161507930449844984","fullname":"S\u00e1u Ngh\u0129a","device_id":"1646-e619-0eb1-a8d1-0000-0030-00f0-0ca8","channel":"1|me|1.0.0","date_create":"2015-07-28 16:09:01","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}],"101":[{"mobo_id":"128147013","mobo_service_id":"1011489789116512995","fullname":"S\u00e1u Ngh\u0129a","device_id":"34cf08865d7ed7495a322ba23c0afd8d9b0e6482","channel":"3|me|1.0.0","date_create":"2015-01-09 10:18:36","status":"actived","phone":"0909968087","email":null,"facebook_id":null,"facebook_orgin_id":"","google_id":null,"status_mobo":"actived"}]},"message":"SEARCH_GRAPH_SUCCESS"}';
        } else {
            $response = $this->get($url);
        }
        return json_decode($response, TRUE);
    }

    public function check_install($device_id, $force_id) {
        $this->load->library('GeneralOTPCode');
        $otp = GeneralOTPCode::getCode("HLDVM5IGJN3H3BAD");
        $params['control'] = "user";
        $params['func'] = __FUNCTION__;
        $params['device_id'] = $device_id;
        $params['service_id'] = $force_id;
        $params['app'] = "mgh";
        $params['otp'] = $otp;

        $needle = array('control', 'func', 'access_token', 'user_agent', 'app', 'otp');
        $params['token'] = md5(implode('', $params) . "HLDVM5IGJN3H3BAD");
        $url = $this->_api_cs . '?' . http_build_query($params);
        $response = $this->get($url);

        return json_decode($response, TRUE);
    }

    public function parse_mobo($data, $service_id = "") {
        if ($data["code"] == 900000) {
            if (empty($service_id))
                return $data["data"];
            else
                return $data["data"][$service_id];
        }
        return null;
    }

    public function get($url) {
        if (empty($url)) {
            return false;
        }
        return $this->request('GET', $url, 'NULL');
    }

    public function sign_http($params) {
        $curtoken = $params["token"];
        unset($params["token"]);
        if (isset($params["sign"])) {
            $resign = $params["sign"];
            $token = md5(implode('', $params) . $this->get_secret_key_by_game_id($resign));
            $params["token"] = $token;
        }
        return $params;
    }

    public function resign_http($params) {
        $curtoken = $params["token"];
        unset($params["token"]);
        if (isset($params["resign"])) {
            $resign = $params["resign"];
            $token = md5(implode('', $params) . $this->get_secret_key_by_game_id($resign));
            $params["token"] = $token;
        }
        return $params;
    }

    public function rebuild_http($url, $params) {
        $response_url = "";
        $parts = parse_url($url);
        parse_str($parts['query'], $query);
        $curtoken = $params["token"];
        unset($params["token"]);
        if ($query == true && !isset($query["cr"])) {
            if (isset($query["sign"])) {
                $sign = $query["sign"];
                $params = array_merge($query, $params);
                $token = md5(implode('', $params) . $this->get_secret_key_by_game_id($sign));
                $params["token"] = $token;
            } else {
                $params = array_merge($query, $params);
                $token = md5(implode('', $params) . $this->private_key);
                $params["token"] = $token;
            }
        } else {
            if (isset($query["cr"]))
                unset($query["cr"]);
            $params = array_merge($query, $params);
            $params["token"] = $curtoken;
        }
        return (isset($parts["scheme"]) ? $parts["scheme"] . "://" : "http://")
                . $parts["host"] . (isset($parts["port"]) ? ":" . $parts["port"] : "")
                . $parts["path"] . "?" . http_build_query($params);
    }

    protected function verify_uri() {
        $params = $this->input->get();
        $token = trim($params['token']);
        unset($params['token']);
        if (isset($params["no"]) && $params["no"] == true) {
            unset($params['no']);
            unset($params['p']);
            $serect = $this->get_secret_key_by_game_id($params["game_id"]);
            unset($params['event_id']);
            unset($params['game_id']);
            unset($params["force_id"]);
        } else {
            $serect = $this->private_key;
        }
        $valid = md5(implode('', $params) . $serect);
        if ($valid === $token) {
            return true;
        } else {
            return false;
        }
    }

    protected function verify_uri_session($params = array()) {
        //var_dump($params); die;
        if (empty($params) === TRUE) {
            $params = $this->input->get();
        }
        $token = trim($params['token']);
        unset($params['token']);
//        unset($params['p']);
//        unset($params['event_id']);
//        unset($params['game_id']);
        //unset($params["game_id"]);
        //unset($params["force_id"]);
        $valid = md5(implode('', $params) . $this->private_key);
        if ($valid === $token) {
            return true;
        } else {
            return false;
        }
    }

    protected function create_session_token() {
        $params = $this->input->get();
        $valid = md5(implode('', $params) . $this->private_key);
        $_SESSION["session_token"] = $valid;
        return $valid;
    }

    protected function validate_login($key) {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        $session_id = session_id();
        $session_save = $this->getMemcache("session_login" . $key);

        if (empty($session_save)) {
            return true;
        }
        return ($session_id === $session_save);
    }

    protected function store_login($key, $cachetime = 3600) {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        $memcache = new Memcache;
        $host = $this->_config["memcache"]["host"];
        $port = $this->_config["memcache"]["port"];
        $status = @$memcache->connect($host, $port);
        if ($status == true) {
            $mkey = md5('session_login' . $key);
            $memcache->set($mkey, session_id(), false, $cachetime);
            $memcache->close();
            return true;
        }
        return false;
    }

    private function unique_id($id) {
        $memcache = new Memcache;
        $host = $this->_config["memcache"]["host"];
        $port = $this->_config["memcache"]["port"];
        $status_memcache = @$memcache->connect($host, $port);
        if ($status_memcache == true) {
            $key = md5($id);
            $value = $memcache->get($key);
            if (empty($value)) {
                $memcache->set($key, $id, false, 3600);
                $memcache->close();
                return false;
            } else {
                return true;
            }
        } else {
            //kiem tra db 
            return false;
        }
    }

    protected function saveMemcache($key, $value, $cachetime = 3600) {
        if ($this->memcache_status !== true) {
            $host = $this->_config["memcache"]["host"];
            $port = $this->_config["memcache"]["port"];
            $this->memcache_status = @$this->memcache->connect($host, $port);
        }
        if ($this->memcache_status == true) {
            $mkey = md5($key);
            $this->memcache->set($mkey, $value, false, $cachetime);
            //$this->memcache->close();
            return true;
        }
        return false;
    }

    protected function getMemcache($key) {

        //$host = $this->_config["memcache"]["host"];
        //$port = $this->_config["memcache"]["port"];
        //$status = @$this->memcache->connect($host, $port);
        if ($this->memcache_status !== true) {
            $host = $this->_config["memcache"]["host"];
            $port = $this->_config["memcache"]["port"];
            $this->memcache_status = @$this->memcache->connect($host, $port);
        }
        if ($this->memcache_status == true) {
            $mkey = md5($key);
            $value = $this->memcache->get($mkey);
            //$this->memcache->close();
            return $value;
        }
        return null;
    }

    protected function init_settings($root_dir = "") {
        $this->root_folder = $root_dir;
    }

    protected function render($view) {
        $this->data["controler"] = $this;
        echo $this->load->view("{$this->root_folder}/{$view}", $this->data, true);
        exit();
    }

    protected function get_user_info($params) {
        $params["control"] = "user";
        $params["func"] = "get_account_info";
        return $this->_call($params);
    }

    protected function get_user_all_level($params) {
        $params["control"] = "user";
        $params["func"] = "get_account_all_level_info";
        return $this->_call($params);
    }

    public function set_token($token) {
        $this->token = $token;
        $p = $this->getMemcache($token);
        if ($p == true) {
            $block = $p + 1;
            $blocktime = 120;
            if ($block > 5) {
                $blocktime = 600;
            } else if ($block > 10) {
                $blocktime = 3600;
            }
            $this->saveMemcache($token, $block, $blocktime);
            return $block;
        } else {
            $this->saveMemcache($token, 1, 120);
            return 1;
        }
    }

    public function un_token() {
        $this->saveMemcache($this->token, null, 1);
    }

    public function invalid_token($token) {
        $p = $this->getMemcache($token);
        if (empty($token))
            return -1;
        return $this->set_token($token);
    }

    public function encrypt($params) {
        if (is_array($params)) {
            $input = json_encode($params);
        } else if (is_string($params)) {
            $input = $params;
        } else {
            throw new Exception('Encrypt data not format.');
        }
        $key_seed = $this->private_key;
        $input = trim($input);
        $block = mcrypt_get_block_size('tripledes', 'ecb');
        $len = strlen($input);
        $padding = $block - ($len % $block);
        $input .= str_repeat(chr($padding), $padding);
        // generate a 24 byte key from the md5 of the seed 
        $key = substr(md5($key_seed), 0, 24);
        $iv_size = mcrypt_get_iv_size(MCRYPT_TRIPLEDES, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        // encrypt 
        $encrypted_data = mcrypt_encrypt(MCRYPT_TRIPLEDES, $key, $input, MCRYPT_MODE_ECB, $iv);
        // clean up output and return base64 encoded 
        return base64_encode($encrypted_data);
    }

    //end function Encrypt() 

    public function decrypt($input) {
        $key_seed = $this->private_key;
        $input = base64_decode($input);
        $key = substr(md5($key_seed), 0, 24);
        $text = mcrypt_decrypt(MCRYPT_TRIPLEDES, $key, $input, MCRYPT_MODE_ECB, '12345678');
        $block = mcrypt_get_block_size('tripledes', 'ecb');
        $packing = ord($text{strlen($text) - 1});
        if ($packing and ( $packing < $block)) {
            for ($P = strlen($text) - 1; $P >= strlen($text) - $packing; $P--) {
                if (ord($text{$P}) != $packing) {
                    $packing = 0;
                }
            }
        }
        $text = substr($text, 0, strlen($text) - $packing);
        $data = json_decode($text, true);
        if (is_array($data)) {
            return $data;
        } else {
            return $text;
        }
    }

    protected function vn_remove($str) {
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );

        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return $str;
    }

    protected function _call($params) {
        set_time_limit(120);
        $this->last_link_request = $this->uri_api . "?" . http_build_query($params) . "&app=3k&data=json";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->last_link_request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        $result = curl_exec($ch);
        return $result;
    }

    protected function request($method, $url, $vars) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER["REMOTE_ADDR"]);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->followlocation);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl);
        //curl_setopt($ch, CURLOPT_COOKIEJAR, $this->pathcookie);
        //curl_setopt($ch, CURLOPT_COOKIEFILE, $this->pathcookie);
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
        }
        $data = curl_exec($ch);
        //var_dump($data);die;
        curl_close($ch);
        if ($data) {
            return $data;
        } else {
            return @curl_error($ch);
        }
    }

    public function checkcrosssale() {

        //start crosssale;
        $user = $this->get_info();
        if (!isset($user) || empty($user)) {
            echo $c = $this->load->view("none", $assigns, true);
            return;
        } else {
            require_once APPPATH . '/libraries/crosssale/crossSale.php';
            $crosssale = new crossSale();
            $crosssale->startCrosssale($this->idgame, $user);
        }
        return;
    }

    public function genRequest() {
        $request = array(
            "control" => "user",
            "func" => "request_access_token",
            "service_id" => 141,
            "access_token" => $_GET["access_token"]
        );

        $url = "http://graph.mobo.vn/?" . http_build_query($request);

        //echo $url;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER["REMOTE_ADDR"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
        $rs = curl_exec($ch);
        curl_close($ch);
        $rsd = json_decode($rs, true);

        if ($rsd == true && $rsd["code"] == 500040) {
            $data = array(
                "access_token" => $rsd["data"]["access_token"],
                "platform" => $_GET["platform"],
                "app" => $_GET["app"],
            );

            $token = md5(implode("", $data) . "709AAc1Ed8c7c");
            $data["token"] = $token;
            return "S0002://action=open_browser&client_id=S0002&url=" . urlencode("http://3k.mobo.vn/caorua3k/index.html?" . http_build_query($data));
        }

        return "S0002://action=open_fullscreen&client_id=S0002&url=http://game.mobo.vn";
    }

}

?>
