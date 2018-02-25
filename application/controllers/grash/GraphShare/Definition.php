<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace GraphShare;
require_once APPPATH . 'controllers/grash/autoloader.php';

use GraphShare\Enum\AbstractEnum;

class Definition extends AbstractEnum {

    const APP_FB_ID = '533414373498024';
    const APP_FB_SECRET_KEY = 'e48719db47bce8cae1005809cd0ab192';
    const VERIFY_KEY = '9a1b4bcd3aac2e1b5910488ed4ba319b';
    const BASE_ASSET_URL = '/onepiece/asset-grashs';
    const GRASH_LOGIN = 'grash_login_store';
    const GRASH_REQUEST_INFO = 'GRASH_REQUEST_INFO';
    const GRASH_INTRO_ID = 4127;
    const GRASH_ACCESS_TOKEN = 'GRASH_ACCESS_TOKEN';
    const GRASH_PROFILE = 'GRASH_PROFILE';
    const GRASH_SHARE_DATA = 'GRASH_SHARE_DATA';
    const GRASH_LIKED_STATUS = 'GRASH_LIKED_STATUS';
    const GRASH_URL_FEED_DIALOG = 'https://www.facebook.com/dialog/feed';
    const GRASH_ME_PROFILE = 'https://graph.facebook.com/me';
    const GRASH_GRAPH_URL = 'https://graph.facebook.com/';
    const GRASH_ME_FRIENDS_URL = 'https://graph.facebook.com/me/friends';
    const GRASH_ME_INVITABLE_FRIENDS_URL = 'https://graph.facebook.com/me/invitable_friends';
    const GRASH_APPREQUEST_URL = 'http://www.facebook.com/dialog/apprequests';
    const GRASH_MAX_INVITE = 5;
    const GRASH_FRIEND_LISTS = 'GRASH_FRIEND_LISTS';
    const GRASH_FRIEND_EXCLUDED = 'GRASH_FRIEND_EXCLUDED';
    const GRASH_FRIEND_EXCLUDED_BY_DAY = 'GRASH_FRIEND_EXCLUDED_BY_DAY';
    const GRASH_PAGING = 20;
    const GRASH_TYPE_INVITE = "invite";
    //if ($_SERVER["REMOTE_ADDR"] == "127.0.0.1") {
    //    const BASE_URI= 'http://one.mobo.vn/onepiece/grash';
    const BASE_HOME_URI = 'http://one.mobo.vn/onepiece/grash/home';
    const MEMCACHED_HOST = '10.10.20.134';
    const MEMCACHED_PORT = '11211';
    //} else {
    const BASE_URI = 'http://one.mobo.vn/onepiece/grash';
    //const BASE_HOME_URI = 'http://game.mobo.vn/onepiece/grash/home';
//    const MEMCACHED_HOST = '10.10.20.121';
//    const MEMCACHED_PORT = '11211';
//}
    const BASE_PATH_VIEW = 'grash';
    const FUNC_NOT_FOUND = -1011010;
    const LIKED_SUCCESS = 1011012;
    const LIKED_ERROR = -1011013;
    const LIKED_EXISTS = -1011014;
    const IN_PROCESS_DATA = -1011015;
    const DATA_EMPTY = -1011016;
    const SYSTEM_ERROR = -1011017;

}
