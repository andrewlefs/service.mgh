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

class GinsideClient extends Client implements ClientInterface {

    protected $responseResult;
   

    public function __construct() {
        $this->setDefaultBaseDomain("mobo.vn");
        $this->setDefaultLastLevelDomain("ginside");
    }

    public function getEndPoint() {
        return __CLASS__;
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
}
