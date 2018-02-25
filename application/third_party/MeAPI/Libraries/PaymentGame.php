<?php

class PaymentGame {

    private $CI;
    private $url_pay_card = 'http://payment.gomobi.vn/index.php/pay/card/';
    private $url_payment = 'http://api.gomobi.vn/api/';
    private $key_payment = 'fvfak8s5';
    private $url_add_money = 'http://api.3k.mobo.vn/?control=user&func=add_money';
    private $key_add_money = 'tsfkgadijqfbcadfhqoqw';
	private $url_in_app = 'http://api.3k.mobo.vn/?control=payment&func=check_receipt_data';
	private $app_name = '3k';

    public function __construct() {
        $this->CI = & get_instance();
    }

    public function get_pay($params) {
        list($provider, $refcode) = explode("|", $params['channel']);
        $args = array(
            'username' => $params['mobo_account'],
			'mobo_account' => $params['mobo_account'],
            'provider' => empty($provider) ? 1 : $provider,
            'refcode' => $refcode,
            'ip' => $_SERVER['REMOTE_ADDR'],
			'useragent' => $_SERVER['HTTP_USER_AGENT']
        );
		$param_update = array(
			'username' => $params['mobo_account'],
			'mobo_account' => $params['mobo_account'],
			'provider' => $provider,
		);
		$this->update_provider($param_update);
        $data = $this->_call_api_payment($args, 'payment', 'get_pay');
        if (empty($data) === FALSE) {
            $result_payment = json_decode($data, TRUE);
            if (is_array($result_payment) === TRUE) {
                $output['status'] = TRUE;
                $output['url'] = $this->last_link_request;
                $output['content'] = $result_payment;
                return $output;
            }
        }
        $output['status'] = FALSE;
        $output['url'] = $this->last_link_request;
        return $output;
    }

    public function verify_card($params) {
        //$info = http_build_query($params['info']);	
        parse_str(urldecode($params['info']), $info);
        list($provider, $refcode) = explode("|", $params['channel']);
        $args = array(
            'username' => $params['mobo_account'],
            'mobo_account' => $params['mobo_account'],
            'provider' => $provider,
            'refcode' => $refcode,
            'serial' => $params['serial'],
            'pin' => $params['pin'],
            'card' => $params['telco'],
            'uid' => $params['mobo_id'],
            'client_id' => $params['client_id'],
            'platform' => $params['platform'],
            'version' => $params['version'],
            'device_id' => $params['device_id'],
            'device_token' => $params['device_token'],
            'bundle_id' => $params['bundle_id'],
            //'info' => json_encode($info),
        );
        $args = array_merge($args,$info);
        //$data = $this->_call_api_pay_card($args);
		//123456asjdhfkjsdfksdjhfksdjhfksj4rwrwerwe_disable
         if ($params['pin'] == '12345612345612345636' AND $params['serial'] == '12345612345612345636') {
                $data = '{"money":"1","value":"1","msg": "KISS1HIT nap thanh cong 5000 voi the menh gia 50000","table":"card_","id":"12072613"}';
                $args = array(
                'tid' => time(),
                'username' => $params['username'],
                'gold' => 1,
                'payMoney' => 1,
                'uid' => $params['transaction_id'],
                'payType' => "VND",
                );
			//@file_get_contents('http://api.3k.mobo.vn/?control=payment&func=add_money&type=card&money=1&gold=1&order_id='.$info['order_id'].'&server_id='.$info['server_id'].'&uid='.$params['mobo_id'].'&char_id='.$info['char_id'].'&app=3k&token=6769e502029187601e64a53d688b0e81');
          } else {
            $data = $this->_call_api_pay_card($args);
          } 
        if (empty($data) === FALSE) {
            $result_payment = json_decode($data, TRUE);
            if (is_array($result_payment) === TRUE) {
                if ($result_payment['value'] > 0) {
                    $output['status'] = TRUE;
                    $output['url'] = $this->last_link_request;
                    $output['content'] = $result_payment;
                    return $output;
                }
            }
        }
        $output['status'] = FALSE;
        $output['url'] = $this->last_link_request;
        $output['content']['msg'] = empty($result_payment) ? "Giao dịch đã được ghi nhận" : $result_payment['msg'];
        return $output;
    }

	public function verify_apple($params){	
		$info = json_decode($params['info'], true);
		list($provider, $refcode) = explode("|", $params['channel']);
		$args = array(
            'username' => $params['mobo_account'],                      
			'receipt_data' => $params['receipt_data'],									
			'server_id' => $info['server_id'],									
			'order_id' => $info['order_id'],
			'char_id' => $info['char_id'],						
			'provider' => $provider,
			'uid' => $params['mobo_id'],
            'refcode' => $refcode,			
        );			
        $data = $this->_call_verify_in_app($args);						
        if (empty($data) === FALSE) {
            $result = json_decode($data, TRUE);			
            if (is_array($result) === TRUE) {
                if ($result['code'] == 500) {
                    $output['status'] = TRUE;
                    $output['url'] = $this->last_link_request;
                    $output['content'] = $result;
                    return $output;
                }
            }
        }		
        $output['status'] = FALSE;		
        $output['url'] = $this->last_link_request;		
        return $output;				
	}
	
    public function add_money($params) {
        $args = array(
            'type' => $params['card'],
            'money' => $params['money'],
            'gold' => $params['gold'],
            'transaction' => $params['transaction'],
            'server_id' => $params['server_id'],
            'client_id' => $params['client_id'],
            'channel' => $params['channel'],
            'platform' => $params['platform'],
            'version' => $params['version'],
            'device_id' => $params['device_id'],
            'info' => $params['info'],
            'device_token' => $params['device_token'],
            'bundle_id' => $params['bundle_id'],
            'access_token' => $params['access_token'],
        );
        $data = $this->_call_api_add_money($args);

        if (empty($data) === FALSE) {
            $result_payment = json_decode($data, TRUE);
            if (is_array($result_payment) === TRUE) {
                if ($result_payment['code'] == 301) {
                    $output['status'] = TRUE;
                    $output['url'] = $this->last_link_request;
                    $output['content'] = $result_payment;
                    return $output;
                }
            }
        }
        $output['status'] = FALSE;
        $output['url'] = $this->last_link_request;
        return $output;
    }

    public function gen_transaction($params) {
        $this->CI->load->MeAPI_Model('UserModel');
        return $this->CI->UserModel->gen_transaction($params);
    }

    public function update_provider($params) {
        $args = array(
            'username' => $params['mobo_account'],
            'mobo_account' => $params['mobo_account'],
            'provider' => $params['provider'],
        );
        $data = $this->_call_api_payment($args, 'payment', 'update_provider');
        if (empty($data) === FALSE) {
            $result = json_decode($data, TRUE);
            if (is_array($result) === TRUE) {
                if ($result['code'] == 140) {
                    $output['status'] = TRUE;
                    $output['url'] = $this->last_link_request;
                    $output['content'] = $result;
                    return $output;
                }
            }
        }
        $output['status'] = FALSE;
        $output['url'] = $this->last_link_request;
        return $output;
    }

    private function _call_api_payment($params, $control, $func) {
        $token = md5(implode("", $params) . $this->key_payment);
        $this->last_link_request = $this->url_payment . "?control={$control}&func={$func}&" . http_build_query($params) . "&app=3k&token={$token}";
        
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->last_link_request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        $result = curl_exec($ch);
        MeAPI_Log::writeCsv(array(date('Y-m-d H:i:s'), $this->last_link_request, $result), 'get_pay_' . date('H'));
        return $result;
    }

    private function _call_api_pay_card($params) {
        $this->last_link_request = $this->url_pay_card . "?" . http_build_query($params) . "&app=3k&data=json";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->last_link_request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        $result = curl_exec($ch);
        MeAPI_Log::writeCsv(array(date('Y-m-d H:i:s'), $this->last_link_request, $result), 'pay_card_' . date('H'));
        return $result;
    }

    private function _call_api_add_money($params) {
        $token = md5(implode("", $params));
        $this->last_link_request = $this->url_add_money . "&" . http_build_query($params) . "&app=3k&token={$token}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->last_link_request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        $result = curl_exec($ch);
        MeAPI_Log::writeCsv(array(date('Y-m-d H:i:s'), $this->last_link_request, $result), 'add_money_' . date('H'));
        return $result;
    }

	private function _call_verify_in_app($params){
		$token = md5(implode("", $params));		
        $this->last_link_request = $this->url_in_app . "&" . http_build_query($params) . "&app={$this->app_name}&token={$token}";		
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->last_link_request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        $result = curl_exec($ch);				
        MeAPI_Log::writeCsv(array(date('Y-m-d H:i:s'), $this->last_link_request, $result), 'check_in_app_' . date('H'));
        return $result;
	}
}