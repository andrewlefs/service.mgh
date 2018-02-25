<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


require_once APPPATH . 'core/EI_Controller.php';

require_once 'autoloader.php';

use GraphShare\Response;
use GraphShare\Definition;
use GraphShare\SendItemRequest;
use GraphShare\Object\Fields\FriendFields;
use GraphShare\Object\Fields\FacebookFields;
use GraphShare\Object\Friends;
use GraphShare\Object\Values\MessageCodes;
use GraphShare\Object\Values\CacheKeys;
use GraphShare\Object\Values\DBKeys;
use GraphShare\Object\Values\Db\ModelKeys;
use GraphShare\Object\Values\ShareRoles;
use GraphShare\Object\Fields\DBTableFields;
use GraphShare\Object\Values\FacebookApps;
use GraphShare\Object\Values\GameApps;
use GraphShare\Object\Fields\MoboFields;
use GraphShare\Object\Fields\UserFields;
use GraphShare\Object\Values\CachedHosts;
use GraphShare\Object\Fields\ScopeFields;
use GraphShare\Object\Values\BaseViews;
use GraphShare\Object\Values\Db\DBFuncs;
use GraphShare\Object\Fields\ApiFields;
use GraphShare\Object\Values\InviteRoles;
use GraphShare\Object\Items\ItemWarriorsOfTheWorld;
use GraphShare\Object\Fields\SendResponseFields;
use \GraphShare\Object\Values\AcceptRoles;
use GraphShare\Object\Values\MessageStrings;
use GraphShare\Object\Values\LikeRoles;

class api extends EI_Controller {

    private $fbconfig;
    private $private_author_api = "AH[KgBAI*69wZA}wp8$";

    public function __construct() {
        parent::__construct();
        $this->{ApiFields::REQUEST} = $this->getRequest();
        $this->init_settings(BaseViews::BASE_PATH_VIEW);
        if ($this->{ApiFields::REQUEST} == false) {
            $this->data["message"] = "Phiên làm việc đã hết hạn vui lòng thử lại";
            $this->render("message");
        }
        try {
            $this->load->library('facebook');
            $this->facebook->setAppId(FacebookApps::APP_FB_ID);
            $this->facebook->setAppSecret(FacebookApps::APP_FB_SECRET_KEY);
            $this->_response = new Response();
            $this->load->model(DBKeys::MODEL, DBKeys::MODEL_NAME);
            //khoi tao data request
            $this->{ApiFields::REQUEST} = $this->getRequest();
            $this->{ApiFields::IDENTIFY} = $this->{ApiFields::REQUEST}->getIdentify();
            $this->facebook->setAccessToken($this->getAccessToken());

            $this->{ApiFields::ACCESS_TOKEN} = $this->getAccessToken();
            $this->{ApiFields::GAME_INFO} = $this->getGame(GameApps::GAME_ID);
            $this->{ApiFields::LOGIN_PROFILE} = $this->getProfile($this->getKeyStoreAccessToken());
            //var_dump($this->{ApiFields::LOGIN_PROFILE});die;
            $this->{ApiFields::STATUS_LOGIN} = $this->getStatusLogin();
            $this->{ApiFields::KEY} = $this->getKeyRequest();
        } catch (Exception $exc) {
            $this->data["message"] = "Lỗi hệ thống vui lòng thử lại sao.";
            $this->render("message");
        }
    }

    public function render($view) {
        $class = new ReflectionClass(__CLASS__);
        $apiclass = new ReflectionClass('GraphShare\Object\Fields\ApiFields');
        $constants = $apiclass->getConstants();
        foreach ($constants as $key => $value) {
            if ($this->{$value} == true)
                $this->data[$value] = $this->{$value};
        }
        parent::render($view);
    }

    function index() {
        $params = $this->input->get();
        $type = "get";
        if (isset($params["func"])) {
            $func = $params["func"];
            if (isset($params["type"]) && strtolower($params["type"]) == "post") {
                $type = "post";
                $params = $this->input->post();
                $params["func"] = $func;
            }
        } else {
            $this->_response->setCode(MessageCodes::FUNC_NOT_FOUND);
            $this->_response->end();
        }
        $this->{$func}($params);
        $this->_response->end();
    }

    protected function Accepted() {
        $args = func_get_args();
        if (is_array($args[0])) {
            $data = (is_array($args[0]["data"])) ? $args[0]["data"] : json_decode($args[0]["data"], true);

            if ($this->{ApiFields::REQUEST} == false) {
                $this->_response->setCode(MessageCodes::DATA_EMPTY);
                return;
            }
            $token = $data["token"];
            unset($data["token"]);

            $verify = md5($data["id"] . $data["unique"] . $data["excludedToken"] . $data["day"] . GameApps::GAME_SECRET_KEY);
            if ($token != $verify) {
                $this->_response->setCode(MessageCodes::INVALID_TOKEN);
                return;
            }
            //check liked  
            $acceptCached = md5(CacheKeys::GRASH_ACCEPT . $data["token"] . $this->{ApiFields::IDENTIFY});
            $processData = $this->getCache($acceptCached);
            if ($processData == true) {
                $this->_response->setCode(MessageCodes::IN_PROCESS_DATA);
                return;
            }
            $this->saveCache($acceptCached, $data["token"]);


            //check liked form db store
            $cache_total = md5(CacheKeys::GRASH_ACCEPT_COUNT . $this->{ApiFields::IDENTIFY} . date("Ymd", time()));
            $acceptCountByDay = $this->getCache($cache_total);
            $total = 0;
            if ($acceptCountByDay == FALSE) {
                $rs = $this->{DBKeys::MODEL_NAME}->{DBFuncs::GET_TOTAL_ACCEPT}($this->{ApiFields::IDENTIFY});
                if ($rs == true) {
                    $total = $rs[DBTableFields::COUNT_SELECT];
                }
            } else {
                $total = $acceptCountByDay;
            }
            if ($total >= AcceptRoles::$MAX) {
                $this->_response->setCode(MessageCodes::INVALID_QUOTA);
                return;
            }
//$this->_response->setData($total);
            $transId = $data["id"];
            $orderUniqueKey = $data["unique"];
            $excludedToken = $data["excludedToken"];
            $orderDay = $data["day"];
            $name = $data["name"];
            //kiem tra giao dịch tồn tại
            //check liked form db store
            $cache_exists = md5(CacheKeys::GRASH_ACCEPT_EXISTS . $transId . $this->{ApiFields::IDENTIFY});
            $existsAcceptData = $this->getCache($cache_exists);
            if ($existsAcceptData == FALSE) {
                $existsAcceptData = $this->{DBKeys::MODEL_NAME}->{DBFuncs::GET_ACCEPT_EXISTS}($this->{ApiFields::IDENTIFY}, $transId);
                if ($existsAcceptData == true) {
                    $this->saveCache($cache_exists, $existsAcceptData);
                }
            }
            if ($existsAcceptData == true) {
                $this->_response->setCode(MessageCodes::ACCEPT_EXISTS);
                return;
            }
            try {
                $mobo = $this->{ApiFields::REQUEST}->getMoboInfo();
                $user = $this->{ApiFields::REQUEST}->getGameUserInfo();
                $iData = array(
                    DBTableFields::GAME_ID => GameApps::GAME_ID,
                    DBTableFields::UNIQUE_KEY => $this->{ApiFields::IDENTIFY},
                    DBTableFields::ORDER_UNIQUE_KEY => $orderUniqueKey,
                    DBTableFields::EXCLUDED_TOKEN => $excludedToken,
                    DBTableFields::ORDER_DAY => $orderDay,
                    DBTableFields::TRANSID => $transId,
                    DBTableFields::DAY => date("Ymd", time()),
                    DBTableFields::NAME => $name,
                    DBTableFields::CREATE_DATE => date("Y-m-d H:i:s", time())
                );
                //var_dump($iData);die;
                $iRs = $this->{DBKeys::MODEL_NAME}->insert(DBKeys::TABLE_AWARD_ACCEPT_LOGS, $iData);
                $cache_key_accept = md5(CacheKeys::GRASH_ACCEPT_LISTS . $identify);
                $this->saveCache($cache_key_accept, null);
                //var_dump($iRs);die;
                if ($iRs == true) {
                    //send item user by rule like
                    $items = $this->getItems();
                    //var_dump($items);
                    if ($items == true) {
                        //expired log
                        $wExpired = array(
                            "unique_key" => $orderUniqueKey,
                            "excluded_token" => $excludedToken,
                            "day" => $orderDay);
                        $uExpired = array("status" => 1);
                        $this->{DBKeys::MODEL_NAME}->{DBFuncs::UPDATE}(DBKeys::TABLE_EXCLUDED_LOGS, $uExpired, $wExpired);

                        $items->setMobo($this->{ApiFields::REQUEST}->getMoboInfo());
                        $items->setUser($this->{ApiFields::REQUEST}->getGameUserInfo());
                        $items->setTitle("Đồng ý lời mời Facebook (Nhận 5 " . AcceptRoles::$TITLE . ")");
                        $message = MessageStrings::Replace("Thành công lần thứ [0] trong ngày nhận 5 " . AcceptRoles::$TITLE . "", array($total + 1));
                        $items->setMailConntent($message);

                        $this->saveCache($cache_total, $total + 1);

                        $sendResult = $items->send(FacebookFields::ACCEPT, -1);
                        $wData = array(DBTableFields::ID => $iRs);
                        //var_dump($sendResult);die;
                        $uData = array(DBTableFields::RESULTS => json_encode($sendResult)
                            , DBTableFields::RESPONSE_CODE => $sendResult->{SendResponseFields::CODE}
                            , DBTableFields::MESSAGE => $message);
                        $this->{DBKeys::MODEL_NAME}->{DBFuncs::UPDATE}(DBKeys::TABLE_AWARD_ACCEPT_LOGS, $uData, $wData);
                        $this->saveCache($acceptCached, false);
                        $this->_response->setCode(MessageCodes::ACCEPT_SUCCESS);
                        return;
                    } else {
                        $this->saveCache($acceptCached, false);
                        $this->_response->setCode(MessageCodes::ACCEPT_ERROR);
                        return;
                    }
                } else {
                    $this->saveCache($acceptCached, false);
                    $this->_response->setCode(MessageCodes::ACCEPT_ERROR);
                    return;
                }
            } catch (Exception $exc) {
                $this->saveCache($acceptCached, false);
                $this->_response->setCode(MessageCodes::ACCEPT_ERROR);
                return;
            }
        }
        $this->_response->setCode(MessageCodes::CATEGORY_INVALID);
        return;
    }

    protected function getFacebookPicture($access_token, $fbid = null) {
        $fbreplace = $fbid == null ? "me" : $fbid;
        $res = $this->facebook->api("/{$fbreplace}/picture", 'GET', array(
            "access_token" => $access_token,
            "redirect" => "false",
            "type" => "normal"
        ));
        if (isset($res["data"]["url"])) {
            return $res["data"]["url"];
        }
    }

    /**
     * API Like
     * @return type
     */
    protected function CheckLiked() {

        //check liked  
        $likeCached = md5(CacheKeys::GRASH_LIKED_STATUS . $this->{ApiFields::IDENTIFY});
        if ($this->{ApiFields::REQUEST} == false) {
            $this->_response->setCode(MessageCodes::DATA_EMPTY);
            $this->saveCache($likeCached, false);
            return;
        }
        if ($likedData == true) {
            $this->_response->setCode(MessageCodes::IN_PROCESS_DATA);
            return;
        }
        $this->saveCache($likeCached, true);

        //redirect fanpage        
        if ($this->{ApiFields::GAME_INFO} == true) {
            $linkFanpage = $this->{ApiFields::GAME_INFO}[FacebookFields::FANPAGE];
            $this->_response->setData(array(FacebookFields::FANPAGE => $linkFanpage));
        }

        //check liked form db store
        $rs = $this->mgrash->getLike($this->{ApiFields::IDENTIFY});
        if ($rs == true) {
            $this->saveCache($likeCached, false);
            $this->_response->setCode(MessageCodes::LIKED_EXISTS);
            return;
        } else {
            $this->saveCache($likeCached, false);
            $this->_response->setCode(MessageCodes::LIKED_NOT_AVALID);
            return;
        }
    }

    public function getGame($gameId) {
        //get game info        
        $cache_game = md5(CacheKeys::GAME_CACHED_INFO . $gameId);
        //$gameInfo = $this->getCache($cache_game);
        if ($gameInfo == FALSE)
            $gameInfo = $this->{DBKeys::MODEL_NAME}->{DBFuncs::GET_GAME_INFO}($gameId);

        $this->saveCache($cache_game, $gameInfo);
        return $gameInfo;
    }

    public function LogError($error_code, $message, $transaction = "", $type = FacebookFields::NONE) {
        //capture log error
        $data = array(
            DBTableFields::GAME_ID => GameApps::GAME_ID,
            DBTableFields::UNIQUE_KEY => $this->{ApiFields::IDENTIFY},
            DBTableFields::TRANSACTION => $transaction,
            DBTableFields::MOBO_ID => $this->{ApiFields::REQUEST}->getMoboInfo()->{MoboFields::MOBO_ID},
            DBTableFields::MOBO_SERVICE_ID => $this->{ApiFields::REQUEST}->getMoboInfo()->{MoboFields::MSI_ID},
            DBTableFields::CHARACTER_ID => $this->{ApiFields::REQUEST}->getGameUserInfo()->{UserFields::CHARACTER_ID},
            DBTableFields::CHARACTER_NAME => $this->{ApiFields::REQUEST}->getGameUserInfo()->{UserFields::CHARACTER_NAME},
            DBTableFields::SERVER_ID => $this->{ApiFields::REQUEST}->getGameUserInfo()->{UserFields::CHARACTER_NAME},
            DBTableFields::TYPE => $type != FacebookFields::NONE ? $type : FacebookFields::SHARE,
            DBTableFields::FID => $this->{ApiFields::LOGIN_PROFILE}[DBTableFields::FBID] == true ? $this->{ApiFields::LOGIN_PROFILE}[DBTableFields::FBID] : "",
            DBTableFields::FBNAME => $this->{ApiFields::LOGIN_PROFILE}[DBTableFields::FBNAME] == true ? $this->{ApiFields::LOGIN_PROFILE}[DBTableFields::FBNAME] : "",
            DBTableFields::ERROR_CODE => $error_code,
            DBTableFields::ERROR_MESSAGE => $message,
            DBTableFields::CREATE_DATE => date("Y-m-d H:i:s", time())
        );
        $rs = $this->{DBKeys::MODEL_NAME}->insert(DBKeys::TABLE_ERROR_LOGS, $data);
    }

    public function setGame($gameId) {
        $cache_game = md5(CacheKeys::GAME_CACHED_INFO . $gameId);
        $gameInfo = $this->{DBKeys::MODEL_NAME}->{DBFuncs::GET_GAME_INFO}($gameId);
        $this->saveCache(md5(CacheKeys::GAME_CACHED_INFO . $gameId), $gameInfo);
        return $gameInfo;
    }

    /**
     * API Like
     * @return type
     */
    protected function Liked() {
        //check liked
        $likeCached = md5(CacheKeys::GRASH_LIKED_STATUS . $this->{ApiFields::IDENTIFY});
        if ($this->{ApiFields::REQUEST} == false) {
            $this->_response->setCode(MessageCodes::DATA_EMPTY);
            $this->saveCache($likeCached, false);
            return;
        }

        if ($likedData == true) {
            $this->_response->setCode(MessageCodes::IN_PROCESS_DATA);
            return;
        }
        $this->saveCache($likeCached, true);
        //var_dump($this->{ApiFields::GAME_INFO});
        //redirect fanpage        
        if ($this->{ApiFields::GAME_INFO} == true) {
            $linkFanpage = $this->{ApiFields::GAME_INFO}[FacebookFields::FANPAGE];
            $this->_response->setData(array(FacebookFields::FANPAGE => $linkFanpage));
        }
        //check liked form db store
        $rs = $this->{DBKeys::MODEL_NAME}->getLike($this->{ApiFields::IDENTIFY});
        if ($rs == true) {
            $this->saveCache($likeCached, false);
            $this->_response->setCode(MessageCodes::LIKED_EXISTS);
            return;
        }
        try {
            $mobo = $this->{ApiFields::REQUEST}->getMoboInfo();
            $user = $this->{ApiFields::REQUEST}->getGameUserInfo();
            $iData = array(
                DBTableFields::GAME_ID => GameApps::GAME_ID,
                DBTableFields::UNIQUE_KEY => $this->{ApiFields::IDENTIFY},
                DBTableFields::MOBO_ID => $mobo->{MoboFields::MOBO_ID},
                DBTableFields::MOBO_SERVICE_ID => $mobo->{MoboFields::MSI_ID},
                DBTableFields::CHARACTER_ID => $user->{UserFields::CHARACTER_ID},
                DBTableFields::CHARACTER_NAME => $user->{UserFields::CHARACTER_NAME},
                DBTableFields::SERVER_ID => $user->{UserFields::SERVER_ID},
                DBTableFields::CREATE_DATE => date("Y-m-d H:i:s", time())
            );
            //var_dump($iData);die;
            $iRs = $this->{DBKeys::MODEL_NAME}->insert(DBKeys::TABLE_LIKE_LOGS, $iData);
            if ($iRs == true) {
                //send item user by rule like
                $items = $this->getItems();
                //var_dump($items);
                if ($items == true) {
                    //$title = "Sự kiện - Like Fanpage Facebook";
                    //            $straward = "Thể Lực";
                    //            $content = "Like Fanpage Facebook nhận Thể Lực";
                    //log like 
                    $data = array(
                        DBTableFields::GAME_ID => GameApps::GAME_ID,
                        DBTableFields::UNIQUE_KEY => $this->{ApiFields::IDENTIFY},
                        DBTableFields::CREATE_DATE => date("Y-m-d H:i:s", time())
                    );

                    $rslog = $this->{DBKeys::MODEL_NAME}->{DBFuncs::INSERT}(DBKeys::TABLE_AWARD_LIKE_LOGS, $data);
                    //var_dump($rslog);die;
                    if ($rslog == true) {
                        $items->setMobo($this->{ApiFields::REQUEST}->getMoboInfo());
                        $items->setUser($this->{ApiFields::REQUEST}->getGameUserInfo());
                        $items->setTitle("Sự kiện - Like Fanpage Facebook");
                        $message = "Like Fanpage Facebook nhận " . LikeRoles::$TITLE;

                        $items->setMailConntent($message);
                        $rs = $items->send(FacebookFields::LIKED, -1);
                        $wData = array(DBTableFields::ID => $rslog);
                        //var_dump($rs);die;
                        $uData = array(DBTableFields::RESULTS => json_encode($rs)
                            , DBTableFields::RESPONSE_CODE => $rs->{SendResponseFields::CODE}
                            , DBTableFields::MESSAGE => $message);
                        $this->{DBKeys::MODEL_NAME}->{DBFuncs::UPDATE}(DBKeys::TABLE_AWARD_LIKE_LOGS, $uData, $wData);
                        $this->saveCache($likeCached, false);
                        $this->_response->setCode(MessageCodes::LIKED_SUCCESS);
                        return;
                    } else {
                        $this->saveCache($likeCached, false);
                        $this->_response->setCode(MessageCodes::LIKED_ERROR);
                        return;
                    }
                } else {
                    $this->saveCache($likeCached, false);
                    $this->_response->setCode(MessageCodes::LIKED_ERROR);
                    return;
                }
            } else {
                $this->saveCache($likeCached, false);
                $this->_response->setCode(MessageCodes::LIKED_ERROR);
                return;
            }
        } catch (Exception $exc) {
            $this->saveCache($likeCached, false);
            $this->_response->setCode(MessageCodes::LIKED_ERROR);
            return;
        }
    }

    protected function clearCached() {
        $cached_item = md5(CacheKeys::ITEMS_CACHED . GameApps::GAME_ID);
        $this->saveCache($cached_item, null);
    }

    protected function getItems() {
        //get item
        $cached_item = md5(CacheKeys::ITEMS_CACHED . GameApps::GAME_ID);
        //$items = $this->getCache($cached_item);
        if ($items == false) {
            $itemDatas = $this->{DBKeys::MODEL_NAME}->{DBFuncs::GetItems}(GameApps::GAME_ID);
            $items = new GraphShare\Object\Items\ItemMgh2();
            if ($itemDatas == true) {
                foreach ($itemDatas as $key => $value) {
                    //var_dump($value);
                    $items->add($value);
                }
            }
        }
        return $items;
    }

    protected function DownTime(/* polymorphic */) {
        $args = func_get_args();
        if (is_array($args[0])) {
            $data = (is_array($args[0]["data"])) ? $args[0]["data"] : json_decode($args[0]["data"], true);
            if (isset($data["cat"])) {
                $cat = strtolower($data["cat"]);
                switch ($cat) {
                    case FacebookFields::SHARE:
                        $keyCached = md5(CacheKeys::DOWNTIME_SHARE . $this->{ApiFields::IDENTIFY});
                        //$cacheData = $this->getCache($keyCached);
                        if ($cacheData == false) {
                            $cacheData = $this->{DBKeys::MODEL_NAME}->{DBFuncs::LAST_SHARE}($this->{ApiFields::IDENTIFY});
                            if ($cacheData == true) {
                                $currentTime = strtotime($cacheData[DBTableFields::CREATE_DATE]);
                            }
                        } else {
                            $currentTime = $cacheData;
                        }
                        $this->saveCache($keyCached, $currentTime);
                        //var_dump($cacheData);die;
                        if ($currentTime == true) {
                            $waiting = (ShareRoles::$COUNT_DOWN * 60) - (time() - (int) $currentTime);
                            //echo (time() - (int) $currentTime);
                            if ($waiting > 0) {
                                $this->_response->setData(array(ShareRoles::WAITING => $waiting));
                                $this->_response->setCode(MessageCodes::NOT_EXPIRED);
                                return $waiting;
                            } else {
                                $this->_response->setCode(MessageCodes::EXPIRED);
                                return false;
                            }
                        } else {
//                            $this->_response->setData(array(ShareRoles::WAITING => 100));
//                            $this->_response->setCode(MessageCodes::NOT_EXPIRED);
                            $this->_response->setCode(MessageCodes::EXPIRED);
                            return false;
                        }
                        break;
                    case FacebookFields::INVITE:
                        $keyCacheWaitingTime = md5(CacheKeys::DOWNTIME_INVITE . $this->{ApiFields::IDENTIFY});
                        $waitingTimeData = $this->getCache($keyCacheWaitingTime);
                        if ($waitingTimeData == false) {
                            $this->_response->setCode(MessageCodes::EXPIRED);
                            $waitingTime = (InviteRoles::$COUNT_DOWN * $data["count"] * 60);
                            $this->saveCache($keyCacheWaitingTime, json_encode(array("inviteTime" => time(), "expireTime" => $waitingTime)), $waitingTime);
                            return false;
                        }
                        $waitingTimeData = json_decode($waitingTimeData, true);
                        $waiting = (time() - (int) $waitingTimeData["inviteTime"]);
                        $expireTime = $waitingTimeData['expireTime'];
                        if ($waiting < $expireTime) {
                            $this->_response->setData(array(ShareRoles::WAITING => ($expireTime - $waiting)));
                            $this->_response->setCode(MessageCodes::NOT_EXPIRED);
                            return ($expireTime - $waiting);
                        } else {
                            $waitingTime = (InviteRoles::$COUNT_DOWN * $data["count"] * 60);
                            $this->saveCache($keyCacheWaitingTime, json_encode(array("inviteTime" => time(), "expireTime" => $waitingTime)), $waitingTime);
                            $this->_response->setCode(MessageCodes::EXPIRED);
                            return false;
                        }
                        break;
                    default:
                        $this->_response->setCode(MessageCodes::CATEGORY_INVALID);
                        return false;
                }
            }
        }
        $this->_response->setCode(MessageCodes::CATEGORY_INVALID);
        return false;
    }

    public function Logout($access_token = "") {
        $params = array();
        if ($access_token == true)
            $params["access_token"] = $access_token;
        //var_dump($params);die;
        $params["response"] = 1;
        $urllogout = $this->facebook->getLogoutUrl($params);
        //echo $urllogout;die;
        header("location: {$urllogout}");
        die;
    }

    protected function getStatusLogin() {
        //return ($this->getAccessToken() == true) ? $this->oauthAccessToken($this->getAccessToken()) : false;
        return ($this->getAccessToken() == true) ? true : false;
    }

    protected function getExcludeds() {
        $cache_excl = md5(CacheKeys::GRASH_FRIEND_EXCLUDED_BY_DAY . date("Ymd", time()) . $this->{ApiFields::IDENTIFY});
        $excluded_from_cacheds = $this->getCache($cache_excl);
        //var_dump($excluded_from_cacheds);
        if ($excluded_from_cacheds == false) {//get db
            $excluded_from_cacheds = $this->{DBKeys::MODEL_NAME}->{DBFuncs::GET_EXCLUDED}($this->{ApiFields::IDENTIFY});
            if ($excluded_from_cacheds == true) {
                foreach ($excluded_from_cacheds as $key => $value) {
                    $excludedLists[] = $value[DBTableFields::EXCLUDED_TOKEN];
                }
                $this->saveCache($cache_excl, $excludedLists);
            }
        } else {
            $excludedLists = $excluded_from_cacheds;
        }
        return $excludedLists;
    }

    public function Login() {
        //"email,publish_actions,public_profile,user_friends",
        $urlLogin = $this->facebook->getLoginUrl(array(
            FacebookFields::SCOPE => ScopeFields::getStringByScope(array(
                ScopeFields::EMAIL,
                ScopeFields::PUBLISH_ACTION,
                ScopeFields::PUBLIC_PROFILE,
                ScopeFields::USER_FRIENDS
            ))
        ));
        //echo $urlLogin;die;
        /*
        if(in_array($_SERVER['REMOTE_ADDR'],array('115.78.161.88','14.161.5.226', '118.69.76.212', '115.78.161.88', '115.78.161.124', '14.169.170.196', '115.78.161.134', '113.161.78.101'))){
            echo "<pre>";
            echo $urlLogin;
            die;
        }
        */
        header("location: {$urlLogin}");
        die;
    }

    public function setAccessToken($access_token) {
        $this->saveCache($this->getKeyStoreAccessToken(), $access_token);
    }

    public function getProfile($identity) {
        if ($identity == false)
            throw new \Exception("Identify empty this function " . __FUNCTION__);

        $cache_profile = md5(CacheKeys::GRASH_PROFILE . $identity);
        $_profile = $this->getCache($cache_profile);
        if ($_profile == FALSE)
            $_profile = $this->{DBKeys::MODEL_NAME}->getProfileAccessToken($identity);
        //store profile
        $this->saveCache($cache_profile, $_profile);
        return $_profile;
    }

    /**
     * Get Store string access token
     *
     * md5(GRASH_ACCESS_TOKEN . $this->_gameId . $this->_userInfo->mobo_service_id . $this->_userInfo->server_id);
     *
     * @return type String md5
     */
    function getKeyStoreAccessToken() {
        return md5(CacheKeys::GRASH_ACCESS_TOKEN . FacebookApps::APP_FB_ID . GameApps::GAME_ID . $this->{ApiFields::REQUEST}->getMoboInfo()->{MoboFields::MSI_ID});
    }

    public function getRequest() {
        $params = $this->input->get();
        if (isset($params["k"]) == false)
            throw new \Exception("Key not exsits " . __FUNCTION__);
        return $this->getCache($params["k"]);
    }

    public function getKeyRequest() {
        $params = $this->input->get();
        if (isset($params["k"]) == false)
            throw new \Exception("Key not exsits " . __FUNCTION__);
        return $params["k"];
    }

    public function getFriends() {
        try {

            $data = array(
                "pretty" => 0,
                "limit" => 5000,
                "access_token" => $this->getAccessToken()
            );
            $res = $this->facebook->api("/me/friends", 'GET', $data);
            if ($res == true && isset($res["data"])) {
                $respFriends = $res["data"];
                $friends = new GraphShare\Object\Friends();

                foreach ($respFriends as $key => $value) {
                    $picture = $this->getFacebookPicture($this->{ApiFields::ACCESS_TOKEN}, $value["id"]);
                    $imgid = $friends->parseImageId($picture);
                    $friend = array(
                        FriendFields::ID => $value["id"],
                        FriendFields::NAME => $value["name"],
                        FriendFields::NAME_LATIN => strtolower($this->vn_remove($value["name"])),
                        FriendFields::PICTURE => "//graph.facebook.com/" . $value["id"] . "/picture",
                        FriendFields::TOKEN => md5($imgid)
                    );
                    $friends->addData(FriendFields::ID, $friend);
                    //$friends->addData(FriendFields::ID, $friend);
                }
                return $friends;
            }
        } catch (Exception $ex) {
            var_dump($ex);
            die;
            //log error message
            //return false;
        }
    }

    public
            function getInvitableFriends() {
        try {

            $data = array(
                "pretty" => 0,
                "limit" => 5000,
                "access_token" => $this->getAccessToken()
            );
            $res = $this->facebook->api("/me/invitable_friends", 'GET', $data);
            //var_dump($res);die;
            if ($res == true && isset($res["data"])) {
                $respFriends = $res["data"];
                $friends = new GraphShare\Object\Friends();
                foreach ($respFriends as $key => $value) {
                    $imgid = $friends->parseImageId($value["picture"]["data"]["url"]);
                    $friend = array(
                        FriendFields::ID => $value["id"],
                        FriendFields::NAME => $value["name"],
                        FriendFields::NAME_LATIN => strtolower($this->vn_remove($value["name"])),
                        FriendFields::PICTURE => $value["picture"]["data"]["url"],
                        FriendFields::TOKEN => md5($imgid)
                    );
                    $friends->addData(FriendFields::ID, $friend);
                    //$friends->addData(FriendFields::ID, $friend);
                }
                return $friends;
            }
        } catch (Exception $ex) {
            //log error message
            return false;
        }
    }

    public function checkPermission($access_token) {
        try {
            $res = $this->facebook->api('/me/permissions', 'GET', array(
                'access_token' => $access_token
            ));
            if (isset($res["data"])) {
                return $res["data"];
            }
            return false;
        } catch (Exception $exc) {
            //var_dump($exc);die;
            return false;
            echo json_encode($exc->result["error"]);
        }
    }

    public function oauthAccessToken($access_token) {

        $data = array(
            "access_token" => $access_token,
            "redirect_uri" => $this->facebook->getCurrentUrl(),
            "client_id" => FacebookApps::APP_FB_ID,
            "client_secret" => FacebookApps::APP_FB_SECRET_KEY
        );

        $res = $this->facebook->makeRequest(FacebookApps::GRASH_ME_PROFILE, $data);
        $res = json_decode($res, true);
        if ($res["success"] == true) {
            return true;
        } else {
            return false;
        }
    }

    public function getAccessToken() {
        $params = $this->input->get();
        $access_token = $this->getCache($this->getKeyStoreAccessToken());
        if ($access_token == false) {
            $rs = $this->{DBKeys::MODEL_NAME}->getProfileAccessToken($this->getKeyStoreAccessToken());
            if ($rs == true) {
                $access_token = $rs["access_token"];
            }
        }
        if ($access_token == false) {
            return false;
        } else {
            $cOauth = $this->getCache("oauthAccessToken" . $this->getKeyStoreAccessToken());
            if ($cOauth == false) {
                $oauth = $this->oauthAccessToken($access_token);
                if ($oauth == false) {
                    return false;
                } else {
                    //cache data
                    $this->saveCache("oauthAccessToken" . $this->getKeyStoreAccessToken(), $oauth, 600);
                }
            }

            return $access_token;
        }
    }

    public function getCache($key) {
        $memcache = new Memcache();
        $memcache->connect(CachedHosts::MEMCACHED_HOST, CachedHosts::MEMCACHED_PORT);
        $memcache->getVersion();
        return $memcache->get($key);
    }

    function saveCache($key, $data, $cacheTime = 7200) {
        $memcache = new Memcache();
        $memcache->connect(CachedHosts::MEMCACHED_HOST, CachedHosts::MEMCACHED_PORT);
        $memcache->set($key, $data, false, $cacheTime);
    }

}
