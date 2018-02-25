<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace GraphShare\Object\Items;

use GraphShare\Object\AbsItemObject;
use GraphShare\Object\Fields\Items\OnepieceItemFields;
use GraphShare\Object\AbsItemInstanceInterface;

class ItemMoba extends AbsItemObject implements AbsItemInstanceInterface {
    
    public function __construct() {
        parent::__construct();
    }

    public function get($type, $position = null) {
        $items = parent::get($type, $position);        
        if ($items == false)
            return false;
        //[
        //{"item_id":1001,"count":1,  “type”:  “gold”},
        //{"item_id":1002,"count" :2,  “type”:  “silver”}
        //]
        AbsItemObject::$format = array("item_id" => "item_id", "count" => "count", "type" => "type");
        return parent::cast($items);        
    }

    public function getEndPoint() {
        return "137";
    }

    public function send() {
        $args = func_get_args();
        if (is_string($args[0])) {
            if (!isset($args[0])) {
                throw new \Exception("Invalid Paramater Type this function " . __FUNCTION__);
            }
            if (!isset($args[1])) {
                throw new \Exception("Invalid Paramater Position this function " . __FUNCTION__);
            }
            $type = $args[0];
            $position = $args[1];
        } else {
            $data = $args[0];
            if (is_array($data)) {
                if (!isset($data[0])) {
                    throw new \Exception("Invalid Paramaters Type this function " . __FUNCTION__);
                }
                if (!isset($data[1])) {
                    throw new \Exception("Invalid Paramaters Position this function " . __FUNCTION__);
                }
                $type = $data[0];
                $position = $data[1];
            } else {
                throw new \Exception("Invalid Paramaters this function " . __FUNCTION__);
            }
        }
        $items = $this->get($type, $position);               
        return $items == false ? false :
                parent::send($this->getEndPoint(), $items);
    }

}
