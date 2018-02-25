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
                ->order_by("tournament_date_start", "desc")
                ->limit(1)
                ->get();

        return $query->result_array();
    }

    public function tournament_list() {
        $query = $this->db->select("*")
                ->from("event_tulinhdan_tournament")
                ->where("tournament_status", 1)
                ->order_by("id", "desc")
                ->limit(1)
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

    public function check_exist_exchange_gift($tournament_id, $server_id, $mobo_service_id) {
        $query = $this->db->select("*")
                ->from("event_tulinhdan_exchange_history")
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
    
    public function check_exist_exchange_gift_vip($tournament_id, $server_id, $mobo_service_id) {
        $query = $this->db->select("*")
                ->from("event_tulinhdan_exchange_history")
                ->where("tournament_id", $tournament_id)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("reward_vip", 1)
                ->get();

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    public function get_reward_list_all($tournament_id) {
        $query = $this->db->select("*")
                ->from("event_tulinhdan_reward")
                ->where("tournament_id", $tournament_id)
                ->where("reward_status", 1)               
                ->order_by("reward_point", "desc")
                ->get();

        return $query->result_array();
    }

    public function get_reward_list($tournament_id) {
        $query = $this->db->select("*")
                ->from("event_tulinhdan_reward")
                ->where("tournament_id", $tournament_id)
                ->where("reward_status", 1)
                ->where("reward_vip_count", 0)
                ->order_by("reward_point", "desc")
                ->get();

        return $query->result_array();
    }

    public function get_reward_list_vip($tournament_id) {
        $query = $this->db->select("*")
                ->from("event_tulinhdan_reward")
                ->where("tournament_id", $tournament_id)
                ->where("reward_status", 1)
                ->where("reward_vip_count >", 0)
                ->order_by("reward_point", "desc")
                ->get();

        return $query->result_array();
    }

    public function get_reward_details($id) {
        $query = $this->db->select("*")
                ->from("event_tulinhdan_reward")
                ->where("id", $id)
                ->get();

        return $query->result_array();
    }

    function add_point($char_id, $server_id, $mobo_service_id, $pet_point) {
        $this->db
                ->set("user_point", "user_point + $pet_point", false)
                ->where("char_id", $char_id)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id);

        $this->db->update("event_cacuoc_point");
        return $this->db->affected_rows();
    }

    public function get_exchange_history($char_id, $server_id, $mobo_service_id) {
        $query = "SELECT eteh.id, eteh.exchange_date, etr.reward_name, etr.reward_point";
        $query .= " FROM event_tulinhdan_exchange_history eteh";
        $query .= " LEFT JOIN event_tulinhdan_reward etr ON (eteh.reward_id = etr.id)";
        $query .= " WHERE eteh.char_id =  '" . $char_id . "' AND eteh.server_id =  " . $server_id . " AND eteh.mobo_service_id = " . $mobo_service_id;
        $query .= " ORDER BY eteh.exchange_date DESC";

        $result = $this->db->query($query);

        //echo $this->db->last_query(); die;

        return $result->result_array();
    }

    public function get_exchange_history_new($tournament_id, $char_id, $server_id, $mobo_service_id) {
        $query = "SELECT eteh.id, eteh.exchange_date, etr.reward_name, etr.reward_point, eteh.tournament_money";
        $query .= " FROM event_tulinhdan_exchange_history eteh";
        $query .= " LEFT JOIN event_tulinhdan_reward etr ON (eteh.reward_id = etr.id)";
        $query .= " WHERE eteh.char_id =  " . $char_id . " AND eteh.server_id =  " . $server_id . " AND eteh.mobo_service_id = " . $mobo_service_id . " AND eteh.tournament_id = " . $tournament_id;
        $query .= " ORDER BY eteh.exchange_date DESC";

        //$query = "SELECT T12.id, T12.exchange_date, etr.reward_name, etr.reward_point";
        //$query .= " FROM (SELECT id, char_id, server_id, mobo_service_id, reward_id, tournament_id, exchange_date";
        //$query .= " FROM event_tulinhdan_exchange_history eteh1 UNION SELECT id, char_id, server_id, mobo_service_id, reward_id, tournament_id, exchange_date";
        //$query .= " FROM event_tulinhdan_exchange_history_top eteh2) T12";
        //$query .= " LEFT JOIN event_tulinhdan_reward etr ON ( T12.reward_id = etr.id ) ";
        //$query .= " WHERE T12.char_id = $char_id AND T12.server_id = $server_id AND T12.mobo_service_id = $mobo_service_id AND T12.tournament_id = $tournament_id";
        //$query .= " ORDER BY T12.exchange_date DESC";   

        $result = $this->db->query($query);
        return $result->result_array();
    }
    
     public function get_exchange_history_shop($server_id, $mobo_service_id) {
        $query = "SELECT eteh.id, eteh.exchange_gift_date, etr.gift_name, eteh.exchange_gift_point, eteh.exchange_gift_count";
        $query .= " FROM event_tulinhdan_shop_exchange_history eteh";
        $query .= " LEFT JOIN event_tulinhdan_shop etr ON (eteh.item_ex_id = etr.id)";
        $query .= " WHERE eteh.server_id =  " . $server_id . " AND eteh.mobo_service_id = " . $mobo_service_id;
        $query .= " ORDER BY eteh.exchange_gift_date DESC";

        $result = $this->db->query($query);
        return $result->result_array();
    }

    public function update_exchange_history($id, $data_send, $data_result, $reward_id) {
        $this->db
                ->set("data_send", $data_send)
                ->set("data_result", $data_result)
                ->set("reward_id", $reward_id)
                ->where("id", $id);

        $this->db->update("event_tulinhdan_exchange_history");
        return $this->db->affected_rows();
    }
    
    public function update_exchange_history_vip($id, $data_send, $data_result, $reward_id) {
        $this->db
                ->set("data_send", $data_send)
                ->set("data_result", $data_result)
                ->set("reward_id", $reward_id)
                ->set("reward_vip", 1)
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

    public function get_exchange_history_new_top($tournament_id, $char_id, $server_id, $mobo_service_id) {
        $query = "SELECT eteh.id, eteh.exchange_date, etr.reward_name, etr.reward_point";
        $query .= " FROM event_tulinhdan_exchange_history_top eteh";
        $query .= " LEFT JOIN event_tulinhdan_reward_top etr ON (eteh.reward_id = etr.id)";
        $query .= " WHERE eteh.char_id =  " . $char_id . " AND eteh.server_id =  " . $server_id . " AND eteh.mobo_service_id = " . $mobo_service_id . " AND eteh.tournament_id = " . $tournament_id;
        $query .= " ORDER BY eteh.exchange_date DESC";

        $result = $this->db->query($query);
        return $result->result_array();
    }
    
    //BXH TOP
    public function get_top($tournament_id) {
        $query = "SELECT `char_id`, `server_id`, `char_name`, `mobo_service_id`,`u_money`, `user_point`, `update_date`, `mobo_id`,@rank:=@rank+1 as `rank`
        FROM `event_tulinhdan_point` e, (Select @rank:=0) r
        WHERE `tournament_id` = $tournament_id
        order by  `u_money` DESC, `update_date` LIMIT 50 ";
        
        $result=$this->db->query($query);      
        return $result->result_array();
    }

    public function get_top_user($tournament_id, $server_id, $mobo_service_id) {
        $query = "SELECT * FROM (SELECT `char_id`, `server_id`, `char_name`, `mobo_service_id`, `u_money`, `user_point`, `update_date`, `tournament_id`, `mobo_id`,@rank:=@rank+1 as `rank` FROM `event_tulinhdan_point` e, (Select @rank:=0) r "
                . "WHERE `tournament_id` = $tournament_id order by `u_money` DESC, `update_date` LIMIT 50) as `top` "
                . "WHERE `top`.tournament_id = $tournament_id AND `top`.server_id = $server_id AND `top`.mobo_service_id = '$mobo_service_id' "
                . "ORDER BY `top`.rank DESC LIMIT 1";
        
        $result=$this->db->query($query);
        return $result->result_array(); 
    }
    
    //Shop
    function get_gift_list_by_type($gift_type) {
        $query = $this->db->select("*")
                ->from("event_tulinhdan_shop")
                ->where("gift_status", 1)
                ->where("gift_type", $gift_type)
                ->get();

        return $query->result_array();
    }
    
    function get_gift_details($id) {
        $query = $this->db->select("*")
                ->from("event_tulinhdan_shop")
                ->where("gift_status", 1)
                ->where("gift_type !=", 4)
                ->where("id", $id)
                ->get();

        return $query->result_array();
    }
    
    public function get_total_gift_exchange_shop_onlyone($server_id, $mobo_service_id, $gift_type) {
        $query = $this->db->select("SUM(exchange_gift_count) AS `TotalExchange`", false)
                ->from("event_tulinhdan_shop_exchange_history")
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("item_type_id", $gift_type)
                ->get();

        //echo $this->db->last_query(); die;
        return $query->result_array();
    }
    
    public function get_total_gift_exchange_shop($server_id, $mobo_service_id, $item_ex_id) {
        $query = $this->db->select("SUM(exchange_gift_count) AS `TotalExchange`", false)
                ->from("event_tulinhdan_shop_exchange_history")
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("item_ex_id", $item_ex_id)
                ->get();

        return $query->result_array();
    }
    
    function update_point_shop($pet_point, $server_id, $mobo_service_id, $tournament_id) {
        $this->db
                ->set("user_point", "user_point - $pet_point", false)               
                ->where("server_id", $server_id)                
                ->where("mobo_service_id", $mobo_service_id)
                ->where("tournament_id", $tournament_id);
        

        $this->db->update("event_tulinhdan_point");
        return $this->db->affected_rows();
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
            ->set("item_count", "item_count + $pet_point", false)
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
    
    public function update_shop_exchange_history($id, $data_send, $data_result, $exchange_gift_count) {
        $this->db
                ->set("data_send", $data_send)
                ->set("data_result", $data_result)
                ->set("exchange_gift_count", $exchange_gift_count)
                ->where("id", $id);

        $this->db->update("event_tulinhdan_shop_exchange_history");
        return $this->db->affected_rows();
    }

    //Point
    function user_check_point_exist($server_id, $mobo_service_id, $tournament_id) {
        $query = $this->db->select("*")
                ->from("event_tulinhdan_point")
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("tournament_id", $tournament_id)
                ->order_by("id", "desc")
                ->limit(1)
                ->get();

        //echo $query->last_query(); die;

        return $query->result_array();
    }

    function update_point($server_id, $mobo_service_id, $pet_point, $user_money, $tournament_id) {
        $this->db
                ->set("user_point", "user_point + $pet_point", false)
                ->set("u_money", "u_money + $user_money", false)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("tournament_id", $tournament_id);

        $this->db->update("event_tulinhdan_point");
        return $this->db->affected_rows();
    }

    function add_turn($server_id, $mobo_service_id, $pet_point, $tournament_id, $u_money) {
        $this->db
                ->set("user_point", "user_point + $pet_point", false)
                ->set("update_date", Date('Y-m-d H:i:s'))
                ->set("u_money", "u_money + $u_money", false)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("tournament_id", $tournament_id);

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

    function check_exchange_turn($mobo_service_id, $server_id, $tournament_id) {
        $date_start = date("Y-m-d 00:00:00", time());
        $date_end = date("Y-m-d 23:59:59", time());

        $query = $this->db->select("*")
                ->where("mobo_service_id", $mobo_service_id)
                ->where('server_id', $server_id)
                ->where('tournament_id', $tournament_id)
                ->where("create_date <= ", $date_end)
                ->where("create_date >= ", $date_start)
                ->get("event_tulinhdan_exchange_turn_money");
        $r = $query->row_array();
        if (count($r) > 0)
            return true;
        else {
            return false;
        }
    }

    function update_exchange_turn($mobo_service_id, $server_id, $turn, $money, $tournament_id) {
        $date_start = date("Y-m-d 00:00:00", time());
        $date_end = date("Y-m-d 23:59:59", time());

        $this->db
                ->set("user_point", "user_point + $turn", false)
                ->set("money", "money + $money", false)
                ->where("server_id", $server_id)
                ->where('tournament_id', $tournament_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("create_date <= ", $date_end)
                ->where("create_date >= ", $date_start);
        $this->db->update("event_tulinhdan_exchange_turn_money");
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

}

?>