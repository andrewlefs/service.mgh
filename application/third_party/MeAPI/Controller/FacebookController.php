<?php
if (empty($_SESSION)) {
    session_name('service_mobo_onepiece');
    session_start();
}
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . 'third_party/MeAPI/Autoloader.php';
require_once APPPATH . 'third_party/MeAPI/Mq.php';

class MeAPI_Controller_FacebookController implements MeAPI_Controller_FacebookInterface
{
    protected $_response;
    /**
     *
     * @var CI_Controller
     */
    private $CI;
    private $key_fb_config = "facebook_config_onepiece";
    private $event_date_start;
    private $event_date_end;

    private $service_id = 139;
    private $codelists = array(
        100100 => "Share success",
        101 => "Token không hợp lệ",
        102 => "Sự kiện không được bỏ trống",
        103 => "Access token facebook không được bỏ trống",
        104 => "Sự kiện không tồn tại",
        105 => "Thông tin nhân vật không được bỏ trống",
        106 => "Bạn đã hoàn thành nhiệm vụ trước đó",
        107 => "Ghi nhận thông tin chưa thành công",
        108 => "Cập nhật dữ liệu chưa thành công",
        109 => "Error validating access token: The user has not authorized application 533414373498024."
    );

    public function __construct()
    {
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $this->CI = &get_instance();
        MeAPI_Autoloader::register();
        $this->CI->facebook_config = MeAPI_Config_Game::facebook();
        $this->CI->cache_config = MeAPI_Config_Game::cache();
        $this->CI->load->library('facebook');
        $userdata = (!empty($_SESSION["game_info"]) ? $_SESSION["game_info"] : json_decode($_GET["info"], true));
        //$accesstoken = json_decode(base64_decode((!empty($_SESSION["info"]["access_token"]) ? $_SESSION["info"]['access_token'] : $_GET['access_token'])), true);
        $server_id = $userdata["server_id"];
        //$mobo_service_id = $accesstoken['mobo_service_id'];
        $this->read_fb_config($server_id);

        $this->CI->load->library('GameFullAPI', FALSE, 'GameFullAPI');
        header('X-Frame-Options: Allow-From https://m.facebook.com/dialog/feed/', true);
        $this->CI->root_url = "http://game.mobo.vn/onepiece/?control=facebook";
        //$this->CI->root_url = "http://local.game.mobo.vn/?control=facebook";
        $this->CI->cache_limit = 60 * 60 * 24 * 1; //thời gian cache
        $this->CI->enable_cache = true; //   
        //Date time config update point when invite
        $this->event_date_start = new DateTime("2015-06-30 00:00:00");
        $this->event_date_end = new DateTime("2015-07-04 23:59:00");

    }

    public function oauth_access_token($params)
    {
        if (isset($params["access_token"])) {
            $data = array(
                "access_token" => $params["access_token"],
                "redirect_uri" => $this->CI->facebook->getCurrentUrl(),
                "client_id" => $this->CI->facebook_config["client_id"],
                "client_secret" => $this->CI->facebook_config["client_secret"]
            );

            $res = $this->CI->facebook->makeRequest("https://graph.facebook.com/me", $data);
            $res = json_decode($res, true);
            if ($res["success"] == true) {
                return $this->return_code(100100);
            } else {
                return $this->return_code(109);
            }
        }
        return false;
    }

    function return_code($code)
    {
        return json_encode(array("code" => $code, "message" => $this->codelists[$code]));
    }
    public function clearcache($mobo_service_id,$server_id){
        $host = $this->CI->cache_config["systeminfo"]["host"];
        $port = $this->CI->cache_config["systeminfo"]["port"];
        $key = md5($this->key_fb_config . $mobo_service_id . $server_id);
        $memcache = new Memcache;
        $status_memcache = @$memcache->connect($host, $port);
        if ($status_memcache == true) {
            $memcache->set($key, null, false, 0);
        }
    }
    public function read_fb_gamer($mobo_service_id, $server_id)
    {
        $memcache = new Memcache;
        //$_SESSION["redirect"] = "http://game.mobo.vn/onepiece/" . $_SERVER["REQUEST_URI"];
        $_SESSION["redirect"] = !empty($_SESSION["redirect"])?$_SESSION["redirect"]:$this->CI->facebook_config['redirect_callback'] . $_SERVER["REQUEST_URI"];

        $host = $this->CI->cache_config["systeminfo"]["host"];
        $port = $this->CI->cache_config["systeminfo"]["port"];
        $key = md5($this->key_fb_config . $mobo_service_id . $server_id);
        $status_memcache = @$memcache->connect($host, $port);
        $status_memcache =false;
        if ($status_memcache == true) {
            $config = $memcache->get($key);
        }
        if (empty($config)) {
            $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
            $config = $this->CI->FacebookModel->get_accesstoken($mobo_service_id, $server_id);
            $_SESSION['configuser'] = $config;
            //var_dump($config);die;
            if ($config == true) {
                $this->checkStatus($config);
                //$memcache->set($key, $config, false, 24 * 3600);
                return $config;
            } else {
                $memcache->close();
                //da sang fb_login
                $this->getLoginStatus();
                return;
            }
        } else {
            $this->checkStatus($config);
            return $config;
        }
        //$this->CI->facebook->setAppId($access_token);
        $memcache->close();
    }

    public function checkStatus($config)
    {
        $response = $this->oauth_access_token($config);
        $response = json_decode($response, true);
        if ($response['code'] == 100100) {
            return $response;
        } else {
            $this->getLoginStatus();
            return;
        }
    }

    protected function getClientIP()
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?: ($_SERVER['HTTP_X_FORWARDED_FOR'] ?: $_SERVER['HTTP_CLIENT_IP']);
        return $ip;
    }

    public function getResponse()
    {
        return $this->_response;
    }

    public function render($view, $data = null)
    {

        $this->CI->data = $data;
        echo $this->CI->load->view('MeAPI/Facebook/' . $view, null, true);
        die;
    }

    public function login()
    {
        echo $_SESSION["redirect"];
    }

    //////////////Security Function Start
    private function unique_id($id)
    {
        $memcache = new Memcache;
        $host = $this->CI->cache_config["systeminfo"]["host"];
        $port = $this->CI->cache_config["systeminfo"]["port"];
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

    private function saveMemcache($key, $value, $cachetime = 3600)
    {
        $memcache = new Memcache;
        $host = $this->CI->cache_config["systeminfo"]["host"];
        $port = $this->CI->cache_config["systeminfo"]["port"];
        $status = @$memcache->connect($host, $port);
        if ($status == true) {
            $mkey = md5($key);
            $memcache->set($mkey, $value, false, $cachetime);
            $memcache->close();
            return true;
        }
        return false;
    }

    private function getMemcache($key)
    {
        $memcache = new Memcache;
        $host = $this->CI->cache_config["systeminfo"]["host"];
        $port = $this->CI->cache_config["systeminfo"]["port"];
        $status = @$memcache->connect($host, $port);
        if ($status == true) {
            $mkey = md5($key);
            $value = $memcache->get($mkey);
            $memcache->close();
            return $value;
        }
        return null;
    }

    public function get_config()
    {
        $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
        $config = $this->CI->FacebookModel->get_config();
        $this->render("view_config", $config);
    }

    public function set_config(MeAPI_RequestInterface $request)
    {
        $id = $_GET["id"];
        if ($_SESSION["game_info"]["mobo_id"] != "128147013") {
            $this->_response = new MeAPI_Response_APIResponse($request, 'REQUEST_FAIL', "Bạn không có quyền thực hiện tính năng này.");
            return;
        }

        $datadecode = base64_decode($_SESSION["oauthtoken"]);
        $dataarray = json_decode($datadecode, true);
        $userdata = json_decode($dataarray["info"], true);
        $server_id = $userdata["server_id"];

        $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
        $config = $this->CI->FacebookModel->set_config($id);
        if ($config == true) {
            $memcache = new Memcache;
            $host = $this->CI->cache_config["systeminfo"]["host"];
            $port = $this->CI->cache_config["systeminfo"]["port"];
            $key = md5($this->key_fb_config . $server_id);
            $status_memcache = @$memcache->connect($host, $port);
            if ($status_memcache == true) {
                $memcache->set($key, null, false, 1);
                $memcache->close();
            }
            $this->_response = new MeAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', "Cập nhật thành công.");
            return;
        }
        $this->_response = new MeAPI_Response_APIResponse($request, 'REQUEST_FAIL', "Bạn không có quyền thực hiện tính năng này.");
        return;
    }

    function change_app_id($server_id)
    {
        $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
        $config = $this->CI->FacebookModel->get_config_none($server_id);
        if ($config == false) {
            return false;
        } else {
            $id = $config["id"];
            $config = $this->CI->FacebookModel->set_config($id);
            if ($config > 0) {
                return true;
            }
            return false;
        }
    }

    private function read_fb_config($server_id)
    {
        $memcache = new Memcache;
        $host = $this->CI->cache_config["systeminfo"]["host"];
        $port = $this->CI->cache_config["systeminfo"]["port"];
        $key = md5($this->key_fb_config . $server_id);

        $status_memcache = @$memcache->connect($host, $port);
        $status_memcache = false;
        if ($status_memcache == true) {
            $config = $memcache->get($key);
            //$memcache->close();
        }
        //var_dump($config);die;
        if (empty($config)) {
            $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
            $config = $this->CI->FacebookModel->get_config_by_id($server_id);
            //var_dump($config);die;
            if ($config == true) {
                $this->CI->facebook_config["client_id"] = $config["client_id"];
                $this->CI->facebook_config["client_secret"] = $config["client_secret"];
                $memcache->set($key, $config, false, 24 * 3600);
            } else {
                $this->CI->facebook_config = MeAPI_Config_Game::facebook();
                $memcache->set($key, $this->CI->facebook_config, false, 24 * 3600);
            }
        } else {
            $this->CI->facebook_config["client_id"] = $config["client_id"];
            $this->CI->facebook_config["client_secret"] = $config["client_secret"];
        }
        $this->CI->facebook->setAppId($this->CI->facebook_config["client_id"]);
        $this->CI->facebook->setAppSecret($this->CI->facebook_config["client_secret"]);
        $memcache->close();
    }

    private function storeSession($character_id, $server_id)
    {
        if (session_id() == '') {
            session_start();
        }
        // Write products to Cache in 10 minutes with same keyword
        $memcache = new Memcache;
        $host = $this->CI->cache_config["systeminfo"]["host"];
        $port = $this->CI->cache_config["systeminfo"]["port"];
        $status = @$memcache->connect($host, $port);
        if ($status == true) {
            $key = md5("store_{$character_id}_{$server_id}");
            echo ("store_{$character_id}_{$server_id}");
            echo "<Br/>";
            echo $key;
            $memcache->set($key, session_id(), false, 3600);
            $memcache->close();
        }
    }

    private function getSession($character_id, $server_id)
    {
        $memcache = new Memcache;
        $host = $this->CI->cache_config["systeminfo"]["host"];
        $port = $this->CI->cache_config["systeminfo"]["port"];
        $key = md5("store_{$character_id}_{$server_id}");
        $status_memcache = @$memcache->connect($host, $port);
        if ($status_memcache == true) {
            echo $key;
            echo "<br/>";
            echo ("store_{$character_id}_{$server_id}");
            echo "<Br/>";
            $session_id = $memcache->get($key);
            var_dump($session_id);
            $memcache->close();
            if (empty($session_id)) {
                return false;
            } else if (!empty($session_id)) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    //////////////////////Security Function End
    public function getLoginStatus()
    {
        $url_login = $this->CI->facebook->getLoginUrl(array(
            "scope" => $this->CI->facebook_config['scope'],
        ), $this->CI->facebook_config['redirect_uri']);
        header("location: {$url_login}");
        die;
    }

    public function home()
    {
        $params = $this->CI->input->get();
        //create oauth token
        //Check Fast Process
        if (isset($_SESSION["last_time"]) && (time() - $_SESSION["last_time"]) < 2) {
            $waiting = 2 - (time() - $_SESSION["last_time"]);
            echo <<<EOF
            <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                    <title>Chia sẽ Facebook Đảo Hải Tặc</title>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                </head>
            <body>
                <p style="
                        color: #3b5998;
                        width: 100%;
                        text-align: center;
                        line-height: 100%;
                        vertical-align: middle;
                    ">Bạn truy cập quá nhanh. Vui lòng đợi sau vài giây</p>
            </body>
            </html>            
EOF;
            die;
        }
        $_SESSION["last_time"] = time();
        $_SESSION['datatime'] = time();
        $token = trim($params['token']);
        unset($params['control']);
        unset($params['func']);
        unset($params['token']);
        $valid = md5(implode('', $params) . $this->CI->facebook_config["oauth_key"]);
        $gameInfo = json_decode($params["info"], true);
        $accesstoken = json_decode(base64_decode($params["access_token"]), true);
        $gameInfo["mobo_id"] = $accesstoken["mobo_id"];
        $gameInfo["mobo_service_id"] = $accesstoken["mobo_service_id"];
        $_SESSION["game_info"] = $gameInfo;
        $_SESSION["info"] = $params;
        $this->read_fb_config($gameInfo["server_id"]);
        $_SESSION["oauthtoken"] = base64_encode(json_encode($params));
        //$_SESSION["redirect"] = "http://game.mobo.vn/onepiece/" . $_SERVER["REQUEST_URI"];
        $_SESSION["redirect"] = $this->CI->facebook_config['redirect_callback'] . $_SERVER["REQUEST_URI"];
        //echo $_SESSION["redirect"]; die;
        if ($valid != $token) {
            unset($_SESSION["oauthtoken"]);
            $this->render("deny");
        }
        $datadecode = base64_decode($_SESSION["oauthtoken"]);
        $dataarray = json_decode($datadecode, true);
        $userdata = json_decode($dataarray["info"], true);

        $character_id = $userdata["character_id"];
        $server_id = $userdata["server_id"];
        $character_name = $userdata["character_name"];

        if (empty($character_id) || empty($server_id) || empty($character_name)) {
            echo <<<EOF
            <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                    <title>Chia sẽ Facebook Đảo Hải Tặc</title>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                </head>
            <body>
                <p style="
                        color: #3b5998;
                        width: 100%;
                        text-align: center;
                        line-height: 100%;
                        vertical-align: middle;
                    ">Vui lòng vào Game trước khi tham gia sự kiện</p>
            </body>
            </html>            
EOF;
            die;
        }
        $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');


        $config = $this->CI->FacebookModel->get_config_by_id($server_id);

        //var_dump($config);die;
        if ($config == false) {
            echo <<<EOF
            <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                    <title>Chia sẽ Facebook Đảo Hải Tặc</title>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                </head>
            <body>
                <p style="
                        color: #3b5998;
                        width: 100%;
                        text-align: center;
                        line-height: 100%;
                        vertical-align: middle;
                    ">Sự kiện mời bạn đang bảo trì, bạn vui lòng quay lại sau!</p>
            </body>
            </html>            
EOF;
            die;
        }

        $this->storeSession($gameInfo["mobo_service_id"], $gameInfo["server_id"]);
        var_dump($gameInfo);

        echo session_id();

        $this->CI->paramsfb = $this->read_fb_gamer($gameInfo["mobo_service_id"], $server_id);
        //gen buy unique id
        $_SESSION["buy_token"] = md5(time());
        if(isset($_SESSION['invite_info']) && !empty($_SESSION['invite_info'])){
            $this->CI->infoinvite = $_SESSION['invite_info'];
            unset($_SESSION['invite_info']);
        }


        //User Point Invite
        //$this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
        //$user_point_invite = $this->CI->FacebookModel->get_user_point_invite($gameInfo["character_id"], $gameInfo["server_id"], $gameInfo["mobo_service_id"]);
        //$_SESSION["user_point_invite"] = $user_point_invite[0]["user_point"];
        //echo var_dump($user_point_invite[0]["user_point"]);die;
        $data['checkLike'] = $this->checkLike($gameInfo);
        $this->CI->post_message = $this->get_message();
        $this->CI->token = $this->signtoken($gameInfo["mobo_service_id"], $gameInfo["server_id"], $this->CI->post_message["id"]);
        $this->render("home", $data);
    }

    function signtoken($mobo_service_id, $server_id, $id)
    {
        $_SESSION['datatime'] = time();
        $arraysign = array("mobo_service_id" => $mobo_service_id, "server_id" => $server_id, "post_id" => $id, 'time' => date('Y-m-d', time()));
        $token = md5(implode('', $arraysign) . '#$^@#$Ds#7');
        return $token;
    }

    public function ajax_login_info(MeAPI_RequestInterface $request)
    {
        $data = $_GET;
        $oauthtoken = $data["oauthtoken"];
        if ($oauthtoken != $_SESSION["oauthtoken"]) {
            $this->_response = new MeAPI_Response_APIResponse($request, 'OAUTH_TOKEN_INVALI', null);
            return;
        } else {
            $needle = array("fid", "fb_accesstoken");
            if (!is_required($data, $needle)) {
                $this->_response = new MeAPI_Response_APIResponse($request, 'INVALID_PARAMS', $data);
                return;
            }
            //login Model Facebook log data login
            $fid = $data["fid"];
            $accesstoken = $data["fb_accesstoken"];
            $client_ip = $this->getClientIP();

            $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
            $this->saveMemcache($fid, $accesstoken, 24 * 3600);
            $is_success = $this->CI->FacebookModel->insert_access_token($fid, $accesstoken, $client_ip);
            if ($is_success != 0) {
                $this->_response = new MeAPI_Response_APIResponse($request, 'LOG_DATA_LOGIN_SUCCESS', null);
                return;
            } else {
                $this->_response = new MeAPI_Response_APIResponse($request, 'LOG_DATA_FAIL', null);
                return;
            }
        }
    }

    public function get_message()
    {
        $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
        return $this->CI->FacebookModel->get_message();
    }

    //Funtion Complete Share 5 times & Invite 100

    public function check_invite(MeAPI_RequestInterface $request)
    {
        $data = $_GET;
        $oauthtoken = $data["oauthtoken"];
        if ($oauthtoken != $_SESSION["oauthtoken"]) {
            $this->_response = new MeAPI_Response_APIResponse($request, 'OAUTH_TOKEN_INVALI', null);
            return;
        } else {
            $dataarray = base64_decode($data["oauthtoken"]);
            $dataarray = json_decode($dataarray, true);
            $accesstoken = json_decode(base64_decode($dataarray["access_token"]), true);
            $userdata = json_decode($dataarray["info"], true);
            $character_id = $userdata["character_id"];
            $server_id = $userdata["server_id"];
            //check thời gian invite
            $key_cache_time = $character_id . $server_id;
            $invite_time = $this->getMemcache($key_cache_time);
            if ($invite_time == true) {
                $current = time();
                $time = $current - $invite_time["time"];
                $time_count = $invite_time["downtime"];
                if ($time < $time_count) {
                    $itime = $time_count - $time;
                    $this->_response = new MeAPI_Response_APIResponse($request, 'WAITING', $itime);
                    return;
                } else {
                    $this->_response = new MeAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', null);
                    return;
                }
            } else {
                $this->_response = new MeAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', null);
                return;
            }
        }
    }

    public function parseparams(){
        $params = $this->CI->input->get();
        unset($params['control'],$params['func']);
        echo json_encode(array("code"=>0,'data'=>base64_encode(json_encode($params))));
        die;
    }
    public function invite(MeAPI_RequestInterface $request)
    {
        $debug = true;
        if ($debug && isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 2) {
            $waiting = 3 - (time() - $_SESSION["last_time"]);
            $this->_response = new MeAPI_Response_APIResponse($request, 'SESSION_EXPIRE', "Bạn truy cập quá nhanh. Vui lòng thử lại sau vài giây");
            die;
        }
        $_SESSION["execute_time"] = time();
        $data = $_GET;
        $oauthtoken = $data["oauthtoken"];
        if ($oauthtoken != $_SESSION["oauthtoken"]) {
            $this->_response = new MeAPI_Response_APIResponse($request, 'OAUTH_TOKEN_INVALI', null);
            return;
        } else {
            var_dump($_SESSION);
            $datadecode = base64_decode($data["oauthtoken"]);
            $dataarray = json_decode($datadecode, true);
            $access_token = json_decode(base64_decode($dataarray["access_token"]), true);

            $userdata = json_decode($dataarray["info"], true);
            $character_id = $userdata["character_id"];
            $server_id = $userdata["server_id"];
            $character_name = $userdata["character_name"];
            $mobo_service_id = $access_token["mobo_service_id"];

            $needle = array("fromfid", "tofid", "requestid");
            if (!is_required($data, $needle)) {
                $this->_response = new MeAPI_Response_APIResponse($request, 'INVALID_PARAMS', $data);
                //echo json_encode(array('code'=>-4,'desc'=>'INVALID_PARAMS',"message"=>"INVALID_PARAMS",'data'=>$data));
                return;
            }

            $toInvite = json_decode($data["tofid"], true);
            $requestid = $data["requestid"];
            $exclude_ids = $data["exclude_ids"];
            $to_invitable_friends = $data["exclude_tokens"];

            if ($requestid == "undefined") {
                $block = $this->getMemcache($this->key_fb_config . $server_id . "block_app_id");
                if (empty($block)) {
                    $block = 0;
                }
                // var_dump($block);
                if ($block >= 10) {
                    //change app id                    
                    $value = $this->change_app_id($server_id);
                    if ($value == true) {
                        $this->saveMemcache($this->key_fb_config . $server_id, null);
                        $block = -1;
                    }
                }
                $this->saveMemcache($this->key_fb_config . $server_id . "block_app_id", $block + 1);
                $this->_response = new MeAPI_Response_APIResponse($request, 'BLOCK_APP', "Cập nhật chưa thành công vui lòng thử lại." . $block);
                return;
            } else {
                $this->saveMemcache($this->key_fb_config . $server_id . "block_app_id", 0);
            }
            //die;
            /*if ($this->unique_id($requestid)) {
                $this->_response = new MeAPI_Response_APIResponse($request, 'DUPLICATE_TRAN', "Yêu cầu đang được xử lý hoặc đã được xử lý trước đó.");
                return;
            }*/

            $fid = $data["fromfid"];
            $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
            $accesstoken = $this->CI->FacebookModel->query_access_token($fid);
            //$accesstoken = $this->getMemcache($fid);

            if (empty($accesstoken)) {
                $this->_response = new MeAPI_Response_APIResponse($request, 'INVITE_FAIL_1', null);
                return;
            } else {
                $toInvite = json_decode($data["tofid"], true);
                $requestid = $data["requestid"];


                //check thời gian invite
                $key_cache_time = $character_id . $server_id;
                $invite_time = $this->getMemcache($key_cache_time);
                if ($invite_time == true) {
                    $current = time();
                    $time = $current - $invite_time["time"];
                    $time_count = $invite_time["downtime"];
                    if ($time < $time_count) {
                        $itime = $time_count - $time;
                        $hour = floor($itime / 3600);
                        $minute = floor(($itime - ($hour * 3600)) / 60);
                        $second = $itime - (($hour * 3600) + ($minute * 60));
                        $response = "Mời thành công, không nhận được thể lực vui lòng đợi {$hour}:{$minute}:{$second} giây hãy mời tiếp.";
                        $this->_response = new MeAPI_Response_APIResponse($request, 'WAITING', $response);
                        return;
                    }
                }

                $invite = array();

                $count_invite = 0;
                echo $mobo_service_id;
                echo $server_id;
var_dump($this->getSession($mobo_service_id, $server_id));
                echo session_id();
                if (!$this->getSession($mobo_service_id, $server_id)) {
                    $this->_response = new MeAPI_Response_APIResponse($request, 'SESSION_EXPIRE', "Bạn đang đăng nhập trên một máy khác.");
                    return;
                }


                if (!empty($exclude_ids) && $exclude_ids != "[]") {
                    $excluded_data = array("fid" => $fid, "to_invitable_friends" => $exclude_ids, "type" => 1, "create_date" => date("Y-m-d H:i:s", time()));
                    $exclude_ids = $this->CI->FacebookModel->insert_excluded($excluded_data);
                }
                if (!empty($to_invitable_friends) && $to_invitable_friends != "[]") {
                    $excluded_data = array("fid" => $fid, "to_invitable_friends" => $to_invitable_friends, "type" => 0, "create_date" => date("Y-m-d H:i:s", time()));
                    $excluded = $this->CI->FacebookModel->insert_excluded($excluded_data);
                }
                if ($excluded == 0 && $exclude_ids == 0) {
                    $this->_response = new MeAPI_Response_APIResponse($request, 'INVITE_FAIL_2', null);
                    return;
                }

                $requestdata = $this->_getRequestInvite($requestid, $accesstoken);
                //var_dump($requestdata);die;
                if (!empty($requestdata)) {
                    foreach ($toInvite as $key => $value) {
                        //check invite
                        //
                        //$is_invite = $this->CI->FacebookModel->check_invite($fid, $value);
                        //if ($is_invite["code"] == 0) {
                        $nameTo = ""; //$this->_getNameFace($value, $accesstoken);

                        $datainvite = array(
                            "character_id" => $character_id,
                            "character_name" => $userdata["character_name"],
                            "server_id" => $userdata["server_id"],
                            "from_fid" => $fid,
                            "from_fname" => '',
                            "invite_date" => date("y-m-d H:i:s", time()),
                            "to_fid" => $value,
                            "to_fname" => $nameTo,
                            "request_id" => $requestdata,
                            "mobo_service_id" => $mobo_service_id
                        );
                        //var_dump($datainvite);die;
                        $is_insert = $this->CI->FacebookModel->insert_invite($datainvite);

                        if ($is_insert > 0) {
                            $count_invite++;
                            $invite[$value] = array("code" => 1, "name" => $nameTo, "is_aready" => 1);
                        } else {
                            $invite[$value] = array("code" => 1, "name" => $nameTo, "is_aready" => -1);
                        }

                        //} else {
                        //    $invite[$value] = array("code" => 1, "name" => $is_invite["name"], "is_aready" => 0);
                        //}
                    }
                    //send award
                } else {
                    $invite[$value] = array("code" => 1, "name" => "Not AuthorizationFF", "is_aready" => -2);
                }
                //var_dump($invite);
                // die ;
                $power = $this->CI->FacebookModel->check_exist_data($character_id, $server_id, array("power"));
                if ($power == true) {
                    unset($_SESSION["REQ_INVITE_ID"]);
                    $this->_response = new MeAPI_Response_APIResponse($request, 'EXIST_DATA');
                    return;
                }

                //var_dump($count_invite);die;
                $total_accept = $this->CI->FacebookModel->check_count_accept_in_day($server_id, $mobo_service_id);

                $invite_dec = $this->CI->facebook_config["invite_dec"];
                $invite_limit = $this->CI->facebook_config["invite_limit"];
                if ($count_invite > 30) {
                    $this->_response = new MeAPI_Response_APIResponse($request, 'INVALID_INVITE', "Bạn đã mời vượt quá giới hạn cho phép (" . $count_invite . " bạn)");
                    return;
                }
                if ($total_accept >= $invite_limit || $count_invite == 0) {
                    $this->_response = new MeAPI_Response_APIResponse($request, 'INVITE_SUCCESS', "Mời thành công " . $total_accept . " bạn nhưng bạn đã nhận đủ quà trong ngày.");
                    unset($_SESSION["REQ_INVITE_ID"]);
                    return;
                } else {
                    $times = ($invite_limit - $total_accept);

                    if ($times > ($count_invite * $invite_dec))
                        $times = ($count_invite * $invite_dec);

                    $info = array("character_id" => $character_id,
                        "character_name" => $character_name,
                        "server_id" => $server_id,
                        "mobo_service_id" => $mobo_service_id);;

                    $issend = $this->send_item($info, "invite", $times);

                    if ($issend == true) {
                        $timing = array("time" => time(), "downtime" => $count_invite * 360);
                        $this->saveMemcache($key_cache_time, $timing, 24 * 3600);

                        //$result_check_crystal = $this->CI->FacebookModel->get_user_point_invite_crystal($character_id, $server_id, $mobo_service_id);
                        //if($mobo_service_id == "1031504917236748154" && $this->day_now >= $this->event_date_start_crystal && $this->day_now <= $this->event_date_end_crystal){
                        $this->_response = new MeAPI_Response_APIResponse($request, 'INVITE_SUCCESS', "Mời thành công " . $total_accept . " bạn," . $title . " thành công");
                        unset($_SESSION["REQ_INVITE_ID"]);
                        return;
                    } else {
                        //$this->CI->FacebookModel->rollback_invite($id);
                        $this->_response = new MeAPI_Response_APIResponse($request, 'INVITE_SUCCESS', "Mời thành công " . $total_accept . " bạn, chưa đủ mốc để nhận quà,hãy tiếp tục...!");
                        unset($_SESSION["REQ_INVITE_ID"]);
                        return;
                    }
                }
                $timing = array("time" => time(), "downtime" => $count_invite * 360);
                $this->saveMemcache($key_cache_time, $timing, 24 * 3600);
                $this->_response = new MeAPI_Response_APIResponse($request, 'INVITE_SUCCESS', $invite);
                unset($_SESSION["REQ_INVITE_ID"]);
                return;
            }
        }
    }

    public function check_login(MeAPI_RequestInterface $request)
    {
        $game_info = $_SESSION["game_info"];
        if (!$this->getSession($game_info["mobo_service_id"], $game_info["server_id"])) {
            $this->_response = new MeAPI_Response_APIResponse($request, 'SESSION_EXPIRE', "Bạn đang đăng nhập trên một máy khác.");
            return;
        } else {
            $this->_response = new MeAPI_Response_APIResponse($request, 'REQUEST_SUCCESS');
            return;
        }
    }

    public function friends(){
        $friends = array('data'=>'');
        if(!empty($_SESSION['configuser'])) {
            $friends = $this->CI->facebook->api('/me/friends?pretty=0&limit=5000',array('access_token'=>$_SESSION['configuser']['access_token']));
        }
        echo json_encode(array('error'=>0,'data'=>$friends['data']));
        die;
    }
    public function invitable_friends(){
        $params = $this->CI->input->get();
        $friends = array('data'=>'');
        if(isset($params['excluded_ids']) && !empty($_SESSION['configuser'])) {
            $friends = $this->CI->facebook->api('/me/invitable_friends?pretty=0&limit=5000&excluded_ids=['.$params['excluded_ids']."]",array('access_token'=>$_SESSION['configuser']['access_token']));
        }
        echo json_encode(array('error'=>0,'data'=>$friends['data']));
        die;
    }

    public function accept_invite(MeAPI_RequestInterface $request)
    {
        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 2) {
            $waiting = 3 - (time() - $_SESSION["execute_time"]);
            $this->_response = new MeAPI_Response_APIResponse($request, 'SESSION_EXPIRE', "Bạn truy cập quá nhanh. Vui lòng thử lại sau vài giây");
            return;
        }
        $_SESSION["execute_time"] = time();

        $data = $_GET;
        $oauthtoken = $data["oauthtoken"];
        if ($oauthtoken != $_SESSION["oauthtoken"]) {
            $this->_response = new MeAPI_Response_APIResponse($request, 'OAUTH_TOKEN_INVALI', null);
            return;
        } else {
            $needle = array("id", "fid", "accesstoken", "token");
            if (!is_required($data, $needle)) {
                $this->_response = new MeAPI_Response_APIResponse($request, 'INVALID_PARAMS', $data);
                return;
            }
            $fid = $data["fid"];
            $id = $data["id"];
            $fromid = $data["fromid"];
            $token = $data["token"];

            if ($this->unique_id($token)) {
                $this->_response = new MeAPI_Response_APIResponse($request, 'DUPLICATE_TRAN', "Yêu cầu đang được xử lý hoặc đã được xử lý trước đó.");
                return;
            }
            $game_info = $_SESSION["game_info"];
            if (!$this->getSession($game_info["mobo_service_id"], $game_info["server_id"])) {
                $this->_response = new MeAPI_Response_APIResponse($request, 'SESSION_EXPIRE', "Bạn đang đăng nhập trên một máy khác.");
                return;
            }

            $datadecode = base64_decode($data["oauthtoken"]);
            $dataarray = json_decode($datadecode, true);
            $accesstoken = json_decode(base64_decode($dataarray["access_token"]), true);
            $userdata = json_decode($dataarray["info"], true);
            $accept_character_id = $userdata["character_id"];
            $accept_server_id = $userdata["server_id"];
            $accept_character_name = $userdata["character_name"];
            $accept_mobo_service_id = $accesstoken["mobo_service_id"];

            $reqaccesstoken = $data["accesstoken"];
            $verify = md5($id . $fromid . "hi%d(kT");
            $newotp = md5($id . $fromid . time() . "hi%d(kT");
            if ($verify != $token) {
                $this->_response = new MeAPI_Response_APIResponse($request, 'TRANS_NOTVALIDATE', array("otp" => $newotp, "message" => "Giao dịch không hợp lệ"));
                return;
            }
            //            if ($_SESSION["accept_token"] == $token || isset($_SESSION["accept_token"])) {
            //                $this->_response = new MeAPI_Response_APIResponse($request, 'TRANS_NOTVALIDATE', array("otp" => $newotp, "message" => "Giao dịch không hợp lệ"));
            //                return;
            //            }
            //            $_SESSION["accept_token"] = $token;

            $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
            $power = $this->CI->FacebookModel->check_exist_data($accept_character_id, $accept_server_id, array("power"));
            if ($power == true) {
                $this->_response = new MeAPI_Response_APIResponse($request, 'EXIST_DATA');
                unset($_SESSION["accept_token"]);
                return;
            }
            $accesstoken = $this->getMemcache($fid);
            if (empty($accesstoken)) {
                $accesstoken = $this->CI->FacebookModel->query_access_token($fid);
            }

            //var_dump($accesstoken);die;
            if ($accesstoken == $reqaccesstoken && !empty($reqaccesstoken)) {
                $update_invite = $this->CI->FacebookModel->update_invite($id, $fid, $fromid, $accept_server_id, $accept_character_id);
                //var_dump($id."-".$fid."-".$fromid."-".$accept_server_id."-".$accept_character_id);die;
                if ($update_invite > 0) {

                    //check rule send qua invite

                    $info = $this->CI->FacebookModel->get_list_invite_id($id);

                    if ($info == true) {
                        $server_id = $info["server_id"];
                        $character_id = $info["character_id"];
                        $mobo_service_id = $info["mobo_service_id"];
                        $character_name = $info["character_name"];

                        //Update User Invite Point                        
                        $datauser = $this->CI->FacebookModel->user_check_point_invite_exist($character_id, $server_id, $mobo_service_id);
                        if (count($datauser) > 0) {
                            //Update User Invite Point    
                            $this->CI->FacebookModel->add_point_invite($character_id, $server_id, $mobo_service_id, 10);
                        } else {
                            //Insert User Point
                            $userdata_p["char_id"] = $character_id;
                            $userdata_p["server_id"] = $server_id;
                            $userdata_p["char_name"] = $character_name;
                            $userdata_p["mobo_service_id"] = $mobo_service_id;
                            $userdata_p["point_date"] = Date('Y-m-d H:i:s');

                            $userdata_p["user_point"] = 10;

                            $this->CI->FacebookModel->insert("facebook_invites_point", $userdata_p);

                            //$this->data["user_point"] = 0;
                        }

                        //Update User Accept Point
                        $datauser = $this->CI->FacebookModel->user_check_point_invite_exist($accept_character_id, $accept_server_id, $accept_mobo_service_id);
                        if (count($datauser) > 0) {
                            //Update User Invite Point    
                            $this->CI->FacebookModel->add_point_invite($accept_character_id, $accept_server_id, $accept_mobo_service_id, 5);
                        } else {
                            //Insert User Point
                            $userdata_p["char_id"] = $accept_character_id;
                            $userdata_p["server_id"] = $accept_server_id;
                            $userdata_p["char_name"] = $accept_character_name;
                            $userdata_p["mobo_service_id"] = $accept_mobo_service_id;
                            $userdata_p["point_date"] = Date('Y-m-d H:i:s');

                            $userdata_p["user_point"] = 5;

                            $this->CI->FacebookModel->insert("facebook_invites_point", $userdata_p);

                            //$this->data["user_point"] = 0;
                        }

                        $accept_limit = $this->CI->FacebookModel->check_accept_limit_in_day($accept_server_id, $accept_character_id);
                        $quota_accept_limit = $this->CI->facebook_config["accept_limit"];

                        if ($accept_limit <= $quota_accept_limit) {
                            $accept_data = $info;
                            $accept_data["server_id"] = $accept_server_id;
                            $accept_data["character_id"] = $accept_character_id;
                            $accept_data["mobo_service_id"] = $mobo_service_id;

                            $issend = $this->send_item($accept_data, "accept", $accept_limit);
                        }
                        $this->_response = new MeAPI_Response_APIResponse($request, 'ACCEPT_INVITE_SUCCESS');
                        unset($_SESSION["accept_token"]);
                        return;
                    } else {
                        $this->_response = new MeAPI_Response_APIResponse($request, 'ACCEPT_INVITE_SUCCESS');
                        unset($_SESSION["accept_token"]);
                        return;
                    }
                } else {
                    $this->_response = new MeAPI_Response_APIResponse($request, 'ACCEPT_INVITE_FAIL', array("otp" => $newotp, "message" => "Giao dịch lỗi"));
                    unset($_SESSION["accept_token"]);
                    return;
                }
            } else {
                $this->_response = new MeAPI_Response_APIResponse($request, 'ACCEPT_INVITE_FAIL', array("otp" => $newotp, "message" => "Giao dịch lỗi"));
                unset($_SESSION["accept_token"]);
                return;
            }
        }
    }

    public function share(MeAPI_RequestInterface $request)
    {
        //echo "xxxxxxxxxxxx"; die;
        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 2) {
            $waiting = 3 - (time() - $_SESSION["execute_time"]);
            $this->_response = new MeAPI_Response_APIResponse($request, 'SESSION_EXPIRE', "Bạn truy cập quá nhanh. Vui lòng thử lại sau vài giây");
            return;
        }
        $_SESSION["execute_time"] = time();
        $data = $_GET;
        $oauthtoken = $data["oauthtoken"];
        //var_dump($oauthtoken);
        //var_dump($_SESSION["oauthtoken"]);
        //die;
        if (false && $oauthtoken != $_SESSION["oauthtoken"]) {
            $this->_response = new MeAPI_Response_APIResponse($request, 'OAUTH_TOKEN_INVALI', null);
            return;
        } else {
            $needle = array("fid", "shareid", "postid");
            if (!is_required($data, $needle)) {
                $this->_response = new MeAPI_Response_APIResponse($request, 'INVALID_PARAMS', $data);
                return;
            }

            $fid = $data["fid"];
            $shareid = $data["shareid"];
            $postid = $data["postid"];

            if ($this->unique_id($postid)) {
                $this->_response = new MeAPI_Response_APIResponse($request, 'DUPLICATE_TRAN', "Yêu cầu đang được xử lý hoặc đã được xử lý trước đó.");
                return;
            }

            $datadecode = base64_decode($data["oauthtoken"]);
            $dataarray = json_decode($datadecode, true);
            $accesstoken = json_decode(base64_decode($dataarray["access_token"]), true);

            $userdata = json_decode($dataarray["info"], true);
            $character_id = $userdata["character_id"];
            $character_name = $userdata["character_name"];
            $server_id = $userdata["server_id"];
            $mobo_service_id = $accesstoken["mobo_service_id"];
            //echo $mobo_service_id; die;
            //if (!$this->getSession($character_id, $server_id)) {
            //    $this->_response = new MeAPI_Response_APIResponse($request, 'SESSION_EXPIRE', "Bạn đang đăng nhập trên một máy khác.");
            //    return;
            //}         
            //echo var_dump($mobo_service_id); die;

            $flags = $this->check_time($server_id, $character_id);
            if ($flags > 0) {
                $this->_response = new MeAPI_Response_APIResponse($request, 'INVALID_TIME', $flags);
                return;
            } else {
                $needle = array("fid", "shareid", "postid");
                if (!is_required($data, $needle)) {
                    $this->_response = new MeAPI_Response_APIResponse($request, 'INVALID_PARAMS', $data);
                    return;
                }
                $fid = $data["fid"];
                $shareid = $data["shareid"];
                $postid = $data["postid"];

                $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
                $data_share = array(
                    "character_id" => $character_id,
                    "character_name" => $character_name,
                    "server_id" => $server_id,
                    "fid" => $fid,
                    "share_id" => $shareid,
                    "post_id" => $postid,
                    "is_award" => 0,
                    "mobo_service_id" => $mobo_service_id,
                    "create_date" => date("y-m-d H:i:s", time())
                );
                //var_dump($data_share);die;
                $share_id = $this->CI->FacebookModel->insert_share($data_share);

                if ($share_id > 0) {
                    $share_times = $this->check_share_rule($server_id, $mobo_service_id);
                    //neu thoa dieu kien thi send code cho user

                    if ($share_times <= $this->CI->facebook_config["share_limit"]) {
                        //update neu send qua thanh cong
                        $info = array("character_id" => $character_id,
                            "character_name" => $character_name,
                            "server_id" => $server_id,
                            "mobo_service_id" => $mobo_service_id,
                            "character_id" => $character_id);
                        //if( $share_times == $this->CI->facebook_config["share_limit"]){
                        //check share bonus
                        //$checkshare = $this->CI->FacebookModel->checkshare($server_id, $character_id,"share5");
                        //if($checkshare == FALSE){
                        //$is_send = $this->bonus_items($info, "share5", $share_times);

                        $is_send = $this->send_item($info, "share", $share_times + 1);
                        //}
                        //}

                        //var_dump($share_times);
                        //die;
                        if ($is_send) {
                            //$this->CI->FacebookModel->update_share($share_id, 1);
                            $this->_response = new MeAPI_Response_APIResponse($request, 'POST_OK', $this->CI->facebook_config["share_delay_minute"]);
                            return;
                        } else {
                            $this->_response = new MeAPI_Response_APIResponse($request, 'POST_FAIL');
                            return;
                        }
                    } else {
                        $this->_response = new MeAPI_Response_APIResponse($request, 'POST_OK', $this->CI->facebook_config["share_delay_minute"]);
                        return;
                    }
                } else {
                    $this->_response = new MeAPI_Response_APIResponse($request, 'POST_FAIL');
                    return;
                }
            }
        }
    }

    public function list_invite()
    {
        $data = $_GET;
        $oauthtoken = $data["oauthtoken"];
        if ($oauthtoken != $_SESSION["oauthtoken"]) {
            echo "<span class='deny'>Truy cập không hợp lệ</span>";
            return;
        } else {
            $needle = array("fid");
            if (!is_required($data, $needle)) {
                echo "<span class='deny'>Truy cập không hợp lệ</span>";
                return;
            }
            $fid = $data["fid"];

            $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
            $invaite_list = $this->CI->FacebookModel->get_list_invite($fid);

            $this->render("view_list_invite", $invaite_list);
        }
    }

    public function get_excluded(MeAPI_RequestInterface $request)
    {
        $data = $_GET;
        $oauthtoken = $data["oauthtoken"];
        if ($oauthtoken != $_SESSION["oauthtoken"]) {
            $this->_response = new MeAPI_Response_APIResponse($request, 'REQUEST_FAIL', array());
            return;
        } else {
            $needle = array("fid");
            if (!is_required($data, $needle)) {
                $this->_response = new MeAPI_Response_APIResponse($request, 'INVALID_PARAMS', array());
                return;
            }
            $fid = $data["fid"];

            $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
            $friend_list = $this->CI->FacebookModel->get_excluded($fid);
            $exclude_ids = "";
            $exclude_invitoken_ids = "";
            foreach ($friend_list as $key => $value) {
                if ($value["type"] == "0") {
                    if (!empty($value["to_invitable_friends"])) {
                        if (!empty($exclude_invitoken_ids))
                            $exclude_invitoken_ids .= ',';
                        $repl = str_replace("]", "", str_replace("[", "", $value["to_invitable_friends"]));
                        $exclude_invitoken_ids .= $repl;
                    }
                } else {
                    if (!empty($value["to_invitable_friends"])) {
                        $repl = str_replace("\"]", "", str_replace("[\"", "", $value["to_invitable_friends"]));
                        if (empty($exclude_ids)) {
                            $exclude_ids = explode('","', $repl);
                        } else {
                            $exclude_ids = array_merge($exclude_ids, explode('","', $repl));
                        }
                    }
                }
            }

            $this->_response = new MeAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', array("exclude_ids" => $exclude_ids, "exclude_invitoken_ids" => $exclude_invitoken_ids));
            return;
        }
    }

    public function get_friends(MeAPI_RequestInterface $request)
    {
        //var_dump($_SESSION);
        //die;
        $data = $_GET;
        $oauthtoken = $data["oauthtoken"];
        if ($oauthtoken != $_SESSION["oauthtoken"]) {
            echo "<span class='deny'>Truy cập không hợp lệ</span>";
            exit;
        } else {
            $datadecode = base64_decode($data["oauthtoken"]);
            $dataarray = json_decode($datadecode, true);
            $userdata = json_decode($dataarray["info"], true);
            $server_id = $userdata["server_id"];

            $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
            $config = $this->CI->FacebookModel->get_config_by_id($server_id);
            //var_dump($config);die;
            if ($config == false) {
                echo <<<EOF
            <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                    <title>Chia sẽ Facebook Đảo Hải Tặc</title>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                </head>
            <body>
                <p style="
                        color: #3b5998;
                        width: 100%;
                        text-align: center;
                        line-height: 100%;
                        vertical-align: middle;
                    ">Sự kiện mời bạn đang bảo trì, bạn vui lòng quay lại sau!</p>
            </body>
            </html>            
EOF;
                die;
            } else {
                $this->render("view_invite");
            }
        }
    }

    public function get_giftcode(MeAPI_RequestInterface $request)
    {
        //var_dump($_SESSION);
        //die;
        $data = $_GET;
        $oauthtoken = $data["oauthtoken"];
        if ($oauthtoken != $_SESSION["oauthtoken"]) {
            echo "<span class='deny'>Truy cập không hợp lệ</span>";
            exit;
        } else {

            $datadecode = base64_decode($data["oauthtoken"]);
            $dataarray = json_decode($datadecode, true);
            $userdata = json_decode($dataarray["info"], true);
            $character_id = $userdata["character_id"];

            $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
            $giftcode = $this->CI->FacebookModel->get_giftcode($character_id);

            $this->render("view_list_giftcode", $giftcode);
        }
    }

    public function top_point_invite(MeAPI_RequestInterface $request)
    {
        //var_dump($_SESSION);
        //die;
        $data = $_GET;
        $oauthtoken = $data["oauthtoken"];
        if ($oauthtoken != $_SESSION["oauthtoken"]) {
            echo "<span class='deny'>Truy cập không hợp lệ</span>";
            exit;
        } else {

            $datadecode = base64_decode($data["oauthtoken"]);
            $dataarray = json_decode($datadecode, true);
            $userdata = json_decode($dataarray["info"], true);
            $access_token = json_decode(base64_decode($dataarray["access_token"]), true);

            $character_id = $userdata["character_id"];
            $server_id = $userdata["server_id"];
            $mobo_service_id = $access_token["mobo_service_id"];

            $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
            //$user_point_invite = $this->CI->FacebookModel->user_check_point_invite_exist($character_id, $server_id, $mobo_service_id);
            //User Point Invite            
            $user_point_invite = $this->CI->FacebookModel->get_user_current_point($character_id, $server_id, $mobo_service_id);
            $_SESSION["user_point_invite"] = $user_point_invite[0]["user_point"];
            $_SESSION["user_rank"] = $user_point_invite[0]["rank"];

            $event_date_start = new DateTime("2015-06-30 00:00:00");
            $event_date_end = new DateTime("2015-07-04 23:59:00");
            $day_now = new DateTime(Date('Y-m-d H:i:s'));

            if ($day_now > $event_date_start && $day_now < $event_date_end) {
                $_SESSION["event_date_start"] = $event_date_start;
                $_SESSION["event_date_end"] = $event_date_end;
                $_SESSION["date_now"] = $day_now;
            }

            $point_invite = $this->CI->FacebookModel->user_list_point_invite();

            $this->render("view_list_invite_point", $point_invite);
        }
    }

    public function user_point_invite()
    {
        $data = $_GET;
        $oauthtoken = $data["oauthtoken"];
        if ($oauthtoken != $_SESSION["oauthtoken"]) {
            echo "<span class='deny'>Truy cập không hợp lệ</span>";
            exit;
        } else {
            $datadecode = base64_decode($data["oauthtoken"]);
            $dataarray = json_decode($datadecode, true);
            $userdata = json_decode($dataarray["info"], true);
            $access_token = json_decode(base64_decode($dataarray["access_token"]), true);

            $character_id = $userdata["character_id"];
            $server_id = $userdata["server_id"];
            $mobo_service_id = $access_token["mobo_service_id"];

            $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
            $user_point_invite = $this->CI->FacebookModel->get_user_point_invite($character_id, $server_id, $mobo_service_id);
            echo var_dump($user_point_invite);
            die;
            //$this->output->set_output(json_encode($user_point_invite));
        }
    }

    public function get_invite_success(MeAPI_RequestInterface $request)
    {
        $data = $_GET;
        $oauthtoken = $data["oauthtoken"];
        if ($oauthtoken != $_SESSION["oauthtoken"]) {
            $this->_response = new MeAPI_Response_APIResponse($request, 'REQUEST_FAIL', array());
            return;
        } else {
            $needle = array("fid");
            if (!is_required($data, $needle)) {
                $this->_response = new MeAPI_Response_APIResponse($request, 'INVALID_PARAMS', array());
                return;
            }
            $fid = $data["fid"];
            $datadecode = base64_decode($data["oauthtoken"]);
            $dataarray = json_decode($datadecode, true);
            $userdata = json_decode($dataarray["info"], true);
            $character_id = $userdata["character_id"];
            $server_id = $userdata["server_id"];

            $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
            $friend_list = $this->CI->FacebookModel->get_invite_success($character_id, $fid, $server_id);
            $exclude_ids;
            foreach ($friend_list as $key => $value) {
                $exclude_ids[$key] = $value["to_fid"];
            }

            $this->_response = new MeAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $exclude_ids);
            return;
        }
    }

    public function get_intro()
    {
        $this->render("view_intro");
    }

    private function send_code($data, $type, $times)
    {
        $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
        $giftcode = $this->CI->FacebookModel->get_select_giftcode($type, $times);
        if ($giftcode) {
            $data["type"] = $type;
            $data["code"] = $giftcode["code"];
            $data["times"] = $times;
            $data["create_date"] = date("y-m-d H:i:s", time());
            $this->CI->FacebookModel->insert_use_giftcode($data);
            $this->CI->FacebookModel->update_store_giftcode($giftcode["id"]);
            return true;
        } else {
            return false;
        }
    }


    private function bonus_items($data, $type, $times)
    {
        $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
        $straward = "";
        $title = $lang_arr["gift_notice"];
        if ($type == "share5") {
            $item = array(
                array("item_id" => 50100001, 'count' => 1, 'type' => 4)
            );
            $title = "Sự kiện - Share 5 lần";
            $straward = "Thuốc EXP 1.5 lần";
            $content = "Sự kiện - Share 5 lần";
        } elseif ($type == "invite20") {
            $item = array(
                array("item_id" => 50203048, 'count' => 1, 'type' => 4, 'item_name' => 'Gói Thể Lực Nhỏ')
            );
            $title = "Sự kiện - Invite 20 friends";
            $straward = "Nhận Gói Thể Lực Nhỏ * 10";
            $content = "Sự kiện - Invite 20 friends";
        } elseif ($type == "invite40") {
            $item = array(
                array("item_id" => 50203048, 'count' => 1, 'type' => 4, 'item_name' => 'Gói Thể Lực Nhỏ')
            );
            $title = "Sự kiện - Invite 40 friends";
            $straward = "Nhận Gói Thể Lực Nhỏ * 10";
            $content = "Sự kiện - Invite 40 friends";
        } elseif ($type == "invite60") {
            $item = array(
                array("item_id" => 50203048, 'count' => 1, 'type' => 4, 'item_name' => 'Gói Thể Lực Nhỏ')
            );
            $title = "Sự kiện - Invite 60 friends";
            $straward = "Nhận Gói Thể Lực Nhỏ * 10";
            $content = "Sự kiện - Invite 60 friends";
        } elseif ($type == "invite80") {
            $item = array(
                array("item_id" => 50203048, 'count' => 1, 'type' => 4, 'item_name' => 'Gói Thể Lực Nhỏ')
            );
            $title = "Sự kiện - Invite 80 friends";
            $straward = "Nhận Gói Thể Lực Nhỏ * 10";
            $content = "Sự kiện - Invite 80 friends";
        } else if ($type == "invite100") {
            $item = array(
                array("item_id" => 50203048, 'count' => 1, 'type' => 4, 'item_name' => 'Gói Thể Lực Nhỏ')
            );
            $title = "Sự kiện - Invite 100 friends";
            $straward = "Nhận Gói Thể Lực Nhỏ * 10";
            $content = "Sự kiện - Invite 100 friends";
        }

        //$items = array();
        $api = new GameFullAPI();

        $items = $item;
        //echo var_dump($items); die;
        //$result = $api->add_item($data['mobo_service_id'], $data['server_id'], $items, $title, $content);
        $result = $api->add_item($this->service_id, $data['mobo_service_id'], $data['server_id'], $items, $title, $content, $data['character_id']);


        if (!empty($result) && $result == TRUE) {
            $data["type"] = $type;
            $data["code"] = $straward;
            $data["times"] = $times;
            $data["item"] = json_encode($item);
            $data["create_date"] = date("y-m-d H:i:s", time());
            $this->CI->FacebookModel->insert_use_giftcode($data);
            return true;
        } else {
            return false;
        }
    }

    private function send_item($data, $type, $times)
    {
        $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');

        $straward = "";
        $title = "Thông báo nhận vật phẩm";
        if ($type == "share") {
            $item = array("item_id" => 0, "count" => 10, 'item_name' => 'Thể Lực', 'item_type' => 11);
            $straward = "10 Thể Lực";
            $content = "Chia sẻ thành công lần thứ {$times} trong ngày. {$straward}";
            $title = "Chia sẻ Facebook (Nhận Thể Lực)";
        } else if ($type == "invite") {
            //The Luc
            $item = array("item_id" => 0, "count" => $times, 'item_name' => 'Thể Lực', "item_type" => 11);
            $straward = "Nhận " . ($times) . " Thể Lực";
            $content = "Mời thành công " . ($times / 2) . " bạn. {$straward}";
            $title = "Mời bạn Facebook (Nhận Thể Lực)";
        } else if ($type == "accept") {
            $item = array("item_id" => 0, "count" => 5, 'item_name' => 'Thể Lực', "item_type" => 11);
            $title = "Đồng ý lời mời Facebook (Nhận 5 Thể Lực)";
            $straward = "Nhận Thể Lực";
            $content = "Thành công lần thứ {$times} trong ngày. {$straward}";
        } else if ($type == "like") {
            $item = array("item_id" => 0, 'count' => 10, 'item_name' => 'Thể Lực', 'item_type' => 11);
            $title = "Sự kiện - Like Fanpage Facebook";
            $straward = "Thể Lực";
            $content = "Like Fanpage Facebook nhận Thể Lực";
        }

        $items = array();
        $items2 = array();
        $api = new GameFullAPI();


        $items = array(array('item_id' => $item['item_id'], 'count' => $item['count'], 'item_type' => $item['item_type']));

        $result = $api->add_item($this->service_id, $data['mobo_service_id'], $data['server_id'], $items, $title, $content, $data['character_id']);
        if (!empty($result) && $result == TRUE) {
            $data["type"] = $type;
            $data["code"] = $straward;
            $data["times"] = $times;
            $data["item"] = json_encode($item);
            $data["create_date"] = date("y-m-d H:i:s", time());
            $this->CI->FacebookModel->insert_use_giftcode($data);
            return true;
        } else {
            return false;
        }
    }

    private function check_share_rule($server_id, $mobo_service_id)
    {
        //$quato_share = $this->CI->facebook_config["share_limit"];        
        $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
        return $this->CI->FacebookModel->check_share_rule($server_id, $mobo_service_id);
    }

    public function check_share(MeAPI_RequestInterface $request)
    {
        $data = $_GET;
        $oauthtoken = $data["oauthtoken"];
        if ($oauthtoken != $_SESSION["oauthtoken"]) {
            $this->_response = new MeAPI_Response_APIResponse($request, 'OAUTH_TOKEN_INVALI', null);
            return;
        } else {
            $datadecode = base64_decode($data["oauthtoken"]);
            $dataarray = json_decode($datadecode, true);
            $userdata = json_decode($dataarray["info"], true);
            $character_id = $userdata["character_id"];
            $server_id = $userdata["server_id"];

            $flags = $this->check_time($server_id, $character_id);
            if ($flags > 0) {
                $this->_response = new MeAPI_Response_APIResponse($request, 'INVALID_TIME', $flags);
                return;
            } else {
                $this->_response = new MeAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', null);
                return;
            }
        }
    }

    private function check_time($server_id, $character_id)
    {
        $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
        $sharetime = $this->CI->FacebookModel->check_share($server_id, $character_id);
        if ($sharetime == false) {
            return 0;
        } else {
            $today = date_create('now');
            $date = new DateTime($sharetime);
            $diff = $today->diff($date);
            $hour = (int)$diff->format("%H");
            $minute = (int)$diff->format("%I");
            $second = (int)$diff->format("%S");
            $total = ($hour * 60 * 60) + ($minute * 60) + $second;

            $rule_delay_time = $this->CI->facebook_config["share_delay_minute"];

            if ($total >= $rule_delay_time) {
                return 0;
            } else {
                return $rule_delay_time - $total;
            }
        }
    }

    private function _getNameFace($fid, $access_token)
    {
        $url = "https://graph.facebook.com/v2.2/" . $fid . "/?access_token=" . $access_token;
        $arr = file_get_contents($url);
        $arr = json_decode($arr, true);
        return $arr['name'];
    }

    private function _getRequestPostId($requestid, $access_token)
    {
        try {
            $url = "https://graph.facebook.com/v2.2/" . $requestid . "?access_token=" . $access_token;
            //var_dump($url);die;
            $arr = file_get_contents($url);
            $arr = json_decode($arr, true);
            return $arr['id'];
        } catch (Exception $ex) {
            return "";
        }
    }

    private function _getRequestInvite($requestid, $access_token)
    {
        try {
            $url = "https://graph.facebook.com/v2.2/" . $requestid . "?access_token=" . $access_token;
            $arr = file_get_contents($url);
            $arr = json_decode($arr, true);
            return $arr['id'];
        } catch (Exception $ex) {
            return "";
        }
    }

    private function checkLike($userdata)
    {
        $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
        $is_like = $this->CI->FacebookModel->check_like($userdata["character_id"], $userdata["server_id"]);
        if (!empty($is_like)) {
            return true;
        }
        return false;
    }

    private function _checkLike($accessToken)
    {
        $this->CI->facebook->setAccessToken(trim($accessToken));

        $like = $this->CI->facebook->isPageLike($this->_appID);
        //var_dump($like);
        //die;

        return $like;
    }

    private function _getFacebookId($accessToken)
    {
        $this->CI->facebook->setAccessToken(trim($accessToken));

        $faceId = $this->CI->facebook->getUser();
        if (!empty($faceId)) {
            return $faceId;
        }
        return FALSE;
    }

    public function fb_like(MeAPI_RequestInterface $request)
    {
        $debug = true;
        if ($debug && isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 2) {
            $waiting = 3 - (time() - $_SESSION["last_time"]);
            $this->_response = new MeAPI_Response_APIResponse($request, 'SESSION_EXPIRE', "Bạn truy cập quá nhanh. Vui lòng thử lại sau vài giây");
            return;
        }
        $_SESSION["execute_time"] = time();

        $data = $_GET;
        $oauthtoken = $_GET["oauthtoken"];
        if ($oauthtoken != $_SESSION["oauthtoken"]) {
            $this->_response = new MeAPI_Response_APIResponse($request, 'OAUTH_TOKEN_INVALI', null);
            return;
        } else {
            $fid = $_GET["fid"];
            $this->CI->load->model('../third_party/MeAPI/Models/FacebookModel', 'FacebookModel');
            $accesstoken = $this->CI->FacebookModel->query_access_token($fid);
            if (empty($accesstoken)) {
                $this->_response = new MeAPI_Response_APIResponse($request, 'VERIFY_ACCESS_TOKEN_FAIL', null);
                return;
            } else {
                //$nameFromFb = $this->_getNameFace($fid, $accesstoken);

                $datadecode = base64_decode($oauthtoken);
                $dataarray = json_decode($datadecode, true);
                $userdata = json_decode($dataarray["info"], true);

                $character_id = $userdata["character_id"];
                $character_name = $userdata["character_name"];
                $server_id = $userdata["server_id"];

                $accesstoken = json_decode(base64_decode($dataarray["access_token"]), true);
                $mobo_service_id = $accesstoken['mobo_service_id'];
                if (!$this->getSession($mobo_service_id, $server_id)) {
                    $this->_response = new MeAPI_Response_APIResponse($request, 'SESSION_EXPIRE', "Bạn đang đăng nhập trên một máy khác.");
                    // return;
                }
                $requestid = $data["requestid"];
                if (false && $this->unique_id($requestid)) {
                    $this->_response = new MeAPI_Response_APIResponse($request, 'DUPLICATE_TRAN', "Yêu cầu đang được xử lý hoặc đã được xử lý trước đó.");
                    return;
                }

                $is_like = $this->CI->FacebookModel->check_like($userdata["character_id"], $userdata["server_id"]);
                if (count($is_like) > 0) {
                    $this->_response = new MeAPI_Response_APIResponse($request, 'FACEBOOK_LIKE_EXIST', null);
                    return;
                } else {
                    $data_like = array(
                        "character_id" => $userdata["character_id"],
                        "character_name" => $userdata["character_name"],
                        "server_id" => $userdata["server_id"],
                        "from_fid" => $fid,
                        "from_fname" => $nameFromFb,
                        "mobo_service_id" => $mobo_service_id,
                        "like_date" => date("y-m-d H:i:s", time())
                    );

                    $id_insert = $this->CI->FacebookModel->insert_like($data_like);

                    if ($id_insert > 0) {
                        //Send Code Like
                        $info = array(
                            "mobo_service_id" => $mobo_service_id,
                            "character_id" => $character_id,
                            "character_name" => $character_name,
                            "server_id" => $server_id);
                        $is_send = $this->send_item($info, "like", 1);
                        //var_dump($share_times);
                        //die;
                        if ($is_send) {
                            //Update send code status
                            $is_update = $this->CI->FacebookModel->update_like_new($id_insert);
                            //Send code success
                            $this->_response = new MeAPI_Response_APIResponse($request, 'LIKE_SUCCESS');
                            return;
                        } else {
                            //Send code fail
                            $this->_response = new MeAPI_Response_APIResponse($request, 'SEND_CODE_FAIL', null);
                            return;
                        }
                    } else {
                        //Fail
                        $this->_response = new MeAPI_Response_APIResponse($request, 'LIKE_FAIL', null);
                        return;
                    }
                }
            }
        }
    }

}
