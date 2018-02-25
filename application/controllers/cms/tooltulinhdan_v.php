<?php

class tooltulinhdan extends CI_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('cms/m_tooltulinhdan');

        $this->output->set_header('Content-type: application/json');
        $this->output->set_header('Access-Control-Allow-Origin: *');
        $this->output->set_header('Access-Control-Allow-Headers: X-Requested-With');
        $this->output->set_header('Access-Control-Allow-Headers: Content-Type');
    }

    public function index() {
        
    }

    function add_tournament() {
        $tournament_name = addslashes($_GET['tournament_name']);
        $tournament_date_start = addslashes($_GET['startdate']);
        $tournament_date_end = addslashes($_GET['enddate']);
        $tournament_status = ( is_numeric($_GET['catstatus']) && $_GET['catstatus'] >= 1) ? $_GET['catstatus'] : 0;

        $params = array(
            'tournament_name' => $tournament_name,
            'tournament_date_start' => $tournament_date_start,
            'tournament_date_end' => $tournament_date_end,
            'tournament_status' => $tournament_status);

        $data = $this->m_tooltulinhdan->add_tournament($params);

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
        $tournament_status = ( is_numeric($_GET['catstatus']) && $_GET['catstatus'] >= 1) ? $_GET['catstatus'] : 0;

        $params = array(
            'id' => $id,
            'tournament_name' => $tournament_name,
            'tournament_date_start' => $tournament_date_start,
            'tournament_date_end' => $tournament_date_end,
            'tournament_status' => $tournament_status);

        $data = $this->m_tooltulinhdan->edit_tournament($params);

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

//        $tournament_date_start_reward = addslashes($_GET['startdate_reward']);
//        $tournament_date_end_reward = addslashes($_GET['enddate_reward']);  

        $tournament_server_list = addslashes($_GET['server_list']);
        $tournament_ip_list = addslashes($_GET['ip_list']);

        $tournament_money = addslashes($_GET['tournament_money']);

        $tournament_status = ( is_numeric($_GET['catstatus']) && $_GET['catstatus'] >= 1) ? $_GET['catstatus'] : 0;

        $params = array(
            'id' => $id,
            'tournament_date_start' => $tournament_date_start,
            'tournament_date_end' => $tournament_date_end,
//            'tournament_date_start_reward'=>$tournament_date_start_reward,
//            'tournament_date_end_reward'=>$tournament_date_end_reward,
            'tournament_server_list' => $tournament_server_list,
            'tournament_ip_list' => $tournament_ip_list,
            'tournament_money' => $tournament_money,
            'tournament_status' => $tournament_status);

        $data = $this->m_tooltulinhdan->edit_tournament_details($params);

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
        $data = $this->m_tooltulinhdan->tournament_list();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    public function tournament_get_by_id() {
        $id = $_GET['id'];

        $data = $this->m_tooltulinhdan->tournament_get_by_id($id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    public function tournament_list_name_id() {
        $data = $this->m_tooltulinhdan->tournament_list_name_id();
        $this->output->json_encode($data);
    }

    //Reward
    function add_reward() {
        $tournament_id = $_GET['tournament_id'];
        $reward_name = $_GET['reward_name'];
		$type = $_GET['type'];

        $params = array(
            'tournament_id' => $tournament_id,
			'type' => $type,
            'reward_name' => $reward_name,
            'reward_point' => 0,
            'reward_img' => 'http://ginside.mobo.vn/assets/img/no-image.png',
            'reward_item1_code' => 0,
            'reward_item1_number' => 0,
            'reward_item2_code' => 0,
            'reward_item2_number' => 0,
            'reward_item3_code' => 0,
            'reward_item3_number' => 0,
            'reward_item4_code' => 0,
            'reward_item4_number' => 0,
            'reward_item5_code' => 0,
            'reward_item5_number' => 0);

        $data = $this->m_tooltulinhdan->add_reward($params);

        if ($data > 0) {
            $R["result"] = $data;
            $R["message"] = 'THÊM MỚI MỐC THƯỞNG THÀNH CÔNG !';
        } else {
            $R["result"] = -1;
            $R["message"] = 'THÊM MỚI MỐC THƯỞNG THẤT BẠI !';
        }

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

    function load_reward() {
        $tournament_id = $_GET['tournament_id'];

        $data = $this->m_tooltulinhdan->load_reward($tournament_id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function load_reward_details() {
        $id = $_GET['id'];

        $data = $this->m_tooltulinhdan->load_reward_details($id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function edit_reward_details() {
        $id = $_POST['id'];
		$type = $_POST['type'];

        $reward_point = $_POST['reward_point'];
        $reward_img = $_POST['reward_img'];

        $reward_item1_code = $_POST['reward_item1_code'];
        $reward_item1_number = $_POST['reward_item1_number'];
        $reward_item1_type = $_POST['reward_item1_type'];
        $reward_item2_code = $_POST['reward_item2_code'];
        $reward_item2_number = $_POST['reward_item2_number'];
        $reward_item2_type = $_POST['reward_item2_type'];
        $reward_item3_code = $_POST['reward_item3_code'];
        $reward_item3_number = $_POST['reward_item3_number'];
        $reward_item3_type = $_POST['reward_item3_type'];
        $reward_item4_code = $_POST['reward_item4_code'];
        $reward_item4_number = $_POST['reward_item4_number'];
        $reward_item4_type = $_POST['reward_item4_type'];
        $reward_item5_code = $_POST['reward_item5_code'];
        $reward_item5_number = $_POST['reward_item5_number'];
        $reward_item5_type = $_POST['reward_item5_type'];
        $reward_item6_code = $_POST['reward_item6_code'];
        $reward_item6_number = $_POST['reward_item6_number'];
        $reward_item6_type = $_POST['reward_item6_type'];
        $reward_item7_code = $_POST['reward_item7_code'];
        $reward_item7_number = $_POST['reward_item7_number'];
        $reward_item7_type = $_POST['reward_item7_type'];
        $reward_item8_code = $_POST['reward_item8_code'];
        $reward_item8_number = $_POST['reward_item8_number'];
        $reward_item8_type = $_POST['reward_item8_type'];
        $reward_item9_code = $_POST['reward_item9_code'];
        $reward_item9_number = $_POST['reward_item9_number'];
        $reward_item9_type = $_POST['reward_item9_type'];
        $reward_item10_code = $_POST['reward_item10_code'];
        $reward_item10_number = $_POST['reward_item10_number'];
        $reward_item10_type = $_POST['reward_item10_type'];
        $reward_vip_count = $_POST['reward_vip_count'];
        $reward_status = $_POST['reward_status'];

        $params = array(
            'id' => $id,
			'type' => $type,
            'reward_point' => $reward_point,
            'reward_img' => $reward_img,
            'reward_item1_code' => $reward_item1_code,
            'reward_item1_number' => $reward_item1_number,
            'reward_item1_type' => $reward_item1_type,
            'reward_item2_code' => $reward_item2_code,
            'reward_item2_number' => $reward_item2_number,
            'reward_item2_type' => $reward_item2_type,
            'reward_item3_code' => $reward_item3_code,
            'reward_item3_number' => $reward_item3_number,
            'reward_item3_type' => $reward_item3_type,
            'reward_item4_code' => $reward_item4_code,
            'reward_item4_number' => $reward_item4_number,
            'reward_item4_type' => $reward_item4_type,
            'reward_item5_code' => $reward_item5_code,
            'reward_item5_number' => $reward_item5_number,
            'reward_item5_type' => $reward_item5_type,
            'reward_item6_code' => $reward_item6_code,
            'reward_item6_number' => $reward_item6_number,
            'reward_item6_type' => $reward_item6_type,
            'reward_item7_code' => $reward_item7_code,
            'reward_item7_number' => $reward_item7_number,
            'reward_item7_type' => $reward_item7_type,
            'reward_item8_code' => $reward_item8_code,
            'reward_item8_number' => $reward_item8_number,
            'reward_item8_type' => $reward_item8_type,
            'reward_item9_code' => $reward_item9_code,
            'reward_item9_number' => $reward_item9_number,
            'reward_item9_type' => $reward_item9_type,
            'reward_item10_code' => $reward_item10_code,
            'reward_item10_number' => $reward_item10_number,
            'reward_item10_type' => $reward_item10_type,
            'reward_vip_count' => $reward_vip_count,
            'reward_status' => $reward_status);

        $data = $this->m_tooltulinhdan->edit_reward_details($params);

        if ($data > 0) {
            $R["result"] = 1;
            $R["message"] = 'CHỈNH SỬA GIẢI THƯỞNG THÀNH CÔNG !';
        } else {
            $R["result"] = -1;
            $R["message"] = 'CHỈNH SỬA GIẢI THƯỞNG THẤT BẠI !';
        }

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

    function edit_reward_name() {
        $id = addslashes($_GET['id']);
        $reward_name = addslashes($_GET['reward_name']);

        $params = array(
            'id' => $id,
            'reward_name' => $reward_name);

        $data = $this->m_tooltulinhdan->edit_reward_name($params);

        $R["result"] = 1;
        $R["message"] = 'CHỈNH SỬA TÊN GIẢI THƯỞNG THÀNH CÔNG !';

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
    
    //Shop
    function gift_type_list() {
        $data = $this->m_tooltulinhdan->gift_type_list();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }
    
    function gift_list() {
        $id = $_GET["id"];
        if ($id != 0) {
            $data = $this->m_tooltulinhdan->gift_list_by_type($id);
        } else {
            $data = $this->m_tooltulinhdan->gift_list();
        }
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }
    
    function add_gift() {
        $item_id = $_GET['item_id'];
        $gift_name = $_GET['gift_name'];
        $gift_price = $_GET['gift_price'];
        $server_list = $_GET['server_list'];
        $gift_quantity = $_GET['gift_quantity'];
        $gift_type = $_GET['gift_type'];
        $gift_send_type = $_GET['gift_send_type'];
        $gift_img = $_GET['gift_img'];
        $gift_status = $_GET['gift_status'];       
        $gift_buy_max = $_GET['gift_buy_max'];      

        $params = array(
            'item_id' => $item_id,
            'gift_name' => $gift_name,
            'gift_price' => $gift_price,
            'server_list' => $server_list,
            'gift_quantity' => $gift_quantity,
            'gift_type' => $gift_type,
            'gift_send_type' => $gift_send_type,
            'gift_img' => $gift_img,
            'gift_status' => $gift_status,                      
            'gift_buy_max' => $gift_buy_max,
            'gift_insert_date' => date('Y-m-d H:i:s')
        );

        $data = $this->m_tooltulinhdan->add_gift($params);

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
    
    function load_gift_details() {
        $id = $_GET['id'];

        $data = $this->m_tooltulinhdan->load_gift_details($id);
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
        $gift_send_type = $_GET['gift_send_type'];
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
            'gift_send_type' => $gift_send_type,
            'gift_buy_max' => $gift_buy_max);

        $data = $this->m_tooltulinhdan->edit_gift($params);

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

    //Reward Top
    function add_reward_top() {
        $tournament_id = $_GET['tournament_id'];
        $reward_name = $_GET['reward_name'];
        $reward_rank_min = $_GET['reward_rank_min'];
        $reward_rank_max = $_GET['reward_rank_max'];

        $params = array(
            'tournament_id' => $tournament_id,
            'reward_name' => $reward_name,
            'reward_rank_min' => $reward_rank_min,
            'reward_rank_max' => $reward_rank_max,
            'reward_point' => 0,
            'reward_img' => null,
            'reward_item1_code' => 0,
            'reward_item1_number' => 0,
            'reward_item2_code' => 0,
            'reward_item2_number' => 0,
            'reward_item3_code' => 0,
            'reward_item3_number' => 0,
            'reward_item4_code' => 0,
            'reward_item4_number' => 0,
            'reward_item5_code' => 0,
            'reward_item5_number' => 0,
            'reward_item6_code' => 0,
            'reward_item6_number' => 0,
            'reward_item7_code' => 0,
            'reward_item7_number' => 0,
            'reward_item8_code' => 0,
            'reward_item8_number' => 0,
            'reward_item9_code' => 0,
            'reward_item9_number' => 0,
            'reward_item10_code' => 0,
            'reward_item10_number' => 0);

        $data = $this->m_tooltulinhdan->add_reward_top($params);

        if ($data > 0) {
            $R["result"] = $data;
            $R["message"] = 'THÊM MỚI MỐC THƯỞNG THÀNH CÔNG !';
        } else {
            $R["result"] = -1;
            $R["message"] = 'THÊM MỚI MỐC THƯỞNG THẤT BẠI !';
        }

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

    function load_reward_top() {
        $tournament_id = $_GET['tournament_id'];

        $data = $this->m_tooltulinhdan->load_reward_top($tournament_id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function load_reward_details_top() {
        $id = $_GET['id'];

        $data = $this->m_tooltulinhdan->load_reward_details_top($id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function edit_reward_details_top() {
        $id = addslashes($_GET['id']);

        $reward_point = addslashes($_GET['reward_point']);
        $reward_img = addslashes($_GET['Thumb']);

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
        $reward_item6_code = addslashes($_GET['reward_item6_code']);
        $reward_item6_number = addslashes($_GET['reward_item6_number']);
        $reward_item7_code = addslashes($_GET['reward_item7_code']);
        $reward_item7_number = addslashes($_GET['reward_item7_number']);
        $reward_item8_code = addslashes($_GET['reward_item8_code']);
        $reward_item8_number = addslashes($_GET['reward_item8_number']);
        $reward_item9_code = addslashes($_GET['reward_item9_code']);
        $reward_item9_number = addslashes($_GET['reward_item9_number']);
        $reward_item10_code = addslashes($_GET['reward_item10_code']);
        $reward_item10_number = addslashes($_GET['reward_item10_number']);

        $reward_status = addslashes($_GET['reward_status']);

        $params = array(
            'id' => $id,
            'reward_point' => $reward_point,
            'reward_img' => $reward_img,
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
            'reward_item6_code' => $reward_item6_code,
            'reward_item6_number' => $reward_item6_number,
            'reward_item7_code' => $reward_item7_code,
            'reward_item7_number' => $reward_item7_number,
            'reward_item8_code' => $reward_item8_code,
            'reward_item8_number' => $reward_item8_number,
            'reward_item9_code' => $reward_item9_code,
            'reward_item9_number' => $reward_item9_number,
            'reward_item10_code' => $reward_item10_code,
            'reward_item10_number' => $reward_item10_number,
            'reward_status' => $reward_status);

        $data = $this->m_tooltulinhdan->edit_reward_details_top($params);

        if ($data > 0) {
            $R["result"] = 1;
            $R["message"] = 'CHỈNH SỬA GIẢI THƯỞNG THÀNH CÔNG !';
        } else {
            $R["result"] = -1;
            $R["message"] = 'CHỈNH SỬA GIẢI THƯỞNG THẤT BẠI !';
        }

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

    function edit_reward_name_top() {
        $id = addslashes($_GET['id']);
        $reward_name = addslashes($_GET['reward_name']);
        $reward_rank_min = addslashes($_GET['reward_rank_min']);
        $reward_rank_max = addslashes($_GET['reward_rank_max']);

        $params = array(
            'id' => $id,
            'reward_name' => $reward_name,
            'reward_rank_min' => $reward_rank_min,
            'reward_rank_max' => $reward_rank_max);

        $data = $this->m_tooltulinhdan->edit_reward_name_top($params);

        $R["result"] = 1;
        $R["message"] = 'CHỈNH SỬA GIẢI THƯỞNG THÀNH CÔNG !';

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

    //History
    function get_exchange_history() {
        $tournament_id = $_GET["tournament_id"];
        $startdate = $_GET["startdate"];
        $enddate = $_GET["enddate"];

        $data = $this->m_tooltulinhdan->get_exchange_history($tournament_id, $startdate, $enddate);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_exchange_history_excel() {
        // Load the Library
        $this->load->library("excel");
        //activate worksheet number 1
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('Log Co Vu');

        $tournament_id = $_GET["tournament_id"];
        $startdate = $_GET["startdate"];
        $enddate = $_GET["enddate"];

        $data = $this->m_tooltulinhdan->get_exchange_history_excel($tournament_id, $startdate, $enddate);
        //var_dump($data); die;
        $this->excel->stream('event_vongquaymayman_logdoiqua.xls', $data);
    }

    function get_exchange_history_top() {
        $data = $this->m_tooltulinhdan->get_exchange_history_top();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_exchange_history_premiership() {
        $data = $this->m_tooltulinhdan->get_exchange_history_premiership();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }
    
    function get_exchange_history_shop() {        
        $startdate = $_GET["startdate"];
        $enddate = $_GET["enddate"];

        $data = $this->m_tooltulinhdan->get_exchange_history_shop($startdate, $enddate);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }
    
    function get_top_user_point() {
        $data = $this->m_tooltulinhdan->get_top_user_point();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

}

?>