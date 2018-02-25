<?php

require_once APPPATH . 'third_party/MeAPI/Autoloader.php';
require_once APPPATH . '/core/EI_Controller.php';

class quanhapmong extends EI_Controller {

    public $transaction_id;
    private $mobo_id_test = array("552397949", "364853453");
    private $is_test = true;
    private $_api_url = 'https://graph.mobo.vn/';
    protected $_key = "QEOODZHBTPE6ZJI7";
    private $_control = 'inside';
    private $_getinfo_func = 'search_graph';
    private $_app = 'skylight';
    protected $secret_key = "Kq@^P8dkr2%!ycq?48Kj";

    public function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $this->load->library('GameFullAPI');
        $this->load->model('event/m_quanhapmong', "quanhapmong", false);

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

        //Set Session
        $this->storeSession($mobo_service_id, $server_id);
        //Non public
        if ($this->is_test && !in_array($user->mobo_id, $this->mobo_id_test)) {
            echo "Bạn không có quyền truy cập sự kiện này";
            die;
        }

        //User Point
        $this->data["user_point"] = $this->user_point($char_id, $server_id, $mobo_service_id, $mobo_id, $char_name);
        echo $this->load->view("event/quanhapmong/index", $this->data, true);
    }

    //Gift
    public function gift_exchange() {
        echo "Truy cập không hợp lệ.";
        die;

        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 10) {
            $result["code"] = "-1";
            $result["message"] = "Mỗi lần đổi quà phải cách nhau 10 giây.";
        } else {
            $_SESSION["execute_time"] = time();
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
            $count = $_GET["count"];

            //if($mobo_service_id != "1061495878891844701" && $mobo_service_id != "1061498602823097731"
            //&& $mobo_id != "671456185" && $mobo_id != "485372761" && $mobo_id != "247165485" && $mobo_id != "853017650" && $mobo_id != "477409422" 
            //&& $mobo_id != "857316426" && $mobo_id != "666629660" && $mobo_id != "886899541"  && $mobo_id != "128147013"){
            //    $result["code"] = "-1";
            //    $result["message"] = "Hệ thống đang bảo trì, vui lòng đợi.";
            //}
            //else{
            //Check Reward Valid
            $reward_details = $this->quanhapmong->get_reward_details($id);
            if (count($reward_details) > 0) {
                if (!$this->getSession($mobo_service_id, $server_id)) {
                    $result["code"] = "-1";
                    $result["message"] = "Bạn đang đăng nhập trên một máy khác.";
                } else {
                    //Check Receive Gift Date
                    $get_tournament_details = $this->quanhapmong->get_tournament_details($reward_details[0]["tournament_id"]);
                    $date_now = date('Y-m-d H:i:s');

                    $tournament_date_start_reward = date('Y-m-d H:i:s', strtotime($get_tournament_details[0]["tournament_date_start"]));
                    $tournament_date_end_reward = date('Y-m-d H:i:s', strtotime($get_tournament_details[0]["tournament_date_end"]));

                    if (strtotime($date_now) < strtotime($tournament_date_start_reward)) {
                        $result["code"] = "-1";
                        $result["message"] = "Giải đấu chưa bắt đầu !";
                    } else
                    if (strtotime($date_now) > strtotime($tournament_date_end_reward)) {
                        //echo $tournament_date_end_reward; die;
                        $result["code"] = "-1";
                        $result["message"] = "Giải đấu đã kết thúc !";
                    } else {
                        $server_list_arena = explode(";", $get_tournament_details[0]["tournament_server_list"]);
                        if (in_array($server_id, $server_list_arena)) {
                            //Check Point Valid
                            $datauser = $this->quanhapmong->user_check_point_exist($char_id, $server_id, $mobo_service_id);
                            if (count($datauser) <= 0) {
                                $result["code"] = "-1";
                                $result["message"] = "Không lấy được Điểm Giang Hồ nạp thẻ, vui lòng thử lại !";
                            } else {
                                //Check Max Exchange
                                $check_max_exchange = $this->quanhapmong->get_total_gift_arena_exchange($reward_details[0]["tournament_id"], $server_id, $mobo_service_id);
                                if (($check_max_exchange[0]["TotalExchange"] + $count) > $reward_details[0]["max_exchange"]) {
                                    $result["code"] = "-1";
                                    $result["message"] = "Chỉ được đổi tối đa '" . $reward_details[0]["max_exchange"] . "' lần !";
                                } else {
                                    if ($datauser[0]["user_point"] < ($reward_details[0]["reward_point"] * $count)) {
                                        $result["code"] = "-1";
                                        $result["message"] = "Điểm Giang Hồ của bạn không đủ để đổi quà !";
                                    } else {
                                        //Add exchange History
                                        $userdata_p["char_id"] = $char_id;
                                        $userdata_p["server_id"] = $server_id;
                                        $userdata_p["char_name"] = $char_name;
                                        $userdata_p["mobo_service_id"] = $mobo_service_id;
                                        $userdata_p["reward_id"] = $id;
                                        $userdata_p["exchange_date"] = Date('Y-m-d H:i:s');
                                        $userdata_p["tournament_id"] = $reward_details[0]["tournament_id"];
                                        $userdata_p["exchange_count"] = $count;

                                        $i_id = $this->quanhapmong->insert_id("event_quanhapmong_exchange_history", $userdata_p);

                                        if ($i_id > 0) {
                                            //Update Point
                                            if ($this->quanhapmong->update_point($char_id, $server_id, $mobo_service_id, ($reward_details[0]["reward_point"] * $count)) > 0) {
                                                //SEND Item
                                                $info['mobo_service_id'] = $datadecode["mobo_service_id"];
                                                $info['character_id'] = $userdata["character_id"];
                                                $info['server_id'] = $userdata["server_id"];
                                                $info['character_name'] = $userdata["character_name"];

                                                $item1 = null;

                                                if ($reward_details[0]["reward_item1_code"] != 0 && $reward_details[0]["reward_item1_number"] != 0) {
                                                    $item1[] = array("item_id" => (int) $reward_details[0]["reward_item1_code"], "count" => ((int) $reward_details[0]["reward_item1_number"] * $count));
                                                }
                                                if ($reward_details[0]["reward_item2_code"] != 0 && $reward_details[0]["reward_item2_number"] != 0) {
                                                    $item1[] = array("item_id" => (int) $reward_details[0]["reward_item2_code"], "count" => ((int) $reward_details[0]["reward_item2_number"] * $count));
                                                }
                                                if ($reward_details[0]["reward_item3_code"] != 0 && $reward_details[0]["reward_item3_number"] != 0) {
                                                    $item1[] = array("item_id" => (int) $reward_details[0]["reward_item3_code"], "count" => ((int) $reward_details[0]["reward_item3_number"] * $count));
                                                }
                                                if ($reward_details[0]["reward_item4_code"] != 0 && $reward_details[0]["reward_item4_number"] != 0) {
                                                    $item1[] = array("item_id" => (int) $reward_details[0]["reward_item4_code"], "count" => ((int) $reward_details[0]["reward_item4_number"] * $count));
                                                }
                                                if ($reward_details[0]["reward_item5_code"] != 0 && $reward_details[0]["reward_item5_number"] != 0) {
                                                    $item1[] = array("item_id" => (int) $reward_details[0]["reward_item5_code"], "count" => ((int) $reward_details[0]["reward_item5_number"] * $count));
                                                }

                                                $data_result = $this->senditemforgame($info, $item1, "Chúc mừng bạn nhận được quà", "Quà Điểm Giang Hồ");
                                                $this->quanhapmong->update_exchange_history_arena($i_id, json_encode($item1), json_encode($data_result), ($reward_details[0]["reward_point"] * $count));

                                                $result["code"] = "0";
                                                $result["message"] = "Nhận quà thành công!";
                                            } else {
                                                //Fail restore point
                                                $this->quanhapmong->add_point($char_id, $server_id, $mobo_service_id, ($reward_details[0]["reward_point"] * $count));
                                                $result["code"] = "-1";
                                                $result["message"] = "Nhận quà thất bại, vui lòng thử lại*!";
                                            }
                                        } else {
                                            $result["code"] = "-1";
                                            $result["message"] = "Nhận quà thất bại, vui lòng thử lại!";
                                        }
                                    }
                                }
                            }
                        } else {
                            $result["code"] = "-1";
                            $result["message"] = "Server hiện tại không cho phép đổi quà !";
                        }
                    }//////////////////////////////////////////////                                 
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

    function exchange_gift() {
        echo "Truy cập không hợp lệ.";
        die;
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

        $data["gift_list"] = $this->quanhapmong->get_gift_list();
        $gift_filter = array();

        foreach ($data["gift_list"] as $key => $value) {
            $server_list = preg_replace('/\s+/', '', $value["server_list"]);

            if ($server_list != "") {
                $server_list = explode(";", $server_list);

                if (in_array($server_id, $server_list)) {
                    array_push($gift_filter, Array("id" => $value["id"], "item_id" => $value["item_id"], "gift_name" => $value["gift_name"], "gift_price" => $value["gift_price"],
                        "gift_quantity" => $value["gift_quantity"], "gift_img" => $value["gift_img"], "gift_status" => $value["gift_status"], "gift_insert_date" => $value["gift_insert_date"]));
                }
            }
        }

        $this->data["gift_list"] = $gift_filter;

        //Set Session
        $this->storeSession($mobo_service_id, $server_id);

        //User Point
        //$this->data["user_point"] = $this->user_point($char_id, $server_id, $mobo_service_id, $mobo_id, $char_name);

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("event/quanhapmong/doiqua", $this->data, true);
    }

    function gift_receive() {
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

        $this->data["calendar_bonus"] = $this->quanhapmong->get_bonus_calendar_by_user($mobo_service_id, $server_id);

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("event/quanhapmong/gift_receive", $this->data, true);
    }

    public function gift_receive_process() {
        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 10) {
            $result["code"] = "-1";
            $result["message"] = "Mỗi lần nhận quà phải cách nhau 10 giây.";
            $this->output->set_output(json_encode($result));
            return;
        }

        if (empty($_SESSION['user_info'])) {
            $result["code"] = "-2";
            $result["message"] = "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            $this->output->set_output(json_encode($result));
            return;
        }
        
        //Tournament
            $tournament = $this->quanhapmong->get_tournament();
            if (count($tournament) == 0) {
                $result["code"] = "-1";
                $result["message"] = "Sự kiện đang tạm đóng, bạn vui lòng quay lại sau";
                $this->output->set_output(json_encode($result));
                return;
            }

            //Check Date Valid
            $date_now = date('Y-m-d H:i:s');
            $tournament_date_start = date('Y-m-d 00:00:00', strtotime($tournament[0]["tournament_date_start"]));
            $tournament_date_end = date('Y-m-d 23:59:59', strtotime($tournament[0]["tournament_date_end"]));

            //echo $date_receive_start . '<br>' . $date_receive_end; die;
            if (strtotime($date_now) < strtotime($tournament_date_start)) {
                $result["code"] = "-1";
                $result["message"] = "Sự kiện chưa mở, bạn vui lòng quay lại sau!";
                $this->output->set_output(json_encode($result));
                return;
            }

            if (strtotime($date_now) > strtotime($tournament_date_end)) {
                $result["code"] = "-1";
                $result["message"] = "Sự kiện đã kết thúc, bạn vui lòng quay lại sau!";
                $this->output->set_output(json_encode($result));
                return;
            }

        $user = unserialize($_SESSION['user_info']);

        if ($this->is_test && !in_array($user->mobo_id, $this->mobo_id_test)) {
            $result["code"] = "-1";
            $result["message"] = "Bạn không có quyền truy cập sự kiện này!";
            $this->output->set_output(json_encode($result));
            return;
        }

        if (!$this->getSession($user->mobo_service_id, $user->server_id)) {
            $result["code"] = "-1";
            $result["message"] = "Bạn đang đăng nhập trên một máy khác.";
            $this->output->set_output(json_encode($result));
            return;
        }

        $calendar_id = $_GET["calendar_id"];

        //Check Received Exist
        $check_received = $this->quanhapmong->get_bonus_calendar_details($calendar_id);
        if (count($check_received) == 0) {
            $result["code"] = "-1";
            $result["message"] = "Không lấy được thông tin quà tặng.";
            $this->output->set_output(json_encode($result));
            return;
        }

        $date_now = date('Y-m-d H:i:s');
        $bonus_date = date('Y-m-d H:i:s', strtotime($check_received[0]["bonus_date"]));
        $bonus_date_end = date('Y-m-d 23:59:59', strtotime($check_received[0]["bonus_date"]));

        if ($check_received[0]["status_received"] == "1") {
            $result["code"] = "-1";
            $result["message"] = "Bạn đã nhận gói quà ngày này rồi.";
            $this->output->set_output(json_encode($result));
            return;
        }

        if (strtotime($date_now) < strtotime($bonus_date)) {
            $result["code"] = "-1";
            $result["message"] = "Chưa đến thời gian nhận quà!";
            $this->output->set_output(json_encode($result));
            return;
        }

        if (strtotime($date_now) > strtotime($bonus_date_end)) {
            $result["code"] = "-1";
            $result["message"] = "Thời gian nhận gói quà này đã hết!";
            $this->output->set_output(json_encode($result));
            return;
        }

//        if (strtotime($date_now) < strtotime($bonus_date_end)) {
//            $result["code"] = "-1";
//            $result["message"] = "Bạn không thể nhận quà của ngày đã bỏ qua.";
//            $this->output->set_output(json_encode($result));
//            return;
//        }
        //Ghi Log       
        $userdata_p["char_id"] = $user->character_id;
        $userdata_p["server_id"] = $user->server_id;
        $userdata_p["char_name"] = $user->character_name;
        $userdata_p["mobo_service_id"] = $user->mobo_service_id;
        $userdata_p["received_date"] = Date('Y-m-d H:i:s');
        $userdata_p["mobo_id"] = $user->mobo_id;
        $userdata_p["bonus_calendar_id"] = $calendar_id;

        $i_id = $this->quanhapmong->insert_id("event_quanhapmong_received_history", $userdata_p);

        if ($i_id <= 0) {
            $result["code"] = "-1";
            $result["message"] = "Nhận quà thất bại, vui lòng thử lại*!";
            $this->output->set_output(json_encode($result));
            return;
        }

        //Check Gift Valid
        $gift_details = $this->quanhapmong->get_gift_pakage_details($check_received[0]["gift_pakage_id"]);

        if (count($gift_details) == 0) {
            $result["code"] = "-1";
            $result["message"] = "Nhận quà thất bại, vui lòng thử lại**!";
            $this->output->set_output(json_encode($result));
            return;
        }

        //Check Server Valid
        $server_list = explode(";", $gift_details[0]["server_list"]);
        if (!in_array($user->server_id, $server_list)) {
            $result["code"] = "-1";
            $result["message"] = "Dữ liệu quà không hợp lệ* !";
            $this->output->set_output(json_encode($result));
            return;
        }

        $i_update = $this->quanhapmong->uptdate_bonus_calendar_status($calendar_id, 1);
        if ($i_update <= 0) {
            $result["code"] = "-1";
            $result["message"] = "Nhận quà thất bại, vui lòng thử lại***!";
            $this->output->set_output(json_encode($result));
            return;
        }

        //SEND Item
        $item1 = null;
        if ($gift_details[0]["reward_item1_code"] != 0 && $gift_details[0]["reward_item1_number"] != 0) {
            $item1[] = array("item_id" => (int) $gift_details[0]["reward_item1_code"], "count" => (int) $gift_details[0]["reward_item1_number"]);
        }
        if ($gift_details[0]["reward_item2_code"] != 0 && $gift_details[0]["reward_item2_number"] != 0) {
            $item1[] = array("item_id" => (int) $gift_details[0]["reward_item2_code"], "count" => (int) $gift_details[0]["reward_item2_number"]);
        }
        if ($gift_details[0]["reward_item3_code"] != 0 && $gift_details[0]["reward_item3_number"] != 0) {
            $item1[] = array("item_id" => (int) $gift_details[0]["reward_item3_code"], "count" => (int) $gift_details[0]["reward_item3_number"]);
        }
        if ($gift_details[0]["reward_item4_code"] != 0 && $gift_details[0]["reward_item4_number"] != 0) {
            $item1[] = array("item_id" => (int) $gift_details[0]["reward_item4_code"], "count" => (int) $gift_details[0]["reward_item4_number"]);
        }
        if ($gift_details[0]["reward_item5_code"] != 0 && $gift_details[0]["reward_item5_number"] != 0) {
            $item1[] = array("item_id" => (int) $gift_details[0]["reward_item5_code"], "count" => (int) $gift_details[0]["reward_item5_number"]);
        }

        $data_result = $this->senditemforgame($userdata_p, $item1, "Chúc mừng bạn nhận được quà", "Quà Nhập Mộng");
        $result_send_json = json_decode($data_result, true);
        //var_dump($result_send_json); die;

        if ($result_send_json["code"] == 0) {
            //Send Item Success
            $this->quanhapmong->update_received_history($i_id, json_encode($item1), $data_result, 1);
            $result["code"] = "0";
            $result["message"] = "Nhận quà thành công !";
            $this->output->set_output(json_encode($result));
            return;
        } else {
            //Send Item Fail, rollback Status Calendar 
            $i_update = $this->quanhapmong->uptdate_bonus_calendar_status($calendar_id, 0);
            $this->quanhapmong->update_received_history($i_id, json_encode($item1), $data_result, 0);
            $result["code"] = "-1";
            $result["message"] = "Nhận quà thất bại, vui lòng thử lại!****";
            $this->output->set_output(json_encode($result));
            return;
        }
    }

    function exchange_gift_by() {
        echo "Truy cập không hợp lệ.";
        die;
        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 10) {
            $result["code"] = "-1";
            $result["message"] = "Mỗi lần đổi quà phải cách nhau 10 giây.";
        } else {
            $_SESSION["execute_time"] = time();

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
            if (!$this->getSession($mobo_service_id, $server_id)) {
                $result["code"] = "-1";
                $result["message"] = "Bạn đang đăng nhập trên một máy khác.";
            } else {
                //Check Valid Point
                $datauser = $this->quanhapmong->user_check_point_exist($char_id, $server_id, $mobo_service_id);

                if (count($datauser) > 0) {
                    //Check Gift Valid
                    $gift_details = $this->quanhapmong->get_gift_details($id);

                    if (count($gift_details) > 0) {
                        foreach ($gift_details as $key => $value) {
                            //Check Server Valid
                            $server_list = explode(";", $value["server_list"]);
                            if (!in_array($server_id, $server_list)) {
                                $result["code"] = "-1";
                                $result["message"] = "Dữ liệu quà không hợp lệ* !";
                            } else {
                                $gift_price = $value["gift_price"];

                                //Item Info
                                $item_id = $value["item_id"];
                                $item_quantity = $value["gift_quantity"];

                                foreach ($datauser as $key => $value) {
                                    //Check Point Valid                        
                                    if ($gift_price > $value["user_point"]) {
                                        $result["code"] = "-1";
                                        $result["message"] = "Số dư Điểm Giang Hồ không đủ !";
                                    } else {
                                        //Send Gift API      
                                        $info['mobo_service_id'] = $datadecode["mobo_service_id"];
                                        $info['character_id'] = $userdata["character_id"];
                                        $info['server_id'] = $userdata["server_id"];
                                        $info['character_name'] = $userdata["character_name"];

                                        //Add Gift Exchange History
                                        $userdata_p["user_id"] = $value["id"];
                                        $userdata_p["item_ex_id"] = $id;
                                        $userdata_p["exchange_gift_point"] = $gift_price;
                                        $userdata_p["exchange_gift_date"] = Date('Y-m-d H:i:s');
                                        $i_id = $this->quanhapmong->insert_id("event_quanhapmong_gift_exchange_history", $userdata_p);

                                        if ($i_id > 0) {
                                            //Update Point
                                            if ($this->quanhapmong->update_point($char_id, $server_id, $mobo_service_id, $gift_price) > 0) {
                                                //SEND Item
                                                $item_create[] = array("item_id" => $item_id, "count" => $item_quantity);
                                                $data_result = $this->senditemforgame($info, $item_create, "Chúc mừng bạn nhận được quà", "Quà đấu trường");
                                                $this->quanhapmong->update_exchange_history($i_id, json_encode($item_create), json_encode($data_result), $item_quantity);

                                                $result["code"] = "0";
                                                $result["message"] = "Đổi quà thành công !";
                                            } else {
                                                $result["code"] = "-1";
                                                $result["message"] = "Đổi quà thất bại, vui lòng thử lại!";
                                            }
                                        } else {
                                            $result["code"] = "-1";
                                            $result["message"] = "Đổi quà thất bại, vui lòng thử lại!";
                                        }
                                    }

                                    //if($this->senditemforgame($info, $item_create)){    
                                    //    //Update Point
                                    //    $this->quanhapmong->update_point($char_id, $server_id, $mobo_service_id, $gift_price);
                                    //    //Add Gift Exchange History
                                    //    $userdata_p["user_id"] = $value["id"];
                                    //    $userdata_p["item_ex_id"] = $id;
                                    //    $userdata_p["exchange_gift_point"] = $gift_price;
                                    //    $userdata_p["exchange_gift_date"] = Date('Y-m-d H:i:s');                            
                                    //    $this->quanhapmong->insert("event_quanhapmong_gift_exchange_history", $userdata_p);
                                    //    $result["code"] = "0";
                                    //    $result["message"] = "Đổi quà thành công !";                            
                                    //}
                                    //else{
                                    //    $result["code"] = "-1";
                                    //    $result["message"] = "Đổi quà thất bại, vui lòng thử lại sau !";
                                    //}
                                }
                            }
                        }
                    } else {
                        $result["code"] = "-1";
                        $result["message"] = "Dữ liệu quà không hợp lệ** !";
                    }
                } else {
                    //User Point Not Found
                    $result["code"] = "-1";
                    $result["message"] = "Không có dữ liệu người dùng !";
                }
            }
        }

        $this->output->set_output(json_encode($result));
    }

    function exchange_gift_by_shop() {
        echo "Truy cập không hợp lệ.";
        die;
        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 10) {
            $result["code"] = "-1";
            $result["message"] = "Mỗi lần đổi quà phải cách nhau 10 giây.";
        } else {
            $_SESSION["execute_time"] = time();

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
            $item_quantity = $_GET["quantity"];

            if (!$this->getSession($mobo_service_id, $server_id)) {
                $result["code"] = "-1";
                $result["message"] = "Bạn đang đăng nhập trên một máy khác.";
            } else {
                //Check Valid Point
                $datauser = $this->quanhapmong->user_check_point_exist($char_id, $server_id, $mobo_service_id);

                if (count($datauser) > 0) {
                    //Check Gift Valid
                    $gift_details = $this->quanhapmong->get_gift_details($id);

                    if (count($gift_details) > 0) {
                        foreach ($gift_details as $key => $value) {
                            //Check Server Valid
                            $server_list = explode(";", $value["server_list"]);
                            if (!in_array($server_id, $server_list)) {
                                $result["code"] = "-1";
                                $result["message"] = "Dữ liệu quà không hợp lệ* !";
                            } else {
                                //Check Max Buy 
                                $check_max_exchange = $this->quanhapmong->get_total_gift_exchange_shop($server_id, $mobo_service_id, $id);
                                if ($value["gift_buy_max"] != 0 && (($check_max_exchange[0]["TotalExchange"] + $item_quantity) > $value["gift_buy_max"])) {
                                    $result["code"] = "-1";
                                    $result["message"] = "Bạn chỉ được đổi tối đa '" . $value["gift_buy_max"] . " " . $value["gift_name"] . "'!";
                                } else {
                                    $gift_price = $item_quantity * $value["gift_price"];

                                    //Item Info
                                    $item_id = $value["item_id"];

                                    foreach ($datauser as $key => $value) {
                                        //Check Point Valid                        
                                        if ($gift_price > $value["user_point"]) {
                                            $result["code"] = "-1";
                                            $result["message"] = "Số dư Điểm Giang Hồ không đủ !";
                                        } else {
                                            //Send Gift API      
                                            $info['mobo_service_id'] = $datadecode["mobo_service_id"];
                                            $info['character_id'] = $userdata["character_id"];
                                            $info['server_id'] = $userdata["server_id"];
                                            $info['character_name'] = $userdata["character_name"];

                                            //Add Gift Exchange History
                                            $userdata_p["user_id"] = $value["id"];
                                            $userdata_p["item_ex_id"] = $id;
                                            $userdata_p["exchange_gift_point"] = $gift_price;
                                            $userdata_p["exchange_gift_date"] = Date('Y-m-d H:i:s');
                                            $i_id = $this->quanhapmong->insert_id("event_quanhapmong_gift_exchange_history", $userdata_p);

                                            if ($i_id > 0) {
                                                //Update Point
                                                if ($this->quanhapmong->update_point($char_id, $server_id, $mobo_service_id, $gift_price) > 0) {
                                                    //SEND Item
                                                    $item_create[] = array("item_id" => $item_id, "count" => $item_quantity);
                                                    $data_result = $this->senditemforgame($info, $item_create, "Chúc mừng bạn nhận được quà", "Quà đấu trường");
                                                    $this->quanhapmong->update_exchange_history($i_id, json_encode($item_create), json_encode($data_result), $item_quantity);

                                                    $result["code"] = "0";
                                                    $result["message"] = "Đổi quà thành công !";
                                                } else {
                                                    $result["code"] = "-1";
                                                    $result["message"] = "Đổi quà thất bại, vui lòng thử lại!";
                                                }
                                            } else {
                                                $result["code"] = "-1";
                                                $result["message"] = "Đổi quà thất bại, vui lòng thử lại!";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $result["code"] = "-1";
                        $result["message"] = "Dữ liệu quà không hợp lệ** !";
                    }
                } else {
                    //User Point Not Found
                    $result["code"] = "-1";
                    $result["message"] = "Không có dữ liệu người dùng !";
                }
            }
        }

        $this->output->set_output(json_encode($result));
    }

    //Process Point From Game Charging
    function point_process() {
        $params = $this->input->get();
        //var_dump($params); die;
        $needle = array("mobo_service_id", "mobo_id", "date", "transaction_id", "payment_type", "character_id",
            "character_name", "server_id", "amount");
        //var_dump($needle); die;
        if (!is_required($params, $needle) == TRUE) {
            $diff = array_diff(array_values($needle), array_keys($params));
            echo json_encode(array("code" => -1, "message" => "INVALID_PARAMS", "data" => $diff));
            die;
        }

        $token = $params["token"];
        unset($params["token"]);

        $tokendata = implode("", $params);
        $valid = md5($tokendata . $this->secret_key);

        if ($token != $valid) {
            echo json_encode(array("code" => -1, "message" => "INVALID_TOKEN", "data" => $valid));
            die;
        }

        $mobo_service_id = $_GET["mobo_service_id"];
        $mobo_id = $_GET["mobo_id"];
        $character_id = $_GET["character_id"];
        $server_id = $_GET["server_id"];
        $character_name = $_GET["character_name"];
        $amount = $_GET["amount"];
        $transaction_id = $_GET["transaction_id"];
        $payment_type = $_GET["payment_type"];

        $point_add = 0;
        if ($payment_type == "mopay") {
            $point_add = ($amount / 100) * 105;
        } else {
            //Get Point Add Rate
            $point_add_rate = $this->quanhapmong->get_point_add_rate($amount, $payment_type);
            if (count($point_add_rate > 0)) {
                $point_add = $point_add_rate[0]["point_bonus"];
            } else {
                echo json_encode(array("code" => -1, "message" => "GET POINT ADD RATE FAIL", "data" => json_encode($point_add_rate)));
                die;
            }
        }

        //echo $transaction_id; die;

        if ($mobo_service_id == null || $character_id == null || $server_id == null || $mobo_id == null) {
            echo json_encode(array("code" => -1, "message" => "USER DATA NULL", "data" => "MSI: " . $mobo_service_id . " - CharID: " . $character_id . " - CharName: " . $character_name));
            die;
        }

        if ($amount == null || $amount <= 0) {
            echo json_encode(array("code" => -1, "message" => "AMOUNT NOT VALID", "data" => $amount));
            die;
        }

        if ($transaction_id == null || $transaction_id == "") {
            echo json_encode(array("code" => -1, "message" => "TRANSACTION ID NOT VALID", "data" => $amount));
            die;
        }

        $datauser = $this->quanhapmong->user_check_point_exist($character_id, $server_id, $mobo_service_id);

        if (count($datauser) > 0) {
            //Check Exist Transaction Processed
            $check_exist_trans = $this->quanhapmong->check_exist_transaction_processed($transaction_id);
            if (count($check_exist_trans) > 0) {
                echo json_encode(array("code" => -1, "message" => "TRANSACTION ID HAS BEEN PROCESSED", "transaction_id" => $transaction_id));
                die;
            }

            //Insert Add Point History                    
            $userdata_add_p["char_id"] = $character_id;
            $userdata_add_p["server_id"] = $server_id;
            $userdata_add_p["char_name"] = $character_name;
            $userdata_add_p["mobo_id"] = $mobo_id;
            $userdata_add_p["mobo_service_id"] = $mobo_service_id;
            $userdata_add_p["point_add"] = $point_add;
            $userdata_add_p["transaction_id"] = $transaction_id;
            $userdata_add_p["update_date"] = Date('Y-m-d H:i:s');
            $userdata_add_p["amount"] = $amount;
            $userdata_add_p["payment_type"] = $payment_type;

            $i_add_history = $this->quanhapmong->insert_id("event_quanhapmong_point_add_history", $userdata_add_p);

            if ($i_add_history > 0) {
                //Update Point
                $i_add_point = $this->quanhapmong->add_point($character_id, $server_id, $mobo_service_id, (int) $point_add);
                if ($i_add_point > 0) {
                    $this->quanhapmong->update_add_point_status_history($i_add_history, 1);
                    echo json_encode(array("code" => 0, "message" => "ADD POINT SUCCESS", "data" => $i_add_history));
                    die;
                } else {
                    $this->quanhapmong->update_add_point_status_history($i_add_history, 0);
                    echo json_encode(array("code" => -1, "message" => "ADD POINT FAIL", "data" => $i_add_history));
                    die;
                }
            } else {
                echo json_encode(array("code" => -1, "message" => "ADD POINT HISTORY FAIL", "data" => $i_add_history));
                die;
            }
        } else {
            //Insert User Point
            $userdata_p["char_id"] = $character_id;
            $userdata_p["server_id"] = $server_id;
            $userdata_p["char_name"] = $character_name;
            $userdata_p["mobo_service_id"] = $mobo_service_id;
            $userdata_p["mobo_id"] = $mobo_id;
            $userdata_p["point_add"] = 0;
            $userdata_p["user_point"] = 0;

            $i_insert = $this->quanhapmong->insert_id("event_quanhapmong_point", $userdata_p);
            if ($i_insert > 0) {
                //Check Exist Transaction Processed
                $check_exist_trans = $this->quanhapmong->check_exist_transaction_processed($transaction_id);
                if (count($check_exist_trans) > 0) {
                    echo json_encode(array("code" => -1, "message" => "TRANSACTION ID HAS BEEN PROCESSED", "transaction_id" => $transaction_id));
                    die;
                }

                //Insert Add Point History                    
                $userdata_add_p["char_id"] = $character_id;
                $userdata_add_p["server_id"] = $server_id;
                $userdata_add_p["char_name"] = $character_name;
                $userdata_add_p["mobo_id"] = $mobo_id;
                $userdata_add_p["mobo_service_id"] = $mobo_service_id;
                $userdata_add_p["point_add"] = $point_add;
                $userdata_add_p["transaction_id"] = $transaction_id;
                $userdata_add_p["update_date"] = Date('Y-m-d H:i:s');
                $userdata_add_p["amount"] = $amount;
                $userdata_add_p["payment_type"] = $payment_type;

                $i_add_history = $this->quanhapmong->insert_id("event_quanhapmong_point_add_history", $userdata_add_p);

                if ($i_add_history > 0) {
                    //Update Point
                    $i_add_point = $this->quanhapmong->add_point($character_id, $server_id, $mobo_service_id, (int) $point_add);
                    if ($i_add_point > 0) {
                        $this->quanhapmong->update_add_point_status_history($i_add_history, 1);
                        echo json_encode(array("code" => 0, "message" => "ADD POINT SUCCESS", "data" => $i_add_history));
                        die;
                    } else {
                        $this->quanhapmong->update_add_point_status_history($i_add_history, 0);
                        echo json_encode(array("code" => -1, "message" => "ADD POINT FAIL", "data" => $i_add_history));
                        die;
                    }
                } else {
                    echo json_encode(array("code" => -1, "message" => "ADD POINT HISTORY FAIL", "data" => $i_add_history));
                    die;
                }
            } else {
                echo json_encode(array("code" => -1, "message" => "INSERT USER FAIL", "data" => $datauser));
                die;
            }
        }
    }

    //Gift Pakage
    function exchange_gift_pakage_by_shop() {
        echo "Truy cập không hợp lệ.";
        die;
        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 10) {
            $result["code"] = "-1";
            $result["message"] = "Mỗi lần đổi quà phải cách nhau 10 giây.";
        } else {
            $_SESSION["execute_time"] = time();

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

            if (!$this->getSession($mobo_service_id, $server_id)) {
                $result["code"] = "-1";
                $result["message"] = "Bạn đang đăng nhập trên một máy khác.";
            } else {
                //Check Valid Point
                $datauser = $this->quanhapmong->user_check_point_exist($char_id, $server_id, $mobo_service_id);

                if (count($datauser) > 0) {
                    //Check Gift Valid
                    $gift_details = $this->quanhapmong->get_gift_pakage_details($id);

                    if (count($gift_details) > 0) {
                        foreach ($gift_details as $key => $value) {
                            //Check Server Valid
                            $server_list = explode(";", $value["server_list"]);
                            if (!in_array($server_id, $server_list)) {
                                $result["code"] = "-1";
                                $result["message"] = "Dữ liệu quà không hợp lệ* !";
                            } else {
                                //Check VIP
                                //Get Game User
                                $api = new MGH_API();
                                $user_info = $api->get_user_info($mobo_service_id, $server_id);

                                if ($user_info['data']['data']['vipPoint'] < $value["gift_vip_point"]) {
                                    $result["code"] = "-1";
                                    $result["message"] = "Bạn phải đạt '" . $this->return_vip_string($value["gift_vip_point"]) . "' để có thể đổi quà !";
                                    $this->output->set_output(json_encode($result));
                                    return;
                                }

                                //Check Max Buy 
                                $check_max_exchange = $this->quanhapmong->get_total_gift_pakage_exchange_shop($server_id, $mobo_service_id, $id);
                                if ($value["gift_buy_max"] != 0 && (($check_max_exchange[0]["TotalExchange"]) >= $value["gift_buy_max"])) {
                                    $result["code"] = "-1";
                                    $result["message"] = "Bạn chỉ được đổi tối đa '" . $value["gift_buy_max"] . " " . $value["gift_name"] . "' trong ngày!";
                                } else {
                                    $gift_price = $value["gift_price"];

                                    foreach ($datauser as $key => $value) {
                                        //Check Point Valid                        
                                        if ($gift_price > $value["user_point"]) {
                                            $result["code"] = "-1";
                                            $result["message"] = "Số dư Điểm Giang Hồ không đủ !";
                                        } else {
                                            //Send Gift API      
                                            $info['mobo_service_id'] = $datadecode["mobo_service_id"];
                                            $info['character_id'] = $userdata["character_id"];
                                            $info['server_id'] = $userdata["server_id"];
                                            $info['character_name'] = $userdata["character_name"];

                                            //Add Gift Exchange History
                                            $userdata_p["user_id"] = $value["id"];
                                            $userdata_p["item_ex_id"] = $id;
                                            $userdata_p["exchange_gift_point"] = $gift_price;
                                            $userdata_p["exchange_gift_date"] = Date('Y-m-d H:i:s');
                                            $i_id = $this->quanhapmong->insert_id("event_quanhapmong_gift_pakage_exchange_history", $userdata_p);

                                            if ($i_id > 0) {
                                                //Update Point
                                                if ($this->quanhapmong->update_point($char_id, $server_id, $mobo_service_id, $gift_price) > 0) {
                                                    //SEND Item
                                                    $item1 = null;

                                                    if ($gift_details[0]["reward_item1_code"] != 0 && $gift_details[0]["reward_item1_number"] != 0) {
                                                        $item1[] = array("item_id" => (int) $gift_details[0]["reward_item1_code"], "count" => (int) $gift_details[0]["reward_item1_number"]);
                                                    }
                                                    if ($gift_details[0]["reward_item2_code"] != 0 && $gift_details[0]["reward_item2_number"] != 0) {
                                                        $item1[] = array("item_id" => (int) $gift_details[0]["reward_item2_code"], "count" => (int) $gift_details[0]["reward_item2_number"]);
                                                    }
                                                    if ($gift_details[0]["reward_item3_code"] != 0 && $gift_details[0]["reward_item3_number"] != 0) {
                                                        $item1[] = array("item_id" => (int) $gift_details[0]["reward_item3_code"], "count" => (int) $gift_details[0]["reward_item3_number"]);
                                                    }
                                                    if ($gift_details[0]["reward_item4_code"] != 0 && $gift_details[0]["reward_item4_number"] != 0) {
                                                        $item1[] = array("item_id" => (int) $gift_details[0]["reward_item4_code"], "count" => (int) $gift_details[0]["reward_item4_number"]);
                                                    }
                                                    if ($gift_details[0]["reward_item5_code"] != 0 && $gift_details[0]["reward_item5_number"] != 0) {
                                                        $item1[] = array("item_id" => (int) $gift_details[0]["reward_item5_code"], "count" => (int) $gift_details[0]["reward_item5_number"]);
                                                    }

                                                    $data_result = $this->senditemforgame($info, $item1, "Chúc mừng bạn nhận được quà", "Quà đấu trường");
                                                    $this->quanhapmong->update_exchange_pakage_history($i_id, json_encode($item1), json_encode($data_result));

                                                    $result["code"] = "0";
                                                    $result["message"] = "Đổi quà thành công !";
                                                } else {
                                                    $result["code"] = "-1";
                                                    $result["message"] = "Đổi quà thất bại, vui lòng thử lại!";
                                                }
                                            } else {
                                                $result["code"] = "-1";
                                                $result["message"] = "Đổi quà thất bại, vui lòng thử lại!";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $result["code"] = "-1";
                        $result["message"] = "Dữ liệu quà không hợp lệ** !";
                    }
                } else {
                    //User Point Not Found
                    $result["code"] = "-1";
                    $result["message"] = "Không có dữ liệu người dùng !";
                }
            }
        }

        $this->output->set_output(json_encode($result));
    }

    function exchange_gift_pakage_special_by_shop() {
        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 10) {
            $result["code"] = "-1";
            $result["message"] = "Mỗi lần đổi quà phải cách nhau 10 giây.";
        } else {
            $_SESSION["execute_time"] = time();

            if (empty($_SESSION['user_info'])) {
                $result["code"] = "-1";
                $result["message"] = "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
                $this->output->set_output(json_encode($result));
                return;
            }

            //Tournament
            $tournament = $this->quanhapmong->get_tournament();
            if (count($tournament) == 0) {
                $result["code"] = "-1";
                $result["message"] = "Sự kiện đang tạm đóng, bạn vui lòng quay lại sau";
                $this->output->set_output(json_encode($result));
                return;
            }

            //Check Date Valid
            $date_now = date('Y-m-d H:i:s');
            $tournament_date_start = date('Y-m-d 00:00:00', strtotime($tournament[0]["tournament_date_start"]));
            $tournament_date_end = date('Y-m-d 23:59:59', strtotime($tournament[0]["tournament_date_end"]));

            //echo $date_receive_start . '<br>' . $date_receive_end; die;
            if (strtotime($date_now) < strtotime($tournament_date_start)) {
                $result["code"] = "-1";
                $result["message"] = "Sự kiện chưa mở, bạn vui lòng quay lại sau!";
                $this->output->set_output(json_encode($result));
                return;
            }

            if (strtotime($date_now) > strtotime($tournament_date_end)) {
                $result["code"] = "-1";
                $result["message"] = "Sự kiện đã kết thúc, bạn vui lòng quay lại sau!";
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

            if ($this->is_test && !in_array($user->mobo_id, $this->mobo_id_test)) {
                $result["code"] = "-1";
                $result["message"] = "Bạn không có quyền truy cập sự kiện này!";
                $this->output->set_output(json_encode($result));
                return;
            }

            $userdata_p["char_id"] = $char_id;
            $userdata_p["server_id"] = $server_id;
            $userdata_p["char_name"] = $char_name;
            $userdata_p["mobo_service_id"] = $mobo_service_id;

            $id = $_GET["id"];
            $day_count = $_GET["day_count"];

            if (!$this->getSession($mobo_service_id, $server_id)) {
                $result["code"] = "-1";
                $result["message"] = "Bạn đang đăng nhập trên một máy khác.";
            } else {
                $day_count_list = array("3", "7", "15", "30");
                if ($day_count == null || $day_count == 0 || !in_array($day_count, $day_count_list)) {
                    $result["code"] = "-1";
                    $result["message"] = "Số ngày nhận quà không hợp lệ!";
                    $this->output->set_output(json_encode($result));
                    return;
                }

                //Check Valid Point
                $datauser = $this->quanhapmong->user_check_point_exist($char_id, $server_id, $mobo_service_id);

                if (count($datauser) > 0) {
                    //Check Gift Valid
                    $gift_details = $this->quanhapmong->get_gift_pakage_details($id);

                    if (count($gift_details) > 0) {
                        foreach ($gift_details as $key => $value) {
                            //Check Server Valid
                            $server_list = explode(";", $value["server_list"]);
                            if (!in_array($server_id, $server_list)) {
                                $result["code"] = "-1";
                                $result["message"] = "Dữ liệu quà không hợp lệ* !";
                            } else {
                                //Check Buy Request number
                                if ($value["gift_number_request"] > 0) {
                                    $check_number = $value["gift_number_request"] - 1;
                                    $check_buy_number = $this->quanhapmong->check_gift_buy_request($server_id, $mobo_service_id, $check_number, $value["gift_date_start"], $value["gift_date_end"]);

                                    if ($check_buy_number[0]['TotalExchange'] == 0) {
                                        $result["code"] = "-1";
                                        $result["message"] = "Bạn phải mua '[Gói " . $value["gift_number_request"] . "]' trước  !";
                                        $this->output->set_output(json_encode($result));
                                        return;
                                    }
                                }

                                //Check Max Buy In Date
                                $check_max_exchange = $this->quanhapmong->get_total_gift_pakage_special_exchange_shop($server_id, $mobo_service_id, $id, $value["gift_date_start"], $value["gift_date_end"]);
                                if ($value["gift_buy_max"] != 0 && (($check_max_exchange[0]["TotalExchange"]) >= $value["gift_buy_max"])) {
                                    $gift_date_start = new DateTime($value["gift_date_start"]);
                                    $gift_date_end = new DateTime($value["gift_date_end"]);

                                    $result["code"] = "-1";
                                    $result["message"] = "Bạn chỉ được đổi tối đa '" . $value["gift_buy_max"] . " " . $value["gift_name"] . "' "
                                            . "từ '" . $gift_date_start->format('d-m-Y H:i:s') . "' đến '" . $gift_date_end->format('d-m-Y H:i:s') . "' !";
                                } else {
                                    $gift_price = $value["gift_price"] * $day_count;

                                    foreach ($datauser as $key => $value) {
                                        //Check Point Valid                        
                                        if ($gift_price > $value["user_point"]) {
                                            $result["code"] = "-1";
                                            $result["message"] = "Số dư Điểm Giang Hồ không đủ !";
                                        } else {
                                            //Send Gift API      
                                            $info['mobo_service_id'] = $mobo_service_id;
                                            $info['character_id'] = $char_id;
                                            $info['server_id'] = $server_id;
                                            $info['character_name'] = $char_name;

                                            //Add Gift Exchange History
                                            $userdata_p["user_id"] = $value["id"];
                                            $userdata_p["item_ex_id"] = $id;
                                            $userdata_p["exchange_gift_point"] = $gift_price;
                                            $userdata_p["exchange_gift_date"] = Date('Y-m-d H:i:s');
                                            $userdata_p["gift_number_request"] = $gift_details[0]["gift_number_request"];
                                            $userdata_p["day_count"] = $day_count;

                                            $i_id = $this->quanhapmong->insert_id("event_quanhapmong_gift_pakage_exchange_history", $userdata_p);

                                            if ($i_id > 0) {
                                                //Update Point
                                                if ($this->quanhapmong->update_point($char_id, $server_id, $mobo_service_id, $gift_price) > 0) {
                                                    //Process Calendar
                                                    $bonus_check = $this->quanhapmong->check_bonus_calendar($mobo_service_id, $server_id, $id);

                                                    $calendar_data["char_id"] = $user->character_id;
                                                    $calendar_data["server_id"] = $user->server_id;
                                                    $calendar_data["char_name"] = $user->character_name;
                                                    $calendar_data["mobo_service_id"] = $user->mobo_service_id;
                                                    $calendar_data["mobo_id"] = $user->mobo_id;
                                                    $calendar_data["gift_pakage_id"] = $id;

                                                    //echo count($bonus_check); die;

                                                    if (count($bonus_check) == 0) {
                                                        $bonus_date = date('Y-m-d', strtotime($bonus_date . ' - 1 days'));
                                                        for ($day = 1; $day <= $day_count; $day++) {
                                                            $calendar_data["bonus_date"] = date('Y-m-d', strtotime($bonus_date . ' + ' . $day . ' days'));
                                                            $i_id = $this->quanhapmong->insert_id("event_quanhapmong_calendar_bonus", $calendar_data);
                                                        }
                                                    } else {
                                                        $bonus_date = date('Y-m-d', strtotime($bonus_check[0]["bonus_date"]));
                                                        //var_dump($bonus_date); die;
                                                        for ($day = 1; $day <= $day_count; $day++) {
                                                            $calendar_data["bonus_date"] = date('Y-m-d', strtotime($bonus_date . ' + ' . $day . ' days'));
                                                            $i_id = $this->quanhapmong->insert_id("event_quanhapmong_calendar_bonus", $calendar_data);
                                                        }
                                                    }

                                                    $result["code"] = "0";
                                                    $result["message"] = "Đổi quà thành công, vui lòng qua mục 'Nhận Quà' để nhận thưởng !";
                                                } else {
                                                    $result["code"] = "-1";
                                                    $result["message"] = "Đổi quà thất bại, vui lòng thử lại!";
                                                }
                                            } else {
                                                $result["code"] = "-1";
                                                $result["message"] = "Đổi quà thất bại, vui lòng thử lại!";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $result["code"] = "-1";
                        $result["message"] = "Dữ liệu quà không hợp lệ** !";
                    }
                } else {
                    //User Point Not Found
                    $result["code"] = "-1";
                    $result["message"] = "Không có dữ liệu người dùng !";
                }
            }
        }

        $this->output->set_output(json_encode($result));
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

    //History
    function get_exchange_history() {
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

        //Set Session
        $this->storeSession($mobo_service_id, $server_id);

        $data["gift_exchange_history"] = $this->quanhapmong->get_gift_pakage_exchange_history($server_id, $mobo_service_id);
        $this->data["gift_exchange_history"] = $data["gift_exchange_history"];

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("event/quanhapmong/history", $this->data, true);
    }

    // Function to get the client IP address
    private function call_api_get($api_url) {
        set_time_limit(180);
        $urlrequest = $api_url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlrequest);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
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

    //Encrypt
    function encrypt($string, $key) {
        $result = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result.=$char;
        }

        return base64_encode($result);
    }

    function decrypt($string, $key) {
        $result = '';
        $string = base64_decode($string);

        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) - ord($keychar));
            $result.=$char;
        }

        return $result;
    }

    //Shop Điểm Giang Hồ
    function gift_type_list() {
        if (empty($_SESSION['user_info'])) {
            echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            die;
        }

        $user = unserialize($_SESSION['user_info']);
        //var_dump($user); die;
        $this->data["user"] = $user;
        $mobo_id = $user->mobo_id;

        //Non public
        if (!in_array($mobo_id, $this->mobo_id_test)) {
            $gift_type_list = $this->quanhapmong->gift_type_list();
        } else {
            $gift_type_list = $this->quanhapmong->gift_type_list_all();
        }

        $this->output->set_output(json_encode($gift_type_list));
    }

    function exchange_gift_shop() {
        if (empty($_SESSION['user_info'])) {
            echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            die;
        }

        //Tournament
        $tournament = $this->quanhapmong->get_tournament();
        if (count($tournament) == 0) {          
            echo "Sự kiện đang tạm đóng, bạn vui lòng quay lại sau";         
            die;
        }

        //Check Date Valid
        $date_now = date('Y-m-d H:i:s');
        $tournament_date_start = date('Y-m-d 00:00:00', strtotime($tournament[0]["tournament_date_start"]));
        $tournament_date_end = date('Y-m-d 23:59:59', strtotime($tournament[0]["tournament_date_end"]));

        //echo $date_receive_start . '<br>' . $date_receive_end; die;
        if (strtotime($date_now) < strtotime($tournament_date_start)) {            
            echo "Sự kiện chưa mở, bạn vui lòng quay lại sau!";         
            die;
        }

        if (strtotime($date_now) > strtotime($tournament_date_end)) {         
            echo "Sự kiện đã kết thúc, bạn vui lòng quay lại sau!";           
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

        if ($this->is_test && !in_array($user->mobo_id, $this->mobo_id_test)) {
            echo "Bạn không có quyền truy cập sự kiện này!";
            die;
        }

        //Set Session
        $this->storeSession($mobo_service_id, $server_id);

        $gift_type = $_GET["id"];

        if ($gift_type == 3) {
            //VIP Gift Pakage
            $data["gift_list"] = $this->quanhapmong->get_gift_pakage_list_by_type($gift_type);
            $gift_filter = array();

            foreach ($data["gift_list"] as $key => $value) {
                $server_list = preg_replace('/\s+/', '', $value["server_list"]);

                if ($server_list != "") {
                    $server_list = explode(";", $server_list);
                    if (in_array($server_id, $server_list)) {
                        array_push($gift_filter, Array("id" => $value["id"], "item_id" => $value["item_id"], "gift_name" => $value["gift_name"], "gift_price" => $value["gift_price"],
                            "gift_quantity" => $value["gift_quantity"], "gift_img" => $value["gift_img"], "gift_status" => $value["gift_status"], "gift_insert_date" => $value["gift_insert_date"],
                            "gift_buy_max" => $value["gift_buy_max"], "reuqets_vip" => $this->return_vip_string($value["gift_vip_point"]), "gift_vip_point" => $value["gift_vip_point"]));
                    }
                }
            }

            $this->data["gift_list"] = $gift_filter;

            //User Point
            $this->data["user_point"] = $this->user_point($char_id, $server_id, $mobo_service_id, $mobo_id, $char_name);

            //Get Game User
            $api = new MGH_API();
            $user_info = $api->get_user_info($mobo_service_id, $server_id);

            //var_dump($user_info); die;            
            $this->data["user_vip_point"] = $user_info['data']['data']['vipPoint'];

            $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
            echo $this->load->view("event/quanhapmong/doiquapakage_shop", $this->data, true);
        } else
        if ($gift_type == 4) {
            //Special Gift Pakage
            $data["gift_list"] = $this->quanhapmong->get_gift_pakage_special_list_by_type($gift_type);
            $gift_filter = array();

            foreach ($data["gift_list"] as $key => $value) {
                $server_list = preg_replace('/\s+/', '', $value["server_list"]);

                if ($server_list != "") {
                    $server_list = explode(";", $server_list);
                    if (in_array($server_id, $server_list)) {
                        array_push($gift_filter, Array("id" => $value["id"], "item_id" => $value["item_id"], "gift_name" => $value["gift_name"], "gift_price" => $value["gift_price"],
                            "gift_quantity" => $value["gift_quantity"], "gift_img" => $value["gift_img"], "gift_status" => $value["gift_status"], "gift_insert_date" => $value["gift_insert_date"],
                            "gift_date_start" => $value["gift_date_start"], "gift_date_end" => $value["gift_date_end"], "gift_number_request" => $value["gift_number_request"],
                            "gift_buy_max" => $value["gift_buy_max"], "reuqets_vip" => $this->return_vip_string($value["gift_vip_point"]), "gift_vip_point" => $value["gift_vip_point"]));
                    }
                }
            }

            $this->data["gift_list"] = $gift_filter;

            //User Point
            $this->data["user_point"] = $this->user_point($char_id, $server_id, $mobo_service_id, $mobo_id, $char_name);

            $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
            echo $this->load->view("event/quanhapmong/doiquapakage_s_shop", $this->data, true);
        } else {
            $data["gift_list"] = $this->quanhapmong->get_gift_list_by_type($gift_type);
            $gift_filter = array();

            foreach ($data["gift_list"] as $key => $value) {
                $server_list = preg_replace('/\s+/', '', $value["server_list"]);

                if ($server_list != "") {
                    $server_list = explode(";", $server_list);

                    if (in_array($server_id, $server_list)) {
                        array_push($gift_filter, Array("id" => $value["id"], "item_id" => $value["item_id"], "gift_name" => $value["gift_name"], "gift_price" => $value["gift_price"],
                            "gift_quantity" => $value["gift_quantity"], "gift_img" => $value["gift_img"], "gift_status" => $value["gift_status"], "gift_insert_date" => $value["gift_insert_date"],
                            "gift_buy_max" => $value["gift_buy_max"]));
                    }
                }
            }

            $this->data["gift_list"] = $gift_filter;

            //User Point
            $this->data["user_point"] = $this->user_point($char_id, $server_id, $mobo_service_id, $mobo_id, $char_name);

            $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
            echo $this->load->view("event/quanhapmong/doiqua_shop", $this->data, true);
        }
    }

    //Chuyển Điểm Giang Hồ
    function transfer_np() {
        echo "Truy cập không hợp lệ.";
        die;
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

        //Set Session
        $this->storeSession($mobo_service_id, $server_id);

//        if (!in_array($mobo_id, $this->mobo_id_test)) {
//            echo "Không thể chuyển điểm Điểm Giang Hồ lúc này, vui lòng thử lại sau"; die;
//        }
        //User Point
        $this->data["user_point"] = $this->user_point($char_id, $server_id, $mobo_service_id, $mobo_id, $char_name);

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("event/quanhapmong/transfer_np", $this->data, true);
    }

    public function transfer_np_process() {
        echo "Truy cập không hợp lệ.";
        die;
        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 10) {
            $result["code"] = "-1";
            $result["message"] = "Mỗi lần chuyển Điểm Giang Hồ phải cách nhau 10 giây.";
        } else {
            $_SESSION["execute_time"] = time();

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

//            if (!in_array($mobo_id, $this->mobo_id_test)) {
//            echo "Không thể chuyển điểm Điểm Giang Hồ lúc này, vui lòng thử lại sau"; die;
//            }

            $value = $_GET["value"];
            $to_mobo_id = $_GET["to_mobo_id"];

            $tax_value = ($value / 100) * 5;

            if (!$this->getSession($mobo_service_id, $server_id)) {
                $result["code"] = "-1";
                $result["message"] = "Bạn đang đăng nhập trên một máy khác.";
            } else {
                //Check Valid Point
                $datauser = $this->quanhapmong->user_check_point_exist($char_id, $server_id, $mobo_service_id);
                if (count($datauser) > 0) {
                    //Check User Point NP
                    $user_point_np = $this->user_point($char_id, $server_id, $mobo_service_id, $mobo_id, $char_name);
                    if ($user_point_np < ($value + ceil($tax_value))) {
                        $result["code"] = "-1";
                        $result["message"] = "Số dư Điểm Giang Hồ không đủ !";
                        $this->output->set_output(json_encode($result));
                        return;
                    }

                    //Check MIN
                    if ($value < 200) {
                        $result["code"] = "-1";
                        $result["message"] = "Mỗi lần chuyển ít nhất 200 Điểm Giang Hồ!";
                        $this->output->set_output(json_encode($result));
                        return;
                    }

                    //Check Valid Mobo Id Received
                    $mobo_info = $this->get_mobo_account($to_mobo_id);
                    //var_dump($mobo_info); die;

                    if ($mobo_info["code"] != 900000) {
                        $result["code"] = "-1";
                        $result["message"] = "Mobo ID cần chuyển không hợp lệ, vui lòng thử lại**";
                        $this->output->set_output(json_encode($result));
                        return;
                    }

                    if ($mobo_info["data"][106] == null) {
                        $result["code"] = "-1";
                        $result["message"] = "Mobo ID cần chuyển chưa chơi Game Mộng Giang hồ";
                        $this->output->set_output(json_encode($result));
                        return;
                    }

                    if ($mobo_info["data"][106][0]["mobo_id"] == $mobo_id) {
                        $result["code"] = "-1";
                        $result["message"] = "Bạn không thể chuyển điểm Điểm Giang Hồ cho chính bạn";
                        $this->output->set_output(json_encode($result));
                        return;
                    }

                    $to_mobo_service_id = $mobo_info["data"][106][0]["mobo_service_id"];

                    //Check Exist User Point
                    $datauser_checi_msi = $this->quanhapmong->user_check_mobo_service_id($to_mobo_service_id);
                    if (count($datauser_checi_msi) == 0) {
                        $result["code"] = "-1";
                        $result["message"] = "Mobo ID cần chuyển chưa tham gian Event Shop Điểm Giang Hồ";
                        $this->output->set_output(json_encode($result));
                        return;
                    }

                    //App Transfer NP History                    
                    $userdata_p["char_id"] = $char_id;
                    $userdata_p["server_id"] = $server_id;
                    $userdata_p["char_name"] = $char_name;
                    $userdata_p["mobo_service_id"] = $mobo_service_id;
                    $userdata_p["exchange_date"] = Date('Y-m-d H:i:s');
                    $userdata_p["mobo_id"] = $mobo_id;
                    $userdata_p["value"] = $value;
                    $userdata_p["tax_value"] = ceil($tax_value);
                    $userdata_p["to_mobo_id"] = $to_mobo_id;
                    $userdata_p["to_mobo_service_id"] = $to_mobo_service_id;

                    $id_insert_log = $this->quanhapmong->insert_id("event_quanhapmong_point_transfer_history", $userdata_p);

                    if ($id_insert_log > 0) {
                        //Update Point NP
                        if ($this->quanhapmong->update_point($char_id, $server_id, $mobo_service_id, $value + ceil($tax_value)) > 0) {
                            //Add Point NP To Mobo ID
                            $is_addpoint_nl = $this->quanhapmong->add_point_np_to_mobo_service_id($to_mobo_service_id, $value);
                            if ($is_addpoint_nl > 0) {
                                //Update Status Transfer
                                $this->quanhapmong->update_status_np_transer($id_insert_log);
                                $result["code"] = "0";
                                $result["message"] = "Chuyển khoản Điểm Giang Hồ thành công !";
                            } else {
                                //Fail Restore NP
                                $this->quanhapmong->add_point_np_to_mobo_service_id($mobo_service_id, $value + ceil($tax_value));
                                $result["code"] = "-1";
                                $result["message"] = "Chuyển khoản Điểm Giang Hồ thất bại, vui lòng thử lại!*";
                            }
                        } else {
                            $result["code"] = "-1";
                            $result["message"] = "Chuyển khoản Điểm Giang Hồ thất bại, vui lòng thử lại!**";
                        }
                    } else {
                        $result["code"] = "-1";
                        $result["message"] = "Chuyển khoản Điểm Giang Hồ thất bại, vui lòng thử lại!***";
                    }
                } else {
                    //User Point Not Found
                    $result["code"] = "-1";
                    $result["message"] = "Không có dữ liệu người dùng !";
                }
            }
        }

        $this->output->set_output(json_encode($result));
    }

    function get_exchange_history2() {
        echo "Truy cập không hợp lệ.";
        die;
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

        $data["point_transfer_history"] = $this->quanhapmong->get_quanhapmong_point_transfer_history($mobo_service_id);
        $this->data["point_transfer_history"] = $data["point_transfer_history"];

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("event/quanhapmong/history_1", $this->data, true);
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
        $response = $this->get($url);
        return json_decode($response, TRUE);
    }

    public function get($url) {
        if (empty($url)) {
            return false;
        }
        return $this->request('GET', $url, 'NULL');
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

    public function return_vip_string($vip_point) {
        if ($vip_point == 0) {
            return "VIP 01";
        }
        if ($vip_point == 270) {
            return "VIP 02";
        }
        if ($vip_point == 540) {
            return "VIP 03";
        }
        if ($vip_point == 2700) {
            return "VIP 04";
        }
        if ($vip_point == 5500) {
            return "VIP 05";
        }
        if ($vip_point == 10500) {
            return "VIP 06";
        }
        if ($vip_point == 21000) {
            return "VIP 07";
        }
        if ($vip_point == 41250) {
            return "VIP 08";
        }
        if ($vip_point == 82500) {
            return "VIP 09";
        }
        if ($vip_point == 165000) {
            return "VIP 10";
        }
        if ($vip_point == 330000) {
            return "VIP 11";
        }
        if ($vip_point == 660000) {
            return "VIP 12";
        }
    }

    private function user_point($char_id, $server_id, $mobo_service_id, $mobo_id, $char_name) {
        $datauser = $this->quanhapmong->user_check_point_exist($char_id, $server_id, $mobo_service_id);

        if (count($datauser) > 0) {
            //Update Mobo Id
            if ($datauser[0]["mobo_id"] == null || empty($datauser[0]["mobo_id"]) || ($datauser[0]["mobo_id"] != $mobo_id)) {
                $this->quanhapmong->update_quanhapmong_point_moboid($datauser[0]["id"], $mobo_id);
                //Insert Log Update MoboID
                $userdata_p['mobo_service_id'] = $mobo_service_id;
                $userdata_p['char_id'] = $char_id;
                $userdata_p['server_id'] = $server_id;
                $userdata_p['from_mobo_id'] = $datauser[0]["mobo_id"];
                $userdata_p['to_mobo_id'] = $mobo_id;
                $userdata_p["update_date"] = Date('Y-m-d H:i:s');
                $this->quanhapmong->insert("event_quanhapmong_point_update_moboid_history", $userdata_p);
            }
            return $datauser[0]["user_point"];
        } else {
            //Insert User Point
            $userdata_p["char_id"] = $char_id;
            $userdata_p["server_id"] = $server_id;
            $userdata_p["char_name"] = $char_name;
            $userdata_p["mobo_service_id"] = $mobo_service_id;
            $userdata_p["mobo_id"] = $mobo_id;
            $userdata_p["point_add"] = 0;
            $userdata_p["user_point"] = 0;

            $i_insert = $this->quanhapmong->insert_id("event_quanhapmong_point", $userdata_p);
            return 0;
        }
    }

    public function content_news() {
        $id = $_GET["id"];
        $api_url = "http://data.mobo.vn/home/get_post_id/$id/1/";
        $api_result = $this->call_api_get($api_url);
        $this->output->set_output($api_result);
    }

}
