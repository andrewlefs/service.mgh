<?php

class MeAPI_Server {

    public $request;
    protected $_response;

    public function __construct($data = NULL) {

        $this->request = MeAPI_Request::createFromGlobals($data);
    }

    public function start() {

        $controller = $this->request->get_controller();

        $function = $this->request->get_function();



        if (method_exists($controller, $function) == TRUE) {

            $method = new $controller();

            $method->{$function}($this->request);

            $this->_response = $method->getResponse();
        } else {

            $this->_response = new MeAPI_Response(array('Welcome to Service !!!'));
        }
    }

    public function getResponse() {

        return $this->_response;
    }

}
?>