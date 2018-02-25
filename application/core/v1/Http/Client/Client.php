<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MigEvents\Http\Client;

use MigEvents\Http\Client\ClientCurl;
use MigEvents\Http\RequestInterface;
use MigEvents\Http\Request;
use MigEvents\Http\ResponseInterface;
use MigEvents\Http\Headers;
use MigEvents\Http\Parameters;
use MigEvents\Http\Adapter\CurlAdapter;
use MigEvents\Http\Response;
use MigEvents\Http\Exception\EmptyResponseException;
use MigEvents\Http\Exception\RequestException;
use MigEvents\Http\Client\ClientInterface;
use MigEvents\Api;
use MigEvents\Controller;

abstract class Client implements ClientInterface {

//    private $api_url_payment = 'http://gapi.mobo.vn/';
//    private $api_url_data = 'http://gapi.mobo.vn/';
    protected $app = 'graph.dxglobal.net';
    protected $secret = 'YAtSTMfEAP';

    /**
     * A CSRF state variable to assist in the defense against CSRF attacks.
     *
     * @var string
     */
    protected $state;

    const VERSION = "2.6";

    /**
     * @var string
     */
    const DEFAULT_GRAPH_BASE_DOMAIN = 'dllglobal.net';

    /**
     * @var string
     */
    protected $defaultLastLevelDomain = '';

    /**
     * @var RequestInterface
     */
    protected $requestPrototype;

    /**
     * @var ResponseInterface
     */
    protected $responsePrototype;

    /**
     * @var Headers
     */
    protected $defaultRequestHeaders;

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     *
     * @var type string
     */
    protected $caBundleName;

    /**
     * @var string
     */
    protected $caBundlePath;

    /**
     * @var string
     */
    protected $defaultBaseDomain = self::DEFAULT_GRAPH_BASE_DOMAIN;

    /**
     *
     * @var Api
     */
    protected $api;

    /**
     *
     * @var Controller
     */
    protected $controller;

    /**
     *
     * @var type boolean
     */
    protected $sslVerifypeer = false;
    protected $timeSlice;

    public function getTimeSlice() {
        if ($this->timeSlice == null) {
            $this->timeSlice = (int) (time() / 30);
        }
        return $this->timeSlice;
    }

    public function setTimeSlice($timeSlice) {
        $this->timeSlice = $timeSlice;
    }

    /**
     * @return RequestInterface
     */
    public function getRequestPrototype() {
        if ($this->requestPrototype === null) {
            $this->requestPrototype = new Request($this);
        }

        return $this->requestPrototype;
    }

    public function getEndPoint() {
        return __CLASS__;
    }

    public function getController() {
        if ($this->controller == null) {
            $this->controller = new Controller();
        }
        return $this->controller;
    }

    public function setController(Controller $controller) {
        $this->controller = $controller;
    }

    /**
     * 
     * @return Api of MigEvents\Api
     */
    public function getApi() {
        if ($this->api == null) {
            $this->api = new Api($this);
            $this->api->getHttpClient()->setApp($this->getApp());
            $this->api->getHttpClient()->setSecret($this->getSecret());
        }
        //$this->getTimeSlice();
        return $this->api;
    }

    /**
     * @param RequestInterface $prototype
     */
    public function setRequestPrototype(RequestInterface $prototype) {
        $this->requestPrototype = $prototype;
    }

    /**
     * 
     * @return type string
     */
    function getSslVerifypeer() {
        return $this->sslVerifypeer;
    }

    /**
     * 
     * @param boolean $sslVerifypeer
     */
    function setSslVerifypeer($sslVerifypeer = false) {
        $this->sslVerifypeer = $sslVerifypeer;
    }

    /**
     * @return RequestInterface
     */
    public function createRequest() {
        return $this->getRequestPrototype()->createClone();
    }

    /**
     * @return ResponseInterface
     */
    public function getResponsePrototype() {
        if ($this->responsePrototype === null) {
            $this->responsePrototype = new Response();
        }

        return $this->responsePrototype;
    }

    public function setApp($app) {
        $this->app = $app;
    }

    public function getApp() {
        return $this->app;
    }

    public function setSecret($secret) {
        $this->secret = $secret;
    }

    public function getSecret() {
        return $this->secret;
    }

    /**
     * @param ResponseInterface $prototype
     */
    public function setResponsePrototype(ResponseInterface $prototype) {
        $this->responsePrototype = $prototype;
    }

    /**
     * @return ResponseInterface
     */
    public function createResponse() {
        return clone $this->getResponsePrototype();
    }

    /**
     * @return Headers
     */
    public function getDefaultRequestHeaderds() {
        if ($this->defaultRequestHeaders === null) {
            $this->defaultRequestHeaders = new Headers(array(
            ));
        }

        return $this->defaultRequestHeaders;
    }

    /**
     * @param Headers $headers
     */
    public function setDefaultRequestHeaders(Headers $headers) {
        $this->defaultRequestHeaders = $headers;
    }

    /**
     * @return string
     */
    public function getDefaultBaseDomain() {
        return $this->defaultBaseDomain;
    }

    /**
     * @return string
     */
    public function getDefaultLastLevelDomain() {
        return $this->defaultLastLevelDomain;
    }

    /**
     * @param string $domain
     */
    public function setDefaultLastLevelDomain($lsst_domain) {
        $this->defaultLastLevelDomain = $lsst_domain;
    }

    /**
     * @param string $domain
     */
    public function setDefaultBaseDomain($domain) {
        $this->defaultBaseDomain = $domain;
    }

    /**
     * @return AdapterInterface
     */
    public function getAdapter() {
        if ($this->adapter === null) {
            $this->adapter = new CurlAdapter($this);
        }

        return $this->adapter;
    }

    /**
     * 
     * @return type string
     */
    public function getCaBundleName() {
        return $this->caBundleName;
    }

    /**
     * 
     * @param string $caBundleName
     */
    public function setCaBundleName($caBundleName) {
        $this->caBundleName = $caBundleName;
    }

    /**
     * @return string
     */
    public function getCaBundlePath() {
        if ($this->getSslVerifypeer() === false)
            return false;
        if ($this->caBundlePath === null) {
            $this->caBundlePath = __DIR__ . DIRECTORY_SEPARATOR
                    . $this->getCaBundleName();
        }

        return $this->caBundlePath;
    }

    /**
     * @param string $path
     */
    public function setCaBundlePath($path) {
        $this->caBundlePath = $path;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws RequestException
     */
    public function sendRequest(RequestInterface $request) {
        //var_dump($request);die;
        $response = $this->getAdapter()->sendRequest($request);
        $response->setRequest($request);
        $response_content = $response->getContent();

        if ($response_content === null) {
            //throw new EmptyResponseException($response->getStatusCode());
        }

        if (is_array($response_content) && array_key_exists('error', $response_content)) {

            throw RequestException::create(
                    $response->getContent(), $response->getStatusCode());
        }
        //xử lý data tại bước này


        return $response;
    }

    /**
     * Lays down a CSRF state token for this process.
     *
     * @return void
     */
    public function establishCSRFTokenState() {
        if ($this->state === null) {
            $this->state = md5(uniqid(mt_rand(), true));
        }
    }

    function getUrlData($url, $raw = false) { // $raw - enable for raw display
        $result = false;

        $contents = $this->getUrlContents($url);

        if (isset($contents) && is_string($contents)) {
            $title = null;
            $metaTags = null;
            $metaProperties = null;

            preg_match('/<title>([^>]*)<\/title>/si', $contents, $match);

            if (isset($match) && is_array($match) && count($match) > 0) {
                $title = strip_tags($match[1]);
            }

            preg_match_all('/<[\s]*meta[\s]*(name|property)="?' . '([^>"]*)"?[\s]*' . 'content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si', $contents, $match);

            if (isset($match) && is_array($match) && count($match) == 4) {
                $originals = $match[0];
                $names = $match[2];
                $values = $match[3];

                if (count($originals) == count($names) && count($names) == count($values)) {
                    $metaTags = array();
                    $metaProperties = $metaTags;
                    if ($raw) {
                        if (version_compare(PHP_VERSION, '5.4.0') == -1)
                            $flags = ENT_COMPAT;
                        else
                            $flags = ENT_COMPAT | ENT_HTML401;
                    }

                    for ($i = 0, $limiti = count($names); $i < $limiti; $i++) {
                        if ($match[1][$i] == 'name')
                            $meta_type = 'metaTags';
                        else
                            $meta_type = 'metaProperties';
                        if ($raw)
                            ${$meta_type}[$names[$i]] = array(
                                'html' => htmlentities($originals[$i], $flags, 'UTF-8'),
                                'value' => $values[$i]
                            );
                        else
                            ${$meta_type}[$names[$i]] = array(
                                'html' => $originals[$i],
                                'value' => $values[$i]
                            );
                    }
                }
            }

            $result = array(
                'title' => $title,
                'metaTags' => $metaTags,
                'metaProperties' => $metaProperties,
            );
        }

        return $result;
    }

    function getUrlContents($url, $maximumRedirections = null, $currentRedirection = 0) {
        $result = false;
        $api = new Api(new RequestClient());
        $info = $api->executeUrl($url);
        $contents = $info->getBody();
        // $contents = @file_get_contents($url);
        // Check if we need to go somewhere else

        if (isset($contents) && is_string($contents)) {
            preg_match_all('/<[\s]*meta[\s]*http-equiv="?REFRESH"?' . '[\s]*content="?[0-9]*;[\s]*URL[\s]*=[\s]*([^>"]*)"?' . '[\s]*[\/]?[\s]*>/si', $contents, $match);

            if (isset($match) && is_array($match) && count($match) == 2 && count($match[1]) == 1) {
                if (!isset($maximumRedirections) || $currentRedirection < $maximumRedirections) {
                    return getUrlContents($match[1][0], $maximumRedirections, ++$currentRedirection);
                }

                $result = false;
            } else {
                $result = $contents;
            }
        }

        return $contents;
    }

}
