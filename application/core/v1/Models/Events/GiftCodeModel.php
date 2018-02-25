<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MigEvents\Models\Events;

use MigEvents\Models\ModelObject;
use MigEvents\Models\ModelObjectInterface;
use MigEvents\Models\ModeTableNamelEnum;
use MigEvents\MemcacheObject;

class GiftCodeModel extends ModelObject implements ModelObjectInterface {

    /**
     * 
     * @param array $config
     * @param Controler $controller
     * @param boolean $type
     */
    public function __construct(array $config, $controller, $type = true) {
        parent::__construct($config, $type);
        parent::setController($controller);
    }

    /**
     * Lấy real key hash data receiver
     * @param int $app
     */
    public function getCode(array $keys, array $fields = array(), $cached = true) {

        $keyId = $this->getController()->getMemcacheObject()->genCacheId(__CLASS__ . __FUNCTION__ . json_encode($keys));
        //echo $this->getEndPoint();die;
        $queryResult = $this->getController()->getMemcacheObject()->getMemcache($keyId, $this->getEndPoint());
        if ($queryResult == false || $cached == false) {
            $query = $this->getConnection()->select(count($fields) == 0 ? '*' : implode(',', $fields))
                    ->where($keys)
                    ->get(ModeTableNamelEnum::LANDING_GIFTCODE);

            //echo $this->getConnection()->last_query();die;
            $queryResult = ($query != FALSE) ? $query->row_array() : FALSE;
            if ($queryResult != false)
                $this->getController()->getMemcacheObject()->saveMemcache($keyId, $queryResult, $this->getEndPoint(), 24 * 3600);
        }

        return $queryResult;
    }

    /**
     * Lấy real key hash data receiver
     * @param int $app
     */
    public function getCodeByUser(array $keys, array $fields = array(), $cached = true) {

        $keyId = $this->getController()->getMemcacheObject()->genCacheId(__CLASS__ . __FUNCTION__ . json_encode($keys));
        //echo $this->getEndPoint();die;
        $queryResult = $this->getController()->getMemcacheObject()->getMemcache($keyId, $this->getEndPoint());
        if ($queryResult == false || $cached == false) {
            $query = $this->getConnection()->select(count($fields) == 0 ? '*' : implode(',', $fields))
                    ->where($keys)
                    ->get(ModeTableNamelEnum::LANDING_GIFTCODE_HISTORY);

            //echo $this->getConnection()->last_query();die;
            $queryResult = ($query != FALSE) ? $query->row_array() : FALSE;
            if ($queryResult != false)
                $this->getController()->getMemcacheObject()->saveMemcache($keyId, $queryResult, $this->getEndPoint(), 24 * 3600);
        }

        return $queryResult;
    }
    public function commitCode(array $data, array $where) {                
        return $this->getConnection()->update(ModeTableNamelEnum::LANDING_GIFTCODE, $data, $where);        
    }
    
    public function addLogs(array $data) {
        $data["create_date"] = date("Y-m-d H:i:s", time());
        return parent::insert(ModeTableNamelEnum::LANDING_GIFTCODE_HISTORY, $data);
    }

    public function updateLog(array $data, array $wheres) {
        $sql = $this->getConnection()->update(ModeTableNamelEnum::LANDING_GIFTCODE_HISTORY, $data, $wheres);
        //var_dump($this->getConnection()->last_query());die;
        return $this->getConnection()->affected_rows();
    }

    public function updateData(array $data, array $wheres) {
        $sql = $this->getConnection()->update(ModeTableNamelEnum::LANDING_GIFTCODE_HISTORY, $data, $wheres);
        //var_dump($this->getConnection()->last_query());die;
        return $this->getConnection()->affected_rows();
    }

    public function getEndPoint() {
        return __CLASS__;
    }

}
