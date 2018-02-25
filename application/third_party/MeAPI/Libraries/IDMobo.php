<?php

class IDMobo {

    private $url_api_user = 'http://api.mobo.vn/';
    private $app_user = 'id.mobo.vn';
    private $app_user_key = 'agiU7J0A';
    private $last_link_request;

    public function send_active_code($phone = NULL, $act = 'DK') {

        if (empty($phone) === TRUE)
            return false;
        $data = $this->_call_api('user', 'send_active_code', array('phone' => $phone, 'act' => $act));

        if (empty($data) === FALSE) {
            $result = json_decode($data, true);
            return $result;
        } else {
            return false;
        }
    }

    public function register($phone = NULL, $active_code = NULL, $password = NULL) {
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
        $this->last_link_request = $this->url_api_user . '?control=' . $control . '&func=' . $function . '&' . http_build_query($params) . '&app=' . $this->app_user . '&token=' . md5(implode('', $params) . $this->app_user_key);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->last_link_request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $result = curl_exec($ch);

        return $result;
    }
}