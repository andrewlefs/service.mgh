<?php

class PaymentGame {

    private $CI;
    private $url_pay_card = 'http://payment.gomobi.vn/index.php/pay/card/';
    private $url_get_pay = 'http://payment.gomobi.vn/index.php/pay/card/';
    private $url_add_money = 'http://api.bongda.mobo.vn/?control=user&func=add_money';
    private $key_add_money = 'tsfkgadijqfbcadfhqoqw';

    public function __construct() {
        $this->CI = & get_instance();
    }

    public function get_pay($params) {
        // client_id, access_token, channel, platform, version, device_id, info, bundle_id, device_token
    }

    public function verify_card($params) {
        $info = json_decode($params['info'], true);
        list($provider, $refcode) = explode("|", $params['channel']);
        $args = array(
            'username' => $params['mobo_account'],
            'mobo_account' => $params['mobo_account'],
            'provider' => $provider,
            'refcode' => $refcode,
            'serial' => $params['serial'],
            'pin' => $params['pin'],
            'card' => $params['telco'],
            'uid' => $info['transaction'],
            'client_id' => $params['client_id'],
            'platform' => $params['platform'],
            'version' => $params['version'],
            'device_id' => $params['device_id'],
            'device_token' => $params['device_token'],
            'bundle_id' => $params['bundle_id'],
            'info' => $params['info'],
        );

        $data = $this->_call_api_pay_card($args);
        /* if ($params['pin'] == '123456' AND $params['serial'] == '123456') {
          $data = '{"money":"1","value":"1","msg": "KISS1HIT nap thanh cong 5000 voi the menh gia 50000","table":"card_","id":"12072613"}';
          $args = array(
          'tid' => time(),
          'username' => $params['username'],
          'gold' => 1,
          'payMoney' => 1,
          'uid' => $params['transaction_id'],
          'payType' => "VND",
          );
          } else {
          $data = $this->_call_api_pay_card($args);
          } */
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

    private function _call_api_get_pay($params) {
        // API get_pay
    }

    private function _call_api_pay_card($params) {
        $this->last_link_request = $this->url_pay_card . "?" . http_build_query($params) . "&app=12g&data=json";
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
        $this->last_link_request = $this->url_add_money . "&" . http_build_query($params) . "&app=12g&token={$token}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->last_link_request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        $result = curl_exec($ch);
        MeAPI_Log::writeCsv(array(date('Y-m-d H:i:s'), $this->last_link_request, $result), 'add_money_' . date('H'));
        return $result;
    }

}