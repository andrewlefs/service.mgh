<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once APPPATH . 'controllers/grash/api.php';
//echo APPPATH . 'controllers/grash/Api.php';

require_once 'autoloader.php';

use GraphShare\Definition;
use GraphShare\Object\RequestUrl;
use GraphShare\Object\Values\GameApps;
use GraphShare\Object\Values\CacheKeys;
use GraphShare\Object\Fields\UserFields;
use GraphShare\Object\Fields\MoboFields;
use GraphShare\Object\Values\CachedHosts;
use GraphShare\Object\Values\BaseLinks;

class route extends EI_Controller {

    public function __construct() {
        parent::__construct();        
    }

    function index() {

        if ($this->verify_uri() == false) {
            $this->render("deny");
        } else {           
            //cached login
            //resign 
            $params = $this->input->get();
            $resign = $params["sign"];
            //var_dump($params);die;
            $request = new RequestUrl();
            $request->setData($params);

            $memcache = new Memcache();
            $memcache->connect(CachedHosts::MEMCACHED_HOST, CachedHosts::MEMCACHED_PORT);
            $identify = $request->getHash();
            if ($memcache->set($identify, $request, false, 3600)) {
                //set thong tin game                
                header("location: " . BaseLinks::BASE_HOME_URI . "?k=" . $identify);
            }
            //echo "dsss";
        }
        die;
    }

}
