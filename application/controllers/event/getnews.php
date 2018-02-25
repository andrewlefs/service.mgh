<?php
require_once APPPATH . 'third_party/MeAPI/Autoloader.php';
require_once APPPATH . 'core/EI_Controller.php';

class getnews extends EI_Controller {
    public function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Ho_Chi_Minh");       
       
    }
    
    public function index() {
        //create oauth token       
        echo "Hello Word";die;    
    }
    public function content_news() {
        $id = $_GET["id"];
        $api_url = "http://data.mobo.vn/home/get_post_id/$id/1/";
        $api_result =  $this->call_api_get($api_url);
        $this->output->set_output($api_result);
    }
	
	public function getcontent($ids) {   
		$user = $this->get_info();
        $api_url = "http://mong.mobo.vn/teaser/loadnewsteaser/".$ids;
        $api_result =  $this->call_api_get($api_url);
        $this->data["content"] = $api_result;
		$this->data["user"] = $user;
        echo $this->load->view("event/crosssale/thele", $this->data, true);
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
}
