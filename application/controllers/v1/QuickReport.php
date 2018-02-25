<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


require_once APPPATH . 'core/v1/Controller.php';

require_once APPPATH . 'controllers/v1/autoloader.php';

use MigEvents\ControllerInterface;
use MigEvents\Controller;
use MigEvents\Authorize;
use MigEvents\Object\Values\ResultObject;
use MigEvents\Http\OneTimePassword;
use MigEvents\Api;
use MigEvents\Http\Client\GraphClient;
use MigEvents\Tripledes;
use MigEvents\Http\Headers;
use MigEvents\Http\Receiver;
use MigEvents\Http\Client\RequestClient;
use MigEvents\Models\Events\LevelRacingOpenBetaModel;

class QuickReport extends Controller implements ControllerInterface {

    public function getEndPoint() {
        return __CLASS__;
    }

    public function __construct() {
        parent::__construct();
        $this->setPathRoot("v1/QuickReport/");
    }

    protected function Authen() {
        if ($_SERVER['PHP_AUTH_USER'] != 'quick' OR $_SERVER['PHP_AUTH_PW'] != '@quick') {
            header('WWW-Authenticate: Basic realm="Vui long nhap ten nguoi dung & mat khau"');
            header('HTTP/1.0 401 Unauthorized');
            echo "Access Denied";
            die;
        }
    }

    public function index() {
        $this->Authen();
        $this->addData("listAction", array(
            array("action" => "Reimbursement250PercentGem", "name" => "Hoàn Trả 250% Ngọc"),
            array("action" => "LevelRacingOpenBeta", "name" => "Đua Cấp Close Beta"),
        ));
        $receiver = new Receiver();

        $postData = $receiver->getPostParams();

        if (isset($postData["buttonSubmit"])) {
            switch ($postData["report-select"]) {
                case "Reimbursement250PercentGem":
                    $fields = array('id', 'mobo_id', 'mobo_service_id', 'character_id', 'character_name', 'server_id', 'msi', 'uid', 'role_name', 'mcoin', 'status', 'create_date');
                    $Model = new MigEvents\Models\Events\Reimbursement250PercentGemModel($this->getDbConfig(), $this);
                    $list = $Model->getListLogs(array(), $fields, false);
                    $this->addData("list", $list);
                    break;
                case "LevelRacingOpenBeta":
                    $fields = array('id', 'mobo_id', 'mobo_service_id', 'character_id', 'character_name', 'server_id', 'msi', 'uid', 'role_name', 'level', 'result', 'status', 'create_date');
                    $Model = new LevelRacingOpenBetaModel($this->getDbConfig(), $this);
                    $list = $Model->getListLogs(array(), $fields, false);
                    $this->addData("list", $list);
                    break;
                default :
                    $this->RenderBuildEvent("index");
                    break;
            }
        }
        $this->RenderBuildEvent("index");
    }

}
