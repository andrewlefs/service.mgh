<?php

class m_keobuabao extends CI_Model {

    private $db_cache, $db, $db_cache_mgh2;

    function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Ho_Chi_Minh");

        if (empty($this->db))
            $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);
    }

    public function get_tournament() {
        $query = $this->db->select("*")
                ->from("event_keobuabao_tournament")
                ->where("tournament_status", 1)
                ->order_by("tournament_status", "desc")
                ->order_by("tournament_date_start", "asc")
                ->limit(1)
                ->get();

        return $query->result_array();
    }

    public function tournament_list() {
        $query = $this->db->select("*")
                ->from("event_keobuabao_tournament")
                ->where("tournament_status", 1)
                ->order_by("id", "desc")
                //->order_by("tournament_date_start", "asc")               
                ->get();

        return $query->result_array();
    }

    public function get_tournament_details($id) {
        $query = $this->db->select("*")
                ->from("event_keobuabao_tournament")
                ->where("tournament_status", 1)
                ->where("id", $id)
                ->get();

        return $query->result_array();
    }

    /////////////////////

    public function get_moccuoc_group($tournament_id) {
        $query = $this->db->select("*")
                ->from("event_keobuabao_moccuoc_group")
                ->where("tournament_id", $tournament_id)
                ->where("status", 1)
                ->get();

        return $query->result_array();
    }

    public function get_moccuoc_group_by_id($id) {
        $query = $this->db->select("*")
                ->from("event_keobuabao_moccuoc_group")
                ->where("id", $id)
                ->where("status", 1)
                ->get();

        return $query->result_array();
    }

    public function update_join_status_history($id, $join_status) {
        $this->db
                ->set("join_status", $join_status)
                ->where("id", $id);

        $this->db->update("event_keobuabao_join_history");
        return $this->db->affected_rows();
    }

    public function get_join_history_by_moccuoc_group($moccuoc_group_id, $mobo_service_id) {
        $date_now = date('Y-m-d H:i:s');
        $query = $this->db->select("*")
                ->from("event_keobuabao_join_history")
                ->where("moccuoc_group_id", $moccuoc_group_id)
                ->where("mobo_service_id !=", $mobo_service_id)
                ->where("play_date_end >=", $date_now)
                ->where("join_status", 0)
                ->order_by("play_date_end", "asc")
                ->get();
        //echo $this->db->last_query(); die;
        return $query->result_array();
    }

    public function get_join_history_by_id($id) {
        $date_now = date('Y-m-d H:i:s');
        $query = $this->db->select("*")
                ->from("event_keobuabao_join_history")
                ->where("id", $id)
                ->where("play_date_end >=", $date_now)
                ->where("join_status", 0)
                ->get();
        return $query->result_array();
    }

    public function get_join_history_by_id_details($id) {
        $query = $this->db->select("*")
                ->from("event_keobuabao_join_history")
                ->where("id", $id)
                ->get();
        return $query->result_array();
    }

    public function get_play_history_by_join_id($join_id) {
        $query = $this->db->select("*")
                ->from("event_keobuabao_play_history")
                ->where("join_id", $join_id)
                ->get();
        return $query->result_array();
    }
    
    public function get_play_history_by_id_details($id) {
        $query = $this->db->select("*")
                ->from("event_keobuabao_play_history")
                ->where("id", $id)
                ->get();
        return $query->result_array();
    }

    public function get_join_history_expried_by_tournament_id($tournament_id) {
        $date_now = date('Y-m-d H:i:s');
        $query = $this->db->select("*")
                ->from("event_keobuabao_join_history")
                ->where("tournament_id", $tournament_id)
                ->where("play_date_end <=", $date_now)
                ->where("join_status", 0)
                ->get();
        return $query->result_array();
    }

    public function update_minus_point_play_status($id, $minus_point_status) {
        $this->db
                ->set("minus_point_status", $minus_point_status)
                ->where("id", $id);

        $this->db->update("event_keobuabao_play_history");
        return $this->db->affected_rows();
    }

    public function update_add_point_play_status($id, $add_point_status) {
        $this->db
                ->set("add_point_status", $add_point_status)
                ->where("id", $id);

        $this->db->update("event_keobuabao_play_history");
        return $this->db->affected_rows();
    }

    public function update_play_result($id, $play_status, $point_bonus, $type_choose_join) {
        $this->db
                ->set("play_status", $play_status)
                ->set("point_bonus", $point_bonus)
                ->set("type_choose_join", $type_choose_join)
                ->where("id", $id);

        $this->db->update("event_keobuabao_play_history");
        return $this->db->affected_rows();
    }

    //History   
    public function get_lichsu_join($tournament_id, $server_id, $mobo_service_id) {
        $query = $this->db->select("*")
                ->from("event_keobuabao_join_history")
                ->where("tournament_id", $tournament_id)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->order_by("id", "desc")
                ->get();
        return $query->result_array();
    }

    public function get_lichsu_play($tournament_id, $server_id, $mobo_service_id) {
        $query = $this->db->select("*")
                ->from("event_keobuabao_play_history")
                ->where("tournament_id", $tournament_id)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->order_by("id", "desc")
                ->get();
        return $query->result_array();
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