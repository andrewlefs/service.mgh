<?php
require_once APPPATH."/core/Backend_Model.php";
class m_toolquanhapmong extends Backend_Model{
    function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);
    } 
    
    function add_tournament($params){
        if(empty($params)){
            return 0;
        }
        
        $this->db->insert('event_quanhapmong_tournament',$params);        
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
        ->set('tournament_server_list',$params['tournament_server_list'])
        ->set('tournament_status',$params['tournament_status'])
        ->where("id", $params['id']);
        
        $this->db->update("event_quanhapmong_tournament");      
        return $this->db->affected_rows();
    }
    
    function edit_tournament_details($params){
        if(empty($params) || empty($params['id']) ){
            return 0;
        }
        
        $this->db       
            ->set('tournament_date_start',$params['tournament_date_start'])
            ->set('tournament_date_end',$params['tournament_date_end'])          
            ->set('tournament_server_list',$params['tournament_server_list'])
            ->set('tournament_ip_list',$params['tournament_ip_list'])           
            ->set('tournament_status',$params['tournament_status'])         
            ->where("id", $params['id']);
        
        $this->db->update("event_quanhapmong_tournament");      
        return $this->db->affected_rows();
    }
    
    function tournament_list(){
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_tournament",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_quanhapmong_tournament",               
                "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
    
    function tournament_get_by_id($id){
        $db = $this->db
        ->select('*')
        ->from('event_quanhapmong_tournament')
        ->where('id', $id)
        ->get();
        
        return $db->result();
    }
    
    function tournament_list_name_id(){
        $db = $this->db
        ->select('id, tournament_name', false)
        ->from('event_quanhapmong_tournament')
        ->order_by("id", "DESC")
        ->get();
        
        return $db->result();
    }
    
    //Match
    function match_list($tournament_id){
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_match",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_quanhapmong_match",   
               "where"=>" where tournament_id = " . $tournament_id ,     
                "order_by"=>" Order By id DESC",                
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
    
    function add_match($params){
        if(empty($params)){
            return 0;
        }
        
        $this->db->insert('event_quanhapmong_match',$params);        
        return $this->db->insert_id();
    }
    
    function load_match_details($id) {
        $db = $this->db
       ->select('*')
       ->from('event_quanhapmong_match')
       ->where('id', $id)
       ->get();
        
        return $db->result();
    }
    
    function edit_match_details($params){
        if(empty($params) || empty($params['id']) ){
            return 0;
        }
        
        $this->db       
            ->set('tournament_id',$params['tournament_id'])
            ->set('match_team_name_a',$params['match_team_name_a'])
            ->set('match_team_img_a',$params['match_team_img_a'])
            ->set('match_team_chap_a',$params['match_team_chap_a'])            
            ->set('match_team_win_rate_a',$params['match_team_win_rate_a'])
            ->set('match_team_name_b',$params['match_team_name_b'])            
            ->set('match_team_img_b',$params['match_team_img_b'])
            ->set('match_team_chap_b',$params['match_team_chap_b'])                              
            ->set('match_team_win_rate_b',$params['match_team_win_rate_b'])
            ->set('match_start_date',$params['match_start_date'])                              
            ->set('match_end_date',$params['match_end_date'])
            ->set('match_end_pet_date',$params['match_end_pet_date'])
            ->set('match_status',$params['match_status'])
            ->set('match_pet_max',$params['match_pet_max'])
            ->where("id", $params['id']);
        
        $this->db->update("event_quanhapmong_match");      
        return $this->db->affected_rows();
    }
    
    function update_match_result($params){
        if(empty($params) || empty($params['id']) ){
            return 0;
        }
        
        $this->db       
            ->set('match_result_team_a',$params['match_result_team_a'])
            ->set('match_result_team_b',$params['match_result_team_b'])
            ->set('match_result_status', 1)
            ->where("id", $params['id']);
        
        $this->db->update("event_quanhapmong_match");      
        return $this->db->affected_rows();
    }
    
    //Gift
    function add_gift($params){
        if(empty($params)){
            return 0;
        }
        
        $this->db->insert('event_quanhapmong_gift',$params);        
        return $this->db->insert_id();
    }
    
    function add_gift_pakage($params){
        if(empty($params)){
            return 0;
        }
        
        $this->db->insert('event_quanhapmong_gift_pakage',$params);        
        return $this->db->insert_id();
    }
    
    function gift_list(){
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_gift",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_quanhapmong_gift",
            "where"=>" where gift_type = 0",       
            "order_by"=>" Order By id DESC",                
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
    
    function gift_list_by_type($id){
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_gift",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_quanhapmong_gift",
                "where"=>" where gift_type = " . $id ,   
                "order_by"=>" Order By id DESC",                
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
    
    function gift_list_pakage_by_type($id){
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_gift_pakage",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_quanhapmong_gift_pakage",
                "where"=>" where gift_type = " . $id ,   
                "order_by"=>" Order By id DESC",                
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
    
    function gift_type_list(){
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_gift_type",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_quanhapmong_gift_type",
                "where"=>" where type_package = 0" ,  
                "order_by"=>" Order By id ASC",                
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
    
    function gift_type_list_pakage(){
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_gift_type",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_quanhapmong_gift_type",
                "where"=>" where type_package = 1" ,  
                "order_by"=>" Order By id ASC",                
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
    
    function load_gift_details($id) {
        $db = $this->db
       ->select('*')
       ->from('event_quanhapmong_gift')
       ->where('id', $id)
       ->get();
        
        return $db->result();
    }
    
    function edit_gift($params){
        if(empty($params) || empty($params['id']) ){
            return 0;
        }
        
        $this->db       
            ->set('item_id',$params['item_id'])
            ->set('gift_name',$params['gift_name'])
            ->set('gift_price',$params['gift_price'])
            ->set('gift_quantity',$params['gift_quantity'])            
            ->set('gift_img',$params['gift_img'])
            ->set('gift_status',$params['gift_status'])  
            ->set('server_list',$params['server_list'])
            ->set('gift_type',$params['gift_type'])  
            ->set('gift_send_type',$params['gift_send_type'])
            ->set('gift_buy_max',$params['gift_buy_max'])
            ->where("id", $params['id']);
        
        $this->db->update("event_quanhapmong_gift");      
        return $this->db->affected_rows();
    }
    
    function load_gift_pakage_details($id) {
        $db = $this->db
       ->select('*')
       ->from('event_quanhapmong_gift_pakage')
       ->where('id', $id)
       ->get();
        
        return $db->result();
    }
    
    function edit_gift_pakage($params){       
        if(empty($params) || empty($params['id']) ){
            return 0;
        }
        
        $this->db 
            ->set('gift_name',$params['gift_name'])
            ->set('gift_price',$params['gift_price'])                      
            ->set('gift_img',$params['gift_img'])
            ->set('gift_status',$params['gift_status'])  
            ->set('server_list',$params['server_list'])
            ->set('gift_type',$params['gift_type'])  
            ->set('gift_buy_max',$params['gift_buy_max'])                
            ->set('gift_date_start',$params['gift_date_start'])  
            ->set('gift_date_end',$params['gift_date_end'])
            ->set('gift_vip_point',$params['gift_vip_point'])  
            ->set('gift_number_request',$params['gift_number_request'])
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
            ->where("id", $params['id']);
       
        $this->db->update("event_quanhapmong_gift_pakage");  
        
        return $this->db->affected_rows();
    }
    
    //Gift OutGame
    function add_gift_outgame($params){
        if(empty($params)){
            return 0;
        }
        
        $this->db->insert('event_quanhapmong_gift_outgame',$params);        
        return $this->db->insert_id();
    }
    
    function gift_list_outgame(){
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_gift_outgame",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_quanhapmong_gift_outgame",
                "order_by"=>" Order By id DESC",                
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
    
    function load_gift_details_outgame($id) {
        $db = $this->db
       ->select('*')
       ->from('event_quanhapmong_gift_outgame')
       ->where('id', $id)
       ->get();
        
        return $db->result();
    }
    
    function edit_gift_details_outgame($params){
        if(empty($params) || empty($params['id']) ){
            return 0;
        }
        
        $this->db       
            ->set('item_id',$params['item_id'])
            ->set('gift_name',$params['gift_name'])
            ->set('gift_price',$params['gift_price'])
            ->set('gift_quantity',$params['gift_quantity'])            
            ->set('gift_img',$params['gift_img'])
            ->set('gift_status',$params['gift_status'])  
            //->set('server_list',$params['server_list']) 
            ->where("id", $params['id']);
        
        $this->db->update("event_quanhapmong_gift_outgame");      
        return $this->db->affected_rows();
    }
    
    //Reward
    function add_reward($params){
        if(empty($params)){
            return 0;
        }
        
        $this->db->insert('event_quanhapmong_reward',$params);        
        return $this->db->insert_id();
    }
    
    function load_reward($tournament_id){
        $db = $this->db
        ->select('id, reward_name', false)
        ->from('event_quanhapmong_reward')
        ->where('tournament_id', $tournament_id)
        ->order_by("id", "DESC")
        ->get();
        
        return $db->result();
    }
    
    function load_reward_details($id) {
        $db = $this->db
       ->select('*')
       ->from('event_quanhapmong_reward')
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
            ->set('max_exchange',$params['max_exchange'])
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
            ->where("id", $params['id']);
        
        $this->db->update("event_quanhapmong_reward");      
        return $this->db->affected_rows();
    }
    
    //Reward Top
    function add_reward_top($params){
        if(empty($params)){
            return 0;
        }
        
        $this->db->insert('event_quanhapmong_reward_top',$params);        
        return $this->db->insert_id();
    }
    
    function load_reward_top($tournament_id){
        $db = $this->db
        ->select('id, reward_name', false)
        ->from('event_quanhapmong_reward_top')
        ->where('tournament_id', $tournament_id)
        ->order_by("id", "DESC")
        ->get();
        
        return $db->result();
    }
    
    function load_reward_details_top($id) {
        $db = $this->db
       ->select('*')
       ->from('event_quanhapmong_reward_top')
       ->where('id', $id)
       ->get();
        
        return $db->result();
    }
    
    function edit_reward_details_top($params){
        if(empty($params) || empty($params['id']) ){
            return 0;
        }
        
        $this->db       
            ->set('reward_rate',$params['reward_rate'])            
            ->set('reward_status',$params['reward_status'])
            ->where("id", $params['id']);
        
        $this->db->update("event_quanhapmong_reward_top");    
        
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
        
        $this->db->update("event_quanhapmong_reward_top");      
        return $this->db->affected_rows();
    }
    
    //Code
    function gen_code($params){
        if(empty($params)){
            return 0;
        }
        
        $this->db->insert('event_quanhapmong_code_store',$params);        
        return $this->db->affected_rows();
    }
    
    function code_list($type_code_id){
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_code_store",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_quanhapmong_code_store",
                    "where"=>" where type_code_id = " . $type_code_id ,   
                " order_by"=>" Order By id DESC",                
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
    
    function code_type(){
        $db = $this->db
        ->select('*')
        ->from('event_quanhapmong_code_type')     
        ->order_by("type_code", "ASC")
        ->get();
        
        return $db->result();
    }
    
    function add_code_type($params){
        if(empty($params)){
            return 0;
        }
        
        $this->db->insert('event_quanhapmong_code_type',$params);        
        return $this->db->insert_id();
    }
    
    function check_exist_code_type($type_code){
        $db = $this->db
        ->select('*')
        ->from('event_quanhapmong_code_type')  
        ->where("type_code", $type_code)      
        ->get();
        
        if ($db->num_rows() > 0) {            
            return true;
        } else {
            return false;
        }
    }
    
    function get_code_type_by_id($id){
        $db = $this->db
        ->select('*')
        ->from('event_quanhapmong_code_type')     
        ->where("id", $id) 
        ->get();
        
        return $db->row_array();
    }
    
    //History
    function get_pet_history_details()
    { 
        $where = "WHERE true";
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_pet_history ecph",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS ecph.id, ecph.char_id, ecph.server_id, ecph.char_name, ecph.mobo_service_id, ecph.pet_team_win, ecph.pet_point, ecm.match_team_name_a, ecm.match_team_name_b, ecph.pet_date, ecph.pet_status, ecph.pet_win_point
                    FROM event_quanhapmong_pet_history ecph INNER JOIN event_quanhapmong_match ecm ON ecph.match_id = ecm.id",               
                "where"     =>$where,
                    "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();  
    }
    
    function get_pet_history_details_by_tournament($tournament_id)
    { 
        $where = "WHERE ecph.match_id IN (SELECT id FROM `event_quanhapmong_match` WHERE `tournament_id` = $tournament_id)";
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_pet_history ecph",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS ecph.id, ecph.char_id, ecph.server_id, ecph.char_name, ecph.mobo_service_id, ecph.pet_team_win, ecph.pet_point, ecm.match_team_name_a, ecm.match_team_name_b, ecph.pet_date, ecph.pet_status, ecph.pet_win_point
                    FROM event_quanhapmong_pet_history ecph INNER JOIN event_quanhapmong_match ecm ON ecph.match_id = ecm.id",               
                "where"     =>$where,
                    "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();  
    }
    
    function get_point_exchange_history()
    { 
        $where = "WHERE true";
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_point_exchange_history",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_quanhapmong_point_exchange_history",               
                "where"     =>$where,
                    "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata(); 
    }
    
    function get_gift_exchange_history($startdate, $enddate)
    { 
        $where = "WHERE  exchange_gift_date >= '$startdate' AND exchange_gift_date <= '$enddate'";
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_gift_exchange_history ecge",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS ecge.id, ecge.char_id, ecge.server_id, ecge.char_name, ecge.mobo_service_id, ecge.exchange_gift_date, ecge.exchange_gift_point, ecg.gift_name, ecg.gift_img
                    FROM event_quanhapmong_gift_exchange_history ecge LEFT JOIN event_quanhapmong_gift ecg ON (ecge.item_ex_id = ecg.id)", 
                     "where"     =>$where,
                    "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();  
    }
    
    function get_gift_pakage_exchange_history($startdate, $enddate)
    { 
        $where = "WHERE  exchange_gift_date >= '$startdate' AND exchange_gift_date <= '$enddate'";
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_gift_pakage_exchange_history ecge",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS ecge.id, ecge.char_id, ecge.server_id, ecge.char_name, ecge.mobo_service_id, ecge.exchange_gift_date, ecge.exchange_gift_point, ecg.gift_name, ecg.gift_img
                    FROM event_quanhapmong_gift_pakage_exchange_history ecge LEFT JOIN event_quanhapmong_gift_pakage ecg ON (ecge.item_ex_id = ecg.id)", 
                     "where"     =>$where,
                    "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();  
    }
    
    function get_total_point_gift_exchange_history($startdate, $enddate){
        $db = $this->db
        ->select('SUM(`exchange_gift_point`) as "Total"', false)
        ->from('event_quanhapmong_gift_exchange_history')     
        ->where("exchange_gift_date <= ", $enddate)
        ->where("exchange_gift_date >= ", $startdate)
        ->get();
        
        return $db->result_array();
    }
    
    function get_total_point_gift_pakage_exchange_history($startdate, $enddate){
        $db = $this->db
        ->select('SUM(`exchange_gift_point`) as "Total"', false)
        ->from('event_quanhapmong_gift_pakage_exchange_history')     
        ->where("exchange_gift_date <= ", $enddate)
        ->where("exchange_gift_date >= ", $startdate)
        ->get();
        
        return $db->result_array();
    }
    
    function get_total_point_gift_exchange_tld_history($startdate, $enddate){
        $db = $this->db
        ->select('SUM(`exchange_point`) as "Total"', false)
        ->from('event_tulinhdan_exchange_history')     
        ->where("exchange_date <= ", $enddate)
        ->where("exchange_date >= ", $startdate)
        ->get();
        
        return $db->result_array();
    }
    
    function get_gift_arena_exchange_history($tournament_id)
    { 
        //$where = "WHERE  exchange_date >= '$startdate' AND exchange_date <= '$enddate'";
        $where = "WHERE  ecge.tournament_id = $tournament_id AND `is_restore` = 0";
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_exchange_history ecge",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS ecge.id, ecge.char_id, ecge.server_id, ecge.char_name, ecge.mobo_service_id, ecge.exchange_date, ecge.exchange_point, ecg.reward_name, ecg.reward_img
                    FROM event_quanhapmong_exchange_history ecge LEFT JOIN event_quanhapmong_reward ecg ON (ecge.reward_id = ecg.id)", 
                     "where"     =>$where,
                    "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();  
    }
    
    function get_total_point_gift_arena_exchange_history($tournament_id){
        $db = $this->db
        ->select('SUM(`exchange_point`) as "Total"', false)
        ->from('event_quanhapmong_exchange_history')     
        //->where("exchange_date <= ", $enddate)
        //->where("exchange_date >= ", $startdate)
        ->where("is_restore", 0)        
        ->where("tournament_id", $tournament_id)
        ->get();
        
        return $db->result_array();
    }
    
    function get_gift_outgame_exchange_history($startdate, $enddate)
    { 
        $where = "WHERE  exchange_gift_date >= '$startdate' AND exchange_gift_date <= '$enddate'";
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_gift_outgame_exchange_history ecge",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS ecge.id, ecge.char_id, ecge.server_id, ecge.char_name, ecge.mobo_service_id, ecge.exchange_gift_date, ecge.exchange_gift_point, ecge.receive_status, ecg.gift_name, ecg.gift_img
                    FROM event_quanhapmong_gift_outgame_exchange_history ecge LEFT JOIN event_quanhapmong_gift_outgame ecg ON (ecge.item_ex_id = ecg.id)", 
                    "where"     =>$where,
                    "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();  
    }
    
    function get_total_point_gift_outgame_exchange_history($startdate, $enddate){
        $db = $this->db
        ->select('SUM(`exchange_gift_point`) as "Total"', false)
        ->from('event_quanhapmong_gift_outgame_exchange_history')     
        ->where("exchange_gift_date <= ", $enddate)
        ->where("exchange_gift_date >= ", $startdate)
        ->get();
        
        return $db->result_array();
    }
    
    function get_top_user_point()
    { 
        $where = "WHERE true";
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_point",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_quanhapmong_point",               
                "where"     =>$where,
                    "order_by"=>" Order By user_point DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata(); 
    }
    
    function get_exchange_g_history() {
        $where = "WHERE true";
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_exchange_g_history",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_quanhapmong_exchange_g_history",               
                "where"     =>$where,
                    "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
    
    function get_history_join_arena($tournament_id) {
        $where = "WHERE `tournament_id` = $tournament_id AND `is_restore` = 0";
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_join_history",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_quanhapmong_join_history",               
                "where"     =>$where,
                    "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
    
    function get_total_point_join_arena($tournament_id){
        $db = $this->db
        ->select('SUM(`join_point`) as "Total"', false)
        ->from('event_quanhapmong_join_history')     
        ->where("tournament_id", $tournament_id)  
        ->where("is_restore", 0)
        ->get();
        
        return $db->result();
    }
    
    function get_recharging_history($startdate, $enddate)
    { 
        $where = "WHERE  insertdate >= '$startdate' AND insertdate <= '$enddate'";
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_charging",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_quanhapmong_charging",               
                "where"     =>$where,
                    "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata(); 
    }
    
    function get_total_recharging_history($startdate, $enddate){
        $db = $this->db
        ->select('SUM(`cardvalue`) as "Total"', false)
        ->from('event_quanhapmong_charging')     
        ->where("insertdate <= ", $enddate)
        ->where("insertdate >= ", $startdate)
        ->where("status", 1)
        ->get();
        
        return $db->result_array();
    }
    
    function get_total_recharging_pu_history($startdate, $enddate){
        $query = "SELECT COUNT(*) AS `Total` FROM (SELECT `mobo_service_id` "
                . "FROM `event_quanhapmong_charging` WHERE `insertdate` >= '$startdate' AND `insertdate` <= '$enddate' "
                . "AND `status` = 1 GROUP BY `mobo_service_id`) AS `PayUser`";

        $result = $this->db->query($query);
        return $result->result_array();
    }
    
    function get_exchange_history_top(){
        $where = "WHERE true";
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_exchange_history_top eteh",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS eteh.id, eteh.char_id, eteh.server_id, eteh.char_name, eteh.mobo_service_id, eteh.reward_id, eteh.exchange_date, eteh.note, eteh.data_result, etr.reward_name
                    FROM event_quanhapmong_exchange_history_top eteh LEFT JOIN event_quanhapmong_reward_top etr ON (eteh.reward_id = etr.id)",               
                "where"     =>$where,
                    "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata();
    }
    
    function load_gift_outgame_exchange_history_details($id) {
        $db = $this->db
       ->select('*')
       ->from('event_quanhapmong_gift_outgame_exchange_history')
       ->where('id', $id)
       ->get();
        
        return $db->result();
    }
    
    function edit_outgame_exchange_history($params){
        if(empty($params) || empty($params['id']) ){
            return 0;
        }
        
        $this->db
        ->set('receive_status',$params['receive_status'])       
        ->where("id", $params['id']);
        
        $this->db->update("event_quanhapmong_gift_outgame_exchange_history");      
        return $this->db->affected_rows();
    }
    
    public function get_total_point_arena($startdate, $enddate)
    {       
    	$query = $this->db->select("SUM(join_point) AS `TotalPoint`", false)
                ->from("event_quanhapmong_join_history")    
                 ->where("join_date <= ", $enddate)
                ->where("join_date >= ", $startdate) 
                ->where("is_restore", 0)
                ->get();
        
        return $query->result_array();
    }
    
    public function get_total_point_arena_gift_exchange($startdate, $enddate)
    {       
    	$query = $this->db->select("SUM(exchange_point) AS `TotalPointEx`", false)
                ->from("event_quanhapmong_exchange_history")    
                 ->where("exchange_date <= ", $enddate)
                ->where("exchange_date >= ", $startdate)   
                ->where("is_restore", 0)
                ->get();
        
        return $query->result_array();
    }
    
    public function get_total_point_user($startdate, $enddate)
    {       
    	$query = $this->db->select("SUM(user_point) AS `Total`", false)
                ->from("event_quanhapmong_point")    
                ->where("update_date <= ", $enddate)
                ->where("update_date >= ", $startdate)   
                ->get();
        
        return $query->result_array();
    }
    
    public function get_total_point_goldchange_tax($startdate, $enddate)
    {       
    	$query = $this->db->select("SUM(`tax`) AS `Total`", false)
                ->from("tbl_nganluong")    
                ->where("insertDate <= ", $enddate)
                ->where("insertDate >= ", $startdate) 
                ->where("status", 1)
                ->get();
        
        return $query->result_array();
    }
    
    //DoPhuong
    function get_total_point_dophuong_play_history($startdate, $enddate){
        $db = $this->db
        ->select('SUM(`play_point`) as "Total"', false)
        ->from('event_dophuong_play_history')     
        ->where("play_date <= ", $enddate)
        ->where("play_date >= ", $startdate)
        ->get();
        
        return $db->result_array();
    }
    
    public function get_total_point_user_dophuong($startdate, $enddate)
    {       
    	$query = $this->db->select("SUM(user_point) AS `Total`", false)
                ->from("event_dophuong_point")    
                ->where("update_date <= ", $enddate)
                ->where("update_date >= ", $startdate)   
                ->get();
        
        return $query->result_array();
    }
    
    function get_total_point_dophuong_update($startdate, $enddate){
        $db = $this->db
        ->select('SUM(`user_point`) as "Total"', false)
        ->from('event_dophuong_point')     
        ->where("update_date <= ", $enddate)
        ->where("update_date >= ", $startdate)
        ->get();
        
        return $db->result_array();
    }
    
    function get_total_point_gift_exchange_history_dophuong($startdate, $enddate){
        $db = $this->db
        ->select('SUM(`exchange_gift_point`) as "Total"', false)
        ->from('event_dophuong_gift_exchange_history')     
        ->where("exchange_gift_date <= ", $enddate)
        ->where("exchange_gift_date >= ", $startdate)
        ->get();
        
        return $db->result_array();
    }
    
    function get_total_point_gift_pakage_exchange_history_dophuong($startdate, $enddate){
        $db = $this->db
        ->select('SUM(`exchange_gift_point`) as "Total"', false)
        ->from('event_dophuong_gift_pakage_exchange_history')     
        ->where("exchange_gift_date <= ", $enddate)
        ->where("exchange_gift_date >= ", $startdate)
        ->get();
        
        return $db->result_array();
    }
    
    function get_total_point_gift_outgame_exchange_history_dophuong($startdate, $enddate){
        $db = $this->db
        ->select('SUM(`exchange_gift_point`) as "Total"', false)
        ->from('event_dophuong_gift_outgame_exchange_history')     
        ->where("exchange_gift_date <= ", $enddate)
        ->where("exchange_gift_date >= ", $startdate)
        ->get();
        
        return $db->result_array();
    }
    
    function get_total_point_revenue_dophuong($startdate, $enddate){
        $db = $this->db
        ->select('(SUM(`play_point`) -  SUM(`win_point`)) as "Total"', false)
        ->from('event_dophuong_play_history')     
        ->where("play_date <= ", $enddate)
        ->where("play_date >= ", $startdate)
        ->get();
        
        return $db->result_array();
    }
    
    public function get_total_point_goldchange_tax_dophuong($startdate, $enddate)
    {       
    	$query = $this->db->select("SUM(`tax_value`) AS `Total`", false)
                ->from("event_dophuong_point_transfer_history")    
                ->where("exchange_date <= ", $enddate)
                ->where("exchange_date >= ", $startdate) 
                ->where("status_transfer", 1)
                ->get();
        
        return $query->result_array();
    }
    
    //History CH Transfer
    function get_quanhapmong_point_transfer_history($startdate, $enddate)
    { 
        $where = "WHERE  exchange_date >= '$startdate' AND exchange_date <= '$enddate'";
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_point_transfer_history",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_quanhapmong_point_transfer_history",               
                "where"     =>$where,
                    "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata(); 
    }
    
    function get_quanhapmong_point_add_history($startdate, $enddate)
    { 
        $where = "WHERE  update_date >= '$startdate' AND update_date <= '$enddate'";
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_point_add_history",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_quanhapmong_point_add_history",               
                "where"     =>$where,
                    "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata(); 
    }
    
    //Card Exchange History
    function get_total_card_exchange_history($startdate, $enddate){
        $db = $this->db
        ->select('SUM(`card_value`) as "Total"', false)
        ->from('event_quanhapmong_card_exchange_history')     
        ->where("exchange_card_date <= ", $enddate)
        ->where("exchange_card_date >= ", $startdate)
        ->where("card_status", 1)  
        ->get();
        
        return $db->result_array();
    }
    
    //Point
    function get_total_card_exchange_history_point($startdate, $enddate){
        $db = $this->db
        ->select('SUM(`exchange_card_point`) as "Total"', false)
        ->from('event_quanhapmong_card_exchange_history')     
        ->where("exchange_card_date <= ", $enddate)
        ->where("exchange_card_date >= ", $startdate)
        ->where("card_status", 1)  
        ->get();
        
        return $db->result_array();
    }
    
    function get_card_exchange_history($startdate, $enddate)
    { 
        $where = "WHERE  exchange_card_date >= '$startdate' AND exchange_card_date <= '$enddate'";
        $this->datatables_config=array(
                "table"=>"event_quanhapmong_card_exchange_history",
                "select"=>"
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM event_quanhapmong_card_exchange_history",               
                "where"     =>$where,
                    "order_by"=>" Order By id DESC",
                "columnmaps"=>array(
                )
        );
        return $this->_bindingdata(); 
    }
    
    //Add Point Top
    public function check_exist_exchange_gift_top($tournament_id, $server_id, $mobo_service_id)
    {       
    	$query = $this->db->select("*")
                ->from("event_quanhapmong_exchange_history_top") 
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
    
    function add_point($char_id, $server_id, $mobo_service_id, $pet_point) {
        $this->db
                ->set("user_point", "user_point + $pet_point", false)
                ->set("update_date", Date('Y-m-d H:i:s'))
                //->where("char_id", $char_id)
                //->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id);
        
        $this->db->update("event_quanhapmong_point");
        return $this->db->affected_rows();
    }
    
    public function update_exchange_history_top($id, $data_send, $data_result){
        $this->db
                ->set("data_send", $data_send)
                ->set("data_result", $data_result)
                ->where("id", $id);

        $this->db->update("event_quanhapmong_exchange_history_top");
        return $this->db->affected_rows();
	}
    
    function user_check_point_exist($char_id, $server_id, $mobo_service_id) {
        $query = $this->db->select("*")
                ->from("event_quanhapmong_point")
                //->where("char_id", $char_id)
                //->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->get();

        return $query->result_array();
    }
    
    function insert_id($table, $data) {        
        $query = $this->db->insert($table, $data);
        $idinsert =  $this->db->insert_id();
        if ($this->db->affected_rows() > 0) {
            return $idinsert;
        } else {
            return false;
        }
    }
    
    function charging_config($params){
        if(empty($params) || empty($params['id']) ){
            return 0;
        }
        
        $this->db
        ->set('charging_status',$params['charging_status'])
        ->where("id", $params['id']);
        
        $this->db->update("events_quanhapmong_config");      
        return $this->db->affected_rows();
    }
    
    function get_charging_config($id)
    { 
       $db = $this->db
       ->select('*')
       ->from('events_quanhapmong_config')
       ->where('id', $id)
       ->get();
        
        return $db->result();
    }

}
?>
