<?php
//require_once(APPPATH.'libraries/CurlFB.php');
class FacebookAPI {

    private $CI;
    private $facebook_graph = 'https://graph.facebook.com';
    private $facebook_url = 'https://www.facebook.com';
    private $libcurl;
    public function __construct() {
        $this->CI = &get_instance();
        $this->CI->load->library('CurlFB');
        $this->libcurl = new CurlFB();
    }

    public function verify_access_token($token) {
        $field = 'id,name,token_for_business';
        $link = "https://graph.facebook.com/v2.2/me?access_token={$token}&fields={$field}";
        $result = $this->libcurl->get($link);
        if (empty($result) == TRUE) {
            return FALSE;
        } else {
            return json_decode($result, TRUE);
        }
    }

    public function get_original_facebook_id($facebook_id) {
       
        $link = "{$this->facebook_url}/{$facebook_id}";
        $this->libcurl->setheader(TRUE);
        $this->libcurl->setpathcookie(CONFIG_PATH . 'cookies.txt');
        $result = $this->libcurl->get($link);
        $pattern = '#facebook.com\/profile.php\?id\=(.*)#imx';
        preg_match($pattern, $result, $matches);

        if (empty($matches[1]) == FALSE) {
            $facebook_id = trim($matches[1]);
        } else {
            $pattern = '#facebook.com\/(.*)#imx';
            preg_match($pattern, $result, $matches);
            $username = trim($matches[1]);

            $link = "{$this->facebook_graph}/{$username}";
            $this->libcurl->setheader(FALSE);
            $this->libcurl->setpathcookie(FALSE);
            $result = json_decode($this->libcurl->get($link), TRUE);
            $facebook_id = $result['id'];
        }
        
        return $facebook_id;
    }

}
