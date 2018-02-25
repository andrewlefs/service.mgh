<?php
 
class m_duatopdautruong extends CI_Model {
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
                ->from("event_duatopdautruong_tournament")
                ->where("tournament_status", 1)
                ->order_by("tournament_status", "desc")
                ->order_by("tournament_date_start", "asc")
                ->limit(1)
                ->get();

        return $query->result_array();
    }
    
    function check_received_gifts($mobo_service_id, $server_id, $tournament ){
        $this->db->select("status");
        $this->db->from("event_duatopdautruong_history");
        $this->db->where("mobo_service_id",$mobo_service_id);
        $this->db->where("server_id",$server_id);
        $this->db->where("tournament_id", $tournament);
        
        $query = $this->db->get();

        return $query->result();
    }
  
    function get_gift($rank,$tournament){
        $query = $this->db->select("*")
                ->from("event_duatopdautruong_gift")
                ->where("tournament_id", $tournament)                
                ->where("rank_min <=", $rank)
                ->where("rank_max >=", $rank)
                //->where("$rank BETWEEN rank_min AND rank_max")
                ->get();
//        echo $this->db->last_query(); die;
        return $query->result_array();
    }
    
    public function update_exchange_history($id, $send_item_data, $send_item_result, $status, $gift_id) {
        $this->db
                ->set("send_item_result", $send_item_result)
                ->set("send_item_data", $send_item_data)
                ->set("status", $status)
                ->set("gift_id", $gift_id)
                ->where("id", $id);

        $this->db->update("event_duatopdautruong_history");      
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
        $this->db->insert('event_duatopdautruong_history', $data);
    }
    
    //History
    public function get_exchange_history($server_id, $mobo_service_id, $tournament_id) {
        $query = "SELECT eteh.id, eteh.exchange_date, etr.item_name, etr.item_quantity, etr.rank_min, etr.rank_max";
        $query .= " FROM event_duatopdautruong_history eteh";
        $query .= " LEFT JOIN event_duatopdautruong_gift etr ON (eteh.gift_id = etr.id)";
        $query .= " WHERE eteh.status = 1 AND eteh.tournament_id = ". $tournament_id ." AND eteh.server_id =  " . $server_id . " AND eteh.mobo_service_id = " . $mobo_service_id;
        $query .= " ORDER BY eteh.exchange_date DESC";

        $result = $this->db->query($query);
        return $result->result_array();
    }
}