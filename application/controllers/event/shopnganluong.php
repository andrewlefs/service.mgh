<?php

require_once APPPATH . 'third_party/MeAPI/Autoloader.php';
require_once APPPATH . '/core/EI_Controller.php';

class shopnganluong extends EI_Controller {

    ////////
    private $mobo_id_test = array("552397949", "886899541", "666629660");
    private $is_test = false;
    private $is_test_local = true;
    public $transaction_id;
    private $max_card_exchange = 163070000;
    protected $secret_key = "UJ;yX3d+E%8!YVa/";
    protected $event_key_nl = array("mgh2_nohu");
    protected $env_s = "real"; //sandbox, real

    public function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $this->load->library('GameFullAPI');
        $this->load->model('events/m_shopnganluong', "shopnganluong", false);
        $this->data["controler"] = $this;

        $this->CI = & get_instance();
        MeAPI_Autoloader::register();
        $this->CI->cache_config = MeAPI_Config_Game::cache();
    }

    private function init() {
        $_SESSION['linkinfo'] = $_GET;

        if ($this->verify_uri() != true) {
            $this->data["message"] = "Truy cập không hợp lệ";
            $this->render("deny", $this->data);
        }

        $user = $this->get_info();
        $this->data["user"] = $user;
        $_SESSION['user_info'] = serialize($user);
    }

    //Test
    public function test() {
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

        echo $this->load->view("events/shopnganluong/test", $this->data, true);
    }

    //Shop
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

        echo $this->load->view("events/shopnganluong/index", $this->data, true);
    }

    function gift_type_list() {
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

        //Non public
        if (!in_array($mobo_id, $this->mobo_id_test)) {
            $gift_type_list = $this->shopnganluong->gift_type_list();
        } else {
            $gift_type_list = $this->shopnganluong->gift_type_list_all();
        }

        $this->output->set_output(json_encode($gift_type_list));
    }

    function exchange_gift_shop() {
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

        $gift_type = $_GET["id"];

        if ($gift_type == 3) {
            //VIP Gift Pakage
            $data["gift_list"] = $this->shopnganluong->get_gift_pakage_list_by_type($gift_type);
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
            $api = new GameFullAPI();
            $user_info = $api->get_user_info($this->service_name, $mobo_service_id, $server_id);

            //var_dump($user_info); die;            
            $this->data["user_vip_point"] = $user_info[0]['vipPoint'];

            $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
            echo $this->load->view("events/shopnganluong/doiquapakage_shop", $this->data, true);
        } else
        if ($gift_type == 4) {
            //Special Gift Pakage
            $data["gift_list"] = $this->shopnganluong->get_gift_pakage_special_list_by_type($gift_type);
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
            echo $this->load->view("events/shopnganluong/doiquapakage_s_shop", $this->data, true);
        } else {
            $data["gift_list"] = $this->shopnganluong->get_gift_list_by_type($gift_type);
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
            echo $this->load->view("events/shopnganluong/doiqua_shop", $this->data, true);
        }
    }

    function exchange_gift_by_shop() {
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
                $datauser = $this->shopnganluong->user_check_point_exist($char_id, $server_id, $mobo_service_id);

                if (count($datauser) > 0) {
                    //Check Gift Valid
                    $gift_details = $this->shopnganluong->get_gift_details($id);

                    if (count($gift_details) > 0) {
                        foreach ($gift_details as $key => $value) {
                            //Check Server Valid
                            $server_list = explode(";", $value["server_list"]);
                            if (!in_array($server_id, $server_list)) {
                                $result["code"] = "-1";
                                $result["message"] = "Dữ liệu quà không hợp lệ* !";
                            } else {
                                //Check Max Buy 
                                $check_max_exchange = $this->shopnganluong->get_total_gift_exchange_shop($server_id, $mobo_service_id, $id);
                                if ($value["gift_buy_max"] != 0 && (($check_max_exchange[0]["TotalExchange"] + $item_quantity) >= $value["gift_buy_max"])) {
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
                                            $result["message"] = "Số dư Ngân Lượng không đủ !";
                                        } else {
                                            //Add Gift Exchange History
                                            $userdata_p["user_id"] = $value["id"];
                                            $userdata_p["item_ex_id"] = $id;
                                            $userdata_p["exchange_gift_point"] = $gift_price;
                                            $userdata_p["exchange_gift_date"] = Date('Y-m-d H:i:s');
                                            $i_id = $this->shopnganluong->insert_id("event_shopnganluong_gift_exchange_history", $userdata_p);

                                            if ($i_id > 0) {
                                                //Update Point
                                                if ($this->shopnganluong->update_point($char_id, $server_id, $mobo_service_id, $gift_price) > 0) {
                                                    //SEND Item
                                                    $item_create[] = array("item_id" => (int) $item_id, "count" => (int) $item_quantity);
                                                    $data_result = $this->senditemforgame($userdata_p, $item_create, "Chúc mừng bạn nhận được quà", "Quà Shop Ngân Lượng");
                                                    $this->shopnganluong->update_exchange_history($i_id, json_encode($item_create), json_encode($data_result), $item_quantity);

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

    function exchange_gift_pakage_by_shop() {
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
                $datauser = $this->shopnganluong->user_check_point_exist($char_id, $server_id, $mobo_service_id);

                if (count($datauser) > 0) {
                    //Check Gift Valid
                    $gift_details = $this->shopnganluong->get_gift_pakage_details($id);

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
                                $api = new GameFullAPI();
                                $user_info = $api->get_user_info($this->service_name, $mobo_service_id, $server_id);

                                if ($user_info[0]['vipPoint'] < $value["gift_vip_point"]) {
                                    $result["code"] = "-1";
                                    $result["message"] = "Bạn phải đạt '" . $this->return_vip_string($value["gift_vip_point"]) . "' để có thể đổi quà !";
                                    $this->output->set_output(json_encode($result));
                                    return;
                                }

                                //Check Max Buy 
                                $check_max_exchange = $this->shopnganluong->get_total_gift_pakage_exchange_shop($server_id, $mobo_service_id, $id);
                                if ($value["gift_buy_max"] != 0 && (($check_max_exchange[0]["TotalExchange"]) >= $value["gift_buy_max"])) {
                                    $result["code"] = "-1";
                                    $result["message"] = "Bạn chỉ được đổi tối đa '" . $value["gift_buy_max"] . " " . $value["gift_name"] . "' trong ngày!";
                                } else {
                                    $gift_price = $value["gift_price"];

                                    foreach ($datauser as $key => $value) {
                                        //Check Point Valid                        
                                        if ($gift_price > $value["user_point"]) {
                                            $result["code"] = "-1";
                                            $result["message"] = "Số dư Ngân Lượng không đủ !";
                                        } else {
                                            //Send Gift API 
                                            //Add Gift Exchange History
                                            $userdata_p["user_id"] = $value["id"];
                                            $userdata_p["item_ex_id"] = $id;
                                            $userdata_p["exchange_gift_point"] = $gift_price;
                                            $userdata_p["exchange_gift_date"] = Date('Y-m-d H:i:s');
                                            $i_id = $this->shopnganluong->insert_id("event_shopnganluong_gift_pakage_exchange_history", $userdata_p);

                                            if ($i_id > 0) {
                                                //Update Point
                                                if ($this->shopnganluong->update_point($char_id, $server_id, $mobo_service_id, $gift_price) > 0) {
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

                                                    $data_result = $this->senditemforgame($userdata_p, $item1, "Chúc mừng bạn nhận được quà", "Quà Shop Ngân Lượng");
                                                    $this->shopnganluong->update_exchange_pakage_history($i_id, json_encode($item1), json_encode($data_result));

                                                    $result["code"] = "0";
                                                    $result["message"] = "Đổi quà thành công !";
                                                } else {
                                                    $result["code"] = "-1";
                                                    $result["message"] = "Đổi quà thất bại, vui lòng thử lại!*";
                                                }
                                            } else {
                                                $result["code"] = "-1";
                                                $result["message"] = "Đổi quà thất bại, vui lòng thử lại!**";
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
                $datauser = $this->shopnganluong->user_check_point_exist($char_id, $server_id, $mobo_service_id);

                if (count($datauser) > 0) {
                    //Check Gift Valid
                    $gift_details = $this->shopnganluong->get_gift_pakage_details($id);

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
                                    $check_buy_number = $this->shopnganluong->check_gift_buy_request($server_id, $mobo_service_id, $check_number, $value["gift_date_start"], $value["gift_date_end"]);

                                    if ($check_buy_number[0]['TotalExchange'] == 0) {
                                        $result["code"] = "-1";
                                        $result["message"] = "Bạn phải mua '[Gói " . $value["gift_number_request"] . "]' trước  !";
                                        $this->output->set_output(json_encode($result));
                                        return;
                                    }
                                }

                                //Check Max Buy In Date
                                $check_max_exchange = $this->shopnganluong->get_total_gift_pakage_special_exchange_shop($server_id, $mobo_service_id, $id, $value["gift_date_start"], $value["gift_date_end"]);
                                if ($value["gift_buy_max"] != 0 && (($check_max_exchange[0]["TotalExchange"]) >= $value["gift_buy_max"])) {
                                    $gift_date_start = new DateTime($value["gift_date_start"]);
                                    $gift_date_end = new DateTime($value["gift_date_end"]);

                                    $result["code"] = "-1";
                                    $result["message"] = "Bạn chỉ được đổi tối đa '" . $value["gift_buy_max"] . " " . $value["gift_name"] . "' "
                                            . "từ '" . $gift_date_start->format('d-m-Y H:i:s') . "' đến '" . $gift_date_end->format('d-m-Y H:i:s') . "' !";
                                } else {
                                    $gift_price = $value["gift_price"];

                                    foreach ($datauser as $key => $value) {
                                        //Check Point Valid                        
                                        if ($gift_price > $value["user_point"]) {
                                            $result["code"] = "-1";
                                            $result["message"] = "Số dư Ngân Lượng không đủ !";
                                        } else {
                                            //Send Gift API 
                                            //Add Gift Exchange History
                                            $userdata_p["user_id"] = $value["id"];
                                            $userdata_p["item_ex_id"] = $id;
                                            $userdata_p["exchange_gift_point"] = $gift_price;
                                            $userdata_p["exchange_gift_date"] = Date('Y-m-d H:i:s');
                                            $userdata_p["gift_number_request"] = $gift_details[0]["gift_number_request"];

                                            $i_id = $this->shopnganluong->insert_id("event_shopnganluong_gift_pakage_exchange_history", $userdata_p);

                                            if ($i_id > 0) {
                                                //Update Point
                                                if ($this->shopnganluong->update_point($char_id, $server_id, $mobo_service_id, $gift_price) > 0) {
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

                                                    $data_result = $this->senditemforgame($userdata_p, $item1, "Chúc mừng bạn nhận được quà", "Quà Shop Ngân Lượng");
                                                    $this->shopnganluong->update_exchange_pakage_history($i_id, json_encode($item1), json_encode($data_result));

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

    //NapThe
    public function napthe_shop() {
//        echo "Tính năng chưa mở, bạn vui lòng quay lại sau.";
//            die;

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

        //User Point
        $this->data["user_point"] = $this->user_point($char_id, $server_id, $mobo_service_id, $mobo_id, $char_name);

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");

        if (in_array($user->mobo_id, $this->mobo_id_test)) {
            echo $this->load->view("events/shopnganluong/napthe_shop_new", $this->data, true);
        } else {
            echo $this->load->view("events/shopnganluong/napthe_shop", $this->data, true);
        }
    }

    //DoiThe
    function card_exchange() {
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

            $value = $_GET["value"];
            $type = $_GET["type"];

            if (!$this->getSession($mobo_service_id, $server_id)) {
                $result["code"] = "-1";
                $result["message"] = "Bạn đang đăng nhập trên một máy khác.";
            } else {
                //Check Valid 
                $nganluong = 0;
                $value = intval($value);
                if ($value == 10000) {
                    $nganluong = 115;
                }
                if ($value == 20000) {
                    $nganluong = 230;
                }
                if ($value == 50000) {
                    $nganluong = 575;
                }
                if ($value == 100000) {
                    $nganluong = 1150;
                }
                if ($value == 200000) {
                    $nganluong = 2300;
                }
                if ($value == 500000) {
                    $nganluong = 5750;
                }

                if ($nganluong == 0) {
                    $result["code"] = "-1";
                    $result["message"] = "Mệnh giá thẻ không hợp lệ.";
                } else {
                    //Check Point Valid
                    $datauser = $this->shopnganluong->user_check_point_exist($char_id, $server_id, $mobo_service_id);
                    if (count($datauser) <= 0) {
                        $result["code"] = "-1";
                        $result["message"] = "Không lấy được thông tin Ngân Lượng, vui lòng thử lại !";
                    } else {
                        if ($datauser[0]["user_point"] < $nganluong) {
                            $result["code"] = "-1";
                            $result["message"] = "Ngân Lượng của bạn không đủ để đổi thẻ mệnh giá '" . $value . "' !";
                        } else {
                            //Check 10M
                            $check_10m = $this->shopnganluong->get_card_total_exchange_history();
                            if (($check_10m[0]["Total"] + $value) >= $this->max_card_exchange) {
                                $result["code"] = "-1";
                                $result["message"] = "Không thể đổi thẻ trong lúc này, bạn vui lòng quay lại sau !";
                            } else {
                                //Update Point
                                if ($this->shopnganluong->update_point($char_id, $server_id, $mobo_service_id, $nganluong) == 0) {
                                    $result["code"] = "-1";
                                    $result["message"] = "Đổi thẻ thất bại, vui lòng thử lại !";
                                } else {
                                    //Addd Card Exchange History                                            
                                    $userdata_p["char_id"] = $char_id;
                                    $userdata_p["server_id"] = $server_id;
                                    $userdata_p["char_name"] = $char_name;
                                    $userdata_p["mobo_service_id"] = $mobo_service_id;

                                    $userdata_p["exchange_card_date"] = Date('Y-m-d H:i:s');
                                    $userdata_p["exchange_card_point"] = $nganluong;
                                    $userdata_p["card_status"] = 0;
                                    $userdata_p["card_type"] = $type;
                                    $userdata_p["card_value"] = $value;

                                    $i_id = $this->shopnganluong->insert_id("event_shopnganluong_card_exchange_history", $userdata_p);

                                    if ($i_id > 0) {
                                        //Send Card 
                                        $key_card_api = "rGPDFbWasd@$2WoN";
                                        $to_staff = $mobo_id . '_' . $mobo_service_id;
                                        $token_string = $i_id . $type . $value . '1' . 'real' . 'app_mgh' . $to_staff . 'mem_mgh_event_2017' . 'mgh' . 'rGPDFbWasd@$2WoN';
                                        //echo $token_string; die;
                                        $token = md5($token_string);
                                        $url_getcard = "http://payment.gomobi.vn/index.php/cardtest/eventcardgate?id=" . $i_id . "&supplier=" . $type . "&value=" . $value . "&amount=1&env=real&from_staff=app_mgh&to_staff=" . $to_staff . "&type=mem_mgh_event_2017&subtype=mgh&token=" . $token;
                                        //echo $url_getcard; die;

                                        $data_result = $this->call_api_get($url_getcard);
                                        $card_result = json_decode($data_result, true);
                                        //var_dump($card_result);die;
                                        //$card_result = json_decode('{"code":1,"data":[{"serial":"sbx0003014","pin":"5158079342","value":"10000"}],"message":null}', true);
                                        //var_dump($card_result["data"][0]["serial"]);die;

                                        if ($card_result["code"] == 1) {
                                            //Update Card Exchange History   
                                            $s_pin_encode = $this->encrypt($card_result["data"][0]["pin"], $key_card_api);
                                            $s_serial_encode = $this->encrypt($card_result["data"][0]["serial"], $key_card_api);

                                            //echo $this->decrypt("g6N8iHR9m4qLoQ==", $key_card_api); die;                                        
                                            $this->shopnganluong->update_card_exchange_history($i_id, $s_pin_encode, $s_serial_encode, 1, $card_result["message"], $data_result);

                                            $result["code"] = "0";
                                            $result["message"] = "Đổi thẻ thành công:<br />-Mệnh giá: <strong>" . $value . "</strong> <br />- Mã thẻ: <span style='font-weight: bold; color: #1649E8;'>" . $this->decrypt($this->encrypt($card_result["data"][0]["pin"], $key_card_api), $key_card_api) . "</span><br /> - Serial: <span style='font-weight: bold; color: #1649E8;'>" . $this->decrypt($this->encrypt($card_result["data"][0]["serial"], $key_card_api), $key_card_api) . "</span><br /><br />Bạn có thể xem lại mã thẻ và serial trong lịch sử đổi thẻ.";
                                        } else {
                                            $this->shopnganluong->update_card_exchange_history($i_id, null, null, 0, $card_result["message"], $data_result);
                                            //Fail restore point
                                            $this->shopnganluong->add_point($char_id, $server_id, $mobo_service_id, $nganluong);
                                            $result["code"] = "-1";
                                            $result["message"] = "Đổi thẻ thất bại, vui lòng thử lại** !";
                                        }
                                        //$result["code"] = "0";
                                        //$result["message"] = $card_result;                                                            
                                    } else {
                                        //Fail restore point
                                        $this->shopnganluong->add_point($char_id, $server_id, $mobo_service_id, $nganluong);
                                        $result["code"] = "-1";
                                        $result["message"] = "Đổi thẻ thất bại, vui lòng thử lại* !";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->output->set_output(json_encode($result));
    }

    function card_exchange_new() {
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

            $value = $_GET["value"];
            $type = $_GET["type"];

            if (!$this->getSession($mobo_service_id, $server_id)) {
                $result["code"] = "-1";
                $result["message"] = "Bạn đang đăng nhập trên một máy khác.";
            } else {
                //Check Valid 
                $nganluong = 0;
                $value = intval($value);
                if ($value == 10000) {
                    $nganluong = 115;
                }
                if ($value == 20000) {
                    $nganluong = 230;
                }
                if ($value == 50000) {
                    $nganluong = 575;
                }
                if ($value == 100000) {
                    $nganluong = 1150;
                }
                if ($value == 200000) {
                    $nganluong = 2300;
                }
                if ($value == 500000) {
                    $nganluong = 5750;
                }

                if ($nganluong == 0) {
                    $result["code"] = "-1";
                    $result["message"] = "Mệnh giá thẻ không hợp lệ.";
                } else {
                    //Check Point Valid
                    $datauser = $this->shopnganluong->user_check_point_exist($char_id, $server_id, $mobo_service_id);
                    if (count($datauser) <= 0) {
                        $result["code"] = "-1";
                        $result["message"] = "Không lấy được thông tin Ngân Lượng, vui lòng thử lại !";
                    } else {
                        if ($datauser[0]["user_point"] < $nganluong) {
                            $result["code"] = "-1";
                            $result["message"] = "Ngân Lượng của bạn không đủ để đổi thẻ mệnh giá '" . $value . "' !";
                        } else {
                            //Check 10M
                            $check_10m = $this->shopnganluong->get_card_total_exchange_history();
                            if (($check_10m[0]["Total"] + $value) >= $this->max_card_exchange && (!in_array($mobo_id, $this->mobo_id_test))) {
                                $result["code"] = "-1";
                                $result["message"] = "Không thể đổi thẻ trong lúc này, bạn vui lòng quay lại sau !";
                            } else {
                                //Update Point
                                if ($this->shopnganluong->update_point($char_id, $server_id, $mobo_service_id, $nganluong) == 0) {
                                    $result["code"] = "-1";
                                    $result["message"] = "Đổi thẻ thất bại, vui lòng thử lại !";
                                } else {
                                    //Addd Card Exchange History                                            
                                    $userdata_p["char_id"] = $char_id;
                                    $userdata_p["server_id"] = $server_id;
                                    $userdata_p["char_name"] = $char_name;
                                    $userdata_p["mobo_service_id"] = $mobo_service_id;

                                    $userdata_p["exchange_card_date"] = Date('Y-m-d H:i:s');
                                    $userdata_p["exchange_card_point"] = $nganluong;
                                    $userdata_p["card_status"] = 0;
                                    $userdata_p["card_type"] = $type;
                                    $userdata_p["card_value"] = $value;

                                    $i_id = $this->shopnganluong->insert_id("event_shopnganluong_card_exchange_history", $userdata_p);

                                    if ($i_id > 0) {
                                        //Send Card 
                                        $key_card_api = "IDpCJtb6Go10vKGRy5DQ";

                                        $array = array(
                                            "mobo_id" => $mobo_id
                                            , "mobo_service_id" => $mobo_service_id
                                            , "username" => $char_name
                                            , "character_id" => $char_id
                                            , "character_name" => $char_name
                                            , "supplier" => $type
                                            , "service_id" => "0"
                                            , "value" => $value
                                            , "amount" => 1
                                            , "service_name" => $this->service_name
                                            , "transid" => time()
                                            , "env" => "sandbox"
                                        );

                                        $token = md5(implode("", $array) . "IDpCJtb6Go10vKGRy5DQ");
                                        $array["app"] = "game";
                                        $array["token"] = $token;

                                        $url = "http://gapi.mobo.vn/?control=recharge&func=buy_card&" . http_build_query($array);

                                        $ch = curl_init();
                                        curl_setopt($ch, CURLOPT_URL, $url);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                        $data_result = curl_exec($ch);
                                        curl_close($ch);

                                        $card_result = json_decode($data_result, true);

                                        if ($card_result["code"] == '0' && $card_result["desc"] == 'BUY_CARD_SUCCESS' && !empty($card_result["data"])) {
                                            //Update Card Exchange History 
                                            $s_pin_encode = $this->encrypt($card_result["data"]["list"][0]["pin"], $key_card_api);
                                            $s_serial_encode = $this->encrypt($card_result["data"]["list"][0]["serial"], $key_card_api);

                                            //echo $this->decrypt("g6N8iHR9m4qLoQ==", $key_card_api); die;   
                                            $message_string_card = $card_result["data"]["list"][0]["title"] . " - print_date: " . $card_result["data"]["list"][0]["print_date"] . " - id: " . $card_result["data"]["list"][0]["id"];
                                            $this->shopnganluong->update_card_exchange_history($i_id, $s_pin_encode, $s_serial_encode, 1, $message_string_card, $data_result);

                                            $result["code"] = "0";
                                            $result["message"] = "Đổi thẻ thành công:<br />-Mệnh giá: <strong>" . $value . "</strong> <br />- Mã thẻ: <span style='font-weight: bold; color: #1649E8;'>" . $this->decrypt($this->encrypt($card_result["data"]["list"][0]["pin"], $key_card_api), $key_card_api) . "</span><br /> - Serial: <span style='font-weight: bold; color: #1649E8;'>" . $this->decrypt($this->encrypt($card_result["data"]["list"][0]["serial"], $key_card_api), $key_card_api) . "</span><br /><br />Bạn có thể xem lại mã thẻ và serial trong lịch sử đổi thẻ.";
                                        } else {
                                            $this->shopnganluong->update_card_exchange_history($i_id, null, null, 0, $card_result["message"], $data_result);
                                            //Fail restore point
                                            $this->shopnganluong->add_point($char_id, $server_id, $mobo_service_id, $nganluong);
                                            $result["code"] = "-1";
                                            $result["message"] = "Đổi thẻ thất bại, vui lòng thử lại** !";
                                        }
                                        //$result["code"] = "0";
                                        //$result["message"] = $card_result;                                                            
                                    } else {
                                        //Fail restore point
                                        $this->shopnganluong->add_point($char_id, $server_id, $mobo_service_id, $nganluong);
                                        $result["code"] = "-1";
                                        $result["message"] = "Đổi thẻ thất bại, vui lòng thử lại* !";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->output->set_output(json_encode($result));
    }

    //History
    function exchange_gate_card_shop() {
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

        //User Point
        $this->data["user_point"] = $this->user_point($char_id, $server_id, $mobo_service_id, $mobo_id, $char_name);

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");

        if (in_array($user->mobo_id, $this->mobo_id_test)) {
            echo $this->load->view("events/shopnganluong/doithegate_shop_new", $this->data, true);
        } else {
            echo $this->load->view("events/shopnganluong/doithegate_shop", $this->data, true);
        }
    }

    function get_charging_history() {
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

        $data["charging_history"] = $this->shopnganluong->get_charging_history($mobo_service_id);
        $this->data["charging_history"] = $data["charging_history"];

        echo $this->load->view("events/shopnganluong/lichsunapthe", $this->data, true);
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

        $tournament_id = $_GET["id"];

        $data["gift_exchange_history"] = $this->shopnganluong->get_gift_exchange_history($char_id, $server_id, $mobo_service_id);
        $this->data["gift_exchange_history"] = $data["gift_exchange_history"];

        $data["history_top"] = $this->shopnganluong->get_exchange_history_new_top($tournament_id, $char_id, $server_id, $mobo_service_id);
        $this->data["history_top"] = $data["history_top"];

        $data["gift_outgame_exchange_history"] = $this->shopnganluong->get_gift_outgame_exchange_history($char_id, $server_id, $mobo_service_id);
        $this->data["gift_outgame_exchange_history"] = $data["gift_outgame_exchange_history"];

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("events/shopnganluong/history", $this->data, true);
    }

    function card_exchange_history() {
        //unset($_SESSION["oauthtoken"]);
        //echo "Truy cập không hợp lệ"; //$this->load->view("deny", "", true);
        //exit();

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

        if (in_array($mobo_id, $this->mobo_id_test)) {
            $key_card_api = "IDpCJtb6Go10vKGRy5DQ";
        } else {
            $key_card_api = "rGPDFbWasd@$2WoN";
        }

        $list_card = $this->shopnganluong->card_exchange_history($mobo_service_id);
        $list_card_decode = array();

        foreach ($list_card as $key => $value) {
            array_push($list_card_decode, Array("id" => $value["id"], "card_type" => $value["card_type"],
                "card_value" => $value["card_value"], "card_code" => $this->decrypt($value["card_code"], $key_card_api)
                , "card_serial" => $this->decrypt($value["card_serial"], $key_card_api), "exchange_card_date" => $value["exchange_card_date"]));
        }

        $data["card_exchange_history"] = $this->shopnganluong->card_exchange_history($mobo_service_id);
        $this->data["card_exchange_history"] = $list_card_decode;

        echo $this->load->view("events/shopnganluong/lichsudoithe", $this->data, true);
    }

    //API
    function user_valid() {
        $params = $this->input->get();
        //var_dump($params); die;
        $needle = array("char_id", "server_id", "mobo_service_id", "mobo_id", "char_name");

        if (!is_required($params, $needle) == TRUE) {
            $R["result"] = -1;
            $R["message"] = 'INVALID_PARAMS !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        $token = $params['token'];
        unset($params["token"]);

        $tokendata = implode("", $params);
        $valid = md5($tokendata . $this->secret_key);

        if ($token != $valid) {
            $R["result"] = -1;
            $R["message"] = 'INVALID_TOKEN !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        $char_id = $_GET['char_id'];
        $server_id = $_GET['server_id'];
        $mobo_service_id = $_GET['mobo_service_id'];
        $mobo_id = $_GET['mobo_id'];
        $char_name = $_GET['char_name'];

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

                $R["result"] = 0;
                $R["message"] = 'USER VALID';
                $R["user_point"] = $value["user_point"];
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;
            }
        } else {
            //Insert User Point
            $userdata_p["char_id"] = $char_id;
            $userdata_p["server_id"] = $server_id;
            $userdata_p["char_name"] = $char_name;
            $userdata_p["mobo_service_id"] = $mobo_service_id;
            $userdata_p["mobo_id"] = $mobo_id;
            $userdata_p["user_point"] = 0;

            $i_adduser = $this->shopnganluong->insert_id("event_shopnganluong_point", $userdata_p);

            if ($i_adduser > 0) {
                $R["result"] = 0;
                $R["message"] = 'USER VALID';
                $R["user_point"] = 0;
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;
            } else {
                $R["result"] = -1;
                $R["message"] = 'INVALID_USER !';
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;
            }
        }
    }

    function add_nl() {
        $params = $this->input->get();
        //var_dump($params); die;
        $needle = array("char_id", "server_id", "mobo_service_id", "mobo_id", "char_name", "nl_quantity", "transaction_id", "event_key");

        if (!is_required($params, $needle) == TRUE) {
            $R["result"] = -1;
            $R["message"] = 'INVALID_PARAMS !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        $token = $params['token'];
        unset($params["token"]);

        $tokendata = implode("", $params);
        $valid = md5($tokendata . $this->secret_key);

        if ($token != $valid) {
            $R["result"] = -1;
            $R["message"] = 'INVALID_TOKEN !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        $char_id = $_GET['char_id'];
        $server_id = $_GET['server_id'];
        $mobo_service_id = $_GET['mobo_service_id'];
        $mobo_id = $_GET['mobo_id'];
        $char_name = $_GET['char_name'];

        //////////
        $nl_quantity = $_GET['nl_quantity'];
        $transaction_id = $_GET['transaction_id'];
        $event_key = $_GET['event_key'];

        //Check Valid Event Key        
        if (!in_array($event_key, $this->event_key_nl)) {
            $R["result"] = -1;
            $R["message"] = 'INVALID EVENT KEY !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        //Check Exist Token Process
        $check_exist_token = $this->shopnganluong->check_token_process($token);
        if (count($check_exist_token) > 0) {
            $R["result"] = -1;
            $R["message"] = 'TOKEN PROCESS DUPLICATE !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        //Add NL Process
        //Add LOG
        //Insert Add Point History                    
        $userdata_add_p["char_id"] = $char_id;
        $userdata_add_p["server_id"] = $server_id;
        $userdata_add_p["char_name"] = $char_name;
        $userdata_add_p["mobo_id"] = $mobo_id;
        $userdata_add_p["mobo_service_id"] = $mobo_service_id;
        $userdata_add_p["point_add"] = $nl_quantity;
        $userdata_add_p["update_date"] = Date('Y-m-d H:i:s');
        $userdata_add_p["payment_type"] = "api_add";
        $userdata_add_p["token_process"] = $token;
        $userdata_add_p["transaction_id"] = $transaction_id;
        $userdata_add_p["event_key"] = $event_key;

        $i_add_history = $this->shopnganluong->insert_id("event_shopnganluong_point_add_history", $userdata_add_p);

        if ($i_add_history == 0) {
            $R["result"] = -1;
            $R["message"] = 'ADD NL FAIL* !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        } else {
            $i_add_point = $this->shopnganluong->add_point($char_id, $server_id, $mobo_service_id, $nl_quantity);
            if ($i_add_point > 0) {
                //Update Status
                $this->shopnganluong->update_add_point_status_history($i_add_history, 1);
                $R["result"] = 0;
                $R["message"] = 'ADD NL SUCCESSFULLY !';
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;
            } else {
                //Update Status
                $this->shopnganluong->update_add_point_status_history($i_add_history, 0);
                $R["result"] = -1;
                $R["message"] = 'ADD NL FAIL** !';
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;
            }
        }
    }

    function minus_nl() {
        $params = $this->input->get();
        //var_dump($params); die;
        $needle = array("char_id", "server_id", "mobo_service_id", "mobo_id", "char_name", "nl_quantity", "transaction_id", "event_key");

        if (!is_required($params, $needle) == TRUE) {
            $R["result"] = -1;
            $R["message"] = 'INVALID_PARAMS !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        $token = $params['token'];
        unset($params["token"]);

        $tokendata = implode("", $params);
        $valid = md5($tokendata . $this->secret_key);

        if ($token != $valid) {
            $R["result"] = -1;
            $R["message"] = 'INVALID_TOKEN !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        $char_id = $_GET['char_id'];
        $server_id = $_GET['server_id'];
        $mobo_service_id = $_GET['mobo_service_id'];
        $mobo_id = $_GET['mobo_id'];
        $char_name = $_GET['char_name'];

        //////////
        $nl_quantity = $_GET['nl_quantity'];
        $transaction_id = $_GET['transaction_id'];
        $event_key = $_GET['event_key'];

        //Check Valid Event Key        
        if (!in_array($event_key, $this->event_key_nl)) {
            $R["result"] = -1;
            $R["message"] = 'INVALID EVENT KEY !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        //Check Exist Token Process
        $check_exist_token = $this->shopnganluong->check_token_process($token);
        if (count($check_exist_token) > 0) {
            $R["result"] = -1;
            $R["message"] = 'TOKEN PROCESS DUPLICATE !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        //Add NL Process
        //Add LOG
        //Insert Add Point History                    
        $userdata_add_p["char_id"] = $char_id;
        $userdata_add_p["server_id"] = $server_id;
        $userdata_add_p["char_name"] = $char_name;
        $userdata_add_p["mobo_id"] = $mobo_id;
        $userdata_add_p["mobo_service_id"] = $mobo_service_id;
        $userdata_add_p["point_add"] = $nl_quantity;
        $userdata_add_p["update_date"] = Date('Y-m-d H:i:s');
        $userdata_add_p["payment_type"] = "api_minus";
        $userdata_add_p["token_process"] = $token;
        $userdata_add_p["transaction_id"] = $transaction_id;
        $userdata_add_p["event_key"] = $event_key;

        $i_add_history = $this->shopnganluong->insert_id("event_shopnganluong_point_add_history", $userdata_add_p);

        if ($i_add_history == 0) {
            $R["result"] = -1;
            $R["message"] = 'MINUS NL FAIL* !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        } else {
            $i_minus_point = $this->shopnganluong->update_point($char_id, $server_id, $mobo_service_id, $nl_quantity);
            if ($i_minus_point > 0) {
                //Update Status
                $this->shopnganluong->update_add_point_status_history($i_add_history, 1);
                $R["result"] = 0;
                $R["message"] = 'MINUS NL SUCCESSFULLY !';
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;
            } else {
                //Update Status
                $this->shopnganluong->update_add_point_status_history($i_add_history, 0);
                $R["result"] = -1;
                $R["message"] = 'MINUS NL FAIL** !';
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;
            }
        }
    }

    function api_gift_type_list() {
        $gift_type_list = $this->shopnganluong->gift_type_list();
        $this->output->set_output(json_encode($gift_type_list));
    }

    function api_get_gift_pakage_list_by_type() {
        $params = $this->input->get();
        //var_dump($params); die;
        $needle = array("gift_type", "server_id");

        if (!is_required($params, $needle) == TRUE) {
            $R["result"] = -1;
            $R["message"] = 'INVALID_PARAMS !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        $token = $params['token'];
        unset($params["token"]);

        $tokendata = implode("", $params);
        $valid = md5($tokendata . $this->secret_key);

        if ($token != $valid) {
            $R["result"] = -1;
            $R["message"] = 'INVALID_TOKEN !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        $gift_type = $_GET["gift_type"];
        $server_id = $_GET["server_id"];

        $data["gift_list"] = $this->shopnganluong->get_gift_pakage_list_by_type($gift_type);
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


        $this->output->set_output(json_encode($gift_filter));
    }

    function api_get_gift_list_by_type() {
        $params = $this->input->get();
        //var_dump($params); die;
        $needle = array("gift_type", "server_id");

        if (!is_required($params, $needle) == TRUE) {
            $R["result"] = -1;
            $R["message"] = 'INVALID_PARAMS !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        $token = $params['token'];
        unset($params["token"]);

        $tokendata = implode("", $params);
        $valid = md5($tokendata . $this->secret_key);

        if ($token != $valid) {
            $R["result"] = -1;
            $R["message"] = 'INVALID_TOKEN !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        $gift_type = $_GET["gift_type"];
        $server_id = $_GET["server_id"];

        $data["gift_list"] = $this->shopnganluong->get_gift_list_by_type($gift_type);
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

        $this->output->set_output(json_encode($gift_filter));
    }

    function api_exchange_gift_pakage_by_shop() {
        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 10) {
            $R["code"] = "-1";
            $R["message"] = "Mỗi lần đổi quà phải cách nhau 10 giây.";
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        } else {
            $_SESSION["execute_time"] = time();

            $params = $this->input->get();
            $needle = array("char_id", "server_id", "mobo_service_id", "mobo_id", "char_name", "id");

            if (!is_required($params, $needle) == TRUE) {
                $R["result"] = -1;
                $R["message"] = 'INVALID_PARAMS !';
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;
            }

            $token = $params['token'];
            unset($params["token"]);

            $tokendata = implode("", $params);
            $valid = md5($tokendata . $this->secret_key);

            if ($token != $valid) {
                $R["result"] = -1;
                $R["message"] = 'INVALID_TOKEN !';
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;
            }

            $char_id = $_GET["char_id"];
            $server_id = $_GET["server_id"];
            $mobo_service_id = $_GET["mobo_service_id"];
            $mobo_id = $_GET["mobo_id"];
            $char_name = $_GET["char_name"];

            $userdata_p["char_id"] = $char_id;
            $userdata_p["server_id"] = $server_id;
            $userdata_p["mobo_service_id"] = $mobo_service_id;
            $userdata_p["mobo_id"] = $mobo_id;
            $userdata_p["char_name"] = $char_name;
            $id = $_GET["id"];

            //Check Valid Point
            $datauser = $this->shopnganluong->user_check_point_exist($char_id, $server_id, $mobo_service_id);

            if (count($datauser) > 0) {
                //Check Gift Valid
                $gift_details = $this->shopnganluong->get_gift_pakage_details($id);

                if (count($gift_details) > 0) {
                    foreach ($gift_details as $key => $value) {
                        //Check Server Valid
                        $server_list = explode(";", $value["server_list"]);
                        if (!in_array($server_id, $server_list)) {
                            $R["result"] = -1;
                            $R["message"] = 'Dữ liệu quà không hợp lệ* !';
                            $this->output->set_header('Content-type: application/json');
                            $this->output->set_output(json_encode($R));
                            return;
                        } else {
                            //Check VIP
                            //Get Game User
                            $api = new GameFullAPI();
                            $user_info = $api->get_user_info($this->service_name, $mobo_service_id, $server_id);

                            if ($user_info[0]['vipPoint'] < $value["gift_vip_point"]) {
                                $R["result"] = -1;
                                $R["message"] = "Bạn phải đạt '" . $this->return_vip_string($value["gift_vip_point"]) . "' để có thể đổi quà !";
                                $this->output->set_header('Content-type: application/json');
                                $this->output->set_output(json_encode($R));
                                return;
                            }

                            //Check Max Buy 
                            $check_max_exchange = $this->shopnganluong->get_total_gift_pakage_exchange_shop($server_id, $mobo_service_id, $id);
                            if ($value["gift_buy_max"] != 0 && (($check_max_exchange[0]["TotalExchange"]) >= $value["gift_buy_max"])) {
                                $R["result"] = -1;
                                $R["message"] = "Bạn chỉ được đổi tối đa '" . $value["gift_buy_max"] . " " . $value["gift_name"] . "' trong ngày!";
                                $this->output->set_header('Content-type: application/json');
                                $this->output->set_output(json_encode($R));
                                return;
                            } else {
                                $gift_price = $value["gift_price"];

                                foreach ($datauser as $key => $value) {
                                    //Check Point Valid                        
                                    if ($gift_price > $value["user_point"]) {
                                        $R["result"] = -1;
                                        $R["message"] = "Số dư Ngân Lượng không đủ !";
                                        $this->output->set_header('Content-type: application/json');
                                        $this->output->set_output(json_encode($R));
                                        return;
                                    } else {
                                        //Send Gift API 
                                        //Add Gift Exchange History
                                        $userdata_p["user_id"] = $value["id"];
                                        $userdata_p["item_ex_id"] = $id;
                                        $userdata_p["exchange_gift_point"] = $gift_price;
                                        $userdata_p["exchange_gift_date"] = Date('Y-m-d H:i:s');
                                        $i_id = $this->shopnganluong->insert_id_api("event_shopnganluong_gift_pakage_exchange_history", $userdata_p);

                                        if ($i_id > 0) {
                                            //Update Point
                                            if ($this->shopnganluong->update_point($char_id, $server_id, $mobo_service_id, $gift_price) > 0) {
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

                                                $data_result = $this->senditemforgame($userdata_p, $item1, "Chúc mừng bạn nhận được quà", "Quà Shop Ngân Lượng");
                                                $this->shopnganluong->update_exchange_pakage_history($i_id, json_encode($item1), json_encode($data_result));

                                                $R["result"] = 0;
                                                $R["message"] = "Đổi quà thành công !";
                                                $this->output->set_header('Content-type: application/json');
                                                $this->output->set_output(json_encode($R));
                                                return;
                                            } else {
                                                $R["result"] = -1;
                                                $R["message"] = "Đổi quà thất bại, vui lòng thử lại!*";
                                                $this->output->set_header('Content-type: application/json');
                                                $this->output->set_output(json_encode($R));
                                            }
                                        } else {
                                            $R["result"] = -1;
                                            $R["message"] = "Đổi quà thất bại, vui lòng thử lại!**";
                                            $this->output->set_header('Content-type: application/json');
                                            $this->output->set_output(json_encode($R));
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $R["result"] = -1;
                    $R["message"] = "Dữ liệu quà không hợp lệ** !";
                    $this->output->set_header('Content-type: application/json');
                    $this->output->set_output(json_encode($R));
                }
            } else {
                //User Point Not Found 
                $R["result"] = -1;
                $R["message"] = "Không có dữ liệu người dùng !";
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
            }
        }

        $this->output->set_output(json_encode($R));
    }

    function api_exchange_gift_pakage_special_by_shop() {
        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 10) {
            $R["code"] = "-1";
            $R["message"] = "Mỗi lần đổi quà phải cách nhau 10 giây.";
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        } else {
            $_SESSION["execute_time"] = time();

            $params = $this->input->get();
            $needle = array("char_id", "server_id", "mobo_service_id", "mobo_id", "char_name", "id");

            if (!is_required($params, $needle) == TRUE) {
                $R["result"] = -1;
                $R["message"] = 'INVALID_PARAMS !';
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;
            }

            $token = $params['token'];
            unset($params["token"]);

            $tokendata = implode("", $params);
            $valid = md5($tokendata . $this->secret_key);

            if ($token != $valid) {
                $R["result"] = -1;
                $R["message"] = 'INVALID_TOKEN !';
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;
            }

            $char_id = $_GET["char_id"];
            $server_id = $_GET["server_id"];
            $mobo_service_id = $_GET["mobo_service_id"];
            $mobo_id = $_GET["mobo_id"];
            $char_name = $_GET["char_name"];

            $userdata_p["char_id"] = $char_id;
            $userdata_p["server_id"] = $server_id;
            $userdata_p["mobo_service_id"] = $mobo_service_id;
            $userdata_p["mobo_id"] = $mobo_id;
            $userdata_p["char_name"] = $char_name;
            $id = $_GET["id"];

            //Check Valid Point
            $datauser = $this->shopnganluong->user_check_point_exist($char_id, $server_id, $mobo_service_id);

            if (count($datauser) > 0) {
                //Check Gift Valid
                $gift_details = $this->shopnganluong->get_gift_pakage_details($id);

                if (count($gift_details) > 0) {
                    foreach ($gift_details as $key => $value) {
                        //Check Server Valid
                        $server_list = explode(";", $value["server_list"]);
                        if (!in_array($server_id, $server_list)) {
                            $R["result"] = -1;
                            $R["message"] = 'Dữ liệu quà không hợp lệ* !';
                            $this->output->set_header('Content-type: application/json');
                            $this->output->set_output(json_encode($R));
                            return;
                        } else {
                            //Check Buy Request number
                            if ($value["gift_number_request"] > 0) {
                                $check_number = $value["gift_number_request"] - 1;
                                $check_buy_number = $this->shopnganluong->check_gift_buy_request($server_id, $mobo_service_id, $check_number, $value["gift_date_start"], $value["gift_date_end"]);

                                if ($check_buy_number[0]['TotalExchange'] == 0) {
                                    $R["result"] = -1;
                                    $R["message"] = "Bạn phải mua '[Gói " . $value["gift_number_request"] . "]' trước  !";
                                    $this->output->set_header('Content-type: application/json');
                                    $this->output->set_output(json_encode($R));
                                    return;
                                }
                            }

                            //Check Max Buy In Date
                            $check_max_exchange = $this->shopnganluong->get_total_gift_pakage_special_exchange_shop($server_id, $mobo_service_id, $id, $value["gift_date_start"], $value["gift_date_end"]);
                            if ($value["gift_buy_max"] != 0 && (($check_max_exchange[0]["TotalExchange"]) >= $value["gift_buy_max"])) {
                                $gift_date_start = new DateTime($value["gift_date_start"]);
                                $gift_date_end = new DateTime($value["gift_date_end"]);

                                $R["result"] = -1;
                                $R["message"] = "Bạn chỉ được đổi tối đa '" . $value["gift_buy_max"] . " " . $value["gift_name"] . "' "
                                        . "từ '" . $gift_date_start->format('d-m-Y H:i:s') . "' đến '" . $gift_date_end->format('d-m-Y H:i:s') . "' !";
                                $this->output->set_header('Content-type: application/json');
                                $this->output->set_output(json_encode($R));
                                return;
                            } else {
                                $gift_price = $value["gift_price"];

                                foreach ($datauser as $key => $value) {
                                    //Check Point Valid                        
                                    if ($gift_price > $value["user_point"]) {
                                        $R["result"] = -1;
                                        $R["message"] = "Số dư Ngân Lượng không đủ !";
                                        $this->output->set_header('Content-type: application/json');
                                        $this->output->set_output(json_encode($R));
                                        return;
                                    } else {
                                        //Send Gift API 
                                        //Add Gift Exchange History
                                        $userdata_p["user_id"] = $value["id"];
                                        $userdata_p["item_ex_id"] = $id;
                                        $userdata_p["exchange_gift_point"] = $gift_price;
                                        $userdata_p["exchange_gift_date"] = Date('Y-m-d H:i:s');
                                        $userdata_p["gift_number_request"] = $gift_details[0]["gift_number_request"];

                                        $i_id = $this->shopnganluong->insert_id("event_shopnganluong_gift_pakage_exchange_history", $userdata_p);

                                        if ($i_id > 0) {
                                            //Update Point
                                            if ($this->shopnganluong->update_point($char_id, $server_id, $mobo_service_id, $gift_price) > 0) {
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

                                                $data_result = $this->senditemforgame($userdata_p, $item1, "Chúc mừng bạn nhận được quà", "Quà Shop Ngân Lượng");
                                                $this->shopnganluong->update_exchange_pakage_history($i_id, json_encode($item1), json_encode($data_result));

                                                $R["result"] = 0;
                                                $R["message"] = "Đổi quà thành công !";
                                                $this->output->set_header('Content-type: application/json');
                                                $this->output->set_output(json_encode($R));
                                                return;
                                            } else {
                                                $R["result"] = -1;
                                                $R["message"] = "Đổi quà thất bại, vui lòng thử lại!";
                                                $this->output->set_header('Content-type: application/json');
                                                $this->output->set_output(json_encode($R));
                                                return;
                                            }
                                        } else {
                                            $R["result"] = -1;
                                            $R["message"] = "Đổi quà thất bại, vui lòng thử lại!*";
                                            $this->output->set_header('Content-type: application/json');
                                            $this->output->set_output(json_encode($R));
                                            return;
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $R["result"] = -1;
                    $R["message"] = "Dữ liệu quà không hợp lệ** !";
                    $this->output->set_header('Content-type: application/json');
                    $this->output->set_output(json_encode($R));
                    return;
                }
            } else {
                //User Point Not Found                
                $R["result"] = -1;
                $R["message"] = "Không có dữ liệu người dùng !";
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;
            }
        }

        $this->output->set_output(json_encode($R));
    }

    function api_exchange_gift_by_shop() {
        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 10) {
            $R["code"] = "-1";
            $R["message"] = "Mỗi lần đổi quà phải cách nhau 10 giây.";
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        } else {
            $_SESSION["execute_time"] = time();

            $params = $this->input->get();
            $needle = array("char_id", "server_id", "mobo_service_id", "mobo_id", "char_name", "id", "quantity");

            if (!is_required($params, $needle) == TRUE) {
                $R["result"] = -1;
                $R["message"] = 'INVALID_PARAMS !';
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;
            }

            $token = $params['token'];
            unset($params["token"]);

            $tokendata = implode("", $params);
            $valid = md5($tokendata . $this->secret_key);

            if ($token != $valid) {
                $R["result"] = -1;
                $R["message"] = 'INVALID_TOKEN !';
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;
            }

            $char_id = $_GET["char_id"];
            $server_id = $_GET["server_id"];
            $mobo_service_id = $_GET["mobo_service_id"];
            $mobo_id = $_GET["mobo_id"];
            $char_name = $_GET["char_name"];

            $userdata_p["char_id"] = $char_id;
            $userdata_p["server_id"] = $server_id;
            $userdata_p["mobo_service_id"] = $mobo_service_id;
            $userdata_p["mobo_id"] = $mobo_id;
            $userdata_p["char_name"] = $char_name;
            $id = $_GET["id"];
            $item_quantity = $_GET["quantity"];

            //Check Valid Point
            $datauser = $this->shopnganluong->user_check_point_exist($char_id, $server_id, $mobo_service_id);

            if (count($datauser) > 0) {
                //Check Gift Valid
                $gift_details = $this->shopnganluong->get_gift_details($id);

                if (count($gift_details) > 0) {
                    foreach ($gift_details as $key => $value) {
                        //Check Server Valid
                        $server_list = explode(";", $value["server_list"]);
                        if ($value["server_list"] != "" && !in_array($server_id, $server_list)) {
                            $R["code"] = "-1";
                            $R["message"] = "Dữ liệu quà không hợp lệ* !";
                            $this->output->set_header('Content-type: application/json');
                            $this->output->set_output(json_encode($R));
                        } else {
                            //Check Max Buy 
                            $check_max_exchange = $this->shopnganluong->get_total_gift_exchange_shop($server_id, $mobo_service_id, $id);
                            if ($value["gift_buy_max"] != 0 && (($check_max_exchange[0]["TotalExchange"] + $item_quantity) >= $value["gift_buy_max"])) {
                                $R["code"] = "-1";
                                $R["message"] = "Bạn chỉ được đổi tối đa '" . $value["gift_buy_max"] . " " . $value["gift_name"] . "'!";
                                $this->output->set_header('Content-type: application/json');
                                $this->output->set_output(json_encode($R));
                            } else {
                                $gift_price = $item_quantity * $value["gift_price"];

                                //Item Info
                                $item_id = $value["item_id"];

                                foreach ($datauser as $key => $value) {
                                    //Check Point Valid                        
                                    if ($gift_price > $value["user_point"]) {
                                        $R["code"] = "-1";
                                        $R["message"] = "Số dư Ngân Lượng không đủ !";
                                        $this->output->set_header('Content-type: application/json');
                                        $this->output->set_output(json_encode($R));
                                    } else {
                                        //Add Gift Exchange History
                                        $userdata_p["user_id"] = $value["id"];
                                        $userdata_p["item_ex_id"] = $id;
                                        $userdata_p["exchange_gift_point"] = $gift_price;
                                        $userdata_p["exchange_gift_date"] = Date('Y-m-d H:i:s');
                                        $i_id = $this->shopnganluong->insert_id("event_shopnganluong_gift_exchange_history", $userdata_p);

                                        if ($i_id > 0) {
                                            //Update Point
                                            if ($this->shopnganluong->update_point($char_id, $server_id, $mobo_service_id, $gift_price) > 0) {
                                                //SEND Item
                                                $item_create[] = array("item_id" => (int) $item_id, "count" => (int) $item_quantity);
                                                $data_result = $this->senditemforgame($userdata_p, $item_create, "Chúc mừng bạn nhận được quà", "Quà Shop Ngân Lượng");
                                                $this->shopnganluong->update_exchange_history($i_id, json_encode($item_create), json_encode($data_result), $item_quantity);

                                                $R["code"] = "0";
                                                $R["message"] = "Đổi quà thành công !";
                                                $this->output->set_header('Content-type: application/json');
                                                $this->output->set_output(json_encode($R));
                                            } else {
                                                $R["code"] = "-1";
                                                $R["message"] = "Đổi quà thất bại, vui lòng thử lại*!";
                                                $this->output->set_header('Content-type: application/json');
                                                $this->output->set_output(json_encode($R));
                                            }
                                        } else {
                                            $R["code"] = "-1";
                                            $R["message"] = "Đổi quà thất bại, vui lòng thử lại**!";
                                            $this->output->set_header('Content-type: application/json');
                                            $this->output->set_output(json_encode($R));
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $R["code"] = "-1";
                    $R["message"] = "Dữ liệu quà không hợp lệ** !";
                    $this->output->set_header('Content-type: application/json');
                    $this->output->set_output(json_encode($R));
                }
            } else {
                //User Point Not Found
                $R["code"] = "-1";
                $R["message"] = "Không có dữ liệu người dùng !";
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
            }
        }

        $this->output->set_output(json_encode($R));
    }

    function api_charging_new() {
        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 10) {
            $R["code"] = "-1";
            $R["message"] = "Mỗi lần đổi quà phải cách nhau 10 giây.";
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        $_SESSION["execute_time"] = time();

        $params = $this->input->get();
        $needle = array("char_id", "server_id", "mobo_service_id", "mobo_id", "char_name", "cardtype", "card_seri", "card_code");

        if (!is_required($params, $needle) == TRUE) {
            $R["result"] = -1;
            $R["message"] = 'INVALID_PARAMS !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        $token = $params['token'];
        unset($params["token"]);

        $tokendata = implode("", $params);
        $valid = md5($tokendata . $this->secret_key);

        if ($token != $valid) {
            $R["result"] = -1;
            $R["message"] = 'INVALID_TOKEN !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        $char_id = $_GET["char_id"];
        $server_id = $_GET["server_id"];
        $mobo_service_id = $_GET["mobo_service_id"];
        $mobo_id = $_GET["mobo_id"];
        $char_name = $_GET["char_name"];

        $cardtype = $_GET["cardtype"];
        $card_seri = $_GET["card_seri"];
        $card_code = $_GET["card_code"];

        $shopnganluong_config = $this->shopnganluong->get_shopnganluong_config();
        $this->load->library("api/cardPaymentApi", array("trans_id" => $this->transaction_id));
        //Get mobo account
        if (empty($mobo_service_id) === TRUE) {
            $R["result"] = -1;
            $R["message"] = 'Truy vấn không hợp lệ*';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        //Test    
        if (!in_array($mobo_id, $this->mobo_id_test)) {
            $R["result"] = -1;
            $R["message"] = 'Truy cập bất hợp pháp!';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        if ((count($shopnganluong_config) == 0 || $shopnganluong_config[0]["charging_status"] == 0) && ($params["mobo_id"] != '260896396' && $params["mobo_id"] != '886899541')) {
            $R["result"] = -1;
            $R["message"] = 'Hệ thống đang bảo trì, bạn vui lòng quay lại sau!';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        //New
        $array = array(
            "mobo_id" => $mobo_id
            , "mobo_service_id" => $mobo_service_id
            , "username" => $char_name
            , "character_id" => $char_id
            , "type" => $cardtype
            , "character_name" => $char_name
            , "server_id" => $server_id
            , "service_id" => "0"
            , "service_name" => $this->service_name
            , "serial" => $card_seri
            , "pin" => $card_code
            , "transid" => time()
            , "event" => "mgh2_naptheshopnganluong"
            , "env" => $this->env_s);

        $token = md5(implode("", $array) . "IDpCJtb6Go10vKGRy5DQ");
        $array["app"] = "game";
        $array["token"] = $token;

        $url = "http://gapi.mobo.vn/?control=recharge&func=verify_card&" . http_build_query($array);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data_result = curl_exec($ch);
        MeAPI_Log::writeCsv(array($url, $data_result), 'user_nap_tien');
        curl_close($ch);

        $this->log('payment', "DATA_SEND_RESULT", $data_result);
        $result = json_decode($data_result);

        if ($result->code == '0' && $result->desc == 'VERIFY_CARD_SUCCESS' && !empty($result->data)) {
            $dbturn = array(
                'char_id' => $char_id,
                'server_id' => $server_id,
                'char_name' => $char_name,
                'mobo_service_id' => $mobo_service_id,
                'cardvalue' => $result->data->value,
                'cardtype' => $cardtype,
                'serial' => $card_seri,
                'tranidcard' => $result->data->id,
                'mess' => $result->data->msg,
                'result' => $data_result,
                'status' => 1,
                'insertdate' => date('Y-m-d H:i:s'),
            );

            $this->log('payment', "DATA_SEND_Info", json_encode($dbturn));

            //$this->shopnganluong->insert('event_shopnganluong_charging', $dbturn);
            $insert_id_api = $this->shopnganluong->insert_id_api('event_shopnganluong_charging', $dbturn);

            if ($insert_id_api > 0) {
                //Add point user   
                $point = $result->data->value / 100;

                $datauser = $this->shopnganluong->user_check_point_exist($params['character_id'], $params['server_id'], $params['mobo_service_id']);
                if (count($datauser) > 0) {
                    if ($this->shopnganluong->add_point($params['character_id'], $params['server_id'], $params['mobo_service_id'], $point) > 0) {
                        $userdata_p["char_id"] = $params['character_id'];
                        $userdata_p["server_id"] = $params['server_id'];
                        $userdata_p["char_name"] = $params['character_name'];
                        $userdata_p["mobo_service_id"] = $params['mobo_service_id'];

                        foreach ($datauser as $key => $u_value) {
                            $userdata_p["user_id"] = $u_value["id"];
                            $userdata_p["ex_type"] = $cardtype;
                            $userdata_p["ex_value"] = $point;
                            $userdata_p["ex_date"] = Date('Y-m-d H:i:s');
                            $this->shopnganluong->insert_id_api("event_shopnganluong_exchange_g_history", $userdata_p);
                        }

                        $R["result"] = 0;
                        $R["message"] = 'Nạp thẻ thành công, bạn được cộng ' . $point . ' Ngân Lượng!';
                        $this->output->set_header('Content-type: application/json');
                        $this->output->set_output(json_encode($R));
                        return;
                    } else {
                        $this->log('payment', "ADD_POINT_FAIL", json_encode($dbturn));

                        $R["result"] = -1;
                        $R["message"] = 'Nạp thẻ thất bại, vui lòng thử lại !****';
                        $this->output->set_header('Content-type: application/json');
                        $this->output->set_output(json_encode($R));
                        return;
                    }
                } else {
                    //User Point Not Found
                    $R["result"] = -1;
                    $R["message"] = 'Không có dữ liệu người dùng !';
                    $this->output->set_header('Content-type: application/json');
                    $this->output->set_output(json_encode($R));
                    return;
                }
            } else {
                $this->log('payment', "ADD_POINT_FAIL", json_encode($dbturn));

                $R["result"] = -1;
                $R["message"] = 'Nạp thẻ thất bại, vui lòng thử lại !*****';
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;
            }
        } else {
            if ($result->data->id != "" && !empty($result->data->id)) {
                $dbturn_ex = array(
                    'char_id' => $params['character_id'],
                    'char_name' => $params['character_name'],
                    'mobo_service_id' => $params['mobo_service_id'],
                    'cardvalue' => $result->data->value,
                    'cardtype' => $cardtype,
                    "serial" => $card_seri,
                    'tranidcard' => $result->data->id,
                    'mess' => $result->data->msg,
                    'result' => $data_result,
                    'status' => 0,
                    'insertdate' => date('Y-m-d H:i:s'),
                );
                $this->shopnganluong->insert_id_api('event_shopnganluong_charging', $dbturn_ex);
            }

            $dbturn = array(
                'char_id' => $params['character_id'],
                'char_name' => $params['character_name'],
                'mobo_service_id' => $params['mobo_service_id'],
                'cardvalue' => $result->data->value,
                'cardtype' => $cardtype,
                'tranidcard' => $result->data->id,
                'mess' => $result->data->msg,
                'status' => 1,
                'insertdate' => date('Y-m-d H:i:s'),
            );
            $this->log('payment', "DATA_SEND_Info", json_encode($dbturn));

            $R["result"] = 0;
            $R["message"] = $result->data->msg;
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        $this->output->set_header('Content-type: application/json');
        $this->output->set_output(json_encode($R));
        return;
    }

    function api_card_exchange_new() {
        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 10) {
            $R["code"] = "-1";
            $R["message"] = "Mỗi lần đổi quà phải cách nhau 10 giây.";
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        $_SESSION["execute_time"] = time();

        $params = $this->input->get();
        $needle = array("char_id", "server_id", "mobo_service_id", "mobo_id", "char_name", "cardtype", "cardvalue");

        if (!is_required($params, $needle) == TRUE) {
            $R["result"] = -1;
            $R["message"] = 'INVALID_PARAMS !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        $token = $params['token'];
        unset($params["token"]);

        $tokendata = implode("", $params);
        $valid = md5($tokendata . $this->secret_key);

        if ($token != $valid) {
            $R["result"] = -1;
            $R["message"] = 'INVALID_TOKEN !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        $char_id = $_GET["char_id"];
        $server_id = $_GET["server_id"];
        $mobo_service_id = $_GET["mobo_service_id"];
        $mobo_id = $_GET["mobo_id"];
        $char_name = $_GET["char_name"];

        $cardtype = $_GET["cardtype"];
        $cardvalue = $_GET["cardvalue"];

        //Check Valid 
        $nganluong = 0;
        $value = intval($cardvalue);
        if ($value == 10000) {
            $nganluong = 115;
        }
        if ($value == 20000) {
            $nganluong = 230;
        }
        if ($value == 50000) {
            $nganluong = 575;
        }
        if ($value == 100000) {
            $nganluong = 1150;
        }
        if ($value == 200000) {
            $nganluong = 2300;
        }
        if ($value == 500000) {
            $nganluong = 5750;
        }

        if ($nganluong == 0) {
            $result["code"] = "-1";
            $result["message"] = "Mệnh giá thẻ không hợp lệ.";
        } else {
            //Check Point Valid
            $datauser = $this->shopnganluong->user_check_point_exist($char_id, $server_id, $mobo_service_id);
            if (count($datauser) <= 0) {
                $result["code"] = "-1";
                $result["message"] = "Không lấy được thông tin Ngân Lượng, vui lòng thử lại !";
            } else {
                if ($datauser[0]["user_point"] < $nganluong) {
                    $result["code"] = "-1";
                    $result["message"] = "Ngân Lượng của bạn không đủ để đổi thẻ mệnh giá '" . $value . "' !";
                } else {
                    //Check 10M
                    $check_10m = $this->shopnganluong->get_card_total_exchange_history();
                    if (($check_10m[0]["Total"] + $value) >= $this->max_card_exchange && (!in_array($mobo_id, $this->mobo_id_test))) {
                        $result["code"] = "-1";
                        $result["message"] = "Không thể đổi thẻ trong lúc này, bạn vui lòng quay lại sau !";
                    } else {
                        //Update Point
                        if ($this->shopnganluong->update_point($char_id, $server_id, $mobo_service_id, $nganluong) == 0) {
                            $result["code"] = "-1";
                            $result["message"] = "Đổi thẻ thất bại, vui lòng thử lại !";
                        } else {
                            //Addd Card Exchange History                                            
                            $userdata_p["char_id"] = $char_id;
                            $userdata_p["server_id"] = $server_id;
                            $userdata_p["char_name"] = $char_name;
                            $userdata_p["mobo_service_id"] = $mobo_service_id;

                            $userdata_p["exchange_card_date"] = Date('Y-m-d H:i:s');
                            $userdata_p["exchange_card_point"] = $nganluong;
                            $userdata_p["card_status"] = 0;
                            $userdata_p["card_type"] = $cardtype;
                            $userdata_p["card_value"] = $value;

                            $i_id = $this->shopnganluong->insert_id("event_shopnganluong_card_exchange_history", $userdata_p);

                            if ($i_id > 0) {
                                //Send Card 
                                $key_card_api = "IDpCJtb6Go10vKGRy5DQ";

                                $array = array(
                                    "mobo_id" => $mobo_id
                                    , "mobo_service_id" => $mobo_service_id
                                    , "username" => $char_name
                                    , "character_id" => $char_id
                                    , "character_name" => $char_name
                                    , "supplier" => $cardtype
                                    , "service_id" => "0"
                                    , "value" => $value
                                    , "amount" => 1
                                    , "service_name" => $this->service_name
                                    , "transid" => time()
                                    , "env" => $this->env_s
                                );

                                $token = md5(implode("", $array) . "IDpCJtb6Go10vKGRy5DQ");
                                $array["app"] = "game";
                                $array["token"] = $token;

                                $url = "http://gapi.mobo.vn/?control=recharge&func=buy_card&" . http_build_query($array);

                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                $data_result = curl_exec($ch);
                                curl_close($ch);

                                $card_result = json_decode($data_result, true);

                                if ($card_result["code"] == '0' && $card_result["desc"] == 'BUY_CARD_SUCCESS' && !empty($card_result["data"])) {
                                    //Update Card Exchange History 
                                    $s_pin_encode = $this->encrypt($card_result["data"]["list"][0]["pin"], $key_card_api);
                                    $s_serial_encode = $this->encrypt($card_result["data"]["list"][0]["serial"], $key_card_api);

                                    //echo $this->decrypt("g6N8iHR9m4qLoQ==", $key_card_api); die;   
                                    $message_string_card = $card_result["data"]["list"][0]["title"] . " - print_date: " . $card_result["data"]["list"][0]["print_date"] . " - id: " . $card_result["data"]["list"][0]["id"];
                                    $this->shopnganluong->update_card_exchange_history($i_id, $s_pin_encode, $s_serial_encode, 1, $message_string_card, $data_result);

                                    $result["code"] = "0";
                                    $result["message"] = "Đổi thẻ thành công:<br />-Mệnh giá: <strong>" . $value . "</strong> <br />- Mã thẻ: <span style='font-weight: bold; color: #1649E8;'>" . $this->decrypt($this->encrypt($card_result["data"]["list"][0]["pin"], $key_card_api), $key_card_api) . "</span><br /> - Serial: <span style='font-weight: bold; color: #1649E8;'>" . $this->decrypt($this->encrypt($card_result["data"]["list"][0]["serial"], $key_card_api), $key_card_api) . "</span><br /><br />Bạn có thể xem lại mã thẻ và serial trong lịch sử đổi thẻ.";
                                } else {
                                    $this->shopnganluong->update_card_exchange_history($i_id, null, null, 0, $card_result["message"], $data_result);
                                    //Fail restore point
                                    $this->shopnganluong->add_point($char_id, $server_id, $mobo_service_id, $nganluong);
                                    $result["code"] = "-1";
                                    $result["message"] = "Đổi thẻ thất bại, vui lòng thử lại** !";
                                }
                                //$result["code"] = "0";
                                //$result["message"] = $card_result;                                                            
                            } else {
                                //Fail restore point
                                $this->shopnganluong->add_point($char_id, $server_id, $mobo_service_id, $nganluong);
                                $result["code"] = "-1";
                                $result["message"] = "Đổi thẻ thất bại, vui lòng thử lại* !";
                            }
                        }
                    }
                }
            }
        }

        $this->output->set_output(json_encode($result));
    }

    function api_get_gift_exchange_history() {
        $params = $this->input->get();
        $needle = array("mobo_service_id");

        if (!is_required($params, $needle) == TRUE) {
            $R["result"] = -1;
            $R["message"] = 'INVALID_PARAMS !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        $token = $params['token'];
        unset($params["token"]);

        $tokendata = implode("", $params);
        $valid = md5($tokendata . $this->secret_key);

        if ($token != $valid) {
            $R["result"] = -1;
            $R["message"] = 'INVALID_TOKEN !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }
        
        $mobo_service_id = $_GET["mobo_service_id"];

        $gift_exchange_history = $this->shopnganluong->api_get_gift_exchange_history($mobo_service_id);
        $this->output->set_output(json_encode($gift_exchange_history));
    }
    
    function api_get_cash_out_history() {
        $params = $this->input->get();
        $needle = array("mobo_service_id");

        if (!is_required($params, $needle) == TRUE) {
            $R["result"] = -1;
            $R["message"] = 'INVALID_PARAMS !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        $token = $params['token'];
        unset($params["token"]);

        $tokendata = implode("", $params);
        $valid = md5($tokendata . $this->secret_key);

        if ($token != $valid) {
            $R["result"] = -1;
            $R["message"] = 'INVALID_TOKEN !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }
        
        $mobo_service_id = $_GET["mobo_service_id"];
        $list_card = $this->shopnganluong->card_exchange_history($mobo_service_id);
        $list_card_decode = array();
        $key_card_api = "rGPDFbWasd@$2WoN";

        foreach ($list_card as $key => $value) {
            array_push($list_card_decode, Array("id" => $value["id"], "card_type" => $value["card_type"],
                "card_value" => $value["card_value"], "card_code" => $this->decrypt($value["card_code"], $key_card_api)
                , "card_serial" => $this->decrypt($value["card_serial"], $key_card_api), "exchange_card_date" => $value["exchange_card_date"]));
        }

        $card_exchange_history = $this->shopnganluong->card_exchange_history($mobo_service_id);        
        
        $this->output->set_output(json_encode($card_exchange_history));
    }
    
    function api_get_cash_in_history() {
        $params = $this->input->get();
        $needle = array("mobo_service_id");

        if (!is_required($params, $needle) == TRUE) {
            $R["result"] = -1;
            $R["message"] = 'INVALID_PARAMS !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }

        $token = $params['token'];
        unset($params["token"]);

        $tokendata = implode("", $params);
        $valid = md5($tokendata . $this->secret_key);

        if ($token != $valid) {
            $R["result"] = -1;
            $R["message"] = 'INVALID_TOKEN !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        }
        
        $mobo_service_id = $_GET["mobo_service_id"];        
        $charging_history = $this->shopnganluong->get_charging_history($mobo_service_id);
        
        $this->output->set_output(json_encode($charging_history));
    }

    /////////////////Function
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

    function senditemforgame($data, $item, $title, $content) {
        if (empty($data) || empty($item)) {
            return false;
        }
        //load thu vien chung
        $api = new GameFullAPI();
        $addditem = $api->add_item_result($this->service_name, $data["mobo_service_id"], $data["server_id"], $item, $title, $content);
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

    public function local_filter() {
        $local_ip = array("14.161.5.226", "118.69.76.212", "115.78.161.88", "115.78.161.124", "14.169.170.196", "115.78.161.134",
            "113.161.78.101", "172.16.0.158");
        $remote_addr = $this->get_remote_ip();
        $ipreal = explode(",", $remote_addr);
        //var_dump($ipreal); die;
        //echo $remote_addr; die;
        foreach ($ipreal as $key => $value) {
            if (in_array($value, $local_ip)) {
                return true;
            }
        }
        return false;
    }

    public function log($group = false, $url = false, $data = false) {
        try {
            $date = 'Y/m/d';
            $time = time();
            $sub = str_replace("/", "", date('Y-m-d', $time));
            $path = LOG_PATH . date($date) . DIRECTORY_SEPARATOR;
            //die($path);
            if (!is_dir($path))
                mkdir($path, 0777, true);

            $file = $group . "_" . date('H', $time) . ".csv";
            //@chmod($path . DIRECTORY_SEPARATOR . $file, 0777);
            $f = fopen($path . DIRECTORY_SEPARATOR . $file, "a+");
            //Build log data
            $data = is_array($data) ? json_encode($data) : $data;
            $csv_data[] = date('H:i:s', $time) . "\t";
            $csv_data[] = "IP : " . $_SERVER['REMOTE_ADDR'] . "\t";
            $csv_data[] = "Refer : " . $_SERVER['HTTP_REFERER'] . "\t";
            $csv_data[] = $url . "\t";
            $csv_data[] = $data . "\t";
            //fputs($f, date('H:i:s', $time) . "\t,IP : " . $_SERVER['REMOTE_ADDR'] . "\t," . $url . "\t," . $data . "\n");
            @fputcsv($f, $csv_data);
            fclose($f);
        } catch (Exception $exc) {
            
        }
    }

}
