<?php

class MeAPI_Config_Game {

    public static function get_secret() {
        return 'Icn1zLIV';
        //return 'dF31qvJs';
    }

    public static function get_client_id() {
        return 'S00024';
    }

    public static function get_count_trial_account() {
        return 5;
    }

    public static function facebook() {
        return array(
            'client_id' => '566199320218549',
            'client_secret' => 'c07982a308ab248ea4083521aead89e0',
            'oauth_key' => "5b60526724106275eb5600c806f9bb07",
            'scope' => 'publish_actions,email,user_friends',
            'page_id_like' => '426717494139192',           
            'share_limit' => 5,
            'accept_limit' => 10,
            'share_delay_minute' => (10 * 60),
            'invite_dec' => 1,
            'invite_limit' => 100,
            'request_message' => 'Đảo Hải Tặc',
            'redirect_uri' => 'http://game.mobo.vn/onepiece/fb_login_success/',
            'redirect_callback' => 'http://game.mobo.vn/onepiece',
        );
    }
    //"systeminfo" => array("host" => "10.10.20.121", "port" => 11211)
    public static function cache(){
        return array(
            "systeminfo" => array("host" => "10.10.20.121", "port" => 11211)
        );
    }

}