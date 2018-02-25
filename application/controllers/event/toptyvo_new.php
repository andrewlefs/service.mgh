<?php

require_once APPPATH . 'third_party/MeAPI/Autoloader.php';
require_once APPPATH . '/core/EI_Controller.php';

class toptyvo_new extends EI_Controller {

    private $mobo_id_test = array("552397949", "364853453", "886899541");
    private $is_test = false;

    public function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $this->load->library('GameFullAPI');
        $this->load->model('events/m_toptyvo', "toptyvo", false);
        $this->data["controler"] = $this;

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

        //Load Server List 
        $server_list_filter = array();
        foreach ($this->get_server_list($this->service_name) as $key => $value) {
            array_push($server_list_filter, array("server_id" => $value["server_id"], "server_name" => $value["server_name"]));
        }
        $this->data["server_list"] = $server_list_filter;

        //Set Session
        $this->storeSession($mobo_service_id, $server_id);

        echo $this->load->view("events/toptyvo_new/index", $this->data, true);
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
        $id = $_GET["id"];
        $tournament_list = $this->toptyvo->get_tournament_details($id);
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
        if (!empty($tournament) && count($tournament) > 0) {
            foreach ($tournament as $key => $value) {
                $this->data["tournament"] = $tournament;
                }
        }

        $data["TopArena"] = $this->toptyvo->Event_TopArena_GetList($server_id, 1);
        $data["TopBattlePoint"] = $this->toptyvo->Event_TopBattlePoint_GetList($server_id, 1);
        
//        var_dump($data["TopArena"]);
//        echo"-------------------------------------------<br>";
//        var_dump($data["TopBattlePoint"]); die;
        //$this->data["history"] = $data["history"];

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("events/toptyvo_new/thamgia", $this->data, true);
    }

    public function get_pk_point_test() {
        $Sever_ID = $_GET["Sever_ID"];
        $UserID = $_GET["UserID"];
        $tournament_store_proc = $_GET["tournament_store_proc"];

        //echo $Sever_ID; die;
        $data["user_point"] = $this->toptyvo->get_pk_point_new($tournament_store_proc, $Sever_ID, $UserID);

        if (count($data["user_point"]) > 0) {
            $this->output->set_output(var_dump($data["user_point"]));
        } else {
            echo "Không có data...";
        }
    }

    function get_exchange_history() {
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

        $data["history"] = $this->toptyvo->get_exchange_history_new($tournament_id, $char_id, $server_id, $mobo_service_id);
        $this->data["history"] = $data["history"];

        $data["history_top"] = $this->toptyvo->get_exchange_history_new_top($tournament_id, $char_id, $server_id, $mobo_service_id);
        $this->data["history_top"] = $data["history_top"];

        $data["history_top_premiership"] = $this->toptyvo->get_exchange_history_premiership($tournament_id, $char_id, $server_id, $mobo_service_id);
        $this->data["history_top_premiership"] = $data["history_top_premiership"];

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("events/toptyvo_new/history", $this->data, true);
    }

    public function gift_exchange() {
        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 10) {
            $result["code"] = "-1";
            $result["message"] = "Mỗi lần đổi quà phải cách nhau 10 giây.";
        } else {
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

            $userdata_p["char_id"] = $char_id;
            $userdata_p["server_id"] = $server_id;
            $userdata_p["char_name"] = $char_name;
            $userdata_p["mobo_service_id"] = $mobo_service_id;

            $id = $_GET["id"];

            //if($mobo_service_id != "1061495878891844701" && $mobo_service_id != "1061498602823097731"
            //&& $mobo_id != "671456185" && $mobo_id != "485372761" && $mobo_id != "247165485" && $mobo_id != "853017650" && $mobo_id != "477409422" 
            //&& $mobo_id != "857316426" && $mobo_id != "666629660" && $mobo_id != "886899541"  && $mobo_id != "128147013"){
            //    $result["code"] = "-1";
            //    $result["message"] = "Hệ thống đang bảo trì, vui lòng đợi.";
            //}
            //else{
            //Check Reward Valid
            $reward_details = $this->toptyvo->get_reward_details($id);
            if (count($reward_details) > 0) {
                if (!$this->getSession($mobo_service_id, $server_id)) {
                    $result["code"] = "-1";
                    $result["message"] = "Bạn đang đăng nhập trên một máy khác.";
                } else {
                    //Check Exist            
                    if ($this->toptyvo->check_exist_exchange_gift($reward_details[0]["tournament_id"], $server_id, $char_id)) {
                        $result["code"] = "-1";
                        $result["message"] = "Bạn đã nhận quà giải đấu này rồi !";
                    } else {
                        //Check Receive Gift Date
                        $get_tournament_details = $this->toptyvo->get_tournament_details($reward_details[0]["tournament_id"]);
                        $date_now = date('Y-m-d H:i:s');

                        $tournament_date_start_reward = date('Y-m-d H:i:s', strtotime($get_tournament_details[0]["tournament_date_start_reward"]));
                        $tournament_date_end_reward = date('Y-m-d H:i:s', strtotime($get_tournament_details[0]["tournament_date_end_reward"]));

                        if (strtotime($date_now) < strtotime($tournament_date_start_reward)) {
                            $result["code"] = "-1";
                            $result["message"] = "Chưa đến thời gian nhận quà !";
                        } else
                        if (strtotime($date_now) > strtotime($tournament_date_end_reward)) {
                            //echo $tournament_date_end_reward; die;
                            $result["code"] = "-1";
                            $result["message"] = "Thời gian nhận quà đã kết thúc !";
                        } else {
                            //Check Point Valid
                            //if($server_id == 28 && $char_id == "750733994"){
                            //	$userpoint[0]["CURRPOINT"] = 20000;
                            //}
                            //else{
                            //$userpoint = $this->toptyvo->get_pk_point($server_id, $char_id);  
                            //}	
                            //$userpoint = $this->toptyvo->get_pk_point($server_id, $char_id);  
//                            if (in_array($server_id, $this->server_merge)) {
//                                $server_id_merge = 3;
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                            } else
//                            if (in_array($server_id, $this->server_merge2)) {
//                                $server_id_merge = 1;
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                            }
////                                    else
////                                        if(in_array($server_id, $this->server_merge3))
////                                        {
////                                            $server_id_merge = 5;
////                                            $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
////                                        }
//                            else
//                            if (in_array($server_id, $this->server_merge4)) {
//                                $server_id_merge = 7;
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                            } else
//                            if (in_array($server_id, $this->server_merge5)) {
//                                $server_id_merge = 11;
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                            } else
//                            if (in_array($server_id, $this->server_merge6)) {
//                                $server_id_merge = 17;
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                            } else
//                            if (in_array($server_id, $this->server_merge7)) {
//                                $server_id_merge = 21;
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                            } else
//                            if (in_array($server_id, $this->server_merge8)) {
//                                $server_id_merge = 27;
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                            } else
//                            if (in_array($server_id, $this->server_merge9)) {
//                                $server_id_merge = 33;
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                            } else
//                            if (in_array($server_id, $this->server_merge10)) {
//                                $server_id_merge = 39;
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                            } else
//                            if (in_array($server_id, $this->server_merge11)) {
//                                $server_id_merge = 43;
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                            } else {
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id, $char_id);
//                            }

                            $server_id_merge = $this->get_server_id_merge($server_id);
                            $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);

                            if ($userpoint[0]["CURRPOINT"] < $reward_details[0]["reward_point"] || $userpoint[0]["CURRPOINT"] == "") {
                                $result["code"] = "-1";
                                $result["message"] = "Số dư điểm không đủ!";
                            } else {
                                //Add exchange History
                                $userdata_p["char_id"] = $char_id;
                                $userdata_p["server_id"] = $server_id;
                                $userdata_p["char_name"] = $char_name;
                                $userdata_p["mobo_service_id"] = $mobo_service_id;
                                $userdata_p["reward_id"] = $id;
                                $userdata_p["exchange_date"] = Date('Y-m-d H:i:s');
                                $userdata_p["tournament_id"] = $reward_details[0]["tournament_id"];

                                $i_id = $this->toptyvo->insert_id("event_toppk_exchange_history", $userdata_p);

                                if ($i_id > 0) {
                                    //Write Cache Log              
                                    $write_cache = $this->toptyvo->write_cache_log($char_id, $server_id, $userpoint[0]["CURRPOINT"], $reward_details[0]["reward_point"], $id, Date('Y-m-d H:i:s'));
                                    //echo var_dump($write_cache); die;               
                                    if ($write_cache[0]["Errcode"] == "00000") {
                                        //SEND Item
                                        $info['mobo_service_id'] = $datadecode["mobo_service_id"];
                                        $info['character_id'] = $userdata["character_id"];
                                        $info['server_id'] = $userdata["server_id"];
                                        $info['character_name'] = $userdata["character_name"];

                                        $item1 = null;

                                        if ($reward_details[0]["reward_item1_code"] != 0 && $reward_details[0]["reward_item1_number"] != 0) {
                                            $item1[] = array("item_id" => (int) $reward_details[0]["reward_item1_code"], "count" => (int) $reward_details[0]["reward_item1_number"]);
                                        }
                                        if ($reward_details[0]["reward_item2_code"] != 0 && $reward_details[0]["reward_item2_number"] != 0) {
                                            $item1[] = array("item_id" => (int) $reward_details[0]["reward_item2_code"], "count" => (int) $reward_details[0]["reward_item2_number"]);
                                        }
                                        if ($reward_details[0]["reward_item3_code"] != 0 && $reward_details[0]["reward_item3_number"] != 0) {
                                            $item1[] = array("item_id" => (int) $reward_details[0]["reward_item3_code"], "count" => (int) $reward_details[0]["reward_item3_number"]);
                                        }
                                        if ($reward_details[0]["reward_item4_code"] != 0 && $reward_details[0]["reward_item4_number"] != 0) {
                                            $item1[] = array("item_id" => (int) $reward_details[0]["reward_item4_code"], "count" => (int) $reward_details[0]["reward_item4_number"]);
                                        }
                                        if ($reward_details[0]["reward_item5_code"] != 0 && $reward_details[0]["reward_item5_number"] != 0) {
                                            $item1[] = array("item_id" => (int) $reward_details[0]["reward_item5_code"], "count" => (int) $reward_details[0]["reward_item5_number"]);
                                        }

                                        $data_result = $this->senditemforgame($info, $item1, "Chúc mừng bạn nhận được quà", "Quà tích lũy điểm Tỷ Võ Trạng Nguyên");
                                        $this->toptyvo->update_exchange_history($i_id, json_encode($item1), json_encode($data_result));

                                        $result["code"] = "0";
                                        $result["message"] = "Nhận quà thành công!";
                                    } else {
                                        $result["code"] = "-1";
                                        $result["message"] = "Nhận quà thất bại, vui lòng thử lại!";
                                    }
                                } else {
                                    $result["code"] = "-1";
                                    $result["message"] = "Nhận quà thất bại, vui lòng thử lại!";
                                }
                            }
                        }
                    }
                    //$result["code"] = "0";
                    //$result["message"] = "Nhận quà thành công!";
                }
            } else {
                $result["code"] = "-1";
                $result["message"] = "Thông tin quà không chính xác!";
            }
            //}
        }

        $this->output->set_output(json_encode($result));
    }

    public function gift_top_exchange() {
        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 10) {
            $result["code"] = "-1";
            $result["message"] = "Mỗi lần đổi quà phải cách nhau 10 giây.";
        } else {
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

            $userdata_p["char_id"] = $char_id;
            $userdata_p["server_id"] = $server_id;
            $userdata_p["char_name"] = $char_name;
            $userdata_p["mobo_service_id"] = $mobo_service_id;

            $tournament_id = $_GET["id"];

            //if($mobo_service_id != "1061495878891844701" && $mobo_service_id != "1061498602823097731"
            //&& $mobo_id != "671456185" && $mobo_id != "485372761" && $mobo_id != "247165485" && $mobo_id != "853017650" && $mobo_id != "477409422" 
            //&& $mobo_id != "857316426" && $mobo_id != "666629660" && $mobo_id != "886899541"  && $mobo_id != "128147013"){
            //    $result["code"] = "-1";
            //    $result["message"] = "Hệ thống đang bảo trì, vui lòng đợi.";
            //}
            //else{
            if (!$this->getSession($mobo_service_id, $server_id)) {
                $result["code"] = "-1";
                $result["message"] = "Bạn đang đăng nhập trên một máy khác.";
            } else {
                //Check Exist            
                if ($this->toptyvo->check_exist_exchange_gift_top($tournament_id, $server_id, $char_id)) {
                    $result["code"] = "-1";
                    $result["message"] = "Bạn đã nhận quà Top giải đấu này rồi !";
                } else {
                    //Check Receive Gift Date
                    $get_tournament_details = $this->toptyvo->get_tournament_details($tournament_id);
                    $date_now = date('Y-m-d H:i:s');

                    $tournament_date_start_reward = date('Y-m-d H:i:s', strtotime($get_tournament_details[0]["tournament_date_start_reward"]));
                    $tournament_date_end_reward = date('Y-m-d H:i:s', strtotime($get_tournament_details[0]["tournament_date_end_reward"]));

                    if (!($server_id == 28 && $char_id == "750733994") && strtotime($date_now) < strtotime($tournament_date_start_reward)) {
                        $result["code"] = "-1";
                        $result["message"] = "Chưa đến thời gian nhận quà !";
                    } else
                    if (!($server_id == 28 && $char_id == "750733994") && strtotime($date_now) > strtotime($tournament_date_end_reward)) {
                        $result["code"] = "-1";
                        $result["message"] = "Thời gian nhận quà đã kết thúc !";
                    } else {
                        //Check Rank Valid 
//                        if (in_array($server_id, $this->server_merge)) {
//                            $server_id_merge = 3;
//                            $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                        } else
//                        if (in_array($server_id, $this->server_merge2)) {
//                            $server_id_merge = 1;
//                            $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                        }
////                                    else
////                                        if(in_array($server_id, $this->server_merge3))
////                                        {
////                                            $server_id_merge = 5;
////                                            $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
////                                        }
//                        else
//                        if (in_array($server_id, $this->server_merge4)) {
//                            $server_id_merge = 7;
//                            $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                        } else
//                        if (in_array($server_id, $this->server_merge5)) {
//                            $server_id_merge = 11;
//                            $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                        } else
//                        if (in_array($server_id, $this->server_merge6)) {
//                            $server_id_merge = 17;
//                            $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                        } else
//                        if (in_array($server_id, $this->server_merge7)) {
//                            $server_id_merge = 21;
//                            $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                        } else
//                        if (in_array($server_id, $this->server_merge8)) {
//                            $server_id_merge = 27;
//                            $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                        } else
//                        if (in_array($server_id, $this->server_merge9)) {
//                            $server_id_merge = 33;
//                            $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                        } else
//                        if (in_array($server_id, $this->server_merge10)) {
//                            $server_id_merge = 39;
//                            $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                        } else
//                        if (in_array($server_id, $this->server_merge11)) {
//                            $server_id_merge = 43;
//                            $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                        } else {
//                            $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id, $char_id);
//                        }

                        $server_id_merge = $this->get_server_id_merge($server_id);
                        $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);

                        if (!($server_id == 28 && $char_id == "750733994") && count($userpoint) == 0) {
                            $result["code"] = "-1";
                            $result["message"] = "Hạng của bạn không đủ để nhận quà!*";
                        } else {

                            if ($server_id == 28 && $char_id == "750733994") {
                                $userpoint[0]["RANK"] = 85;
                            }

                            $reward_rank_valid = $this->toptyvo->check_rank_valid($userpoint[0]["RANK"], $tournament_id);

                            if (count($reward_rank_valid) == 0) {
                                $result["code"] = "-1";
                                $result["message"] = "Hạng của bạn không đủ để nhận quà!**";
                            } else {
                                //Add exchange History
                                $userdata_p["char_id"] = $char_id;
                                $userdata_p["server_id"] = $server_id;
                                $userdata_p["char_name"] = $char_name;
                                $userdata_p["mobo_service_id"] = $mobo_service_id;
                                $userdata_p["reward_id"] = $reward_rank_valid[0]["id"];
                                $userdata_p["exchange_date"] = Date('Y-m-d H:i:s');
                                $userdata_p["tournament_id"] = $tournament_id;

                                $i_id = $this->toptyvo->insert_id("event_toppk_exchange_history_top", $userdata_p);

                                if ($i_id > 0) {
                                    //SEND Item
                                    $info['mobo_service_id'] = $datadecode["mobo_service_id"];
                                    $info['character_id'] = $userdata["character_id"];
                                    $info['server_id'] = $userdata["server_id"];
                                    $info['character_name'] = $userdata["character_name"];

                                    $item1 = null;

                                    if ($reward_rank_valid[0]["reward_item1_code"] != 0 && $reward_rank_valid[0]["reward_item1_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $reward_rank_valid[0]["reward_item1_code"], "count" => (int) $reward_rank_valid[0]["reward_item1_number"]);
                                    }
                                    if ($reward_rank_valid[0]["reward_item2_code"] != 0 && $reward_rank_valid[0]["reward_item2_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $reward_rank_valid[0]["reward_item2_code"], "count" => (int) $reward_rank_valid[0]["reward_item2_number"]);
                                    }
                                    if ($reward_rank_valid[0]["reward_item3_code"] != 0 && $reward_rank_valid[0]["reward_item3_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $reward_rank_valid[0]["reward_item3_code"], "count" => (int) $reward_rank_valid[0]["reward_item3_number"]);
                                    }
                                    if ($reward_rank_valid[0]["reward_item4_code"] != 0 && $reward_rank_valid[0]["reward_item4_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $reward_rank_valid[0]["reward_item4_code"], "count" => (int) $reward_rank_valid[0]["reward_item4_number"]);
                                    }
                                    if ($reward_rank_valid[0]["reward_item5_code"] != 0 && $reward_rank_valid[0]["reward_item5_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $reward_rank_valid[0]["reward_item5_code"], "count" => (int) $reward_rank_valid[0]["reward_item5_number"]);
                                    }

                                    $data_result = $this->senditemforgame($info, $item1, "Chúc mừng bạn nhận được quà", "Quà Top Server Tỷ Võ Trạng Nguyên");
                                    $this->toptyvo->update_exchange_history_top($i_id, json_encode($item1), json_encode($data_result));

                                    $result["code"] = "0";
                                    $result["message"] = "Nhận quà Top thành công!";
                                } else {
                                    $result["code"] = "-1";
                                    $result["message"] = "Nhận quà Top thất bại, vui lòng thử lại!";
                                }
                            }
                        }
                    }
                }
            }
            //}
        }

        $this->output->set_output(json_encode($result));
    }

    public function gift_top_exchange_premiership() {
        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 10) {
            $result["code"] = "-1";
            $result["message"] = "Mỗi lần đổi quà phải cách nhau 10 giây.";
        } else {
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

            $userdata_p["char_id"] = $char_id;
            $userdata_p["server_id"] = $server_id;
            $userdata_p["char_name"] = $char_name;
            $userdata_p["mobo_service_id"] = $mobo_service_id;

            $tournament_id = $_GET["id"];

            //if($mobo_service_id != "1061495878891844701" && $mobo_service_id != "1061498602823097731"
            //&& $mobo_id != "671456185" && $mobo_id != "485372761" && $mobo_id != "247165485" && $mobo_id != "853017650" && $mobo_id != "477409422" 
            //&& $mobo_id != "857316426" && $mobo_id != "666629660" && $mobo_id != "886899541"  && $mobo_id != "128147013"){
            //    $result["code"] = "-1";
            //    $result["message"] = "Vui lòng nhận quà sau ít phút";
            //}
            //else{
            $reward_details = $this->toptyvo->get_reward_details_premiership($tournament_id);
            if (count($reward_details) > 0) {
                if (!$this->getSession($mobo_service_id, $server_id)) {
                    $result["code"] = "-1";
                    $result["message"] = "Bạn đang đăng nhập trên một máy khác.";
                } else {
                    //Check Exist            
                    if ($this->toptyvo->check_exist_exchange_gift_premiership($tournament_id, $server_id, $char_id)) {
                        $result["code"] = "-1";
                        $result["message"] = "Bạn đã nhận quà Top Ngoại Hạng giải đấu này rồi !";
                    } else {
                        //Check Receive Gift Date
                        $get_tournament_details = $this->toptyvo->get_tournament_details($tournament_id);
                        $date_now = date('Y-m-d H:i:s');

                        $tournament_date_start_reward = date('Y-m-d H:i:s', strtotime($get_tournament_details[0]["tournament_date_start_reward"]));
                        $tournament_date_end_reward = date('Y-m-d H:i:s', strtotime($get_tournament_details[0]["tournament_date_end_reward"]));

                        if (!($server_id == 2 && $char_id == "750000045") && strtotime($date_now) < strtotime($tournament_date_start_reward)) {
                            $result["code"] = "-1";
                            $result["message"] = "Chưa đến thời gian nhận quà !";
                        } else
                        if (!($server_id == 2 && $char_id == "750000045") && strtotime($date_now) > strtotime($tournament_date_end_reward)) {
                            $result["code"] = "-1";
                            $result["message"] = "Thời gian nhận quà đã kết thúc !";
                        } else {
//                            if (in_array($server_id, $this->server_merge)) {
//                                $server_id_merge = 3;
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                            } else
//                            if (in_array($server_id, $this->server_merge2)) {
//                                $server_id_merge = 1;
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                            }
////                                    else
////                                        if(in_array($server_id, $this->server_merge3))
////                                        {
////                                            $server_id_merge = 5;
////                                            $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
////                                        }
//                            else
//                            if (in_array($server_id, $this->server_merge4)) {
//                                $server_id_merge = 7;
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                            } else
//                            if (in_array($server_id, $this->server_merge5)) {
//                                $server_id_merge = 11;
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                            } else
//                            if (in_array($server_id, $this->server_merge6)) {
//                                $server_id_merge = 17;
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                            } else
//                            if (in_array($server_id, $this->server_merge7)) {
//                                $server_id_merge = 21;
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                            } else
//                            if (in_array($server_id, $this->server_merge8)) {
//                                $server_id_merge = 27;
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                            } else
//                            if (in_array($server_id, $this->server_merge9)) {
//                                $server_id_merge = 33;
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                            } else
//                            if (in_array($server_id, $this->server_merge10)) {
//                                $server_id_merge = 39;
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                            } else
//                            if (in_array($server_id, $this->server_merge11)) {
//                                $server_id_merge = 43;
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);
//                            } else {
//                                $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id, $char_id);
//                            }

                            $server_id_merge = $this->get_server_id_merge($server_id);
                            $userpoint = $this->toptyvo->get_pk_point_new($get_tournament_details[0]["tournament_store_proc"], $server_id_merge, $char_id);

                            if (!($server_id == 2 && $char_id == "750000045") && count($userpoint) == 0) {
                                $result["code"] = "-1";
                                $result["message"] = "Hạng của bạn không đủ để nhận quà!*";
                            } else {
                                if (!($server_id == 2 && $char_id == "750000045") && $userpoint[0]["CURRPOINT"] == 0) {
                                    $result["code"] = "-1";
                                    $result["message"] = "Hạng của bạn không đủ để nhận quà!**";
                                } else {
                                    //if($server_id == 2 && $char_id == "750000045"){
                                    //    $char_id = "750435344";
                                    //}
                                    //Check Top User Valid      
                                    $data_top_premiership = $this->toptyvo->get_top($get_tournament_details[0]["tournament_store_proc_top_premiership"]);

                                    foreach ($data_top_premiership as $key => $value) {
                                        if ($char_id == $value['UID']) {
                                            //Add exchange History
                                            $userdata_p["char_id"] = $char_id;
                                            $userdata_p["server_id"] = $server_id;
                                            $userdata_p["char_name"] = $char_name;
                                            $userdata_p["mobo_service_id"] = $mobo_service_id;
                                            $userdata_p["reward_id"] = $reward_details[0]["id"];
                                            $userdata_p["exchange_date"] = Date('Y-m-d H:i:s');
                                            $userdata_p["tournament_id"] = $tournament_id;

                                            $i_id = $this->toptyvo->insert_id("event_toppk_exchange_history_premiership", $userdata_p);

                                            if ($i_id > 0) {
                                                //SEND Item
                                                $info['mobo_service_id'] = $datadecode["mobo_service_id"];
                                                $info['character_id'] = $userdata["character_id"];
                                                $info['server_id'] = $userdata["server_id"];
                                                $info['character_name'] = $userdata["character_name"];

                                                $item1 = null;

                                                if ($reward_details[0]["reward_item1_code"] != 0 && $reward_details[0]["reward_item1_number"] != 0) {
                                                    $item1[] = array("item_id" => (int) $reward_details[0]["reward_item1_code"], "count" => (int) $reward_details[0]["reward_item1_number"]);
                                                }
                                                if ($reward_details[0]["reward_item2_code"] != 0 && $reward_details[0]["reward_item2_number"] != 0) {
                                                    $item1[] = array("item_id" => (int) $reward_details[0]["reward_item2_code"], "count" => (int) $reward_details[0]["reward_item2_number"]);
                                                }
                                                if ($reward_details[0]["reward_item3_code"] != 0 && $reward_details[0]["reward_item3_number"] != 0) {
                                                    $item1[] = array("item_id" => (int) $reward_details[0]["reward_item3_code"], "count" => (int) $reward_details[0]["reward_item3_number"]);
                                                }
                                                if ($reward_details[0]["reward_item4_code"] != 0 && $reward_details[0]["reward_item4_number"] != 0) {
                                                    $item1[] = array("item_id" => (int) $reward_details[0]["reward_item4_code"], "count" => (int) $reward_details[0]["reward_item4_number"]);
                                                }
                                                if ($reward_details[0]["reward_item5_code"] != 0 && $reward_details[0]["reward_item5_number"] != 0) {
                                                    $item1[] = array("item_id" => (int) $reward_details[0]["reward_item5_code"], "count" => (int) $reward_details[0]["reward_item5_number"]);
                                                }

                                                $data_result = $this->senditemforgame($info, $item1, "Chúc mừng bạn nhận được quà", "Quà Top Ngoại Hạng Tỷ Võ Trạng Nguyên");
                                                $this->toptyvo->update_exchange_history_premiership($i_id, json_encode($item1), json_encode($data_result));

                                                $result["code"] = "0";
                                                $result["message"] = "Nhận quà Top Ngoại Hạng thành công!";
                                            } else {
                                                $result["code"] = "-1";
                                                $result["message"] = "Nhận quà Top thất bại, vui lòng thử lại!";
                                            }
                                        } else {
                                            $result["code"] = "-1";
                                            $result["message"] = "Hạng của bạn không đủ để nhận quà!***";
                                        }
                                    }

                                    //if(!in_array(array('UID', $char_id), $data_top_premiership, true)){
                                    //    $result["code"] = "-1";
                                    //    $result["message"] = "Hạng của bạn không đủ để nhận quà!***";
                                    //}
                                    //else{
                                    //}
                                }
                            }
                        }
                    }
                }
            } else {
                $result["code"] = "-1";
                $result["message"] = "Thông tin quà không chính xác!";
            }
            //}
        }

        $this->output->set_output(json_encode($result));
    }

    function get_top() {
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

        //if($mobo_service_id != "1061495878891844701" && $mobo_service_id != "1061498602823097731"
        //&& $mobo_id != "671456185" && $mobo_id != "485372761" && $mobo_id != "247165485" && $mobo_id != "853017650" && $mobo_id != "477409422" 
        //&& $mobo_id != "857316426" && $mobo_id != "666629660" && $mobo_id != "886899541"  && $mobo_id != "128147013"){
        //    unset($_SESSION["oauthtoken"]);
        //    echo "Tính năng đang cập nhật, bạn vui lòng quay lại sau.![***]";//$this->load->view("deny", "", true);
        //    exit();
        //}
        //Get Tournament List 
        $id = $_GET["id"];
        $server_id_s = $_GET["server_id"];
        $this->data["server_id"] = $server_id_s;

        $tournament_list = $this->toptyvo->get_tournament_details($id);
        $this->data["tournament"] = $tournament_list;
        if (count($tournament_list) == 0) {
            echo "Không có giải đấu...";
            die;
        }

//        if (in_array($server_id, $this->server_merge)) {
//            $server_id_merge = 3;
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
//        } else
//        if (in_array($server_id, $this->server_merge2)) {
//            $server_id_merge = 1;
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
//        }
////            else
////                if(in_array($server_id, $this->server_merge3))
////                {
////                    $server_id_merge = 5;
////                    $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
////                }
//        else
//        if (in_array($server_id, $this->server_merge4)) {
//            $server_id_merge = 7;
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
//        } else
//        if (in_array($server_id, $this->server_merge5)) {
//            $server_id_merge = 11;
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
//        } else
//        if (in_array($server_id, $this->server_merge6)) {
//            $server_id_merge = 17;
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
//        } else
//        if (in_array($server_id, $this->server_merge7)) {
//            $server_id_merge = 21;
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
//        } else
//        if (in_array($server_id, $this->server_merge8)) {
//            $server_id_merge = 27;
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
//        } else
//        if (in_array($server_id, $this->server_merge9)) {
//            $server_id_merge = 33;
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
//        } else
//        if (in_array($server_id, $this->server_merge10)) {
//            $server_id_merge = 39;
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
//        } else
//        if (in_array($server_id, $this->server_merge11)) {
//            $server_id_merge = 43;
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
//        } else {
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id, $char_id);
//        }

        $server_id_merge = $this->get_server_id_merge($server_id);
        $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);

        if (empty($userpoint) || count($userpoint) == 0) {
            $this->data["user_point"] = "0";
            $this->data["user_rank"] = "0";
            $this->data["user_attackwin"] = "0";
            $this->data["user_attacklose"] = "0";
            $this->data["user_defendlose"] = "0";
            $this->data["user_defendwin"] = "0";
        } else {
            //if($server_id == 28 && $char_id == "750733994"){
            //    $this->data["user_point"] = 20000;
            //}
            //else{
            if ($userpoint[0]["CURRPOINT"] == "") {
                $this->data["user_point"] = "0";
            } else {
                $this->data["user_point"] = $userpoint[0]["CURRPOINT"];
            }
            //}

            if ($userpoint[0]["RANK"] == "") {
                $this->data["user_rank"] = "0";
            } else {
                $this->data["user_rank"] = $userpoint[0]["RANK"];
            }

            if ($userpoint[0]["ATTACKWIN"] == "") {
                $this->data["user_attackwin"] = "0";
            } else {
                $this->data["user_attackwin"] = $userpoint[0]["ATTACKWIN"];
            }

            if ($userpoint[0]["ATTACKLOSE"] == "") {
                $this->data["user_attacklose"] = "0";
            } else {
                $this->data["user_attacklose"] = $userpoint[0]["ATTACKLOSE"];
            }

            if ($userpoint[0]["DEFENDLOSE"] == "") {
                $this->data["user_defendlose"] = "0";
            } else {
                $this->data["user_defendlose"] = $userpoint[0]["DEFENDLOSE"];
            }

            if ($userpoint[0]["DEFENDWIN"] == "") {
                $this->data["user_defendwin"] = "0";
            } else {
                $this->data["user_defendwin"] = $userpoint[0]["DEFENDWIN"];
            }
        }

        //Get TOP
        if ($server_id_s == 0) {
            $data["get_top"] = $this->toptyvo->get_top($tournament_list[0]["tournament_store_proc_top"]);
            $this->data["get_top"] = $data["get_top"];
        } else {
            $data["get_top_server"] = $this->toptyvo->get_top_server($tournament_list[0]["tournament_store_proc_top_server"], $server_id_s);
            $this->data["get_top_server"] = $data["get_top_server"];
        }

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("events/toptyvo_new/top", $this->data, true);
    }

    function get_top_premiership() {
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
        //Get Tournament List 
        $id = $_GET["id"];

        $tournament_list = $this->toptyvo->get_tournament_details($id);
        $this->data["tournament"] = $tournament_list;
        if (count($tournament_list) == 0) {
            echo "Không có giải đấu...";
            die;
        }

//        if (in_array($server_id, $this->server_merge)) {
//            $server_id_merge = 3;
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
//        } else
//        if (in_array($server_id, $this->server_merge2)) {
//            $server_id_merge = 1;
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
//        }
////            else
////                if(in_array($server_id, $this->server_merge3))
////                {
////                    $server_id_merge = 5;
////                    $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
////                }
//        else
//        if (in_array($server_id, $this->server_merge4)) {
//            $server_id_merge = 7;
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
//        } else
//        if (in_array($server_id, $this->server_merge5)) {
//            $server_id_merge = 11;
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
//        } else
//        if (in_array($server_id, $this->server_merge6)) {
//            $server_id_merge = 17;
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
//        } else
//        if (in_array($server_id, $this->server_merge7)) {
//            $server_id_merge = 21;
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
//        } else
//        if (in_array($server_id, $this->server_merge8)) {
//            $server_id_merge = 27;
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
//        } else
//        if (in_array($server_id, $this->server_merge9)) {
//            $server_id_merge = 33;
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
//        } else
//        if (in_array($server_id, $this->server_merge10)) {
//            $server_id_merge = 39;
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
//        } else
//        if (in_array($server_id, $this->server_merge11)) {
//            $server_id_merge = 43;
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);
//        } else {
//            $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id, $char_id);
//        }

        $server_id_merge = $this->get_server_id_merge($server_id);
        $userpoint = $this->toptyvo->get_pk_point_new($tournament_list[0]["tournament_store_proc"], $server_id_merge, $char_id);

        if (empty($userpoint) || count($userpoint) == 0) {
            $this->data["user_point"] = "0";
            $this->data["user_rank"] = "0";
            $this->data["user_attackwin"] = "0";
            $this->data["user_attacklose"] = "0";
            $this->data["user_defendlose"] = "0";
            $this->data["user_defendwin"] = "0";
        } else {
            //if($server_id == 28 && $char_id == "750733994"){
            //    $this->data["user_point"] = 20000;
            //}
            //else{
            if ($userpoint[0]["CURRPOINT"] == "") {
                $this->data["user_point"] = "0";
            } else {
                $this->data["user_point"] = $userpoint[0]["CURRPOINT"];
            }
            //}

            if ($userpoint[0]["RANK"] == "") {
                $this->data["user_rank"] = "0";
            } else {
                $this->data["user_rank"] = $userpoint[0]["RANK"];
            }

            if ($userpoint[0]["ATTACKWIN"] == "") {
                $this->data["user_attackwin"] = "0";
            } else {
                $this->data["user_attackwin"] = $userpoint[0]["ATTACKWIN"];
            }

            if ($userpoint[0]["ATTACKLOSE"] == "") {
                $this->data["user_attacklose"] = "0";
            } else {
                $this->data["user_attacklose"] = $userpoint[0]["ATTACKLOSE"];
            }

            if ($userpoint[0]["DEFENDLOSE"] == "") {
                $this->data["user_defendlose"] = "0";
            } else {
                $this->data["user_defendlose"] = $userpoint[0]["DEFENDLOSE"];
            }

            if ($userpoint[0]["DEFENDWIN"] == "") {
                $this->data["user_defendwin"] = "0";
            } else {
                $this->data["user_defendwin"] = $userpoint[0]["DEFENDWIN"];
            }
        }

        //Get TOP
        if ($id >= 12) {
            $data["data_top_premiership"] = $this->toptyvo->get_top($tournament_list[0]["tournament_store_proc_top_premiership"]);
            $this->data["data_top_premiership"] = $data["data_top_premiership"];
        } else {
            $this->data["data_top_premiership"] = null;
        }

        //Get Reward Premiership
        $data["data_reward_premiership"] = $this->toptyvo->get_reward_details_premiership($id);
        $this->data["data_reward_premiership"] = $data["data_reward_premiership"];

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("events/toptyvo_new/top_premiership", $this->data, true);
    }

    function get_top_allserver() {
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
        //Get Tournament List 
        $id = $_GET["id"];

        $tournament_list = $this->toptyvo->get_tournament_details($id);
        $this->data["tournament"] = $tournament_list;
        if (count($tournament_list) == 0) {
            echo "Không có giải đấu...";
            die;
        }

        //Get TOP
        if ($id >= 14) {
            $data["data_top_all_server"] = $this->toptyvo->get_top($tournament_list[0]["tournament_store_proc_top_all_server"]);
            $this->data["data_top_all_server"] = $data["data_top_all_server"];
        } else {
            //$this->data["data_top_all_server"] = null;
        }

        //Get Reward Premiership
        //$data["data_reward_premiership"] = $this->toptyvo->get_reward_details_premiership($id);
        //$this->data["data_reward_premiership"] = $data["data_reward_premiership"]; 

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("events/toptyvo_new/top_all_server", $this->data, true);
    }

    function tournament_list_ex() {
        //Get Tournament for Filter IP and Server ID allow
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

        $tournament_list = $this->toptyvo->tournament_list();

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
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"]));
                }
            }

            if ($server_list != "" && $ip_list == "") {
                $server_list = explode(";", $server_list);
                if (in_array($server_id, $server_list)) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"]));
                }
            }

            if ($server_list == "" && $ip_list == "") {
                array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                    "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                    , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"]));
            }

            if ($server_list != "" && $ip_list != "") {
                $server_list = explode(";", $server_list);
                $ip_list = explode(";", $ip_list);
                if (in_array($server_id, $server_list) && (in_array($client_ip, $ip_list))) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"]));
                }
            }
        }

        $this->output->set_output(json_encode($tournament_filter));
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
    
    //Get Top New
    function get_top_arena() {
        if (empty($_SESSION['user_info'])) {
            echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            die;
        }

        $user = unserialize($_SESSION['user_info']);      
        $this->data["user"] = $user;

        $char_id = $user->character_id;
        $server_id = $user->server_id;
        $char_name = $user->character_name;
        $mobo_service_id = $user->mobo_service_id;
        $mobo_id = $user->mobo_id;
      
        //Get Tournament List 
        $id = $_GET["id"];        

        $tournament_list = $this->toptyvo->get_tournament_details($id);
        $this->data["tournament"] = $tournament_list;
        if (count($tournament_list) == 0) {
            echo "Không có giải đấu...";
            die;
        }
       
        //Get TOP      
        $this->data["tournament_id"] = $id;
        $this->data["TopArena"] = $this->toptyvo->Event_TopArena_GetList($server_id, (int)$tournament_list[0]["week_no"]);       
        
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("events/toptyvo_new/top", $this->data, true);
    }
    
    function get_top_battle() {
        if (empty($_SESSION['user_info'])) {
            echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            die;
        }

        $user = unserialize($_SESSION['user_info']);      
        $this->data["user"] = $user;

        $char_id = $user->character_id;
        $server_id = $user->server_id;
        $char_name = $user->character_name;
        $mobo_service_id = $user->mobo_service_id;
        $mobo_id = $user->mobo_id;
      
        //Get Tournament List 
        $id = $_GET["id"];        

        $tournament_list = $this->toptyvo->get_tournament_details($id);
        $this->data["tournament"] = $tournament_list;
        if (count($tournament_list) == 0) {
            echo "Không có giải đấu...";
            die;
        }
       
        //Get TOP      
        $this->data["tournament_id"] = $id;
        $this->data["TopBattle"] = $this->toptyvo->Event_TopBattlePoint_GetList($server_id, (int)$tournament_list[0]["week_no"]);       
        
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("events/toptyvo_new/top_battle", $this->data, true);
    }
    
     //Get Percent Top
    public function get_top_percent($reward_rank, $tournament_id){
        //return 11;
        if (empty($_SESSION['user_info'])) {
            return 0;
        }

        $user = unserialize($_SESSION['user_info']);      
        $this->data["user"] = $user;   
        $server_id = $user->server_id; 
        
        $reward_details_top = $this->toptyvo->check_rank_valid($reward_rank, $tournament_id);
        //var_dump($reward_details_top); die;
        
        if(count($reward_details_top) > 0 && $reward_details_top[0]["reward_percent"] > 0){
            $tournament_details = $this->toptyvo->get_tournament_details($tournament_id);
            if(count($reward_details_top) > 0){
                $api = new GameFullAPI();
                $getmoney_api = $api->get_money($this->service_name, NULL, $server_id, $tournament_details[0]["tournament_date_start"], $tournament_details[0]["tournament_date_end"], 2);
                
                $percent_server = ($getmoney_api["amount"] / 100) * $tournament_details[0]["reward_percent"];
                $point_bonus = ($percent_server / 100) * $reward_details_top[0]["reward_percent"];
                return floor($point_bonus/100);
            }
            else{
                return -1;
            }
            //$bonus = 
        }
        else{
            return -2;
        }
    }
    
    public function get_premier_percent($reward_rank, $tournament_id){
        //return 11;
        if (empty($_SESSION['user_info'])) {
            return 0;
        }

        $user = unserialize($_SESSION['user_info']);      
        $this->data["user"] = $user;   
        $server_id = $user->server_id; 
        
        $reward_details_top = $this->toptyvo->check_rank_premier_valid($reward_rank, $tournament_id);
        //var_dump($reward_details_top); die;
        
        if(count($reward_details_top) > 0 && $reward_details_top[0]["reward_percent"] > 0){
            $tournament_details = $this->toptyvo->get_tournament_details($tournament_id);
            if(count($reward_details_top) > 0){
                $api = new GameFullAPI();
                $getmoney_api = $api->get_money($this->service_name, NULL, $server_id, $tournament_details[0]["tournament_date_start"], $tournament_details[0]["tournament_date_end"], 2);
                
                $percent_server = ($getmoney_api["amount"] / 100) * $tournament_details[0]["reward_percent"];
                $point_bonus = ($percent_server / 100) * $reward_details_top[0]["reward_percent"];
                return floor($point_bonus/100);
            }
            else{
                return -1;
            }
            //$bonus = 
        }
        else{
            return -2;
        }
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
