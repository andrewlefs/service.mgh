<?php

class m_tulinhdan extends CI_Model {

    private $db_cache, $db;

    function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Ho_Chi_Minh");

//        if (empty($this->db_cache))
//            $this->db_cache = $this->load->database(array('db' => 'db_cache', 'type' => 'slave'), true);

        if (empty($this->db))
            $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);
    }

    public function get_tournament() {
        $query = $this->db->select("*")
                ->from("event_tulinhdan_tournament")
                ->where("tournament_status", 1)
                ->order_by("tournament_status", "desc")
                ->order_by("tournament_date_start", "asc")
                ->limit(1)
                ->get();

        return $query->result_array();
    }

    public function tournament_list() {
        $query = $this->db->select("*")
                ->from("event_tulinhdan_tournament")
                ->where("tournament_status", 1)
                ->order_by("id", "desc")
                //->order_by("tournament_date_start", "asc")               
                ->get();

        return $query->result_array();
    }

    public function get_tournament_details($id) {
        $query = $this->db->select("*")
                ->from("event_tulinhdan_tournament")
                ->where("tournament_status", 1)
                ->where("id", $id)
                ->get();

        return $query->result_array();
    }

    public function get_exchange_history($server_id, $mobo_service_id) {
        $query = "SELECT eteh.id, eteh.exchange_date, etr.gift_name";
        $query .= " FROM event_tulinhdan_exchange_history eteh";
        $query .= " LEFT JOIN event_tulinhdan_gift etr ON (eteh.reward_id = etr.id)";
        $query .= " WHERE eteh.server_id =  " . $server_id . " AND eteh.mobo_service_id = " . $mobo_service_id;
        $query .= " ORDER BY eteh.exchange_date DESC";

        $result = $this->db->query($query);
        return $result->result_array();
    }

    public function get_top($tournament_id) {
        $query = "SELECT `char_id`, `server_id`, `char_name`, `mobo_service_id`, `user_point`, `update_date`, `mobo_id`,@rank:=@rank+1 as `rank`
        FROM `event_tulinhdan_point` e, (Select @rank:=0) r
        WHERE `tournament_id` = $tournament_id
        order by  `user_point` DESC, `update_date` LIMIT 50 ";
        
        $result=$this->db->query($query);      
        return $result->result_array();        
        
//        $query = $this->db
//                ->select('s1.id, s1.char_id, s1.char_name, s1.server_id, s1.user_point,
//              (SELECT COUNT(*) FROM event_tulinhdan_point AS s2 WHERE s2.user_point > s1.user_point AND s2.tournament_id = '.$tournament_id.') + 1 AS `rank`', FALSE)
//                ->from('event_tulinhdan_point s1')
//                ->where("tournament_id", $tournament_id)
//                ->order_by("user_point", "DESC")
//                ->get();
//        echo $this->db->last_query(); die;
//        return $query->result_array();
    }

    public function get_top_user($tournament_id, $server_id, $mobo_service_id) {
        $query = "SELECT * FROM (SELECT `char_id`, `server_id`, `char_name`, `mobo_service_id`, `user_point`, `update_date`, `tournament_id`, `mobo_id`,@rank:=@rank+1 as `rank` FROM `event_tulinhdan_point` e, (Select @rank:=0) r "
                . "WHERE `tournament_id` = $tournament_id order by `user_point` DESC, `update_date` LIMIT 50) as `top` "
                . "WHERE `top`.tournament_id = $tournament_id AND `top`.server_id = $server_id AND `top`.mobo_service_id = '$mobo_service_id' "
                . "ORDER BY `top`.rank DESC LIMIT 1";
        
        $result=$this->db->query($query);  
        //echo $this->db->last_query(); die;
        return $result->result_array(); 
        
        
//        $query = $this->db
//                ->select('s1.id, s1.char_id, s1.char_name, s1.server_id, s1.user_point,
//              FIND_IN_SET(user_point, (SELECT GROUP_CONCAT(user_point ORDER BY user_point DESC ) FROM event_tulinhdan_point WHERE tournament_id = '.$tournament_id.')) AS `rank`', FALSE)
//                ->from('event_tulinhdan_point s1')
//                ->where("tournament_id", $tournament_id)
//                ->where("server_id", $server_id)
//                ->where("mobo_service_id", $mobo_service_id)                
//                ->get();
//        echo $this->db->last_query(); die;
//        return $query->result_array();
    }

    public function update_exchange_history_log($id,  $data_send,$data_link) {
        $this->db
            ->set("data_send", $data_send)
            ->set("data_link", $data_link)
            ->where("id", $id);

        $this->db->update("event_tulinhdan_exchange_history");
        return $this->db->affected_rows();
    }
    public function update_exchange_history($id, $data_send, $data_result, $reward_id, $tournament_id) {
        $this->db
                ->set("data_send", $data_send)
                ->set("data_result", $data_result)
                ->set("reward_id", $reward_id)
                ->set("tournament_id", $tournament_id)
                ->where("id", $id);

        $this->db->update("event_tulinhdan_exchange_history");
        return $this->db->affected_rows();
    }

    //Reward Top
    public function check_exist_exchange_gift_top($tournament_id, $server_id, $mobo_service_id) {
        $query = $this->db->select("*")
                ->from("event_tulinhdan_exchange_history_top")
                ->where("tournament_id", $tournament_id)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->get();

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function get_reward_details_top($id) {
        $query = $this->db->select("*")
                ->from("event_tulinhdan_reward_top")
                ->where("id", $id)
                ->get();

        return $query->result_array();
    }

    public function check_rank_valid($rank, $tournament_id) {
        $query = $this->db->select("*")
                ->from("event_tulinhdan_reward_top")
                ->where("tournament_id", $tournament_id)
                ->where("reward_rank_min <=", $rank)
                ->where("reward_rank_max >=", $rank)
                ->get();

        return $query->result_array();
    }

    public function update_exchange_history_top($id, $data_send, $data_result) {
        $this->db
                ->set("data_send", $data_send)
                ->set("data_result", $data_result)
                ->where("id", $id);

        $this->db->update("event_tulinhdan_exchange_history_top");
        return $this->db->affected_rows();
    }
    
    public function get_exchange_history_new($tournament_id, $server_id, $mobo_service_id)
    {       
        $query = "SELECT eteh.id, eteh.exchange_date, etr.gift_name, eteh.exchange_point";
        $query .= " FROM event_tulinhdan_exchange_history eteh";
        $query .= " LEFT JOIN event_tulinhdan_gift etr ON (eteh.reward_id = etr.id)";       
        $query .= " WHERE eteh.server_id =  " . $server_id . " AND eteh.mobo_service_id = " . $mobo_service_id . " AND eteh.tournament_id = " . $tournament_id;      
        $query .= " ORDER BY eteh.exchange_date DESC";
        
    	$result=$this->db->query($query);      
        return $result->result_array(); 
    }

    public function get_exchange_history_new_top($tournament_id, $server_id, $mobo_service_id) {
        $query = "SELECT eteh.id, eteh.exchange_date, etr.reward_name, etr.reward_point";
        $query .= " FROM event_tulinhdan_exchange_history_top eteh";
        $query .= " LEFT JOIN event_tulinhdan_reward_top etr ON (eteh.reward_id = etr.id)";
        $query .= " WHERE eteh.server_id =  " . $server_id . " AND eteh.mobo_service_id = " . $mobo_service_id . " AND eteh.tournament_id = " . $tournament_id;
        $query .= " ORDER BY eteh.exchange_date DESC";

        $result = $this->db->query($query);
        return $result->result_array();
    }

    //Point
    function user_check_point_exist($mobo_service_id, $tournament_id) {
        $query = $this->db->select("*")
                ->from("event_tulinhdan_point")
                //->where("char_id", $char_id)
                ->where("tournament_id", $tournament_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->get();

        return $query->result_array();
    }

    function update_point($mobo_service_id, $pet_point, $tournament_id) {
        $this->db
                ->set("user_point", "user_point + $pet_point", false)
                ->set("update_date", date("y-m-d H:i:s", time()))
                //->where("char_id", $char_id)
                ->where("tournament_id", $tournament_id)
                ->where("mobo_service_id", $mobo_service_id);

        $this->db->update("event_tulinhdan_point");
        return $this->db->affected_rows();
    }

    public function update_tulinhdan_point_moboid($id, $mobo_id) {
        $this->db
                ->set("mobo_id", $mobo_id)
                ->where("id", $id);

        $this->db->update("event_tulinhdan_point");
        return $this->db->affected_rows();
    }

    //Gift
    public function get_gif_list($tournament_id) {
        $query = $this->db->select("*")
                ->from("event_tulinhdan_gift")
                ->where("tournament_id", $tournament_id)
                ->where("gift_status", 1)
                ->order_by("id", "RANDOM")
                ->get();

        return $query->result_array();
    }

    //NganLuong
    function user_check_nl_exist($mobo_service_id) {
        $query = $this->db->select("*")
                ->from("event_dautruong_point")
                ->where("mobo_service_id", $mobo_service_id)
                ->get();

        return $query->result_array();
    }

    function update_nl($mobo_service_id, $pet_point) {
        $this->db
                ->set("user_point", "user_point - $pet_point", false)
                ->where("mobo_service_id", $mobo_service_id);

        $this->db->update("event_dautruong_point");
        return $this->db->affected_rows();
    }

    function add_nl($mobo_service_id, $pet_point) {
        $this->db
                ->set("user_point", "user_point + $pet_point", false)
                ->set("update_date", Date('Y-m-d H:i:s'))
                ->where("mobo_service_id", $mobo_service_id);

        $this->db->update("event_dautruong_point");
        return $this->db->affected_rows();
    }

    public function update_dautruong_nl_moboid($id, $mobo_id) {
        $this->db
                ->set("mobo_id", $mobo_id)
                ->where("id", $id);

        $this->db->update("event_dautruong_point");
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
    public function get_nohu() {
        $query = $this->db->select("*")
            ->from("event_tulinhdan_nohu")
            ->where("id",1)
            ->get();

        return $query->row_array();
    }
    function update_nohu($pet_point) {
        $this->db
            ->set("item_count", "`item_count` + $pet_point", false)
            ->where("id", 1);


        $this->db->update("event_tulinhdan_nohu");
        return $this->db->affected_rows();
    }
    function update_nohu_reset() {
        $this->db
            ->set("item_count", 0)
            ->where("id", 1);


        $this->db->update("event_tulinhdan_nohu");
        return $this->db->affected_rows();
    }

}

?>