<?php
require_once APPPATH."/core/Backend_Model.php";
class m_event extends Backend_Model{
    function __construct(){
        parent::__construct();
        $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);
    }
    function slbItem($tournament_id){
        $query = $this->db->select("*")
                ->from("event_tulinhdan_gift")
                ->where("tournament_id", $tournament_id)
                ->order_by('id ASC')
                ->get();
        if (is_object($query)) {
            return $query->result_array();
        }
        return array();
    }
    function deleteItem($tournament_id){
        $this->db->query("DELETE FROM event_tulinhdan_gift WHERE tournament_id=".$tournament_id);
    }
    function addItem($json_data,$tournament_id){
        $arrParam = json_decode($json_data,true);   
        if(count($arrParam)>0){
            foreach($arrParam as $k=>$v){
                $arrParam[$k]['tournament_id'] = $tournament_id;
                $arrParam[$k]['gift_insert_date'] = date('Y-m-d H:i:s');
            }
            foreach($arrParam as $key=>$val){
                $this->db->insert('event_tulinhdan_gift',$val);
            }
        }
    }
    function listItems(){
        $where = "WHERE true";
        $this->datatables_config=array(
                "table"=>"event_tulinhdan_tournament",
                "select"=>"SELECT * FROM event_tulinhdan_tournament",  
                "where"     =>$where,
                "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
    function onInsertBox($arrParam){
        if(empty($arrParam)){
            return 0;
        }
        $this->db->insert('event_tulinhdan_tournament',$arrParam);
        $val =  $this->db->insert_id() ;
        if($val>0){
            return $val;
        }
        return 0;
    }
    function onUpdateBox($arrParam){
        if(empty($arrParam) || empty($arrParam['id'])){
            return 0;
        }
        $this->db
            ->set('tournament_name',$arrParam['tournament_name'])
            ->set('tournament_date_start',$arrParam['tournament_date_start'])
            ->set('tournament_date_end',$arrParam['tournament_date_end'])
            ->set('tournament_status',$arrParam['tournament_status'])
            ->set('tournament_server_list',$arrParam['tournament_server_list'])
            ->set('tournament_date_start_reward',$arrParam['tournament_date_start_reward'])
            ->set('tournament_date_end_reward',$arrParam['tournament_date_end_reward'])
            ->set('tournament_ip_list',$arrParam['tournament_ip_list'])
            ->set('tournament_point',$arrParam['tournament_point'])
            ->where("id", $arrParam['id']);
        $this->db->update("event_tulinhdan_tournament");      
        return $arrParam['id'];
    }
    function getItem($id){
        if(empty($id)){
            return 0;
        }
        $query = $this->db->select("*")
                ->from("event_tulinhdan_tournament")               
                ->where("id", $id)
                ->get();
        if (is_object($query)) {
            return $query->row_array();
        }
        return array();
    }
    function delete($id = null){
        if(empty($id)){
            return 0;
        } 
        $db = $this->db->query("DELETE FROM event_tulinhdan_tournament WHERE id=".$id);
        $this->db->query("DELETE FROM event_tulinhdan_gift WHERE tournament_id=".$id);
        if($db){
            return $db->result();
        }
        return 0;
    }
}