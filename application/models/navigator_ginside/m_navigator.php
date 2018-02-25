<?php
require_once APPPATH."/core/Backend_Model.php";
class m_navigator extends Backend_Model{
    function __construct(){
        parent::__construct();
        $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);
    }
    function listItems(){
        $query = $this->db->select("e.*")
                ->from("service_navigation_event as e")
                ->order_by("e.service_order ASC")
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
        $db = $this->db->query("DELETE FROM service_navigation_event WHERE id=".$id);
        if($db){
            return $db->result();
        }
        return 0;
    }
    public function update($arrParam, $where){
        $this->db->where('service_id', $where);
        $this->db->update('service_navigation_event', $arrParam);
        return $this->db->affected_rows();
    }
    public function updateOrder($arrParam,$id){
        $this->db->where('service_id',$id);
        $this->db->update('service_navigation_event', $arrParam);
    }
    public function insert($arrParam){
        $this->db->insert('service_navigation_event', $arrParam);
        $idinsert =  $this->db->insert_id();
        if ($idinsert> 0) {
            return $idinsert;
        } else {
            return false;
        }
    }
    public function status($arrParam){
        if($arrParam['type']=='multi'){
            if(count($arrParam['cid'])>0){
                $data= array('service_status'=>$arrParam['s']);
                $this->db->where_in('service_id', $arrParam['cid']);
                $this->db->update('service_navigation_event',$data);
            }
        }else{
            $status = ($arrParam['s']== 'true' )? 'false':'true';
            $data= array('service_status'=>$status);
            $this->db->where('service_id', $arrParam['id']);
            $this->db->update('service_navigation_event',$data);
        }
    }
    public function sortItem($arrParam){
        $countlist = count($arrParam['listid']);
        for ($i = 0; $i < $countlist ; $i ++){
            $data = array('service_order'=>$arrParam['listorder'][$i]);
            $this->db->where('service_id', $arrParam['listid'][$i]);
            $this->db->update('service_navigation_event',$data);
        }
    }
    public function titlelang($id,$lang){
        $query = $this->db->select("e.*")
                ->from("service_navigation_language as e")
                ->where('nav_id',$id)
                ->where('alias',$lang)
                ->get();
        if (is_object($query)) {
            return $query->row_array();
        }
        return 0;
    }
    function getItem($id){
        if(empty($id)){
            return 0;
        }
        $query = $this->db->select("*")
                ->from("service_navigation_event")               
                ->where("service_id", $id)
                ->get();
        if (is_object($query)) {
            return $query->row_array();
        }
        return 0;
    }
    public function deleteEventLang($id,$lang){
        $this->db->delete('service_navigation_language',array('nav_id' => $id,'alias'=>$lang)); 
    }
    public function insertEventLang($arrParam){
        $this->db->insert('service_navigation_language', $arrParam);
        $idinsert =  $this->db->insert_id();
        if ($this->db->affected_rows() > 0) {
            return $idinsert;
        } else {
            return false;
        }
    }
    public function deleteNavigationEventByID($id){
        $this->db->delete('service_navigation_event',array('service_id' => $id)); 
    }
    public function deleteNavigationEventByIDtLang($id){
        $this->db->delete('service_navigation_language',array('nav_id' => $id)); 
    }
}