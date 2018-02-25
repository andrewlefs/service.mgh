<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MigEvents;

use MigEvents\ControllerInterface;
use MigEvents\Object\ModelObject;
use MigEvents\Models\ModelObjectInterface;
use MigEvents\Http\Receiver;
use MigEvents\Object\Fields\HeaderField;
use MigEvents\Models\TabModels;
use MigEvents\Models\MainModels;
use MigEvents\MemcacheObject;
use MigEvents\Http\Client\GraphClient;
use MigEvents\Http\Client\FacebookClient;
use MigEvents\Http\Client\GoogleClient;
use MigEvents\Enum\Language;
use MigEvents\Http\Client\GApiClient;
use MigEvents\Authorize;
use MigEvents\Object\Values\ResultObject;

class Controller extends \CI_Controller {

    /**
     * @var Api
     */
    protected static $instance;

    /**
     *
     * @var Receiver 
     */
    protected $receiver;

    /**
     *
     * @var string
     */
    protected $appId;

    /**
     *
     * @var integer 
     */
    protected $dbConfig;

    /**
     *
     * @var string 
     */
    protected $pathRoot;

    /**
     *
     * @var array $data 
     */
    protected $data;

    /**
     *
     * @var Memcached 
     */
    protected $memcached;

    /**
     * Độ lệch time server
     * @var int 
     */
    protected $athwartTimeSlice;
    static $Language = array();

    /**
     *
     * @var MigEvents\Http\Client\GraphClient 
     */
    protected $graphClient;
    protected $gapiApplication;
    protected $gapiClient;
    protected $authorize;

    public function __construct() {
        parent::__construct();
        $this->setDbConfig(array('db' => 'system_info', 'type' => 'slave'));
        $this->bindingLanguage();
        static::setInstance($this);
        //$this->bindingLanguage();
    }

    public function bindingLanguage() {
        if (self::$Language == null) {
            $params = $this->prepareQuerySecure();
            self::$Language = new Language(isset($params["lang"]) ? $params["lang"] : "vi");
        }
        return self::$Language;
    }

    private $state;

    protected function establishCSRFTokenState() {
        if ($this->state == null) {
            $paramBodys = $this->getReceiver()->getBodys();
            $this->state = md5(uniqid(mt_rand(), true) . json_encode($paramBodys) . $this->getSecret());
        }
        return $_SESSION["csrfTokenState"] = $this->state;
    }

    public function verifyCsrfToken($csrfToken) {
        $csrfTokenState = $_SESSION["csrfTokenState"];
        unset($_SESSION["csrfTokenState"]);        
        return hash_equals($csrfToken, $csrfTokenState);
    }

    /**
     * 
     * @return Receiver
     */
    public function getReceiver() {
        if ($this->receiver == null)
            $this->receiver = new Receiver();
        return $this->receiver;
    }

    public function _location($url) {
        header("location: " . $url);
        die;
    }

    public function getAuthorize() {
        if ($this->authorize === null) {
            $authorize = new Authorize();
            $authResult = $authorize->AuthorizeRequest($this->getReceiver()->getQueryParams());
            $this->authorize = $authResult->getCode() === ResultObject::AUTHORIZE_SUCCESS ?
                    true : $authResult;
        }
        return $this->authorize;
    }

    /**
     * 
     * @return GapiController
     */
    public function getGApiClient() {
        if ($this->gapiClient == null) {
            $this->gapiClient = new GApiClient();
            $this->gapiClient->setController($this);
        }
        return $this->gapiClient;
    }

    /**
     * 
     * @return Api of MigEvents\Http\Client\GraphClient
     */
    public function getGraphClient() {
        if ($this->graphClient == null) {
            $this->graphClient = new GraphClient();
            $this->graphClient->setController($this);
        }
        //$this->getTimeSlice();
        return $this->graphClient;
    }

    /**
     * 
     * @return type MemcacheObject
     */
    public function getMemcacheObject() {
        if ($this->memcached == null) {
            $this->memcached = new MemcacheObject();
            $this->memcached->setController($this);
        }
        return $this->memcached;
    }

    protected function setMemcached(MemcacheObject $memcached) {
        $this->memcached = $memcached;
    }

    /**
     * 
     * @param Receiver $receiver
     */
    public function setReceiver(Receiver $receiver) {
        $this->receiver = $receiver;
    }

    public function getDbConfig() {
        return $this->dbConfig;
    }

    public function setDbConfig($dbConfig) {
        $this->dbConfig = $dbConfig;
    }

    /**
     * 
     * @return integer value equals 1002
     */
    public function getAppId() {
        if ($this->appId == null) {
            $this->appId = 146;
        }
        return $this->appId;
    }

    protected function genCacheId($keyId) {
        return md5($this->getAppId() . $keyId);
    }

    /**
     * 
     * @return string secret key by app id 1002
     */
    public function getSecret() {
        return "71a5ef7ecb7b29f86a2613a5e9c629e4";
    }

    /**
     * 
     * @return string
     */
    public function getPathRoot() {
        return $this->pathRoot;
    }

    /**
     * set path view
     * default will view path
     * @param string $pathRoot
     */
    function setPathRoot($pathRoot = "") {
        $this->pathRoot = $pathRoot;
    }

    public function getPathView() {
        //APPPATH . 'views/' . $base_public . 
        return APPPATH . 'views/' . $this->getPathRoot();
    }

    /**
     * 
     * @return array 
     */
    function getData() {
        return $this->data;
    }

    /**
     * 
     * @param mixed $message
     */
    public function setMessage($message) {
        $this->data["message"] = $message;
    }

    /**
     * Add new key data to this data of class
     * @param mixed $key
     * @param mixed $data
     */
    public function addData($key, $data) {
        $this->data[$key] = $data;
    }

    /**
     * Genaral data from this constants value and properties by class
     * 
     * @return array
     */
    function getThisData() {
        $values = array();

        $oClass = new \ReflectionClass($this->getEndPoint());
        $oContants = $oClass->getConstants();

        foreach ($oContants as $key => $value) {
            $values[$key] = $value;
        }
        $oProperties = $oClass->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
        $thisMethods = get_class_methods(__CLASS__);
        foreach ($oProperties as $key => $value) {
            try {
                $propertise = json_decode(json_encode($value), true);
                $values[$propertise["name"]] = $this->{$propertise["name"]};
            } catch (\Exception $ex) {
                //var_dump($ex);
                continue;
            }
        }
        return $values;
    }

    function setData($data) {
        $this->data = $data;
    }

    /**
     * Like setData but will skip field validation
     *
     * @param array
     * @return $this
     */
    public function setDataWithoutValidation(array $data) {
        if ($data == false)
            return $this;
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Render view by view name of path root
     * Require init path root before default root view path
     * @param string $filePath
     * @param boolean $isFullProperties default false, if true binding all properties this class
     * 
     */
    protected function Render($filePath, $isFullProperties = false) {
        echo $this->RenderContent($filePath, $isFullProperties);
        exit();
    }

    protected function RenderBuildEvent($fileName) {
        $requestData = $this->prepareArray($this->prepareQuerySecure());
        $server = $this->getGApiClient()->getServerInfo($this->getAppId(), $requestData["info"]["server_id"], false);

        $this->addData("server", $server);
        echo $this->RenderContent($fileName, true);
        exit();
    }

    protected function RenderContent($filePath, $isFullProperties = false) {

        if ($isFullProperties == true) {
            $buildDatas = $this->prepareArray($this->getThisData());
            if ($buildDatas == FALSE)
                $buildDatas = array();
            $rebuildData = $this->prepareArray($this->data);
            if ($rebuildData == true)
                $buildDatas = array_merge($buildDatas, $rebuildData);
            $decodeQueryString = $this->prepareArray($this->prepareQuerySecure());
            if (is_array($decodeQueryString) == true)
                $buildDatas = array_merge($buildDatas, $decodeQueryString);
        } else {
            $buildDatas = $this->prepareArray($this->data);
        }
        $buildDatas["csrfToken"] = $this->establishCSRFTokenState();
        $buildDatas["controller"] = $this;
        return $this->load->view($this->getPathRoot() . "{$filePath}", $buildDatas, true);
    }

    public function prepareArray($data) {
        if (is_object($data)) {
            return $data;
        } elseif (is_array($data)) {
            $reBuilds = array();
            foreach ($data as $key => $value) {
                $reBuilds[$key] = $this->prepareArray($value);
            }
            return $reBuilds;
        } else if (is_json($data)) {
            $jsonArrays = json_decode($data, true);
            if (is_array($jsonArrays)) {
                $reBuilds = array();
                foreach ($jsonArrays as $key => $value) {
                    $reBuilds[$key] = $this->prepareArray($value);
                }
                return $reBuilds;
            } else {
                return $jsonArrays;
            }
        } else if (is_scalar($data)) {
            return $data;
        } else {
            return $data;
        }
    }

    public function prepareQuerySecure() {
        $paramBodys = $this->getReceiver()->getBodys();
        if (isset($paramBodys["access_token"])) {
            $authorInfo = base64_decode($paramBodys["access_token"]);
            $paramBodys["access_info"] = $authorInfo;
        }
        return $paramBodys;
    }

    /**
     * Store capture query string from url request
     * void
     */
    public function StoreQueryString() {
        if (session_status() == PHP_SESSION_NONE)
            session_start();
        $gets = $this->getReceiver()->getQueryParams();
        foreach ($gets as $key => $value) {
            $_SESSION["QUERYSTRING"][$key] = $value;
        }
    }

    /**
     * @return Controller|null
     */
    public static function instance() {
        return static::$instance;
    }

    /**
     * @param Api $instance
     */
    public static function setInstance(Controller $instance) {
        static::$instance = $instance;
    }

    public function getParentClass($class = null, $plist = array()) {
        $class = $class ? $class : $this;
        $parent = get_parent_class($class);
        if ($parent) {
            $plist[] = $parent;
            /* Do not use $this. Use 'self' here instead, or you
             * will get an infinite loop. */
            $plist = self::getParents($parent, $plist);
        }
        return $plist;
    }

    public function getEndPoint() {
        return __CLASS__;
    }

}

?>
