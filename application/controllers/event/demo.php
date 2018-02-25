<?php

require_once APPPATH . 'core/EI_Controller.php';

class demo extends EI_Controller {

    public function __construct() {
        parent::__construct();
        //$rs = $this->write_log($data);
    }

    public function index() {
        $this->init_settings("event/demo");
        $this->load->library('GameFullAPI');
        $user = $this->get_info();
        $this->data["user"] = $user;
        //kiem tra request
        //$this->data["message"] = "Truy cập không hợp lệ";
        if ($this->verify_uri() != true) {
            $this->data["message"] = "Truy cập không hợp lệ";
            $this->render("deny", $this->data);
        }
        $gameapi = new GameFullAPI();
        $result = $gameapi->get_user_info($this->idgame, $user->mobo_service_id, $user->server_id);
        $this->data["list"] = $result;
        $this->render("index", $this->data);
    }

    public function minusitem() {
        $this->init_settings("event/demo");
        $this->load->library('GameFullAPI');
        $user = $this->get_info();
        $this->data["user"] = $user;
        //kiem tra request
        //$this->data["message"] = "Truy cập không hợp lệ";
        if ($this->verify_uri() != true) {
            $this->data["message"] = "Truy cập không hợp lệ";
            $this->render("deny", $this->data);
        }

        $gameapi = new GameFullAPI();
        $params = $this->input->post();
        if (isset($params["button"])) {
            $item_id = $params["item_id"];
            $item_type = $params["item_type"];
            $item_count = (int) $params["count"];
            if ($item_id != "" && $item_type != "" && $item_count != 0) {
                if ($item_count > 100) {
                    $this->data["message"] = "Bạn không được send số lượng > 100";
                } else {
                    $time = time();
                    $item = array(array("item_id" => $item_id, "item_name" => "Name Item", "count" => $item_count, "type" => $item_type));
                    $result = $gameapi->minus_item($this->idgame, $user->mobo_service_id, $user->server_id, $item, "Test Item " . $time, "Test Content Item " . $time, 5);
                    var_dump($result);
                    $this->data["message"] = $result;
                }
            } else {
                $this->data["message"] = "Thông tin chưa đầy đử";
            }
        }
        $this->render("minusitem", $this->data);
    }

    public function senditem() {
        $this->init_settings("event/demo");
        $this->load->library('GameFullAPI');
        $user = $this->get_info();
        $this->data["user"] = $user;
        //kiem tra request
        //$this->data["message"] = "Truy cập không hợp lệ";
        if ($this->verify_uri() != true) {
            $this->data["message"] = "Truy cập không hợp lệ";
            $this->render("deny", $this->data);
        }
        if($user->mobo_id != "128147013" && $user->mobo_id != "860715390" && $user->mobo_id != "162502071"){
            $this->data["message"] = "Tài khoản không có quyền sử dụng chức năng này";
            $this->render("deny", $this->data);
        }

        $gameapi = new GameFullAPI();
        $params = $this->input->post();
        //[{"type": "item", "item_id": "IM001", "count": 10}, {"type": "item", "item_id": "IM002", "count": 20}, {"type": "equip", "item_id": "EQ00001", "count": 1}]
        //EQ00001	Lửa Sét, type: equip
        //IM001	Vàng , type: item
        //IM002	Ngọc , type: item

        if (isset($params["button"])) {
            $item_id = $params["item_id"];
            $item_type = $params["item_type"];
            $item_count = (int) $params["count"];
            if ($item_id != "" && $item_type != "" && $item_count != 0) {
                if ($item_count > 100) {
                    $this->data["message"] = "Bạn không được send số lượng > 100";
                } else {
                    $time = time();
                    $item = array(array("item_id" => $item_id, "count" => $item_count, "type" => $item_type));
                    $result = $gameapi->add_item($this->idgame, $user->mobo_service_id, $user->server_id, $item, "Test Item " . $time, "Test Content Item " . $time, 5);
                    //var_dump($result);
                    $this->data["message"] = $result;
                }
            } else {
                $this->data["message"] = "Thông tin chưa đầy đử";
            }
        }
        $this->render("senditem", $this->data);
    }

}

?>
