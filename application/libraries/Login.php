<?php

class Login {
    /*
     * oauth config
     */

    private $oauth_url_api_user = 'http://oauth.mobo.vn/';
    private $oauth_app_user = 'id.mobo.vn';
    private $oauth_app_user_key = 'GPGo1K0e';
    private $oauth_last_link_request;
    private $oauth_state;
    private $client_id = '223510387745406';
    /*
     * API config
     */
    private $url_api_user = 'http://api.mobo.vn/';
    private $app_user = 'id.mobo.vn';
    private $app_user_key = 'agiU7J0A';
    private $last_link_request;
    /*
     * Facebook get access token Key 
     */
    private $facebook_access_token_key = 'a7aa593d176e2287c606daac47b3de51';

    /*
     * API Facebook
     */

    public function authorize_facebook($fb_id = NULL) {	
        //chan login facebook
        //return false;
        if (empty($fb_id) === TRUE)
            return false;

        $data = $this->_call_api('user', 'authorize_facebook', array('facebook_id' => $fb_id));
        if (empty($data) === FALSE) {
            $result = json_decode($data, true);
            return $result;
        } else {
            return false;
        }
    }

    public function register_facebook($fb_id = NULL) {
        if (empty($fb_id) === TRUE)
            return false;

        $data = $this->_call_api('user', 'register_facebook', array('facebook_id' => $fb_id));
        if (empty($data) === FALSE) {
            $result = json_decode($data, true);
            return $result;
        } else {
            return false;
        }
    }

    /*
     * Get Facebook accessToken
     */

    public function get_facebook_access_token($data) {
        if (empty($data)) {
            return false;
        }
        $param = http_build_query($data);
        $token = md5(implode('', $data) . $this->facebook_access_token_key);
        $url = 'http://push.3t.mobo.vn/pushApi/?control=check&func=check_access_token&app=api&' . $param . '&token='.$token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $data = curl_exec($ch);

        if (empty($data) === FALSE) {
            $result = json_decode($data, true);
            if ($result['code'] == 100) {
                return $result['data'];
            }
            return false;
        } else {
            return false;
        }
    }

    /*
     * API Func
     */

    public function authorize($user = NULL, $password = NULL) {
        include_once APPPATH . "/libraries/Crypt.php";

        $crypt = new Crypt();

        $data = $this->_call_api('user', 'authorize', array('phone' => $user, 'password' => md5($password), 'password_src' => $crypt->encrypt($password, $this->app_user_key)));

        if (empty($data) === FALSE) {
            $result = json_decode($data, true);
            return $result;
        } else {
            return false;
        }
    }

    private function _call_api($control, $function, $params) {
        $this->last_link_request = $this->url_api_user . '?control=' . $control . '&func=' . $function . '&' . http_build_query($params) . '&app=' . $this->app_user . '&token=' . md5(implode('', $params) . $this->app_user_key);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->last_link_request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $result = curl_exec($ch);

        return $result;
    }

    /*
     * Oauth Func
     */

    public function register_authorization_code($phone = NULL, $signature = NULL, $check_not_exist = NULL) {
        if (empty($phone) === TRUE)
            return false;
        $state = NULL;
        if (empty($signature) === FALSE) {
            $state = 'signature=' . $signature;
        }
        $data = $this->_call_oauth('oauth', 'register_authorization_code', array('client_id' => $this->client_id, 'phone' => $phone, 'check_not_exist' => $check_not_exist, 'state' => $state));
        if (empty($data) === FALSE) {
            $result = json_decode($data, TRUE);
            if (is_array($result) === TRUE) {
                if ($result['code'] == 2) {
                    $output['status'] = 1;
                    $output['url'] = $this->oauth_last_link_request;
                    $output['content'] = $result;
                    return $output;
                } else {
                    $output['status'] = 0;
                    $output['url'] = $this->oauth_last_link_request;
                    $output['content'] = $result;
                    return $output;
                }
            }
            return false;
        }
        return false;
    }

    private function _call_oauth($control, $function, $params) {
        $src_params = $params;
        $params['state'] = urlencode($params['state']);
        $this->oauth_last_link_request = $this->oauth_url_api_user . '?control=' . $control . '&func=' . $function . '&' . http_build_query($params) . '&app=' . $this->oauth_app_user . '&token=' . md5(implode('', $src_params) . $this->oauth_app_user_key);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->oauth_last_link_request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $result = curl_exec($ch);
        return $result;
    }

}