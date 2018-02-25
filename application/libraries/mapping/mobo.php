<?php

class mobo {

    public $app = "3t";
    public $key = "Mkajfiqnvmahg";
    public $url = "http://api.3t.mobo.vn/";

    public function mobo_logout($mobo_token) {
        
        $token  = md5($mobo_token.$this->key);
        $url = "{$this->url}?control=api&func=logout&mobo_access_token={$mobo_token}&app={$this->app}&token={$token}";
        $result = $this->ApiRequest($url);
        if ($result == null)
            return "FAILED(" . $url . ")";
        $result = json_decode($result);
        if ($result->error != 0) {
            return "FAILED(" . $url . ")";
        }
        return "SUCCESS";
    }

    public function ApiRequest($url, $data = false) {
        $t1 = time();
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
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        $result = curl_exec($ch);
        curl_close($ch);
        $t = time() - $t1;
        $CI = & get_instance();
        //$CI->log_request("API_REQUEST : " . $url . '(excute_time:' . $t . 's)');
        if ($result) {
            return ($result);
        } else {
            return null;
        }
    }

}