<?php

require_once APPPATH . 'third_party/MeAPI/Autoloader.php';
require_once APPPATH . 'third_party/MeAPI/Mq.php';

class MeAPI_Controller_PaymentController implements MeAPI_Controller_PaymentInterface {

    protected $_response;
    
    /**
     *
     * @var CI_Controller
     */
    private $CI;
    private $service_id = "103"; // do phòng API (TTKT) cung cấp
    private $whiteListIP;
    private $clientIP;
    
    public function __construct() {
        $this->CI = & get_instance();       
        MeAPI_Autoloader::register();
              
        $this->CI->cache_limit = 60 * 60 * 24 * 1; //thời gian cache
        $this->CI->enable_cache = true; //        
        
        // List IPs
        //123.30.140.185 pay.gomobi.vn
        //123.30.140.181 payment.gomobi.vn
        $this->whiteListIP = $this->whiteListIP = array('123.30.140.185', '123.30.140.181', '127.0.0.1');
        $this->clientIP = $this->getClientIP();
    }

    public function getResponse() {
        return $this->_response;
    }
    
    /*
     * Add money to gamer
     * sample request: http://game.mobo.vn/bog//?control=payment&func=add_money&mobo_service_id=123&user_name=abcd&server_id=1&transaction_id=123456&time_stamp=2014-12-04%2017:31:32&type=card&amount=100000&channel=1|me|1.0.0&platform=ios&carrier=vinaphone&client_ip=127.0.0.1&app=eden&token=a655fffd0387f26507828eef701dd1bd
     */
    public function add_gold(MeAPI_RequestInterface $request){
        $authorize = new MeAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == true) {
            $params = $request->input_request();
            $games = json_decode($params["games"], true);
            // init start_time
            $start_time = microtime(true);
            
            // check IPs
            if (!in_array($this->clientIP, $this->whiteListIP)){
                $this->_response = new MeAPI_Response_APIResponse($request, 'IP_REJECT');
                return;
            }
            
            // Kiểm tra tồn tại các input-params
            if (!is_required($params, array('mobo_service_id', 'user_name', 'server_id', 'transaction_id', 'time_stamp', 'type', 'amount', 'money', 'channel', 'platform'))) {
                $this->_response = new MeAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }
            
            if (!is_required($params, array('org_money'))) {
                $this->_response = new MeAPI_Response_APIResponse($request, 'INVALID_PARAMS org_money');
                return;
            }
            
            $this->CI->load->library('EdenAPI', NULL, 'EdenAPI');
            
            // call model set transaction
            $this->CI->load->model('../third_party/MeAPI/Models/PaymentModel', 'PaymentModel');
            
            // check duplicate transaction
            $isDuplicate = $this->CI->PaymentModel->checkDuplicateTransaction(trim($params['transaction_id']));
            if ($isDuplicate){
                $this->_response = new MeAPI_Response_APIResponse($request, 'DUPLICATE_TRANSACTION');
                return;
            }
            
            $idInserted = $this->CI->PaymentModel->setTransaction($params['mobo_service_id'], $params['user_name'], $params['transaction_id'],
                                    $params['server_id'], $params['time_stamp'], $params['type'], $params['amount'], 
                                    $params['org_money'], $params['money'], $params['channel'], $params['platform'], $params['carrier'],
                                    $params['client_ip'], $games['order_id'], $params['games']);
            
            if ($idInserted == null){
                $this->_response = new MeAPI_Response_APIResponse($request, 'INSERT_TRANSACTION_FAIL');
                return;
            }
            
            // call API add money
            $result = $this->CI->EdenAPI->add_money($params['mobo_service_id'], $params['transaction_id'], $params['type'],
                    $params['amount'], $params['server_id'], $params['time_stamp'], $params['org_money'], $params['money'], $games['order_id']);
            
            // calc latency
            $latency = (microtime(true) - $start_time);
            
            if ($result == true) {
                 // push message queue
                $this->push_rabbit_mq($params, 1);
                
                $this->CI->PaymentModel->finishTransaction($idInserted, 1, $latency); // update status = 1: success
                $this->_response = new MeAPI_Response_APIResponse($request, 'ADD_MONEY_SUCCESS');
            } else {		
                // push message queue
                $this->push_rabbit_mq($params, 0, 'ADD_MONEY_FAIL');
                
                $this->CI->PaymentModel->finishTransaction($idInserted, 2, $latency, $this->CI->EdenAPI->getErrorMessage()); // update status = 2: fail
                $this->_response = new MeAPI_Response_APIResponse($request, 'ADD_MONEY_FAIL', $this->CI->EdenAPI->getErrorMessage());
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }
    
    protected function getClientIP() {
        $ip = $_SERVER['REMOTE_ADDR']?:($_SERVER['HTTP_X_FORWARDED_FOR']?:$_SERVER['HTTP_CLIENT_IP']);
        return $ip;
    }
    
    private function push_rabbit_mq($params, $status, $message = ''){

        // decode json games
        $games = json_decode($params['games'], true);
        // data channel
        $channel_info = explode('|', $params['channel']);
        $ptype = '';
        if($params['platform'] == 'ios' && $params['type'] == 'inapp'){                    
            $ptype = 'inapp_apple';                    
        }elseif($params['type'] == 'inapp'){
            $ptype = 'inapp_google';
        }else{
            $ptype = $params['type'];
        }
        $insert = array(
            'currency' => 'vnd',                    
            'datetime' => $params['time_stamp'],
            'date' => substr($params['time_stamp'], 0, 10),
            'device_id' => '', 
            'ip' => $_SERVER['REMOTE_ADDR'],
            'mobo_id' => $params['user_name'], 
            'mobo_service_id' => $params['mobo_service_id'],     
            'sid' => intval($params['server_id']),
            'payment_type' => $ptype,
            'platform' => $params['platform'],
            'money' => (int)$params['amount'],                    
            'provider' => $games['provider'],
            'refcode' => $games['refcode'],         
            'service_id' => $this->service_id,    
            'telco' => $games['telco'],                    
            'user_agent' => $games['user_agent'],
            'version' => $channel_info[2],
            'status' => $status,
            'msg' => $message
        );      
        //Start push rabbit mq	
        $data_insert = array(
                'collection' => 'payment', //tên collection
                'store' => $insert
        );	
        //format message truyền xuống queue	
        $mq_message = json_encode($data_insert);
        $this->CI->config->load('mq_setting');
        $mq_config = $this->CI->config->item('mq');				
        $config['routing'] = $mq_config['payment_mq_routing'];
        $config['exchange'] = $mq_config['payment_mq_exchange'];				
        MEAPI_Mq::push_rabbitmq($config, $mq_message);				
        //End push mq            
    }
}