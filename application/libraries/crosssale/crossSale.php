<?php
if(empty($_SESSION)) session_start();
class crossSale{
    private $CI;
    private $mailtitle = 'Fantasy Legend - Tang qua choi game moi';
    private $mailcontent = "Chuc mung ban nhan qua thanh cong";
    private $table_user_history = "tbl_user_history";
    private $table_user_request = "tbl_user_request";
    private $event_key = "cached_cross";
    private $_config = array();
    public function __construct() {
        $this->CI = &get_instance();
        $this->CI->load->library('GameFullAPI');
        $this->CI->load->model('m_crosssale_v2', "m_promo", false);
        $this->api = new GameFullAPI();
        //load memcache config
        if ($_SERVER['REMOTE_ADDR'] == "127.0.0.1") {
            $this->_config["memcache"] = array("host" => "127.0.0.1", "port" => 11211);
        } else {
            $this->_config["memcache"] = array("host" => "10.10.20.121", "port" => 11211);
        }
    }
    protected function saveMemcache($key, $value, $cachetime = 3600) {
        $memcache = new Memcache;
        $host = $this->_config["memcache"]["host"];
        $port = $this->_config["memcache"]["port"];
        $status = @$memcache->connect($host, $port);
        if ($status == true) {
            $mkey = md5($key);
            $memcache->set($mkey, $value, false, $cachetime);
            $memcache->close();
            return true;
        }
        return false;
    }

    protected function getMemcache($key) {
        $memcache = new Memcache;
        $host = $this->_config["memcache"]["host"];
        $port = $this->_config["memcache"]["port"];
        $status = @$memcache->connect($host, $port);
        if ($status == true) {
            $mkey = md5($key);
            $value = $memcache->get($mkey);
            $memcache->close();
            return $value;
        }
        return null;
    }
    public function startCrosssale($idgame,$user){
        //1 auto confirm install all
        //2 auto checkin login every days all
        //3 auto send gift if gamer has match level all

        //tien hanh cached
        $keyrequestgame = $this->event_key . "requestgame" .$idgame . date("Ymd", time());
        $getrequestgame = $this->getMemcache($keyrequestgame);
        //var_dump($start);die;
        if ($getrequestgame == false) {
            $getrequestgame = $this->CI->m_promo->listrequestgame($idgame);
            $this->saveMemcache($keyrequestgame, $getrequestgame, 24 * 3600);
        }

        /*echo "<pre>";
        print_r($getrequestgame);die;
        */

        $parsebygame = array();
        foreach($getrequestgame as $key=>$val){
            $parsebygame[$val['gameID']][$val['configFitter']][] = $val;
        }
        //tien hanh cached
       // $keyinfofirst = $this->event_key . "infofirst" .$idgame. $user->mobo_id . date("Ymd", time());
        //$getinfofirst = $this->getMemcache($keyinfofirst);
        //var_dump($start);die;
        //if ($getinfofirst == false) {
            $getinfofirst = $this->CI->m_promo->getinfofirst($idgame,$user->mobo_id);
        //    $this->saveMemcache($keyinfofirst, $getinfofirst, 24 * 3600);
        //}



        $parseGameType = array();
        foreach($getinfofirst as $key=>$val){
            $parseGameType[$val['gameID']] = $val;
        }
        foreach($parsebygame as $key=>$val){
            if(isset($parseGameType[$key]) && !empty($parseGameType[$key])){

                foreach($val as $key1=>$val1){
                    $parseGameType[$key]['configIDFillter'] = $key1;

                    if($key1 == 1 ){
                        $itemsConfigID = json_decode($val1[0]['items'],true);
                        if($parseGameType[$key]['status'] == 0){
                            //update function access and insert log and send item
                            //check user has receive giftitem
                            //if has receive then not send
                            if($this->updateUserRequest($idgame,$parseGameType[$key],$user) ){
                                $getidx = $this->updateHistory($idgame,$parseGameType[$key],$itemsConfigID);
                                if($getidx){
                                    if($this->sendItems($parseGameType[$key],$itemsConfigID,$user)){
                                        $this->updateHistory($idgame,$parseGameType[$key],$itemsConfigID,true,$getidx);
                                    }
                                }
                            }
                        }
                    }
                    else {

                        if ($key1 == 2) {
                            //tien hanh cached
                            $listhistoryByID = $this->getHistoryByConfig($key1, $idgame, $parseGameType[$key]);
                            //list history to get total
                            foreach ($val1 as $keyConfig => $valConfig) {
                                //parse to multiple reqeust 2
                                $jsonRule = json_decode($valConfig['jsonRule'], true);
                                $items = json_decode($valConfig['items'],true);
                                $senditem = 0;
                                //get history configID 2

                                if ($listhistoryByID[$key]['total'] < $jsonRule['login']) {
                                    //check history gamer da nhan ngay hom nay chua
                                    //da send qua
                                    $checkinday = $this->CI->m_promo->checkLoginInDay($key1, $key, $idgame, $user->mobo_id);
                                    if (empty($checkinday)) {
                                        //hnay chua checkday
                                        $parseGameType[$key]['total'] = (isset($listhistoryByID[$key]['total'])) ? $listhistoryByID[$key]['total'] : 0;
                                        if ($this->insertUserRequest($idgame, $parseGameType[$key], $user)) {
                                            $senditem = 1;
											//$this->updateHistory($idgame, $parseGameType[$key], $items);
                                        }
                                    }

                                }
                                if ($senditem != 0) {
                                    if ($listhistoryByID[$key]['total'] == ($jsonRule['login'] - 1)) {
                                        //send item
                                        $idinsert = $this->updateHistory($idgame, $parseGameType[$key], $items);
                                        if($idinsert){
                                            if ($this->sendItems($parseGameType[$key], $items, $user)) {
                                                $this->updateHistory($idgame, $parseGameType[$key], $items, true, $senditem);
                                            }
                                        }
                                    }
                                }

                            }

                        } else
                            if ($key1 == 3) {
                                //quest catagory cua key 3
                                //xem nhung item nao` chua nhan
                                //tien hanh quet level xem co du mốc hok

                                //tien hanh cached
                                //$service_name = $this->mappinglistgame($parseGameType[$key]['gameID']);
								$service_name = $this->mappinglistgame($idgame);

                                //$keygetinfo = $this->event_key . "getinfo".$service_name . $parseGameType[$key]['mobo_service_id'].$parseGameType[$key]['server_id']. date("Ymd", time());
                                $keygetinfo = $this->event_key . "getinfo".$service_name . $user->mobo_service_id.$user->server_id. date("Ymd", time());
								$getlistinfouser = $this->getMemcache($keygetinfo);
                                //var_dump($start);die;
                                if ($getlistinfouser == false) {
                                    //$getlistinfouser = $this->api->get_user_info($service_name, $parseGameType[$key]['mobo_service_id'], $parseGameType[$key]['server_id']);
									$getlistinfouser = $this->api->get_user_info($service_name, $user->mobo_service_id, $user->server_id);
                                    $this->saveMemcache($keygetinfo, $getlistinfouser, 1800);
                                }


                                $level = 0;
                                if ($getlistinfouser) {
                                    $level = $getlistinfouser['level'];
                                }
                                $gettype = array();
                                foreach ($val1 as $keyConfig => $valConfig) {
                                    $jsonRule = json_decode($valConfig['jsonRule'], true);
                                    array_push($gettype, $jsonRule['alias']);
                                }
                                foreach ($val1 as $keyr => $valr) {
                                    $jsonRule = json_decode($valr['jsonRule'], true);
                                    $items = json_decode($valr['items'],true);
                                    $parseGameType[$key]['typemoc'] = $jsonRule['alias'];
                                    if ($level >= $jsonRule['levelstart']) {
                                        if (!in_array($parseGameType[$key]['ruleType'], $gettype)) {
                                            if ($this->insertUserRequest($idgame, $parseGameType[$key], $user)) {
                                                $getidx = $this->updateHistory($idgame, $parseGameType[$key], $items);
                                                if ($getidx) {
                                                    if ($this->sendItems($parseGameType[$key], $items, $user)) {
                                                        $this->updateHistory($idgame, $parseGameType[$key], $items, true, $getidx);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }//END FE VAL 1

                            }//END KEY 3
                    }//END ESLE

                }//END FE VAL

            }//END IFNO BY GAME

        }//END FE PARSE BY GAME

    }


    public function getHistoryByConfig($configID,$idgame,$infogamer){
        $getlistrequest = $this->CI->m_promo->gettotalcheckLoginDetailNon($configID,$idgame,$infogamer['mobo_id']);

        $parseArray = array();
        foreach($getlistrequest as $key=>$val){
            $parseArray[$val['gameID']] = $val;
        }
        return $parseArray;
    }
    public function listRequestAll($idgame,$infogamer){
        $listrequestall = $this->event_key . "listrequestall".$idgame . $infogamer['gameID']. date("Ymd", time());
        $getlistrequest = $this->getMemcache($listrequestall);
        //var_dump($start);die;
        if ($getlistrequest == false) {
            $getlistrequest = $this->CI->m_promo->getListRequestAll($idgame,$infogamer['gameID']);
            $this->saveMemcache($listrequestall, $getlistrequest, 24 * 3600);
        }

        $parseArray = array();
        foreach($getlistrequest as $key=>$val){
            if( $val['configID']==$infogamer['configID'] ) {
                $parseArray[] = $val;
            }
        }
        return $parseArray;

    }
    public function sendItems($firtGamer,$items,$user){
        $service_name = $this->mappinglistgame($firtGamer['gameID']);

        $parseJsonitem = $items;

        if($service_name == 'eden' || $service_name == 'hiepkhach'  ){
            foreach($parseJsonitem as $val){

                $sentitem = array( array("item_id"=>$val['item_id'],"item_name"=>$val['item_name'],"count"=>$val['count']));

                $apisend_items = $this->api->add_item($service_name,$firtGamer['mobo_service_id'],$firtGamer['server_id'],$sentitem,$this->mailtitle,$this->mailcontent);
            }
        }else{
			foreach($parseJsonitem as $key=>$val){
				unset($parseJsonitem[$key]['item_name']);
				unset($parseJsonitem[$key]['url']);
			}
            $apisend_items = $this->api->add_item($service_name,$firtGamer['mobo_service_id'],$firtGamer['server_id'],$parseJsonitem,$this->mailtitle,$this->mailcontent);
        }
        return $apisend_items;

    }
    public function insertUserRequest($idgame,$firtGamer,$user){

        $paramsHistory = array(
            "configID"=>$firtGamer['configIDFillter'],
            "device_id"=>$firtGamer['device_id'],
            "character_id"=>$firtGamer['character_id'],
            "mobo_service_id"=>$firtGamer['mobo_service_id'],
            "mobo_id"=>$firtGamer['mobo_id'],
            "server_id"=>$firtGamer['server_id'],
            "character_name"=>$firtGamer['character_name'],
            "gameIDreceive"=>$idgame,
            "gameID"=>$firtGamer['gameID'],
            "receive_character_id"=>$user->character_id,
            "receive_mobo_service_id"=>$user->mobo_service_id,
            "receive_mobo_id"=>$user->mobo_id,
            "receive_server_id"=>$user->server_id,
            "receive_character_name"=>$user->character_name,
            "status"=>1,
            "statusFinish"=>0,
            "createDate"=>date('y-m-d H:i:s',time())
        );
        if($firtGamer['configIDFillter'] == 1){
            $paramsHistory['ruleType'] = 'installfinish';
            $paramsHistory['type'] = 'install';
        }elseif($firtGamer['configIDFillter'] == 2){
            $paramsHistory['type'] = 'checklogin';
            $paramsHistory['ruleType'] =  isset($firtGamer['total'])?$firtGamer['total']:0;
        }elseif($firtGamer['configIDFillter'] == 3){
            $paramsHistory['ruleType'] = $firtGamer['typemoc'];
            $paramsHistory['type'] = 'checklevel';
        }
        $statusInsert = $this->CI->m_promo->insert($this->table_user_request,$paramsHistory);
        return $statusInsert;
    }
    public function updateUserRequest($idgame,$firtGamer,$user){
        $params = array(
            "receive_character_id"=>$user->character_id,
            "receive_mobo_service_id"=>$user->mobo_service_id,
            "receive_mobo_id"=>$user->mobo_id,
            "receive_server_id"=>$user->server_id,
            "receive_character_name"=>$user->character_name,
            "status"=>3,
            "statusFinish"=>1,
            "createDate"=>date('y-m-d H:i:s',time())
        );
        $whereUpdate = array("mobo_id"=>$firtGamer['mobo_id'],'configID'=>$firtGamer['configID'],
            'gameIDreceive'=>$idgame,'gameID'=>$firtGamer['gameID']);
        $statusInsert = $this->CI->m_promo->update($this->table_user_request,$params,$whereUpdate);
        return $statusInsert;
    }
    public function updateHistory($idgame,$firtGamer,$items,$status = false,$idx = 0){
        if($status == false){
            $paramsHistory = array(
                "configID" => $firtGamer['configIDFillter'],
                "mobo_id" => $firtGamer['mobo_id'],
                "device_id" => $firtGamer['device_id'],
                "mobo_service_id" => $firtGamer['mobo_service_id'],
                "server_id" => $firtGamer['server_id'],
                "gamereceiveID" => $idgame,
                "gameID" => $firtGamer['gameID'],
                "items" => json_encode($items),
                "status" => 0,
                "createDate" => date('y-m-d H:i:s', time())
            );
            if($firtGamer['configIDFillter'] == 1){
                $paramsHistory['ruleType'] = 'installfinish';
                $paramsHistory['type'] = 'install';
            }elseif($firtGamer['configIDFillter'] == 2){
                $paramsHistory['type'] = 'checklogin';
                $paramsHistory['ruleType'] =  isset($firtGamer['total'])?$firtGamer['total']:0;
            }elseif($firtGamer['configIDFillter'] == 3){
                $paramsHistory['ruleType'] = $firtGamer['typemoc'];
                $paramsHistory['type'] = 'checklevel';
            }
            $statusInsertHistory = $this->CI->m_promo->insert_id($this->table_user_history, $paramsHistory);
        }else{
            //update
            $params = array("status"=>1);
            $whereUpdate = array("idx"=>$idx,'status'=>0);
            $statusInsertHistory = $this->CI->m_promo->update($this->table_user_history,$params,$whereUpdate);
        }

        return $statusInsertHistory;
    }
    public function mappinglistgame($gameid){
        $keymapping = $this->event_key . "mapping" . date("Ymd", time());
        $listgamemapping = $this->getMemcache($keymapping);
        //var_dump($start);die;
        if ($listgamemapping == false) {
            $listgamemapping = $this->CI->m_promo->getlistgamemapping();
            $this->saveMemcache($keymapping, $listgamemapping, 24 * 3600);
        }
        $service_name = "";
        foreach($listgamemapping as $key=>$val){
            if($gameid == $val['gameID']){
                $service_name = $val['alias'];
                break;
            }
        }
        return $service_name;
    }
}
?>
