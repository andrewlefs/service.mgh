<?php

class MeAPI_Response_HTMLResponse extends MeAPI_Response {

    protected $_code = array();

    public function __construct(MeAPI_RequestInterface $request, $html = 'null') {

        $parameters = $html;

        parent::__construct($parameters, $statusCode);
    }

}
?>