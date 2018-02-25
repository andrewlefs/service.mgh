<?php
class tooltulinhdan extends CI_Controller {
    function __construct() {
        parent::__construct();

        $this->load->model('cms/m_tooltulinhdan');
        
        $this->output->set_header('Content-type: application/json');
        $this->output->set_header('Access-Control-Allow-Origin: *');
        $this->output->set_header('Access-Control-Allow-Headers: X-Requested-With');
        $this->output->set_header('Access-Control-Allow-Headers: Content-Type');
    }

    public function index() {
    } 
    
    function add_tournament(){
        $tournament_name = addslashes($_GET['tournament_name']);
        $tournament_date_start = addslashes($_GET['startdate']);
        $tournament_date_end = addslashes($_GET['enddate']);
        $tournament_status = ( is_numeric($_GET['catstatus']) && $_GET['catstatus'] >=1) ?$_GET['catstatus']:0;
        
        $params = array(            
            'tournament_name'=>$tournament_name,
            'tournament_date_start'=>$tournament_date_start,
            'tournament_date_end'=>$tournament_date_end,
            'tournament_status'=>$tournament_status);
        
        $data = $this->m_tooltulinhdan->add_tournament($params);
        
        if($data > 0){
            $R["result"] = 1;
            $R["message"]='THÊM MỚI GIẢI ĐẤU THÀNH CÔNG !';

        }else{
            $R["result"] = -1;
            $R["message"]='THÊM MỚI GIẢI ĐẤU THẤT BẠI !';
        }
        
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
    
    function edit_tournament(){
        $id = addslashes($_GET['id']);
        $tournament_name = addslashes($_GET['tournament_name']);
        $tournament_date_start = addslashes($_GET['startdate']);
        $tournament_date_end = addslashes($_GET['enddate']);
        $tournament_status = ( is_numeric($_GET['catstatus']) && $_GET['catstatus'] >=1) ?$_GET['catstatus']:0;
        
        $params = array(      
            'id'=>$id,
            'tournament_name'=>$tournament_name,
            'tournament_date_start'=>$tournament_date_start,
            'tournament_date_end'=>$tournament_date_end,
            'tournament_status'=>$tournament_status);
        
        $data = $this->m_tooltulinhdan->edit_tournament($params);
        
        if($data > 0){
            $R["result"] = 1;
            $R["message"]='CHỈNH SỬA GIẢI ĐẤU THÀNH CÔNG !';

        }else{
            $R["result"] = -1;
            $R["message"]='CHỈNH SỬA GIẢI ĐẤU THẤT BẠI !';
        }
        
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
    
    function edit_tournament_details(){
        $id = addslashes($_GET['id']);
        
        $tournament_date_start = addslashes($_GET['startdate']);
        $tournament_date_end = addslashes($_GET['enddate']);
        
        $tournament_date_start_reward = addslashes($_GET['startdate_reward']);
        $tournament_date_end_reward = addslashes($_GET['enddate_reward']);  
        
        $tournament_server_list = addslashes($_GET['server_list']);
        $tournament_ip_list = addslashes($_GET['ip_list']);
        
        $tournament_status = ( is_numeric($_GET['catstatus']) && $_GET['catstatus'] >=1) ?$_GET['catstatus']:0;
        $tournament_point = addslashes($_GET['tournament_point']);
        
        $params = array(      
            'id'=>$id,         
            'tournament_date_start'=>$tournament_date_start,
            'tournament_date_end'=>$tournament_date_end,
            'tournament_date_start_reward'=>$tournament_date_start_reward,
            'tournament_date_end_reward'=>$tournament_date_end_reward,
            'tournament_server_list'=>$tournament_server_list,
            'tournament_ip_list'=>$tournament_ip_list,
            'tournament_status'=>$tournament_status,
            'tournament_point'=>$tournament_point);
        
        $data = $this->m_tooltulinhdan->edit_tournament_details($params);
        
        if($data > 0){
            $R["result"] = 1;
            $R["message"]='CHỈNH SỬA GIẢI ĐẤU THÀNH CÔNG !';

        }else{
            $R["result"] = -1;
            $R["message"]='CHỈNH SỬA GIẢI ĐẤU THẤT BẠI !';
        }
        
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
    
    public function tournament_list(){
        $data = $this->m_tooltulinhdan->tournament_list();
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{            
            $this->output->set_output(json_encode($R));
        }
    }
    
    public function tournament_get_by_id(){
        $id = $_GET['id'];

        $data = $this->m_tooltulinhdan->tournament_get_by_id($id);
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{            
            $this->output->set_output(json_encode($R));
        }
    }
    
    public function tournament_list_name_id(){
        $data = $this->m_tooltulinhdan->tournament_list_name_id();
        $this->output->json_encode($data);     
    }
    
    //Reward Top
    function add_reward_top(){
        $tournament_id = $_GET['tournament_id'];    
        $reward_name = $_GET['reward_name']; 
        $reward_rank_min = $_GET['reward_rank_min'];
        $reward_rank_max = $_GET['reward_rank_max'];
        
        $params = array(     
            'tournament_id'=>$tournament_id,
            'reward_name'=>$reward_name, 
            'reward_rank_min'=>$reward_rank_min,
            'reward_rank_max'=>$reward_rank_max,
            'reward_point'=>0,
            'reward_img'=>null,            
            'reward_item1_code'=>0,
            'reward_item1_number'=>0,
            'reward_item2_code'=>0,
            'reward_item2_number'=>0,
            'reward_item3_code'=>0,
            'reward_item3_number'=>0,
            'reward_item4_code'=>0,
            'reward_item4_number'=>0,
            'reward_item5_code'=>0,
            'reward_item5_number'=>0);
        
        $data = $this->m_tooltulinhdan->add_reward_top($params);
        
        if($data > 0){
            $R["result"] = $data;
            $R["message"]='THÊM MỚI MỐC THƯỞNG THÀNH CÔNG !';

        }else{
            $R["result"] = -1;
            $R["message"]='THÊM MỚI MỐC THƯỞNG THẤT BẠI !';
        }
        
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
    
    function load_reward_top(){
        $tournament_id = $_GET['tournament_id'];

        $data = $this->m_tooltulinhdan->load_reward_top($tournament_id);
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{            
            $this->output->set_output(json_encode($R));
        }
    }
    
    function load_reward_details_top() {
        $id = $_GET['id'];

        $data = $this->m_tooltulinhdan->load_reward_details_top($id);
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{            
            $this->output->set_output(json_encode($R));
        }
    }
    function load_reward_details_top_tournament() {
        $id = $_GET['id'];

        $data = $this->m_tooltulinhdan->load_reward_details_top_by_tournament($id);
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{            
            $this->output->set_output(json_encode($R));
        }
    }
    
    function edit_reward_details_top(){
        $id = addslashes($_POST['id']);
        
        $reward_point = addslashes($_POST['reward_point']);
        $reward_img = addslashes($_POST['Thumb']);
        
        $reward_item1_code = addslashes($_POST['reward_item1_code']);
        $reward_item1_number = addslashes($_POST['reward_item1_number']);          
        $reward_item2_code = addslashes($_POST['reward_item2_code']);
        $reward_item2_number = addslashes($_POST['reward_item2_number']);         
        $reward_item3_code = addslashes($_POST['reward_item3_code']);
        $reward_item3_number = addslashes($_POST['reward_item3_number']);         
        $reward_item4_code = addslashes($_POST['reward_item4_code']);
        $reward_item4_number = addslashes($_POST['reward_item4_number']);         
        $reward_item5_code = addslashes($_POST['reward_item5_code']);
        $reward_item5_number = addslashes($_POST['reward_item5_number']); 
        $reward_status = addslashes($_POST['reward_status']); 
        
        $params = array(      
            'id'=>$id,         
            'reward_point'=>$reward_point,
            'reward_img'=>$reward_img,
            'reward_item1_code'=>$reward_item1_code,
            'reward_item1_number'=>$reward_item1_number,            
            'reward_item2_code'=>$reward_item2_code,
            'reward_item2_number'=>$reward_item2_number,            
            'reward_item3_code'=>$reward_item3_code,
            'reward_item3_number'=>$reward_item3_number,            
            'reward_item4_code'=>$reward_item4_code,
            'reward_item4_number'=>$reward_item4_number,            
            'reward_item5_code'=>$reward_item5_code,
            'reward_item5_number'=>$reward_item5_number,
            'reward_status'=>$reward_status);
        
        $data = $this->m_tooltulinhdan->edit_reward_details_top($params);
        
        if($data > 0){
            $R["result"] = 1;
            $R["message"]='CHỈNH SỬA GIẢI THƯỞNG THÀNH CÔNG !';

        }else{
            $R["result"] = -1;
            $R["message"]='CHỈNH SỬA GIẢI THƯỞNG THẤT BẠI !';
        }
        
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
    
    function edit_reward_name_top(){
        $id = addslashes($_GET['id']);        
        $reward_name = addslashes($_GET['reward_name']);
        $reward_rank_min = addslashes($_GET['reward_rank_min']);
        $reward_rank_max = addslashes($_GET['reward_rank_max']);
        
        $params = array(      
            'id'=>$id,         
            'reward_name'=>$reward_name,
            'reward_rank_min'=>$reward_rank_min,
            'reward_rank_max'=>$reward_rank_max);
        
        $data = $this->m_tooltulinhdan->edit_reward_name_top($params);
        
        $R["result"] = 1;
        $R["message"]='CHỈNH SỬA GIẢI THƯỞNG THÀNH CÔNG !';
        
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }   
    
    //Gift
    function add_gift(){
        $item_id = $_GET['item_id'];          
        $gift_name = $_GET['gift_name'];
        $gift_rate = $_GET['gift_rate'];   
        $gift_quantity = $_GET['gift_quantity'];
        $gift_img = $_GET['gift_img'];        
        $gift_status = $_GET['gift_status']; 
        $tournament_id = $_GET['tournament'];
        
        $params = array(     
            'item_id'=>$item_id,            
            'gift_name'=>$gift_name,     
            'gift_rate'=>$gift_rate,
            'gift_quantity'=>$gift_quantity,
            'gift_img'=>$gift_img,            
            'gift_status'=>$gift_status,
            'tournament_id'=>$tournament_id,
            'gift_insert_date'=>date('Y-m-d H:i:s')
            );
        
        $data = $this->m_tooltulinhdan->add_gift($params);
        
        if($data > 0){
            $R["result"] = $data;
            $R["message"]='THÊM MỚI QUÀ THÀNH CÔNG !';

        }else{
            $R["result"] = -1;
            $R["message"]='THÊM MỚI QUÀ THẤT BẠI !';
        }
        
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
    
    function gift_list(){
        $tournament_id = $_GET['id'];
        $data = $this->m_tooltulinhdan->gift_list($tournament_id);
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{            
            $this->output->set_output(json_encode($R));
        }
    }
    
    function load_gift_details() {
        $id = $_GET['id'];

        $data = $this->m_tooltulinhdan->load_gift_details($id);
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{            
            $this->output->set_output(json_encode($R));
        }
    }
    
    function edit_gift_details(){
        $id = addslashes($_GET['id']);        
        $item_id = addslashes($_GET['item_id']);
        $gift_name = addslashes($_GET['gift_name']);        
        $gift_rate = addslashes($_GET['gift_rate']);
        $gift_quantity = addslashes($_GET['gift_quantity']);          
        $gift_img = addslashes($_GET['gift_img']);
        $gift_status = addslashes($_GET['gift_status']);
        $tournament_id = $_GET['tournament'];
        
        $params = array(      
            'id'=>$id,         
            'item_id'=>$item_id,
            'gift_name'=>$gift_name,
            'gift_rate'=>$gift_rate,
            'gift_quantity'=>$gift_quantity,            
            'gift_img'=>$gift_img,
            'tournament_id'=>$tournament_id,
            'gift_status'=>$gift_status);
        
        $data = $this->m_tooltulinhdan->edit_gift_details($params);
        
        if($data > 0){
            $R["result"] = 1;
            $R["message"]='CHỈNH SỬA QUÀ THÀNH CÔNG !';

        }else{
            $R["result"] = -1;
            $R["message"]='CHỈNH SỬA QUÀ THẤT BẠI !';
        }
        
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
    
    //History
    function get_exchange_history()
    {
        $tournament_id = $_GET["id"];
        $data = $this->m_tooltulinhdan->get_exchange_history($tournament_id);
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{            
            $this->output->set_output(json_encode($R));
        }
    } 
    
    function get_exchange_history_top()
    {
        $tournament_id = $_GET["id"];
        $data = $this->m_tooltulinhdan->get_exchange_history_top($tournament_id);
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{            
            $this->output->set_output(json_encode($R));
        }
    } 
    
    function get_top_user_point()
    {
        $id = addslashes($_GET['id']);
        
        $data = $this->m_tooltulinhdan->get_top_user_point($id);
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{            
            $this->output->set_output(json_encode($R));
        }
    } 
    
    function get_total_point() {
        $tournament_id = $_GET["id"];
        $data = $this->m_tooltulinhdan->get_total_point($tournament_id);
        $R = $data;
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(" . json_encode($R) . ")";
        } else {
            $this->output->set_output(json_encode($R));
        }
    }
}

?>