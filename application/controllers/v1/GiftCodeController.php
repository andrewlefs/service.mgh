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
use MigEvents\Http\Client\FacebookClient;
use MigEvents\Models\Events\GiftCodeModel;

class GiftCodeController extends Controller implements ControllerInterface {

    private $facebookClient;
    private $giftCodeModel;

    public function __construct() {
        parent::__construct();
    }

    public function getEndPoint() {
        return __CLASS__;
    }

    public function getFacebookClient() {
        if ($this->facebookClient == null) {
            $this->facebookClient = new FacebookClient();
            $this->facebookClient->setApp("420240011468958");
            $this->facebookClient->setSecret("7598b94358b12bf3d66e798d9f59cbfd");
        }
        return $this->facebookClient;
    }

    public function getGiftCodeModel() {
        if ($this->giftCodeModel == null) {
            $this->giftCodeModel = new GiftCodeModel($this->getDbConfig(), $this);
        }
        return $this->giftCodeModel;
    }

    public function index() {        
        $result = new ResultObject();
        $result->OutOfJsonResponse();
    }

    public function AuthorCode() {
        $domain = ($_SERVER['HTTP_HOST'] == 'mong2.gate.vn') ? $_SERVER['HTTP_HOST'] : "mong2.gate.vn";
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 6400');    // cache for 1 day
        header("Access-Control-Request-Method: GET");
        header("Access-Control-Allow-Methods: GET, POST");
        header("Content-Type: json");
        header("X-Powered-By", "1");
        header("X-Frame-Options", "DENY");
        try {
            $params = $this->getReceiver()->getQueryParams();
            $response = new ResultObject();
            if (!isset($params["access_token"])) {
                $response->setCode(-100);
                $response->setMessage("Access Token empty");
                $response->OutOfJsonResponse();
            }
            if (!isset($params["access_token"])) {
                $response->setCode(-101);
                $response->setMessage("Event empty");
                $response->OutOfJsonResponse();
            }

            $access_token = $params["access_token"];
            $event = $params["event"];

            $verifyApp = $this->getFacebookClient()->getVerifyApp($access_token);
            if($verifyApp == false){
                $response->setCode(-105);
                $response->setMessage("Access Token not App Id: " . $this->getFacebookClient()->getApp());
                $response->OutOfJsonResponse();
            }
            
            $userInfo = $this->getFacebookClient()->getUserInfo($access_token);
            if ($userInfo == false) {
                $response->setCode(-102);
                $response->setMessage("Access Token expired");
                $response->OutOfJsonResponse();
            }
            //kiem tra code tồn tại

            $keyId = $this->getMemcacheObject()->genCacheId(__CLASS__ . __FUNCTION__ . $userInfo["id"] . $event);
            $getCodeData = $this->getMemcacheObject()->getMemcache($keyId);
            if ($getCodeData == false) {
                //kiem tra đã nhận code

                $isUsed = $this->getGiftCodeModel()->getCodeByUser(array("facebook_id" => $userInfo["id"], "event_type" => $event), array());
                //nếu log đã tồn tại
                if ($isUsed == true) {
                    $response->setCode(0);
                    $response->setData(array("code" => $isUsed["giftcode"]));
                    $response->OutOfJsonResponse();
                }

                $retry = 0;
                //tối đa thử lấy code 3 lần
                while ($retry < 3) {
                    $codes = $this->getGiftCodeModel()->getCode(array("status" => 0, "event_type" => $event), array("id", "giftcode"), false);
                    if ($codes == null) {
                        $response->setCode(-104);
                        $response->setMessage("Code not enough");
                        $response->OutOfJsonResponse();
                    }
                    $commitData = array("status" => 1, "facebook_id" => $userInfo["id"], "update_date" => date("Y-m-d H:i:s", time()));
                    $commitWhere = array("id =" => $codes["id"], "status <>" => 1);
                    $commit = $this->getGiftCodeModel()->commitCode($commitData, $commitWhere);
                    if ($commit > 0) {
                        //ghi nhan log gift code
                        $logData = array("giftcode" => $codes["giftcode"], "facebook_id" => $userInfo["id"], "event_type" => $event);
                        $this->getGiftCodeModel()->addLogs($logData);
                        //store cache with user id
                        $this->getMemcacheObject()->saveMemcache($keyId, array("code" => $codes["giftcode"]), 24 * 3600);
                        //response to api
                        $response->setCode(0);
                        $response->setData(array("code" => $codes["giftcode"]));
                        $response->OutOfJsonResponse();
                    } else {
                        $retry += 1;
                        continue;
                    }
                }
            } else {
                $response->setCode(0);
                $response->setData($getCodeData);
                $response->OutOfJsonResponse();
            }
        } catch (Exception $ex) {
             $response = new ResultObject();
             $response->setCode(-10000);
             $response->setMessage($ex->getMessage());
             $response->OutOfJsonResponse();
        }
    }

}
