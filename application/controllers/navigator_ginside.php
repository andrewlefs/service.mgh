<?php
if(empty($_SESSION)) session_start();
class navigator_ginside extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('navigator_ginside/m_navigator');
    }
    public function index(){
        $data = $this->m_navigator->listItems();
        echo json_encode($data);
        die();
    }
    public function status(){        
        $arrParam = array(
            'cid'=>!empty($_POST['cid'])?explode(',', $_POST['cid']):array(),
            'id'=>($_POST['id']>0)?$_POST['id']:'',
            'type'=>$_POST['type'],
            's'=>$_POST['s']
        );
        $this->m_navigator->status($arrParam);
    }
    public function sort(){        
        $arrParam = array(
            'listid'=>!empty($_POST['listid'])?explode(',', $_POST['listid']):array(),
            'listorder'=>!empty($_POST['listorder'])?explode(',', $_POST['listorder']):array(),
        );
        $this->m_navigator->sortItem($arrParam);
    }
    public function titlelang(){
        $data = $this->m_navigator->titlelang($_GET['id'],$_GET['lang']);
        echo json_encode($data);
        die();
    }
    public function getItem(){
        $id = $_GET['id'];
        $data = $this->m_navigator->getItem($id);
        $R=$data;
        if(isset($_GET['callback'])){
            echo $_GET['callback']."(".json_encode($R).")";
        }else{
            $this->output->set_header('Content-type: application/json');
            $this->output->set_output(json_encode($R));
        }
    }
    public function edit(){
        $arrLang = explode(',', $_POST['strlang']);
        
        $arrEvent = array(
            'service_start'=>$_POST['service_start'],
            'service_end'=>$_POST['service_end'],
            'service_trustip'=>$_POST['service_trustip'],
            'service_title'=>$_POST['title_'.$arrLang[0]],
            'service_update'=>gmdate('Y-m-d'),
            'service_status'=>$_POST['service_status'],
            'service_author'=>$_POST['service_author'],
            'service_url'=>$_POST['service_url'],
            'service_ishot'=>$_POST['service_ishot'],
            'service_android'=>isset($_POST['service_android'])?'1':'0',
            'service_ios'=>isset($_POST['service_ios'])?'1':'0',
            'service_wp'=>isset($_POST['service_wp'])?'1':'0',
            'jsonRule'=>$_POST['jsonRule'],
            'service_order'=>$_POST['service_order'],
            'service_img'=>$_POST['service_img'],
        );
        $this->m_navigator->update($arrEvent,$_POST['id']);
        
        if(count($arrLang)>0){
            foreach($arrLang as $lang){
                $arrEventLang = array(
                    'nav_id'=>$_POST['id'],
                    'alias'=>$lang,
                    'title'=>$_POST['title_'.$lang],
                    'createDate'=>gmdate('Y-m-d G:i:s')
                );
                $this->m_navigator->deleteEventLang($_POST['id'],$lang);
                $this->m_navigator->insertEventLang($arrEventLang);
            }
        }
    }
    public function add(){        
        $arrLang = explode(',', $_POST['strlang']);
        
        $arrEvent = array(
            'service_start'=>$_POST['service_start'],
            'service_end'=>$_POST['service_end'],
            'service_trustip'=>$_POST['service_trustip'],
            'service_title'=>$_POST['title_'.$arrLang[0]],
            'service_insert'=>gmdate('Y-m-d'),
            'service_status'=>$_POST['service_status'],
            'service_author'=>$_POST['service_author'],
            'service_url'=>$_POST['service_url'],
            'service_ishot'=>$_POST['service_ishot'],
            'service_android'=>isset($_POST['service_android'])?'1':'0',
            'service_ios'=>isset($_POST['service_ios'])?'1':'0',
            'service_wp'=>isset($_POST['service_wp'])?'1':'0',
            'jsonRule'=>$_POST['jsonRule'],
            'service_order'=>-1,
            'service_img'=>$_POST['service_img'],
        );
        $nav_id = $this->m_navigator->insert($arrEvent);
        $this->m_navigator->updateOrder(array('service_order'=>(-1)*$nav_id),$nav_id);
        if(count($arrLang)>0){
            foreach($arrLang as $lang){
                $arrEventLang = array(
                    'nav_id'=>$nav_id,
                    'alias'=>$lang,
                    'title'=>$_POST['title_'.$lang],
                    'createDate'=>gmdate('Y-m-d G:i:s')
                );
                $this->m_navigator->insertEventLang($arrEventLang);
            }
        }
        $f = array('id'=>$nav_id);
        echo json_encode($f);
    }
    public function delete(){
        if($_POST['type']=='multi'){
            $listID = !empty($_POST['cid'])?explode(',', $_POST['cid']):array();
            if(count($listID)>0){
                foreach($listID as $v){
                    $this->m_navigator->deleteNavigationEventByIDtLang($v);
                    $this->m_navigator->deleteNavigationEventByID($v);
                }
            }
        }else{
            $this->m_navigator->deleteNavigationEventByIDtLang($_POST['id']);
            $this->m_navigator->deleteNavigationEventByID($_POST['id']);
        }
    }
}