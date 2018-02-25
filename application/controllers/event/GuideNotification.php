<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


require_once APPPATH . 'core/v1/Controller.php';

require_once APPPATH . 'controllers/v1/autoloader.php';

use MigEvents\ControllerInterface;
use MigEvents\Controller;
use MigEvents\Authorize;
use MigEvents\Object\Values\ResultObject;
use MigEvents\Http\OneTimePassword;
use MigEvents\Api;
use MigEvents\Http\Client\GraphClient;
use MigEvents\Tripledes;
use MigEvents\Http\Headers;
use MigEvents\Http\Receiver;
use MigEvents\Http\Client\RequestClient;
use MigEvents\Models\Events\LevelRacingOpenBetaModel;

class GuideNotification extends Controller implements ControllerInterface {

    public function getEndPoint() {
        return __CLASS__;
    }

   
    public function __construct() {
        parent::__construct();
        $this->setPathRoot("event/GuideNotification/");
    }

    public function index() {
         $this->RenderBuildEvent("notify");        
    }

    public function Result() {

        $author = $this->getAuthorize();
        if ($author !== true) {
            $this->setMessage("Truy cập của bạn không hợp lệ.");
            $this->RenderBuildEvent("deny");
        }

        $requestData = $this->prepareArray($this->prepareQuerySecure());
        //var_dump($requestData);die;
        if (!isset($requestData["info"])) {
            $this->setMessage("Chưa lấy được thông tin nhân vật.");
            $this->RenderBuildEvent("deny");
        }

        //xử lý nhận thưởng

        $isReplace = true;
        $postData = $this->getReceiver()->getPostParams();
        if (isset($postData["buttonSubmit"])) {
            $csrfToken = $postData["csrfToken"];
            if ($this->verifyCsrfToken($csrfToken) === true) {
                $data = Tripledes::decrypt($postData["data"], $this->getSecret());
                $array_merge = array_merge_is_null($requestData, $data);
                $ups = array_level_up($array_merge);
                $params = args_with_not_empty_keys($ups, array('mobo_id',
                    'mobo_service_id',
                    'character_id',
                    'character_name',
                    'server_id',
                    'msi',
                    'uid',
                    'role_name',
                    'level',
                    'platform',
                    'ip_user',
                    'device_id',
                    'package_name'
                ));
                $params["uniday"] = date("YmdHi", time());
                $params["create_date"] = date("Y-m-d H:i:s", time());
                $logIds = $this->getLevelRacingOpenBetaModel()->addLogs($params);
                if ($logIds > 0) {
                    //add item 
                    $awards = array(
                        array("item_id" => "IM002", "item_name" => "Ngọc", "count" => 700, "type" => "diamond"),
                        array("item_id" => "ID006", "item_name" => "Thể Lực 3", "count" => 5, "type" => "item"),
                        array("item_id" => "ID009", "item_name" => "Nguyên Lực 3", "count" => 5, "type" => "item")
                    );
                    $sendResult = $this->getGApiClient()->addItems($this->getAppId()
                            , $params["mobo_service_id"]
                            , $params["server_id"]
                            , $awards
                            , "Đua Cấp Open Beta"
                            , "Chúc mừng đã nhận thành công quà Đua Cấp Close Beta");
                    $this->getLevelRacingOpenBetaModel()->updateLog(array("result" => json_encode($awards), "status" => $sendResult), array("id" => $logIds));
                    $this->setMessage("Đã hoàn thành nhận thưởng vui lòng kiểm tra thư.");
                    $isReplace = false;
                } else {
                    $this->setMessage("Nhận thưởng chưa thành công vui lòng thử lại sau ít phút.");
                }
            } else {
                $this->setMessage("Thao tác không hợp lệ.");
            }
        }

        $list = $this->getLevelRacingOpenBetaModel()->getLevelRacing(array("msi", "uid", "role_name", "level"));
        $this->addData("is_receive", true);
        if ($list == true) {
            $mobo_service_id = $requestData["access_info"]["mobo_service_id"];
            $val = array_search($mobo_service_id, array_column($list, "msi"));
            if ($val !== false) {
                $receiverExists = $this->getLevelRacingOpenBetaModel()->getLogs(array("mobo_service_id" => $mobo_service_id, "status" => 1));
                if ($receiverExists == true) {
                    if ($isReplace)
                        $this->setMessage("Bạn đã nhận phần quà này trước đó");
                    $this->addData("is_receive", true);
                } else {
                    $this->addData("logLevel", $list[$val]);
                    $this->addData("is_receive", false);
                }
            } else {
                $this->setMessage("Bạn không đủ điều kiện nhận thưởng");
            }
        } else {
            $this->setMessage("Bạn không đủ điều kiện nhận thưởng");
        }

        $this->addData("list", $list);
        $this->RenderBuildEvent("thamgia");
        exit();
    }

}
