<?php

require_once APPPATH . "/core/Backend_Model.php";

class m_toolduatoplevel extends Backend_Model {
    private $db;

    function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        if (empty($this->db))
            $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);
    }

    function add_tournament($params) {
        if (empty($params)) {
            return 0;
        }

        $this->db->insert('event_duatoplevel_tournament', $params);
        return $this->db->affected_rows();
    }

    function edit_tournament($params) {
        if (empty($params) || empty($params['id'])) {
            return 0;
        }

        $this->db
                ->set('tournament_name', $params['tournament_name'])
                ->set('tournament_date_start', $params['tournament_date_start'])
                ->set('tournament_date_end', $params['tournament_date_end'])
                ->set('tournament_date_start_reward', $params['tournament_date_start_reward'])
                ->set('tournament_date_end_reward', $params['tournament_date_end_reward'])
                ->set('tournament_status', $params['tournament_status'])
                ->where("id", $params['id']);

        $this->db->update("event_duatoplevel_tournament");
        return $this->db->affected_rows();
    }

    function edit_tournament_details($params) {
        if (empty($params) || empty($params['id'])) {
            return 0;
        }

        $this->db
                ->set('tournament_date_start', $params['tournament_date_start'])
                ->set('tournament_date_end', $params['tournament_date_end'])
                ->set('tournament_date_start_reward', $params['tournament_date_start_reward'])
                ->set('tournament_date_end_reward', $params['tournament_date_end_reward'])
                ->set('tournament_server_list', $params['tournament_server_list'])
                ->set('tournament_ip_list', $params['tournament_ip_list'])
                ->set('tournament_status', $params['tournament_status'])
                ->where("id", $params['id']);

        $this->db->update("event_duatoplevel_tournament");
        return $this->db->affected_rows();
    }

    function tournament_list() {
        $this->datatables_config = array(
            "table" => "event_duatoplevel_tournament",
            "select" => "
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_duatoplevel_tournament",
            "order_by" => " Order By id DESC",
            "columnmaps" => array(
            )
        );
        return $this->_bindingdata();
    }

    function tournament_get_by_id($id) {
        $db = $this->db
                ->select('*')
                ->from('event_duatoplevel_tournament')
                ->where('id', $id)
                ->get();

        return $db->result();
    }

    function tournament_list_name_id() {
        $db = $this->db
                ->select('id, tournament_name', false)
                ->from('event_duatoplevel_tournament')
                ->order_by("id", "DESC")
                ->get();

        return $db->result();
    }

    function get_duatoplevel_gift($tournament_id) {
        $where = "WHERE tournament_id = $tournament_id";
        $this->datatables_config = array(
            "table" => "event_duatoplevel_gift",
            "select" => "
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_duatoplevel_gift",
            "where" => $where,
            "order_by" => " Order By id DESC",
            "columnmaps" => array(
            )
        );
        return $this->_bindingdata();
    }

    function add_duatoplevel_gift($params) {
        if (empty($params)) {
            return 0;
        }

        $this->db->insert('event_duatoplevel_gift', $params);
        return $this->db->affected_rows();
    }

    function get_duatoplevel_gift_details($id) {
        $db = $this->db
                ->select('*')
                ->from('event_duatoplevel_gift')
                ->where('id', $id)
                ->get();

        return $db->result();
    }

    function edit_duatoplevel_gift($params) {
        if (empty($params) || empty($params['id'])) {
            return 0;
        }

        $this->db
                ->set('tournament_id', $params['tournament_id'])
                ->set('item_name', $params['item_name'])
                ->set('item_id', $params['item_id'])
                ->set('item_quantity', $params['item_quantity'])
                ->set('rank_min', $params['rank_min'])
                ->set('rank_max', $params['rank_max'])
                ->set('item_img', $params['item_img'])
                ->where("id", $params['id']);

        $this->db->update("event_duatoplevel_gift");
        return $this->db->affected_rows();
    }

    //History
    function get_exchange_history($tournament_id, $startdate, $enddate) {
        $where = " WHERE tournament_id = $tournament_id AND exchange_date >= '$startdate' AND exchange_date <= '$enddate'";
        $this->datatables_config = array(
            "table" => "event_duatoplevel_history",
            "select" => "
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_duatoplevel_history",
            "where" => $where,
            "order_by" => " ORDER BY id DESC",
            "columnmaps" => array(
            )
        );
        return $this->_bindingdata();
    }
//
//    function get_exchange_history_excel($tournament_id, $startdate, $enddate) {
//        $query = "SELECT eteh.id, eteh.exchange_date, etr.pakage_name, etr.pakage_price, etr.vip_required, eteh.exchange_status";
//        $query .= " FROM event_duatoplevel_exchange_history eteh LEFT JOIN event_duatoplevel_gift_pakage etr ON (eteh.pakage_id = etr.id)";
//        $query .= " WHERE eteh.tournament_id = $tournament_id AND eteh.exchange_date >= '$startdate' AND eteh.exchange_date <= '$enddate'";
//        $query .= " ORDER BY eteh.exchange_date DESC";
//
//        $result = $this->db->query($query);
//        return $result->result_array();
//    }

}

?>
