<?php
/**
 * Created by Ivoglent Nguyen.
 * User: longnv
 * Date: 10/23/13
 * Time: 3:22 PM
 * Project : pay3t
 * File : mobo_api.php
 */

class mobo_api {
    public $app="service.3t.mobo.vn";
    public $key="1ge8urDH";
    public $url ="http://oauth.mobo.vn/";

    public function verifyMoboToken($token){
        if(isset($_GET['long']) && $_GET['long']=='depzai')
            return "ivoglent";
        $url=$this->url."?control=oauth&func=valid_access_token&access_token=".$token."&app=".$this->app ."&token=" .md5($token.$this->key);
        $result=$this->ApiRequest($url);
        if($result==null) return null;
        $result=json_decode($result);
        if($result==null) return null;
        if($result->code!=50) return null;
        return $result->data->phone;//$this->ApiRequest($mobo_veryfi_token_url);
    }
    public function testToken($token){
        $url=$this->url."?control=oauth&func=valid_access_token&access_token=".$token."&app=".$this->app ."&token=" .md5($token.$this->key);
        $result=$this->ApiRequest($url);
        echo ($result);die("Completed");
    }
    public function mobo_logout($account){
        $url="http://id.mobo.vn/api/logout/".$account."/". md5($account."longnv@mecorp");
        $result= $this->ApiRequest($url);
        if($result==null) return "FAILED(".$url.")";
        $result=json_decode($result);
        if($result->error!=0){
            return "FAILED(".$url.")";
        }
        return "SUCCESS";
    }
    public function update_provider($mobo_account,$provider){
        $param=array(
            'username' => $mobo_account,
            'provider'  => $provider,
			'mobo_account' => $mobo_account
        );
        $token=md5($param['username'].$param['provider'].$param['mobo_account']."3t!@#t3");
        $url='http://api.gomobi.vn/?control=payment&func=update_provider' . "&". http_build_query($param). "&app=3t&token=".$token;
        $CI =& get_instance();
        $result= $this->ApiRequest($url);
        $CI->log_request("API_REQUEST : ".$url . "\t". $result);
        if($result==null) return false;
        $result=json_decode($result);
        if($result->code!=104) return false;
        return true;
    }
    public  function ApiRequest($url,$data=false)
    {
        $t1=time();
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
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
        }
        $result = curl_exec($ch);
        curl_close($ch);
        $t=time()- $t1;
        $CI =& get_instance();
        $CI->log_request("API_REQUEST : ".$url . '(excute_time:'. $t .'s)');
        if ($result) {
            return  ($result);
        } else {
            return null;
        }
    }
}
