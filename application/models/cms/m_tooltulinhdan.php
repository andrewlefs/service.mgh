<?php
require_once APPPATH."/core/Backend_Model.php";
class m_tooltulinhdan extends Backend_Model{
    function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);
    } 
    
    function add_tournament($params){
        if(empty($params)){
            return 0;
        }
        
        $this->db->insert('event_tulinhdan_tournament',$params);        
        return $this->db->affected_rows();
    }
    
    function edit_tournament($params){
        if(empty($params) || empty($params['id']) ){
            return 0;
        }
        
        $this->db
        ->set('tournament_name',$params['tournament_name'])
        ->set('tournament_date_start',$params['tournament_date_start'])
        ->set('tournament_date_end',$params['tournament_date_end'])
        ->set('tournament_status',$params['tournament_status'])
        ->where("id", $params['id']);
        
        $this->db->update("event_tulinhdan_tournament");      
        return $this->db->affected_rows();
    }
    
    function edit_tournament_details($params){
        if(empty($params) || empty($params['id']) ){
            return 0;
        }
        
        $this->db       
            ->set('tournament_date_start',$params['tournament_date_start'])
            ->set('tournament_date_end',$params['tournament_date_end'])
            ->set('tournament_date_start_reward',$params['tournament_date_start_reward'])
            ->set('tournament_date_end_reward',$params['tournament_date_end_reward'])
            ->set('tournament_server_list',$params['tournament_server_list'])
            ->set('tournament_ip_list',$params['tournament_ip_list'])
            ->set('tournament_status',$params['tournament_status'])
            ->set('tournament_point',$params['tournament_point'])
            ->where("id", $params['id']);
        
        $this->db->update("event_tulinhdan_tournament");      
        return $this->db->affected_rows();
    }
    
    function tournament_list(){
        $this->datatables_config=array(
                "table"=>"event_tulinhdan_tournament",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_tulinhdan_tournament",               
                "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
    
    function tournament_get_by_id($id){
        $db = $this->db
        ->select('*')
        ->from('event_tulinhdan_tournament')
        ->where('id', $id)
        ->get();
        
        return $db->result();
    }
    
    function tournament_list_name_id(){
        $db = $this->db
        ->select('id, tournament_name', false)
        ->from('event_tulinhdan_tournament')
        ->order_by("id", "DESC")
        ->get();
        
        return $db->result();
    } 
    
    //Reward Top
    function add_reward_top($params){
        if(empty($params)){
            return 0;
        }
        
        $this->db->insert('event_tulinhdan_reward_top',$params);        
        return $this->db->insert_id();
    }
    
    function load_reward_top($tournament_id){
        $db = $this->db
        ->select('id, reward_name', false)
        ->from('event_tulinhdan_reward_top')
        ->where('tournament_id', $tournament_id)
        ->order_by("id", "DESC")
        ->get();
        
        return $db->result();
    }
    
    function load_reward_details_top($id) {
        $db = $this->db
       ->select('*')
       ->from('event_tulinhdan_reward_top')
       ->where('id', $id)
       ->get();
        
        return $db->result();
    }
    
    function load_reward_details_top_by_tournament($tournament_id) {
        $db = $this->db
       ->select('*')
       ->from('event_tulinhdan_reward_top')
       ->where('tournament_id', $tournament_id)
       ->get();
        
        return $db->result();
    }
    
    function edit_reward_details_top($params){
        if(empty($params) || empty($params['id']) ){
            return 0;
        }
        
        $this->db       
            ->set('reward_point',$params['reward_point'])
            ->set('reward_img',$params['reward_img'])
            ->set('reward_item1_code',$params['reward_item1_code'])
            ->set('reward_item1_number',$params['reward_item1_number'])            
            ->set('reward_item2_code',$params['reward_item2_code'])
            ->set('reward_item2_number',$params['reward_item2_number'])            
            ->set('reward_item3_code',$params['reward_item3_code'])
            ->set('reward_item3_number',$params['reward_item3_number'])                              
            ->set('reward_item4_code',$params['reward_item4_code'])
            ->set('reward_item4_number',$params['reward_item4_number'])                              
            ->set('reward_item5_code',$params['reward_item5_code'])
            ->set('reward_item5_number',$params['reward_item5_number'])
            ->set('reward_status',$params['reward_status'])
            ->where("id", $params['id']);
        
        $this->db->update("event_tulinhdan_reward_top");    
        
        //echo  $this->db->last_query(); die;
        
        return $this->db->affected_rows();
    }
    
    function edit_reward_name_top($params){
        if(empty($params) || empty($params['id']) ){
            return 0;
        }
        
        $this->db       
            ->set('reward_name',$params['reward_name']) 
            ->set('reward_rank_min',$params['reward_rank_min'])  
            ->set('reward_rank_max',$params['reward_rank_max'])  
            ->where("id", $params['id']);
        
        $this->db->update("event_tulinhdan_reward_top");      
        return $this->db->affected_rows();
    }
    
    //Gift
    function add_gift($params){
        if(empty($params)){
            return 0;
        }
        
        $this->db->insert('event_tulinhdan_gift',$params);        
        return $this->db->insert_id();
    }
    
    function gift_list($tournament_id){
        $where = "WHERE tournament_id = $tournament_id";
        $this->datatables_config=array(
                "table"=>"event_tulinhdan_gift",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_tulinhdan_gift",
                "where"     =>$where,
                "order_by"=>" Order By id DESC",                
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
    
    function load_gift_details($id) {
        $db = $this->db
       ->select('*')
       ->from('event_tulinhdan_gift')
       ->where('id', $id)
       ->get();
        
        return $db->result();
    }
    
    function edit_gift_details($params){
        if(empty($params) || empty($params['id']) ){
            return 0;
        }
        
        $this->db       
            ->set('item_id',$params['item_id'])
            ->set('gift_name',$params['gift_name'])
            ->set('gift_rate',$params['gift_rate'])
            ->set('gift_quantity',$params['gift_quantity'])            
            ->set('gift_img',$params['gift_img'])
            ->set('gift_status',$params['gift_status'])  
            ->set('tournament_id',$params['tournament_id'])
            ->where("id", $params['id']);
        
        $this->db->update("event_tulinhdan_gift");      
        return $this->db->affected_rows();
    }
    
    //History
    function get_exchange_history($tournament_id){
        $where = "WHERE eteh.tournament_id = $tournament_id";
        $this->datatables_config=array(
                "table"=>"event_tulinhdan_exchange_history eteh",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS eteh.id, eteh.char_id, eteh.server_id, eteh.char_name, eteh.mobo_service_id, eteh.reward_id, eteh.exchange_date, etr.gift_name
                    FROM event_tulinhdan_exchange_history eteh LEFT JOIN event_tulinhdan_gift etr ON (eteh.reward_id = etr.id)",               
                "where"     =>$where,
                    "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    } 
    
    function get_exchange_history_top($tournament_id){
        $where = "WHERE eteh.tournament_id = $tournament_id";
        $this->datatables_config=array(
                "table"=>"event_tulinhdan_exchange_history_top eteh",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS eteh.id, eteh.char_id, eteh.server_id, eteh.char_name, eteh.mobo_service_id, eteh.reward_id, eteh.exchange_date, etr.reward_name
                    FROM event_tulinhdan_exchange_history_top eteh LEFT JOIN event_tulinhdan_reward_top etr ON (eteh.reward_id = etr.id)",               
                "where"     =>$where,
                    "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
    
     function get_top_user_point($tournament_id)
    { 
        $where = "WHERE tournament_id = $tournament_id";
        $this->datatables_config=array(
                "table"=>"event_tulinhdan_point",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_tulinhdan_point",               
                "where"     =>$where,
                    "order_by"=>" Order By user_point DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata(); 
    }
    
    function get_total_point($tournament_id){
        $db = $this->db
        ->select('SUM(`user_point`) as "Total"', false)
        ->from('event_tulinhdan_point')     
        ->where("tournament_id", $tournament_id)       
        ->get();
        
        return $db->result_array();
    }
}
?>
