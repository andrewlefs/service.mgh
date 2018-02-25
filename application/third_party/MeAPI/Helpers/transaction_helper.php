<?php

/*

 * To change this template, choose Tools | Templates

 * and open the template in the editor.

 */

if (!function_exists('make_transaction')) {



    function make_transaction($prefix = NULL) {

        if ($prefix)
            $prefix .= '.';

        return strtolower(uniqid($prefix, TRUE));
    }

}
?>