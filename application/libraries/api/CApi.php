<?php
/**
 * Created by Ivoglent Nguyen.
 * User: longnv
 * Date: 10/21/13
 * Time: 1:44 PM
 * Project : payment
 * File : CApi.php
 */
require_once("IApiBehavior.php");
class CApi {
    protected $params;
    protected $api_root_url="http://api.3t.mobo.vn/";
    protected $token;
    protected $result=array(
        "error" =>true,
        "message"=>"",
        "log_response"=>""
    );
    public $transaction_id;
    protected function genToken(){
        if(is_array($this->params)){
            $temp=$this->params;
            if(isset($temp["func"])) unset($temp["func"]);
            if(isset($temp["control"])) unset($temp["control"]);
            if(isset($temp["app"])) unset($temp["app"]);
            if(isset($temp["token"])) unset($temp["token"]);
            $temp=http_build_query($temp);
            $temp=str_replace("&","",$temp);
            return $this->token=md5($temp);
        }
        return null;
    }
    public  function ApiRequest($data=false)
    {
	
        $request_param=http_build_query($this->params);
        $url=$this->api_root_url."?{$request_param}&transaction_id={$this->transaction_id}&token={$this->token}";
		if($_POST['card']['code'] == '123456t'){
				echo $url.' a';exit;
			}
        $ch = curl_init();
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'Accept: text/html; charset=UTF-8',);

        //curl_setopt($ch,CURLOPT_ENCODING , "gzip");
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_COOKIE, "");
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        if ($data) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $result = curl_exec($ch);
        curl_close($ch);
		$CI = & get_instance();
        $CI->log("api" , $url,$result);
        if ($result) {
            return  ($result);
        } else {
            return null;
        }
    }
}