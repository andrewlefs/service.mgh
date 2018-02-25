<?php

require_once APPPATH . 'third_party/MeAPI/Autoloader.php';
require_once APPPATH . '/core/EI_Controller.php';

class keobuabao extends EI_Controller {

    private $mobo_id_test = array("552397949", "886899541", "300391787");
    private $is_test = true;

    public function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $this->load->library('GameFullAPI');
        $this->load->model('events/m_keobuabao', "keobuabao", false);
        $this->load->model('events/m_shopnganluong', "shopnganluong", false);

        $this->CI = & get_instance();
        MeAPI_Autoloader::register();
        $this->CI->cache_config = MeAPI_Config_Game::cache();
    }

    private function init() {
        if ($this->verify_uri() != true) {
            $this->data["message"] = "Truy cập không hợp lệ";
            $this->render("deny", $this->data);
        }

        $user = $this->get_info();
        $this->data["user"] = $user;
        $_SESSION['user_info'] = serialize($user);
    }

    public function index() {
        $this->init();

        if (empty($_SESSION['user_info'])) {
            echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            die;
        }

        $user = unserialize($_SESSION['user_info']);
        //var_dump($user); die;
        $this->data["user"] = $user;

        $char_id = $user->character_id;
        $server_id = $user->server_id;
        $char_name = $user->character_name;
        $mobo_service_id = $user->mobo_service_id;
        $mobo_id = $user->mobo_id;

        $this->data["mobo_id"] = $mobo_id;

        //Non public
        if ($this->is_test && !in_array($user->mobo_id, $this->mobo_id_test)) {
            echo "Bạn không có quyền truy cập sự kiện này";
            die;
        }

        //Set Session
        $this->storeSession($mobo_service_id, $server_id);
        
        $this->check_join_expried();

        echo $this->load->view("events/keobuabao/index", $this->data, true);
    }

    public function thamgia() {
        if (empty($_SESSION['user_info'])) {
            echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            die;
        }

        $user = unserialize($_SESSION['user_info']);
        //var_dump($user); die;
        $this->data["user"] = $user;

        $char_id = $user->character_id;
        $server_id = $user->server_id;
        $char_name = $user->character_name;
        $mobo_service_id = $user->mobo_service_id;
        $mobo_id = $user->mobo_id;

        //Check join Game
        if ($char_id == "" || $server_id == "") {
            echo "Vui lòng vào game trước khi tham gia sự kiện...";
            die;
        }

        //Get Tournament List       
        $tournament_list = $this->keobuabao->get_tournament();
        if (count($tournament_list) == 0) {
            echo "Không có giải đấu...";
            die;
        }

        $tournament_filter = array();
        $client_ip = $this->get_client_ip();

        foreach ($tournament_list as $key => $value) {
            //echo var_dump(preg_replace('/\s+/', '', $value["tournament_ip_list"]));die;
            $ip_list = preg_replace('/\s+/', '', $value["tournament_ip_list"]);
            $server_list = preg_replace('/\s+/', '', $value["tournament_server_list"]);

            if ($ip_list != "" && $server_list == "") {
                $ip_list = explode(";", $ip_list);
                if (in_array($client_ip, $ip_list)) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }

            if ($server_list != "" && $ip_list == "") {
                $server_list = explode(";", $server_list);
                if (in_array($server_id, $server_list)) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }

            if ($server_list == "" && $ip_list == "") {
                array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                    "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                    , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
            }

            if ($server_list != "" && $ip_list != "") {
                $server_list = explode(";", $server_list);
                $ip_list = explode(";", $ip_list);
                if (in_array($server_id, $server_list) && (in_array($client_ip, $ip_list))) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }
        }

        $tournament = $tournament_filter;
        if (count($tournament) == 0) {
            echo "Không có giải đấu...*";
            die;
        }

        //User Point
        $this->data["user_point"] = $this->user_point($char_id, $server_id, $mobo_service_id, $mobo_id, $char_name);
        $this->data["tournament"] = $tournament;
        $this->data["moccuoc_group"] = $this->keobuabao->get_moccuoc_group($tournament[0]["id"]);
        
        $this->check_join_expried();

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("events/keobuabao/thamgia", $this->data, true);
    }

    public function thachdau() {
        if (empty($_SESSION['user_info'])) {
            echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            die;
        }

        $user = unserialize($_SESSION['user_info']);
        //var_dump($user); die;
        $this->data["user"] = $user;

        $char_id = $user->character_id;
        $server_id = $user->server_id;
        $char_name = $user->character_name;
        $mobo_service_id = $user->mobo_service_id;
        $mobo_id = $user->mobo_id;

        //Check join Game
        if ($char_id == "" || $server_id == "") {
            echo "Vui lòng vào game trước khi tham gia sự kiện...";
            die;
        }

        //Get Tournament List       
        $tournament_list = $this->keobuabao->get_tournament();
        if (count($tournament_list) == 0) {
            echo "Không có giải đấu...";
            die;
        }

        $tournament_filter = array();
        $client_ip = $this->get_client_ip();

        foreach ($tournament_list as $key => $value) {
            //echo var_dump(preg_replace('/\s+/', '', $value["tournament_ip_list"]));die;
            $ip_list = preg_replace('/\s+/', '', $value["tournament_ip_list"]);
            $server_list = preg_replace('/\s+/', '', $value["tournament_server_list"]);

            if ($ip_list != "" && $server_list == "") {
                $ip_list = explode(";", $ip_list);
                if (in_array($client_ip, $ip_list)) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }

            if ($server_list != "" && $ip_list == "") {
                $server_list = explode(";", $server_list);
                if (in_array($server_id, $server_list)) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }

            if ($server_list == "" && $ip_list == "") {
                array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                    "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                    , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
            }

            if ($server_list != "" && $ip_list != "") {
                $server_list = explode(";", $server_list);
                $ip_list = explode(";", $ip_list);
                if (in_array($server_id, $server_list) && (in_array($client_ip, $ip_list))) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }
        }

        $tournament = $tournament_filter;
        if (count($tournament) == 0) {
            echo "Không có giải đấu...*";
            die;
        }

        //User Point
        $this->data["user_point"] = $this->user_point($char_id, $server_id, $mobo_service_id, $mobo_id, $char_name);
        $this->data["tournament"] = $tournament;
        $this->data["moccuoc_group"] = $this->keobuabao->get_moccuoc_group($tournament[0]["id"]);


        //Get Join Hisory
        $moccuoc_group_id = $_GET["moccuoc_group"];
        $this->data["join_history"] = $this->keobuabao->get_join_history_by_moccuoc_group($moccuoc_group_id, $mobo_service_id);

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("events/keobuabao/thachdau", $this->data, true);
    }

    public function thachdau_reload() {
        if (empty($_SESSION['user_info'])) {
            echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            die;
        }

        $user = unserialize($_SESSION['user_info']);
        //var_dump($user); die;
        $this->data["user"] = $user;

        $char_id = $user->character_id;
        $server_id = $user->server_id;
        $char_name = $user->character_name;
        $mobo_service_id = $user->mobo_service_id;
        $mobo_id = $user->mobo_id;

        //Check join Game
        if ($char_id == "" || $server_id == "") {
            echo "Vui lòng vào game trước khi tham gia sự kiện...";
            die;
        }

        //Get Tournament List       
        $tournament_list = $this->keobuabao->get_tournament();
        if (count($tournament_list) == 0) {
            echo "Không có giải đấu...";
            die;
        }

        $tournament_filter = array();
        $client_ip = $this->get_client_ip();

        foreach ($tournament_list as $key => $value) {
            //echo var_dump(preg_replace('/\s+/', '', $value["tournament_ip_list"]));die;
            $ip_list = preg_replace('/\s+/', '', $value["tournament_ip_list"]);
            $server_list = preg_replace('/\s+/', '', $value["tournament_server_list"]);

            if ($ip_list != "" && $server_list == "") {
                $ip_list = explode(";", $ip_list);
                if (in_array($client_ip, $ip_list)) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }

            if ($server_list != "" && $ip_list == "") {
                $server_list = explode(";", $server_list);
                if (in_array($server_id, $server_list)) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }

            if ($server_list == "" && $ip_list == "") {
                array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                    "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                    , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
            }

            if ($server_list != "" && $ip_list != "") {
                $server_list = explode(";", $server_list);
                $ip_list = explode(";", $ip_list);
                if (in_array($server_id, $server_list) && (in_array($client_ip, $ip_list))) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }
        }

        $tournament = $tournament_filter;
        if (count($tournament) == 0) {
            echo "Không có giải đấu...*";
            die;
        }

        //User Point
        $this->data["user_point"] = $this->user_point($char_id, $server_id, $mobo_service_id, $mobo_id, $char_name);
        $this->data["tournament"] = $tournament;
        $this->data["moccuoc_group"] = $this->keobuabao->get_moccuoc_group($tournament[0]["id"]);


        //Get Join Hisory
        $moccuoc_group_id = $_GET["moccuoc_group"];
        $this->data["join_history"] = $this->keobuabao->get_join_history_by_moccuoc_group($moccuoc_group_id, $mobo_service_id);

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("events/keobuabao/thachdau_reload", $this->data, true);
    }

    function join_details() {
        if (empty($_SESSION['user_info'])) {
            $result["code"] = "-1";
            $result["message"] = "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            $this->output->set_output(json_encode($result));
            return;
        }

        $join_id = $_GET["join_id"];
        $join_details = $this->keobuabao->get_join_history_by_id_details($join_id);
        if (count($join_details) == 0) {
            $result["code"] = "-1";
            $result["message"] = "Không lấy được chi tiết đặt cược";
            $this->output->set_output(json_encode($result));
            return;
        }
        $play_details = $this->keobuabao->get_play_history_by_join_id($join_id);
        if (count($play_details) == 0) {
            $result["code"] = "-1";
            $result["message"] = "Không lấy được chi tiết kết quả";
            $this->output->set_output(json_encode($result));
            return;
        }
        if ($join_details[0]["join_status"] == 1) {
            $win_point = ($join_details[0]["point_cuoc"] / 100) * 90;
            $html_result = '<div style="text-align: center;"><span style="font-weight: bold; color: #053cf7">Thắng: <span style="">' . $win_point . ' Ngân Lượng</span></span></div>';
        }
        if ($join_details[0]["join_status"] == 2) {
            $html_result = '<div style="text-align: center;"><span style="font-weight: bold; color: #f79646">Hòa: <span style="">' . $join_details[0]["point_cuoc"] . ' Ngân Lượng</span></span></div>';
        }
        if ($join_details[0]["join_status"] == 3) {
            $html_result = '<div style="text-align: center;"><span style="font-weight: bold; color: #af1318">Thua: <span style="">' . $join_details[0]["point_cuoc"] . ' Ngân Lượng</span></span></div>';
        }
        $html_result .= '<table class="table-role" style="width: 100%; padding: 4px;margin-top: 10px;" cellpadding="4" cellspacing="0">';
        $html_result .= '<tbody><tr><td><span style="font-weight: bold; color: #FF5722;"">Bạn chọn</span></td><td><span style="font-weight: bold; color: #FF5722;"">' . $play_details[0]["char_name"] . '(S' . $play_details[0]["server_id"] . ')</span></td></tr> ';
        $html_result .= '<tr><td><img src="/mgh2/assets_dev/events/keobuabao/images/' . $play_details[0]["type_choose_join"] . '.png"></td>';
        $html_result .= '<td><img src="/mgh2/assets_dev/events/keobuabao/images/' . $play_details[0]["type_choose_play"] . '.png"></td></tr></tbody></table>';
        $date_play = new DateTime($play_details[0]["play_date"]);
        $html_result .= '<div style="text-align: center;">Thời gian đấu: ' . $date_play->format('d-m-Y H:i:s') . '</div>';
        $result["code"] = "0";
        $result["message"] = $html_result;
        $this->output->set_output(json_encode($result));
        return;
    }
    
    function play_details() {
        if (empty($_SESSION['user_info'])) {
            $result["code"] = "-1";
            $result["message"] = "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            $this->output->set_output(json_encode($result));
            return;
        }

        $play_id = $_GET["play_id"];
        $play_details = $this->keobuabao->get_play_history_by_id_details($play_id);
        if (count($play_details) == 0) {
            $result["code"] = "-1";
            $result["message"] = "Không lấy được chi tiết thách đấu";
            $this->output->set_output(json_encode($result));
            return;
        }
        $join_details = $this->keobuabao->get_join_history_by_id_details($play_details[0]["join_id"]);
        if (count($join_details) == 0) {
            $result["code"] = "-1";
            $result["message"] = "Không lấy được chi tiết đặt cược";
            $this->output->set_output(json_encode($result));
            return;
        }
        if ($play_details[0]["play_status"] == 1) {
            $win_point = ($play_details[0]["point_bonus"] / 100) * 90;
            $html_result = '<div style="text-align: center;"><span style="font-weight: bold; color: #053cf7">Thắng: <span style="">' . $win_point . ' Ngân Lượng</span></span></div>';
        }
        if ($play_details[0]["play_status"] == 2) {
            $html_result = '<div style="text-align: center;"><span style="font-weight: bold; color: #f79646">Hòa</span></span></div>';
        }
        if ($play_details[0]["play_status"] == 3) {
            $html_result = '<div style="text-align: center;"><span style="font-weight: bold; color: #af1318">Thua: <span style="">' . $play_details[0]["point_bonus"] . ' Ngân Lượng</span></span></div>';
        }
        $html_result .= '<table class="table-role" style="width: 100%; padding: 4px;margin-top: 10px;" cellpadding="4" cellspacing="0">';
        $html_result .= '<tbody><tr><td><span style="font-weight: bold; color: #FF5722;"">Bạn chọn</span></td><td><span style="font-weight: bold; color: #FF5722;"">' . $join_details[0]["char_name"] . '(S' . $join_details[0]["server_id"] . ')</span></td></tr> ';
        $html_result .= '<tr><td><img src="/mgh2/assets_dev/events/keobuabao/images/' . $play_details[0]["type_choose_play"] . '.png"></td>';
        $html_result .= '<td><img src="/mgh2/assets_dev/events/keobuabao/images/' . $play_details[0]["type_choose_join"] . '.png"></td></tr></tbody></table>';
        $date_play = new DateTime($play_details[0]["play_date"]);
        $html_result .= '<div style="text-align: center;">Thời gian đấu: ' . $date_play->format('d-m-Y H:i:s') . '</div>';
        $result["code"] = "0";
        $result["message"] = $html_result;
        $this->output->set_output(json_encode($result));
        return;
    }

    function join_process() {
        if (isset($_SESSION["execute_time_join_process"]) && (time() - $_SESSION["execute_time_join_process"]) < 10) {
            $result["code"] = "-1";
            $result["message"] = "Mỗi lần đặt cược phải cách nhau 10 giây.";
            $this->output->set_output(json_encode($result));
            return;
        }

        $_SESSION["execute_time_join_process"] = time();

        if (empty($_SESSION['user_info'])) {
            $result["code"] = "-1";
            $result["message"] = "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            $this->output->set_output(json_encode($result));
            return;
        }

        $user = unserialize($_SESSION['user_info']);
        //var_dump($user); die;
        $this->data["user"] = $user;

        $char_id = $user->character_id;
        $server_id = $user->server_id;
        $char_name = $user->character_name;
        $mobo_service_id = $user->mobo_service_id;
        $mobo_id = $user->mobo_id;

        if (!$this->getSession($mobo_service_id, $server_id)) {
            $result["code"] = "-1";
            $result["message"] = "Bạn đang đăng nhập trên một máy khác.";
            $this->output->set_output(json_encode($result));
            return;
        }

        //Non public
        if ($this->is_test && !in_array($mobo_id, $this->mobo_id_test)) {
            $result["code"] = "-1";
            $result["message"] = "Bạn không có quyền truy cập sự kiện này";
            $this->output->set_output(json_encode($result));
            return;
        }

        //Check join Game
        if ($char_id == "" || $server_id == "") {
            $result["code"] = "-1";
            $result["message"] = "Vui lòng vào game trước khi tham gia sự kiện...";
            $this->output->set_output(json_encode($result));
            return;
        }

        //Get Tournament List       
        $tournament_list = $this->keobuabao->get_tournament();
        if (count($tournament_list) == 0) {
            $result["code"] = "-1";
            $result["message"] = "Không có giải đấu...";
            $this->output->set_output(json_encode($result));
            return;
        }

        $tournament_filter = array();
        $client_ip = $this->get_client_ip();

        foreach ($tournament_list as $key => $value) {
            //echo var_dump(preg_replace('/\s+/', '', $value["tournament_ip_list"]));die;
            $ip_list = preg_replace('/\s+/', '', $value["tournament_ip_list"]);
            $server_list = preg_replace('/\s+/', '', $value["tournament_server_list"]);

            if ($ip_list != "" && $server_list == "") {
                $ip_list = explode(";", $ip_list);
                if (in_array($client_ip, $ip_list)) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }

            if ($server_list != "" && $ip_list == "") {
                $server_list = explode(";", $server_list);
                if (in_array($server_id, $server_list)) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }

            if ($server_list == "" && $ip_list == "") {
                array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                    "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                    , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
            }

            if ($server_list != "" && $ip_list != "") {
                $server_list = explode(";", $server_list);
                $ip_list = explode(";", $ip_list);
                if (in_array($server_id, $server_list) && (in_array($client_ip, $ip_list))) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }
        }

        $tournament = $tournament_filter;
        if (count($tournament) == 0) {
            $result["code"] = "-1";
            $result["message"] = "Không có giải đấu...*";
            $this->output->set_output(json_encode($result));
            return;
        }

        $type_choose = $_GET["type_choose"];
        $moccuoc_group = $_GET["moccuoc_group"];

        //Check Valid Type
        $valid_type = array("bao", "bua", "keo");
        if (!in_array($type_choose, $valid_type)) {
            $result["code"] = "-1";
            $result["message"] = "Đặt cược không chính xác, bạn vui lòng thử lại";
            $this->output->set_output(json_encode($result));
            return;
        }

        //Check Valid MocCuocGroup
        $moccuoc_group_valid = $this->keobuabao->get_moccuoc_group_by_id($moccuoc_group);
        if (count($moccuoc_group_valid) == 0) {
            $result["code"] = "-1";
            $result["message"] = "Mức cược không hợp lệ, bạn vui lòng thử lại";
            $this->output->set_output(json_encode($result));
            return;
        }

        //Check Valid Point
        $datauser = $this->shopnganluong->user_check_point_exist($char_id, $server_id, $mobo_service_id);
        if (count($datauser) == 0) {
            $result["code"] = "-1";
            $result["message"] = "Đặt cược thất bại, vui lòng thử lại*.";
            $this->output->set_output(json_encode($result));
            return;
        }

        if ($datauser[0]["user_point"] < $moccuoc_group_valid[0]["moccuoc_required"]) {
            $result["code"] = "-1";
            $result["message"] = "Ngân lượng của bạn không đủ để đặt cược.";
            $this->output->set_output(json_encode($result));
            return;
        }

        //Insert Join History
        $userdata_p["char_id"] = $char_id;
        $userdata_p["server_id"] = $server_id;
        $userdata_p["char_name"] = $char_name;
        $userdata_p["mobo_service_id"] = $mobo_service_id;
        $userdata_p["mobo_id"] = $mobo_id;

        $play_date_start = date('Y-m-d H:i:s', time());
        $play_date_end = date('Y-m-d H:i:s', strtotime($play_date_start . ' + 1 days'));
        //$play_date_end = date('Y-m-d H:i:s', strtotime($play_date_start . ' + 10 minutes'));
        $userdata_p["moccuoc_group_id"] = $moccuoc_group;
        $userdata_p["play_date_start"] = $play_date_start;
        $userdata_p["play_date_end"] = $play_date_end;
        $userdata_p["type_choose"] = $type_choose;
        $userdata_p["point_cuoc"] = $moccuoc_group_valid[0]["moccuoc_required"];
        $userdata_p["tournament_id"] = $tournament[0]["id"];

        $i_id = $this->keobuabao->insert_id("event_keobuabao_join_history", $userdata_p);
        if ($i_id == 0) {
            $result["code"] = "-1";
            $result["message"] = "Đặt cược thất bại, vui lòng thử lại**.";
            $this->output->set_output(json_encode($result));
            return;
        }

        //Minus NL
        if ($this->shopnganluong->update_point($char_id, $server_id, $mobo_service_id, $moccuoc_group_valid[0]["moccuoc_required"]) == 0) {
            $result["code"] = "-1";
            $result["message"] = "Đặt cược thất bại, vui lòng thử lại***.";
            $this->output->set_output(json_encode($result));
            return;
        }

        $this->keobuabao->update_join_status_history($i_id, 0);

        $result["code"] = "0";
        $result["message"] = "Đặt cược thành công";
        $this->output->set_output(json_encode($result));
        return;
    }

    function play_process() {
        if (isset($_SESSION["execute_time_play_process"]) && (time() - $_SESSION["execute_time_play_process"]) < 10) {
            $result["code"] = "-1";
            $result["message"] = "Mỗi lần Đấu phải cách nhau 10 giây.";
            $this->output->set_output(json_encode($result));
            return;
        }

        $_SESSION["execute_time_play_process"] = time();

        if (empty($_SESSION['user_info'])) {
            $result["code"] = "-1";
            $result["message"] = "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            $this->output->set_output(json_encode($result));
            return;
        }

        $user = unserialize($_SESSION['user_info']);
        //var_dump($user); die;
        $this->data["user"] = $user;

        $char_id = $user->character_id;
        $server_id = $user->server_id;
        $char_name = $user->character_name;
        $mobo_service_id = $user->mobo_service_id;
        $mobo_id = $user->mobo_id;

        if (!$this->getSession($mobo_service_id, $server_id)) {
            $result["code"] = "-1";
            $result["message"] = "Bạn đang đăng nhập trên một máy khác.";
            $this->output->set_output(json_encode($result));
            return;
        }

        //Non public
        if ($this->is_test && !in_array($mobo_id, $this->mobo_id_test)) {
            $result["code"] = "-1";
            $result["message"] = "Bạn không có quyền truy cập sự kiện này";
            $this->output->set_output(json_encode($result));
            return;
        }

        //Check join Game
        if ($char_id == "" || $server_id == "") {
            $result["code"] = "-1";
            $result["message"] = "Vui lòng vào game trước khi tham gia sự kiện...";
            $this->output->set_output(json_encode($result));
            return;
        }

        //Get Tournament List       
        $tournament_list = $this->keobuabao->get_tournament();
        if (count($tournament_list) == 0) {
            $result["code"] = "-1";
            $result["message"] = "Không có giải đấu...";
            $this->output->set_output(json_encode($result));
            return;
        }

        $tournament_filter = array();
        $client_ip = $this->get_client_ip();

        foreach ($tournament_list as $key => $value) {
            //echo var_dump(preg_replace('/\s+/', '', $value["tournament_ip_list"]));die;
            $ip_list = preg_replace('/\s+/', '', $value["tournament_ip_list"]);
            $server_list = preg_replace('/\s+/', '', $value["tournament_server_list"]);

            if ($ip_list != "" && $server_list == "") {
                $ip_list = explode(";", $ip_list);
                if (in_array($client_ip, $ip_list)) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }

            if ($server_list != "" && $ip_list == "") {
                $server_list = explode(";", $server_list);
                if (in_array($server_id, $server_list)) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }

            if ($server_list == "" && $ip_list == "") {
                array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                    "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                    , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
            }

            if ($server_list != "" && $ip_list != "") {
                $server_list = explode(";", $server_list);
                $ip_list = explode(";", $ip_list);
                if (in_array($server_id, $server_list) && (in_array($client_ip, $ip_list))) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }
        }

        $tournament = $tournament_filter;
        if (count($tournament) == 0) {
            $result["code"] = "-1";
            $result["message"] = "Không có giải đấu...*";
            $this->output->set_output(json_encode($result));
            return;
        }

        $type_choose = $_GET["type_choose"];
        $join_id = $_GET["join_id"];

        //Check Valid Type
        $valid_type = array("bao", "bua", "keo");
        if (!in_array($type_choose, $valid_type)) {
            $result["code"] = "-1";
            $result["message"] = "Lựa chọn Đấu không chính xác, bạn vui lòng thử lại";
            $this->output->set_output(json_encode($result));
            return;
        }

        //Check Valid Join Id
        $join_history_valid = $this->keobuabao->get_join_history_by_id($join_id);
        if (count($join_history_valid) == 0) {
            $result["code"] = "-1";
            $result["message"] = "Không có thông tin đặt cược";
            $this->output->set_output(json_encode($result));
            return;
        }

        if ($join_history_valid[0]["join_status"] > 0) {
            $result["code"] = "-1";
            $result["message"] = "Không có thông tin đặt cược hoặc thời gian lượt đấu đã kết thúc";
            $this->output->set_output(json_encode($result));
            return;
        }

        //Check Valid Point
        $datauser = $this->shopnganluong->user_check_point_exist($char_id, $server_id, $mobo_service_id);
        if (count($datauser) == 0) {
            $result["code"] = "-1";
            $result["message"] = "Đấu thất bại, vui lòng thử lại*.";
            $this->output->set_output(json_encode($result));
            return;
        }

        if ($datauser[0]["user_point"] < $join_history_valid[0]["point_cuoc"]) {
            $result["code"] = "-1";
            $result["message"] = "Ngân lượng của bạn không đủ để Đấu lượt này.";
            $this->output->set_output(json_encode($result));
            return;
        }

        //Insert Play History
        $userdata_p["char_id"] = $char_id;
        $userdata_p["server_id"] = $server_id;
        $userdata_p["char_name"] = $char_name;
        $userdata_p["mobo_service_id"] = $mobo_service_id;
        $userdata_p["mobo_id"] = $mobo_id;

        $userdata_p["join_id"] = $join_id;
        $userdata_p["type_choose_play"] = $type_choose;
        $userdata_p["tournament_id"] = $tournament[0]["id"];

        $i_id = $this->keobuabao->insert_id("event_keobuabao_play_history", $userdata_p);
        if ($i_id == 0) {
            $result["code"] = "-1";
            $result["message"] = "Đấu thất bại, vui lòng thử lại**.";
            $this->output->set_output(json_encode($result));
            return;
        }

        //Process Result 1:Win, 2:Draw, 3:Loses
        $type_choose_join = $join_history_valid[0]["type_choose"];
        $type_choose_play = $type_choose;

        if ($type_choose_play == 'keo') {
            if ($type_choose_join == 'bua') {
                //Loses
                $this->update_result_loses($i_id, $char_id, $server_id, $mobo_service_id, $join_id, $join_history_valid, $type_choose_join, $type_choose_play);
                return;
            }
            if ($type_choose_join == 'keo') {
                //Draw
                $this->update_result_draw($i_id, $char_id, $server_id, $mobo_service_id, $join_id, $join_history_valid, $type_choose_join, $type_choose_play);
                return;
            }
            if ($type_choose_join == 'bao') {
                //Win
                $this->update_result_win($i_id, $char_id, $server_id, $mobo_service_id, $join_id, $join_history_valid, $type_choose_join, $type_choose_play);
                return;
            }
        }
        ///////////////////////
        if ($type_choose_play == 'bua') {
            if ($type_choose_join == 'bua') {
                //Draw
                $this->update_result_draw($i_id, $char_id, $server_id, $mobo_service_id, $join_id, $join_history_valid, $type_choose_join, $type_choose_play);
                return;
            }
            if ($type_choose_join == 'keo') {
                //Win
                $this->update_result_win($i_id, $char_id, $server_id, $mobo_service_id, $join_id, $join_history_valid, $type_choose_join, $type_choose_play);
                return;
            }
            if ($type_choose_join == 'bao') {
                //Loses 
                $this->update_result_loses($i_id, $char_id, $server_id, $mobo_service_id, $join_id, $join_history_valid, $type_choose_join, $type_choose_play);
                return;
            }
        }
        ///////////////////////
        if ($type_choose_play == 'bao') {
            if ($type_choose_join == 'bua') {
                //Win
                $this->update_result_win($i_id, $char_id, $server_id, $mobo_service_id, $join_id, $join_history_valid, $type_choose_join, $type_choose_play);
                return;
            }
            if ($type_choose_join == 'keo') {
                //Loses 
                $this->update_result_loses($i_id, $char_id, $server_id, $mobo_service_id, $join_id, $join_history_valid, $type_choose_join, $type_choose_play);
                return;
            }
            if ($type_choose_join == 'bao') {
                //Draw
                $this->update_result_draw($i_id, $char_id, $server_id, $mobo_service_id, $join_id, $join_history_valid, $type_choose_join, $type_choose_play);
                return;
            }
        }

        $result["code"] = "-1";
        $result["message"] = "Yêu cầu không có thực!!!";
        $this->output->set_output(json_encode($result));
        return;
    }

    public function update_result_loses($i_id, $char_id, $server_id, $mobo_service_id, $join_id, $join_history_valid, $type_choose_join, $type_choose_play) {
        //Loses 
        //Update Status Join Win
        $i_update_join_status = $this->keobuabao->update_join_status_history($join_id, 1);
        if ($i_update_join_status == 0) {
            $result["code"] = "-1";
            $result["message"] = "Đấu thất bại, vui lòng thử lại***.";
            $this->output->set_output(json_encode($result));
            return;
        }

        //Minus 100% NL User Play                
        if ($this->shopnganluong->update_point($char_id, $server_id, $mobo_service_id, $join_history_valid[0]["point_cuoc"]) == 0) {
            $this->keobuabao->update_join_status_history($join_id, 0);
            $result["code"] = "-1";
            $result["message"] = "Đấu thất bại, vui lòng thử lại****.";
            $this->output->set_output(json_encode($result));
            return;
        }
        //Update status Minus Point
        $this->keobuabao->update_minus_point_play_status($i_id, 1);

        //Add (90% + NL Cuoc) User Join & 100% Point Cược
        $point_bonus_user_join = $join_history_valid[0]["point_cuoc"] + (($join_history_valid[0]["point_cuoc"] / 100) * 90);
        $i_add_point = $this->shopnganluong->add_point($join_history_valid[0]["char_id"], $join_history_valid[0]["server_id"], $join_history_valid[0]["mobo_service_id"], $point_bonus_user_join);
        if ($i_add_point > 0) {
            //Update Point Play Status     
            $this->keobuabao->update_add_point_play_status($i_id, 1);
            //Update Play Result Loses
            $this->keobuabao->update_play_result($i_id, 3, $join_history_valid[0]["point_cuoc"], $type_choose_join);
            $html_result = '<div style="text-align: center;">Thua: <span style="">' . $join_history_valid[0]["point_cuoc"] . ' Ngân Lượng</span></div>';
            $html_result .= '<table style="width: 100%; padding: 4px;margin-top: 10px;" cellpadding="4" cellspacing="0">';
            $html_result .= '<tbody><tr><td>Bạn chọn</td><td>' . $join_history_valid[0]["char_name"] . '</td></tr> ';
            $html_result .= '<tr><td><img src="/mgh2/assets_dev/events/keobuabao/images/' . $type_choose_play . '.png"></td>';
            $html_result .= '<td><img src="/mgh2/assets_dev/events/keobuabao/images/' . $type_choose_join . '.png"></td></tr></tbody></table>';

            $result["code"] = "0";
            $result["message"] = $html_result;
            $this->output->set_output(json_encode($result));
            return;
        } else {
            //Add point user join Fail
            //Retore point user play
            $this->shopnganluong->add_point($char_id, $server_id, $mobo_service_id, $join_history_valid[0]["point_cuoc"]);
            //Update status Join
            $this->keobuabao->update_join_status_history($join_id, 0);
            $result["code"] = "-1";
            $result["message"] = "Đấu thất bại, vui lòng thử lại*****.";
            $this->output->set_output(json_encode($result));
            return;
        }
    }

    public function update_result_draw($i_id, $char_id, $server_id, $mobo_service_id, $join_id, $join_history_valid, $type_choose_join, $type_choose_play) {
        //Draw
        //Update Status Join Draw
        $i_update_join_status = $this->keobuabao->update_join_status_history($join_id, 2);
        if ($i_update_join_status == 0) {
            $result["code"] = "-1";
            $result["message"] = "Đấu thất bại, vui lòng thử lại.";
            $this->output->set_output(json_encode($result));
            return;
        }

        //Return 100% NL User Join 
        $point_bonus_user_join = $join_history_valid[0]["point_cuoc"];
        $i_add_point = $this->shopnganluong->add_point($join_history_valid[0]["char_id"], $join_history_valid[0]["server_id"], $join_history_valid[0]["mobo_service_id"], $point_bonus_user_join);
        //Update Play Result Draw
        $i_updare_draw = $this->keobuabao->update_play_result($i_id, 2, $join_history_valid[0]["point_cuoc"], $type_choose_join);
        if ($i_updare_draw == 0) {
            $this->keobuabao->update_join_status_history($join_id, 0);
            $result["code"] = "-1";
            $result["message"] = "Đấu thất bại, vui lòng thử lại.";
            $this->output->set_output(json_encode($result));
            return;
        }

        $html_result = '<div style="text-align: center;">Hòa: <span style="">Không có thưởng</span></div>';
        $html_result .= '<table style="width: 100%; padding: 4px;margin-top: 10px;" cellpadding="4" cellspacing="0">';
        $html_result .= '<tbody><tr><td>Bạn chọn</td><td>' . $join_history_valid[0]["char_name"] . '</td></tr> ';
        $html_result .= '<tr><td><img src="/mgh2/assets_dev/events/keobuabao/images/' . $type_choose_play . '.png"></td>';
        $html_result .= '<td><img src="/mgh2/assets_dev/events/keobuabao/images/' . $type_choose_join . '.png"></td></tr></tbody></table>';

        $result["code"] = "0";
        $result["message"] = $html_result;
        $this->output->set_output(json_encode($result));
        return;
    }

    public function update_result_win($i_id, $char_id, $server_id, $mobo_service_id, $join_id, $join_history_valid, $type_choose_join, $type_choose_play) {
        //Win
        //Update Status Join Lose
        $i_update_join_status = $this->keobuabao->update_join_status_history($join_id, 3);
        if ($i_update_join_status == 0) {
            $result["code"] = "-1";
            $result["message"] = "Đấu thất bại, vui lòng thử lại***.";
            $this->output->set_output(json_encode($result));
            return;
        }

        //Minus 100% NL User Join                
//        if ($this->shopnganluong->update_point($join_history_valid[0]["char_id"], $join_history_valid[0]["server_id"], $join_history_valid[0]["mobo_service_id"], $join_history_valid[0]["point_cuoc"]) == 0) {
//            $this->keobuabao->update_join_status_history($join_id, 0);
//            $result["code"] = "-1";
//            $result["message"] = "Đấu thất bại, vui lòng thử lại****.";
//            $this->output->set_output(json_encode($result));
//            return;
//        }
        //Add 90% NL User Play
        $point_bonus_user_play = ($join_history_valid[0]["point_cuoc"] / 100) * 90;
        $i_add_point = $this->shopnganluong->add_point($char_id, $server_id, $mobo_service_id, $point_bonus_user_play);
        if ($i_add_point > 0) {
            //Update Point Play Status     
            $this->keobuabao->update_add_point_play_status($i_id, 1);
            //Update Play Result Loses
            $this->keobuabao->update_play_result($i_id, 1, $join_history_valid[0]["point_cuoc"], $type_choose_join);
            $html_result = '<div style="text-align: center;">Thắng: <span style="">' . $point_bonus_user_play . ' Ngân Lượng</span></div>';
            $html_result .= '<table style="width: 100%; padding: 4px;margin-top: 10px;" cellpadding="4" cellspacing="0">';
            $html_result .= '<tbody><tr><td>Bạn chọn</td><td>' . $join_history_valid[0]["char_name"] . '</td></tr> ';
            $html_result .= '<tr><td><img src="/mgh2/assets_dev/events/keobuabao/images/' . $type_choose_play . '.png"></td>';
            $html_result .= '<td><img src="/mgh2/assets_dev/events/keobuabao/images/' . $type_choose_join . '.png"></td></tr></tbody></table>';

            $result["code"] = "0";
            $result["message"] = $html_result;
            $this->output->set_output(json_encode($result));
            return;
        }
    }

    function check_join_expried() {
        $user = unserialize($_SESSION['user_info']);
        //var_dump($user); die;
        $this->data["user"] = $user;

        $char_id = $user->character_id;
        $server_id = $user->server_id;
        $char_name = $user->character_name;
        $mobo_service_id = $user->mobo_service_id;
        $mobo_id = $user->mobo_id;

        if (!$this->getSession($mobo_service_id, $server_id)) {
            return;
        }

        //Get Tournament List       
        $tournament_list = $this->keobuabao->get_tournament();
        if (count($tournament_list) == 0) {
            return;
        }

        $tournament_filter = array();
        $client_ip = $this->get_client_ip();

        foreach ($tournament_list as $key => $value) {
            //echo var_dump(preg_replace('/\s+/', '', $value["tournament_ip_list"]));die;
            $ip_list = preg_replace('/\s+/', '', $value["tournament_ip_list"]);
            $server_list = preg_replace('/\s+/', '', $value["tournament_server_list"]);

            if ($ip_list != "" && $server_list == "") {
                $ip_list = explode(";", $ip_list);
                if (in_array($client_ip, $ip_list)) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }

            if ($server_list != "" && $ip_list == "") {
                $server_list = explode(";", $server_list);
                if (in_array($server_id, $server_list)) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }

            if ($server_list == "" && $ip_list == "") {
                array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                    "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                    , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
            }

            if ($server_list != "" && $ip_list != "") {
                $server_list = explode(";", $server_list);
                $ip_list = explode(";", $ip_list);
                if (in_array($server_id, $server_list) && (in_array($client_ip, $ip_list))) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }
        }

        $tournament = $tournament_filter;
        if (count($tournament) == 0) {
            return;
        }

        $join_history_expried = $this->keobuabao->get_join_history_expried_by_tournament_id($tournament[0]["id"]);
        if (count($join_history_expried) == 0) {
            return;
        }

        foreach ($join_history_expried as $key => $value) {
            //Refund NL
            $i_add_point = $this->shopnganluong->add_point($value["char_id"], $value["server_id"], $value["mobo_service_id"], $value["point_cuoc"]);
            if ($i_add_point > 0) {
                //Update Status Join Refund
                //Update status Join
                $this->keobuabao->update_join_status_history($value["id"], 4);
            }
        }
    }

    //User Point    
    private function user_point($char_id, $server_id, $mobo_service_id, $mobo_id, $char_name) {
        $datauser = $this->shopnganluong->user_check_point_exist($char_id, $server_id, $mobo_service_id);

        //Check Dublicated MoboId & Update MoboId NULL
        $user_check_point_exist_mobo = $this->shopnganluong->user_check_point_exist_mobo($mobo_id);
        if (count($user_check_point_exist_mobo) > 1) {
            $update_moboid_null = $this->shopnganluong->update_moboid_null($mobo_id);
        }

        $datauser = $this->shopnganluong->user_check_point_exist($char_id, $server_id, $mobo_service_id);

        if (count($datauser) > 0) {
            foreach ($datauser as $key => $value) {
                //Update Mobo Id
                if ($value["mobo_id"] == null || empty($value["mobo_id"]) || ($value["mobo_id"] != $mobo_id)) {
                    $this->shopnganluong->update_shopnganluong_point_moboid($value["id"], $mobo_id);

                    //Insert Log Update MoboID
                    $userdata_p['mobo_service_id'] = $mobo_service_id;
                    $userdata_p['char_id'] = $char_id;
                    $userdata_p['server_id'] = $server_id;
                    $userdata_p['from_mobo_id'] = $value["mobo_id"];
                    $userdata_p['to_mobo_id'] = $mobo_id;
                    $userdata_p["update_date"] = Date('Y-m-d H:i:s');
                    $this->shopnganluong->insert("event_shopnganluong_point_update_moboid_history", $userdata_p);
                }

                return $value["user_point"];
            }
        } else {
            //Insert User Point
            $userdata_p["char_id"] = $char_id;
            $userdata_p["server_id"] = $server_id;
            $userdata_p["char_name"] = $char_name;
            $userdata_p["mobo_service_id"] = $mobo_service_id;
            $userdata_p["mobo_id"] = $mobo_id;
            $userdata_p["user_point"] = 0;

            $this->shopnganluong->insert("event_shopnganluong_point", $userdata_p);

            return 0;
        }
    }

    public function get_server_id_merge($server_id) {
        foreach ($this->get_server_list($this->service_name) as $key => $value) {
            if ($value["server_id"] == $server_id) {
                return $value["server_id_merge"];
            }
        }
        return $server_id;
    }

    public function get_server_id_merge_test() {
        $server_id = $_GET["server_id"];
        foreach ($this->get_server_list($this->service_name) as $key => $value) {
            if ($value["server_id"] == $server_id) {
                echo $value["server_id_merge"];
                die;
            }
        }
    }

    public function get_server_public() {
        $server_list_filter = array();
        foreach ($this->get_server_list($this->service_name) as $key => $value) {
            if ($value["server_id"] <= 53) {
                array_push($server_list_filter, Array("server_id" => $value["server_id"], "server_name" => $value["server_name"]));
            }
        }
        $this->data["server_list"] = $server_list_filter;
    }

    function lichsu_join() {
        if (empty($_SESSION['user_info'])) {
            echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            die;
        }

        $user = unserialize($_SESSION['user_info']);
        //var_dump($user); die;
        $this->data["user"] = $user;

        $char_id = $user->character_id;
        $server_id = $user->server_id;
        $mobo_service_id = $user->mobo_service_id;

        //Get Tournament List       
        $tournament_list = $this->keobuabao->get_tournament();
        if (count($tournament_list) == 0) {
            $result["code"] = "-1";
            $result["message"] = "Không có giải đấu...";
            $this->output->set_output(json_encode($result));
            return;
        }

        $tournament_filter = array();
        $client_ip = $this->get_client_ip();

        foreach ($tournament_list as $key => $value) {
            //echo var_dump(preg_replace('/\s+/', '', $value["tournament_ip_list"]));die;
            $ip_list = preg_replace('/\s+/', '', $value["tournament_ip_list"]);
            $server_list = preg_replace('/\s+/', '', $value["tournament_server_list"]);

            if ($ip_list != "" && $server_list == "") {
                $ip_list = explode(";", $ip_list);
                if (in_array($client_ip, $ip_list)) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }

            if ($server_list != "" && $ip_list == "") {
                $server_list = explode(";", $server_list);
                if (in_array($server_id, $server_list)) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }

            if ($server_list == "" && $ip_list == "") {
                array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                    "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                    , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
            }

            if ($server_list != "" && $ip_list != "") {
                $server_list = explode(";", $server_list);
                $ip_list = explode(";", $ip_list);
                if (in_array($server_id, $server_list) && (in_array($client_ip, $ip_list))) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }
        }

        $tournament = $tournament_filter;
        if (count($tournament) == 0) {
            $result["code"] = "-1";
            $result["message"] = "Không có giải đấu...*";
            $this->output->set_output(json_encode($result));
            return;
        }

        $data["lichsu_join"] = $this->keobuabao->get_lichsu_join($tournament[0]["id"], $server_id, $mobo_service_id);
        $this->data["lichsu_join"] = $data["lichsu_join"];

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("events/keobuabao/lichsu_join", $this->data, true);
    }
    
    function lichsu_play() {
        if (empty($_SESSION['user_info'])) {
            echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            die;
        }

        $user = unserialize($_SESSION['user_info']);
        //var_dump($user); die;
        $this->data["user"] = $user;

        $char_id = $user->character_id;
        $server_id = $user->server_id;
        $mobo_service_id = $user->mobo_service_id;

        //Get Tournament List       
        $tournament_list = $this->keobuabao->get_tournament();
        if (count($tournament_list) == 0) {
            $result["code"] = "-1";
            $result["message"] = "Không có giải đấu...";
            $this->output->set_output(json_encode($result));
            return;
        }

        $tournament_filter = array();
        $client_ip = $this->get_client_ip();

        foreach ($tournament_list as $key => $value) {
            //echo var_dump(preg_replace('/\s+/', '', $value["tournament_ip_list"]));die;
            $ip_list = preg_replace('/\s+/', '', $value["tournament_ip_list"]);
            $server_list = preg_replace('/\s+/', '', $value["tournament_server_list"]);

            if ($ip_list != "" && $server_list == "") {
                $ip_list = explode(";", $ip_list);
                if (in_array($client_ip, $ip_list)) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }

            if ($server_list != "" && $ip_list == "") {
                $server_list = explode(";", $server_list);
                if (in_array($server_id, $server_list)) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }

            if ($server_list == "" && $ip_list == "") {
                array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                    "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                    , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
            }

            if ($server_list != "" && $ip_list != "") {
                $server_list = explode(";", $server_list);
                $ip_list = explode(";", $ip_list);
                if (in_array($server_id, $server_list) && (in_array($client_ip, $ip_list))) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"]));
                }
            }
        }

        $tournament = $tournament_filter;
        if (count($tournament) == 0) {
            $result["code"] = "-1";
            $result["message"] = "Không có giải đấu...*";
            $this->output->set_output(json_encode($result));
            return;
        }

        $data["lichsu_play"] = $this->keobuabao->get_lichsu_play($tournament[0]["id"], $server_id, $mobo_service_id);
        $this->data["lichsu_play"] = $data["lichsu_play"];

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("events/keobuabao/lichsu_play", $this->data, true);
    }

    function senditemforgame($data, $item, $title, $content) {
        if (empty($data) || empty($item)) {
            return false;
        }
        //load thu vien chung
        $api = new GameFullAPI();
        $addditem = $api->add_item_result("150", $data["mobo_service_id"], $data["server_id"], $item, $title, $content);
        return $addditem;
    }

    private function storeSession($character_id, $server_id) {
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
            $memcache->set($key, session_id(), false, 3600);
            $memcache->close();
        }
    }

    private function getSession($character_id, $server_id) {
        $memcache = new Memcache;
        $host = $this->CI->cache_config["systeminfo"]["host"];
        $port = $this->CI->cache_config["systeminfo"]["port"];
        $key = md5("store_{$character_id}_{$server_id}");
        $status_memcache = @$memcache->connect($host, $port);
        if ($status_memcache == true) {
            $session_id = $memcache->get($key);
            $memcache->close();
            if (empty($session_id)) {
                return false;
            } else if ($session_id == session_id()) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    // Function to get the client IP address
    private function call_api_get($api_url) {
        set_time_limit(30);
        $urlrequest = $api_url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlrequest);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $result = curl_exec($ch);
        $err_msg = "";

        if ($result === false)
            $err_msg = curl_error($ch);

        //var_dump($result);
        //die;
        curl_close($ch);
        return $result;
    }

    function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

}
