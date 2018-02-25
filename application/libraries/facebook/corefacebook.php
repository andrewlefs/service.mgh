<?php

require_once "facebook.php";

class corefacebook extends Facebook {

    private $CI;

    function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->config('facebook');
        parent::__construct(array(
            'appId' => $this->CI->config->item('facebook_app_id'),
            'secret' => $this->CI->config->item('facebook_api_secret'),
            'fileUpload' => true
        ));
    }

    public function postMyFeed($link, $message = '', $picture = '', $name = '',$caption='', $description = '', $tags = array(), $place = '') {
        try {
            $res = $this->api('/me/feed', 'POST', array(
                'link' => $link,
                'message' => $message,
                'picture' => $picture,
                'name' => $name,
                'caption' => $caption,
                'description' => $description,
                'tags' => implode(',', $tags),
                'place' => $place ? $place : '155021662189'
            ));
            return $res;
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function postMyPhoto($pathPhoto, $message = '', $tags = array()) {
        try {
            $res = $this->api('/me/photos', 'POST', array(
                'source' => '@' . $pathPhoto,
                'message' => $message,
                'tags' => $tags
                    )
            );
            return $res;
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function makeTagPhoto($fbid, $x = 0, $y = 0) {
        return array('tag_uid' => $fbid, 'x' => $x, 'y' => $y);
    }

    public function isPageLike($pageId) {
        try {
            $res = $this->api('/me/likes/' . $pageId);
            if ($res['data'] || $res['paging']) {
                return true;
            }
            return false;
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function getFBUser($username) {
        try {
            return $this->api('/' . $username);
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function parsePageSignedRequest($signed_request) {
        if (isset($signed_request)) {
            $encoded_sig = null;
            $payload = null;
            list($encoded_sig, $payload) = explode('.', $signed_request, 2);
            $sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
            $data = json_decode(base64_decode(strtr($payload, '-_', '+/'), true));
            return $data;
        }
        return false;
    }

    public function isLikeMyPage() {
        $signed_request = $this->parsePageSignedRequest($_REQUEST['signed_request']);
        if ($signed_request) {
            if ($signed_request->page->liked) {
                return true;
            } else {
                return false;
            }
        }
    }
    
    public function isMyPage() {
        $signed_request = $this->parsePageSignedRequest($_REQUEST['signed_request']);
        if ($signed_request) {
            if ($signed_request->page) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getRandomFriend($limit = 5, $listField = array('uid', 'name')) {
        $fql = 'SELECT ' . implode(',', $listField) . ' FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1=me() ORDER BY rand()) LIMIT ' . $limit;
        $res = $this->api(array(
            'method' => 'fql.query',
            'query' => $fql,
        ));
        return $res;
    }
    
    /**
     * 
     * require permision user_status
     */
    public function getUserOnline($limit = 5, $listField = array('uid', 'name','online_presence')) {
        $fql = 'SELECT ' . implode(',', $listField) . ' FROM user WHERE online_presence IN (\'active\', \'idle\') AND uid IN (SELECT uid2 FROM friend WHERE uid1 = me()) LIMIT .'.$limit;
        $res = $this->api(array(
            'method' => 'fql.query',
            'query' => $fql,
        ));
        return $res;
    }

}