<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once APPPATH . 'controllers/grash/api.php';

require_once 'autoloader.php';

use GraphShare\Definition;
use GraphShare\Response;
use GraphShare\Object\Values\GameApps;
use GraphShare\Object\Fields\FacebookFields;
use GraphShare\Object\Fields\ScopeFields;
use GraphShare\Object\Fields\ApiFields;
use GraphShare\Object\Values\CacheKeys;
use GraphShare\Object\Fields\DBTableFields;
use GraphShare\Object\Friends;
use GraphShare\Object\Fields\MoboFields;
use GraphShare\Object\Fields\UserFields;
use GraphShare\Object\Values\DBKeys;
use GraphShare\Object\Values\Db\DBFuncs;
use GraphShare\Object\Values\BaseLinks;
use GraphShare\Object\Values\BaseViews;
use GraphShare\Object\Values\FacebookApps;
use GraphShare\SendRequest;
use GraphShare\Object\Values\ShareRoles;
use GraphShare\Object\Values\MessageCodes;
use GraphShare\Object\Values\MessageStrings;
use GraphShare\Object\Items\ItemWarriorsOfTheWorld;
use GraphShare\Object\Fields\SendResponseFields;
use GraphShare\Object\Values\InviteRoles;
use GraphShare\Object\Values\AcceptRoles;

class home extends api {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $params = $this->input->get();
        $request = $this->getCache($params["k"]);
        if (!($request == true)) {
            $this->render("deny");
        }
    }

    function index() {
        $this->render("home", $this->data);
    }

    public function invites() {
        try {
            if ($this->{ApiFields::STATUS_LOGIN} == true) {
                $params = $this->input->get();
                if ($this->{ApiFields::GAME_INFO} == false) {
                    $this->data["message"] = "Không tìn thấy thông tin Game được triển khai.";
                    $this->render(BaseViews::MESSAGES);
                    die;
                }
                if (!isset($params["to"]) || $params["to"] == FALSE || count($params["to"]) == 0) {
                    $this->data["message"] = "Danh sách bạn được rỗng.";
                    $this->render(BaseViews::MESSAGES);
                    die;
                }

//var_dump(json_decode($params["to"], true));
// die;
                $count = count(json_decode($params["to"], true));

                $cache_excl = md5(CacheKeys::GRASH_FRIEND_EXCLUDED . date("Ymd", time()) . $this->{ApiFields::IDENTIFY});
                $excludeds = $params["excludeds"];
//var_dump($excludeds);die;
                $this->saveCache($cache_excl, $excludeds);
//echo $url;
                $unit = time();
                $responseData = array(
                    "k" => $this->getKeyRequest(),
                    "unit" => $unit,
                    "c" => $count,
                    "token" => md5($this->getKeyRequest() . $unit . $count . GameApps::GAME_SECRET_KEY)
                );
                $config = array(
                    "access_token" => $this->getAccessToken(),
                    "message" => htmlspecialchars($this->{ApiFields::GAME_INFO}["message"]),
                    "description" => htmlspecialchars($this->{ApiFields::GAME_INFO}["description"]),
                    "title" => htmlspecialchars($this->{ApiFields::GAME_INFO}["title"]),
                    "app_id" => FacebookApps::APP_FB_ID,
                    "to" => $params["to"],
                    "display" => "popup",
                    "redirect_uri" => BaseLinks::BASE_HOME_URI . "/inv_resp?" . http_build_query($responseData),
                );
                header("location:" . FacebookApps::GRASH_APPREQUEST_URL . "?" . http_build_query($config));
                die;
            } else {
                $this->data["message"] = "Bạn chưa đăng nhập Facebook, đăng nhập trước khi thao tác.";
                $this->render(BaseViews::MESSAGES);
            }
        } catch (Exception $exc) {
//            var_dump($exc);
//            die;
//echo json_encode($exc->result["error"]);
            $this->data["message"] = "Thông tin tham gia sự kiện không đúng vui lòng thử lại.";
            $this->render(BaseViews::MESSAGES);
            die;
        }
    }

    public function inv_resp() {
        try {
            if ($this->{ApiFields::STATUS_LOGIN} == true) {
                $params = $this->input->get();
                $request_id = 0;
                if (isset($params["request"])) {
                    $request_id = $params["request"];
                }
                $tos = $params["to"];
                $k = $params["k"];
                $unit = $params["unit"];
                $count = $params["c"];
                $token = $params["token"];

                $verify = md5($k . $unit . $count . GameApps::GAME_SECRET_KEY);

                if ($verify == $token) {
                    if (isset($params["error_code"])) {
//capture log error
                        $this->LogError($params["error_code"], $params["error_message"], $unit, FacebookFields::INVITE);
                        header("location: " . BaseLinks::BASE_HOME_URI . "?k=" . $this->getKeyRequest());
                        die;
                    } else {
                        $this->data["form"] = "friends";
                        $cach_transid = $unit . $this->{ApiFields::IDENTIFY};
                        $invaild = $this->getCache($cach_transid);
                        if ($invaild == true) {
                            $this->data["message"] = "Giao dịch đã được xử lý trước đó.";
                            $this->render(BaseViews::MESSAGES);
                            die;
                        }
                        $this->saveCache($cach_transid, $unit);
//store excluded friend facebook
                        $cache_excl = md5(CacheKeys::GRASH_FRIEND_EXCLUDED . date("Ymd", time()) . $this->{ApiFields::IDENTIFY});
                        $excludeds = $this->getCache($cache_excl);
//var_dump($excludeds);die;
                        if ($excludeds == true) {
                            $excludeds = json_decode($excludeds, true);
                            foreach ($excludeds as $key => $value) {
                                $data[] = array(
                                    DBTableFields::GAME_ID => GameApps::GAME_ID,
                                    DBTableFields::UNIQUE_KEY => $this->{ApiFields::IDENTIFY},
                                    DBTableFields::TRANSACTION => $unit,
                                    DBTableFields::MOBO_ID => $this->{ApiFields::REQUEST}->getMoboInfo()->{MoboFields::MOBO_ID},
                                    DBTableFields::MOBO_SERVICE_ID => $this->{ApiFields::REQUEST}->getMoboInfo()->{MoboFields::MSI_ID},
                                    DBTableFields::CHARACTER_ID => $this->{ApiFields::REQUEST}->getGameUserInfo()->{UserFields::CHARACTER_ID},
                                    DBTableFields::CHARACTER_NAME => $this->{ApiFields::REQUEST}->getGameUserInfo()->{UserFields::CHARACTER_NAME},
                                    DBTableFields::SERVER_ID => $this->{ApiFields::REQUEST}->getGameUserInfo()->{UserFields::SERVER_ID},
                                    DBTableFields::EXCLUDED_TOKEN => $value,
                                    DBTableFields::DAY => date("Ymd", time()),
                                    DBTableFields::LINK_PICTURE => $this->{ApiFields::LOGIN_PROFILE}[DBTableFields::LINK_PICTURE],
                                    DBTableFields::NAME => $this->{ApiFields::LOGIN_PROFILE}[DBTableFields::FBNAME],
                                    DBTableFields::CREATE_DATE => date("Y-m-d H:i:s", time())
                                );
                            }
                            if ($data == true) {
                                //var_dump($data);die;
                                $rs = $this->{DBKeys::MODEL_NAME}->insert_batch(DBKeys::TABLE_EXCLUDED_LOGS, $data);
                                if ($rs > 0) {
                                    //reset cache excluded
                                    $cache_excl = md5(CacheKeys::GRASH_FRIEND_EXCLUDED_BY_DAY . date("Ymd", time()) . $this->{ApiFields::IDENTIFY});
                                    $this->saveCache($cache_excl, null);
                                }
                            }
                        }
                        $data = NULL;

                        $resultLastInvites = $this->DownTime(array("data" => array("cat" => FacebookFields::INVITE, "count" => count($tos))));
                        //var_dump($resultLastInvites);die;
                        foreach ($tos as $key => $value) {
                            $data[] = array(
                                DBTableFields::GAME_ID => GameApps::GAME_ID,
                                DBTableFields::UNIQUE_KEY => $this->{ApiFields::IDENTIFY},
                                DBTableFields::TRANSACTION => $unit,
                                DBTableFields::MOBO_ID => $this->{ApiFields::REQUEST}->getMoboInfo()->{MoboFields::MOBO_ID},
                                DBTableFields::MOBO_SERVICE_ID => $this->{ApiFields::REQUEST}->getMoboInfo()->{MoboFields::MSI_ID},
                                DBTableFields::CHARACTER_ID => $this->{ApiFields::REQUEST}->getGameUserInfo()->{UserFields::CHARACTER_ID},
                                DBTableFields::CHARACTER_NAME => $this->{ApiFields::REQUEST}->getGameUserInfo()->{UserFields::CHARACTER_NAME},
                                DBTableFields::SERVER_ID => $this->{ApiFields::REQUEST}->getGameUserInfo()->{UserFields::SERVER_ID},
                                DBTableFields::FID => $this->{ApiFields::LOGIN_PROFILE}[DBTableFields::FBID],
                                DBTableFields::FBNAME => $this->{ApiFields::LOGIN_PROFILE}[DBTableFields::FBNAME],
                                DBTableFields::TOFID => $value,
                                DBTableFields::DAY => date("Ymd", time()),
                                DBTableFields::FROM_TOKEN => $this->{ApiFields::LOGIN_PROFILE}[DBTableFields::TOKEN_PICTURE],
                                DBTableFields::REQUEST_ID => $request_id,
                                DBTableFields::CREATE_DATE => date("Y-m-d H:i:s", time())
                            );
                        }

                        $rs = $this->{DBKeys::MODEL_NAME}->insert_batch(DBKeys::TABLE_INVITE_LOGS, $data);

                        if ($rs == true) {
//send item cho sự kiện invite
//expired transaction id 

                            $cache_accept_key = md5(CacheKeys::GRASH_ACCEPT_LISTS . $this->{ApiFields::IDENTIFY});
                            $this->saveCache($cache_accept_key, null);
                            //var_dump($resultLastInvites);die;
                            if ($resultLastInvites === false && is_bool($resultLastInvites)) {
                                //var_dump($resultLastInvites);die;
                                //check rule 
                                //count total share
                                $isSendItem = false;
                                $curCount = 0;
                                if (InviteRoles::$MAX > 0) {
                                    $cacehed_quota = md5(CacheKeys::INVITE_QUOTA . $this->{ApiFields::IDENTIFY} . date("Ymd", time()));
                                    $curCount = $this->getCache($cacehed_quota);
                                    if ($curCount == false) {
                                        $mData = $this->{DBKeys::MODEL_NAME}->{DBFuncs::GET_COUNT_INVITE_BY_DAY}($this->{ApiFields::IDENTIFY});
                                        if ($mData == true) {
                                            $curCount = $mData[DBTableFields::COUNT_SELECT];
                                        }
                                    }
                                    if ($curCount < InviteRoles::$MAX) {
                                        $isSendItem = true;
                                    }
                                } else {
                                    $isSendItem = true;
                                }
//var_dump($isSendItem); 
                                //var_dump($isSendItem);
                                if ($isSendItem == true) {
//get item
                                    $items = $this->getItems();
                                    if ($items == true) {
//rule vi send vi tri chua apply
//$item = $items->get(FacebookFields::SHARE);
//log db
                                        $countInviteSuccess = count($tos);

                                        $exchangeCount = InviteRoles::$MAX - $curCount;
                                        $coefficient = 5;
                                        if (($countInviteSuccess * 5) >= $exchangeCount)
                                            $itemCount = $exchangeCount / $coefficient;
                                        else {
                                            $itemCount = $countInviteSuccess;
                                        }

                                        $data = array(
                                            DBTableFields::GAME_ID => GameApps::GAME_ID,
                                            DBTableFields::UNIQUE_KEY => $this->{ApiFields::IDENTIFY},
                                            DBTableFields::TRANSID => $unit,
                                            DBTableFields::ITEMS => $this->{ApiFields::REQUEST}->getMoboInfo()->{MoboFields::MOBO_ID},
                                            DBTableFields::TOTAL_FRIEND => $countInviteSuccess,
                                            DBTableFields::ITEM_COUNT => $itemCount * $coefficient,
                                            DBTableFields::DAY => date("Ymd", time()),
                                            DBTableFields::CREATE_DATE => date("Y-m-d H:i:s", time()),
                                            DBTableFields::STATUS => 1
                                        );
                                        $rslog = $this->{DBKeys::MODEL_NAME}->{DBFuncs::INSERT}(DBKeys::TABLE_AWARD_INVITE_LOGS, $data);
                                        if ($rslog == true) {
                                            $items->setMobo($this->{ApiFields::REQUEST}->getMoboInfo());
                                            $items->setUser($this->{ApiFields::REQUEST}->getGameUserInfo());
                                            $items->setTitle("Mời bạn Facebook (Nhận " . InviteRoles::$SHORT_TITLE . ")");
                                            $message = MessageStrings::Replace("Mời thành công [0] bạn nhận [1] " . InviteRoles::$SHORT_TITLE . ", đã nhận [2] " . InviteRoles::$SHORT_TITLE . ", nhận tối đa [3]/ngày."
                                                            , array($countInviteSuccess, ($itemCount * $coefficient), $curCount + ($itemCount * $coefficient), InviteRoles::$MAX));
                                            $items->setMailConntent($message);
                                            $items->setOveriteCount(($itemCount * $coefficient));
                                            $rs = $items->send(FacebookFields::INVITE, -1);

                                            $this->saveCache($cacehed_quota, $curCount + ($itemCount * $coefficient));
                                            $wData = array(DBTableFields::ID => $rslog);
//var_dump($rs);die;
                                            $uData = array(DBTableFields::RESULTS => json_encode($rs)
                                                , DBTableFields::RESPONSE_CODE => $rs->{SendResponseFields::CODE}
                                                , DBTableFields::MESSAGE => $message);
                                            $this->{DBKeys::MODEL_NAME}->{DBFuncs::UPDATE}(DBKeys::TABLE_AWARD_INVITE_LOGS, $uData, $wData);
                                            $this->data["message"] = $message;

                                            $this->render(BaseViews::MESSAGES);
                                            die;
                                        } else {
                                            $this->LogError(0, MessageStrings::ALERT_SHARE_SUCCES_ERROR_AWARD, $unit);
                                            $this->data["message"] = MessageStrings::ALERT_SHARE_DUPLICATE;
                                            $this->render(BaseViews::MESSAGES);
                                            die;
                                        }
                                    } else {
                                        $this->LogError(0, MessageStrings::ALERT_SHARE_SUCCES_ERROR_AWARD, $unit);
//$this->saveCache($cach_transid, null);
                                        $this->data["message"] = MessageStrings::ALERT_SHARE_SUCCES_ERROR_AWARD;
                                        $this->render(BaseViews::MESSAGES);
                                        die;
                                    }
                                } else {
//expired transaction id
//$this->saveCache($cach_transid, null);
                                    $this->data["message"] = MessageStrings::ALERT_SHARE_SUCCES_NOT_AWARD;
                                    $this->render(BaseViews::MESSAGES);
                                    die;
                                }
                            } else {
//$this->saveCache($cach_transid, null);
                                $this->data["message"] = MessageStrings::Replace(MessageStrings::ALERT_SHARE_COUNTDOWN, array((int) ($resultLastInvites / 60 / 60) . ":" . (int) ($resultLastInvites / 60) . ":" . (int) ($resultLastInvites % 60)));
//"Chia sẽ nhận quà thành công. Tuy nhiên thời gian nhận quà lượt kế tiếp còn " . date("H:i:s", $rs) . "s.";
                                $this->render(BaseViews::MESSAGES);
                                die;
                            }
                        } else {
//$this->saveCache($cach_transid, null);
                            $this->data["message"] = "Sự kiện này đã hoàn thành trước đó, vui lòng thử lại.";
                            $this->render(BaseViews::MESSAGES);
                            die;
                        }
                    }
                } else {
                    $this->data["message"] = "Thông tin tham gia sự kiện không đúng vui lòng thử lại.";
                    $this->render(BaseViews::MESSAGES);
                    die;
                }
            } else {
                $this->data["message"] = "Bạn chưa đăng nhập Facebook, đăng nhập trước khi thao tác.";
                $this->render(BaseViews::MESSAGES);
            }
        } catch (Exception $exc) {
            var_dump($exc);
            die;
//echo json_encode($exc->result["error"]);
            $this->data["message"] = "Thông tin tham gia sự kiện không đúng vui lòng thử lại.";
            $this->render(BaseViews::MESSAGES);
            die;
        }
    }

    public function feed() {
        try {
            if ($this->{ApiFields::STATUS_LOGIN} == true) {
//check limit share by day
                $cachedShareLimit = md5(CacheKeys::SHARE_LIMIT . $this->{ApiFields::IDENTIFY});
                if (ShareRoles::$LIMIT == true) {
                    $cData = $this->getCache($cachedShareLimit);
                    if ($cData == false) {
                        $cData = $this->{DBKeys::MODEL_NAME}->{DBFuncs::GetTotalShareByDay}($this->{ApiFields::IDENTIFY});
//store total share day
                        if ($cData == true) {
                            $this->saveCache($cachedShareLimit, $cData);
                        }
                    }
                    if ($cData == true && ShareRoles::$MAXIMUM > 0 && $cData[DBTableFields::COUNT_SELECT] > ShareRoles::$MAXIMUM) {
                        $this->data["message"] = MessageStrings::Replace(MessageStrings::LIMIT_SHARE, array(ShareRoles::$MAXIMUM));
                        $this->render(BaseViews::MESSAGES);
                        die;
                    }
                }
                $key = md5(CacheKeys::GRASH_SHARE_DATA . GameApps::GAME_ID);
                $data = $this->getCache($key);
                if ($data == FALSE) {
                    $data = $this->{DBKeys::MODEL_NAME}->getDataShare(GameApps::GAME_ID);
                    $this->saveCache($key, $data);
                }
//var_dump($data);die;
                $this->data["message"] = "Tạm thời đang bảo trì";
                if ($data == false) {
                    $this->render(BaseViews::MESSAGES);
                    die;
                }
                $count = count($data);
                $ran = rand(0, $count - 1);

                $filterRow = $data[$ran];
//echo $url;
                $unit = time();
                $responseData = array(
                    "k" => $this->getKeyRequest(),
                    "unit" => $unit,
                    "id" => $filterRow["id"],
                    "token" => md5($this->getKeyRequest() . $unit . $filterRow["id"] . GameApps::GAME_SECRET_KEY)
                );
                $config = array(
                    "picture" => $filterRow["photo"],
                    "description" => htmlspecialchars($filterRow["message"]),
                    "app_id" => FacebookApps::APP_FB_ID,
                    "name" => htmlspecialchars($filterRow["name"]),
                    "display" => "popup",
                    "caption" => htmlspecialchars($filterRow["caption"]),
                    "link" => htmlspecialchars($filterRow["link"]),
                    "redirect_uri" => BaseLinks::BASE_HOME_URI . "/feed_resp?" . http_build_query($responseData),
                );
                header("location:" . FacebookApps::GRASH_URL_FEED_DIALOG . "?" . http_build_query($config));
                die;
            } else {
                $this->data["message"] = "Bạn chưa đăng nhập Facebook, đăng nhập trước khi thao tác.";
                $this->render(BaseViews::MESSAGES);
            }
        } catch (Exception $exc) {
//            var_dump($exc);
//            die;
//echo json_encode($exc->result["error"]);
            $this->data["message"] = "Thông tin tham gia sự kiện không đúng vui lòng thử lại.";
            $this->render(BaseViews::MESSAGES);
            die;
        }
    }

    public function feed_resp() {
        try {
            if ($this->{ApiFields::STATUS_LOGIN} == true) {
                $params = $this->input->get();
                $post_id = 0;
                if (isset($params["post_id"])) {
                    $post_id = $params["post_id"];
                }
                $k = $params["k"];
                $unit = $params["unit"];
                $share_id = $params["id"];
                $token = $params["token"];
                $verify = md5($k . $unit . $share_id . GameApps::GAME_SECRET_KEY);

                if ($verify == $token) {
                    if (isset($params["error_code"])) {
//capture log error
                        $this->LogError($params["error_code"], $params["error_message"], $unit, FacebookFields::SHARE);
                        header("location: " . BaseLinks::BASE_HOME_URI . "?k=" . $this->getKeyRequest());
                        die;
                    } else {

                        $cach_transid = $unit . $this->{ApiFields::IDENTIFY};
                        $invaild = $this->getCache($cach_transid);
                        if ($invaild == true) {
                            $this->data["message"] = "Giao dịch đã được xử lý trước đó.";
                            $this->render(BaseViews::MESSAGES);
                            die;
                        }
                        $this->saveCache($cach_transid, $unit);
                        $data = array(
                            DBTableFields::GAME_ID => GameApps::GAME_ID,
                            DBTableFields::UNIQUE_KEY => $this->{ApiFields::IDENTIFY},
                            DBTableFields::TRANSACTION => $unit,
                            DBTableFields::MOBO_ID => $this->{ApiFields::REQUEST}->getMoboInfo()->{MoboFields::MOBO_ID},
                            DBTableFields::MOBO_SERVICE_ID => $this->{ApiFields::REQUEST}->getMoboInfo()->{MoboFields::MSI_ID},
                            DBTableFields::CHARACTER_ID => $this->{ApiFields::REQUEST}->getGameUserInfo()->{UserFields::CHARACTER_ID},
                            DBTableFields::CHARACTER_NAME => $this->{ApiFields::REQUEST}->getGameUserInfo()->{UserFields::CHARACTER_NAME},
                            DBTableFields::SERVER_ID => $this->{ApiFields::REQUEST}->getGameUserInfo()->{UserFields::SERVER_ID},
                            DBTableFields::FID => $this->{ApiFields::LOGIN_PROFILE}[DBTableFields::FBID],
                            DBTableFields::FBNAME => $this->{ApiFields::LOGIN_PROFILE}[DBTableFields::FBNAME],
                            DBTableFields::POST_ID => $post_id,
                            DBTableFields::DAY => date("Ymd", time()),
                            DBTableFields::CREATE_DATE => date("Y-m-d H:i:s", time())
                        );
                        $rs = $this->{DBKeys::MODEL_NAME}->insert(DBKeys::TABLE_SHARE_LOGS, $data);
                        if ($rs == true) {
//send item cho sự kiện share
                            $rs = $this->DownTime(array("data" => FacebookFields::SHARE));
                            if ($rs == false) {
//check rule 
//count total share
                                $isSendItem = false;
                                $curCount = 0;
                                if (ShareRoles::$MAX > 0) {
                                    $cacehed_quota = md5(CacheKeys::SHARE_QUOTA . $this->{ApiFields::IDENTIFY} . date("Ymd", time()));
                                    $curCount = $this->getCache($cacehed_quota);
                                    //var_dump($curCount);
                                    if ($curCount == false) {
                                        $mData = $this->{DBKeys::MODEL_NAME}->{DBFuncs::GET_COUNT_SHARE_BY_DAY}($this->{ApiFields::IDENTIFY});
                                        if ($mData == true) {
                                            $curCount = $mData[DBTableFields::COUNT_SELECT];
                                        }
                                    }
                                    //var_dump($mData);
                                    if ($curCount < ShareRoles::$MAX) {
                                        $isSendItem = true;
                                    }
                                } else {
                                    $isSendItem = true;
                                }
//var_dump($isSendItem);
                                //var_dump($isSendItem);
                                if ($isSendItem == true) {
//get item
                                    $items = $this->getItems();
                                    if ($items == true) {
//rule vi send vi tri chua apply
//$item = $items->get(FacebookFields::SHARE);
//log db
                                        $data = array(
                                            DBTableFields::GAME_ID => GameApps::GAME_ID,
                                            DBTableFields::UNIQUE_KEY => $this->{ApiFields::IDENTIFY},
                                            DBTableFields::TRANSID => $unit,
                                            DBTableFields::ITEMS => $this->{ApiFields::REQUEST}->getMoboInfo()->{MoboFields::MOBO_ID},
                                            DBTableFields::DAY => date("Ymd", time()),
                                            DBTableFields::CREATE_DATE => date("Y-m-d H:i:s", time()),
                                            DBTableFields::STATUS => 1
                                        );
                                        $rslog = $this->{DBKeys::MODEL_NAME}->{DBFuncs::INSERT}(DBKeys::TABLE_AWARD_SHARE_LOGS, $data);
                                        if ($rslog == true) {
                                            $items->setMobo($this->{ApiFields::REQUEST}->getMoboInfo());
                                            $items->setUser($this->{ApiFields::REQUEST}->getGameUserInfo());
                                            $items->setTitle(ShareRoles::$TITLE);
                                            $message = MessageStrings::Replace(ShareRoles::$MESSAGE, array($curCount + 1, ShareRoles::$MAX));
                                            $items->setMailConntent($message);
                                            $rs = $items->send(FacebookFields::SHARE, -1);

                                            $this->saveCache($cacehed_quota, $curCount + 1);
                                            $wData = array(DBTableFields::ID => $rslog);
//var_dump($rs);die;
                                            $uData = array(DBTableFields::RESULTS => json_encode($rs)
                                                , DBTableFields::RESPONSE_CODE => $rs->{SendResponseFields::CODE}
                                                , DBTableFields::MESSAGE => $message);
                                            $this->{DBKeys::MODEL_NAME}->{DBFuncs::UPDATE}(DBKeys::TABLE_AWARD_SHARE_LOGS, $uData, $wData);
                                            $this->data["message"] = $message;
                                            $this->render(BaseViews::MESSAGES);
                                            die;
                                        } else {
                                            $this->LogError(0, MessageStrings::ALERT_SHARE_SUCCES_ERROR_AWARD, $unit);
                                            $this->data["message"] = MessageStrings::ALERT_SHARE_DUPLICATE;
                                            $this->render(BaseViews::MESSAGES);
                                            die;
                                        }
                                    } else {
                                        $this->LogError(0, MessageStrings::ALERT_SHARE_SUCCES_ERROR_AWARD, $unit);
//$this->saveCache($cach_transid, null);
                                        $this->data["message"] = MessageStrings::ALERT_SHARE_SUCCES_ERROR_AWARD;
                                        $this->render(BaseViews::MESSAGES);
                                        die;
                                    }
                                } else {
//expired transaction id
//$this->saveCache($cach_transid, null);
                                    $this->data["message"] = MessageStrings::ALERT_SHARE_SUCCES_NOT_AWARD;
                                    $this->render(BaseViews::MESSAGES);
                                    die;
                                }
                            } else {
//$this->saveCache($cach_transid, null);
                                $this->data["message"] = MessageStrings::Replace(MessageStrings::ALERT_SHARE_COUNTDOWN, array(date("H:i:s", $rs)));
//"Chia sẽ nhận quà thành công. Tuy nhiên thời gian nhận quà lượt kế tiếp còn " . date("H:i:s", $rs) . "s.";
                                $this->render(BaseViews::MESSAGES);
                                die;
                            }
                        } else {
//$this->saveCache($cach_transid, null);
                            $this->data["message"] = "Mã giao dịch đã hết hạn, vui lòng thử lại.";
                            $this->render(BaseViews::MESSAGES);
                            die;
                        }
                    }
                } else {
                    $this->data["message"] = "Thông tin tham gia sự kiện không đúng vui lòng thử lại.";
                    $this->render(BaseViews::MESSAGES);
                    die;
                }
            } else {
                $this->data["message"] = "Bạn chưa đăng nhập Facebook, đăng nhập trước khi thao tác.";
                $this->render(BaseViews::MESSAGES);
            }
        } catch (Exception $exc) {
//            var_dump($exc);
//            die;
//echo json_encode($exc->result["error"]);
            $this->data["message"] = "Thông tin tham gia sự kiện không đúng vui lòng thử lại.";
            $this->render(BaseViews::MESSAGES);
            die;
        }
    }

    public function Logout() {
        $params = $this->input->get();

        if (isset($params["response"])) {
            $this->facebook->destroySession();
            $cache_key = md5(CacheKeys::GRASH_FRIEND_LISTS . $this->{ApiFields::IDENTIFY});
            $this->saveCache($cache_key, NULL);
            $profile = $this->getProfile($this->getKeyStoreAccessToken());
            $this->{DBKeys::MODEL_NAME}->{DBFuncs::DELETE}(DBKeys::TABLE_LOGIN, array(DBTableFields::ACCESS_KEY => $this->getKeyStoreAccessToken()));
            if ($profile == true) {
                $profile[DBTableFields::UPDATE_DATE] = date("Y-m-d H:i:s", time());
                //var_dump($profile);die;
                $this->{DBKeys::MODEL_NAME}->{DBFuncs::INSERT}(DBKeys::TABLE_LOGOUT, $profile);
//die;}
            }
            $cache_profile = md5(CacheKeys::GRASH_PROFILE . $this->getKeyStoreAccessToken());
            $this->saveCache($cache_profile, NULL);
            //var_dump($this->getCache($cache_profile));die;
            $this->saveCache($this->getKeyStoreAccessToken(), null);
            $key = $params["k"];
            header("location: " . BaseLinks::BASE_HOME_URI . "?k=" . $key);
            die;
        }
        $access_token = $this->getAccessToken();
        //var_dump($access_token);
        parent::Logout($access_token);
    }

    public function Login() {
        $params = $this->input->get();
        if (isset($params["code"]) && isset($params["state"])) {
//var_dump($this->facebook);die;
            $access_token = $this->facebook->getAccessTokenFromCode($params["code"]);

            /*
            if(in_array($_SERVER['REMOTE_ADDR'],array('115.78.161.88','14.161.5.226', '118.69.76.212', '115.78.161.88', '115.78.161.124', '14.169.170.196', '115.78.161.134', '113.161.78.101'))){
                echo "<pre>";
                print_r($params);
                var_dump($access_token);die;
            }
            */


//store access token
            if ($access_token == true) {

                $this->facebook->setAccessToken($access_token);
                $user = $this->facebook->getUserInfoFromAccessToken();
//var_dump($user);die;
                $gameinfo = json_decode($params["game_info"], true);

                $linkPicture = $this->getFacebookPicture($access_token);
                $imgid = Friends::parseImageId($linkPicture);

                $mobo = $this->{ApiFields::REQUEST}->getMoboInfo();

                $idata = array(
                    DBTableFields::UNIQUE_KEY => $this->{ApiFields::IDENTIFY},
                    DBTableFields::ACCESS_KEY => $this->getKeyStoreAccessToken(),
                    DBTableFields::GAME_ID => GameApps::GAME_ID,
                    DBTableFields::MOBO_ID => $this->{ApiFields::REQUEST}->getMoboInfo()->{MoboFields::MOBO_ID},
                    DBTableFields::MOBO_SERVICE_ID => $this->{ApiFields::REQUEST}->getMoboInfo()->{MoboFields::MSI_ID},
                    DBTableFields::SERVER_ID => $this->{ApiFields::REQUEST}->getGameUserInfo()->{UserFields::SERVER_ID},
                    DBTableFields::CHARACTER_ID => $this->{ApiFields::REQUEST}->getGameUserInfo()->{UserFields::CHARACTER_ID},
                    DBTableFields::CHARACTER_NAME => $this->{ApiFields::REQUEST}->getGameUserInfo()->{UserFields::CHARACTER_NAME},
                    DBTableFields::FBID => $user["id"],
                    DBTableFields::FBNAME => $user["name"],
                    DBTableFields::CREATE_DATE => date("Y-m-d H:i:s", time()),
                    DBTableFields::ACCESS_TOKEN => $access_token,
                    DBTableFields::LINK_PICTURE => $linkPicture,
                    DBTableFields::TOKEN_PICTURE => md5($imgid)
                );
                $this->{DBKeys::MODEL_NAME}->{DBFuncs::ON_DUPLICATE_LOGIN}(DBKeys::TABLE_LOGIN, $idata);
//store cache access token                
                $this->saveCache($this->getKeyStoreAccessToken(), $access_token);
                header("location: " . BaseLinks::BASE_HOME_URI . "?k=" . $params["k"]);
                die;
            }
//die;
        }
        parent::Login();
    }

    public function friends() {
        try {

            if ($this->{ApiFields::STATUS_LOGIN} == true) {
                $identify = $this->{ApiFields::IDENTIFY};

                $cache_key = md5(CacheKeys::GRASH_FRIEND_LISTS . $identify);
                $friendLists = $this->getCache($cache_key);
//var_dump($friendLists);die;
                if ($friendLists == false) {
                    $friends = $this->getFriends()->getChangeData();
//var_dump($friens);die;
					if($this->getInvitableFriends() != false){
						$invitableFriends = $this->getInvitableFriends()->getChangeData();
					}
                    if ($friends == true) {
                        $friendLists = $friends;
                    }
                    if ($friendLists == true && $invitableFriends == true)
                        $friendLists = array_merge($invitableFriends, $friendLists);
                    else if ($friendLists == false && $invitableFriends == true)
                        $friendLists = $invitableFriends;
//var_dump($friendLists);die;
                    $this->saveCache($cache_key, $friendLists);
                }
//xu ly excluded friend invite in day  
                $excludedLists = $this->getExcludeds();
                //cast data excluded
                //var_dump($excludedLists);
                //die;
                $this->{ApiFields::EXCLUDES} = $excludedLists;
                $this->{ApiFields::FRIEND_LISTS} = $friendLists;

                $this->render(BaseViews::FRIENDS);
            } else {
                $this->data["message"] = "Bạn chưa đăng nhập Facebook, đăng nhập trước khi thao tác.";
                $this->render(BaseViews::MESSAGES);
            }
        } catch (Exception $exc) {
            $this->data["message"] = "Lỗi hệ thống vui lòng thử lại sao.";
            $this->render(BaseViews::MESSAGES);
            die;
        }
    }

    public function accept() {
        try {

            if ($this->{ApiFields::STATUS_LOGIN} == true) {
                $identify = $this->{ApiFields::IDENTIFY};

                $cache_key = md5(CacheKeys::GRASH_ACCEPT_LISTS . $identify . date("Ymd", time()));
                $acceptLists = $this->getCache($cache_key);

                if ($acceptLists == false) {
                    $acceptLists = $this->{DBKeys::MODEL_NAME}->{DBFuncs::GET_LIST_ACCEPT}($this->{ApiFields::LOGIN_PROFILE}[DBTableFields::TOKEN_PICTURE], AcceptRoles::LIMIT_DAY);
                    $this->saveCache($cache_key, $acceptLists);
                }
                $this->{ApiFields::ACCEPT_LISTS} = $acceptLists;
                $this->render(BaseViews::ACCEPT);
            } else {
                $this->data["message"] = "Bạn chưa đăng nhập Facebook, đăng nhập trước khi thao tác.";
                $this->render(BaseViews::MESSAGES);
            }
        } catch (Exception $exc) {
            $this->data["message"] = "Lỗi hệ thống vui lòng thử lại sao.";
            $this->render(BaseViews::MESSAGES);
            die;
        }
    }

    public function intro() {
        $this->render(BaseViews::INTRO);
    }

    public function awardlist() {
        $this->data["likeLists"] = $this->{DBKeys::MODEL_NAME}->{DBFuncs::GET_AWARD_LIST}(DBKeys::TABLE_AWARD_LIKE_LOGS, $this->{ApiFields::IDENTIFY}, 3);
        $this->data["shareLists"] = $this->{DBKeys::MODEL_NAME}->{DBFuncs::GET_AWARD_LIST}(DBKeys::TABLE_AWARD_SHARE_LOGS, $this->{ApiFields::IDENTIFY}, 3);
        //var_dump($this->data);
        $this->data["inviteLists"] = $this->{DBKeys::MODEL_NAME}->{DBFuncs::GET_AWARD_LIST}(DBKeys::TABLE_AWARD_INVITE_LOGS, $this->{ApiFields::IDENTIFY}, 3);
        $this->data["acceptLists"] = $this->{DBKeys::MODEL_NAME}->{DBFuncs::GET_AWARD_LIST}(DBKeys::TABLE_AWARD_ACCEPT_LOGS, $this->{ApiFields::IDENTIFY}, 3);

        $this->render(BaseViews::AWARDLIST);
    }

}
