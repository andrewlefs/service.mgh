<?php
class m_toptyvo extends CI_Model {

    private $db_cache, $db, $db_cache_mgh2;

    function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        
        if (empty($this->db_cache))
            $this->db_cache = $this->load->database(array('db' => 'db_cache', 'type' => 'slave'), true);
        
        if (empty($this->db))
            $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true); 
        
        if (empty($this->db_cache_mgh2))
            $this->db_cache_mgh2 = $this->load->database(array('db' => 'db_cache_mgh2', 'type' => 'slave'), true);
    }
    
    public function get_tournament()
    {       
    	$query = $this->db->select("*")
                ->from("event_toppk_tournament")               
                ->where("tournament_status", 1)
                ->order_by("tournament_status", "desc")
                ->order_by("tournament_date_start", "asc")
                ->limit(1)
                ->get();
        
        return $query->result_array();
    }
    
    public function tournament_list()
    {       
    	$query = $this->db->select("*")
                ->from("event_toppk_tournament")               
                ->where("tournament_status", 1)
                ->order_by("id", "desc")
                //->order_by("tournament_date_start", "asc")               
                ->get();
        
        return $query->result_array();
    }
    
    public function get_tournament_details($id)
    {       
    	$query = $this->db->select("*")
                ->from("event_toppk_tournament")               
                ->where("tournament_status", 1)
                ->where("id", $id)                
                ->get();
        
        return $query->result_array();
    }
    
    public function check_exist_exchange_gift($tournament_id, $server_id, $char_id)
    {       
    	$query = $this->db->select("*")
                ->from("event_toppk_exchange_history") 
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
    
    public function get_reward_list($tournament_id)
    {       
    	$query = $this->db->select("*")
                ->from("event_toppk_reward")
                ->where("tournament_id", $tournament_id)
                ->where("reward_status", 1)
                ->order_by("reward_point", "desc")               
                ->get();
        
        return $query->result_array();
    }
    
    public function get_reward_details($id)
    {       
    	$query = $this->db->select("*")
                ->from("event_toppk_reward")
                ->where("id", $id)                        
                ->get();
        
        return $query->result_array();
    }
    
    public function get_exchange_history($char_id, $server_id, $mobo_service_id)
    {       
    	$query = "SELECT eteh.id, eteh.exchange_date, etr.reward_name, etr.reward_point";
        $query .= " FROM event_toppk_exchange_history eteh";
        $query .= " LEFT JOIN event_toppk_reward etr ON (eteh.reward_id = etr.id)";       
        $query .= " WHERE eteh.char_id =  " . $char_id . " AND eteh.server_id =  " . $server_id . " AND eteh.mobo_service_id = " . $mobo_service_id;      
        $query .= " ORDER BY eteh.exchange_date DESC";
        
    	$result=$this->db->query($query);      
        return $result->result_array(); 
    }
    
    public function get_exchange_history_new($tournament_id, $char_id, $server_id, $mobo_service_id)
    {       
        $query = "SELECT eteh.id, eteh.exchange_date, etr.reward_name, etr.reward_point";
        $query .= " FROM event_toppk_exchange_history eteh";
        $query .= " LEFT JOIN event_toppk_reward etr ON (eteh.reward_id = etr.id)";       
        $query .= " WHERE eteh.char_id =  " . $char_id . " AND eteh.server_id =  " . $server_id . " AND eteh.mobo_service_id = " . $mobo_service_id . " AND eteh.tournament_id = " . $tournament_id;      
        $query .= " ORDER BY eteh.exchange_date DESC";
        
        //$query = "SELECT T12.id, T12.exchange_date, etr.reward_name, etr.reward_point";
        //$query .= " FROM (SELECT id, char_id, server_id, mobo_service_id, reward_id, tournament_id, exchange_date";
        //$query .= " FROM event_toppk_exchange_history eteh1 UNION SELECT id, char_id, server_id, mobo_service_id, reward_id, tournament_id, exchange_date";
        //$query .= " FROM event_toppk_exchange_history_top eteh2) T12";
        //$query .= " LEFT JOIN event_toppk_reward etr ON ( T12.reward_id = etr.id ) ";
        //$query .= " WHERE T12.char_id = $char_id AND T12.server_id = $server_id AND T12.mobo_service_id = $mobo_service_id AND T12.tournament_id = $tournament_id";
        //$query .= " ORDER BY T12.exchange_date DESC";   
        
    	$result=$this->db->query($query);      
        return $result->result_array(); 
    }
    
    public function get_exchange_history_premiership($tournament_id, $char_id, $server_id, $mobo_service_id)
    {       
        $query = "SELECT eteh.id, eteh.exchange_date, etr.reward_name, etr.reward_point";
        $query .= " FROM event_toppk_exchange_history_premiership eteh";
        $query .= " LEFT JOIN event_toppk_reward_premiership etr ON (eteh.reward_id = etr.id)";       
        $query .= " WHERE eteh.char_id =  " . $char_id . " AND eteh.server_id =  " . $server_id . " AND eteh.mobo_service_id = " . $mobo_service_id . " AND eteh.tournament_id = " . $tournament_id;      
        $query .= " ORDER BY eteh.exchange_date DESC";
        
    	$result=$this->db->query($query);      
        return $result->result_array(); 
    }
    
    public function write_cache_log($p_uid, $p_serverid, $p_currpoint, $p_point, $p_rewardid, $p_date)
    {
        $this->freeDBResource($this->db_cache->conn_id);
        $sql = "CALL Event_Tyvo_Doiqua_LogWriter(?,?,?,?,?,?)";
        $parameters = array($p_uid, $p_serverid, $p_currpoint, $p_point, $p_rewardid, $p_date);
        $query = $this->db_cache->query($sql, $parameters);        
       
        return $query->result_array();
    }
    
    public function get_pk_point($Sever_ID, $UserID)
    {
        $this->freeDBResource($this->db_cache->conn_id);
        $sql = "CALL Event_Tyvo_GetPoint(?,?)";
        $parameters = array($Sever_ID, $UserID);
        $query = $this->db_cache->query($sql, $parameters);
        
        return $query->result_array();
    }
    
    public function get_pk_point_new($tournament_store_proc, $Sever_ID, $UserID)
    {
        $this->freeDBResource($this->db_cache->conn_id);
        $sql = "CALL $tournament_store_proc(?,?)";
        $parameters = array($Sever_ID, $UserID);
        $query = $this->db_cache->query($sql, $parameters);
        
        if ($query){        
            return $query->result_array();
        }
        else{
            return null;
        }
    }
    
    public function get_pk_point_new_test($tournament_store_proc, $Sever_ID, $UserID)
    {
        $this->freeDBResource($this->db_cache->conn_id);
        $sql = "CALL $tournament_store_proc(?,?)";
        $parameters = array($Sever_ID, $UserID);
        $query = $this->db_cache->query($sql, $parameters);
        
        if ($query){        
            return $query->result_array();
        }
        else{
            return null;
        }
    }
    
    public function get_top($tournament_store_proc_top)
    {
        $this->freeDBResource($this->db_cache->conn_id);
        $sql = "CALL $tournament_store_proc_top()";       
        $query = $this->db_cache->query($sql);
        
        return $query->result_array();
    }
    
    public function get_top_server($tournament_store_proc_top_server, $server_id)
    {
        $this->freeDBResource($this->db_cache->conn_id);
        $sql = "CALL $tournament_store_proc_top_server($server_id)";       
        $query = $this->db_cache->query($sql);
        
        return $query->result_array();
    }
	
	public function update_exchange_history($id, $data_send, $data_result){
        $this->db
                ->set("data_send", $data_send)
                ->set("data_result", $data_result)
                ->where("id", $id);

        $this->db->update("event_toppk_exchange_history");
        return $this->db->affected_rows();
	}
    
    //Reward Top
    public function check_exist_exchange_gift_top($tournament_id, $server_id, $char_id)
    {       
    	$query = $this->db->select("*")
                ->from("event_toppk_exchange_history_top") 
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
    
    public function get_reward_details_top($id)
    {       
    	$query = $this->db->select("*")
                ->from("event_toppk_reward_top")
                ->where("id", $id)                        
                ->get();
        
        return $query->result_array();
    }
    
    public function check_rank_valid($rank, $tournament_id)
    {       
    	$query = $this->db->select("*")
                ->from("event_toppk_reward_top") 
                ->where("tournament_id", $tournament_id)   
                ->where("reward_rank_min <=", $rank) 
                ->where("reward_rank_max >=", $rank) 
                ->get();
        
        return $query->result_array();
    }
    
    public function check_rank_premier_valid($rank, $tournament_id)
    {       
    	$query = $this->db->select("*")
                ->from("event_toppk_reward_premiership") 
                ->where("tournament_id", $tournament_id)   
                ->where("reward_rank_min <=", $rank) 
                ->where("reward_rank_max >=", $rank) 
                ->get();
        
        return $query->result_array();
    }
    
    public function update_exchange_history_top($id, $data_send, $data_result){
        $this->db
                ->set("data_send", $data_send)
                ->set("data_result", $data_result)
                ->where("id", $id);

        $this->db->update("event_toppk_exchange_history_top");
        return $this->db->affected_rows();
	}
    
    public function get_exchange_history_new_top($tournament_id, $char_id, $server_id, $mobo_service_id)
    {       
    	$query = "SELECT eteh.id, eteh.exchange_date, etr.reward_name, etr.reward_point";
        $query .= " FROM event_toppk_exchange_history_top eteh";
        $query .= " LEFT JOIN event_toppk_reward_top etr ON (eteh.reward_id = etr.id)";       
        $query .= " WHERE eteh.char_id =  " . $char_id . " AND eteh.server_id =  " . $server_id . " AND eteh.mobo_service_id = " . $mobo_service_id . " AND eteh.tournament_id = " . $tournament_id;      
        $query .= " ORDER BY eteh.exchange_date DESC";
        
    	$result=$this->db->query($query);      
        return $result->result_array(); 
    }
    
    //Reward Premiership
    public function check_exist_exchange_gift_premiership($tournament_id, $server_id, $char_id)
    {       
    	$query = $this->db->select("*")
                ->from("event_toppk_exchange_history_premiership") 
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
    
    public function get_reward_details_premiership($tournament_id)
    {       
    	$query = $this->db->select("*")
                ->from("event_toppk_reward_premiership")
                ->where("tournament_id", $tournament_id)   
                ->where("reward_status", 1)
                ->get();
        
        return $query->result_array();
    }
    
    public function update_exchange_history_premiership($id, $data_send, $data_result){
        $this->db
                ->set("data_send", $data_send)
                ->set("data_result", $data_result)
                ->where("id", $id);

        $this->db->update("event_toppk_exchange_history_premiership");
        return $this->db->affected_rows();
	}
        
    //Get Top New
    public function Event_TopArena_GetList($server_id, $week_event){
        $this->freeDBResource($this->db_cache_mgh2->conn_id);
        $sql = "CALL Event_TopArena_GetList('$server_id', $week_event)"; 
        $query = $this->db_cache_mgh2->query($sql);
        
        return $query->result_array();
    }
    
    public function Event_TopBattlePoint_GetList($server_id, $week_event){
        $this->freeDBResource($this->db_cache_mgh2->conn_id);
        $sql = "CALL Event_TopBattlePoint_GetList('$server_id', 1)";       
        $query = $this->db_cache_mgh2->query($sql);
        
        return $query->result_array();
    } 
    
    //Get Top Percent
    
    
    ///////////////////////////////////////// 
    function freeDBResource($dbh){
        while(mysqli_next_result($dbh)){
            if($l_result = mysqli_store_result($dbh)){
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
        $idinsert =  $this->db->insert_id();
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
    
    function query_history($char_id,$server) {
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