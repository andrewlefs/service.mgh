<?php
require_once APPPATH . 'third_party/MeAPI/Autoloader.php';
class tulinhdan extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->model('cms/tulinhdan/m_event');
        $this->load->model('cms/tulinhdan/m_filters');
        $this->load->model('cms/tulinhdan/m_history');
        $this->output->set_header('Content-type: application/json');
        $this->output->set_header('Access-Control-Allow-Origin: *');
        $this->output->set_header('Access-Control-Allow-Headers: X-Requested-With');
        $this->output->set_header('Access-Control-Allow-Headers: Content-Type');
    }
    function slbitem(){
        $data = $this->m_event->slbItem($_GET['tournament_id']);
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{            
            $this->output->set_output(json_encode($R));
        }
    }
    function index_event(){
        $data = $this->m_event->listItems();
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{            
            $this->output->set_output(json_encode($R));
        }
    }
    
    function add_event(){
        $arrServer = json_decode($_POST['content_server'],true);
        $server_id = '';
        if(count($arrServer)>0){
            foreach($arrServer as $v){
                $resultServer[] = $v['tournament_server_list'];
            }
            $server_id = implode(';', $resultServer);
        }
        $arrParam = array(
            'tournament_name'=>$_POST['tournament_name'],            
            'tournament_date_start'=>date_format(date_create($_POST['tournament_date_start']),"Y-m-d H:i:s"),
            'tournament_date_end'=>date_format(date_create($_POST['tournament_date_end']),"Y-m-d H:i:s"),
            'tournament_status'=>$_POST['tournament_status'],
            'tournament_server_list'=>$server_id,
            'tournament_date_start_reward'=>date_format(date_create($_POST['tournament_date_start_reward']),"Y-m-d H:i:s"),
            'tournament_date_end_reward'=>date_format(date_create($_POST['tournament_date_end_reward']),"Y-m-d H:i:s"),
            'tournament_ip_list'=>$_POST['tournament_ip_list'],
            'tournament_point'=>$_POST['tournament_point'],
        );
        $data = $this->m_event->onInsertBox($arrParam);
        if($data > 0){
            $this->m_event->deleteItem($_GET['id']);
            $this->m_event->addItem($_POST['rules'],$data);
            $R["result"] = 1;
            $R["message"]='THÊM SỰ KIỆN THÀNH CÔNG !';

        }else{
            $R["result"] = -1;
            $R["message"]='THÊM SỰ KIỆN THẤT BẠI !';
        }
        
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
    function get_event(){
        $id = $_GET['id'];
        $data = $this->m_event->getItem($id);
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
    function edit_event(){
        $arrServer = json_decode($_POST['content_server'],true);
        $server_id = '';
        if(count($arrServer)>0){
            foreach($arrServer as $v){
                $resultServer[] = $v['tournament_server_list'];
            }
            $server_id = implode(';', $resultServer);
        }
        $arrParam = array(
            'id'=>addslashes($_GET['id']),
            'tournament_name'=>$_POST['tournament_name'],            
            'tournament_date_start'=>date_format(date_create($_POST['tournament_date_start']),"Y-m-d H:i:s"),
            'tournament_date_end'=>date_format(date_create($_POST['tournament_date_end']),"Y-m-d H:i:s"),
            'tournament_status'=>$_POST['tournament_status'],
            'tournament_server_list'=>$server_id,
            'tournament_date_start_reward'=>date_format(date_create($_POST['tournament_date_start_reward']),"Y-m-d H:i:s"),
            'tournament_date_end_reward'=>date_format(date_create($_POST['tournament_date_end_reward']),"Y-m-d H:i:s"),
            'tournament_ip_list'=>$_POST['tournament_ip_list'],
            'tournament_point'=>$_POST['tournament_point'],
        );       
        $data = $this->m_event->onUpdateBox($arrParam);
        if($data > 0){
            $this->m_event->deleteItem($_GET['id']);
            $this->m_event->addItem($_POST['rules'],$_GET['id']);
            $R["result"] = 1;
            $R["message"]='CHỈNH SỬA SỰ KIỆN THÀNH CÔNG !';

        }else{
            $R["result"] = -1;
            $R["message"]='CHỈNH SỬA SỰ KIỆN THẤT BẠI !';
        }
        
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
    function delete_event(){
        $data = $this->m_event->delete($_GET['id']);
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
    //filters
    function slbevent(){
        $data = $this->m_filters->slbEvent();
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{            
            $this->output->set_output(json_encode($R));
        }
    }
    function index_filters(){
        $data = $this->m_filters->listItems();
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{            
            $this->output->set_output(json_encode($R));
        }
    }
    function add_filters(){
        $arrParam = array(
            'tournament_id'=>$_POST['tournament_id'],
            'reward_name'=>$_POST['reward_name'],
            'reward_img'=>$_POST['reward_img'],
            'reward_rank_min'=>$_POST['reward_rank_min'],
            'reward_rank_max'=>$_POST['reward_rank_max'],
            'reward_item1_code'=>$_POST['reward_item1_code'],
            'reward_item1_number'=>$_POST['reward_item1_number'],
            'reward_item2_code'=>$_POST['reward_item2_code'],
            'reward_item2_number'=>$_POST['reward_item2_number'],
            'reward_item3_code'=>$_POST['reward_item3_code'],
            'reward_item3_number'=>$_POST['reward_item3_number'],
            'reward_item4_code'=>$_POST['reward_item4_code'],
            'reward_item4_number'=>$_POST['reward_item4_number'],
            'reward_item5_code'=>$_POST['reward_item5_code'],
            'reward_item5_number'=>$_POST['reward_item5_number'],
            'reward_point'=>$_POST['reward_point'],
            'reward_status'=>$_POST['reward_status'],
        );
        $data = $this->m_filters->onInsertBox($arrParam);
        if($data > 0){
            $R["result"] = 1;
            $R["message"]='THÊM BỘ LỌC THÀNH CÔNG !';

        }else{
            $R["result"] = -1;
            $R["message"]='THÊM BỘ LỌC THẤT BẠI !';
        }
        
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
    function get_filters(){
        $id = $_GET['id'];
        $data = $this->m_filters->getItem($id);
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
    function delete_filters(){
        $data = $this->m_filters->delete($_GET['id']);
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
    function edit_filters(){
        $arrParam = array(
            'id'=>addslashes($_GET['id']),
            'tournament_id'=>$_POST['tournament_id'],
            'reward_name'=>$_POST['reward_name'],
            'reward_img'=>$_POST['reward_img'],
            'reward_rank_min'=>$_POST['reward_rank_min'],
            'reward_rank_max'=>$_POST['reward_rank_max'],
            'reward_item1_code'=>$_POST['reward_item1_code'],
            'reward_item1_number'=>$_POST['reward_item1_number'],
            'reward_item2_code'=>$_POST['reward_item2_code'],
            'reward_item2_number'=>$_POST['reward_item2_number'],
            'reward_item3_code'=>$_POST['reward_item3_code'],
            'reward_item3_number'=>$_POST['reward_item3_number'],
            'reward_item4_code'=>$_POST['reward_item4_code'],
            'reward_item4_number'=>$_POST['reward_item4_number'],
            'reward_item5_code'=>$_POST['reward_item5_code'],
            'reward_item5_number'=>$_POST['reward_item5_number'],
            'reward_point'=>$_POST['reward_point'],
            'reward_status'=>$_POST['reward_status'],
        );
        $data = $this->m_filters->onUpdateBox($arrParam);
        if($data > 0){
            $R["result"] = 1;
            $R["message"]='CHỈNH SỬA BỘ LỌC THÀNH CÔNG !';

        }else{
            $R["result"] = -1;
            $R["message"]='CHỈNH SỬA BỘ LỌC THẤT BẠI !';
        }
        
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
    function index_history(){
        $arrParam = array(
            'start'=>date('Y-m-d H:i:s',$_GET['start']),
            'end'=>date('Y-m-d H:i:s',$_GET['end'])
        );
        $data = $this->m_history->history($arrParam);
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{            
            $this->output->set_output(json_encode($R));
        }
    }
    function delete_history(){
        $data = $this->m_history->delete($_GET['id']);
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
    function excel(){
        $arrParam = array(
            'start'=>date('Y-m-d G:i:s',$_GET['start']),
            'end'=>date('Y-m-d G:i:s',$_GET['end'])
        );
        $data = $this->m_history->exportExcel($arrParam);
        echo json_encode($data);
        die();
    }
}