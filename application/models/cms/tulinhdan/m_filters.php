<?php
require_once APPPATH."/core/Backend_Model.php";
class m_filters extends Backend_Model{
    function __construct(){
        parent::__construct();
        $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);
    }
    function slbEvent(){
        $query = $this->db->select("*")
                ->from("event_tulinhdan_tournament")
                ->order_by('id DESC')
                ->get();
        if (is_object($query)) {
            return $query->result_array();
        }
        return array();
    }
    function listItems(){
        $where = "WHERE true";
        $this->datatables_config=array(
                "table"=>"event_tulinhdan_reward_top",
                "select"=>"SELECT f.*,e.tournament_name FROM event_tulinhdan_reward_top as f LEFT JOIN event_tulinhdan_tournament as e ON e.id=f.tournament_id",               
                "where"     =>$where,
                "order_by"=>" Order By f.id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
    function onInsertBox($arrParam){
        if(empty($arrParam)){
            return 0;
        }
        $data = $this->db->insert('event_tulinhdan_reward_top',$arrParam);
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
            ->set('tournament_id',$arrParam['tournament_id'])
            ->set('reward_name',$arrParam['reward_name'])
            ->set('reward_img',$arrParam['reward_img'])
            ->set('reward_rank_min',$arrParam['reward_rank_min'])
            ->set('reward_rank_max',$arrParam['reward_rank_max'])
            ->set('reward_item1_code',$arrParam['reward_item1_code'])
            ->set('reward_item1_number',$arrParam['reward_item1_number'])
            ->set('reward_item2_code',$arrParam['reward_item2_code'])
            ->set('reward_item2_number',$arrParam['reward_item2_number'])
            ->set('reward_item3_code',$arrParam['reward_item3_code'])
            ->set('reward_item3_number',$arrParam['reward_item3_number'])
            ->set('reward_item4_code',$arrParam['reward_item4_code'])
            ->set('reward_item4_number',$arrParam['reward_item4_number'])
            ->set('reward_item5_code',$arrParam['reward_item5_code'])
            ->set('reward_item5_number',$arrParam['reward_item5_number'])
            ->set('reward_point',$arrParam['reward_point'])
            ->set('reward_status',$arrParam['reward_status'])
            ->where("id", $arrParam['id']);
        $this->db->update("event_tulinhdan_reward_top");
        return $arrParam['id'];
    }
    function getItem($id){
        if(empty($id)){
            return 0;
        }
        $query = $this->db->select("*")
                ->from("event_tulinhdan_reward_top")               
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
        $db = $this->db->query("DELETE FROM event_tulinhdan_reward_top WHERE id=".$id);
        if($db){
            return $db->result();
        }
        return 0;
    }
}