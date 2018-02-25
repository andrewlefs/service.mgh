<?php
require_once APPPATH."/core/Backend_Model.php";
class m_history extends Backend_Model{
    function __construct(){
        parent::__construct();
        $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);
    }
    function history($arrParam){
        $where = "WHERE true and h.exchange_date>='".$arrParam['start']."' and h.exchange_date<='".$arrParam['end']."'";
        $this->datatables_config=array(
                "table"=>"event_tulinhdan_exchange_history",
                "select"=>"SELECT h.*,e.tournament_name,r.reward_name,DATE_FORMAT(h.exchange_date,'%d-%m-%Y %H:%i:%s') as exchange_date FROM event_tulinhdan_exchange_history as h LEFT JOIN event_tulinhdan_tournament as e ON e.id=h.tournament_id LEFT JOIN event_tulinhdan_reward_top as r ON r.id=h.reward_id", 
                "where"     =>$where,
                "order_by"=>" Order By h.id DESC",
                "columnmaps"=>array(
                )
        );        
        return $this->_bindingdata();
    }
    function exportExcel($arrParam){
        $query = $this->db->select("h.*,e.tournament_name,r.reward_name")
                ->from("event_tulinhdan_exchange_history as h")
                ->join('event_tulinhdan_tournament as e', 'e.id = h.tournament_id', 'left')
                ->join('event_tulinhdan_reward_top as r', 'r.id = h.reward_id', 'left')
                ->where("h.exchange_date >= ", $arrParam['start'])
                ->where("h.exchange_date <= ", $arrParam['end'])
                ->order_by('h.id DESC')
                ->get();
        if (is_object($query)) {
            return $query->result_array();
        }
        return 0;
    }
    function delete($id = null){
        if(empty($id)){
            return 0;
        } 
        $db = $this->db->query("DELETE FROM event_tulinhdan_exchange_history WHERE id=".$id);
        if($db){
            return $db->result();
        }
        return 0;
    }
}