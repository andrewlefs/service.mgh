<?php
if(empty($_SESSION)) session_start();
class responseginside extends CI_Controller {
    public function __construct() {
        parent::__construct();
		$this->load->model('m_responseginside');
    }
    private $service_navigation_language = "service_navigation_language";
    private function valid_params($params) {
        return TRUE;
    }
    public function index() {
		echo 'Welcome';
    }
	
	
	public function update(){
		$getid = $_POST['service_id'];
        $return = array('message'=>"UPDATE FALIED",'code'=>-100,'data'=>'');
		$status = false;
		if($getid){
			$params = $_POST;
			$status = $this->m_responseginside->update($params,array('service_id'=>$params['service_id']));
		}
		if($status){
            $return = array('message'=>'UPDATE SUCCESSFUL','code'=>0,'data'=>$status);
		}
        result:
        echo json_encode($return);
        die;
		
	}
	
	public function insert(){
		$params = $_POST;
        $status = false;
		if($params){
			$status = $this->m_responseginside->insert_id($params);
		}
        if($status){
            $return = array('message'=>'UPDATE SUCCESSFUL','code'=>0,'data'=>$status);
        }
        result:
        echo json_encode($return);
        die;
		
	}
	public function delete(){
	
	}
    public function updateLanguage(){
        $return = array('message'=>"GET LIST FALIED",'code'=>-100,'data'=>'');
        $idlang = is_numeric($_GET['idlang'])?$_GET['idlang']:0;
        $titlelang = addslashes($_GET['titlelang']);
        if(empty($idlang) || empty($titlelang)){
            goto result;
        }

        $params = array("title"=>$titlelang);
        $status = $this->m_responseginside->updateTable($this->service_navigation_language,$params,array('id'=>$idlang));
        if($status){
            $return = array('message'=>'GET LIST SUCCESSFUL','code'=>0,'data'=>$status);
        }

        result:
        $R = $return;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
	public function getcategorybyid(){
		$return = array('message'=>"GET LIST FALIED",'code'=>-100,'data'=>'');
        $idget = is_numeric($_GET['id'])?$_GET['id']:0;
        if(empty($idget) || $idget ==0 ){
            $return = array('message'=>"GET LIST FALIED",'code'=>-100,'data'=>'');
        }
		$status = $this->m_responseginside->getcategorybyid($idget);
		if($status){
            $return = array('message'=>'GET LIST SUCCESSFUL','code'=>0,'data'=>$status);
		}

        result:
        echo json_encode($return);
        die;
	}
    function getcategoryall(){
        $return = array('message'=>'GET LIST FALIED','code'=>-100,'data'=>'');
        $status = false;
        $getlist = $this->m_responseginside->getcategoryall();
        if($getlist){
            $return = array('message'=>'GET LIST SUCCESSFUL','code'=>0,'data'=>$getlist);
        }
        result:
        echo json_encode($return);
        die;
    }
}
