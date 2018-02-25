<?php
 
class m_doivatpham extends CI_Model {
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
                ->from("event_doivatpham_tournament")
                ->where("tournament_status", 1)
                ->order_by("tournament_status", "desc")
                ->order_by("tournament_date_start", "asc")
                ->limit(1)
                ->get();

        return $query->result_array();
    }
    
    function check_times_received_gift($mobo_service_id, $server_id, $tournament){
        $this->db->select("*");
        $this->db->from("event_doivatpham_history");
        $this->db->where("mobo_service_id",$mobo_service_id);
        $this->db->where("server_id",$server_id);
        $this->db->where("status",1);
        $this->db->where("tournament_id", $tournament);
        
//        WHERE DATEDIFF(  `exchange_date` , NOW( ) ) =0
        $this->db->where("DATEDIFF(exchange_date,NOW())=",0);
        $query = $this->db->get();

        return $query->result_array();
    }
  
    function get_gift($tournament){
        $query = $this->db->select("*")
                ->from("event_doivatpham_gift")
                ->where("tournament_id", $tournament)                
                //->where("$rank BETWEEN rank_min AND rank_max")
                ->get();
//        echo $this->db->last_query(); die;
        return $query->result_array();
    }
    
    
    function get_gift_detail($tournament,$id){
        $query = $this->db->select("*")
                ->from("event_doivatpham_gift")
                ->where("tournament_id", $tournament)                
                ->where("id", $id)                
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

        $this->db->update("event_doivatpham_history");      
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
        $this->db->insert('event_doivatpham_history', $data);
    }
    
    //History
    public function get_exchange_history($server_id, $mobo_service_id, $tournament_id) {
        $query = "SELECT eteh.id, eteh.exchange_date, etr.item_name, etr.item_quantity, etr.gold_consumption, etr.gem_consumption";
        $query .= " FROM event_doivatpham_history eteh";
        $query .= " LEFT JOIN event_doivatpham_gift etr ON (eteh.gift_id = etr.id)";
        $query .= " WHERE eteh.status = 1 AND eteh.tournament_id = ". $tournament_id ." AND eteh.server_id =  " . $server_id . " AND eteh.mobo_service_id = " . $mobo_service_id;
        $query .= " ORDER BY eteh.exchange_date DESC";

        $result = $this->db->query($query);
        return $result->result_array();
    }
}