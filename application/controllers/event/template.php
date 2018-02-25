<?php

require_once APPPATH . 'core/EI_Controller.php';

class template extends EI_Controller {

    public function __construct() {
        parent::__construct();
        //$rs = $this->write_log($data);        
        $this->root_folder = "event/template";
        $this->data["event_name"] = "Triệu Hồi Sói Lửa";
    }

    private function init() {
        $this->init_settings("event/template");
        $user = $this->get_info();
        $this->data["user"] = $user;
        //var_dump($user);die;        
        //kiem tra request
        //$this->data["message"] = "Truy cập không hợp lệ";
        if ($this->verify_uri() != true) {
            $this->data["message"] = "Truy cập không hợp lệ";
            $this->render("deny", $this->data);
        }
        $this->data["content_id"] = 1271;
    }

    public function index() {
        $this->init();
        $this->render("index", $this->data);
    }

    public function thamgia() {
        $this->init();
        $this->render("thamgia", $this->data);
    }

    public function result() {
        $this->init();
        $this->render("result", $this->data);
    }

//
//    public function index() {
//        $this->load->library('GameFullAPI');
//        $user = $this->get_info();
//        $this->data["user"] = $user;
//        //kiem tra request
//        //$this->data["message"] = "Truy cập không hợp lệ";
//        if ($this->verify_uri() != true) {
//            $this->data["message"] = "Truy cập không hợp lệ";
//            $this->render("deny", $this->data);
//        }
//
//        $gameapi = new GameFullAPI();
//        echo $user->mobo_service_id;
//        echo "<br/>";
//        $userinfo = $gameapi->get_user_info("125", $user->mobo_service_id, $user->server_id);
//        echo "<br/>";
//
//        $params = $this->input->post();
//
//        if (isset($params["submit"])) {
//            $item1 = intval($params["item1"]);
//            $item2 = intval($params["item2"]);
//            $item3 = intval($params["item3"]);
//
//            $type1 = $params["type1"];
//            $type2 = $params["type2"];
//            $type3 = $params["type3"];
//
//            if (true || $user->mobo_id == 128147013 || $user->mobo_id == 492439763 || $user->mobo_id == 260896396) {
//                if ($item1 >= 0) {
//                    $item[] = array("item_id" => $item1, "count" => 1, "type" => intval($type1));
//                }
//                if ($item2 >= 0) {
//                    $item[] = array("item_id" => $item2, "count" => 1, "type" => intval($type2));
//                }
//                if ($item3 >= 0) {
//                    $item[] = array("item_id" => $item3, "count" => 1, "type" => intval($type3));
//                }
//                var_dump($item);
//                $senrs = $gameapi->add_item("125", $user->mobo_service_id, $user->server_id, $item, "Nhận thưởng sự kiện", "Phần thưởng template", $user->character_id);
//                var_dump($senrs);
//                echo "<br> " . time();
//                echo "<br>Đã send: <br>";
//                var_dump($item);
//                echo "<br>";
//            } else {
//                echo "Bạn không được phép test nhận thưởng";
//            }
//        }
//
//        echo <<<EOF
//<!DOCTYPE html>
//<!--
//To change this license header, choose License Headers in Project Properties.
//To change this template file, choose Tools | Templates
//and open the template in the editor.
//-->
//<html>
//    <head>
//        <meta charset="UTF-8">
//        <title>Check Query String</title>
//        <style>
//            #customers
//            {
//                font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
//                width:100%;
//                border-collapse:collapse;
//            }
//            #customers td, #customers th 
//            {
//                font-size:1.2em;
//                border:1px solid #98bf21;
//                padding:3px 7px 2px 7px;
//            }
//            #customers th 
//            {
//                font-size:1.4em;
//                text-align:left;
//                padding-top:5px;
//                padding-bottom:4px;
//                background-color:#A7C942;
//                color:#fff;
//            }
//            #customers tr.alt td 
//            {
//                color:#000;
//                background-color:#EAF2D3;
//            }
//        </style>
//    </head>
//    <body>
//        <form action='' method='post'>
//            Item: <input type='text' name='item1' value='{$params['item1']}'/> Type: <input type='text' name='type1' value='{$params['type1']}'/><br><br>
//            Item: <input type='text' name='item2' value='{$params['item2']}'/> Type: <input type='text' name='type2' value='{$params['type2']}'/><br><br>
//            Item: <input type='text' name='item3' value='{$params['item3']}'/> Type: <input type='text' name='type3' value='{$params['type3']}'/><br><br>
//            <input type='submit' name='submit' value='Send'>
//         </form>        
//EOF;
//
//        if (is_array($userinfo)) {
//            echo '<table id="customers">';
//            echo '<tr>';
//            echo '<th>Key</th>';
//            echo '<th>Value</th>';
//            echo '</tr>';
//            foreach ($userinfo as $key => $value) {
//                echo "<tr>"
//                . "<td>" . $key . "</td>"
//                . "<td>" . $value . "</td>"
//                . "</tr>";
//            }
//            echo '</table>';
//        }
//        echo <<<EOF
//         </body>
//</html>
//EOF;
//        echo "<br>";
//
//        die;
//    }
}

?>
