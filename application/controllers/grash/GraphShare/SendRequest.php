<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace GraphShare;

use GraphShare\Object\Item;
use GraphShare\Object\Result;

class SendRequest {

    private $_listItems = [];
    private $user;

    public function __construct() {
        
    }

    public function Add($item) {
        $this->_listItems[count($this->_listItems)] = $item;
    }

    public function Send() {
        
    }

}
