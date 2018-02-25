<?php

/*
 * 
 */

require_once APPPATH . 'third_party/MeAPI/Autoloader.php';

class GameFullAPI {

    private $api_url_payment = 'http://gapi.mobo.vn/';
    private $api_url_data = 'http://gapi.mobo.vn/';
    private $api_app = 'game';
    private $api_secret = 'IDpCJtb6Go10vKGRy5DQ';
    private $service_id = 150;
    private $last_link_request;

    function __construct() {
        MeAPI_Autoloader::register();
    }

    function mapping($sources) {
        if (!is_array($sources) || empty($sources)) {
            return null;
        }
        $destination = $sources;
        $characternames = array("role_name", "name");
        $levels = array("level", "lv", "role_level");
        $golds = array("gold", 'coin');
        $silvers = array("silver");
        $create_times = array("create_time", "register_time");
        $last_login_times = array("last_login_time");
        $vips = array("vip");
        foreach ($sources as $key => $value) {
            if (in_array($key, $characternames)) {
                unset($destination[$key]);
                $destination["character_name"] = $value;
            }
            if (in_array($key, $levels)) {
                unset($destination[$key]);
                $destination["level"] = $value;
            }
            if (in_array($key, $golds)) {
                unset($destination[$key]);
                $destination["gold"] = $value;
            }
            if (in_array($key, $silvers)) {
                unset($destination[$key]);
                $destination["silver"] = $value;
            }
            if (in_array($key, $create_times)) {
                unset($destination[$key]);
                $destination["create_time"] = $value;
            }
            if (in_array($key, $last_login_times)) {
                unset($destination[$key]);
                $destination["last_login"] = $value;
            }
            if (in_array($key, $vips)) {
                unset($destination[$key]);
                $destination["vip"] = $value;
            }
        }
        return $destination;
    }

    private function call_api($url, $control, $func, $params, $log_file_name = 'call_api') {
        $this->last_link_request = $url . '?control=' . $control . "&func=" . $func . "&" . http_build_query($params) . '&app=' . $this->api_app . '&token=' . md5(implode('', $params) . $this->api_secret);
//        if($func == "get_money"){
//            echo $this->last_link_request; die;
//        }
        $ch = curl_init();
        //echo $this->last_link_request ;die;
        curl_setopt($ch, CURLOPT_URL, $this->last_link_request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $result = curl_exec($ch);
        if (!empty($log_file_name)) {
            MeAPI_Log::writeCsv(array($this->last_link_request, $result), $log_file_name);
        }
        return $result;
    }

    /*
     * FUNCTION minuteItem to MGH API
     */

    public function minus_item($service_name, $mobo_service_id, $server_id, $item, $title = "Chuc mung ban nhan duoc qua", $content = "Qua event", $character_id = null) {
        /* http://gapi.mobo.vn/?control=game&func=minus_item&
          mobo_service_id=1061495523104478231&server_id=1&service_name=monggiangho&
          service_id=106&time_stamp=2015-03-17 11:05:00&award=[{"item_id":1,"count":100},{"item_id":21,"count":100}]&
          title=Trừ Bạc và Vàng&
          content=Trừ Bạc và Vàng&app=monggiangho&token=abc
         */
        //send toi da 5 item
        $time_stamp = date('Y-m-d H:i:s', time());
        $params = array();
        //$params['control'] = 'game';
        //$params['func'] = 'minus_item';
        $params['mobo_service_id'] = $mobo_service_id;
        $params['server_id'] = $server_id;
        $params['service_name'] = $service_name;
        $params['service_id'] = $this->service_id;
        $params['time_stamp'] = $time_stamp;
        $params['award'] = json_encode($item);
        $params['title'] = $title;
        $params['content'] = $content;
        if (!empty($character_id))
            $params['character_id'] = $character_id;

        $result = $this->call_api($this->api_url_data, "game", "minus_item", $params, 'minus_item');
        if (!empty($result) && isset($result)) {
            $result = json_decode($result, true);                     
            if ($result["code"] == 0 && array_key_exists("code", $result))
                return true;
            else
                return false;
        }
        return false;
    }

    /*
     * FUNCTION sendItem to LangKhach API
     */

    public function add_item($service_name, $mobo_service_id, $server_id, $item, $title = "", $content = "", $character_id = null) {
        /* http://gapi.mobo.vn/?control=game&func=add_item
         * &mobo_service_id=1071499048780501561
         * &server_id=1
         * &service_name=hiepkhach
         * &service_id=107
         * &time_stamp=2015-05-05 08:00:13
         * &award=[{"item_id":20017,"count":1}]
         * &title=Sự kiện - Chia sẻ Facebook
         * &content=Chia sẻ thành công lần thứ 2 trong ngày. Nhận 10 thể lực
         * &app=hiepkhach
         * &token=5f8f4c4d003f83d5196b6da8ca894354
         */
        /*
         * add item
         * award: json format
         *  [ 
          {"item_id":1001,"count":1}, //type int
          {"item_id":1002,"count":2},
          ...
          ],
         */
        //send toi da 5 item
        $time_stamp = date('Y-m-d H:i:s', time());
        $params = array();
        // $params['control'] = 'game';
        // $params['func'] = 'add_item';
        $params['mobo_service_id'] = $mobo_service_id;
        $params['server_id'] = $server_id;
        $params['service_name'] = $service_name;
        $params['service_id'] = $this->service_id;
        $params['time_stamp'] = $time_stamp;
        $params['award'] = json_encode($item);
        $params['title'] = $title;
        $params['content'] = $content;
        if (!empty($character_id))
            $params['character_id'] = $character_id;

        $result = $this->call_api($this->api_url_data, "game", "add_item", $params, 'add_item');
        if (!empty($result) && isset($result)) {
            $result = json_decode($result, true);
            if ($result["code"] == 0 && array_key_exists("code", $result))
                return true;
            else
                return false;
        }
        return false;
    }

    public function add_item_result($service_name, $mobo_service_id, $server_id, $item, $title = "", $content = "", $character_id = null) {
        /* http://gapi.mobo.vn/?control=game&func=add_item
         * &mobo_service_id=1071499048780501561
         * &server_id=1
         * &service_name=hiepkhach
         * &service_id=107
         * &time_stamp=2015-05-05 08:00:13
         * &award=[{"item_id":20017,"count":1}]
         * &title=Sự kiện - Chia sẻ Facebook
         * &content=Chia sẻ thành công lần thứ 2 trong ngày. Nhận 10 thể lực
         * &app=hiepkhach
         * &token=5f8f4c4d003f83d5196b6da8ca894354
         */
        /*
         * add item
         * award: json format
         *  [ 
          {"item_id":1001,"count":1}, //type int
          {"item_id":1002,"count":2},
          ...
          ],
         */
        //send toi da 5 item
        $time_stamp = date('Y-m-d H:i:s', time());
        $params = array();
        // $params['control'] = 'game';
        // $params['func'] = 'add_item';
        $params['mobo_service_id'] = $mobo_service_id;
        $params['server_id'] = $server_id;
        $params['service_name'] = $service_name;
        $params['service_id'] = $this->service_id;
        $params['time_stamp'] = $time_stamp;
        $params['award'] = json_encode($item);
        $params['title'] = $title;
        $params['content'] = $content;
        
        if (!empty($character_id))
            $params['character_id'] = $character_id;

        $result = $this->call_api($this->api_url_data, "game", "add_item", $params, 'add_item');        
        return $result;
    }
    
    public function get_user_info($service_name, $mobo_service_id, $server_id, $is_full = false) {
        //$wallet_info = $this->CI->GameAPI->query_wallet_info('1021488227995350684', 1, '2014-12-04 15:00:00');
        /*
          http://gapi.mobo.vn/?control=game&func=get_game_account_info&mobo_service_id=1061495523104478231&
          server_id=1&service_name=hiepkhach&service_id=107&
          time_stamp=2015-03-17 11:05:00&app=hiepkhach&token=abc
         */
        $time_stamp = date('Y-m-d H:i:s', time());
        $params = array();
        //$params['control'] = 'game';
        //$params['func'] = 'get_game_account_info';
        $params['mobo_service_id'] = $mobo_service_id;
        $params['server_id'] = $server_id;
        $params['service_name'] = $service_name;
        $params['service_id'] = $this->service_id;
        $params['time_stamp'] = $time_stamp;
        $result = $this->call_api($this->api_url_data, "game", "get_game_account_info", $params, '');
        if (!empty($result) && isset($result)) {
            $result = json_decode($result, true);
            if ($result["code"] == 0 && array_key_exists("code", $result)) {
                if (is_string($result["data"])) {
                    $data = json_decode($result["data"], true);
                } else {
                    $data = $result["data"];
                }
                if (empty($data))
                    return null;
                //var_dump($data);
                //die;
                if (isset($data[0])) {
                    $rsdata = null;
                    foreach ($data as $key => $value) {
                        $rsdata[] = $this->mapping($value);
                    }
                    return $rsdata;
                } else {
                    $rsdata = null;
                    $rsdata[] = $this->mapping($data);
                    return $rsdata;
                }
            } else {
                return null;
            }
        }
        return null;
    }

    public function get_list_server($service_name) {
        $params = array();
        $params['service_name'] = $service_name;
        $params['service_id'] = $this->service_id;
        $result = $this->call_api($this->api_url_data, "game", "get_server_list", $params, '');
        if (!empty($result) && isset($result)) {
            $result = json_decode($result, true);
            if ($result["code"] == 500102 && array_key_exists("code", $result)) {

                $return['code'] = $result['code'];
                $return['mess'] = 'GET_INFO_SUCCESS';
                $return['data'] = $result['data']['data'];
                return $return;
            } else {
                return null;
            }
        }
        return $return;
    }

    public function get_money($service_name, $mobo_service_id, $server_id, $startdate, $end_date = "", $group_type = 0, $character_id = null) {
//        if ($_SERVER['REMOTE_ADDR'] == "127.0.0.1" || $mobo_service_id == "1031504929525518311") {
//            return array('amount' => 0,
//                'origin_money' => 5700,
//                'money' => 0);
//        }
        $params = array();
        $params["mobo_service_id"] = $mobo_service_id;
        $params["server_id"] = $server_id;
        $params["service_name"] = $service_name;
        $params["service_id"] = $this->service_id;
        $params["group_type"] = $group_type;
        $params["character_id"] = $character_id;

        if (!empty($startdate)) {
            $params["date"] = $startdate;
        }

        if (!empty($startdate)) {
            $params["end_date"] = $end_date;
        }

        //var_dump(implode("", $params) );
//        $token = md5(implode("", $params) . $this->api_secret);
//        $params["app"] = $this->api_app;
//        $params["token"] = $token;
        $data = $this->call_api($this->api_url_payment, "game", "get_money", $params, '');
        //echo $url_call; die;        
        $data = @json_decode($data, true);

        if ($data["code"] == 500102) {
            foreach ($data["data"] as $key => $value) {
                if (empty($value)) {
                    $data["data"][$key] = 0;
                }
            }
            return $data["data"];
        }
        return null;
    }

    function get_remote_ip() {
        $ipaddress = '';
        if ($_SERVER['HTTP_CLIENT_IP'])
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if ($_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if ($_SERVER['HTTP_X_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if ($_SERVER['HTTP_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if ($_SERVER['HTTP_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if ($_SERVER['REMOTE_ADDR'])
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

}

?>
