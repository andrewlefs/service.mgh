<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MigEvents\Http\Client;

use MigEvents\Http\Client\ClientCurl;
use MigEvents\Http\RequestInterface;
use MigEvents\Http\Request;
use MigEvents\Http\ResponseInterface;
use MigEvents\Http\Headers;
use MigEvents\Http\Client\ClientInterface;
use MigEvents\Http\Parameters;
use MigEvents\Http\Adapter\CurlAdapter;
use MigEvents\Http\Response;
use MigEvents\Http\Exception\EmptyResponseException;
use MigEvents\Http\Exception\RequestException;
use MigEvents\Http\OneTimePassword;
use MigEvents\Tripledes;

class GraphClient extends Client implements ClientInterface {

    protected $responseResult;
    protected $timeSlice;

    public function __construct() {
        $this->setDefaultBaseDomain("mobo.vn");
        $this->setDefaultLastLevelDomain("graph");
    }

    public function getEndPoint() {
        return __CLASS__;
    }

    public function setTimeSlice($timeSlice) {
        $this->timeSlice = $timeSlice;
    }

    public function sendRequest(RequestInterface $request) {
        $header = new Headers();

        $otpCode = OneTimePassword::getCode($this->getSecret(), $this->getTimeSlice());
        $params = $request->getQueryParams()->getArrayCopy();

        $original = implode("", $params) . $otpCode;
        $token = md5($original . $this->getSecret());

        $header['otp'] = $otpCode;
        $header["app"] = $this->getApp();
        $header["token"] = $token;

        $this->setDefaultRequestHeaders($header);

        $this->responseResult = parent::sendRequest($request);
        //parse result
        return $this->responseResult;
    }

    public function prepareResponse() {
        //var_dump($this->responseResult);
        if ($this->responseResult != NULL) {
            $result = $this->responseResult->getBody();
            $resultDecrypt = Tripledes::decrypt($result, $this->getSecret());

            $endResult = null;
            if (is_array($resultDecrypt))
                $endResult = $resultDecrypt;
            else {
                $endResult = json_decode($result, true);
                if ($endResult == null) {
                    return $this->responseResult->getContent();
                }
            }
            return $endResult;
        } else {
            return null;
        }
    }

    /**
     * 
     * @return Api of MigEvents\Http\Client\GraphClient
     */
    public function getGraph() {
        if ($this->api == null) {
            $this->api = new Api(new GraphClient());
            $this->api->getHttpClient()->setApp($this->getAppId());
            $this->api->getHttpClient()->setSecret($this->getSecret());
            $this->athwartTimeSlice = $this->getMemcacheObject()->getMemcache("misc.dllglobal.net.athwartTimeSlice", "athwartTimeSlice");
            if ($this->athwartTimeSlice == false) {
                $timeServer = $this->api->call("/ntp/time")->getContent();
                $cuurentTimeSlice = time();
                //tổ chức cache server nếu cần
                if ($timeServer["code"] === 100000) {
                    $this->athwartTimeSlice = ((int) ($timeServer["data"]["timestamps"])) - time();
                }
                if ($this->athwartTimeSlice != false)
                    $this->getMemcacheObject()->saveMemcache("misc.dllglobal.net.athwartTimeSlice", $this->athwartTimeSlice, "athwartTimeSlice", 10 * 30);
            }
            if ($this->athwartTimeSlice != false) {
                $timeSlice = (int) ((time() + $this->athwartTimeSlice) / 30);
                $this->api->getHttpClient()->setTimeSlice($timeSlice);
            } else {
                $timeSlice = (int) (time() / 30);
                $this->api->getHttpClient()->setTimeSlice($timeSlice);
            }
        }
        //$this->getTimeSlice();
        return $this->api;
    }

    public function getTimeSlice() {
        $this->athwartTimeSlice = $this->getMemcacheObject()->getMemcache("misc.dllglobal.net.athwartTimeSlice", "athwartTimeSlice");

        if ($this->athwartTimeSlice == false) {
            $timeServer = $this->getGraph()->call("/ntp/time")->getContent();
            $cuurentTimeSlice = time();
            //tổ chức cache server nếu cần
            if ($timeServer["code"] === 100000) {
                $this->athwartTimeSlice = ((int) ($timeServer["data"]["timestamps"])) - time();
            }
            if ($this->athwartTimeSlice != null)
                $this->getMemcacheObject()->saveMemcache("misc.dllglobal.net.athwartTimeSlice", $this->athwartTimeSlice, "athwartTimeSlice", 10 * 30);
        }
        if ($this->athwartTimeSlice != null) {
            $timeSlice = (int) ((time() + $this->athwartTimeSlice) / 30);
            $this->getGraph()->getHttpClient()->setTimeSlice($timeSlice);
            return $timeSlice;
        } else {
            $timeSlice = (int) (time() / 30);
            $this->getGraph()->getHttpClient()->setTimeSlice($timeSlice);
            return $timeSlice;
        }
    }

    /**
     * 
     * @param array $params array("access_token" => ?)
     * @return boolean or array account info
     * array feilds  'account_id', 'account', 'email', 'channel', 'device_id'
     */
    public function verifyAccessToken(array $params = array()) {
        if (count($params) == false) {
            $paramBodys = $this->prepareQuerySecure();
            if (isset($paramBodys["access_token"]))
                $params = array("access_token" => urldecode($paramBodys["access_token"]));
        }
        if (count($params) == false)
            return false;
        $result = $this->getGraph()->call("/game/verify_access_token", "GET", $params)->getContent();
        if ($result["code"] === 500010) {
            return $result["data"];
        } else {
            return false;
        }
    }
}
