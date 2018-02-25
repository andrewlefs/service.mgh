<?php

class MeAPI_Config_Map {

    public static function getController() {
        return array(
            'authorize' => 'MeAPI_Controller_AuthorizeController',
            'payment' => 'MeAPI_Controller_PaymentController',
            'game' => 'MeAPI_Controller_GameController',
            'pushface' => 'MeAPI_Controller_PushFaceController',
            'pushnoti' => 'MeAPI_Controller_PushNotiController',            
            'report' => 'MeAPI_Controller_ReportController',
            'api' => 'MeAPI_Controller_ApiController',
            'facebook' => 'MeAPI_Controller_FacebookController',
        );
    }

    public static function getFunction() {
        return array();
    }

}