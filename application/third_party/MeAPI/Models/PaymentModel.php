<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PaymentModel
 *
 * @author vietbl
 */
class PaymentModel extends CI_Model {

    protected $dbMaster;
    protected $dbSlave;
    protected $this_class;
    protected $this_func;
    protected $curDate;

    public function __construct() {
        parent::__construct();
        
        // current class name
        $this->this_class = __CLASS__;
        // current date
        $this->curDate = date('Y-m-d H:i:s');
        
        if (empty($this->dbSlave))
            $this->dbSlave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), true);
        if (empty($this->dbMaster))
            $this->dbMaster = $this->load->database(array('db' => 'user_info', 'type' => 'master'), true);
    }
    
    /*
     * Chức năng ghi log
     */
    private function write_log_message($message){
        log_message('error', $this->this_class . ' - ' . $this->this_func . ' --> ' . $message);
    }
    
    /*
     * Check duplicate transaction
     */
    public function checkDuplicateTransaction($transaction_id){
        // init log
        $this->this_func = __FUNCTION__;
        
        $trans_query = $this->dbMaster->select("transaction_id")
                ->from("cash_to_game_trans")
                ->where("transaction_id", $transaction_id)
                ->where_in("status", array(0, 1)) //0: transaction init; 1: transaction success
                ->get();
        
        // check error
        if ($this->dbMaster->_error_number() > 0) {
            // ghi log lỗi
            $this->write_log_message($this->dbMaster->_error_message());
            return true;
        }
        
        if ($trans_query->num_rows() > 0){
            return true;
        }
        return false;
    }   

    public function setTransaction($mobo_service_id, $user_name, $transaction_id, $server_id, $time_stamp, $type, $amount, $origin_gold, $gold, 
                            $channel, $platform, $carrier, $client_ip, $ext, $games, $description = ''){
        // init log
        $this->this_func = __FUNCTION__;
        
        $this->dbMaster->set('date', $this->curDate)
                ->set('mobo_service_id', $mobo_service_id)
                ->set('user_name', $user_name)
                ->set('transaction_id', $transaction_id)
                ->set('server_id', $server_id)
                ->set('time_stamp', $time_stamp)
                ->set('type', $type)
                ->set('amount', $amount)
                ->set('origin_gold', $origin_gold)
                ->set('gold', $gold)
                ->set('channel', $channel)
                ->set('platform', $platform)
                ->set('carrier', $carrier)
                ->set('client_ip', $client_ip)
                ->set('ext', $ext)
                ->set('games', $games)
                ->set('status', 0)
                ->set('description', $description)
                ->set('is_promo_payment', 0)
                ->insert('cash_to_game_trans');
        
        // check error
        if ($this->dbMaster->_error_number() > 0) {
            // ghi log lỗi
            $this->write_log_message($this->dbMaster->_error_message());
            //show_error($this->dbMaster->_error_message());
            return null;
        }
        
        if ($this->dbMaster->affected_rows() > 0)
            return $this->dbMaster->insert_id();
    }
    
    public function finishTransaction($idInserted, $status, $latency, $description = ''){
        // init log
        $this->this_func = __FUNCTION__;
        
        $this->dbMaster->set('status', $status)
                        ->set('latency', $latency)
                        ->set('description', $description)
                        ->where('id', $idInserted)
                        ->update('cash_to_game_trans');
        
        // check error
        if ($this->dbMaster->_error_number() > 0) {
            // ghi log lỗi
            $this->write_log_message($this->dbMaster->_error_message());
            //show_error($this->dbMaster->_error_message());
        }
    }
}
