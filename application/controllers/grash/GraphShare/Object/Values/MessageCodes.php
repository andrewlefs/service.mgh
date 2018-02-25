<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace GraphShare\Object\Values;

use GraphShare\Enum\AbstractEnum;

class MessageCodes extends AbstractEnum {

    const FUNC_NOT_FOUND = -1011010;
    const LIKED_SUCCESS = 1011012;
    const LIKED_ERROR = -1011013;
    const LIKED_EXISTS = -1011014;
    const IN_PROCESS_DATA = -1011015;
    const DATA_EMPTY = -1011016;
    const SYSTEM_ERROR = -1011017;
    const ERROR = 1001009;
    const PARAM_INVLID = 1001020;
    const CATEGORY_INVALID = 1001021;
    const EXPIRED = 1001022;
    const NOT_EXPIRED = 1001023;
    const LIKED_NOT_AVALID = 1001024;
    const ACCEPT_ERROR = 1001025;
    const ACCEPT_SUCCESS = 1001026;
    const INVALID_TOKEN = 1001027;
    const INVALID_QUOTA = 1001028;
    const ACCEPT_EXISTS = 1001029;
    const SUCCESS = 1001026;
    const EXISTS = 1001029;

    /**
     * Get error message of a value. It's actually the constant's name
     * @param integer $value
     * 
     * @return string
     */
    public static function getErrorMessage($value) {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());

        return $constants[$value];
    }

}
