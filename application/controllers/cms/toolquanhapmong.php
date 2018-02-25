<?php

class toolquanhapmong extends CI_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('cms/m_toolquanhapmong');

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
        $tournament_server_list = addslashes($_GET['tournament_server_list']);
        $tournament_status = ( is_numeric($_GET['catstatus']) && $_GET['catstatus'] >= 1) ? $_GET['catstatus'] : 0;

        $params = array(
            'tournament_name' => $tournament_name,
            'tournament_date_start' => $tournament_date_start,
            'tournament_date_end' => $tournament_date_end,
            'tournament_server_list' => $tournament_server_list,
            'tournament_status' => $tournament_status);

        $data = $this->m_toolquanhapmong->add_tournament($params);

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

        $data = $this->m_toolquanhapmong->edit_tournament($params);

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

        $data = $this->m_toolquanhapmong->edit_tournament_details($params);

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
        $data = $this->m_toolquanhapmong->tournament_list();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    public function tournament_get_by_id() {
        $id = $_GET['id'];

        $data = $this->m_toolquanhapmong->tournament_get_by_id($id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    public function tournament_list_name_id() {
        $data = $this->m_toolquanhapmong->tournament_list_name_id();
        $this->output->json_encode($data);
    }

    //Match
    public function match_list() {
        $tournament_id = $_GET['tournament_id'];
        $data = $this->m_toolquanhapmong->match_list($tournament_id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function add_match() {
        $tournament_id = $_GET['tournament_id'];

        $match_team_name_a = $_GET['match_team_name_a'];
        $match_team_img_a = $_GET['match_team_img_a'];
        $match_team_chap_a = $_GET['match_team_chap_a'];
        $match_team_win_rate_a = $_GET['match_team_win_rate_a'];

        $match_team_name_b = $_GET['match_team_name_b'];
        $match_team_img_b = $_GET['match_team_img_b'];
        $match_team_chap_b = $_GET['match_team_chap_b'];
        $match_team_win_rate_b = $_GET['match_team_win_rate_b'];

        $match_start_date = $_GET['match_start_date'];
        $match_end_date = $_GET['match_end_date'];
        $match_end_pet_date = $_GET['match_end_pet_date'];
        $match_status = ( is_numeric($_GET['match_status']) && $_GET['match_status'] >= 1) ? $_GET['match_status'] : 0;
        $match_pet_max = $_GET['match_pet_max'];

        $params = array(
            'tournament_id' => $tournament_id,
            'match_team_name_a' => $match_team_name_a,
            'match_team_img_a' => $match_team_img_a,
            'match_team_chap_a' => $match_team_chap_a,
            'match_team_win_rate_a' => $match_team_win_rate_a,
            'match_team_name_b' => $match_team_name_b,
            'match_team_img_b' => $match_team_img_b,
            'match_team_chap_b' => $match_team_chap_b,
            'match_team_win_rate_b' => $match_team_win_rate_b,
            'match_start_date' => $match_start_date,
            'match_end_date' => $match_end_date,
            'match_end_pet_date' => $match_end_pet_date,
            'match_status' => $match_status,
            'match_pet_max' => $match_pet_max
        );

        $data = $this->m_toolquanhapmong->add_match($params);

        if ($data > 0) {
            $R["result"] = $data;
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

    function load_match_details() {
        $id = $_GET['id'];

        $data = $this->m_toolquanhapmong->load_match_details($id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function edit_match_details() {
        $id = addslashes($_GET['id']);
        $tournament_id = addslashes($_GET['tournament_id']);
        $match_team_name_a = addslashes($_GET['match_team_name_a']);
        $match_team_img_a = addslashes($_GET['match_team_img_a']);
        $match_team_chap_a = addslashes($_GET['match_team_chap_a']);
        $match_team_win_rate_a = addslashes($_GET['match_team_win_rate_a']);
        $match_team_name_b = addslashes($_GET['match_team_name_b']);
        $match_team_img_b = addslashes($_GET['match_team_img_b']);
        $match_start_date = addslashes($_GET['match_start_date']);
        $match_end_date = addslashes($_GET['match_end_date']);
        $match_team_chap_b = addslashes($_GET['match_team_chap_b']);
        $match_end_pet_date = addslashes($_GET['match_end_pet_date']);
        $match_status = addslashes($_GET['match_status']);
        $match_pet_max = addslashes($_GET['match_pet_max']);
        $match_team_win_rate_b = addslashes($_GET['match_team_win_rate_b']);

        $params = array(
            'id' => $id,
            'tournament_id' => $tournament_id,
            'match_team_name_a' => $match_team_name_a,
            'match_team_img_a' => $match_team_img_a,
            'match_team_chap_a' => $match_team_chap_a,
            'match_team_win_rate_a' => $match_team_win_rate_a,
            'match_team_name_b' => $match_team_name_b,
            'match_team_img_b' => $match_team_img_b,
            'match_start_date' => $match_start_date,
            'match_end_date' => $match_end_date,
            'match_team_chap_b' => $match_team_chap_b,
            'match_end_pet_date' => $match_end_pet_date,
            'match_status' => $match_status,
            'match_pet_max' => $match_pet_max,
            'match_team_win_rate_b' => $match_team_win_rate_b);

        $data = $this->m_toolquanhapmong->edit_match_details($params);

        if ($data > 0) {
            $R["result"] = 1;
            $R["message"] = 'CHỈNH SỬA TRẬN ĐẤU THÀNH CÔNG !';
        } else {
            $R["result"] = -1;
            $R["message"] = 'CHỈNH SỬA TRẬN ĐẤU THẤT BẠI !';
        }

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

    function update_match_result() {
        $id = addslashes($_GET['id']);
        $match_result_team_a = addslashes($_GET['match_result_team_a']);
        $match_result_team_b = addslashes($_GET['match_result_team_b']);

        $params = array(
            'id' => $id,
            'match_result_team_a' => $match_result_team_a,
            'match_result_team_b' => $match_result_team_b
        );

        $data = $this->m_toolquanhapmong->update_match_result($params);

        if ($data > 0) {
            $R["result"] = 1;
            $R["message"] = 'CẬP NHẬT KẾT QUẢ TRẬN ĐẤU THÀNH CÔNG !';
        } else {
            $R["result"] = -1;
            $R["message"] = 'CẬP NHẬT KẾT QUẢ TRẬN ĐẤU THẤT BẠI !';
        }

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

    //Gift
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

        $data = $this->m_toolquanhapmong->add_gift($params);

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
        $gift_name = $_GET['gift_name'];
        $gift_price = $_GET['gift_price'];      
        $gift_img = $_GET['gift_img'];
        $gift_status = $_GET['gift_status'];
        $server_list = $_GET['server_list'];
        $gift_buy_max = $_GET['gift_buy_max'];
        $gift_type = $_GET['gifttype'];        
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
            'reward_item5_number' => $reward_item5_number,  
            'gift_insert_date' => date('Y-m-d H:i:s')
        );

        $data = $this->m_toolquanhapmong->add_gift_pakage($params);

        if ($data > 0) {
            $R["result"] = $data;
            $R["message"] = 'THÊM MỚI GÓI QUÀ THÀNH CÔNG !';
        } else {
            $R["result"] = -1;
            $R["message"] = 'THÊM MỚI GÓI QUÀ THẤT BẠI !';
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
            $data = $this->m_toolquanhapmong->gift_list_by_type($id);
        } else {
            $data = $this->m_toolquanhapmong->gift_list();
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
        $data = $this->m_toolquanhapmong->gift_list_pakage_by_type($id);

        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function gift_type_list() {
        $data = $this->m_toolquanhapmong->gift_type_list();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }
    
    function gift_type_list_pakage() {
        $data = $this->m_toolquanhapmong->gift_type_list_pakage();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function load_gift_details() {
        $id = $_GET['id'];

        $data = $this->m_toolquanhapmong->load_gift_details($id);
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

        $data = $this->m_toolquanhapmong->edit_gift($params);

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

        $data = $this->m_toolquanhapmong->load_gift_pakage_details($id);
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
        $gift_type = $_GET['gifttype'];        
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
            'reward_item5_number' => $reward_item5_number
            );
        
        
        //var_dump($params); die;

        $data = $this->m_toolquanhapmong->edit_gift_pakage($params);

        if ($data > 0) {
            $R["result"] = 1;
            $R["message"] = 'CHỈNH SỬA GÓI QUÀ THÀNH CÔNG !';
        } else {
            $R["result"] = -1;
            $R["message"] = 'CHỈNH SỬA GÓI QUÀ THẤT BẠI !';
        }

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

    //Gift OutGame
    function add_gift_outgame() {
        $item_id = $_GET['item_id'];
        $gift_name = $_GET['gift_name'];
        $gift_price = $_GET['gift_price'];
        $gift_quantity = $_GET['gift_quantity'];
        $gift_img = $_GET['gift_img'];
        $gift_status = $_GET['gift_status'];
        $gift_status = $_GET['gift_status'];
        //$server_list = $_GET['server_list']; 

        $params = array(
            'item_id' => $item_id,
            'gift_name' => $gift_name,
            'gift_price' => $gift_price,
            'gift_quantity' => $gift_quantity,
            'gift_img' => $gift_img,
            'gift_status' => $gift_status,
            //'server_list'=>$server_list,
            'gift_insert_date' => date('Y-m-d H:i:s')
        );

        $data = $this->m_toolquanhapmong->add_gift_outgame($params);

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

    function gift_list_outgame() {
        $data = $this->m_toolquanhapmong->gift_list_outgame();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function load_gift_details_outgame() {
        $id = $_GET['id'];

        $data = $this->m_toolquanhapmong->load_gift_details_outgame($id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function edit_gift_details_outgame() {
        $id = addslashes($_GET['id']);
        $item_id = addslashes($_GET['item_id']);
        $gift_name = addslashes($_GET['gift_name']);
        $gift_price = addslashes($_GET['gift_price']);
        $gift_quantity = addslashes($_GET['gift_quantity']);
        $gift_img = addslashes($_GET['gift_img']);
        $gift_status = addslashes($_GET['gift_status']);
        //$server_list = $_GET['server_list'];  

        $params = array(
            'id' => $id,
            'item_id' => $item_id,
            'gift_name' => $gift_name,
            'gift_price' => $gift_price,
            'gift_quantity' => $gift_quantity,
            'gift_img' => $gift_img,
            //'server_list'=>$server_list,
            'gift_status' => $gift_status);

        $data = $this->m_toolquanhapmong->edit_gift_details_outgame($params);

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

    //Reward
    function add_reward() {
        $tournament_id = $_GET['tournament_id'];
        $reward_name = $_GET['reward_name'];

        $params = array(
            'tournament_id' => $tournament_id,
            'reward_name' => $reward_name,
            'reward_point' => 0,
            'max_exchange' => 0,
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
            'reward_item5_number' => 0);

        $data = $this->m_toolquanhapmong->add_reward($params);

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

        $data = $this->m_toolquanhapmong->load_reward($tournament_id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function load_reward_details() {
        $id = $_GET['id'];

        $data = $this->m_toolquanhapmong->load_reward_details($id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function edit_reward_details() {
        $id = addslashes($_GET['id']);

        $reward_point = addslashes($_GET['reward_point']);
        $max_exchange = addslashes($_GET['max_exchange']);
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

        $params = array(
            'id' => $id,
            'reward_point' => $reward_point,
            'max_exchange' => $max_exchange,
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
            'reward_item5_number' => $reward_item5_number);

        $data = $this->m_toolquanhapmong->edit_reward_details($params);

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
            'reward_rate' => 0);

        $data = $this->m_toolquanhapmong->add_reward_top($params);

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

        $data = $this->m_toolquanhapmong->load_reward_top($tournament_id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function load_reward_details_top() {
        $id = $_GET['id'];

        $data = $this->m_toolquanhapmong->load_reward_details_top($id);
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
        $reward_status = addslashes($_GET['reward_status']);
        $reward_rate = addslashes($_GET['reward_rate']);

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
            'reward_status' => $reward_status,
            'reward_rate' => $reward_rate);

        $data = $this->m_toolquanhapmong->edit_reward_details_top($params);

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

        $data = $this->m_toolquanhapmong->edit_reward_name_top($params);

        $R["result"] = 1;
        $R["message"] = 'CHỈNH SỬA GIẢI THƯỞNG THÀNH CÔNG !';

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

    //Code
    function gen_code() {
        $cacuoc_code_value = $_GET['cacuoc_code_value'];
        $code_quantity = $_GET['code_quantity'];
        $ause_username = $_GET['ause_username'];
        $type_code_id = $_GET['code_type'];
        //Get Code Type
        $code_type = $_GET['code_type'];
        $get_code_type_by_id = $this->m_toolquanhapmong->get_code_type_by_id($code_type);

        if ($get_code_type_by_id > 0) {
            for ($i = 0; $i < $code_quantity; $i++) {
                $params = array(
                    'ause_username' => $ause_username,
                    'cacuoc_code' => $get_code_type_by_id["type_code"] . $this->generateRandomString(),
                    'cacuoc_code_value' => $cacuoc_code_value,
                    'cacuoc_code_staus' => 0,
                    'type_code_id' => $type_code_id,
                    'gen_date' => date('Y-m-d H:i:s'));

                $data += $this->m_toolquanhapmong->gen_code($params);
            }

            if ($data > 0) {
                $R["result"] = $data;
                $R["message"] = 'GEN CODE THÀNH CÔNG !';
            } else {
                $R["result"] = -1;
                $R["message"] = 'GEN CODE THẤT BẠI !';
            }
        } else {
            $R["result"] = -1;
            $R["message"] = 'CODE TYPE KHÔNG HỢP LỆ !';
        }

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

    function code_list() {
        $type_code_id = $_GET['code_type'];

        $data = $this->m_toolquanhapmong->code_list($type_code_id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function generateRandomString($length = 8) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function code_type() {
        $data = $this->m_toolquanhapmong->code_type();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function add_code_type() {
        $type_code = strtoupper($_GET['type_code']);

        if (strlen($type_code) != 4) {
            $R["result"] = -1;
            $R["message"] = 'LOẠI CODE PHẢI 4 KÝ TỰ !';
        } else if ($data = $this->m_toolquanhapmong->check_exist_code_type($type_code)) {
            $R["result"] = -1;
            $R["message"] = 'LOẠI CODE ĐÃ TỒN TẠI !';
        } else {
            $params = array(
                'type_code' => $type_code
            );

            $data = $this->m_toolquanhapmong->add_code_type($params);

            if ($data > 0) {
                $R["result"] = $data;
                $R["message"] = 'THÊM MỚI LOẠI CODE THÀNH CÔNG !';
            } else {
                $R["result"] = -1;
                $R["message"] = 'THÊM MỚI LOẠI CODE THẤT BẠI !';
            }
        }

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

    function export_code_excel() {
        $data = array(
            array("firstname" => "Mary", "lastname" => "Johnson", "age" => 25),
            array("firstname" => "Amanda", "lastname" => "Miller", "age" => 18),
            array("firstname" => "James", "lastname" => "Brown", "age" => 31),
            array("firstname" => "Patricia", "lastname" => "Williams", "age" => 7),
            array("firstname" => "Michael", "lastname" => "Davis", "age" => 43),
            array("firstname" => "Sarah", "lastname" => "Miller", "age" => 24),
            array("firstname" => "Patrick", "lastname" => "Miller", "age" => 27)
        );

        // filename for download
        $filename = "website_data_" . date('Ymd') . ".xls";

        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");

        $flag = false;
        foreach ($data as $row) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, 'cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
        exit;
    }

    function cleanData($str) {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if (strstr($str, '"'))
            $str = '"' . str_replace('"', '""', $str) . '"';
    }

    //History
    function get_pet_history_details() {
        $data = $this->m_toolquanhapmong->get_pet_history_details();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_pet_history_details_by_tournament() {
        $tournament_id = $_GET["id"];

        $data = $this->m_toolquanhapmong->get_pet_history_details_by_tournament($tournament_id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_point_exchange_history() {
        $data = $this->m_toolquanhapmong->get_point_exchange_history();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_gift_exchange_history() {
        $startdate = $_GET["startdate"];
        $enddate = $_GET["enddate"];
        $data = $this->m_toolquanhapmong->get_gift_exchange_history($startdate, $enddate);
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
        $data = $this->m_toolquanhapmong->get_gift_pakage_exchange_history($startdate, $enddate);
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
        $data = $this->m_toolquanhapmong->get_total_point_gift_exchange_history($startdate, $enddate);
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
        $data = $this->m_toolquanhapmong->get_total_point_gift_pakage_exchange_history($startdate, $enddate);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_gift_arena_exchange_history() {
        //$startdate = $_GET["startdate"];
        //$enddate = $_GET["enddate"];
        $tournament_id = $_GET["id"];
        $data = $this->m_toolquanhapmong->get_gift_arena_exchange_history($tournament_id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_total_point_gift_arena_exchange_history() {
        //$startdate = $_GET["startdate"];
        //$enddate = $_GET["enddate"];
        $tournament_id = $_GET["id"];
        $data = $this->m_toolquanhapmong->get_total_point_gift_arena_exchange_history($tournament_id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_gift_outgame_exchange_history() {
        $startdate = $_GET["startdate"];
        $enddate = $_GET["enddate"];
        $data = $this->m_toolquanhapmong->get_gift_outgame_exchange_history($startdate, $enddate);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_total_point_gift_outgame_exchange_history() {
        $startdate = $_GET["startdate"];
        $enddate = $_GET["enddate"];
        $data = $this->m_toolquanhapmong->get_total_point_gift_outgame_exchange_history($startdate, $enddate);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_top_user_point() {
        $data = $this->m_toolquanhapmong->get_top_user_point();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_exchange_g_history() {
        $data = $this->m_toolquanhapmong->get_exchange_g_history();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_history_join_arena() {
        $tournament_id = $_GET["id"];

        $data = $this->m_toolquanhapmong->get_history_join_arena($tournament_id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_total_point_join_arena() {
        $tournament_id = $_GET["id"];
        $data = $this->m_toolquanhapmong->get_total_point_join_arena($tournament_id);
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
        $data = $this->m_toolquanhapmong->get_recharging_history($startdate, $enddate);
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
        $data = $this->m_toolquanhapmong->get_total_recharging_history($startdate, $enddate);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_exchange_history_top() {
        $data = $this->m_toolquanhapmong->get_exchange_history_top();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function load_gift_outgame_exchange_history_details() {
        $id = $_GET["id"];

        $data = $this->m_toolquanhapmong->load_gift_outgame_exchange_history_details($id);
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

        $data = $this->m_toolquanhapmong->edit_outgame_exchange_history($params);

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
    
    //History CH Transfer
    function get_quanhapmong_point_transfer_history() {
        $startdate = $_GET["startdate"];
        $enddate = $_GET["enddate"];
        $data = $this->m_toolquanhapmong->get_quanhapmong_point_transfer_history($startdate, $enddate);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }
    
    function get_quanhapmong_point_add_history() {
        $startdate = $_GET["startdate"];
        $enddate = $_GET["enddate"];
        $data = $this->m_toolquanhapmong->get_quanhapmong_point_add_history($startdate, $enddate);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    //Card Exchange
    function get_total_card_exchange_history() {
        $startdate = $_GET["startdate"];
        $enddate = $_GET["enddate"];
        $data = $this->m_toolquanhapmong->get_total_card_exchange_history($startdate, $enddate);
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
        $data = $this->m_toolquanhapmong->get_card_exchange_history($startdate, $enddate);
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

        $data = $this->m_toolquanhapmong->charging_config($params);

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
        $data = $this->m_toolquanhapmong->get_charging_config($id);
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
            $data_recharging = $this->m_toolquanhapmong->get_total_recharging_history($startdate_cal, $enddate_cal);
            if (is_null($data_recharging[0]["Total"])) {
                $data_recharging[0]["Total"] = 0;
            }
            
            //Recharging PU
            $data_recharging_pu = $this->m_toolquanhapmong->get_total_recharging_pu_history($startdate_cal, $enddate_cal);
            if (is_null($data_recharging_pu[0]["Total"])) {
                $data_recharging_pu[0]["Total"] = 0;
            }

            //Gift InGame
            $data_gift_exchange = $this->m_toolquanhapmong->get_total_point_gift_exchange_history($startdate_cal, $enddate_cal);
            if (is_null($data_gift_exchange[0]["Total"])) {
                $data_gift_exchange[0]["Total"] = 0;
            }
            
            //Gift Pakage InGame
            $data_gift_pakage_exchange = $this->m_toolquanhapmong->get_total_point_gift_pakage_exchange_history($startdate_cal, $enddate_cal);
            if (is_null($data_gift_pakage_exchange[0]["Total"])) {
                $data_gift_pakage_exchange[0]["Total"] = 0;
            }
            
            //Gift InGame DoPhuong
            $data_gift_exchange_dophuong = $this->m_toolquanhapmong->get_total_point_gift_exchange_history_dophuong($startdate_cal, $enddate_cal);
            if (is_null($data_gift_exchange_dophuong[0]["Total"])) {
                $data_gift_exchange_dophuong[0]["Total"] = 0;
            }
            
            //Gift Pakage InGame DoPhuong
            $data_gift_pakage_exchange_dophuong = $this->m_toolquanhapmong->get_total_point_gift_pakage_exchange_history_dophuong($startdate_cal, $enddate_cal);
            if (is_null($data_gift_pakage_exchange_dophuong[0]["Total"])) {
                $data_gift_pakage_exchange_dophuong[0]["Total"] = 0;
            }
            
            //Gift TLD Exchange
            $data_gift_exchange_tld = $this->m_toolquanhapmong->get_total_point_gift_exchange_tld_history($startdate_cal, $enddate_cal);
            if (is_null($data_gift_exchange_tld[0]["Total"])) {
                $data_gift_exchange_tld[0]["Total"] = 0;
            }
            
            $total_ex_ingame = $data_gift_exchange[0]["Total"] + $data_gift_exchange_tld[0]["Total"] + $data_gift_pakage_exchange[0]["Total"]
                    + $data_gift_exchange_dophuong[0]["Total"] + $data_gift_pakage_exchange_dophuong[0]["Total"];

            //Gift OutGame
            $total_card_exchange_point = $this->m_toolquanhapmong->get_total_card_exchange_history_point($startdate_cal, $enddate_cal);
            $data_gift_outgame_exchange = $this->m_toolquanhapmong->get_total_point_gift_outgame_exchange_history($startdate_cal, $enddate_cal);

            if (is_null($total_card_exchange_point[0]["Total"])) {
                $total_card_exchange_point[0]["Total"] = 0;
            }

            if (is_null($data_gift_outgame_exchange[0]["Total"])) {
                $data_gift_outgame_exchange[0]["Total"] = 0;
            }
            
            //Gift OutGame DoPhuong
            $data_gift_outgame_exchange_dophuong = $this->m_toolquanhapmong->get_total_point_gift_outgame_exchange_history_dophuong($startdate_cal, $enddate_cal);
            if (is_null($data_gift_outgame_exchange_dophuong[0]["Total"])) {
                $data_gift_outgame_exchange_dophuong[0]["Total"] = 0;
            }
            
            $total_outgame = $total_card_exchange_point[0]["Total"] + $data_gift_outgame_exchange[0]["Total"] + $data_gift_outgame_exchange_dophuong[0]["Total"];

            //Total Point Arena (Join + Point arena gift exchange)                                             
            $dataarena = $this->m_toolquanhapmong->get_total_point_arena($startdate_cal, $enddate_cal);
            $dataarena_ex = $this->m_toolquanhapmong->get_total_point_arena_gift_exchange($startdate_cal, $enddate_cal);
            if (is_null($dataarena[0]["TotalPoint"])) {
                $dataarena[0]["TotalPoint"] = 0;
            }
            if (is_null($dataarena_ex[0]["TotalPointEx"])) {
                $dataarena_ex[0]["TotalPointEx"] = 0;
            }
            
            //Add Point ThamGia DoPhuong to Point Arena
            $data_dophuong_history = $this->m_toolquanhapmong->get_total_point_dophuong_play_history($startdate_cal, $enddate_cal);
            if (is_null($data_dophuong_history[0]["Total"])) {
                $data_dophuong_history[0]["Total"] = 0;
            }
            
            $total_point_arena = $dataarena[0]["TotalPoint"] + $dataarena_ex[0]["TotalPointEx"] + $data_dophuong_history[0]["Total"];

            //Total Point User
            $datauser = $this->m_toolquanhapmong->get_total_point_user($startdate_cal, $enddate_cal);
            if (is_null($datauser[0]["Total"])) {
                $datauser[0]["Total"] = 0;
            }
            
            //Add Point DoPhuong User
            $datauser_dophuong = $this->m_toolquanhapmong->get_total_point_user_dophuong($startdate_cal, $enddate_cal);
            if (is_null($datauser_dophuong[0]["Total"])) {
                $datauser_dophuong[0]["Total"] = 0;
            }
            
            $total_point_user =  $datauser[0]["Total"] + $datauser_dophuong[0]["Total"];

            //Total Point GoldChange Tax
            $total_goldchange_tax = $this->m_toolquanhapmong->get_total_point_goldchange_tax($startdate_cal, $enddate_cal);
            if (is_null($total_goldchange_tax[0]["Total"])) {
                $total_goldchange_tax[0]["Total"] = 0;
            }            
            
            //Total Point DoPhuong Revenue
            $dophuong_revenue = $this->m_toolquanhapmong->get_total_point_revenue_dophuong($startdate_cal, $enddate_cal);
            if (is_null($dophuong_revenue[0]["Total"])) {
                $dophuong_revenue[0]["Total"] = 0;
            }
            
            //Total Point GoldChange Tax DoPhuong
            $total_goldchange_tax_dophuong = $this->m_toolquanhapmong->get_total_point_goldchange_tax_dophuong($startdate_cal, $enddate_cal);
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

        $data_recharging = $this->m_toolquanhapmong->get_total_recharging_history($startdate_cal, $enddate_cal);
        if (is_null($data_recharging[0]["Total"])) {
            $data_recharging[0]["Total"] = 0;
        }
        
        //Recharging PU
        $data_recharging_pu = $this->m_toolquanhapmong->get_total_recharging_pu_history($startdate_cal, $enddate_cal);
        if (is_null($data_recharging_pu[0]["Total"])) {
            $data_recharging_pu[0]["Total"] = 0;
        }

        //Gift InGame
        $data_gift_exchange = $this->m_toolquanhapmong->get_total_point_gift_exchange_history($startdate_cal, $enddate_cal);
        if (is_null($data_gift_exchange[0]["Total"])) {
            $data_gift_exchange[0]["Total"] = 0;
        }
        
        //Gift Pakage InGame
        $data_gift_pakage_exchange = $this->m_toolquanhapmong->get_total_point_gift_pakage_exchange_history($startdate_cal, $enddate_cal);
        if (is_null($data_gift_pakage_exchange[0]["Total"])) {
            $data_gift_pakage_exchange[0]["Total"] = 0;
        }
        
        //Gift InGame DoPhuong
        $data_gift_exchange_dophuong = $this->m_toolquanhapmong->get_total_point_gift_exchange_history_dophuong($startdate_cal, $enddate_cal);
        if (is_null($data_gift_exchange_dophuong[0]["Total"])) {
            $data_gift_exchange_dophuong[0]["Total"] = 0;
        }

        //Gift Pakage InGame DoPhuong
        $data_gift_pakage_exchange_dophuong = $this->m_toolquanhapmong->get_total_point_gift_pakage_exchange_history_dophuong($startdate_cal, $enddate_cal);
        if (is_null($data_gift_pakage_exchange_dophuong[0]["Total"])) {
            $data_gift_pakage_exchange_dophuong[0]["Total"] = 0;
        }
        
        //Gift TLD Exchange
        $data_gift_exchange_tld = $this->m_toolquanhapmong->get_total_point_gift_exchange_tld_history($startdate_cal, $enddate_cal);
        if (is_null($data_gift_exchange_tld[0]["Total"])) {
            $data_gift_exchange_tld[0]["Total"] = 0;
        }

        $total_ex_ingame = $data_gift_exchange[0]["Total"] + $data_gift_exchange_tld[0]["Total"] + $data_gift_pakage_exchange[0]["Total"] 
                + $data_gift_exchange_dophuong[0]["Total"] + $data_gift_pakage_exchange_dophuong[0]["Total"];

        //Gift OutGame
        $total_card_exchange_point = $this->m_toolquanhapmong->get_total_card_exchange_history_point($startdate_cal, $enddate_cal);
        $data_gift_outgame_exchange = $this->m_toolquanhapmong->get_total_point_gift_outgame_exchange_history($startdate_cal, $enddate_cal);

        if (is_null($total_card_exchange_point[0]["Total"])) {
            $total_card_exchange_point[0]["Total"] = 0;
        }

        if (is_null($data_gift_outgame_exchange[0]["Total"])) {
            $data_gift_outgame_exchange[0]["Total"] = 0;
        }

        //Gift OutGame DoPhuong
        $data_gift_outgame_exchange_dophuong = $this->m_toolquanhapmong->get_total_point_gift_outgame_exchange_history_dophuong($startdate_cal, $enddate_cal);
        if (is_null($data_gift_outgame_exchange_dophuong[0]["Total"])) {
            $data_gift_outgame_exchange_dophuong[0]["Total"] = 0;
        }

        $total_outgame = $total_card_exchange_point[0]["Total"] + $data_gift_outgame_exchange[0]["Total"] + $data_gift_outgame_exchange_dophuong[0]["Total"];

        //Total Point Arena (Join + Point arena gift exchange)                                             
        $dataarena = $this->m_toolquanhapmong->get_total_point_arena($startdate_cal, $enddate_cal);
        $dataarena_ex = $this->m_toolquanhapmong->get_total_point_arena_gift_exchange($startdate_cal, $enddate_cal);
        if (is_null($dataarena[0]["TotalPoint"])) {
            $dataarena[0]["TotalPoint"] = 0;
        }
        if (is_null($dataarena_ex[0]["TotalPointEx"])) {
            $dataarena_ex[0]["TotalPointEx"] = 0;
        }
        //Add Point ThamGia DoPhuong to Point Arena
        $data_dophuong_history = $this->m_toolquanhapmong->get_total_point_dophuong_play_history($startdate_cal, $enddate_cal);
        if (is_null($data_dophuong_history[0]["Total"])) {
                $data_dophuong_history[0]["Total"] = 0;
        }
            
        $total_point_arena = $dataarena[0]["TotalPoint"] + $dataarena_ex[0]["TotalPointEx"] + $data_dophuong_history[0]["Total"];       

        //Total Point User
        $datauser = $this->m_toolquanhapmong->get_total_point_user($startdate_cal, $enddate_cal);
        if (is_null($datauser[0]["Total"])) {
            $datauser[0]["Total"] = 0;
        }
        
        //Add Point DoPhuong User
        $datauser_dophuong = $this->m_toolquanhapmong->get_total_point_user_dophuong($startdate_cal, $enddate_cal);
        if (is_null($datauser_dophuong[0]["Total"])) {
            $datauser_dophuong[0]["Total"] = 0;
        }

        $total_point_user =  $datauser[0]["Total"] + $datauser_dophuong[0]["Total"];

        //Total Point GoldChange Tax
        $total_goldchange_tax = $this->m_toolquanhapmong->get_total_point_goldchange_tax($startdate_cal, $enddate_cal);
        if (is_null($total_goldchange_tax[0]["Total"])) {
            $total_goldchange_tax[0]["Total"] = 0;
        }            

        //Total Point DoPhuong Revenue
        $dophuong_revenue = $this->m_toolquanhapmong->get_total_point_revenue_dophuong($startdate_cal, $enddate_cal);
        if (is_null($dophuong_revenue[0]["Total"])) {
            $dophuong_revenue[0]["Total"] = 0;
        }

        //Total Point GoldChange Tax DoPhuong
        $total_goldchange_tax_dophuong = $this->m_toolquanhapmong->get_total_point_goldchange_tax_dophuong($startdate_cal, $enddate_cal);
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
        if ($this->m_toolquanhapmong->check_exist_exchange_gift_top($tournament_id, $server_id, $mobo_service_id)) {
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

            $i_id = $this->m_toolquanhapmong->insert_id("event_quanhapmong_exchange_history_top", $userdata_p);

            if ($i_id > 0) {
                //Add Bonus Point
                $datauser = $this->m_toolquanhapmong->user_check_point_exist($userdata_p["char_id"], $server_id, $mobo_service_id);
                if (count($datauser) > 0) {
                    if ($this->m_toolquanhapmong->add_point($userdata_p["char_id"], $server_id, $mobo_service_id, $bonus_point) > 0) {
                        $this->m_toolquanhapmong->update_exchange_history_top($i_id, $bonus_point, $bonus_point);

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