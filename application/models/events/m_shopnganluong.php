<?php

class m_shopnganluong extends CI_Model {

    private $db_cache, $db;

    function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Ho_Chi_Minh");

        if (empty($this->db_cache))
            $this->db_cache = $this->load->database(array('db' => 'db_cache', 'type' => 'slave'), true);

        if (empty($this->db))
            $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);
    }

    //Shop
    function gift_type_list() {
        $query = $this->db->select("*")
                ->from("event_shopnganluong_gift_type")
                ->where("type_status", 1)
                ->where("id !=", 5)
                ->order_by("order_no", "asc")
                ->get();

        return $query->result_array();
    }

    function gift_type_list_all() {
        $query = $this->db->select("*")
                ->from("event_shopnganluong_gift_type")
                ->where("id !=", 5)
                ->order_by("order_no", "asc")
                ->get();

        return $query->result_array();
    }

    function get_gift_pakage_special_list_by_type($gift_type) {
        $query = $this->db->select("*")
                ->from("event_shopnganluong_gift_pakage")
                ->where("gift_status", 1)
                ->where("gift_type", $gift_type)
                ->order_by("gift_number_request", "asc")
                ->get();

        return $query->result_array();
    }

    function get_gift_pakage_list_by_type($gift_type) {
        $query = $this->db->select("*")
                ->from("event_shopnganluong_gift_pakage")
                ->where("gift_status", 1)
                ->where("gift_type", $gift_type)
                ->order_by("gift_vip_point", "asc")
                ->get();

        return $query->result_array();
    }

    function get_gift_list_by_type($gift_type) {
        $query = $this->db->select("*")
                ->from("event_shopnganluong_gift")
                ->where("gift_status", 1)
                ->where("gift_type", $gift_type)
                ->get();

        return $query->result_array();
    }

    function get_gift_details($id) {
        $query = $this->db->select("*")
                ->from("event_shopnganluong_gift")
                ->where("gift_status", 1)
                ->where("id", $id)
                ->get();

        return $query->result_array();
    }

    public function get_total_gift_exchange_shop($server_id, $mobo_service_id, $item_ex_id) {
        $query = $this->db->select("COUNT(*) AS `TotalExchange`", false)
                ->from("event_shopnganluong_gift_exchange_history")
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("item_ex_id", $item_ex_id)
                ->get();

        return $query->result_array();
    }

    public function update_exchange_history($id, $data_send, $data_result, $exchange_gift_count) {
        $this->db
                ->set("data_send", $data_send)
                ->set("data_result", $data_result)
                ->set("exchange_gift_count", $exchange_gift_count)
                ->where("id", $id);

        $this->db->update("event_shopnganluong_gift_exchange_history");
        return $this->db->affected_rows();
    }

    //Gift Pakage
    function get_gift_pakage_details($id) {
        $query = $this->db->select("*")
                ->from("event_shopnganluong_gift_pakage")
                ->where("gift_status", 1)
                ->where("id", $id)
                ->get();

        return $query->result_array();
    }

    public function check_gift_buy_request($server_id, $mobo_service_id, $gift_number_request, $date_check_start, $date_check_end) {
        $query = $this->db->select("COUNT(*) AS `TotalExchange`", false)
                ->from("event_shopnganluong_gift_pakage_exchange_history")
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("gift_number_request", $gift_number_request)
                ->where("exchange_gift_date <= ", $date_check_end)
                ->where("exchange_gift_date >= ", $date_check_start)
                ->get();

        return $query->result_array();
    }

    public function get_total_gift_pakage_special_exchange_shop($server_id, $mobo_service_id, $item_ex_id, $date_check_start, $date_check_end) {
        $query = $this->db->select("COUNT(*) AS `TotalExchange`", false)
                ->from("event_shopnganluong_gift_pakage_exchange_history")
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("item_ex_id", $item_ex_id)
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

        $this->db->update("event_shopnganluong_gift_pakage_exchange_history");
        return $this->db->affected_rows();
    }

    public function get_total_gift_pakage_exchange_shop($server_id, $mobo_service_id, $item_ex_id) {
        $date_check_start = date("Y-m-d 00:00:00", time());
        $date_check_end = date("Y-m-d 23:59:59", time());

        $query = $this->db->select("COUNT(*) AS `TotalExchange`", false)
                ->from("event_shopnganluong_gift_pakage_exchange_history")
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("item_ex_id", $item_ex_id)
                ->where("exchange_gift_date <= ", $date_check_end)
                ->where("exchange_gift_date >= ", $date_check_start)
                ->get();

        return $query->result_array();
    }

    //Point
    function user_check_mobo_id($char_id, $server_id, $mobo_service_id) {
        $query = $this->db->select("*")
                ->from("event_shopnganluong_point")
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
                ->from("event_shopnganluong_point")
                //->where("char_id", $char_id)
                //->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->order_by("id", "desc")
                ->limit(1)
                ->get();

        return $query->result_array();
    }

    function user_check_point_exist_mobo($mobo_id) {
        $query = $this->db->select("*")
                ->from("event_shopnganluong_point")
                ->where("mobo_id", $mobo_id)
                ->order_by("id", "desc")
                ->get();

        return $query->result_array();
    }

    function update_moboid_null($mobo_id) {
        $this->db->set("mobo_id", null)
                ->where("mobo_id", $mobo_id);
        $this->db->update("event_shopnganluong_point");
        return $this->db->affected_rows();
    }

    function update_point($char_id, $server_id, $mobo_service_id, $pet_point) {
        $this->db
                ->set("user_point", "user_point - $pet_point", false)
                //->where("char_id", $char_id)
                //->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id);

        $this->db->update("event_shopnganluong_point");
        return $this->db->affected_rows();
    }

    public function update_shopnganluong_point_moboid($id, $mobo_id) {
        $this->db
                ->set("mobo_id", $mobo_id)
                ->where("id", $id);

        $this->db->update("event_shopnganluong_point");
        return $this->db->affected_rows();
    }

    //History
    public function get_charging_history($mobo_service_id) {
        $query = $this->db->select("*")
                ->from("event_shopnganluong_charging")
                ->where("mobo_service_id", $mobo_service_id)
                ->where("status", 1)
                ->get();

        return $query->result_array();
    }

    function get_gift_exchange_history($char_id, $server_id, $mobo_service_id) {
//        $query = "SELECT ecge.id, ecge.exchange_gift_date, ecge.exchange_gift_point, ecg.gift_name, ecg.gift_img";
//        $query .= " FROM event_shopnganluong_gift_exchange_history ecge";
//        $query .= " LEFT JOIN event_shopnganluong_gift ecg ON (ecge.item_ex_id = ecg.id)";
//        $query .= " WHERE ecge.char_id =  " . $char_id . " AND ecge.server_id =  " . $server_id . " AND ecge.mobo_service_id = " . $mobo_service_id;
//        $query .= " ORDER BY ecge.exchange_gift_date DESC";
        //echo $query; die;

        $query = "SELECT * FROM ((SELECT ecge.id, ecge.exchange_gift_date, ecge.exchange_gift_point, ecg.gift_name, ecg.gift_img ";
        $query .= "FROM event_shopnganluong_gift_exchange_history ecge LEFT JOIN event_shopnganluong_gift ecg ON (ecge.item_ex_id = ecg.id) ";
        $query .= "WHERE ecge.server_id =  " . $server_id . " AND ecge.mobo_service_id = '" . $mobo_service_id . "') ";
        $query .= "UNION (SELECT ecge.id, ecge.exchange_gift_date, ecge.exchange_gift_point, ecg.gift_name, ecg.gift_img ";
        $query .= "FROM event_shopnganluong_gift_pakage_exchange_history ecge LEFT JOIN event_shopnganluong_gift_pakage ecg ON (ecge.item_ex_id = ecg.id) ";
        $query .= "WHERE ecge.server_id =  " . $server_id . " AND ecge.mobo_service_id = '" . $mobo_service_id . "')) ";
        $query .= "as p ORDER BY p.exchange_gift_date DESC";

        $result = $this->db->query($query);
        return $result->result_array();
    }

    public function get_exchange_history_new_top($tournament_id, $char_id, $server_id, $mobo_service_id) {
        $query = $this->db->select("*")
                ->from("event_shopnganluong_exchange_history_top")
                ->where("mobo_service_id", $mobo_service_id)
                ->order_by("id", "desc")
                ->get();

        return $query->result_array();
    }

    function get_gift_outgame_exchange_history($char_id, $server_id, $mobo_service_id) {
        $query = "SELECT ecge.id, ecge.exchange_gift_date, ecge.exchange_gift_point, ecg.gift_name, ecg.gift_img";
        $query .= " FROM event_shopnganluong_gift_outgame_exchange_history ecge";
        $query .= " LEFT JOIN event_shopnganluong_gift_outgame ecg ON (ecge.item_ex_id = ecg.id)";
        $query .= " WHERE ecge.char_id =  " . $char_id . " AND ecge.server_id =  " . $server_id . " AND ecge.mobo_service_id = " . $mobo_service_id;
        $query .= " ORDER BY ecge.exchange_gift_date DESC";

        //echo $query; die;

        $result = $this->db->query($query);
        return $result->result_array();
    }

    public function card_exchange_history($mobo_service_id) {
        $query = $this->db->select("*")
                ->from("event_shopnganluong_card_exchange_history")
                ->where("mobo_service_id", $mobo_service_id)
                ->where("card_status", 1)
                ->order_by("id", "desc")
                ->get();

        return $query->result_array();
    }

    //NapThe
    public function get_shopnganluong_config() {
        $query = $this->db->select("*")
                ->from("event_shopnganluong_config")
                ->where("id", 1)
                ->get();

        return $query->result_array();
    }

    function add_point($char_id, $server_id, $mobo_service_id, $pet_point) {
        $this->db
                ->set("user_point", "user_point + $pet_point", false)
                ->set("update_date", Date('Y-m-d H:i:s'))
                //->where("char_id", $char_id)
                //->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id);

        $this->db->update("event_shopnganluong_point");
        return $this->db->affected_rows();
    }

    //DoiThe
    public function get_card_total_exchange_history() {
        $db = $this->db
                ->select('SUM(`card_value`) as "Total"', false)
                ->from('event_shopnganluong_card_exchange_history')
                ->where("card_status", 1)
                ->get();

        return $db->result_array();
    }

    public function update_card_exchange_history($id, $card_code, $card_serial, $card_status, $message_result, $message_result_json) {
        $this->db
                ->set("card_code", $card_code)
                ->set("card_serial", $card_serial)
                ->set("card_status", $card_status)
                ->set("message_result", $message_result)
                ->set("message_result_json", $message_result_json)
                ->where("id", $id);

        $this->db->update("event_shopnganluong_card_exchange_history");
        return $this->db->affected_rows();
    }

    //API NganLuong
    public function check_token_process($token_process) {
        $query = $this->db->select("*")
                ->from("event_shopnganluong_point_add_history")
                ->where("token_process", $token_process)
                ->get();

        return $query->result_array();
    }

    public function update_add_point_status_history($id, $add_status) {
        $this->db
                ->set("add_status", $add_status)
                ->where("id", $id);

        $this->db->update("event_shopnganluong_point_add_history");
        return $this->db->affected_rows();
    }

    function api_get_gift_exchange_history($mobo_service_id) {
        $query = "SELECT * FROM ((SELECT ecge.id, ecge.exchange_gift_date, ecge.exchange_gift_point, ecge.server_id, ecge.char_name, ecg.gift_name, ecg.gift_img ";
        $query .= "FROM event_shopnganluong_gift_exchange_history ecge LEFT JOIN event_shopnganluong_gift ecg ON (ecge.item_ex_id = ecg.id) ";
        $query .= "WHERE ecge.mobo_service_id = '" . $mobo_service_id . "') ";
        $query .= "UNION (SELECT ecge.id, ecge.exchange_gift_date, ecge.exchange_gift_point, ecge.server_id, ecge.char_name, ecg.gift_name, ecg.gift_img ";
        $query .= "FROM event_shopnganluong_gift_pakage_exchange_history ecge LEFT JOIN event_shopnganluong_gift_pakage ecg ON (ecge.item_ex_id = ecg.id) ";
        $query .= "WHERE ecge.mobo_service_id = '" . $mobo_service_id . "')) ";
        $query .= "as p ORDER BY p.exchange_gift_date DESC";

        $result = $this->db->query($query);
        return $result->result_array();
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
        if (empty($this->db))
            $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'master'), true);
        $sql = $this->db->update($table, $data, $where);
        return $this->db->affected_rows();
    }

    function insert($table, $data) {
        if (empty($this->db))
            $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'master'), true);
        $query = $this->db->insert($table, $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function insert_id($table, $data) {
        if (empty($this->db))
            $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'master'), true);
        $query = $this->db->insert($table, $data);
        $idinsert = $this->db->insert_id();
        if ($this->db->affected_rows() > 0) {
            return $idinsert;
        } else {
            return false;
        }
    }

    function insert_id_api($table, $data) {
        if (empty($this->db))
            $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'master'), true);
        $query = $this->db->insert($table, $data);
        $idinsert = $this->db->insert_id();
        //echo $this->db->last_query(); die;
        if ($this->db->affected_rows() > 0) {
            return $idinsert;
        } else {
            return false;
        }
    }

    function check_history($mobo_service_id) {
        if (empty($this->db))
            $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);

        $query = $this->db->select("id,char_id,char_name,server,mobo_service_id,award_name,type,create_date, status")
                //->where("char_id", $char_id)
                //->where("server", $server)
                ->where("mobo_service_id", $mobo_service_id)
                ->get("event_cacuoc_history");
        return $r = $query->num_rows();
    }

    function query_history($char_id, $server) {
        if (empty($this->db))
            $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);

        $query = $this->db->select("*")
                ->from("event_cacuoc_history")
                ->where("char_id", $char_id)
                ->where("server", $server)
                ->get();

        return $query->result_array();
    }

}

?>