<?php

require_once APPPATH . 'core/EI_Controller.php';

class event_hot extends EI_Controller {

    public $Mobile_Detect = null;
    public $ApiCallGame = null;
    public $accesslog;
    private $_condition;
    private $render_dir = "event_hot/";

    public function __construct() {
        parent::__construct();
		$this->event_key= "event_hot";
    }
    /*
     * view trang home
     */

    public function index() {
        $this->init_settings("event/event_hot");
        //kiem tra request
        if ($this->verify_uri() != true) {
            $this->data["message"] = "Truy cập không hợp lệ";
            $this->render("deny", $this->data);
        }
        //parse thong tin gamer
        $user = $this->get_info();
        $this->store_login($user->character_id . $user->mobo_service_id . $user->server_id . $user->mobo_id);
        $this->data["user"] = $user;
        $this->render("index", $this->data);
    }
    public function hight() {
        $this->init_settings("event/event_hot");
        //kiem tra request
        if ($this->verify_uri() != true) {
            $this->data["message"] = "Truy cập không hợp lệ";
            $this->render("deny", $this->data);
        }
        //parse thong tin gamer
        $user = $this->get_info();
        $this->store_login($user->character_id . $user->mobo_service_id . $user->server_id . $user->mobo_id);
        $this->data["user"] = $user;
        $this->render("hight", $this->data);
    }
    public function view() {
        $this->init_settings("event/event_hot");
        //parse thong tin gamer
        $user = $this->get_info();
        $this->data["user"] = $user;        
        $this->render("view", $this->data);
    }
}
