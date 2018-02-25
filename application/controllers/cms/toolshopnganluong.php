<?php

class toolshopnganluong extends CI_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('cms/m_toolshopnganluong');

        $this->output->set_header('Content-type: application/json');
        $this->output->set_header('Access-Control-Allow-Origin: *');
        $this->output->set_header('Access-Control-Allow-Headers: X-Requested-With');
        $this->output->set_header('Access-Control-Allow-Headers: Content-Type');
    }

    public function index() {
        
    }   
    
    //Tournament
     function add_tournament() {
        $tournament_name = addslashes($_GET['tournament_name']);
        $tournament_date_start = addslashes($_GET['startdate']);
        $tournament_date_end = addslashes($_GET['enddate']);
        $tournament_server_list = addslashes($_GET['tournament_server_list']);
        $tournament_status = ( is_numeric($_GET['catstatus']) && $_GET['catstatus'] >= 1) ? $_GET['catstatus'] : 0;

        $params = array(
            'tournament_name' => $tournament_name,
            'tournament_date_start' => $tournament_date_start,
            'tournament_date_end' => $tournament_date_end,
            'tournament_server_list' => $tournament_server_list,
            'tournament_status' => $tournament_status);

        $data = $this->m_toolshopnganluong->add_tournament($params);

        if ($data > 0) {
            $R["result"] = 1;
            $R["message"] = 'THÊM MỚI GIẢI ĐẤU THÀNH CÔNG !';
        } else {
            $R["result"] = -1;
            $R["message"] = 'THÊM MỚI GIẢI ĐẤU THẤT BẠI !';
        }

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

    function edit_tournament() {
        $id = addslashes($_GET['id']);
        $tournament_name = addslashes($_GET['tournament_name']);
        $tournament_date_start = addslashes($_GET['startdate']);
        $tournament_date_end = addslashes($_GET['enddate']);
        $tournament_server_list = addslashes($_GET['tournament_server_list']);
        $tournament_status = ( is_numeric($_GET['catstatus']) && $_GET['catstatus'] >= 1) ? $_GET['catstatus'] : 0;

        $params = array(
            'id' => $id,
            'tournament_name' => $tournament_name,
            'tournament_date_start' => $tournament_date_start,
            'tournament_date_end' => $tournament_date_end,
            'tournament_server_list' => $tournament_server_list,
            'tournament_status' => $tournament_status);

        $data = $this->m_toolshopnganluong->edit_tournament($params);

        if ($data > 0) {
            $R["result"] = 1;
            $R["message"] = 'CHỈNH SỬA GIẢI ĐẤU THÀNH CÔNG !';
        } else {
            $R["result"] = -1;
            $R["message"] = 'CHỈNH SỬA GIẢI ĐẤU THẤT BẠI !';
        }

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

    function edit_tournament_details() {
        $id = addslashes($_GET['id']);

        $tournament_date_start = addslashes($_GET['startdate']);
        $tournament_date_end = addslashes($_GET['enddate']);   
        $tournament_server_list = addslashes($_GET['server_list']);     
        $tournament_ip_list = addslashes($_GET['ip_list']);
        $tournament_status = ( is_numeric($_GET['catstatus']) && $_GET['catstatus'] >= 1) ? $_GET['catstatus'] : 0;     

        $params = array(
            'id' => $id,
            'tournament_date_start' => $tournament_date_start,
            'tournament_date_end' => $tournament_date_end,       
            'tournament_server_list' => $tournament_server_list,
            'tournament_ip_list' => $tournament_ip_list,          
            'tournament_status' => $tournament_status);

        $data = $this->m_toolshopnganluong->edit_tournament_details($params);

        if ($data > 0) {
            $R["result"] = 1;
            $R["message"] = 'CHỈNH SỬA GIẢI ĐẤU THÀNH CÔNG !';
        } else {
            $R["result"] = -1;
            $R["message"] = 'CHỈNH SỬA GIẢI ĐẤU THẤT BẠI !';
        }

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

    public function tournament_list() {
        $data = $this->m_toolshopnganluong->tournament_list();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    public function tournament_get_by_id() {
        $id = $_GET['id'];

        $data = $this->m_toolshopnganluong->tournament_get_by_id($id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    public function tournament_list_name_id() {
        $data = $this->m_toolshopnganluong->tournament_list_name_id();
        $this->output->json_encode($data);
    }

    //Gift
    function add_gift() {
        $item_id = $_GET['item_id'];
        $gift_name = $_GET['gift_name'];
        $gift_price = $_GET['gift_price'];
        $gift_quantity = $_GET['gift_quantity'];
        $gift_img = $_GET['gift_img'];
        $gift_status = $_GET['gift_status'];
        $server_list = $_GET['server_list'];
        $gift_buy_max = $_GET['gift_buy_max'];
        $gift_type = $_GET['gift_type'];

        $params = array(
            'item_id' => $item_id,
            'gift_name' => $gift_name,
            'gift_price' => $gift_price,
            'gift_quantity' => $gift_quantity,
            'gift_img' => $gift_img,
            'gift_status' => $gift_status,
            'server_list' => $server_list,
            'gift_type' => $gift_type,
            'gift_buy_max' => $gift_buy_max,
            'gift_insert_date' => date('Y-m-d H:i:s')
        );

        $data = $this->m_toolshopnganluong->add_gift($params);

        if ($data > 0) {
            $R["result"] = $data;
            $R["message"] = 'THÊM MỚI QUÀ THÀNH CÔNG !';
        } else {
            $R["result"] = -1;
            $R["message"] = 'THÊM MỚI QUÀ THẤT BẠI !';
        }

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
    
    function add_gift_pakage() {
        //$item_id = $_GET['item_id'];
        $gift_name = $_GET['gift_name'];
        $gift_price = $_GET['gift_price'];
        //$gift_quantity = $_GET['gift_quantity'];
        $gift_img = $_GET['gift_img'];
        $gift_status = $_GET['gift_status'];
        $server_list = $_GET['server_list'];
        $gift_buy_max = $_GET['gift_buy_max'];
        $gift_type = $_GET['gift_type'];        
        $gift_date_start =  $_GET['gift_date_start'];
        $gift_date_end =  $_GET['gift_date_end'];  
        $gift_vip_point =  $_GET['gift_vip_point'];
        $gift_number_request =  $_GET['gift_number_request'];
        $reward_item1_code = addslashes($_GET['reward_item1_code']);
        $reward_item1_number = addslashes($_GET['reward_item1_number']);
        $reward_item2_code = addslashes($_GET['reward_item2_code']);
        $reward_item2_number = addslashes($_GET['reward_item2_number']);
        $reward_item3_code = addslashes($_GET['reward_item3_code']);
        $reward_item3_number = addslashes($_GET['reward_item3_number']);
        $reward_item4_code = addslashes($_GET['reward_item4_code']);
        $reward_item4_number = addslashes($_GET['reward_item4_number']);
        $reward_item5_code = addslashes($_GET['reward_item5_code']);
        $reward_item5_number = addslashes($_GET['reward_item5_number']);

        $params = array(
            //'item_id' => $item_id,
            'gift_name' => $gift_name,
            'gift_price' => $gift_price,
            //'gift_quantity' => $gift_quantity,
            'gift_img' => $gift_img,
            'gift_status' => $gift_status,
            'server_list' => $server_list,
            'gift_type' => $gift_type,
            'gift_buy_max' => $gift_buy_max,            
            'gift_date_start' => $gift_date_start,
            'gift_date_end' => $gift_date_end,
            'gift_vip_point' => $gift_vip_point,
            'gift_number_request' => $gift_number_request,            
            'reward_item1_code' => $reward_item1_code,
            'reward_item1_number' => $reward_item1_number,
            'reward_item2_code' => $reward_item2_code,
            'reward_item2_number' => $reward_item2_number,
            'reward_item3_code' => $reward_item3_code,
            'reward_item3_number' => $reward_item3_number,
            'reward_item4_code' => $reward_item4_code,
            'reward_item4_number' => $reward_item4_number,
            'reward_item5_code' => $reward_item5_code,
            'reward_item5_number' => $reward_item5_number,
            
            
            'gift_insert_date' => date('Y-m-d H:i:s')
        );

        $data = $this->m_toolshopnganluong->add_gift_pakage($params);

        if ($data > 0) {
            $R["result"] = $data;
            $R["message"] = 'THÊM MỚI QUÀ THÀNH CÔNG !';
        } else {
            $R["result"] = -1;
            $R["message"] = 'THÊM MỚI QUÀ THẤT BẠI !';
        }

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

    function gift_list() {
        $id = $_GET["id"];
        if ($id != 0) {
            $data = $this->m_toolshopnganluong->gift_list_by_type($id);
        } else {
            $data = $this->m_toolshopnganluong->gift_list();
        }
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }
    
    function gift_list_pakage() {
        $id = $_GET["id"];
        $data = $this->m_toolshopnganluong->gift_list_pakage_by_type($id);

        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function gift_type_list() {
        $data = $this->m_toolshopnganluong->gift_type_list();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }
    
    function gift_type_list_pakage() {
        $data = $this->m_toolshopnganluong->gift_type_list_pakage();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function load_gift_details() {
        $id = $_GET['id'];

        $data = $this->m_toolshopnganluong->load_gift_details($id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function edit_gift() {
        $id = addslashes($_GET['id']);
        $item_id = addslashes($_GET['item_id']);
        $gift_name = addslashes($_GET['gift_name']);
        $gift_price = addslashes($_GET['gift_price']);
        $gift_quantity = addslashes($_GET['gift_quantity']);
        $gift_img = addslashes($_GET['gift_img']);
        $gift_status = addslashes($_GET['gift_status']);
        $server_list = $_GET['server_list'];
        $gift_type = $_GET['gift_type'];
        $gift_buy_max = $_GET['gift_buy_max'];

        $params = array(
            'id' => $id,
            'item_id' => $item_id,
            'gift_name' => $gift_name,
            'gift_price' => $gift_price,
            'gift_quantity' => $gift_quantity,
            'gift_img' => $gift_img,
            'server_list' => $server_list,
            'gift_status' => $gift_status,
            'gift_type' => $gift_type,
            'gift_buy_max' => $gift_buy_max);

        $data = $this->m_toolshopnganluong->edit_gift($params);

        if ($data > 0) {
            $R["result"] = 1;
            $R["message"] = 'CHỈNH SỬA QUÀ THÀNH CÔNG !';
        } else {
            $R["result"] = -1;
            $R["message"] = 'CHỈNH SỬA QUÀ THẤT BẠI !';
        }

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
    
    function load_gift_pakage_details() {
        $id = $_GET['id'];

        $data = $this->m_toolshopnganluong->load_gift_pakage_details($id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function edit_gift_pakage() {
        $id = addslashes($_GET['id']);        
        $gift_name = $_GET['gift_name'];
        $gift_price = $_GET['gift_price'];        
        $gift_img = $_GET['gift_img'];
        $gift_status = $_GET['gift_status'];
        $server_list = $_GET['server_list'];
        $gift_buy_max = $_GET['gift_buy_max'];
        $gift_type = $_GET['gift_type'];
        
        $gift_date_start =  $_GET['gift_date_start'];
        $gift_date_end =  $_GET['gift_date_end'];  
        $gift_vip_point =  $_GET['gift_vip_point'];
        $gift_number_request =  $_GET['gift_number_request'];
        $reward_item1_code = addslashes($_GET['reward_item1_code']);
        $reward_item1_number = addslashes($_GET['reward_item1_number']);
        $reward_item2_code = addslashes($_GET['reward_item2_code']);
        $reward_item2_number = addslashes($_GET['reward_item2_number']);
        $reward_item3_code = addslashes($_GET['reward_item3_code']);
        $reward_item3_number = addslashes($_GET['reward_item3_number']);
        $reward_item4_code = addslashes($_GET['reward_item4_code']);
        $reward_item4_number = addslashes($_GET['reward_item4_number']);
        $reward_item5_code = addslashes($_GET['reward_item5_code']);
        $reward_item5_number = addslashes($_GET['reward_item5_number']);

        $params = array(   
            'id' => $id,
            'gift_name' => $gift_name,
            'gift_price' => $gift_price,           
            'gift_img' => $gift_img,
            'gift_status' => $gift_status,
            'server_list' => $server_list,
            'gift_type' => $gift_type,
            'gift_buy_max' => $gift_buy_max,            
            'gift_date_start' => $gift_date_start,
            'gift_date_end' => $gift_date_end,
            'gift_vip_point' => $gift_vip_point,
            'gift_number_request' => $gift_number_request,            
            'reward_item1_code' => $reward_item1_code,
            'reward_item1_number' => $reward_item1_number,
            'reward_item2_code' => $reward_item2_code,
            'reward_item2_number' => $reward_item2_number,
            'reward_item3_code' => $reward_item3_code,
            'reward_item3_number' => $reward_item3_number,
            'reward_item4_code' => $reward_item4_code,
            'reward_item4_number' => $reward_item4_number,
            'reward_item5_code' => $reward_item5_code,
            'reward_item5_number' => $reward_item5_number);
        
        
        //var_dump($params); die;

        $data = $this->m_toolshopnganluong->edit_gift_pakage($params);

        if ($data > 0) {
            $R["result"] = 1;
            $R["message"] = 'CHỈNH SỬA QUÀ THÀNH CÔNG !';
        } else {
            $R["result"] = -1;
            $R["message"] = 'CHỈNH SỬA QUÀ THẤT BẠI !';
        }

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

    //History
    function get_gift_exchange_history() {
        $startdate = $_GET["startdate"];
        $enddate = $_GET["enddate"];
        $data = $this->m_toolshopnganluong->get_gift_exchange_history($startdate, $enddate);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }
    
    function get_gift_pakage_exchange_history() {
        $startdate = $_GET["startdate"];
        $enddate = $_GET["enddate"];
        $data = $this->m_toolshopnganluong->get_gift_pakage_exchange_history($startdate, $enddate);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_total_point_gift_exchange_history() {
        $startdate = $_GET["startdate"];
        $enddate = $_GET["enddate"];
        $data = $this->m_toolshopnganluong->get_total_point_gift_exchange_history($startdate, $enddate);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }
    
    function get_total_point_gift_pakage_exchange_history() {
        $startdate = $_GET["startdate"];
        $enddate = $_GET["enddate"];
        $data = $this->m_toolshopnganluong->get_total_point_gift_pakage_exchange_history($startdate, $enddate);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }   

    function get_top_user_point() {
        $data = $this->m_toolshopnganluong->get_top_user_point();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_exchange_g_history() {
        $data = $this->m_toolshopnganluong->get_exchange_g_history();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }    

    function get_recharging_history() {
        $startdate = $_GET["startdate"];
        $enddate = $_GET["enddate"];
        $data = $this->m_toolshopnganluong->get_recharging_history($startdate, $enddate);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_total_recharging_history() {
        $startdate = $_GET["startdate"];
        $enddate = $_GET["enddate"];
        $data = $this->m_toolshopnganluong->get_total_recharging_history($startdate, $enddate);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_exchange_history_top() {
        $data = $this->m_toolshopnganluong->get_exchange_history_top();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function load_gift_outgame_exchange_history_details() {
        $id = $_GET["id"];

        $data = $this->m_toolshopnganluong->load_gift_outgame_exchange_history_details($id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function edit_outgame_exchange_history() {
        $id = addslashes($_GET['id']);
        $receive_status = ( is_numeric($_GET['receive_status']) && $_GET['receive_status'] >= 1) ? $_GET['receive_status'] : 0;

        $params = array(
            'id' => $id,
            'receive_status' => $receive_status);

        $data = $this->m_toolshopnganluong->edit_outgame_exchange_history($params);

        if ($data > 0) {
            $R["result"] = 1;
            $R["message"] = 'CẬP NHẬT QUÀ OUTGAME THÀNH CÔNG !';
        } else {
            $R["result"] = -1;
            $R["message"] = 'CẬP NHẬT QUÀ OUTGAME THẤT BẠI !';
        }

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

    //Card Exchange
    function get_total_card_exchange_history() {
        $startdate = $_GET["startdate"];
        $enddate = $_GET["enddate"];
        $data = $this->m_toolshopnganluong->get_total_card_exchange_history($startdate, $enddate);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_card_exchange_history() {
        $startdate = $_GET["startdate"];
        $enddate = $_GET["enddate"];
        $data = $this->m_toolshopnganluong->get_card_exchange_history($startdate, $enddate);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function charging_config() {
        $id = 1;
        $charging_status = ( is_numeric($_GET['charging_status']) && $_GET['charging_status'] >= 1) ? $_GET['charging_status'] : 0;

        $params = array(
            'id' => $id,
            'charging_status' => $charging_status);

        $data = $this->m_toolshopnganluong->charging_config($params);

        if ($data > 0) {
            $R["result"] = 1;
            $R["message"] = 'CẬP NHẬT CẤU HÌNH NẠP THẺ THÀNH CÔNG !';
        } else {
            $R["result"] = -1;
            $R["message"] = 'CẬP NHẬT CẤU HÌNH NẠP THẺ THẤT BẠI !';
        }

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

    function get_charging_config() {
        $id = 1;
        $data = $this->m_toolshopnganluong->get_charging_config($id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    //Report
    function load_report_general() {
        $startdate = $_GET["startdate"];
        $enddate = $_GET["enddate"];

        $begin = new DateTime($startdate);
        $end = new DateTime($enddate);
        $end = $end->modify('+1 day');

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        $table_html = '<table class="table table-striped table-bordered dataTable no-footer" id="data_table" role="grid" aria-describedby="data_table_info">';
        $table_html .= '<tr role="row">
        <th class="sorting_disabled" rowspan="1" colspan="1">Ngày</th>
        <th class="sorting_disabled" rowspan="1" colspan="1">Tổng Nạp</th> 
        <th class="sorting_disabled" rowspan="1" colspan="1">Pay User</th> 
        <th class="sorting_disabled" rowspan="1" colspan="1">Tổng Điểm Đấu Trường</th>
        <th class="sorting_disabled" rowspan="1" colspan="1">Tổng Điểm Tồn</th>
        <th class="sorting_disabled" rowspan="1" colspan="1">Điểm Đổi Quà InGame</th>
        <th class="sorting_disabled" rowspan="1" colspan="1">Điểm Đổi Quà OutGame</th>
        <th class="sorting_disabled" rowspan="1" colspan="1">Tổng Phí</th></tr>';

        foreach ($period as $dt) {
            $startdate_cal = $dt->format("Y-m-d 00:00:00");
            $enddate_cal = $dt->format("Y-m-d 23:59:59");

            //Recharging
            $data_recharging = $this->m_toolshopnganluong->get_total_recharging_history($startdate_cal, $enddate_cal);
            if (is_null($data_recharging[0]["Total"])) {
                $data_recharging[0]["Total"] = 0;
            }
            
            //Recharging PU
            $data_recharging_pu = $this->m_toolshopnganluong->get_total_recharging_pu_history($startdate_cal, $enddate_cal);
            if (is_null($data_recharging_pu[0]["Total"])) {
                $data_recharging_pu[0]["Total"] = 0;
            }

            //Gift InGame
            $data_gift_exchange = $this->m_toolshopnganluong->get_total_point_gift_exchange_history($startdate_cal, $enddate_cal);
            if (is_null($data_gift_exchange[0]["Total"])) {
                $data_gift_exchange[0]["Total"] = 0;
            }
            
            //Gift Pakage InGame
            $data_gift_pakage_exchange = $this->m_toolshopnganluong->get_total_point_gift_pakage_exchange_history($startdate_cal, $enddate_cal);
            if (is_null($data_gift_pakage_exchange[0]["Total"])) {
                $data_gift_pakage_exchange[0]["Total"] = 0;
            }
            
            //Gift InGame DoPhuong
            $data_gift_exchange_dophuong = $this->m_toolshopnganluong->get_total_point_gift_exchange_history_dophuong($startdate_cal, $enddate_cal);
            if (is_null($data_gift_exchange_dophuong[0]["Total"])) {
                $data_gift_exchange_dophuong[0]["Total"] = 0;
            }
            
            //Gift Pakage InGame DoPhuong
            $data_gift_pakage_exchange_dophuong = $this->m_toolshopnganluong->get_total_point_gift_pakage_exchange_history_dophuong($startdate_cal, $enddate_cal);
            if (is_null($data_gift_pakage_exchange_dophuong[0]["Total"])) {
                $data_gift_pakage_exchange_dophuong[0]["Total"] = 0;
            }
            
            //Gift TLD Exchange
            $data_gift_exchange_tld = $this->m_toolshopnganluong->get_total_point_gift_exchange_tld_history($startdate_cal, $enddate_cal);
            if (is_null($data_gift_exchange_tld[0]["Total"])) {
                $data_gift_exchange_tld[0]["Total"] = 0;
            }
            
            $total_ex_ingame = $data_gift_exchange[0]["Total"] + $data_gift_exchange_tld[0]["Total"] + $data_gift_pakage_exchange[0]["Total"]
                    + $data_gift_exchange_dophuong[0]["Total"] + $data_gift_pakage_exchange_dophuong[0]["Total"];

            //Gift OutGame
            $total_card_exchange_point = $this->m_toolshopnganluong->get_total_card_exchange_history_point($startdate_cal, $enddate_cal);
            $data_gift_outgame_exchange = $this->m_toolshopnganluong->get_total_point_gift_outgame_exchange_history($startdate_cal, $enddate_cal);

            if (is_null($total_card_exchange_point[0]["Total"])) {
                $total_card_exchange_point[0]["Total"] = 0;
            }

            if (is_null($data_gift_outgame_exchange[0]["Total"])) {
                $data_gift_outgame_exchange[0]["Total"] = 0;
            }
            
            //Gift OutGame DoPhuong
            $data_gift_outgame_exchange_dophuong = $this->m_toolshopnganluong->get_total_point_gift_outgame_exchange_history_dophuong($startdate_cal, $enddate_cal);
            if (is_null($data_gift_outgame_exchange_dophuong[0]["Total"])) {
                $data_gift_outgame_exchange_dophuong[0]["Total"] = 0;
            }
            
            $total_outgame = $total_card_exchange_point[0]["Total"] + $data_gift_outgame_exchange[0]["Total"] + $data_gift_outgame_exchange_dophuong[0]["Total"];

            //Total Point Arena (Join + Point arena gift exchange)                                             
            $dataarena = $this->m_toolshopnganluong->get_total_point_arena($startdate_cal, $enddate_cal);
            $dataarena_ex = $this->m_toolshopnganluong->get_total_point_arena_gift_exchange($startdate_cal, $enddate_cal);
            if (is_null($dataarena[0]["TotalPoint"])) {
                $dataarena[0]["TotalPoint"] = 0;
            }
            if (is_null($dataarena_ex[0]["TotalPointEx"])) {
                $dataarena_ex[0]["TotalPointEx"] = 0;
            }
            
            //Add Point ThamGia DoPhuong to Point Arena
            $data_dophuong_history = $this->m_toolshopnganluong->get_total_point_dophuong_play_history($startdate_cal, $enddate_cal);
            if (is_null($data_dophuong_history[0]["Total"])) {
                $data_dophuong_history[0]["Total"] = 0;
            }
            
            $total_point_arena = $dataarena[0]["TotalPoint"] + $dataarena_ex[0]["TotalPointEx"] + $data_dophuong_history[0]["Total"];

            //Total Point User
            $datauser = $this->m_toolshopnganluong->get_total_point_user($startdate_cal, $enddate_cal);
            if (is_null($datauser[0]["Total"])) {
                $datauser[0]["Total"] = 0;
            }
            
            //Add Point DoPhuong User
            $datauser_dophuong = $this->m_toolshopnganluong->get_total_point_user_dophuong($startdate_cal, $enddate_cal);
            if (is_null($datauser_dophuong[0]["Total"])) {
                $datauser_dophuong[0]["Total"] = 0;
            }
            
            $total_point_user =  $datauser[0]["Total"] + $datauser_dophuong[0]["Total"];

            //Total Point GoldChange Tax
            $total_goldchange_tax = $this->m_toolshopnganluong->get_total_point_goldchange_tax($startdate_cal, $enddate_cal);
            if (is_null($total_goldchange_tax[0]["Total"])) {
                $total_goldchange_tax[0]["Total"] = 0;
            }            
            
            //Total Point DoPhuong Revenue
            $dophuong_revenue = $this->m_toolshopnganluong->get_total_point_revenue_dophuong($startdate_cal, $enddate_cal);
            if (is_null($dophuong_revenue[0]["Total"])) {
                $dophuong_revenue[0]["Total"] = 0;
            }
            
            //Total Point GoldChange Tax DoPhuong
            $total_goldchange_tax_dophuong = $this->m_toolshopnganluong->get_total_point_goldchange_tax_dophuong($startdate_cal, $enddate_cal);
            if (is_null($total_goldchange_tax_dophuong[0]["Total"])) {
                $total_goldchange_tax_dophuong[0]["Total"] = 0;
            }

            //Tong Phi
            $tong_phi = floor((($total_point_arena / 100) * 10) + $total_goldchange_tax[0]["Total"]) 
                    + $dophuong_revenue[0]["Total"] + $total_goldchange_tax_dophuong[0]["Total"];

            $table_html .= '<tr role="row" class="odd">
            <td>' . $dt->format("Y-m-d") . '</td>
            <td>' . $data_recharging[0]["Total"] . '</td>
            <td>' . $data_recharging_pu[0]["Total"] . '</td>    
            <td>' . $total_point_arena . '</td>
            <td>' . $total_point_user . '</td>
            <td>' . $total_ex_ingame . '</td>
            <td>' . $total_outgame . '</td>
            <td>' . $tong_phi . '</td>
            </tr>';
        }

        //Total Amount
        //Recharging
        $startdate_cal = $_GET["startdate"] . " 00:00:00";
        $enddate_cal = $_GET["enddate"] . " 23:59:59";

        $data_recharging = $this->m_toolshopnganluong->get_total_recharging_history($startdate_cal, $enddate_cal);
        if (is_null($data_recharging[0]["Total"])) {
            $data_recharging[0]["Total"] = 0;
        }
        
        //Recharging PU
        $data_recharging_pu = $this->m_toolshopnganluong->get_total_recharging_pu_history($startdate_cal, $enddate_cal);
        if (is_null($data_recharging_pu[0]["Total"])) {
            $data_recharging_pu[0]["Total"] = 0;
        }

        //Gift InGame
        $data_gift_exchange = $this->m_toolshopnganluong->get_total_point_gift_exchange_history($startdate_cal, $enddate_cal);
        if (is_null($data_gift_exchange[0]["Total"])) {
            $data_gift_exchange[0]["Total"] = 0;
        }
        
        //Gift Pakage InGame
        $data_gift_pakage_exchange = $this->m_toolshopnganluong->get_total_point_gift_pakage_exchange_history($startdate_cal, $enddate_cal);
        if (is_null($data_gift_pakage_exchange[0]["Total"])) {
            $data_gift_pakage_exchange[0]["Total"] = 0;
        }
        
        //Gift InGame DoPhuong
        $data_gift_exchange_dophuong = $this->m_toolshopnganluong->get_total_point_gift_exchange_history_dophuong($startdate_cal, $enddate_cal);
        if (is_null($data_gift_exchange_dophuong[0]["Total"])) {
            $data_gift_exchange_dophuong[0]["Total"] = 0;
        }

        //Gift Pakage InGame DoPhuong
        $data_gift_pakage_exchange_dophuong = $this->m_toolshopnganluong->get_total_point_gift_pakage_exchange_history_dophuong($startdate_cal, $enddate_cal);
        if (is_null($data_gift_pakage_exchange_dophuong[0]["Total"])) {
            $data_gift_pakage_exchange_dophuong[0]["Total"] = 0;
        }
        
        //Gift TLD Exchange
        $data_gift_exchange_tld = $this->m_toolshopnganluong->get_total_point_gift_exchange_tld_history($startdate_cal, $enddate_cal);
        if (is_null($data_gift_exchange_tld[0]["Total"])) {
            $data_gift_exchange_tld[0]["Total"] = 0;
        }

        $total_ex_ingame = $data_gift_exchange[0]["Total"] + $data_gift_exchange_tld[0]["Total"] + $data_gift_pakage_exchange[0]["Total"] 
                + $data_gift_exchange_dophuong[0]["Total"] + $data_gift_pakage_exchange_dophuong[0]["Total"];

        //Gift OutGame
        $total_card_exchange_point = $this->m_toolshopnganluong->get_total_card_exchange_history_point($startdate_cal, $enddate_cal);
        $data_gift_outgame_exchange = $this->m_toolshopnganluong->get_total_point_gift_outgame_exchange_history($startdate_cal, $enddate_cal);

        if (is_null($total_card_exchange_point[0]["Total"])) {
            $total_card_exchange_point[0]["Total"] = 0;
        }

        if (is_null($data_gift_outgame_exchange[0]["Total"])) {
            $data_gift_outgame_exchange[0]["Total"] = 0;
        }

        //Gift OutGame DoPhuong
        $data_gift_outgame_exchange_dophuong = $this->m_toolshopnganluong->get_total_point_gift_outgame_exchange_history_dophuong($startdate_cal, $enddate_cal);
        if (is_null($data_gift_outgame_exchange_dophuong[0]["Total"])) {
            $data_gift_outgame_exchange_dophuong[0]["Total"] = 0;
        }

        $total_outgame = $total_card_exchange_point[0]["Total"] + $data_gift_outgame_exchange[0]["Total"] + $data_gift_outgame_exchange_dophuong[0]["Total"];

        //Total Point Arena (Join + Point arena gift exchange)                                             
        $dataarena = $this->m_toolshopnganluong->get_total_point_arena($startdate_cal, $enddate_cal);
        $dataarena_ex = $this->m_toolshopnganluong->get_total_point_arena_gift_exchange($startdate_cal, $enddate_cal);
        if (is_null($dataarena[0]["TotalPoint"])) {
            $dataarena[0]["TotalPoint"] = 0;
        }
        if (is_null($dataarena_ex[0]["TotalPointEx"])) {
            $dataarena_ex[0]["TotalPointEx"] = 0;
        }
        //Add Point ThamGia DoPhuong to Point Arena
        $data_dophuong_history = $this->m_toolshopnganluong->get_total_point_dophuong_play_history($startdate_cal, $enddate_cal);
        if (is_null($data_dophuong_history[0]["Total"])) {
                $data_dophuong_history[0]["Total"] = 0;
        }
            
        $total_point_arena = $dataarena[0]["TotalPoint"] + $dataarena_ex[0]["TotalPointEx"] + $data_dophuong_history[0]["Total"];       

        //Total Point User
        $datauser = $this->m_toolshopnganluong->get_total_point_user($startdate_cal, $enddate_cal);
        if (is_null($datauser[0]["Total"])) {
            $datauser[0]["Total"] = 0;
        }
        
        //Add Point DoPhuong User
        $datauser_dophuong = $this->m_toolshopnganluong->get_total_point_user_dophuong($startdate_cal, $enddate_cal);
        if (is_null($datauser_dophuong[0]["Total"])) {
            $datauser_dophuong[0]["Total"] = 0;
        }

        $total_point_user =  $datauser[0]["Total"] + $datauser_dophuong[0]["Total"];

        //Total Point GoldChange Tax
        $total_goldchange_tax = $this->m_toolshopnganluong->get_total_point_goldchange_tax($startdate_cal, $enddate_cal);
        if (is_null($total_goldchange_tax[0]["Total"])) {
            $total_goldchange_tax[0]["Total"] = 0;
        }            

        //Total Point DoPhuong Revenue
        $dophuong_revenue = $this->m_toolshopnganluong->get_total_point_revenue_dophuong($startdate_cal, $enddate_cal);
        if (is_null($dophuong_revenue[0]["Total"])) {
            $dophuong_revenue[0]["Total"] = 0;
        }

        //Total Point GoldChange Tax DoPhuong
        $total_goldchange_tax_dophuong = $this->m_toolshopnganluong->get_total_point_goldchange_tax_dophuong($startdate_cal, $enddate_cal);
        if (is_null($total_goldchange_tax_dophuong[0]["Total"])) {
            $total_goldchange_tax_dophuong[0]["Total"] = 0;
        }

        //Tong Phi
        $tong_phi = floor((($total_point_arena / 100) * 10) + $total_goldchange_tax[0]["Total"]) 
                + $dophuong_revenue[0]["Total"] + $total_goldchange_tax_dophuong[0]["Total"];

        $table_html .= '<tr role="row" class="odd">
            <td style="font-weight: bold;">Tổng Cộng:</td> 
            <td>' . $data_recharging[0]["Total"] . '</td>
            <td>' . $data_recharging_pu[0]["Total"] . '</td>
            <td>' . $total_point_arena . '</td>
            <td>' . $total_point_user . '</td>
            <td>' . $total_ex_ingame . '</td>
            <td>' . $total_outgame . '</td>
            <td>' . $tong_phi . '</td>
            </tr>';

        $table_html .= '</table>';

        $result["code"] = "0";
        $result["message"] = $table_html;

        $R["result"] = -1;
        $R["message"] = $table_html;

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
        //echo json_encode($result);
        //$this->output->set_output(json_encode($result));
    }

    //Add point top
    public function add_point_top() {
        $tournament_id = $_GET['tournament'];
        $server_id = $_GET['server_id'];
        $mobo_service_id = $_GET['mobo_service_id'];
        $bonus_point = $_GET['bonus_point'];
        $note = $_GET['note'];
        $email = $_GET['email'];

        //Check Exist            
        if ($this->m_toolshopnganluong->check_exist_exchange_gift_top($tournament_id, $server_id, $mobo_service_id)) {
            $R["result"] = -1;
            $R["message"] = 'MSI đã trao thưởng giải đấu này';
        } else {
            //Add exchange History
            $userdata_p["char_id"] = null;
            $userdata_p["server_id"] = $server_id;
            $userdata_p["char_name"] = null;
            $userdata_p["mobo_service_id"] = $mobo_service_id;
            $userdata_p["reward_id"] = null;
            $userdata_p["exchange_date"] = Date('Y-m-d H:i:s');
            $userdata_p["tournament_id"] = $tournament_id;
            $userdata_p["note"] = $note;
            $userdata_p["ause_username"] = $email;

            $i_id = $this->m_toolshopnganluong->insert_id("event_shopnganluong_exchange_history_top", $userdata_p);

            if ($i_id > 0) {
                //Add Bonus Point
                $datauser = $this->m_toolshopnganluong->user_check_point_exist($userdata_p["char_id"], $server_id, $mobo_service_id);
                if (count($datauser) > 0) {
                    if ($this->m_toolshopnganluong->add_point($userdata_p["char_id"], $server_id, $mobo_service_id, $bonus_point) > 0) {
                        $this->m_toolshopnganluong->update_exchange_history_top($i_id, $bonus_point, $bonus_point);

                        $R["result"] = 0;
                        $R["message"] = "Trao thưởng '" . $bonus_point . "' Ngân Lượng thành công!";
                    } else {
                        $R["result"] = -1;
                        $R["message"] = 'Trao thưởng thất bại*';
                    }
                } else {
                    //User Point Not Found
                    $R["result"] = -1;
                    $R["message"] = 'Tài khoản không chính xác';
                }
            } else {
                $R["result"] = -1;
                $R["message"] = 'Trao thưởng thất bại**';
            }
        }

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

}

?>