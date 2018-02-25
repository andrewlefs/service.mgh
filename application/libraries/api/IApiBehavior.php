<?php
/**
 * Created by Ivoglent Nguyen.
 * User: longnv
 * Date: 10/21/13
 * Time: 1:38 PM
 * Project : payment
 * File : IApiBehavior.php
 */

interface IApiBehavior {
    /*
     * Function setParams
     * @set parameter for this action
     * @params is array
     */
    public function setParams($params);
    /*
     * function setCommand
     * @set command fo this action
     * @c is string
     */
    public function setCommand($c);
    /*
     * function process
     * @process this action an return result
     */
    public function process();
}