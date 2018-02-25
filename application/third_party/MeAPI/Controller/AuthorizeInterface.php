<?php
interface MeAPI_Controller_AuthorizeInterface extends MeAPI_Response_ResponseInterface {
    public function validateAuthorizeRequest(MeAPI_RequestInterface $request, $scope = NULL);    
}
?>