<?php

interface MeAPI_Controller_PaymentInterface extends MeAPI_Response_ResponseInterface {

    public function add_gold(MeAPI_RequestInterface $request);
}