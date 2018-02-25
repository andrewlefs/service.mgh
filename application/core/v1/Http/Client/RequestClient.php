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

class RequestClient extends Client implements ClientInterface {

    public function __construct() {
      
    }
}
