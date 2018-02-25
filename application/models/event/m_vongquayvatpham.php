<?php
 
class m_vongquayvatpham extends CI_Model {
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
                ->from("event_vongquayvatpham_tournament")
                ->where("tournament_status", 1)
                ->order_by("tournament_status", "desc")
                ->order_by("tournament_date_start", "asc")
                ->limit(1)
                ->get();

        return $query->result_array();
    }
  
    function get_gift($tournament){
        $query = $this->db->select("*")
                ->from("event_vongquayvatpham_gift")
                ->where("tournament_id", $tournament)                
                ->get();
//        echo $this->db->last_query(); die;
        return $query->result_array();
    }
    
    public function update_exchange_history($id, $send_item_data, $send_item_result, $status) {
        $this->db
                ->set("send_item_result", $send_item_result)
                ->set("send_item_data", $send_item_data)
                ->set("status", $status)
                ->where("id", $id);

        $this->db->update("event_vongquayvatpham_history");      
        return $this->db->affected_rows();
    }
  
    function insert($table, $data) {
        $query = $this->db->insert($table, $data);
        //echo $this->db->last_query(); die;
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    function insert_id($table, $data) {
        $query = $this->db->insert($table, $data);
        $idinsert = $this->db->insert_id();
        if ($this->db->affected_rows() > 0) {
            return $idinsert;
        } else {
            return false;
        }
    }
    
    function log($char_id, $server_id, $char_name, $mobo_service_id, $exchange_date, $mobo_id, $send_item_result){
        $data = array(
                   'char_id' => $char_id,
                   'server_id' => $server_id,
                   'char_name' => $char_name,
                   'mobo_service_id' => $mobo_service_id,
                   'exchange_date' => $exchange_date,
                   'mobo_id' => $mobo_id,
                   'status' => 1,
                   'send_item_result' => $send_item_result,
                );
        $this->db->insert('event_vongquayvatpham_history', $data);
    }
}