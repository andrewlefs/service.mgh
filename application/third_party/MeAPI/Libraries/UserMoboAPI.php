<?php

class UserMoboAPI {

    private $CI;
    private $url_api = 'http://api.mobo.vn/';
    private $app_user = '8t';
    private $app_secret = 'z9MsqPro';
    private $last_link_request;

    public function __construct() {
        $this->CI = & get_instance();
    }

    public function authorize($account, $password) {
        $this->CI->load->library('crypt');
        $data = $this->_call_api('user', 'authorize', array('phone' => $account, 'password' => md5($password), 'password_src' => $this->CI->crypt->encrypt($password, $this->app_secret)));
        if (empty($data) === FALSE) {
            $result = json_decode($data, TRUE);
            if (is_array($result) === TRUE) {
                if ($result['code'] == 500020) {
                    if ($result['data']['is_mobo'] == TRUE) {
                        $result['data']['account'] = $result['data']['phone'];
                    } else {
                        $result['data']['account'] = $result['data']['username'];
                    }
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

    /*
     * API Facebook
     */

    public function authorize_facebook($fb_id = NULL) {
        if (empty($fb_id) === TRUE)
            return FALSE;

        $data = $this->_call_api('user', 'authorize_facebook', array('facebook_id' => $fb_id));

        if (empty($data) === FALSE) {
            $result = json_decode($data, TRUE);
            if (is_array($result) === TRUE) {
                if ($result['code'] == 500020) {
                    if ($result['data']['is_mobo'] == TRUE) {
                        $result['data']['account'] = $result['data']['phone'];
                    } else {
                        $result['data']['account'] = $result['data']['username'];
                    }
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

    public function register_facebook($fb_id = NULL) {
        if (empty($fb_id) === TRUE)
            return FALSE;

        $data = $this->_call_api('user', 'register_facebook', array('facebook_id' => $fb_id));
        if (empty($data) === FALSE) {
            $result = json_decode($data, true);
            if (is_array($result) === TRUE) {
                if ($result['code'] == 500010) {
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
            }
            return $arr;
        } else {
            return FALSE;
        }
    }

    public function send_active_code() {
        $data = $this->_call_api('user', 'register_facebook', array('facebook_id' => $fb_id));
        if (empty($data) === FALSE) {
            $result = json_decode($data, true);
            if (is_array($result) === TRUE) {
                if ($result['code'] == 500010) {
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
            }
            return $arr;
        } else {
            return FALSE;
        }
    }

    public function register($phone, $active_code, $password) {
        if (empty($phone) === TRUE)
            return false;

        $data = $this->_call_api('user', 'register', array('phone' => $phone, 'active_code' => $active_code, 'password' => md5($password)));
        if (empty($data) === FALSE) {
            $result = json_decode($data, true);
            return $result;
        } else {
            return false;
        }
    }

    private function _call_api($control, $function, $params) {
        $src = $params;
        if ($params['password_src'])
            $params['password_src'] = urlencode($params['password_src']);
        $this->last_link_request = $this->url_api . '?control=' . $control . '&func=' . $function . '&' . http_build_query($params) . '&app=' . $this->app_user . '&token=' . md5(implode('', $src) . $this->app_secret);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->last_link_request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $result = curl_exec($ch);
        return $result;
    }

}