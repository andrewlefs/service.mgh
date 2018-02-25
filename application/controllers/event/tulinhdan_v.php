<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
if (empty($_SESSION))
    session_start();
require_once APPPATH . "core/EI_Controller.php";

class tulinhdan extends EI_Controller {

    private $mobo_id_test = array("364853453", "493545409", "286056527", "260896396", "552397949", "432637351");
    private $is_test = false;
    private $definerechare = 5;

    public function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $this->load->library('GameFullAPI');
        $this->load->model('events/m_tulinhdan', "tulinhdan", false);
        $this->data["event_name"] = "Kho Báu Tân Mộng Giang Hồ";
    }

    public function index() {
        if ($this->verify_uri() != true) {
            echo "Bạn không thể truy cập sự kiện này.![1]";
        }

        //Check Point Exist 
        $user = $this->get_info();
        $_SESSION['user_info'] = serialize($user);

        //var_dump($user); die;
        $char_id = $user->character_id;
        $char_name = $user->character_name;
        $server_id = $user->server_id;
        $mobo_service_id = $user->mobo_service_id;
        $mobo_id = $user->mobo_id;

        $this->data["mobo_id"] = $mobo_id;

        //Check join Game
        if ($char_id == "") {
            echo "Vui lòng vào game trước khi tham gia sự kiện...";
            die;
        }

        //Set Session
        $this->storeSession($mobo_service_id, $server_id);

        //Non public
        if ($this->is_test && !in_array($user->mobo_id, $this->mobo_id_test)) {
            echo "Bạn không có quyền truy cập sự kiện này";
            die;
        }

        $this->data["char_name"] = $char_name;
        $this->data["server_id"] = $server_id;
        $this->data["content_id"] = 5410;

        echo $this->load->view("events/tulinhdan/index", $this->data, true);
    }

    public function thamgia() {
        if (empty($_SESSION['user_info'])) {
            echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            die;
        }

        $user = unserialize($_SESSION['user_info']);

        $char_id = $user->character_id;
        $char_name = $user->character_name;
        $server_id = $user->server_id;
        $mobo_service_id = $user->mobo_service_id;
        $mobo_id = $user->mobo_id;

//        $api = new GameFullAPI();
//        $user_info = $api->get_user_info($this->service_name, $mobo_service_id, $server_id);
//        var_dump($user_info);
//        die;
        //var_dump($userdata);die;
        //Check join Game
        if ($char_id == "") {
            echo "Vui lòng vào game trước khi tham gia sự kiện...";
            die;
        }

        //Set Session
        $this->storeSession($mobo_service_id, $server_id);

        //Non public
        if ($this->is_test && !in_array($user->mobo_id, $this->mobo_id_test)) {
            echo "Bạn không có quyền truy cập sự kiện này";
            die;
        }

        //Get Tournament List 
        $tournament_list = $this->tulinhdan->get_tournament();
        if (count($tournament_list) == 0) {
            echo "Không có sự kiện...";
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
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_money" => $value["tournament_money"]));
                }
            }

            if ($server_list != "" && $ip_list == "") {
                $server_list = explode(";", $server_list);
                if (in_array($server_id, $server_list)) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_money" => $value["tournament_money"]));
                }
            }

            if ($server_list == "" && $ip_list == "") {
                array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                    "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                    , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_money" => $value["tournament_money"]));
            }

            if ($server_list != "" && $ip_list != "") {
                $server_list = explode(";", $server_list);
                $ip_list = explode(";", $ip_list);
                if (in_array($server_id, $server_list) && (in_array($client_ip, $ip_list))) {
                    array_push($tournament_filter, Array("id" => $value["id"], "tournament_name" => $value["tournament_name"], "tournament_date_start" => $value["tournament_date_start"], "tournament_date_end" => $value["tournament_date_end"],
                        "tournament_status" => $value["tournament_status"], "tournament_server_list" => $server_list, "tournament_date_start_reward" => $value["tournament_date_start_reward"], "tournament_date_end_reward" => $value["tournament_date_end_reward"]
                        , "tournament_ip_list" => $ip_list, "tournament_img" => $value["tournament_img"], "tournament_money" => $value["tournament_money"]));
                }
            }
        }

        $tournament = $tournament_filter;

        //var_dump($tournament_list); die;

        if (count($tournament) > 0) {
            foreach ($tournament as $key => $value) {
                $this->data["tournament"] = $tournament;
                $reward_list = $this->tulinhdan->get_reward_list_all($value["id"]);
                //Get Reward List
                $this->data["reward_list"] = $reward_list;
                //Status Receive Gift Tournament
                $this->data["gift_receive_status"] = $this->tulinhdan->check_exist_exchange_gift($value["id"], $server_id, $mobo_service_id);

                //User Point
                $this->data["user_point"] = $this->user_point($char_id, $server_id, $mobo_service_id, $mobo_id, $char_name, $value["id"]);
            }
        }

        //Get User Point
        //$datauser_point = $this->tulinhdan->get_pk_point($server_id, $char_id);       
        //if(count($datauser_point) > 0){           
        //    foreach ($datauser_point as $key => $value) { 
        //        $this->data["user_point"] = $value["CURRPOINT"];
        //    }          
        //}
        //echo $userpoint[0]["StartDate"]; die;
//        if($mobo_id == "364853453"){
//            $this->data["user_point"] = 3000;
//        }
        //$data["history"] =$this->cacuoc->get_pet_history_details($char_id, $server_id, $mobo_service_id);
        //$this->data["history"] = $data["history"];

        $script_chart .= '<script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          [\'Language\', \'Speakers (in millions)\'],';

        foreach ($reward_list as $key => $value) {
            if ($key < count($reward_list)) {
                $script_chart .= '[\' ' . $value['reward_name'] . ' \',  20],';
            } else {
                $script_chart .= '[\' ' . $value['reward_name'] . ' \',  20]';
            }
        }

        $script_chart .= ' ]);
                var options = {
                  legend: \'none\',
                  pieSliceText: \'label\',                 
                  pieStartAngle: 100000,
                  width: 650,
                  height: 500,
                };

                  var chart = new google.visualization.PieChart(document.getElementById(\'piechart\'));
                  chart.draw(data, options);
                }
              </script>';

        $this->data["char_name"] = $char_name;
        $this->data["server_id"] = $server_id;
        $this->data["script_chart"] = $script_chart;

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("events/tulinhdan/thamgia", $this->data, true);
    }

    public function get_pk_point_test() {
        $Sever_ID = $_GET["Sever_ID"];
        $UserID = $_GET["UserID"];
        //echo $Sever_ID; die;
        $data["user_point"] = $this->tulinhdan->get_pk_point($Sever_ID, $UserID);

        $this->output->set_header('Content-type: application/json');
        $this->output->set_output(json_encode($data["user_point"]));

        ///$this->output->set_output($data["user_point"][0]["RANK"]);
    }

    function get_exchange_history() {
        if (empty($_SESSION['user_info'])) {
            echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            die;
        }

        $user = unserialize($_SESSION['user_info']);

        $char_id = $user->character_id;
        $char_name = $user->character_name;
        $server_id = $user->server_id;
        $mobo_service_id = $user->mobo_service_id;
        $mobo_id = $user->mobo_id;
        
        $tournament_list = $this->tulinhdan->tournament_list();
        if (count($tournament_list) == 0) {
            echo "Sự kiện đang tạm đóng, bạn vui lòng quay lại sau.";
            die;
        }
        
        $id_his = $_GET["id_his"];
        
        if($id_his == 1){
            $this->data["history_name"] = "Lịch Sử Tích Lũy";
            $data["history"] = $this->tulinhdan->get_exchange_history_new($tournament_list[0]["id"], $char_id, $server_id, $mobo_service_id);
            $this->data["history"] = $data["history"];
            $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
            echo $this->load->view("events/tulinhdan/history", $this->data, true);
        }
        else
        if($id_his == 2){
            $this->data["history_name"] = "Lịch Sử Đổi Quà";
            $data["history"] = $this->tulinhdan->get_exchange_history_shop($server_id, $mobo_service_id);
            $this->data["history"] = $data["history"];
            $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
            echo $this->load->view("events/tulinhdan/history_shop", $this->data, true);
        }        
        
    }

    public function gift_exchange() {
        if (empty($_SESSION['user_info'])) {
            $result["code"] = "-1";
            $result["message"] = "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            $this->output->set_output(json_encode($result));
            return;
        }

        $user = unserialize($_SESSION['user_info']);

        $char_id = $user->character_id;
        $char_name = $user->character_name;
        $server_id = $user->server_id;
        $mobo_service_id = $user->mobo_service_id;
        $mobo_id = $user->mobo_id;

        //var_dump($userdata);die;
        //Check join Game
        if ($char_id == "") {
            $result["code"] = "-1";
            $result["message"] = "Vui lòng vào game trước khi tham gia sự kiện...";
            $this->output->set_output(json_encode($result));
            return;
        }
        //Non public
        if ($this->is_test && !in_array($user->mobo_id, $this->mobo_id_test)) {
            $result["code"] = "-1";
            $result["message"] = "Bạn không có quyền truy cập sự kiện này";
            $this->output->set_output(json_encode($result));
            return;
        }

        if (!$this->getSession($mobo_service_id, $server_id)) {
            $result["code"] = "-1";
            $result["message"] = "Bạn đang đăng nhập trên một máy khác.";
            $this->output->set_output(json_encode($result));
            return;
        }

        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 3) {
            $result["code"] = "-1";
            $result["message"] = "Mỗi lần Tích Lũy phải cách nhau 3 giây.";
        } else {
            $_SESSION["execute_time"] = time();

            //Check Tournament
            $get_tournament_details = $this->tulinhdan->get_tournament();

            if (count($get_tournament_details) == 0) {
                $result["code"] = "-1";
                $result["message"] = "Thông tin sự kiện không chính xác !";
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
                $valid_nl = $this->tulinhdan->user_check_nl_exist($mobo_service_id);
                if (count($valid_nl) == 0) {
                    $result["code"] = "-1";
                    $result["message"] = "Thông tin người dùng không chính xác !";
                    return;
                }

                if ($valid_nl[0]["user_point"] < $get_tournament_details[0]["tournament_point"] || $valid_nl[0]["user_point"] == "") {
                    $result["code"] = "-1";
                    $result["message"] = "Ngân lượng không đủ để tích lũy !";
                }else {
                    //var_dump($user_info); die;
                    //Update Gold
                    MeAPI_Log::writeCsv(array(json_encode($user_info)), 'event_tulinhdan_exchange_g_user_info_' . date('H'));
                    $item_gold = array(array("item_id" => 0, "count" => (int) $get_tournament_details[0]["tournament_money"], "item_type" => 2));
                    //var_dump($item_gold); die;

                    $minuteditem = $api->minus_item($this->service_name, $mobo_service_id, $server_id, $item_gold, $title = "Ban vua bi tru ", $content = "Tru Kim Cuong Tu Linh Dan");

                    //write log file 2
                    MeAPI_Log::writeCsv(array(json_encode($minuteditem)), 'event_tulinhdan_exchange_g_updategold_' . date('H'));
                    //var_dump($minuteditem); die;
                    if ($minuteditem) {
                        $userdata_p["char_id"] = $char_id;
                        $userdata_p["server_id"] = $server_id;
                        $userdata_p["char_name"] = $char_name;
                        $userdata_p["mobo_service_id"] = $mobo_service_id;
                        $userdata_p["exchange_date"] = Date('Y-m-d H:i:s');
                        $userdata_p["tournament_id"] = $get_tournament_details[0]["id"];
                        $userdata_p["tournament_money"] = $get_tournament_details[0]["tournament_money"];

                        $i_id = $this->tulinhdan->insert_id("event_tulinhdan_exchange_history", $userdata_p);

                        //add tich luy 5%
                        if ($i_id > 0) {
                            $info_u['mobo_service_id'] = $mobo_service_id;
                            $info_u['character_id'] = $char_id;
                            $info_u['server_id'] = $server_id;
                            $info_u['character_name'] = $char_name;

                            //insert nohu
                            if($this->tulinhdan->update_nohu($this->definerechare) == FALSE){
                                $result["code"] = "-1";
                                $result["message"] = "Tích lũy thất bại, vui lòng thử lại !*";
                                $this->output->set_output(json_encode($result));
                                return;
                            }


                            //Update Point
                            if ($this->tulinhdan->update_point($server_id, $mobo_service_id, 1, $get_tournament_details[0]["tournament_money"], $get_tournament_details[0]["id"]) <= 0) {
                                $result["code"] = "-1";
                                $result["message"] = "Tích lũy thất bại, vui lòng thử lại !*";
                                $this->output->set_output(json_encode($result));
                                return;
                            }

                            //Check Gift List VIP
                            $reward_list_vip = $this->tulinhdan->get_reward_list_vip($get_tournament_details[0]["id"]);
                            $check_gift_exchange_vip = $this->tulinhdan->check_exist_exchange_gift_vip($get_tournament_details[0]["id"], $server_id, $mobo_service_id);

                            //var_dump($reward_list_vip); die;

                            if (count($reward_list_vip) > 0 && $valid_point[0]["user_point"] >= $reward_list_vip[0]["reward_vip_count"] && !$check_gift_exchange_vip) {
                                //SEND VIP Item
                                $item1 = null;

                                if($reward_list_vip['type'] == 2){
                                    //get no hu
                                    $nohu = $this->tulinhdan->get_nohu();
                                    if($nohu){
                                        $item1[] = array("item_id" => (int) $nohu["item_id"], "count" => (int) $nohu['item_count'], "item_type" => (int) $nohu['item_type']);
                                    }


                                    if($this->tulinhdan->update_nohu_reset() == FALSE){
                                        $result["code"] = "-1";
                                        $result["message"] = "Tích lũy thất bại, vui lòng thử lại !*";
                                        $this->output->set_output(json_encode($result));
                                        return;
                                    }
                                    //minute no hu
                                }else{
                                    if ($reward_list_vip[0]["reward_item1_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $reward_list_vip[0]["reward_item1_code"], "count" => (int) $reward_list_vip[0]["reward_item1_number"], "item_type" => (int) $reward_list_vip[0]["reward_item1_type"]);
                                    }
                                    if ($reward_list_vip[0]["reward_item2_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $reward_list_vip[0]["reward_item2_code"], "count" => (int) $reward_list_vip[0]["reward_item2_number"], "item_type" => (int) $reward_list_vip[0]["reward_item2_type"]);
                                    }
                                    if ($reward_list_vip[0]["reward_item3_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $reward_list_vip[0]["reward_item3_code"], "count" => (int) $reward_list_vip[0]["reward_item3_number"], "item_type" => (int) $reward_list_vip[0]["reward_item3_type"]);
                                    }
                                    if ($reward_list_vip[0]["reward_item4_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $reward_list_vip[0]["reward_item4_code"], "count" => (int) $reward_list_vip[0]["reward_item4_number"], "item_type" => (int) $reward_list_vip[0]["reward_item4_type"]);
                                    }
                                    if ($reward_list_vip[0]["reward_item5_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $reward_list_vip[0]["reward_item5_code"], "count" => (int) $reward_list_vip[0]["reward_item5_number"], "item_type" => (int) $reward_list_vip[0]["reward_item5_type"]);
                                    }

                                    if ($reward_list_vip[0]["reward_item6_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $reward_list_vip[0]["reward_item6_code"], "count" => (int) $reward_list_vip[0]["reward_item6_number"], "item_type" => (int) $reward_list_vip[0]["reward_item6_type"]);
                                    }
                                    if ($reward_list_vip[0]["reward_item7_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $reward_list_vip[0]["reward_item7_code"], "count" => (int) $reward_list_vip[0]["reward_item7_number"], "item_type" => (int) $reward_list_vip[0]["reward_item7_type"]);
                                    }
                                    if ($reward_list_vip[0]["reward_item8_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $reward_list_vip[0]["reward_item8_code"], "count" => (int) $reward_list_vip[0]["reward_item8_number"], "item_type" => (int) $reward_list_vip[0]["reward_item8_type"]);
                                    }
                                    if ($reward_list_vip[0]["reward_item9_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $reward_list_vip[0]["reward_item9_code"], "count" => (int) $reward_list_vip[0]["reward_item9_number"], "item_type" => (int) $reward_list_vip[0]["reward_item9_type"]);
                                    }
                                    if ($reward_list_vip[0]["reward_item10_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $reward_list_vip[0]["reward_item10_code"], "count" => (int) $reward_list_vip[0]["reward_item10_number"], "item_type" => (int) $reward_list_vip[0]["reward_item10_type"]);
                                    }
                                }


                                $data_result = $this->senditemforgame($info_u, $item1);
                                $this->tulinhdan->update_exchange_history_vip($i_id, json_encode($item1), json_encode($data_result), $reward_list_vip[0]["id"]);

                                $result["code"] = "0";
                                $result["message"] = "Nhận quà thành công, nhận được '" . $reward_list_vip[0]["reward_name"] . "'!";
                            } else {
                                //Get Gift List
                                $reward_list = $this->tulinhdan->get_reward_list($get_tournament_details[0]["id"]);

                                if (count($reward_list) < 1) {
                                    $result["code"] = "-1";
                                    $result["message"] = json_encode($reward_list);
                                    $this->output->set_output(json_encode($result));
                                    return;
                                }

                                $total = 0;
                                $final_award = array();
                                //coutn total random
                                foreach ($reward_list as $value) {
                                    $total += (int) $value["reward_point"];
                                    $final_award[$value["id"]] = $value;
                                }
                                //load random item
                                $this->load->library('WeightedRandom');
                                $objRandom = new WeightedRandom();
                                $luckyKeyItem = $this->random_luck($objRandom, $final_award, $total);

                                $item1 = null;

                                if($luckyKeyItem['type'] == 2){

                                    $nohu = $this->tulinhdan->get_nohu();
                                    if($nohu){
                                        $item1[] = array("item_id" => (int) $nohu["item_id"], "count" => (int) $nohu['item_count'], "item_type" => (int) $nohu['item_type']);
                                    }


                                    if($this->tulinhdan->update_nohu_reset() == FALSE){
                                        $result["code"] = "-1";
                                        $result["message"] = "Tích lũy thất bại, vui lòng thử lại !*";
                                        $this->output->set_output(json_encode($result));
                                        return;
                                    }

                                    //minute no hu
                                }else{

                                    if ($luckyKeyItem["reward_item1_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $luckyKeyItem["reward_item1_code"], "count" => (int) $luckyKeyItem["reward_item1_number"], "item_type" => (int) $luckyKeyItem["reward_item1_type"]);
                                    }
                                    if ($luckyKeyItem["reward_item2_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $luckyKeyItem["reward_item2_code"], "count" => (int) $luckyKeyItem["reward_item2_number"], "item_type" => (int) $luckyKeyItem["reward_item2_type"]);
                                    }
                                    if ($luckyKeyItem["reward_item3_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $luckyKeyItem["reward_item3_code"], "count" => (int) $luckyKeyItem["reward_item3_number"], "item_type" => (int) $luckyKeyItem["reward_item3_type"]);
                                    }
                                    if ($luckyKeyItem["reward_item4_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $luckyKeyItem["reward_item4_code"], "count" => (int) $luckyKeyItem["reward_item4_number"], "item_type" => (int) $luckyKeyItem["reward_item4_type"]);
                                    }
                                    if ($luckyKeyItem["reward_item5_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $luckyKeyItem["reward_item5_code"], "count" => (int) $luckyKeyItem["reward_item5_number"], "item_type" => (int) $luckyKeyItem["reward_item5_type"]);
                                    }

                                    if ($luckyKeyItem["reward_item6_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $luckyKeyItem["reward_item6_code"], "count" => (int) $luckyKeyItem["reward_item6_number"], "item_type" => (int) $luckyKeyItem["reward_item6_type"]);
                                    }

                                    if ($luckyKeyItem["reward_item7_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $luckyKeyItem["reward_item7_code"], "count" => (int) $luckyKeyItem["reward_item7_number"], "item_type" => (int) $luckyKeyItem["reward_item7_type"]);
                                    }

                                    if ($luckyKeyItem["reward_item8_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $luckyKeyItem["reward_item8_code"], "count" => (int) $luckyKeyItem["reward_item8_number"], "item_type" => (int) $luckyKeyItem["reward_item8_type"]);
                                    }

                                    if ($luckyKeyItem["reward_item9_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $luckyKeyItem["reward_item9_code"], "count" => (int) $luckyKeyItem["reward_item9_number"], "item_type" => (int) $luckyKeyItem["reward_item9_type"]);
                                    }

                                    if ($luckyKeyItem["reward_item10_number"] != 0) {
                                        $item1[] = array("item_id" => (int) $luckyKeyItem["reward_item10_code"], "count" => (int) $luckyKeyItem["reward_item10_number"], "item_type" => (int) $luckyKeyItem["reward_item10_type"]);
                                    }
                                }



                                $data_result = $this->senditemforgame($info_u, $item1);
                                $this->tulinhdan->update_exchange_history($i_id, json_encode($item1), json_encode($data_result), $luckyKeyItem["id"]);

                                $result["code"] = "0";
                                $result["message"] = "Nhận quà thành công, nhận được '" . $luckyKeyItem["reward_name"] . "'!";
                            }
                        } else {
                            $result["code"] = "-1";
                            $result["message"] = "Tích lũy thất bại, vui lòng thử lại !**";
                        }
                    } else {
                        $result["code"] = "-1";
                        $result["message"] = "Trừ Kim Cương thất bại, vui lòng thử lại !";
                    }
                }
            }
        }

        $this->output->set_output(json_encode($result));
    }

    //TOP
    public function gift_top_exchange() {
        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 5) {
            $result["code"] = "-1";
            $result["message"] = "Mỗi lần đổi quà phải cách nhau 5 giây.";
        } else {
            $_SESSION["execute_time"] = time();
            $params = $_SESSION['linkinfo'];
            $info = new Info();

            if ($info->checksign($params) === FALSE) {
                unset($_SESSION["oauthtoken"]);
                echo "Truy cập không hợp lệ"; //$this->load->view("deny", "", true);
                exit();
            }

            //User Data
            $userdata = json_decode($params["info"], true);
            $datadecode = json_decode(base64_decode($params["access_token"]), true);
            $char_name = $userdata["character_name"];
            $char_id = $userdata["character_id"];
            $server_id = $userdata["server_id"];
            $mobo_service_id = $datadecode["mobo_service_id"];
            $mobo_id = $datadecode["mobo_id"];

            $userdata_p["char_id"] = $char_id;
            $userdata_p["server_id"] = $server_id;
            $userdata_p["char_name"] = $char_name;
            $userdata_p["mobo_service_id"] = $mobo_service_id;

            $tournament_id = $_GET["id"];

            //Check Exist            
            if ($this->tulinhdan->check_exist_exchange_gift_top($tournament_id, $server_id, $mobo_service_id)) {
                $result["code"] = "-1";
                $result["message"] = "Bạn đã nhận quà Top sự kiện này rồi !";
            } else {
                //Check Receive Gift Date
                $get_tournament_details = $this->tulinhdan->get_tournament_details($tournament_id);
                $date_now = date('Y-m-d H:i:s');

                $tournament_date_end = date('Y-m-d H:i:s', strtotime($get_tournament_details[0]["tournament_date_end"]));

                if (strtotime($date_now) < strtotime($tournament_date_end)) {
                    $result["code"] = "-1";
                    $result["message"] = "Bạn chỉ được nhận quà Top khi sự kiện kết thúc !";
                } else {
                    //Check Rank Valid 
                    $userpoint = $this->tulinhdan->get_top_user($tournament_id, $server_id, $mobo_service_id);

                    if (count($userpoint) == 0) {
                        $result["code"] = "-1";
                        $result["message"] = "Hạng của bạn không đủ để nhận quà!*";
                    } else {

                        $reward_rank_valid = $this->tulinhdan->check_rank_valid($userpoint[0]["rank"], $tournament_id);

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

                            $i_id = $this->tulinhdan->insert_id("event_tulinhdan_exchange_history_top", $userdata_p);

                            if ($i_id > 0) {
                                //SEND Item 
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

                                if ($reward_rank_valid[0]["reward_item6_code"] != 0 && $reward_rank_valid[0]["reward_item6_number"] != 0) {
                                    $item1[] = array("item_id" => (int) $reward_rank_valid[0]["reward_item6_code"], "count" => (int) $reward_rank_valid[0]["reward_item6_number"]);
                                }
                                if ($reward_rank_valid[0]["reward_item7_code"] != 0 && $reward_rank_valid[0]["reward_item7_number"] != 0) {
                                    $item1[] = array("item_id" => (int) $reward_rank_valid[0]["reward_item7_code"], "count" => (int) $reward_rank_valid[0]["reward_item7_number"]);
                                }
                                if ($reward_rank_valid[0]["reward_item8_code"] != 0 && $reward_rank_valid[0]["reward_item8_number"] != 0) {
                                    $item1[] = array("item_id" => (int) $reward_rank_valid[0]["reward_item8_code"], "count" => (int) $reward_rank_valid[0]["reward_item8_number"]);
                                }
                                if ($reward_rank_valid[0]["reward_item9_code"] != 0 && $reward_rank_valid[0]["reward_item9_number"] != 0) {
                                    $item1[] = array("item_id" => (int) $reward_rank_valid[0]["reward_item9_code"], "count" => (int) $reward_rank_valid[0]["reward_item9_number"]);
                                }
                                if ($reward_rank_valid[0]["reward_item10_code"] != 0 && $reward_rank_valid[0]["reward_item10_number"] != 0) {
                                    $item1[] = array("item_id" => (int) $reward_rank_valid[0]["reward_item10_code"], "count" => (int) $reward_rank_valid[0]["reward_item10_number"]);
                                }

                                $data_result = $this->senditemforgame($userdata_p, $item1);
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

        $this->output->set_output(json_encode($result));
    }

    function get_top() {
        if (empty($_SESSION['user_info'])) {
            echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            die;
        }

        $user = unserialize($_SESSION['user_info']);

        $char_id = $user->character_id;
        $char_name = $user->character_name;
        $server_id = $user->server_id;
        $mobo_service_id = $user->mobo_service_id;
        $mobo_id = $user->mobo_id;

        //var_dump($userdata);die;
        //Check join Game
        if ($char_id == "") {
            echo "Vui lòng vào game trước khi tham gia sự kiện...";
            die;
        }

        //Non public
        if ($this->is_test && !in_array($user->mobo_id, $this->mobo_id_test)) {
            echo "Bạn không có quyền truy cập sự kiện này";
            die;
        }

        //Get Tournament List 
        $tournament_list = $this->tulinhdan->get_tournament();
        if (count($tournament_list) == 0) {
            echo "Không có sự kiện...";
            die;
        }

        $this->data["tournament"] = $tournament_list;

        $tournament_top = $this->tulinhdan->get_top($tournament_list[0]["id"]);
        $this->data["tournament_top"] = $tournament_top;

        //Get User
        $get_top_user = $this->tulinhdan->get_top_user($tournament_list[0]["id"], $server_id, $mobo_service_id);
        $this->data["user_point"] = $this->user_point($char_id, $server_id, $mobo_service_id, $mobo_id, $char_name, $tournament_list[0]["id"]);

        //var_dump($this->data["user_point"]); die;

        if ($get_top_user[0]["rank"] == null || $get_top_user[0]["rank"] == "" || $get_top_user[0]["rank"] > 50) {
            $this->data["user_rank"] = "Lớn hơn 50";
        } else {
            $this->data["user_rank"] = $get_top_user[0]["rank"];
        }
        //var_dump($get_top_user); die;

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        echo $this->load->view("events/tulinhdan/top", $this->data, true);
    }

    function senditemforgame($data, $item) {
        if (empty($data) || empty($item)) {
            return false;
        }
        //load thu vien chung
        $api = new GameFullAPI();
        //$jsonitem = json_encode($item);

        $addditem = $api->add_item_result($this->service_name, $data["mobo_service_id"], $data["server_id"], $item, "Tu Linh Dan", "Qua Tu Linh Dan");
        return $addditem;
    }

    public function load_user_point() {
        if (empty($_SESSION['user_info'])) {
            echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            die;
        }

        $user = unserialize($_SESSION['user_info']);

        $char_id = $user->character_id;
        $char_name = $user->character_name;
        $server_id = $user->server_id;
        $mobo_service_id = $user->mobo_service_id;
        $mobo_id = $user->mobo_id;

        $tournament_list = $this->tulinhdan->get_tournament();
        $datauser = $this->tulinhdan->user_check_point_exist($server_id, $mobo_service_id, $tournament_list[0]["id"]);

        $result["code"] = "0";
        $result["message"] = $datauser[0]["user_point"];

        $this->output->set_output(json_encode($result));
    }

    private function user_point($char_id, $server_id, $mobo_service_id, $mobo_id, $char_name, $tournament_id) {
        $datauser = $this->tulinhdan->user_check_point_exist($server_id, $mobo_service_id, $tournament_id);
        if (count($datauser) > 0) {
            foreach ($datauser as $key => $value) {
                //Update Mobo Id
                if ($value["mobo_id"] == null || empty($value["mobo_id"]) || ($value["mobo_id"] != $mobo_id)) {
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
            $userdata_p["create_date"] = Date('Y-m-d H:i:s');
            $userdata_p["user_point"] = 0;
            $userdata_p["tournament_id"] = $tournament_id;

            $this->tulinhdan->insert("event_tulinhdan_point", $userdata_p);

            return 0;
        }
    }

    function get_money_test() {
        $mobo_service_id = $_GET["mobo_service_id"];
        $server_id = $_GET["server_id"];
        $date_check = $_GET["date_check"];

        $api = new GameFullAPI();
        $getmoney_api = $api->get_money($this->service_name, $mobo_service_id, $server_id, $date_check);
        var_dump($getmoney_api);
    }

    function random_luck($objRandom, $final_award, $total) {
        $keyItem = array();
        $weightItem = array();
        foreach ($final_award as $key => $value) {
            $current_mount = $value["reward_point"];
            $percent = ($current_mount / $total) * 1000000;
            array_push($keyItem, $value);
            array_push($weightItem, $percent);
        }
// randomize with weighted
        return $objRandom->weighted_random($keyItem, $weightItem);
    }

    //Shop
    function exchange_gift_shop() {
        if (empty($_SESSION['user_info'])) {
            echo "Không lấy được thông tin người dùng, bạn vui lòng làm mới trang.";
            die;
        }

        //Check Tournament Enable
        $tournament_list = $this->tulinhdan->tournament_list();
        if (count($tournament_list) == 0) {
            echo "Sự kiện đang tạm đóng, bạn vui lòng quay lại sau.";
            die;
        }

        $user = unserialize($_SESSION['user_info']);
        $this->data["user"] = $user;

        $char_id = $user->character_id;
        $server_id = $user->server_id;
        $char_name = $user->character_name;
        $mobo_service_id = $user->mobo_service_id;
        $mobo_id = $user->mobo_id;

        //User Point
        $this->data["user_point"] = $this->user_point($char_id, $server_id, $mobo_service_id, $mobo_id, $char_name, $tournament_list[0]["id"]);

        //Set Session
        $this->storeSession($mobo_service_id, $server_id);
        $gift_type = $_GET["id"];

        if ($gift_type == 3) {
            //VIP Gift Pakage
            $data["gift_list"] = $this->tulinhdan->get_gift_pakage_list_by_type($gift_type);
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
            $user_info = $api->get_user_info($mobo_service_id, $server_id);

            //var_dump($user_info); die;            
            $this->data["user_vip_point"] = $user_info['data']['data']['vipPoint'];

            $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
            echo $this->load->view("event/tulinhdan/doiquapakage_shop", $this->data, true);
        } else
        if ($gift_type == 4) {
            //Special Gift Pakage
            $data["gift_list"] = $this->tulinhdan->get_gift_pakage_special_list_by_type($gift_type);
            $gift_filter = array();

            foreach ($data["gift_list"] as $key => $value) {
                $server_list = preg_replace('/\s+/', '', $value["server_list"]);

                if ($server_list != "") {
                    $server_list = explode(";", $server_list);
                    if (in_array($server_id, $server_list)) {
                        array_push($gift_filter, Array("id" => $value["id"], "item_id" => $value["item_id"], "gift_name" => $value["gift_name"], "gift_price" => $value["gift_price"],
                            "gift_quantity" => $value["gift_quantity"], "gift_img" => $value["gift_img"], "gift_status" => $value["gift_status"], "gift_insert_date" => $value["gift_insert_date"],
                            "gift_date_start" => $value["gift_date_start"], "gift_date_end" => $value["gift_date_end"], "gift_number_request" => $value["gift_number_request"],
                            "gift_buy_max" => $value["gift_buy_max"], "reuqets_vip" => $value["gift_vip_point"], "gift_vip_point" => $value["gift_vip_point"]));
                    }
                }
            }

            $this->data["gift_list"] = $gift_filter;

            //User Point
            $this->data["user_point"] = $this->user_point($char_id, $server_id, $mobo_service_id, $mobo_id, $char_name);

            $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
            echo $this->load->view("event/tulinhdan/doiquapakage_s_shop", $this->data, true);
        } else {
            $data["gift_list"] = $this->tulinhdan->get_gift_list_by_type($gift_type);
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

            $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
            echo $this->load->view("events/tulinhdan/doiqua_shop", $this->data, true);
        }
    }

    function exchange_gift_by_shop() {
        $tournament_list = $this->tulinhdan->tournament_list();
        if (count($tournament_list) == 0) {
            $result["code"] = "-1";
            $result["message"] = "Sự kiện đang tạm đóng, bạn vui lòng quay lại sau.";
            $this->output->set_output(json_encode($result));
            return;
        }

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

            $user = unserialize($_SESSION['user_info']);
            $this->data["user"] = $user;

            $char_id = $user->character_id;
            $server_id = $user->server_id;
            $char_name = $user->character_name;
            $mobo_service_id = $user->mobo_service_id;
            $mobo_id = $user->mobo_id;

            $id = $_GET["id"];

            if (!$this->getSession($mobo_service_id, $server_id)) {
                $result["code"] = "-1";
                $result["message"] = "Bạn đang đăng nhập trên một máy khác.";
            } else {
                //Check Valid Point              
                $datauser = $this->tulinhdan->user_check_point_exist($server_id, $mobo_service_id, $tournament_list[0]["id"]);

                if (count($datauser) > 0) {
                    //Check Gift Valid
                    $gift_details = $this->tulinhdan->get_gift_details($id);

                    if (count($gift_details) > 0) {
                        foreach ($gift_details as $key => $value) {
                            //Check Server Valid
                            $server_list = explode(";", $value["server_list"]);
                            if (!in_array($server_id, $server_list)) {
                                $result["code"] = "-1";
                                $result["message"] = "Dữ liệu quà không hợp lệ* !";
                            } else {
                                if ($value["gift_type"] == 5) {
                                    //Check Buy Only One
                                    $check_max_exchange = $this->tulinhdan->get_total_gift_exchange_shop_onlyone($server_id, $mobo_service_id, 5);
                                    if ($check_max_exchange[0]["TotalExchange"] > 0) {
                                        $result["code"] = "-1";
                                        $result["message"] = "Loại quà này bạn chỉ được đổi một lần!";
                                        $this->output->set_output(json_encode($result));
                                        return;
                                    }
                                }

                                //Check Max Buy 
                                $check_max_exchange = $this->tulinhdan->get_total_gift_exchange_shop($server_id, $mobo_service_id, $id);
                                if ($value["gift_buy_max"] != 0 && (($check_max_exchange[0]["TotalExchange"] + $_GET["quantity"]) > $value["gift_buy_max"])) {
                                    $result["code"] = "-1";
                                    $result["message"] = "Bạn chỉ được đổi tối đa '" . $value["gift_buy_max"] . " " . $value["gift_name"] . "'!";
                                } else {
                                    $gift_price = $_GET["quantity"] * $value["gift_price"];

                                    //Item Info
                                    $item_id = $value["item_id"];
                                    $gift_send_type = $value["gift_send_type"];
                                    $item_quantity = $_GET["quantity"] * $value["gift_quantity"];
                                    $item_name = $value["gift_name"];
                                    $gift_type = $value["gift_type"];

                                    foreach ($datauser as $key => $value) {
                                        //Check Point Valid                        
                                        if ($gift_price > $value["user_point"]) {
                                            $result["code"] = "-1";
                                            $result["message"] = "Số dư Điểm Tích Lũy không đủ !";
                                        } else {
                                            //Add Gift Exchange History
                                            $userdata_p["mobo_service_id"] = $mobo_service_id;
                                            $userdata_p["char_id"] = $char_id;
                                            $userdata_p["server_id"] = $server_id;
                                            $userdata_p["char_name"] = $char_name;
                                            $userdata_p["user_id"] = $value["id"];
                                            $userdata_p["item_ex_id"] = $id;
                                            $userdata_p["exchange_gift_point"] = $gift_price;
                                            $userdata_p["exchange_gift_date"] = Date('Y-m-d H:i:s');
                                            $userdata_p["item_type_id"] = $gift_type;

                                            $i_id = $this->tulinhdan->insert_id("event_tulinhdan_shop_exchange_history", $userdata_p);

                                            if ($i_id > 0) {
                                                //Update Point
                                                if ($this->tulinhdan->update_point_shop($gift_price, $server_id, $mobo_service_id, $tournament_list[0]["id"]) > 0) {
                                                    //SEND Item
                                                    $item_create[] = array("item_id" => $item_id, "count" => (int) $item_quantity, 'item_type' => (int) $gift_send_type);
                                                    $gameapi = new GameFullAPI();
                                                    $data_result = $gameapi->add_item_result($this->service_name, $mobo_service_id, $server_id, $item_create, "Qua Shop Tu Linh Dan", "Qua Shop Tu Linh Dan", $char_id);
                                                    $this->tulinhdan->update_shop_exchange_history($i_id, json_encode($item_create), json_encode($data_result), $_GET["quantity"]);

                                                    $result["code"] = "0";
                                                    $result["message"] = "Đổi quà thành công !";
                                                } else {
                                                    $result["code"] = "-1";
                                                    $result["message"] = "Đổi quà thất bại, vui lòng thử lại*!";
                                                }
                                            } else {
                                                $result["code"] = "-1";
                                                $result["message"] = "Đổi quà thất bại, vui lòng thử lại**!";
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

    // Function to get the client IP address
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

    public function content_news() {
        $id = $_GET["id"];
        $api_url = "http://data.mobo.vn/home/get_post_id/$id/1/";
        $api_result = $this->call_api_get($api_url);
        $this->output->set_output($api_result);
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

}
