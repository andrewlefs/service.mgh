<?php

class OAuthMoboAPI {

    private $CI;
    private $url_api = 'http://oauth.mobo.vn/';
    private $app_user = '8t';
    private $app_secret = 'z9MsqPro';
    private $last_link_request;

    public function __construct() {

        $this->CI = & get_instance();
    }

    public function register_access_token($client_id, $username) {

        $data = $this->_call_api('oauth', 'register_access_token', array('client_id' => $client_id, 'phone' => $username));

        if (empty($data) === FALSE) {

            $result = json_decode($data, TRUE);

            if (is_array($result) === TRUE) {

                if ($result['code'] == 2) {

                    $arr = array(
                        'status' => TRUE,
                        'data' => $result['data'],
                        'url' => $this->last_link_request
                    );
                } else {

                    $arr = array(
                        'status' => 0,
                        'url' => $this->last_link_request
                    );
                }

                return $arr;
            }

            return FALSE;
        } else {

            return FALSE;
        }
    }

    private function _call_api($control, $function, $params) {

        $src = $params;

        $this->last_link_request = $this->url_api . '?control=' . $control . '&func=' . $function . '&' . http_build_query($params) . '&app=' . $this->app_user . '&token=' . md5(implode('', $src) . $this->app_secret);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->last_link_request);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($ch);

        return $result;
    }

}

?>