<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MigEvents;

use MigEvents\Http\OneTimePassword;
use MigEvents\Object\Values\SecretKeyList;
use MigEvents\Object\Fields\HeaderField;
use MigEvents\Object\Values\ResultObject;
use MigEvents\Validation;
use MigEvents\Controller;
use MigEvents\Models\AppHashKeyModels;

class Authorize extends Controller {

    public function __construct() {
        
    }

    public function AuthorizeRequest(array $paramBodys, array $paramHeaders = null) {
        try {
            $_result = new ResultObject();

// check valid params
            $needle = array(HeaderField::TOKEN);
            $appid = 0;
            $otp = "";
            $token = "";
            
            if ($paramHeaders !== null) {
                if (is_required($paramHeaders, $needle) == FALSE) {
                    $diff = array_diff(array_values($needle), array_keys($paramHeaders));
                    $_result->setCode(ResultObject::INVALID_PARAMS_HEADER);
                    $_result->setDataWithoutValidation($diff);
                    return $_result;
                }                
                $token = $paramHeaders[HeaderField::TOKEN];
            } else if ($paramHeaders === null) {
                if (is_required($paramBodys, $needle) == FALSE) {
                    $diff = array_diff(array_values($needle), array_keys($paramBodys));
                    $_result->setCode(ResultObject::INVALID_PARAMS_HEADER);
                    $_result->setDataWithoutValidation($diff);
                    return $_result;
                }                
                $token = $paramBodys[HeaderField::TOKEN];
                unset($paramBodys[HeaderField::TOKEN]);
            }

            
            $hashkey = $this->getSecret();

            $_result->setApp($appid);
            $_result->setHashKey($hashkey);            
            $source = implode("", $paramBodys);
            $token_source = $source . $hashkey;

            $valid = md5($token_source);

            if ($token != $valid) {
                $_result->setCode(ResultObject::INVALID_TOKEN);
                $_result->setDataWithoutValidation(array(
                    "otp" => $serOtp,
                    "source" => $source,
                    "token" => $token,
                    "valid" => $valid
                ));
                return $_result;
            } else {
                $_result->setCode(ResultObject::AUTHORIZE_SUCCESS);
                return $_result;
            }
        } catch (Exception $ex) {
            throw new \InvalidArgumentException(
            'Error is not a field of ' . get_class($this));
        }
    }

}
