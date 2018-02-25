<?php

/* 
 * Author        :   VietBL
 * Creation Date :   2014/09/16
 * Description   :   Class API controller cung cấp các method phục vụ các chức năng tương tác với game.
 * 
 * Update History:
 *  
 */
@require_once APPPATH . 'third_party/MeAPI/Autoloader.php';

class MeAPI_Controller_GameController implements MeAPI_Controller_GameInterface {
    
    private $CI;
    protected $_response;
    
    private $fileNameLog;
    
    public function __construct(){
        $this->CI = & get_instance();
        
        //load helper utils
        $this->CI->load->helper('utils');
        
        //gọi function register ở class MeAPI_Autoloader (third_party/MeAPI/Autoloader.php)
        MeAPI_Autoloader::register();
        
        // get current class name
        $this->fileNameLog = __CLASS__;
    }
    
    /*
     * Override
     */
    public function getResponse() {
        return $this->_response;
    }
}