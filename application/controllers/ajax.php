<?php

require_once APPPATH . 'core/MO_Controller.php';

class ajax extends MO_Controller {

    private $mobo_id_test = array("552397949", "886899541");
    private $pay_url = "";
    public $cart_name = array(
        CARD_TYPE_VIETTEL => "viettel",
        CARD_TYPE_MOBIFONE => "vms",
        CARD_TYPE_VINAPHONE => "vina",
        CARD_TYPE_GATE => "gate",
        CARD_TYPE_MEGA => "mega"
    );

    public function __construct() {
        parent::__construct();
        //$this->load->library("utilities");
    }

    public function sms() {
        if (isset($_POST['sms_content'])) {
            //call API
            $this->setAjaxError(0);
            $this->setAjaxMessage("Bạn đã nạp tiền thành công");
        } else {
            $this->setAjaxError(UNKOW_METHOD);
            $this->setAjaxMessage("Truy vấn không hợp lệ. Click để quay lại");
        }
        $this->ajaxOutput();
    }

    public function app() {
        
    }

    function extractparam() {
        $params = $_SESSION['linkinfo'];
        $datadecode = json_decode(base64_decode($params["access_token"]), true);
        $userdata = json_decode($params["info"], true);
        $character_id = $userdata["character_id"];
        $character_name = $userdata["character_name"];
        $server_id = $userdata["server_id"];
        $mobo_service_id = $datadecode["mobo_service_id"];
        $mobo_id = $datadecode["mobo_id"];
        return array('mobo_id' => $mobo_id, 'mobo_service_id' => $mobo_service_id, 'server_id' => $server_id, 'character_id' => $character_id, 'character_name' => $character_name, 'data' => $params);
    }

    public function cardnomey() {

        if (isset($_POST['card'])) {
            $this->load->library("api/cardPaymentApi", array("trans_id" => $this->transaction_id));
            $card = $_POST['card'];
            $validate = true;
            $params = $this->extractparam();
            if (!empty($params)) {
                if ($validate) {
                    $channel = isset($params['data']['channel']) ? $params['data']['channel'] : "";
                    $ls = explode("|", urldecode($channel));
                    if (is_array($ls)) {
                        $provider = $ls[0];
                        $refcode = $ls[1];
                    } else {
                        $provider = "";
                        $refcode = "";
                    }
                    $request = urlencode($_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_SERVER['QUERY_STRING']);
                    //Call API
                    $type = $card['type'];
                    $status = TRANS_FAILED;

                    //Get mobo account
                    //dung session mobo_Account hoac la lấy từ db
                    //$this->load->model("Model_GameAccount");
                    //$account=$this->Model_GameAccount->getAccByGName($_SESSION['username']);
                    if (empty($params['mobo_service_id']) === TRUE) {
                        $this->setAjaxError(UNKOW_METHOD);
                        $this->setAjaxMessage("Truy vấn không hợp lệ");
                        $this->ajaxOutput();
                        die;
                    }
                    $param = array(
                        "control" => "api",
                        "func" => "pay_card",
                        "username" => $params['mobo_service_id'],
                        'mobo_account' => $params['mobo_service_id'],
                        "serial" => $card["seri"],
                        "pin" => $card["code"],
                        "provider" => $provider,
                        "refcode" => $refcode,
                        "request" => $request,
                        "infochannel" => $channel,
                        "card" => $type,
                        "app" => 'mgh',
                        "useragent" => $_SERVER['HTTP_USER_AGENT'],
                        "checkcard" => 1
                    );
                    $this->cardpaymentapi->setParams($param);
                    //$this->log_request("DATA_SEND:".json_encode($param));
                    $this->log('payment', "DATA_SEND", json_encode($param));

                    $result = $this->cardpaymentapi->process();
                    //writelog to db and increment turn
                    $getLogresponse = json_decode($result['log_response'], true);
                    //  $getLogresponse = array('code'=>400090,'data'=>array('value'=>10000,'tranidcard'=>11,'msg'=>'thong tin') );
                    if ($getLogresponse['code'] == 400090) {

                        $dbturn = array(
                            'char_id' => $params['character_id'],
                            'char_name' => $params['character_name'],
                            'mobo_service_id' => $params['mobo_service_id'],
                            'cardvalue' => $getLogresponse['data']['value'],
                            'cardtype' => $type,
                            'tranidcard' => $getLogresponse['data']['id'],
                            'mess' => $getLogresponse['data']['msg'],
                            'status' => 1,
                            'insertdate' => date('Y-m-d H:i:s'),
                        );

                        $this->log('payment', "DATA_SEND_Info", json_encode($dbturn));

                        $this->load->model('events/m_quaytuong', "m_promo", false);
                        $this->m_promo->insert('event_quaytuong_charging', $dbturn);
                        //send item for gamer
                        //load item info cua gamer trung
                        //send item tuong s
                        $inforeturn = $this->buyitemCorrelateCard();

                        $this->setAjaxError(0);
                        $this->setAjaxMessage($inforeturn['message']);
                        $this->ajaxOutput();
                        exit;
                    } else {
                        $dbturn = array(
                            'char_id' => $params['character_id'],
                            'char_name' => $params['character_name'],
                            'mobo_service_id' => $params['mobo_service_id'],
                            'cardvalue' => $getLogresponse['data']['value'],
                            'cardtype' => $type,
                            'tranidcard' => $getLogresponse['data']['id'],
                            'mess' => $getLogresponse['data']['msg'],
                            'status' => 1,
                            'insertdate' => date('Y-m-d H:i:s'),
                        );
                        $this->log('payment', "DATA_SEND_Info", json_encode($dbturn));
                    }
                    if ($result["error"] === false) {
                        $this->setAjaxError(0);
                        $status = true;
                        $this->setAjaxMessage("Bạn vừa nhận được tướng.Vào túi quà kiểm tra");
                        $this->ajaxOutput();
                        exit;
                    }

                    $this->setAjaxMessage($result["message"]);
                }
            }
        } else {
            $this->setAjaxError(UNKOW_METHOD);
            $this->setAjaxMessage("Truy vấn không hợp lệ");
        }
        $this->ajaxOutput();
    }

    function get_tranid() {
        $microtime = microtime();
        $comps = explode(' ', $microtime);
        return sprintf('%d%03d', $comps[1], $comps[0] * 1000000);
    }

    function buyitemCorrelateCard() {
        //mua tuong s tru 10k
        //insert history
        $params = $_SESSION['linkinfo'];
        $quantity = 1;
        $message = '';

        $infogamer = $this->extractparam();

        $detailinfo = json_decode($params['info'], true);

        $this->load->model('events/m_quaytuong', "m_promo", false);

        $info['mobo_service_id'] = $infogamer['mobo_service_id'];
        $info['character_id'] = $detailinfo["character_id"];
        $info['server_id'] = $detailinfo["server_id"];
        $info['character_name'] = $detailinfo["character_name"];

        $infoloadgamer = $this->m_promo->query_info($info['mobo_service_id'], $info['server_id']);

        //kiem tra item_id co ton tai trong gamer hok    
        $checkitem = $this->m_promo->query_checkbuyitemS($info['mobo_service_id'], $info['server_id']);
        if (empty($checkitem) || count($checkitem) <= 0) {
            $response = array('code' => -100, 'message' => 'Không tìm thấy thông tin tướng bạn mua');
        } else {
            //load item send item vao gamer//
            if (isset($checkitem['item_id']) && !empty($checkitem['item_id'])) {
                $updateItem = array(
                    'status' => 2
                );
                $whereItem = array(
                    "mobo_service_id" => $info['mobo_service_id'],
                    "server_id" => $info['server_id'],
                    "chest_id" => $checkitem['chest_id'],
                    "item_id" => $checkitem['item_id']
                );
                $itemsend = array("item_id" => $checkitem['item_id'], 'count' => 10);

                $this->m_promo->update("event_quaytuong_buyitem", $updateItem, $whereItem);
                $this->senditemforgame($info, $itemsend);
                $response = array('code' => 0, 'message' => 'Bạn vừa nhận được tướng.Vào túi quà kiểm tra');
            } else {
                $response = array('code' => -100, 'message' => 'Không tìm thấy thông tin tướng bạn mua');
            }
        }
        return $response;
    }

    function senditemforgame($data, $item) {
        $this->load->library('MGH_API');
        if (empty($data) || empty($item)) {
            return false;
        }
        //load thu vien chung
        $api = new MGH_API();
        //write log db
        //insert log status = 0;
        //send item
        //update log status = 1;

        $getitem[] = $item;
        $jsonitem = json_encode($getitem);
        $addditem = $api->add_item($data["mobo_service_id"], $data["server_id"], $jsonitem, $title = "Chuc mung ban nhan duoc qua ", $content = "Qua quay tuong");
        //MeAPI_Log::writeCsv(array(json_encode($addditem)), 'eventquaytuong_additem_' . date('H'));
        if ($addditem) {
            $updateItem = array(
                'status' => 3
            );
            $whereItem = array(
                "mobo_service_id" => $data['mobo_service_id'],
                "server_id" => $data['server_id'],
                "item_id" => $item['item_id'],
                "type" => 'S'
            );
            $this->m_promo->update("event_quaytuong_buyitem", $updateItem, $whereItem);
        }
        return true;
    }

    function get_user_info() {

        if (!empty($_GET["mobo_service_id"]) && !empty($_GET["server_id"])) {
            $mobo_service_id = $_GET["mobo_service_id"];
            $server_id = $_GET["server_id"];

            $this->load->library('MGH_API');
            $api = new MGH_API();
            $data = $api->get_user_info($mobo_service_id, $server_id);
            //var_dump($_GET["server_id"]);
            $data = json_encode($api->get_user_info($mobo_service_id, $server_id));
            echo $data;
            die;
        } else {
            echo "";
            die;
        }
    }

    function get_server() {
        $this->load->library('MGH_API');
        $api = new MGH_API();
        $data = json_encode($api->get_list_server());
        echo $data;
        die;
    }

    //Nap Card Event Dau Truong
    public function shopnganluong_charging() {
        $this->load->model('events/m_shopnganluong', "shopnganluong", false);
        $shopnganluong_config = $this->shopnganluong->get_shopnganluong_config();

        if (isset($_POST['card'])) {
            $this->load->library("api/cardPaymentApi", array("trans_id" => $this->transaction_id));
            $card = $_POST['card'];
            $validate = true;
            $params = $this->extractparam();
            if (!empty($params)) {
                if ($validate) {
                    $channel = isset($params['data']['channel']) ? $params['data']['channel'] : "";
                    $ls = explode("|", urldecode($channel));
                    if (is_array($ls)) {
                        $provider = $ls[0];
                        $refcode = $ls[1];
                    } else {
                        $provider = "";
                        $refcode = "";
                    }
                    $request = urlencode($_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_SERVER['QUERY_STRING']);
                    //Call API
                    $type = $card['type'];
                    $status = TRANS_FAILED;

                    //Get mobo account
                    if (empty($params['mobo_service_id']) === TRUE) {
                        $this->setAjaxError(UNKOW_METHOD);
                        $this->setAjaxMessage("Truy vấn không hợp lệ*");
                        $this->ajaxOutput();
                        die;
                    }

                    if ((count($shopnganluong_config) == 0 || $shopnganluong_config[0]["charging_status"] == 0) && ($params["mobo_id"] != '260896396' && $params["mobo_id"] != '886899541')) {
                        $this->setAjaxError(0);
                        $this->setAjaxMessage('Hệ thống đang bảo trì, bạn vui lòng quay lại sau!');
                        $this->ajaxOutput();
                        exit;
                    }

                    $params = array("character_name" => $params['character_name'],
                        "mobo_id" => $params["mobo_id"],
                        "server_id" => $params['server_id'],
                        "character_id" => $params['character_id'],
                        "serial" => $card["seri"],
                        "pin" => $card["code"],
                        "card" => $type,
                        "mobo_service_id" => $params['mobo_service_id'],
                        "platform" => $_SERVER['HTTP_USER_AGENT'],
                        "service_name" => "150",
                        'mobo_account' => $params["mobo_id"],
                        'event' => 'mgh2_shopnganluong'
                    );

                    $resultjson = $this->_call_api($params);
                    $this->log('payment', "DATA_SEND_RESULT", $resultjson);
                    $result = json_decode($resultjson);

                    if ($result->code == '0' && $result->desc == 'ADD_MONEY_SUCCESS' && !empty($result->data)) {
                        $dbturn = array(
                            'char_id' => $params['character_id'],
                            'server_id' => $params['server_id'],
                            'char_name' => $params['character_name'],
                            'mobo_service_id' => $params['mobo_service_id'],
                            'cardvalue' => $result->data->value,
                            'cardtype' => $type,
                            'serial' => $card["seri"],
                            'tranidcard' => $result->data->id,
                            'mess' => $result->data->msg,
                            'result' => $resultjson,
                            'status' => 1,
                            'insertdate' => date('Y-m-d H:i:s'),
                        );

                        $this->log('payment', "DATA_SEND_Info", json_encode($dbturn));

                        //$this->shopnganluong->insert('event_shopnganluong_charging', $dbturn);
                        if ($this->shopnganluong->insert('event_shopnganluong_charging', $dbturn)) {
                            //Add point user   
                            $point = $result->data->value / 100;

                            $datauser = $this->shopnganluong->user_check_point_exist($params['character_id'], $params['server_id'], $params['mobo_service_id']);
                            if (count($datauser) > 0) {
                                if ($this->shopnganluong->add_point($params['character_id'], $params['server_id'], $params['mobo_service_id'], $point) > 0) {
                                    $userdata_p["char_id"] = $params['character_id'];
                                    $userdata_p["server_id"] = $params['server_id'];
                                    $userdata_p["char_name"] = $params['character_name'];
                                    $userdata_p["mobo_service_id"] = $params['mobo_service_id'];

                                    foreach ($datauser as $key => $u_value) {
                                        $userdata_p["user_id"] = $u_value["id"];
                                        $userdata_p["ex_type"] = $type;
                                        $userdata_p["ex_value"] = $point;
                                        $userdata_p["ex_date"] = Date('Y-m-d H:i:s');
                                        $this->shopnganluong->insert("event_shopnganluong_exchange_g_history", $userdata_p);
                                    }

                                    $this->setAjaxError(0);
                                    $this->setAjaxMessage('Nạp thẻ thành công, bạn được cộng ' . $point . ' Ngân Lượng!');
                                    $this->ajaxOutput();
                                    exit;
                                } else {
                                    $this->log('payment', "ADD_POINT_FAIL", json_encode($dbturn));
                                    $this->setAjaxError(UNKOW_METHOD);
                                    $this->setAjaxMessage('Nạp thẻ thất bại, vui lòng thử lại !****');
                                    $this->ajaxOutput();
                                    exit;
                                }
                            } else {
                                //User Point Not Found                            
                                $this->setAjaxError(UNKOW_METHOD);
                                $this->setAjaxMessage('Không có dữ liệu người dùng !');
                                $this->ajaxOutput();
                                exit;
                            }
                        } else {
                            $this->log('payment', "ADD_POINT_FAIL", json_encode($dbturn));
                            $this->setAjaxError(UNKOW_METHOD);
                            $this->setAjaxMessage('Nạp thẻ thất bại, vui lòng thử lại !****');
                            $this->ajaxOutput();
                            exit;
                        }
                    } else {
                        if ($result->data->id != "" && !empty($result->data->id)) {
                            $dbturn_ex = array(
                                'char_id' => $params['character_id'],
                                'char_name' => $params['character_name'],
                                'mobo_service_id' => $params['mobo_service_id'],
                                'cardvalue' => $result->data->value,
                                'cardtype' => $type,
                                "serial" => $card["seri"],
                                'tranidcard' => $result->data->id,
                                'mess' => $result->data->msg,
                                'result' => $resultjson,
                                'status' => 0,
                                'insertdate' => date('Y-m-d H:i:s'),
                            );
                            $this->shopnganluong->insert('event_shopnganluong_charging', $dbturn_ex);
                        }

                        $dbturn = array(
                            'char_id' => $params['character_id'],
                            'char_name' => $params['character_name'],
                            'mobo_service_id' => $params['mobo_service_id'],
                            'cardvalue' => $result->data->value,
                            'cardtype' => $type,
                            'tranidcard' => $result->data->id,
                            'mess' => $result->data->msg,
                            'status' => 1,
                            'insertdate' => date('Y-m-d H:i:s'),
                        );
                        $this->log('payment', "DATA_SEND_Info", json_encode($dbturn));

                        $this->setAjaxError(0);
                        $this->setAjaxMessage($result->data->msg);
                        $this->ajaxOutput();
                        exit;
                    }
                }
            }
        } else {
            $this->setAjaxError(UNKOW_METHOD);
            $this->setAjaxMessage("Truy vấn không hợp lệ**");
        }
        $this->ajaxOutput();
    }

    public function shopnganluong_charging_new() {
        $this->load->model('events/m_shopnganluong', "shopnganluong", false);
        $shopnganluong_config = $this->shopnganluong->get_shopnganluong_config();

        if (isset($_POST['card'])) {
            $this->load->library("api/cardPaymentApi", array("trans_id" => $this->transaction_id));
            $card = $_POST['card'];
            $validate = true;
            $params = $this->extractparam();
            if (!empty($params)) {
                if ($validate) {
                    $channel = isset($params['data']['channel']) ? $params['data']['channel'] : "";
                    $ls = explode("|", urldecode($channel));
                    if (is_array($ls)) {
                        $provider = $ls[0];
                        $refcode = $ls[1];
                    } else {
                        $provider = "";
                        $refcode = "";
                    }
                    $request = urlencode($_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_SERVER['QUERY_STRING']);
                    //Call API
                    $type = $card['type'];
                    $status = TRANS_FAILED;

                    //Get mobo account
                    if (empty($params['mobo_service_id']) === TRUE) {
                        $this->setAjaxError(UNKOW_METHOD);
                        $this->setAjaxMessage("Truy vấn không hợp lệ*");
                        $this->ajaxOutput();
                        die;
                    }

                    //Test    
                    if (!in_array($params["mobo_id"], $this->mobo_id_test)) {
                        $this->setAjaxError(0);
                        $this->setAjaxMessage('Truy cập bất hợp pháp!');
                        $this->ajaxOutput();
                        exit;
                    }

                    if ((count($shopnganluong_config) == 0 || $shopnganluong_config[0]["charging_status"] == 0) && ($params["mobo_id"] != '260896396' && $params["mobo_id"] != '886899541')) {
                        $this->setAjaxError(0);
                        $this->setAjaxMessage('Hệ thống đang bảo trì, bạn vui lòng quay lại sau!');
                        $this->ajaxOutput();
                        exit;
                    }

                    //New
                    $array = array(
                        "mobo_id" => $params["mobo_id"]
                        , "mobo_service_id" => $params['mobo_service_id']
                        , "username" => $params['character_name']
                        , "character_id" => $params['character_id']
                        , "type" => $type
                        , "character_name" => $params['character_name']
                        , "server_id" => $params['server_id']
                        , "service_id" => "0"
                        , "service_name" => 150
                        , "serial" => $card["seri"]
                        , "pin" => $card["code"]
                        , "transid" => time()
                        , "event" => "mgh2_naptheshopnganluong"
                        , "env" => "sandbox");

                    $token = md5(implode("", $array) . "IDpCJtb6Go10vKGRy5DQ");
                    $array["app"] = "game";
                    $array["token"] = $token;

                    $url = "http://gapi.mobo.vn/?control=recharge&func=verify_card&" . http_build_query($array);

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $data_result = curl_exec($ch);
                    MeAPI_Log::writeCsv(array($url, $data_result), 'user_nap_tien');
                    curl_close($ch);

//                    $this->setAjaxError(0);
//                    $this->setAjaxMessage($result_d->desc);
//                    $this->ajaxOutput();
//                    exit;

                    $this->log('payment', "DATA_SEND_RESULT", $data_result);
                    $result = json_decode($data_result);

                    if ($result->code == '0' && $result->desc == 'VERIFY_CARD_SUCCESS' && !empty($result->data)) {
                        $dbturn = array(
                            'char_id' => $params['character_id'],
                            'server_id' => $params['server_id'],
                            'char_name' => $params['character_name'],
                            'mobo_service_id' => $params['mobo_service_id'],
                            'cardvalue' => $result->data->value,
                            'cardtype' => $type,
                            'serial' => $card["seri"],
                            'tranidcard' => $result->data->id,
                            'mess' => $result->data->msg,
                            'result' => $resultjson,
                            'status' => 1,
                            'insertdate' => date('Y-m-d H:i:s'),
                        );

                        $this->log('payment', "DATA_SEND_Info", json_encode($dbturn));

                        //$this->shopnganluong->insert('event_shopnganluong_charging', $dbturn);
                        if ($this->shopnganluong->insert('event_shopnganluong_charging', $dbturn)) {
                            //Add point user   
                            $point = $result->data->value / 100;

                            $datauser = $this->shopnganluong->user_check_point_exist($params['character_id'], $params['server_id'], $params['mobo_service_id']);
                            if (count($datauser) > 0) {
                                if ($this->shopnganluong->add_point($params['character_id'], $params['server_id'], $params['mobo_service_id'], $point) > 0) {
                                    $userdata_p["char_id"] = $params['character_id'];
                                    $userdata_p["server_id"] = $params['server_id'];
                                    $userdata_p["char_name"] = $params['character_name'];
                                    $userdata_p["mobo_service_id"] = $params['mobo_service_id'];

                                    foreach ($datauser as $key => $u_value) {
                                        $userdata_p["user_id"] = $u_value["id"];
                                        $userdata_p["ex_type"] = $type;
                                        $userdata_p["ex_value"] = $point;
                                        $userdata_p["ex_date"] = Date('Y-m-d H:i:s');
                                        $this->shopnganluong->insert("event_shopnganluong_exchange_g_history", $userdata_p);
                                    }

                                    $this->setAjaxError(0);
                                    $this->setAjaxMessage('Nạp thẻ thành công, bạn được cộng ' . $point . ' Ngân Lượng!');
                                    $this->ajaxOutput();
                                    exit;
                                } else {
                                    $this->log('payment', "ADD_POINT_FAIL", json_encode($dbturn));
                                    $this->setAjaxError(UNKOW_METHOD);
                                    $this->setAjaxMessage('Nạp thẻ thất bại, vui lòng thử lại !****');
                                    $this->ajaxOutput();
                                    exit;
                                }
                            } else {
                                //User Point Not Found                            
                                $this->setAjaxError(UNKOW_METHOD);
                                $this->setAjaxMessage('Không có dữ liệu người dùng !');
                                $this->ajaxOutput();
                                exit;
                            }
                        } else {
                            $this->log('payment', "ADD_POINT_FAIL", json_encode($dbturn));
                            $this->setAjaxError(UNKOW_METHOD);
                            $this->setAjaxMessage('Nạp thẻ thất bại, vui lòng thử lại !****');
                            $this->ajaxOutput();
                            exit;
                        }
                    } else {
                        if ($result->data->id != "" && !empty($result->data->id)) {
                            $dbturn_ex = array(
                                'char_id' => $params['character_id'],
                                'char_name' => $params['character_name'],
                                'mobo_service_id' => $params['mobo_service_id'],
                                'cardvalue' => $result->data->value,
                                'cardtype' => $type,
                                "serial" => $card["seri"],
                                'tranidcard' => $result->data->id,
                                'mess' => $result->data->msg,
                                'result' => $resultjson,
                                'status' => 0,
                                'insertdate' => date('Y-m-d H:i:s'),
                            );
                            $this->shopnganluong->insert('event_shopnganluong_charging', $dbturn_ex);
                        }

                        $dbturn = array(
                            'char_id' => $params['character_id'],
                            'char_name' => $params['character_name'],
                            'mobo_service_id' => $params['mobo_service_id'],
                            'cardvalue' => $result->data->value,
                            'cardtype' => $type,
                            'tranidcard' => $result->data->id,
                            'mess' => $result->data->msg,
                            'status' => 1,
                            'insertdate' => date('Y-m-d H:i:s'),
                        );
                        $this->log('payment', "DATA_SEND_Info", json_encode($dbturn));

                        $this->setAjaxError(0);
                        $this->setAjaxMessage($result->data->msg);
                        $this->ajaxOutput();
                        exit;
                    }
                }
            }
        } else {
            $this->setAjaxError(UNKOW_METHOD);
            $this->setAjaxMessage("Truy vấn không hợp lệ**");
        }
        $this->ajaxOutput();
    }

    private function _call_api($params) {
        $url = "http://gapi.mobo.vn/?control=paycard&func=pay_card";
        $token = md5(implode("", $params) . "64gPeOF0pGr7G7NncHsb");

        $url = $url . "&" . http_build_query($params) . "&app=monggiangho&token=" . $token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        MeAPI_Log::writeCsv(array($url, $result), 'user_nap_tien');
        return $result;
    }

}
