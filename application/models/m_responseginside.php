<?php
class m_responseginside extends CI_Model {

    private $db;
	private $service_navigation_event = "service_navigation_event";
    private $service_navigation_language = "service_navigation_language";
    function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Ho_Chi_Minh");
		if (empty($this->db))
            $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'master'), true);
    }

    public function update($data, $where){
        $sql = $this->db->update($this->service_navigation_event, $data, $where);
		//echo $this->db->last_query();die;
        return $this->db->affected_rows();
    }

    public function updateTable($table,$data, $where){
        $sql = $this->db->update($table, $data, $where);
        return $this->db->affected_rows();
    }
    public function insert_id($data){

        $query = $this->db->insert($this->service_navigation_event, $data);
        $idinsert =  $this->db->insert_id();
        $arrayInsert = array();
        $service_language = json_decode($data['service_language'],true);
        foreach($service_language as $key=>$val){
            $arrayInsert[] = array('nav_id'=>$idinsert,'alias'=>$key,'title'=>'');
        }
        if($arrayInsert) {
            $queryLang = $this->db->insert_batch($this->service_navigation_language,$arrayInsert );
        }

        if ($this->db->affected_rows() > 0) {
            return $idinsert;
        } else {
            return false;
        }
    }
    public function getcategorybyid($service_id){
        $this->db->select('l.*,e.service_id,e.service_ishot,e.service_android,e.service_ios,e.service_wp,e.service_function,e.service_start,e.service_end,e.service_trustip,e.service_title,e.service_status,e.service_url');
        $this->db->from($this->service_navigation_event." as e");
        $this->db->join($this->service_navigation_language." as l"," l.nav_id  = e.service_id","left");
        $this->db->where('e.service_id', $service_id);
        $data = $this->db->get();
        if (is_object($data)) {
            return $data->result_array();
        }
        return false;
    }
    public function getcategoryall(){
        $data = $this->db->get($this->service_navigation_event);
        if (is_object($data)){
            $returnData = $data->result_array();
            return $returnData;
        }      
		return array();
    }
}
