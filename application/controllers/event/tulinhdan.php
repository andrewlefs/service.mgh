<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
if (empty($_SESSION))
    session_start();
require_once APPPATH . "core/EI_Controller.php";

class tulinhdan extends EI_Controller {

   private $mobo_id_test = array("671456185", "485372761", "247165485", "853017650",
        "477409422", "857316426", "666629660", "886899541", "128147013", "260896396", "139416976");

    private $definerechare = 5;

    public function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $this->load->library('GameFullAPI');
        $this->load->helper('url');
        $this->load->model('events/m_tulinhdan', "tulinhdan", false);
        $this->data["event_name"] = "Kho Báu Tân Mộng Giang Hồ";

        $this->init_settings('events/tulinhdan');
    }

    public function index() {
        //create oauth token
        if ($this->verify_uri() != true) {
            //$this->data["message"] = "Bạn không thể truy cập sự kiện này.![1]";
            //$this->render("deny", $this->data);
        }

        $user = $this->get_info();
        $_SESSION['linkinfo'] = $_GET;
        $_SESSION['user_info'] = serialize($user);

        $this->data['user'] = $user;

        //Check join Game
        if ($user->character_id == "") {
            $this->data["message"] = "Vui lòng vào game trước khi tham gia sự kiện...";
            $this->render("deny", $this->data);
        }
        //Set Session
        $this->storeSession($user->mobo_service_id, $user->server_id);

        //Non public
        if ($this->is_test && !in_array($user->mobo_id, $this->mobo_id_test)) {
            $this->data["message"] = "Bạn không có quyền truy cập sự kiện này";
            $this->render("deny", $this->data);
        }


        $this->data["content_id"] = 5410;

        $this->render("index", $this->data);
    }

    public function thamgia() {

        if (empty($_SESSION['user_info'])) {
            echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            die;
            //$this->render("deny", $this->data);
        }

        $user = unserialize($_SESSION['user_info']);

        $this->data['user'] = $user;

        //Check join Game
        if ($user->character_id == "") {
            echo "Vui lòng vào game trước khi tham gia sự kiện...";
            die;
            //$this->render("deny", $this->data);
        }

        //Set Session
        $this->storeSession($user->mobo_service_id, $user->server_id);

        //Non public
        if ($this->is_test && !in_array($user->mobo_id, $this->mobo_id_test)) {
            echo "Bạn không có quyền truy cập sự kiện này";
            die;
            //$this->render("deny", $this->data);
        }

        //Get Tournament List 
        $id = $_GET["id"];
        $tournament_list = $this->tulinhdan->get_tournament_details($id);
        if (count($tournament_list) == 0) {
            echo "Không có giải đấu...";
            die;
            //$this->render("deny", $this->data);
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
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"], "tournament_point" => $value["tournament_point"]));
                }
            }

            if ($server_list != "" && $ip_list == "") {
                $server_list = explode(";", $server_list);
                if (in_array($user->server_id, $server_list)) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"], "tournament_point" => $value["tournament_point"]));
                }
            }

            if ($server_list == "" && $ip_list == "") {
                array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                    "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                    , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"], "tournament_point" => $value["tournament_point"]));
            }

            if ($server_list != "" && $ip_list != "") {
                $server_list = explode(";", $server_list);
                $ip_list = explode(";", $ip_list);
                if (in_array($user->server_id, $server_list) && (in_array($client_ip, $ip_list))) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_store_proc" => $value["tournament_store_proc"], "tournament_point" => $value["tournament_point"]));
                }
            }
        }

        $tournament = $tournament_filter;
        if (!empty($tournament) && count($tournament) > 0) {
            foreach ($tournament as $key => $value) {
                $this->data["tournament"] = $tournament;
                $this->data["tournament_id"] = $value["id"];
            }
        }

        //User Point
        $this->data["user_point"] = $this->user_point($user->character_id, $user->character_name, $user->server_id, $user->mobo_service_id, $user->mobo_id, $id);

        //Gift List
        $gift_list = $this->tulinhdan->get_gif_list($id);
        $this->data['point_jackpot'] = $this->tulinhdan->get_nohu();
        $this->data["gift_list"] = $gift_list;

        //NganLuong
        $getUser = $this->validate_user($user);

        if( $getUser == FALSE){
            echo "Hiện tại bạn chưa thể tham gia sự kiện này, vui lòng quay lại vào lúc khác";
            die;
            //$this->render("deny", $this->data);
        }

        $this->data["user_nl"] = $getUser['user_point'];

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        //echo $this->load->view("events/tulinhdan/thamgia", $this->data, true);
        $this->render("thamgia", $this->data);
    }

    function get_exchange_history() {

        if (empty($_SESSION['user_info'])) {
            echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            die;
            //$this->render("deny", $this->data);
        }

        $user = unserialize($_SESSION['user_info']);

        $this->data['user'] = $user;

        $tournament_id = $_GET["id"];

        $data["history"] = $this->tulinhdan->get_exchange_history_new($tournament_id, $user->server_id, $user->mobo_service_id);
        $this->data["history"] = $data["history"];

        $data["history_top"] = $this->tulinhdan->get_exchange_history_new_top($tournament_id, $user->server_id, $user->mobo_service_id);
        $this->data["history_top"] = $data["history_top"];        

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("events/tulinhdan/history", $this->data, true);
    }

    public function play_now() {
        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 5) {
            $result["code"] = "-1";
            $result["message"] = "Mỗi lần tích lũy phải cách nhau 5 giây.";
        } else {
            $_SESSION["execute_time"] = time();

            if (empty($_SESSION['user_info'])) {
                echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
                die;
                //$this->render("deny", $this->data);
            }

            $user = unserialize($_SESSION['user_info']);

            $this->data['user'] = $user;

            //User Data

            $userdata_p["char_id"] = $user->character_id;
            $userdata_p["server_id"] = $user->server_id;
            $userdata_p["char_name"] = $user->character_name;
            $userdata_p["mobo_service_id"] = $user->mobo_service_id;

            $id = $_GET["id"];

            if (!$this->getSession($user->mobo_service_id, $user->server_id)) {
                $result["code"] = "-1";
                $result["message"] = "Bạn đang đăng nhập trên một máy khác.";
            } else {
                //Check Tournament
                $get_tournament_details = $this->tulinhdan->get_tournament_details($id);

                if (count($get_tournament_details) == 0) {
                    $result["code"] = "-1";
                    $result["message"] = "Thông tin giải đấu không chính xác !";
                    $this->output->set_output(json_encode($result));
                    return;
                }

                $date_now = date('Y-m-d H:i:s');
                $tournament_date_start = date('Y-m-d H:i:s', strtotime($get_tournament_details[0]["tournament_date_start"]));
                $tournament_date_end = date('Y-m-d H:i:s', strtotime($get_tournament_details[0]["tournament_date_end"]));


                if (strtotime($date_now) < strtotime($tournament_date_start)) {
                    $result["code"] = "-1";
                    $result["message"] = "Giải đấu chưa bắt đầu !";
                } else
                if (strtotime($date_now) > strtotime($tournament_date_end)) {
                    $result["code"] = "-1";
                    $result["message"] = "Giải đấu đã kết thúc !";
                } else {
                    //Check NL Valid
                    $getUser = $this->validate_user($user);

                    if( $getUser == FALSE){
                        $result["code"] = "-1";
                        $result["message"] = "Thông tin người dùng không chính xác !";
                        $this->output->set_output(json_encode($result));
                        return;
                        //$this->render("deny", $this->data);
                    }

                    $valid_nl = $getUser['user_point'];


                    if ($valid_nl < $get_tournament_details[0]["tournament_point"] || $valid_nl == "") {
                        $result["code"] = "-1";
                        $result["message"] = "Ngân lượng không đủ để tích lũy !";
                    } else {

                        $transaction_id = $user->character_id.time();
                        //Add exchange History
                        $userdata_p["char_id"] = $user->character_id;
                        $userdata_p["server_id"] = $user->server_id;
                        $userdata_p["char_name"] = $user->character_name;
                        $userdata_p["mobo_service_id"] = $user->mobo_service_id;
                        //$userdata_p["reward_id"] = $id;
                        $userdata_p["exchange_date"] = Date('Y-m-d H:i:s');
                        $userdata_p["tournament_id"] = $id;
                        $userdata_p["exchange_point"] = $get_tournament_details[0]["tournament_point"];
                        $userdata_p['transaction_id'] = $transaction_id;

                        $i_id = $this->tulinhdan->insert_id("event_tulinhdan_exchange_history", $userdata_p);

                        if ($i_id > 0) {
                            //insert nohu
                            //$definerechare = ($get_tournament_details[0]["tournament_point"] * $this->definerechare);
                            $definerechare = $this->definerechare;
                            if($this->tulinhdan->update_nohu($definerechare) == FALSE){
                                $result["code"] = "-1";
                                $result["message"] = "Tích lũy thất bại, vui lòng thử lại !*";
                                $this->output->set_output(json_encode($result));
                                return;
                            }

                            //Update NL
                            //update
                            $params_minute = array(
                                "nl_quantity"=>$get_tournament_details[0]["tournament_point"],
                                "transaction_id"=>"sub_".$transaction_id,
                                "id"=>$i_id
                            );

                            if ($this->minuteNL($user,$params_minute) == FALSE) {
                                $result["code"] = "-1";
                                $result["message"] = "Tích lũy thất bại, vui lòng thử lại !**";
                                $this->output->set_output(json_encode($result));
                                return;
                            }

                            //Update Point
                            $this->tulinhdan->update_point($user->mobo_service_id, $get_tournament_details[0]["tournament_point"], $id);

                            //Get Gift List
                            $getItem = $this->tulinhdan->get_gif_list($id);

                            if (count($getItem) < 1) {
                                $result["code"] = "-1";
                                $result["message"] = "Thông tin quà không chính xác !";
                                $this->output->set_output(json_encode($result));
                                return;
                            }

                            $total = 0;
                            $final_award = array();
                            //coutn total random
                            foreach ($getItem as $value) {
                                $total += (int) $value["gift_rate"];
                                $final_award[$value["id"]] = $value;
                            }
                            //load random item
                            $this->load->library('WeightedRandom');
                            $objRandom = new WeightedRandom();
                            $luckyKeyItem = $this->random_luck($objRandom, $final_award, $total);

                            if($luckyKeyItem['gift_type'] == 2){
                                $nohu = $this->tulinhdan->get_nohu();
                                if($nohu){
                                    $itemsend = array(
                                        "nl_quantity"=>floor($nohu['item_count']),
                                        "transaction_id"=>"add_".$transaction_id,
                                        "id"=>$i_id
                                    );
                                    $data_result = $this->addNL($user,$itemsend);
                                }


                                if($this->tulinhdan->update_nohu_reset() == FALSE){
                                    $result["code"] = "-1";
                                    $result["message"] = "Tích lũy thất bại, vui lòng thử lại !***";
                                    return;
                                }
                            }else{
                                $itemsend[] = array("item_id" => $luckyKeyItem['item_id'], 'count' => $luckyKeyItem['gift_quantity']);

                                //SEND Item
                                $info['mobo_service_id'] = $user->mobo_service_id;
                                $info['character_id'] = $user->character_id;
                                $info['server_id'] = $user->server_id;
                                $info['character_name'] = $user->character_name;

                                $data_result = $this->senditemforgame($info, $itemsend, "Chúc mừng bạn nhận được quà", "Chúc mừng bạn nhận quà");

                            }

                            $this->tulinhdan->update_exchange_history($i_id, json_encode($itemsend), json_encode($data_result), $luckyKeyItem['id'], $id);

                            $result["code"] = "0";
                            $result["message"] = "Tích lũy thành công, bạn nhận được '" . $luckyKeyItem['gift_name'] . "' <br /> <img src='" . $luckyKeyItem['gift_img'] . "' />";
                        } else {
                            $result["code"] = "-1";
                            $result["message"] = "Tích lũy thất bại, vui lòng thử lại !**";
                        }
                    }
                }
            }
        }

        $this->output->set_output(json_encode($result));
    }

    public function gift_top_exchange() {
        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 5) {
            $result["code"] = "-1";
            $result["message"] = "Mỗi lần đổi quà phải cách nhau 5 giây.";
        } else {
            $_SESSION["execute_time"] = time();

            if (empty($_SESSION['user_info'])) {
                echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
                die;
                //$this->render("deny", $this->data);
            }

            $user = unserialize($_SESSION['user_info']);

            $this->data['user'] = $user;

            //User Data

            $userdata_p["char_id"] = $user->character_id;
            $userdata_p["server_id"] = $user->server_id;
            $userdata_p["char_name"] = $user->character_name;
            $userdata_p["mobo_service_id"] = $user->mobo_service_id;

            $tournament_id = $_GET["id"];
            
            if (!$this->getSession($user->mobo_service_id, $user->server_id)) {
                $result["code"] = "-1";
                $result["message"] = "Bạn đang đăng nhập trên một máy khác.";
            } else {
                //Check Exist            
                if ($this->tulinhdan->check_exist_exchange_gift_top($tournament_id, $user->server_id, $user->mobo_service_id)) {
                    $result["code"] = "-1";
                    $result["message"] = "Bạn đã nhận quà Top giải đấu này rồi !";
                } else {
                    //Check Receive Gift Date
                    $get_tournament_details = $this->tulinhdan->get_tournament_details($tournament_id);
                    $date_now = date('Y-m-d H:i:s');

                    $tournament_date_start_reward = date('Y-m-d H:i:s', strtotime($get_tournament_details[0]["tournament_date_start_reward"]));
                    $tournament_date_end_reward = date('Y-m-d H:i:s', strtotime($get_tournament_details[0]["tournament_date_end_reward"]));

                    if (!($user->server_id == 28 && $user->character_id == "750733994") && strtotime($date_now) < strtotime($tournament_date_start_reward)) {
                        $result["code"] = "-1";
                        $result["message"] = "Chưa đến thời gian nhận quà !";
                    } else
                    if (!($user->server_id == 28 && $user->character_id == "750733994") && strtotime($date_now) > strtotime($tournament_date_end_reward)) {
                        $result["code"] = "-1";
                        $result["message"] = "Thời gian nhận quà đã kết thúc !";
                    } else {
                        //Check Rank Valid 
                        $userpoint = $this->tulinhdan->get_top_user($tournament_id, $user->server_id, $user->mobo_service_id);
                        
                        if (!($user->server_id == 28 && $user->char_id == "750733994") && count($userpoint) == 0) {
                            $result["code"] = "-1";
                            $result["message"] = "Hạng của bạn không đủ để nhận quà!*";
                        } else {

                            if ($user->server_id == 28 && $user->char_id == "750733994") {
                                $userpoint[0]["RANK"] = 85;
                            }

                            $reward_rank_valid = $this->tulinhdan->check_rank_valid($userpoint[0]["rank"], $tournament_id);

                            if (count($reward_rank_valid) == 0) {
                                $result["code"] = "-1";
                                $result["message"] = "Hạng của bạn không đủ để nhận quà!**";
                            } else {
                                //Add exchange History
                                $userdata_p["char_id"] = $user->character_id;
                                $userdata_p["server_id"] = $user->server_id;
                                $userdata_p["char_name"] = $user->character_name;
                                $userdata_p["mobo_service_id"] = $user->mobo_service_id;
                                $userdata_p["reward_id"] = $reward_rank_valid[0]["id"];
                                $userdata_p["exchange_date"] = Date('Y-m-d H:i:s');
                                $userdata_p["tournament_id"] = $tournament_id;

                                $i_id = $this->tulinhdan->insert_id("event_tulinhdan_exchange_history_top", $userdata_p);

                                if ($i_id > 0) {
                                    //SEND Item
                                    $info['mobo_service_id'] = $user->mobo_service_id;
                                    $info['character_id'] = $user->character_id;
                                    $info['server_id'] = $user->server_id;
                                    $info['character_name'] = $user->character_name;

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

                                    $data_result = $this->senditemforgame($info, $item1, "Chúc mừng bạn nhận được quà", "Quà Top ".$userpoint[0]["rank"]." Server Tụ Linh Đàn");
                                    $this->tulinhdan->update_exchange_history_top($i_id, json_encode($item1), json_encode($data_result));

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
        }

        $this->output->set_output(json_encode($result));
    }

    function get_top() {
        if (empty($_SESSION['user_info'])) {
            echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            die;
            //$this->render("deny", $this->data);
        }

        $user = unserialize($_SESSION['user_info']);

        $this->data['user'] = $user;
        
        //Get Tournament List 
        $id = $_GET["id"];
        $server_id_s = $_GET["server_id"];
        $this->data["server_id"] = $server_id_s;

        $tournament_list = $this->tulinhdan->get_tournament_details($id);
        $this->data["tournament"] = $tournament_list;
        if (count($tournament_list) == 0) {
            echo "Không có giải đấu...";
            die;
        }
        
        $tournament_top = $this->tulinhdan->get_top($id);
        $this->data["tournament_top"] = $tournament_top;
        //var_dump($tournament_top); die;
        
        //Get User
        $get_top_user = $this->tulinhdan->get_top_user($id, $user->server_id, $user->mobo_service_id);
        $this->data["user_point"] = $this->user_point($user->character_id, $user->character_name, $user->server_id, $user->mobo_service_id, $user->mobo_id, $id);
        
        if($get_top_user[0]["rank"] == null || $get_top_user[0]["rank"] == "" || $get_top_user[0]["rank"] > 50){
            $this->data["user_rank"] = "Lớn hơn 50";
        }
        else{
            $this->data["user_rank"] = $get_top_user[0]["rank"];
        }
        //var_dump($get_top_user); die;

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("events/tulinhdan/top", $this->data, true);
    }

    function tournament_list_ex() {
        //Get Tournament for Filter IP and Server ID allow

        if (empty($_SESSION['user_info'])) {
            echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            //$this->render("deny", $this->data);
        }

        $user = unserialize($_SESSION['user_info']);

        $this->data['user'] = $user;

        $tournament_list = $this->tulinhdan->tournament_list();

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
                if (in_array($user->server_id, $server_list)) {
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
                if (in_array($user->server_id, $server_list) && (in_array($client_ip, $ip_list))) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"]));
                }
            }
        }

        $this->output->set_output(json_encode($tournament_filter));
    }
    
    public function napthe() {

        if (empty($_SESSION['user_info'])) {
            echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            //$this->render("deny", $this->data);
        }

        $user = unserialize($_SESSION['user_info']);

        $this->data['user'] = $user;

        $id = $_GET["id"];

        //Check join Game
        if ($user->character_id == "" || $user->server_id == "") {
            echo "Vui lòng vào game trước khi tham gia sự kiện...";
            die;
        }

        //User Point
        $getUser = $this->validate_user($user);

        if( $getUser == FALSE){
            echo "Hiện tại bạn chưa thể tham gia sự kiện này, vui lòng quay lại vào lúc khác";
            die;
            //$this->render("deny", $this->data);
        }

        $this->data["user_nl"] = $getUser['user_point'];

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("events/tulinhdan/napthe", $this->data, true);
    }

    private function user_point($char_id, $char_name, $server_id, $mobo_service_id, $mobo_id, $tournament_id) {
        $datauser = $this->tulinhdan->user_check_point_exist($mobo_service_id, $tournament_id);
        if (count($datauser) > 0) {
            foreach ($datauser as $key => $value) {
                //Update Mobo Id
                if ($value["mobo_id"] == null || empty($value["mobo_id"])) {
                    $this->tulinhdan->update_tulinhdan_point_moboid($value["id"], $mobo_id);
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
            $userdata_p["update_date"] = Date('Y-m-d H:i:s');
            $userdata_p["user_point"] = 0;
            $userdata_p["tournament_id"] = $tournament_id;

            $this->tulinhdan->insert("event_tulinhdan_point", $userdata_p);

            return 0;
        }
    }

    private $url_nganluong = 'http://game.mobo.vn/mgh2/event/shopnganluong/';
    private $secret_key_nl = "UJ;yX3d+E%8!YVa/";
    private $event_key_nl = "mgh2_nohu";

    private function validate_user($user){
        $array = array(
            "char_id"=>$user->character_id,
            "server_id"=>$user->server_id,
            "mobo_service_id"=>$user->mobo_service_id,
            "mobo_id"=>$user->mobo_id,
            "char_name"=>$user->character_name,
        );
        $array['token'] = md5(implode("", $array) . $this->secret_key_nl);

        $url_callback = $this->url_nganluong."user_valid/?".http_build_query($array);

        $getValidate = $this->call_api_get($url_callback);
        if($getValidate){
            $parVali = json_decode($getValidate,true);
            if($parVali['result'] == 0){
                return $parVali;
            }
        }
        return false;
    }

    private function addNL($user,$params){
        $array = array(
            "char_id"=>$user->character_id,
            "server_id"=>$user->server_id,
            "mobo_service_id"=>$user->mobo_service_id,
            "mobo_id"=>$user->mobo_id,
            "char_name"=>$user->character_name,
            "nl_quantity"=>$params['nl_quantity'],
            "transaction_id"=>$params['transaction_id'],
            "event_key"=> $this->event_key_nl
        );

        $array['token'] = md5(implode("", $array) . $this->secret_key_nl);

        $url_callback = $this->url_nganluong."add_nl/?".http_build_query($array);

        $getValidate = $this->call_api_get($url_callback);

        if(!empty($params['id'])){
            $this->tulinhdan->update_exchange_history_log($params['id'], $getValidate,$url_callback);
        }

        if($getValidate){
            $parVali = json_decode($getValidate,true);
            if(!empty($parVali) && $parVali['result'] == 0){
                return true;
            }
        }
        return false;

    }
    private function minuteNL($user,$params){
        $array = array(
            "char_id"=>$user->character_id,
            "server_id"=>$user->server_id,
            "mobo_service_id"=>$user->mobo_service_id,
            "mobo_id"=>$user->mobo_id,
            "char_name"=>$user->character_name,
            "nl_quantity"=>$params['nl_quantity'],
            "transaction_id"=>$params['transaction_id'],
            "event_key"=> $this->event_key_nl
        );

        $array['token'] = md5(implode("", $array) . $this->secret_key_nl);

        $url_callback = $this->url_nganluong."minus_nl/?".http_build_query($array);

        $getValidate = $this->call_api_get($url_callback);

        if(!empty($params['id'])){
            $this->tulinhdan->update_exchange_history_log($params['id'], $getValidate,$url_callback);
        }

        if($getValidate){
            $parVali = json_decode($getValidate,true);
            if(!empty($parVali) && $parVali['result'] == 0){
                return true;
            }
        }
        return false;

    }
    private function user_nl($char_id, $char_name, $server_id, $mobo_service_id, $mobo_id) {
        $datauser = $this->tulinhdan->user_check_nl_exist($mobo_service_id);
        if (count($datauser) > 0) {
            foreach ($datauser as $key => $value) {
                //Update Mobo Id
                if ($value["mobo_id"] == null || empty($value["mobo_id"])) {
                    $this->tulinhdan->update_dautruong_nl_moboid($value["id"], $mobo_id);
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

            $this->tulinhdan->insert("event_dautruong_point", $userdata_p);

            return 0;
        }
    }

    function checksign($params) {
        $token = trim($params['token']);
        unset($params['token']);
        $valid = md5(implode('', $params) . $this->config->item("oauth_key"));
        $_SESSION["oauthtoken"] = base64_encode(json_encode($params));
        $_SESSION["redirect"] = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        if ($valid != $token) {
            return false;
        }
        return true;
    }

    function senditemforgame($data, $item, $title, $content) {
        if (empty($data) || empty($item)) {
            return false;
        }
        //load thu vien chung
        $api = new GameFullAPI();
        //$getitem[] = $item;
        $addditem = $api->add_item_result($this->service_name,$data["mobo_service_id"], $data["server_id"], $item, $title, $content);
        return $addditem;
    }

    function random_luck($objRandom, $final_award, $total) {
        $keyItem = array();
        $weightItem = array();
        foreach ($final_award as $key => $value) {
            $current_mount = $value["gift_rate"];
            $percent = ($current_mount / $total) * 1000000;
            array_push($keyItem, $value);
            array_push($weightItem, $percent);
        }
// randomize with weighted
        return $objRandom->weighted_random($keyItem, $weightItem);
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

    function extractparam() {
        $params = $_SESSION['linkinfo'];
        $datadecode = json_decode(base64_decode($params["access_token"]), true);
        $userdata = json_decode($params["info"], true);
        $character_id = $userdata["character_id"];
        $character_name = $userdata["character_name"];
        $server_id = $userdata["server_id"];
        $mobo_service_id = $datadecode["mobo_service_id"];
        return array('mobo_service_id' => $mobo_service_id, 'server_id' => $server_id, 'character_id' => $character_id, 'character_name' => $character_name);
    }

    function getinfogame($params) {
        $gamer = array();
        $info = $this->extractparam();
        if (!empty($info['mobo_service_id']) && !empty($info['server_id'])) {
            $api = new MGH_API();
            $getuserinfo = $api->get_user_info($info['mobo_service_id'], $info['server_id']);
            if ($getuserinfo['code'] == 0 && count($getuserinfo['data']) >= 1) {
                return $gamer = $_SESSION['gamer'] = $getuserinfo['data']['data'];
            }
        }
        return $gamer;
    }

}
