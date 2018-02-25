<?php

class UserGame {

    private $CI;

    public function __construct() {
        $this->CI = & get_instance();
    }

    public function create_access_token($account_id) {
        $this->CI->load->MeAPI_Model('UserModel');
        $this->CI->load->MeAPI_Helper('transaction');
        $access_token = make_transaction('access_token');
        $arrInsertAccessTokes = array(
            'access_token' => $access_token,
            'account_id' => $account_id
        );
        $insert_access_token_id = $this->CI->UserModel->insert_access_token($arrInsertAccessTokes);
        if (empty($insert_access_token_id) === TRUE) {
            return array(
                'status' => 0
            );
        } else {
            $arrResult = array(
                'access_token' => $access_token,
                'client_id' => MeAPI_Config_Game::get_client_id(),
            );
            return array(
                'status' => TRUE,
                'data' => $arrResult
            );
        }
        return array(
            'status' => 0
        );
    }

    public function get_trial_account($device_id) {
        $this->CI->load->MeAPI_Model('UserModel');
        $result = $this->CI->UserModel->get_trial_account($device_id);
        if (empty($result) === FALSE) {
            $this->CI->load->library('Crypt', NULL, 'Crypt');
            foreach ($result as $key => &$value) {
                $value['signature'] = $this->CI->Crypt->Encrypt(json_encode($value), MeAPI_Config_Game::get_secret());
            }
        }
        return $result;
    }

    public function verify_access_token($access_token) {
        $this->CI->load->MeAPI_Model('UserModel');
        $result = $this->CI->UserModel->get_access_token($access_token);
        if (empty($result['id']) === FALSE) {
            return array(
                'account_id' => $result['account_id']
            );
        }
        return FALSE;
    }

    public function get_account($account) {
        $this->CI->load->MeAPI_Model('UserModel');
        $result = $this->CI->UserModel->get_account($account);		
        if (empty($result['id']) === FALSE) {
            return $result;
        }
        return FALSE;
    }

    public function get_account_info($account_id) {
        $this->CI->load->MeAPI_Model('UserModel');
        $result = $this->CI->UserModel->get_account_info($account_id);
        if (empty($result['id']) === FALSE) {
            return $result;
        }
        return FALSE;
    }

    public function store_access_token($params) {
        $this->CI->load->MeAPI_Model('UserModel');
        $data_insert = array(
            'account_id' => $params['account_id'],
            'character_id' => $params['character_id'],
            'server_id' => $params['server_id'],
            'device_token' => $params['device_token'],
            'platform' => $params['platform'],
        );
        return $this->CI->UserModel->store_access_token($data_insert);
    }
    public function general_event_url($event_id, $params){
        $key = "kd(Kuk0OdosIi#";
        $sign = md5(implode('', $params) . $key);
        return "{$event_id}/?" . http_build_query($params) . "&sign={$sign}";
    }

}
