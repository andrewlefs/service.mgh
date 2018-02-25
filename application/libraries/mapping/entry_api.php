<?php
class entry_api {

    private $key = "Mkajfiqnvmahg";
    private $url = "http://api.3t.mobo.vn/";

    public function get_info($data) {
        if (empty($data)) {
            return false;
        }
        $token = md5(implode('', $data) . $this->key);
        $url = $this->url . "?control=api&func=get_info&character_name={$data['character_name']}&server_id={$data['server']}&app=3t&token=" . $token;
        if(isset($_GET['test'])){
			//echo '<br />'.$url.'<br />';
		}
		
		$result = $this->ApiRequest($url);
        if ($result == null)
            return null;
        $result = json_decode($result, true);
        if ($result == null)
            return null;
        if ($result['code'] != 400290)
            return null;
        return $result['data'];
    }
	public function get_infotest($data) {
        if (empty($data)) {
            return false;
        }
        $token = md5(implode('', $data) . $this->key);
        $url = $this->url . "?control=api&func=get_info&character_name={$data['character_name']}&server_id={$data['server']}&app=3t&token=" . $token;
        if(isset($_GET['test'])){
			//echo '<br />'.$url.'<br />';
		}
		
		$result = $this->ApiRequest($url);
		var_dump($result);
		die;
        if ($result == null)
            return null;
        $result = json_decode($result, true);
        if ($result == null)
            return null;
        if ($result['code'] != 400290)
            return null;
        return $result['data'];
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
        $CI->log_request("API_REQUEST : " . $url . '(excute_time:' . $t . 's)');
        if ($result) {
            return ($result);
        } else {
            return null;
        }
    }

}