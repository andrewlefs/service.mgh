<?php

/**
 * Created by Ivoglent Nguyen.
 * User: longnv
 * Date: 11/8/13
 * Time: 2:48 PM
 * Project : api
 * File : ApiRequest.php
 */
class ApiRequest {

    private $url;
    private $data = null;
    private $_post = false;
    private $config;

    public function __construct($_config = false) {
	
        $this->config = $_config;
		
    }

    public function post($url, $data = null) {
        $this->url = $url;
        if ($data != null)
            $this->data = array_merge($this->data, $data);
        $this->_post = true;
        return $this->_process();
    }

    public function get($url) {
        $this->url = $url;
        $this->_post = false;
        return $this->_process();
    }

    public function assign($name, $value) {
        $this->data[$name] = $value;
    }

    private function _process() {
        $ch = curl_init();
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'Accept: text/html; charset=UTF-8',);
        //curl_setopt($ch,CURLOPT_ENCODING , "gzip");
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_COOKIE, "");
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        if ($this->_post) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->data));
        }
        $result = curl_exec($ch);
        curl_close($ch);
        $this->log($this->url, $result);
        if (!empty($result)) {

            if ($this->isJson($result)) {
                return json_decode($result, true);
            }
            else
                return $result;
        } else {
            return null;
        }
    }

    private function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    private function log($url, $str) {
        if ($this->config['log']) {
            $typedir = "";
            $time = time();
            $sub = str_replace("/", "", date('Y-m-d', $time));
            $path = LOG_PATH . $typedir . DS . $sub;
            //die($path);
            if (!is_dir($path))
                mkdir($path, 0777, true);
            $file = "api_request_" . str_replace("-", "", str_replace("/", "", date('Y-m-d'))) . "_" . date('H', $time) . ".log";
            $f = fopen($path . DIRECTORY_SEPARATOR . $file, "a+");
            fputs($f, date('H:i:s', $time) . "\t\t" . $url . "\t\t" . $str . "\n");
            fclose($f);
        }
    }

}