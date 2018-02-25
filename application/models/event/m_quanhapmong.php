<?php

class m_quanhapmong extends CI_Model {

    private $db_cache, $db;

    function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        if (empty($this->db))
            $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);
    } 
    
    //Tournament
    public function get_tournament() {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_tournament")
                ->where("tournament_status", 1)
                ->order_by("tournament_status", "desc")
                ->order_by("tournament_date_start", "asc")
                ->limit(1)
                ->get();

        return $query->result_array();
    }

    public function check_exist_exchange_gift($tournament_id, $server_id, $char_id) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_exchange_history")
                ->where("tournament_id", $tournament_id)
                ->where("server_id", $server_id)
                ->where("char_id", $char_id)
                ->get();

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function get_reward_list($tournament_id) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_reward")
                ->where("tournament_id", $tournament_id)
                ->where("reward_status", 1)
                ->order_by("reward_point", "desc")
                ->get();

        return $query->result_array();
    }

    public function get_reward_details($id) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_reward")
                ->where("id", $id)
                ->get();

        return $query->result_array();
    }
    
    public function update_exchange_history($id, $data_send, $data_result, $exchange_gift_count) {
        $this->db
                ->set("data_send", $data_send)
                ->set("data_result", $data_result)
                ->set("exchange_gift_count", $exchange_gift_count)
                ->where("id", $id);

        $this->db->update("event_quanhapmong_gift_exchange_history");
        return $this->db->affected_rows();
    }

    public function update_exchange_history_arena($id, $data_send, $data_result, $exchange_point) {
        $this->db
                ->set("data_send", $data_send)
                ->set("data_result", $data_result)
                ->set("exchange_point", $exchange_point)
                ->where("id", $id);

        $this->db->update("event_quanhapmong_exchange_history");
        return $this->db->affected_rows();
    } 

    //Point
    function user_check_mobo_service_id($mobo_service_id) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_point")               
                ->where("mobo_service_id", $mobo_service_id)
                ->order_by("id", "desc")
                ->limit(1)
                ->get();

        return $query->result_array();
    }
    
    function user_check_mobo_id($char_id, $server_id, $mobo_service_id) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_point")
                //->where("char_id", $char_id)
                //->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->order_by("id", "desc")
                ->limit(1)
                ->get();

        return $query->result_array();
    }
    
    function user_check_point_exist($char_id, $server_id, $mobo_service_id) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_point")
                //->where("char_id", $char_id)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->order_by("id", "desc")
                ->limit(1)
                ->get();

        return $query->result_array();
    }
    
    function user_check_point_exist_mobo($mobo_id) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_point")                
                ->where("mobo_id", $mobo_id)
                ->order_by("id", "desc")               
                ->get();

        return $query->result_array();
    }
    
    function update_moboid_null($mobo_id) {
        $this->db->set("mobo_id", null)               
                ->where("mobo_id", $mobo_id);
        $this->db->update("event_quanhapmong_point");
        return $this->db->affected_rows();
    }

    function update_point($char_id, $server_id, $mobo_service_id, $pet_point) {
        $this->db
                ->set("user_point", "user_point - $pet_point", false)
                //->where("char_id", $char_id)
                //->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id);

        $this->db->update("event_quanhapmong_point");
        return $this->db->affected_rows();
    }

    function get_exchange_g_history($char_id, $server_id, $mobo_service_id, $ex_type) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_exchange_g_history")
                ->where("char_id", $char_id)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("ex_type", $ex_type)
                ->order_by("ex_date", "desc")
                ->get();

        return $query->result_array();
    }

    function check_exist_code_type($type_code_id, $char_id, $server_id, $mobo_service_id) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_point_exchange_history")
                ->where("type_code_id", $type_code_id)
                ->where("char_id", $char_id)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->get();

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function add_point($char_id, $server_id, $mobo_service_id, $pet_point) {
        $this->db
                ->set("user_point", "user_point + $pet_point", false)
                ->set("update_date", Date('Y-m-d H:i:s'))
                ->set("point_add", "point_add + $pet_point", false)
                //->where("char_id", $char_id)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id);

        $this->db->update("event_quanhapmong_point");
        return $this->db->affected_rows();
    }
    
    function add_point_np_to_mobo_service_id($mobo_service_id, $pet_point) {
        $this->db
                ->set("user_point", "user_point + $pet_point", false)
                ->set("update_date", Date('Y-m-d H:i:s'))                
                ->where("mobo_service_id", $mobo_service_id);

        $this->db->update("event_quanhapmong_point");
        return $this->db->affected_rows();
    }
    
     public function update_status_np_transer($id) {
        $this->db
                ->set("status_transfer", 1)
                ->where("id", $id);

        $this->db->update("event_quanhapmong_point_transfer_history");
        return $this->db->affected_rows();
    }
    
    function check_exist_transaction_processed($transaction_id) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_point_add_history")            
                ->where("transaction_id", $transaction_id) 
                ->where("add_status", 1)    
                ->get();

        return $query->result_array();
    }
    
    public function update_add_point_status_history($id, $add_status) {
        $this->db
                ->set("add_status", $add_status)                  
                ->where("id", $id);

        $this->db->update("event_quanhapmong_point_add_history");
        return $this->db->affected_rows();
    }
    
    function get_point_add_rate($charging_value, $payment_type) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_point_add_history")            
                ->where("charging_value", $charging_value) 
                ->where("type", $payment_type) 
                ->where("status", 1) 
                ->get();

        return $query->result_array();
    }

    //Gift
    function get_gift_list() {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_gift")
                ->where("gift_status", 1)
                ->where("gift_type", 0)
                ->get();

        return $query->result_array();
    }

    function get_gift_details($id) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_gift")
                ->where("gift_status", 1)
                ->where("id", $id)
                ->get();

        return $query->result_array();
    }

    function get_gift_list_by_type($gift_type) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_gift")
                ->where("gift_status", 1)
                ->where("gift_type", $gift_type)
                ->get();

        return $query->result_array();
    }
    
    //Gift Pakage
    function get_gift_pakage_details($id) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_gift_pakage")
                ->where("gift_status", 1)
                ->where("id", $id)
                ->get();

        return $query->result_array();
    }
    
    function get_gift_pakage_list_by_type($gift_type) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_gift_pakage")
                ->where("gift_status", 1)
                ->where("gift_type", $gift_type)
                ->order_by("gift_vip_point", "asc")
                ->get();

        return $query->result_array();
    }
    
    public function get_total_gift_pakage_exchange_shop($server_id, $mobo_service_id, $item_ex_id) {
        $date_check_start = date("Y-m-d 00:00:00", time());
        $date_check_end = date("Y-m-d 23:59:59", time());
        
        $query = $this->db->select("COUNT(*) AS `TotalExchange`", false)
                ->from("event_quanhapmong_gift_pakage_exchange_history")
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("item_ex_id", $item_ex_id)
                ->where("exchange_gift_date <= ", $date_check_end)
                ->where("exchange_gift_date >= ", $date_check_start) 
                ->get();

        return $query->result_array();
    }
    
    public function get_total_gift_pakage_special_exchange_shop($server_id, $mobo_service_id, $item_ex_id, $date_check_start, $date_check_end) {
        $query = $this->db->select("COUNT(*) AS `TotalExchange`", false)
                ->from("event_quanhapmong_gift_pakage_exchange_history")
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("item_ex_id", $item_ex_id)
                ->where("exchange_gift_date <= ", $date_check_end)
                ->where("exchange_gift_date >= ", $date_check_start) 
                ->get();

        return $query->result_array();
    }
    
    public function check_gift_buy_request($server_id, $mobo_service_id, $gift_number_request, $date_check_start, $date_check_end) {
        $query = $this->db->select("COUNT(*) AS `TotalExchange`", false)
                ->from("event_quanhapmong_gift_pakage_exchange_history")
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("gift_number_request", $gift_number_request)
                ->where("exchange_gift_date <= ", $date_check_end)
                ->where("exchange_gift_date >= ", $date_check_start) 
                ->get();

        return $query->result_array();
    }
    
    public function update_exchange_pakage_history($id, $data_send, $data_result) {
        $this->db
                ->set("data_send", $data_send)
                ->set("data_result", $data_result)               
                ->where("id", $id);

        $this->db->update("event_quanhapmong_gift_pakage_exchange_history");
        return $this->db->affected_rows();
    }
    
    function get_gift_pakage_special_list_by_type($gift_type) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_gift_pakage")
                ->where("gift_status", 1)
                ->where("gift_type", $gift_type)
                ->order_by("gift_number_request", "asc")
                ->get();

        return $query->result_array();
    }

    //History
    public function get_exchange_history($char_id, $server_id, $mobo_service_id) {
        $query = "SELECT eteh.id, eteh.exchange_date, etr.reward_name, etr.reward_point";
        $query .= " FROM event_quanhapmong_exchange_history eteh";
        $query .= " LEFT JOIN event_quanhapmong_reward etr ON (eteh.reward_id = etr.id)";
        $query .= " WHERE eteh.char_id =  " . $char_id . " AND eteh.server_id =  " . $server_id . " AND eteh.mobo_service_id = " . $mobo_service_id;
        $query .= " ORDER BY eteh.exchange_date DESC";

        $result = $this->db->query($query);
        return $result->result_array();
    }

    public function get_exchange_history_new($tournament_id, $char_id, $server_id, $mobo_service_id) {
        $query = "SELECT eteh.id, eteh.exchange_date, etr.reward_name, etr.reward_point";
        $query .= " FROM event_quanhapmong_exchange_history eteh";
        $query .= " LEFT JOIN event_quanhapmong_reward etr ON (eteh.reward_id = etr.id)";
        $query .= " WHERE eteh.char_id =  " . $char_id . " AND eteh.server_id =  " . $server_id . " AND eteh.mobo_service_id = " . $mobo_service_id . " AND eteh.tournament_id = " . $tournament_id;
        $query .= " ORDER BY eteh.exchange_date DESC";

        //$query = "SELECT T12.id, T12.exchange_date, etr.reward_name, etr.reward_point";
        //$query .= " FROM (SELECT id, char_id, server_id, mobo_service_id, reward_id, tournament_id, exchange_date";
        //$query .= " FROM event_quanhapmong_exchange_history eteh1 UNION SELECT id, char_id, server_id, mobo_service_id, reward_id, tournament_id, exchange_date";
        //$query .= " FROM event_quanhapmong_exchange_history_top eteh2) T12";
        //$query .= " LEFT JOIN event_quanhapmong_reward etr ON ( T12.reward_id = etr.id ) ";
        //$query .= " WHERE T12.char_id = $char_id AND T12.server_id = $server_id AND T12.mobo_service_id = $mobo_service_id AND T12.tournament_id = $tournament_id";
        //$query .= " ORDER BY T12.exchange_date DESC";   

        $result = $this->db->query($query);
        return $result->result_array();
    }

    public function get_exchange_history_premiership($tournament_id, $char_id, $server_id, $mobo_service_id) {
        $query = "SELECT eteh.id, eteh.exchange_date, etr.reward_name, etr.reward_point";
        $query .= " FROM event_quanhapmong_exchange_history_premiership eteh";
        $query .= " LEFT JOIN event_quanhapmong_reward_premiership etr ON (eteh.reward_id = etr.id)";
        $query .= " WHERE eteh.char_id =  " . $char_id . " AND eteh.server_id =  " . $server_id . " AND eteh.mobo_service_id = " . $mobo_service_id . " AND eteh.tournament_id = " . $tournament_id;
        $query .= " ORDER BY eteh.exchange_date DESC";

        $result = $this->db->query($query);
        return $result->result_array();
    }

    public function get_charging_history($mobo_service_id) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_charging")
                ->where("mobo_service_id", $mobo_service_id)
                ->where("status", 1)
                ->get();

        return $query->result_array();
    }
    
    function get_gift_pakage_exchange_history($server_id, $mobo_service_id) {
        $query = "SELECT ecge.id, ecge.exchange_gift_date, ecge.exchange_gift_point, ecg.gift_name, ecg.gift_img ";
        $query .= "FROM event_quanhapmong_gift_pakage_exchange_history ecge LEFT JOIN event_quanhapmong_gift_pakage ecg ON (ecge.item_ex_id = ecg.id) ";
        $query .= "WHERE ecge.server_id = " . $server_id . " AND ecge.mobo_service_id = '" . $mobo_service_id . "' ";
        $query .= " ORDER BY ecge.exchange_gift_date DESC";
        
        $result = $this->db->query($query);
        return $result->result_array();  
    }

    function get_gift_exchange_history($char_id, $server_id, $mobo_service_id) {
//        $query = "SELECT ecge.id, ecge.exchange_gift_date, ecge.exchange_gift_point, ecg.gift_name, ecg.gift_img";
//        $query .= " FROM event_quanhapmong_gift_exchange_history ecge";
//        $query .= " LEFT JOIN event_quanhapmong_gift ecg ON (ecge.item_ex_id = ecg.id)";
//        $query .= " WHERE ecge.char_id =  " . $char_id . " AND ecge.server_id =  " . $server_id . " AND ecge.mobo_service_id = " . $mobo_service_id;
//        $query .= " ORDER BY ecge.exchange_gift_date DESC";

        //echo $query; die;

        $query = "SELECT * FROM ((SELECT ecge.id, ecge.exchange_gift_date, ecge.exchange_gift_point, ecg.gift_name, ecg.gift_img ";
        $query .= "FROM event_quanhapmong_gift_exchange_history ecge LEFT JOIN event_quanhapmong_gift ecg ON (ecge.item_ex_id = ecg.id) ";
        $query .= "WHERE ecge.char_id =  '" . $char_id . "' AND ecge.server_id =  " . $server_id . " AND ecge.mobo_service_id = '" . $mobo_service_id . "') ";
        $query .= "UNION (SELECT ecge.id, ecge.exchange_gift_date, ecge.exchange_gift_point, ecg.gift_name, ecg.gift_img ";
        $query .= "FROM event_quanhapmong_gift_pakage_exchange_history ecge LEFT JOIN event_quanhapmong_gift_pakage ecg ON (ecge.item_ex_id = ecg.id) ";
        $query .= "WHERE ecge.char_id =  '" . $char_id . "' AND ecge.server_id =  " . $server_id . " AND ecge.mobo_service_id = '" . $mobo_service_id . "')) ";
        $query .= "as p ORDER BY p.exchange_gift_date DESC";
        
        $result = $this->db->query($query);
        return $result->result_array();
    }

    function get_gift_outgame_exchange_history($char_id, $server_id, $mobo_service_id) {
        $query = "SELECT ecge.id, ecge.exchange_gift_date, ecge.exchange_gift_point, ecg.gift_name, ecg.gift_img";
        $query .= " FROM event_quanhapmong_gift_outgame_exchange_history ecge";
        $query .= " LEFT JOIN event_quanhapmong_gift_outgame ecg ON (ecge.item_ex_id = ecg.id)";
        $query .= " WHERE ecge.char_id =  " . $char_id . " AND ecge.server_id =  " . $server_id . " AND ecge.mobo_service_id = " . $mobo_service_id;
        $query .= " ORDER BY ecge.exchange_gift_date DESC";

        //echo $query; die;

        $result = $this->db->query($query);
        return $result->result_array();
    }

    public function card_exchange_history($mobo_service_id) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_card_exchange_history")
                ->where("mobo_service_id", $mobo_service_id)
                ->where("card_status", 1)
                ->order_by("id", "desc")
                ->get();

        return $query->result_array();
    }

    public function get_card_total_exchange_history() {
        $db = $this->db
                ->select('SUM(`card_value`) as "Total"', false)
                ->from('event_quanhapmong_card_exchange_history')
                ->where("card_status", 1)
                ->get();

        return $db->result_array();
    }

    public function update_quanhapmong_point_moboid($id, $mobo_id) {
        $this->db
                ->set("mobo_id", $mobo_id)
                ->where("id", $id);

        $this->db->update("event_quanhapmong_point");
        return $this->db->affected_rows();
    }

    public function quanhapmong_join_history($mobo_service_id, $tournament_id) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_join_history")
                ->where("mobo_service_id", $mobo_service_id)
                ->where("tournament_id", $tournament_id)
                ->get();

        return $query->result_array();
    }

    //Shop NganLuong
    function gift_type_list(){
        $query = $this->db->select("*")
                ->from("event_quanhapmong_gift_type")
                ->where("type_status", 1)
                ->where("id !=", 5)
                ->order_by("id", "desc")
                ->get();

        return $query->result_array();
    }
    
    function gift_type_list_all(){
        $query = $this->db->select("*")
                ->from("event_quanhapmong_gift_type")  
                ->where("id !=", 5)
                ->order_by("id", "desc")
                ->get();

        return $query->result_array();
    }
    
    public function get_total_gift_exchange_shop($server_id, $mobo_service_id, $item_ex_id) {
        $query = $this->db->select("SUM(exchange_gift_count) AS `TotalExchange`", false)
                ->from("event_quanhapmong_gift_exchange_history")
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("item_ex_id", $item_ex_id)
                ->get();

        return $query->result_array();
    }
    
    public function get_quanhapmong_point_transfer_history($mobo_service_id) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_point_transfer_history")               
                ->where("mobo_service_id", $mobo_service_id)  
                ->where("status_transfer", 1) 
                ->order_by("id", "desc")
                ->get();

        return $query->result_array();
    }
    
    //Calendar Bonus
    function check_bonus_calendar($mobo_service_id, $server_id, $gift_pakage_id) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_calendar_bonus")    
                ->where("mobo_service_id", $mobo_service_id)
                ->where("server_id", $server_id)               
                ->where("gift_pakage_id", $gift_pakage_id)
                ->order_by("id", "desc")
                ->limit(1)
                ->get();
        
        //echo $this->_db->last_query(); die;

        if ($query) {
            return $query->result_array();
        } else {
            return null;
        }
    }
    
    function get_bonus_calendar_by_user($mobo_service_id, $server_id) {        
        $query = "SELECT ecge.id, ecge.bonus_date, ecge.status_received, ecge.gift_pakage_id, ecg.gift_name, ecg.gift_img";
        $query .= " FROM event_quanhapmong_calendar_bonus ecge";
        $query .= " LEFT JOIN event_quanhapmong_gift_pakage ecg ON (ecge.gift_pakage_id = ecg.id)";
        $query .= " WHERE ecge.server_id =  " . $server_id . " AND ecge.mobo_service_id = " . $mobo_service_id;
        $query .= " ORDER BY ecge.bonus_date ASC";

        $result = $this->db->query($query);
        return $result->result_array();
    }
    
    function get_bonus_calendar_details($id) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_calendar_bonus")    
                ->where("id", $id) 
                ->get();

        if ($query) {
            return $query->result_array();
        } else {
            return null;
        }
    }
    
    function uptdate_bonus_calendar_status($id, $status_received) {
        $this->db
                ->set("status_received", $status_received)    
                ->where("id", $id);

        $this->db->update("event_quanhapmong_calendar_bonus");
        return $this->db->affected_rows();
    }
    
    function update_received_history($id, $send_item_data, $send_item_result, $status) {
        $this->db
                ->set("send_item_data", $send_item_data)
                ->set("send_item_result", $send_item_result)
                ->set("status", $status)   
                ->where("id", $id);

        $this->db->update("event_quanhapmong_received_history");
        return $this->db->affected_rows();
    }

    ///////////////////////////////////////// 
    function freeDBResource($dbh) {
        while (mysqli_next_result($dbh)) {
            if ($l_result = mysqli_store_result($dbh)) {
                mysqli_free_result($l_result);
            }
        }
    }

    function update($table, $data, $where) {
        $sql = $this->db->update($table, $data, $where);
        return $this->db->affected_rows();
    }

    function insert($table, $data) {
        $query = $this->db->insert($table, $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function insert_id($table, $data) {
        $query = $this->db->insert($table, $data);
        $idinsert = $this->db->insert_id();
//        if($table == "event_quanhapmong_calendar_bonus"){
//        echo $this->db->last_query(); die;
//        }
        if ($this->db->affected_rows() > 0) {
            return $idinsert;
        } else {
            return false;
        }
    }

    function check_history($mobo_service_id) {
        $query = $this->db->select("id,char_id,char_name,server,mobo_service_id,award_name,type,create_date, status")
                //->where("char_id", $char_id)
                //->where("server", $server)
                ->where("mobo_service_id", $mobo_service_id)
                ->get("event_quanhapmong_history");
        return $r = $query->num_rows();
    }

    function query_history($char_id, $server) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_history")
                ->where("char_id", $char_id)
                ->where("server", $server)
                ->get();

        return $query->result_array();
    }
}

?>