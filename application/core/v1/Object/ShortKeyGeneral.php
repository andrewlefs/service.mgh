<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MigEvents\Object;

class ShortKeyGeneral {

    static protected function _getBaseAllLookupTable() {
        return array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', //  7
            'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', // 15
            'q', 'r', 's', 't', 'u', 'v', 'w', 'x', // 23
            'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
            'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
            'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
            'Y', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9' // 31            
        );
    }

    static public function getShortLink($lenght = 8) {
        $binaryString = "";
        $lookup = self::_getBaseAllLookupTable();
        for ($i = 0; $i < $lenght; $i++) {
            $rand = rand(0, count($lookup));
            $binaryString .= $lookup[$rand];
        }
        return $binaryString;
    }

}
