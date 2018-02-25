<?php
 
class m_naplandau extends CI_Model {
    private $db_cache, $db, $db_nap;

    function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        if (empty($this->db))
            $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);
        if (empty($this->db_nap))
            $this->db_nap = $this->load->database(array('db' => 'db_nap', 'type' => 'slave'), true);
    } 
    //Tournament
    public function get_tournament() {
        $query = $this->db->select("*")
                ->from("event_naplandau_tournament")
                ->where("tournament_status", 1)
                ->order_by("tournament_status", "desc")
                ->order_by("tournament_date_start", "asc")
                ->limit(1)
                ->get();

        return $query->result_array();
    }
    
    function freeDBResource($dbh){
        while(mysqli_next_result($dbh)){
            if($l_result = mysqli_store_result($dbh)){
                mysqli_free_result($l_result);
            }
        }
    }
//    public function getfirstpay($server_id, $msi){
//     //lấy danh sách top chiến lực PET event_get_top_pet(sid) roleid,RoleName,petid,petname,level,exp,power
//     $this->freeDBResource($this->db_nap->conn_id);
//           $sql = "CALL sp_event_mathan_first_pay(?,?)";
//           $parameters = array($server_id,$msi);
//           $query = $this->db_nap->query($sql, $parameters);
//           if ($query){        
//               return $query->result_array();
//           }
//           else{
//               return false;
//           }
//    }
    public function getfirstpay($server_id, $mobo_service_id)
    {
        $this->freeDBResource($this->db_nap->conn_id);
        $sql = "CALL sp_event_mathan_first_pay($server_id, $mobo_service_id)";
//        $sql = "CALL sp_event_mathan_first_pay(18,1461537892523738379)";        
        $query = $this->db_nap->query($sql);  
        return $query->result_array();
    }
    function check_received_gifts($mobo_service_id, $server_id){
        $this->db->select("status");
        $this->db->from("event_naplandau_history");
        $this->db->where("mobo_service_id",$mobo_service_id);
        $this->db->where("server_id",$server_id);
        $query = $this->db->get();

        return $query->result();
    }
    
    public function update_exchange_history($id, $send_item_data, $send_item_result, $status) {
        $this->db
                ->set("send_item_result", $send_item_result)
                ->set("send_item_data", $send_item_data)
                ->set("status", $status)
                ->where("id", $id);

        $this->db->update("event_naplandau_history");      
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
        $this->db->insert('event_naplandau_history', $data);
    }
}