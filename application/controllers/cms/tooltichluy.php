<?php

class tooltichluy extends CI_Controller {

    protected $secret_key = "qgvQ^cD#h68_h9_Hu9+";

    function __construct() {
        parent::__construct();

        $this->load->model('cms/m_tooltichluy');

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
        $tournament_server_list = addslashes($_GET['tournament_server_list']);

        $params = array(
            'tournament_name' => $tournament_name,
            'tournament_date_start' => $tournament_date_start,
            'tournament_date_end' => $tournament_date_end,
            'tournament_status' => $tournament_status,
            'tournament_server_list' => $tournament_server_list);

        $data = $this->m_tooltichluy->add_tournament($params);

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

        $data = $this->m_tooltichluy->edit_tournament($params);

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

        $tournament_date_start_reward = addslashes($_GET['startdatereward']);
        $tournament_date_end_reward = addslashes($_GET['enddatereward']);

        $tournament_server_list = addslashes($_GET['server_list']);
        $tournament_ip_list = addslashes($_GET['ip_list']);

        $tournament_status = ( is_numeric($_GET['catstatus']) && $_GET['catstatus'] >= 1) ? $_GET['catstatus'] : 0;
        $reward_percent = addslashes($_GET['reward_percent']);

        $params = array(
            'id' => $id,
            'tournament_date_start' => $tournament_date_start,
            'tournament_date_end' => $tournament_date_end,
            'tournament_date_start_reward' => $tournament_date_start_reward,
            'tournament_date_end_reward' => $tournament_date_end_reward,
            'tournament_server_list' => $tournament_server_list,
            'tournament_ip_list' => $tournament_ip_list,
            'tournament_status' => $tournament_status,
            'reward_percent' => $reward_percent);

        $data = $this->m_tooltichluy->edit_tournament_details($params);

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
        $data = $this->m_tooltichluy->tournament_list();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    public function tournament_get_by_id() {
        $id = $_GET['id'];

        $data = $this->m_tooltichluy->tournament_get_by_id($id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    public function tournament_list_name_id() {
        $data = $this->m_tooltichluy->tournament_list_name_id();
        $this->output->json_encode($data);
    }

    //Tournament TotalDate
    function add_tournament_totaldate() {
        $tournament_name = addslashes($_GET['tournament_name']);
        $tournament_date_start = addslashes($_GET['startdate']);
        $tournament_date_end = addslashes($_GET['enddate']);
        $tournament_status = ( is_numeric($_GET['catstatus']) && $_GET['catstatus'] >= 1) ? $_GET['catstatus'] : 0;
        $tournament_server_list = addslashes($_GET['server_list']);
        $reward_percent = addslashes($_GET['reward_percent']);

        $params = array(
            'tournament_name' => $tournament_name,
            'tournament_date_start' => $tournament_date_start,
            'tournament_date_end' => $tournament_date_end,
            'tournament_status' => $tournament_status,
            'tournament_server_list' => $tournament_server_list,
            'reward_percent' => $reward_percent);

        $data = $this->m_tooltichluy->add_tournament_totaldate($params);

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

    function edit_tournament_totaldate() {
        $id = addslashes($_GET['id']);
        $tournament_name = addslashes($_GET['tournament_name']);
        $tournament_date_start = addslashes($_GET['startdate']);
        $tournament_date_end = addslashes($_GET['enddate']);
        $tournament_status = ( is_numeric($_GET['catstatus']) && $_GET['catstatus'] >= 1) ? $_GET['catstatus'] : 0;
        $tournament_server_list = addslashes($_GET['tournament_server_list']);
        $reward_percent = addslashes($_GET['reward_percent']);

        $params = array(
            'id' => $id,
            'tournament_name' => $tournament_name,
            'tournament_date_start' => $tournament_date_start,
            'tournament_date_end' => $tournament_date_end,
            'tournament_status' => $tournament_status,
            'tournament_server_list' => $tournament_server_list,
            'reward_percent' => $reward_percent);

        $data = $this->m_tooltichluy->edit_tournament_totaldate($params);

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

    function edit_tournament_totaldate_details() {
        $id = addslashes($_GET['id']);

        $tournament_date_start = addslashes($_GET['startdate']);
        $tournament_date_end = addslashes($_GET['enddate']);

        $tournament_date_start_reward = addslashes($_GET['startdatereward']);
        $tournament_date_end_reward = addslashes($_GET['enddatereward']);

        $tournament_server_list = addslashes($_GET['server_list']);
        $tournament_ip_list = addslashes($_GET['ip_list']);

        $tournament_status = ( is_numeric($_GET['catstatus']) && $_GET['catstatus'] >= 1) ? $_GET['catstatus'] : 0;
        $reward_percent = addslashes($_GET['reward_percent']);

        $params = array(
            'id' => $id,
            'tournament_date_start' => $tournament_date_start,
            'tournament_date_end' => $tournament_date_end,
            'tournament_date_start_reward' => $tournament_date_start_reward,
            'tournament_date_end_reward' => $tournament_date_end_reward,
            'tournament_server_list' => $tournament_server_list,
            'tournament_ip_list' => $tournament_ip_list,
            'tournament_status' => $tournament_status,
            'reward_percent' => $reward_percent);

        $data = $this->m_tooltichluy->edit_tournament_totaldate_details($params);

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

    public function tournament_totaldate_list() {
        $data = $this->m_tooltichluy->tournament_totaldate_list();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    public function tournament_totaldate_get_by_id() {
        $id = $_GET['id'];

        $data = $this->m_tooltichluy->tournament_totaldate_get_by_id($id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    public function tournament_totaldate_list_name_id() {
        $data = $this->m_tooltichluy->tournament_totaldate_list_name_id();
        $this->output->json_encode($data);
    }

    //Reward
    function add_reward() {
        $tournament_id = $_GET['tournament_id'];
        $reward_name = $_GET['reward_name'];

        $params = array(
            'tournament_id' => $tournament_id,
            'reward_name' => $reward_name,
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
            'reward_item5_number' => 0);

        $data = $this->m_tooltichluy->add_reward($params);

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

        $data = $this->m_tooltichluy->load_reward($tournament_id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function load_reward_details() {
        $id = $_GET['id'];

        $data = $this->m_tooltichluy->load_reward_details($id);
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
        $reward_img = addslashes($_GET['reward_img']);

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
            'reward_status' => $reward_status);

        $data = $this->m_tooltichluy->edit_reward_details($params);

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

        $data = $this->m_tooltichluy->edit_reward_name($params);

        $R["result"] = 1;
        $R["message"] = 'CHỈNH SỬA TÊN GIẢI THƯỞNG THÀNH CÔNG !';

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
            'reward_item5_number' => 0);

        $data = $this->m_tooltichluy->add_reward_top($params);

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

        $data = $this->m_tooltichluy->load_reward_top($tournament_id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function load_reward_details_top() {
        $id = $_GET['id'];

        $data = $this->m_tooltichluy->load_reward_details_top($id);
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
        $reward_img = addslashes($_GET['reward_img']);

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
            'reward_status' => $reward_status);

        $data = $this->m_tooltichluy->edit_reward_details_top($params);

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

        $data = $this->m_tooltichluy->edit_reward_name_top($params);

        $R["result"] = 1;
        $R["message"] = 'CHỈNH SỬA GIẢI THƯỞNG THÀNH CÔNG !';

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

    //Reward Premiership
    function add_reward_premiership() {
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
            'reward_item5_number' => 0);

        $data = $this->m_tooltichluy->add_reward_premiership($params);

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

    function load_reward_premiership() {
        $tournament_id = $_GET['tournament_id'];

        $data = $this->m_tooltichluy->load_reward_premiership($tournament_id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function load_reward_details_premiership() {
        $id = $_GET['id'];

        $data = $this->m_tooltichluy->load_reward_details_premiership($id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function edit_reward_details_premiership() {
        $id = addslashes($_GET['id']);

        $reward_point = addslashes($_GET['reward_point']);
        $reward_img = addslashes($_GET['reward_img']);

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
            'reward_status' => $reward_status);

        $data = $this->m_tooltichluy->edit_reward_details_premiership($params);

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

    function edit_reward_name_premiership() {
        $id = addslashes($_GET['id']);
        $reward_name = addslashes($_GET['reward_name']);
        $reward_rank_min = addslashes($_GET['reward_rank_min']);
        $reward_rank_max = addslashes($_GET['reward_rank_max']);

        $params = array(
            'id' => $id,
            'reward_name' => $reward_name,
            'reward_rank_min' => $reward_rank_min,
            'reward_rank_max' => $reward_rank_max);

        $data = $this->m_tooltichluy->edit_reward_name_premiership($params);

        $R["result"] = 1;
        $R["message"] = 'CHỈNH SỬA GIẢI THƯỞNG THÀNH CÔNG !';

        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }

    //Send NL Tool
    function send_nl() {
        if (isset($_SESSION["execute_time"]) && (time() - $_SESSION["execute_time"]) < 3) {
            $R["result"] = 1;
            $R["message"] = 'MỖI LẦN SEND NK PHẢI CÁCH NHAU 3 GIÂY !';
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
            return;
        } else {
            $_SESSION["execute_time"] = time();
            $params = $this->input->get();
            //var_dump($params); die;
            $needle = array("email", "nl_count", "mobo_service_id", "send_note");

            if (!is_required($params, $needle) == TRUE) {
                $R["result"] = -1;
                $R["message"] = 'INVALID_PARAMS !';
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;
            }

            $user_acc = $params['email'];
            $nl_count = $params['nl_count'];
            $mobo_service_id = $params['mobo_service_id'];
            $send_note = $params['send_note'];
            
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

            //Check Account NL
            $user_check_mobo_id = $this->m_tooltichluy->user_check_mobo_id($mobo_service_id);
            if (count($user_check_mobo_id) == 0) {
                $R["result"] = -1;
                $R["message"] = 'MSI KHÔNG CHÍNH XÁC !';
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;
            }

            //Check Already Send Token
            $check_exist_token = $this->m_tooltichluy->check_exist_token($valid);
            if (count($check_exist_token) > 0) {
                $R["result"] = -1;
                $R["message"] = 'PROCESS EXIST !';
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;
            }

            //Insert Log Send
            $params = array(
                'user_acc' => $user_acc,
                'mobo_service_id_send' => $mobo_service_id,
                'token_process' => $valid,
                'nl_count' => $nl_count,
                'send_note' => $send_note);

            $add_nlsend_log = $this->m_tooltichluy->add_nlsend_log($params);

            if ($add_nlsend_log == 0) {
                $R["result"] = -1;
                $R["message"] = 'SEND NGÂN LƯỢNG THẤT BẠI, VUI LÒNG THỬ LẠI* !';
                $this->m_tooltichluy->update_add_nl_status($add_nlsend_log, -1);
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;
            }
            
            //Update NL
            $add_nl = $this->m_tooltichluy->add_nl($mobo_service_id, $nl_count);
            if($add_nl > 0){
                $R["result"] = 0;
                $R["message"] = 'SEND NGÂN LƯỢNG THÀNH CÔNG !';
                
                //Update Status
                $this->m_tooltichluy->update_add_nl_status($add_nlsend_log, 1);
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;
            }
            else {
                $R["result"] = -1;
                $R["message"] = 'SEND NGÂN LƯỢNG THẤT BẠI, VUI LÒNG THỬ LẠI** !';
                $this->m_tooltichluy->update_add_nl_status($add_nlsend_log, -2);
                $this->output->set_header('Content-type: application/json');
                $this->output->set_output(json_encode($R));
                return;                
            }
        }
    }

    //History
    function get_sendnl_history() {      
        $startdate = addslashes($_GET['startdate']);
        $enddate = addslashes($_GET['enddate']);
      
        $data = $this->m_tooltichluy->get_sendnl_history($startdate, $enddate);
       
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }
    
    function get_exchange_history() {
        $type = addslashes($_GET['type']);

        $tournament_id = addslashes($_GET['tournament']);
        $startdate = addslashes($_GET['startdate']);
        $enddate = addslashes($_GET['enddate']);

        if ($type == "1") {
            $data = $this->m_tooltichluy->get_exchange_history($tournament_id, $startdate, $enddate);
        }
        if ($type == "2") {
            $data = $this->m_tooltichluy->get_exchange_history_top($tournament_id, $startdate, $enddate);
        }
        if ($type == "3") {
            $data = $this->m_tooltichluy->get_exchange_history_premiership($tournament_id, $startdate, $enddate);
        }
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_exchange_history_top() {
        $data = $this->m_tooltichluy->get_exchange_history_top();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

    function get_exchange_history_premiership() {
        $data = $this->m_tooltichluy->get_exchange_history_premiership();
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }

}

?>