<?php

class MeAPI_Controller_AuthorizeController implements MeAPI_Controller_AuthorizeInterface {

    protected $_response;
    private $CI;

    public function __construct() {
        $this->CI = & get_instance();
    }

    public function validateAuthorizeRequest(MeAPI_RequestInterface $request, $scope = NULL) {
        $app = $request->get_app();
        $params = $request->input_request();
        $token = trim($params['token']);
        $this->CI->load->model('../third_party/MeAPI/Models/SystemModel', 'SystemModel');
        $is_check_token = TRUE;
        unset($params['app'], $params['token']);
        if (empty($app) === FALSE) {
            $this->CI->load->library('cache');
            $cache = $this->CI->cache->load('memcache', 'system_info');
            $app_info = $cache->store('MeAPI_System_App_' . $request->get_controller() . $app, $this->CI->SystemModel, 'get_app', array($app));
            
            if (empty($app_info) === TRUE) {
                $this->_response = new MeAPI_Response_APIResponse($request, 'INVALID_APP');
                return FALSE;
            }
            if (empty($token) === TRUE) {
                if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) || strtolower($_SERVER['PHP_AUTH_USER']) != $request->get_app()) {
                    AuthorizeHeader: {
                        header('WWW-Authenticate: Basic realm="Vui long nhap username=' . $app . ', password tuong ung voi ' . $app . '"');
                        header('HTTP/1.0 401 Unauthorized');
                        echo 'Qua trinh chung thuc duoc huy boi nguoi dung';
                        exit;
                    }
                } else {
                    if ($app != $_SERVER['PHP_AUTH_USER']) {
                        goto AuthorizeHeader;
                        die('Ban phai nhap username = ' . $app);
                    }
                    $app_secret = $_SERVER['PHP_AUTH_PW'];
                    if ($app_secret != $app_info['app_secret']) {
                        goto AuthorizeHeader;
                        die('Qua trinh chung thuc that bai');
                    }
                    $is_check_token = FALSE;
                }
            }
            
            if ($is_check_token == TRUE) {
                //echo implode('', $params) . $app_info['app_secret'];
                $valid = md5(implode('', $params) . $app_info['app_secret']);
                if ($valid != $token && $is_check_token) {
                    $this->_response = new MeAPI_Response_APIResponse($request, 'INVALID_TOKEN');
                    return FALSE;
                }
            }
            
            if ($scope) {
                if (!preg_match('/' . strtolower($scope) . '/i', $app_info['scope'])) {
                    $this->_response = new MeAPI_Response_APIResponse($request, 'INVALID_SCOPE');
                    return FALSE;
                }
            }
            define('SERVICE_ID', $app_info['service_id']);
            define('SERVICE', strtolower($app_info['service']));
            define('APP_SECRET', $app_info['app_secret']);
            define('API_VERSION', $app_info['api_version']);
            return TRUE;
        }
        return FALSE;
    }

    public function getResponse() {
        return $this->_response;
    }
}
?>