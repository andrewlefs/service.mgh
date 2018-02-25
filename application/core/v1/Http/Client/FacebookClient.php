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
use MigEvents\Http\Client\ClientInterface;
use MigEvents\Http\Parameters;
use MigEvents\Http\Adapter\CurlAdapter;
use MigEvents\Http\Response;
use MigEvents\Http\Exception\EmptyResponseException;
use MigEvents\Http\Exception\RequestException;
use MigEvents\Tripledes;
use MigEvents\Http\Receiver;
use MigEvents\Api;
use MigEvents\Http\Exception\AuthorizationException;

class FacebookClient extends Client implements ClientInterface {

    /**
     * Maps aliases to Facebook domains.
     *
     * @var array
     */
    public static $DOMAIN_MAP = array(
        'api' => 'https://api.facebook.com/',
        'api_video' => 'https://api-video.facebook.com/',
        'api_read' => 'https://api-read.facebook.com/',
        'graph' => 'https://graph.facebook.com/v2.2/',
        'graph_video' => 'https://graph-video.facebook.com/',
        'www' => 'https://www.facebook.com/',
    );
    protected $receive;

    public function __construct() {
        $this->setApp("420240011468958");
        $this->setSecret("7598b94358b12bf3d66e798d9f59cbfd");
        $this->setDefaultBaseDomain("facebook.com");
        $this->setDefaultLastLevelDomain("graph");
        $this->getRequestPrototype()->setProtocol("https://");
        $this->setSslVerifypeer(true);
        $this->setCaBundleName("fb_ca_chain_bundle.crt");
    }

    function getReceive() {
        if ($this->receive == null)
            $this->receive = new Receiver ();
        return $this->receive;
    }

    function setReceive($receive) {
        $this->receive = $receive;
    }

    public function getAccessTokenFromCode($code, $redirect_uri = null) {
        if (empty($code)) {
            return false;
        }

        if ($redirect_uri === null) {
            $redirect_uri = $this->getReceive()->getCurrentUrl();
        }

        try {
            // need to circumvent json_decode by calling _oauthRequest
            // directly, since response isn't JSON format.
            $api = new Api($this);
            $access_token_response = $api->call('/oauth/access_token', "GET", array('client_id' => $this->getApp(),
                'client_secret' => $this->getSecret(),
                'redirect_uri' => $redirect_uri,
                'code' => $code));
        } catch (FacebookApiException $e) {
            // most likely that user very recently revoked authorization.
            // In any event, we don't have an access token, so say so.
            return false;
        }

        if (empty($access_token_response)) {
            return false;
        }

        $response_params = array();
        parse_str($access_token_response->getBody(), $response_params);
        if (!isset($response_params['access_token'])) {
            return false;
        }

        return $response_params['access_token'];
    }

    /**
     * Get a Login URL for use with redirects. By default, full page redirect is
     * assumed. If you are using the generated URL with a window.open() call in
     * JavaScript, you can pass in display=popup as part of the $params.
     *
     * The parameters:
     * - redirect_uri: the url to go to after a successful login
     * - scope: comma separated list of requested extended perms
     *
     * @param array $params Provide custom parameters
     * @return string The URL for the login flow
     */
    public function getLoginUrl($params = array(), $currentUrl = "") {
        $this->establishCSRFTokenState();

        if ($currentUrl == "")
            $currentUrl = (new Receiver())->getUrl();

        // if 'scope' is passed as an array, convert to comma separated list
        $scopeParams = isset($params['scope']) ? $params['scope'] : null;
        if ($scopeParams && is_array($scopeParams)) {
            $params['scope'] = implode(',', $scopeParams);
        }

        return $this->buildUrl(
                        'www', 'dialog/oauth', array_merge(
                                array(
                    'client_id' => $this->getApp(),
                    'redirect_uri' => $currentUrl, // possibly overwritten
                    'state' => $this->state,
                    'sdk' => 'php-sdk-' . self::VERSION
                                ), $params
        ));
    }

    /**
     * 
     * @param String $access_token
     * @param String $redirect_uri
     * @return boolean
     */
    public function getUserInfo($access_token, $redirect_uri = null) {
        if (empty($access_token)) {
            return false;
        }

        if ($redirect_uri === null) {
            $redirect_uri = $this->getReceive()->getCurrentUrl();
        }

        try {
            // need to circumvent json_decode by calling _oauthRequest
            // directly, since response isn't JSON format.
            $api = new Api($this);
            $userInfo = $api->call('/me', "GET", array('client_id' => $this->getApp(),
                'client_secret' => $this->getSecret(),
                'redirect_uri' => $redirect_uri,
                'access_token' => $access_token));
        } catch (FacebookApiException $e) {
            // most likely that user very recently revoked authorization.
            // In any event, we don't have an access token, so say so.
            return false;
        } catch (AuthorizationException $e) {
            // most likely that user very recently revoked authorization.
            // In any event, we don't have an access token, so say so.
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        if (empty($userInfo)) {
            return false;
        }

        $response_params = array();
        $contents = $userInfo->getContent();
        if (isset($contents["error"]))
            return false;
        else
            return $contents;
    }

    /**
     * 
     * @param String $access_token
     * @param String $redirect_uri
     * @return boolean
     */
    public function getVerifyApp($access_token) {
        if (empty($access_token)) {
            return false;
        }
        try {
            // need to circumvent json_decode by calling _oauthRequest
            // directly, since response isn't JSON format.
            $api = new Api($this);
            $userInfo = $api->call('/app/', "GET", array('client_id' => $this->getApp(),
                'client_secret' => $this->getSecret(),
                'redirect_uri' => $redirect_uri,
                'access_token' => $access_token));
        } catch (FacebookApiException $e) {
            // most likely that user very recently revoked authorization.
            // In any event, we don't have an access token, so say so.
            return false;
        } catch (AuthorizationException $e) {
            // most likely that user very recently revoked authorization.
            // In any event, we don't have an access token, so say so.
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        if (empty($userInfo)) {
            return false;
        }

        $response_params = array();
        $contents = $userInfo->getContent();
        if (isset($contents["error"]))
            return false;
        else {
            if ($contents["id"] != $this->getApp()) {
                return false;
            } else {
                return $contents;
            }
        }
    }

    /**
     * Build the URL for given domain alias, path and parameters.
     *
     * @param string $name   The name of the domain
     * @param string $path   Optional path (without a leading slash)
     * @param array  $params Optional query parameters
     *
     * @return string The URL for the given parameters
     */
    protected function buildUrl($name, $path = '', $params = array()) {
        $url = self::$DOMAIN_MAP[$name];
        if ($path) {
            if ($path[0] === '/') {
                $path = substr($path, 1);
            }
            $url .= $path;
        }
        if ($params) {
            $url .= '?' . http_build_query($params, null, '&');
        }

        return $url;
    }

}
