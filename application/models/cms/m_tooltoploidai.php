<?php
require_once APPPATH."/core/Backend_Model.php";
class m_tooltoploidai extends Backend_Model{
    function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);
    } 
    
    function add_tournament($params){
        if(empty($params)){
            return 0;
        }
        
        $this->db->insert('event_toploidai_tournament',$params);        
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
        
        $this->db->update("event_toploidai_tournament");      
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
            ->set('week_no',$params['week_no'])
            ->set('reward_percent',$params['reward_percent'])    
            ->where("id", $params['id']);
        
        $this->db->update("event_toploidai_tournament");      
        return $this->db->affected_rows();
    }
    
    function tournament_list(){
        $this->datatables_config=array(
                "table"=>"event_toploidai_tournament",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_toploidai_tournament",               
                "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
    
    function tournament_get_by_id($id){
        $db = $this->db
        ->select('*')
        ->from('event_toploidai_tournament')
        ->where('id', $id)
        ->get();
        
        return $db->result();
    }
    
    function tournament_list_name_id(){
        $db = $this->db
        ->select('id, tournament_name', false)
        ->from('event_toploidai_tournament')
        ->order_by("id", "DESC")
        ->get();
        
        return $db->result();
    }
    
    //Reward
    function add_reward($params){
        if(empty($params)){
            return 0;
        }
        
        $this->db->insert('event_toploidai_reward',$params);        
        return $this->db->insert_id();
    }
    
    function load_reward($tournament_id){
        $db = $this->db
        ->select('id, reward_name', false)
        ->from('event_toploidai_reward')
        ->where('tournament_id', $tournament_id)
        ->order_by("id", "DESC")
        ->get();
        
        return $db->result();
    }
    
    function load_reward_details($id) {
        $db = $this->db
       ->select('*')
       ->from('event_toploidai_reward')
       ->where('id', $id)
       ->get();
        
        return $db->result();
    }
    
    function edit_reward_details($params){
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
        
        $this->db->update("event_toploidai_reward");    
        
        //echo  $this->db->last_query(); die;
        
        return $this->db->affected_rows();
    }
    
    function edit_reward_name($params){
        if(empty($params) || empty($params['id']) ){
            return 0;
        }
        
        $this->db       
            ->set('reward_name',$params['reward_name'])          
            ->where("id", $params['id']);
        
        $this->db->update("event_toploidai_reward");      
        return $this->db->affected_rows();
    }
    
    //Reward Top
    function add_reward_top($params){
        if(empty($params)){
            return 0;
        }
        
        $this->db->insert('event_toploidai_reward_top',$params);        
        return $this->db->insert_id();
    }
    
    function load_reward_top($tournament_id){
        $db = $this->db
        ->select('id, reward_name', false)
        ->from('event_toploidai_reward_top')
        ->where('tournament_id', $tournament_id)
        ->order_by("id", "DESC")
        ->get();
        
        return $db->result();
    }
    
    function load_reward_details_top($id) {
        $db = $this->db
       ->select('*')
       ->from('event_toploidai_reward_top')
       ->where('id', $id)
       ->get();
        
        return $db->result();
    }
    
    function edit_reward_details_top($params){
        if(empty($params) || empty($params['id']) ){
            return 0;
        }
        
        $this->db       
            ->set('reward_point',$params['reward_point'])
            ->set('reward_percent',$params['reward_percent'])
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
        
        $this->db->update("event_toploidai_reward_top");    
        
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
        
        $this->db->update("event_toploidai_reward_top");      
        return $this->db->affected_rows();
    }
    
    //Reward Premiership
    function add_reward_premiership($params){
        if(empty($params)){
            return 0;
        }
        
        $this->db->insert('event_toploidai_reward_premiership',$params);        
        return $this->db->insert_id();
    }
    
    function load_reward_premiership($tournament_id){
        $db = $this->db
        ->select('id, reward_name', false)
        ->from('event_toploidai_reward_premiership')
        ->where('tournament_id', $tournament_id)
        ->order_by("id", "DESC")
        ->get();
        
        return $db->result();
    }
    
    function load_reward_details_premiership($id) {
        $db = $this->db
       ->select('*')
       ->from('event_toploidai_reward_premiership')
       ->where('id', $id)
       ->get();
        
        return $db->result();
    }
    
    function edit_reward_details_premiership($params){
        if(empty($params) || empty($params['id']) ){
            return 0;
        }
        
        $this->db       
            ->set('reward_point',$params['reward_point'])
            ->set('reward_percent',$params['reward_percent'])
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
        
        $this->db->update("event_toploidai_reward_premiership");    
        
        //echo  $this->db->last_query(); die;
        
        return $this->db->affected_rows();
    }
    
    function edit_reward_name_premiership($params){
        if(empty($params) || empty($params['id']) ){
            return 0;
        }
        
        $this->db       
            ->set('reward_name',$params['reward_name']) 
            ->set('reward_rank_min',$params['reward_rank_min'])  
            ->set('reward_rank_max',$params['reward_rank_max'])  
            ->where("id", $params['id']);
        
        $this->db->update("event_toploidai_reward_premiership");      
        return $this->db->affected_rows();
    }
    
    //History
    function get_exchange_history($tournament_id, $startdate, $enddate){
        $where = "WHERE eteh.tournament_id = $tournament_id AND eteh.exchange_date >= '$startdate' AND eteh.exchange_date <= '$enddate'";
        $this->datatables_config=array(
                "table"=>"event_toploidai_exchange_history eteh",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS eteh.id, eteh.char_id, eteh.server_id, eteh.char_name, eteh.mobo_service_id, eteh.reward_id, eteh.exchange_date, etr.reward_name
                    FROM event_toploidai_exchange_history eteh LEFT JOIN event_toploidai_reward etr ON (eteh.reward_id = etr.id)",               
                "where"     =>$where,
                    "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
    
    function get_exchange_history_top($tournament_id, $startdate, $enddate){
        $where = "WHERE eteh.tournament_id = $tournament_id AND eteh.ex_date >= '$startdate' AND eteh.ex_date <= '$enddate'";
        $this->datatables_config=array(
                "table"=>"event_toploidai_add_nganluong_history eteh",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS eteh.id, eteh.char_id, eteh.server_id, eteh.char_name, eteh.mobo_service_id, eteh.reward_top_id, eteh.ex_date, etr.reward_name, eteh.ex_value
                    FROM event_toploidai_add_nganluong_history eteh LEFT JOIN event_toploidai_reward_top etr ON (eteh.reward_top_id = etr.id)",               
                "where"     =>$where,
                    "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
    
    function get_exchange_history_premiership($tournament_id, $startdate, $enddate){
        $where = "WHERE eteh.tournament_id = $tournament_id AND eteh.exchange_date >= '$startdate' AND eteh.exchange_date <= '$enddate'";
        $this->datatables_config=array(
                "table"=>"event_toploidai_exchange_history_premiership eteh",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS eteh.id, eteh.char_id, eteh.server_id, eteh.char_name, eteh.mobo_service_id, eteh.reward_id, eteh.exchange_date, etr.reward_name
                    FROM event_toploidai_exchange_history_premiership eteh LEFT JOIN event_toploidai_reward_premiership etr ON (eteh.reward_id = etr.id)",               
                "where"     =>$where,
                    "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
}
?>
