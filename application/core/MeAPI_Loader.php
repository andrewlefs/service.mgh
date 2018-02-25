<?php

require_once APPPATH . 'third_party/MeAPI/Autoloader.php';

class MeAPI_Loader extends CI_Loader {

    public function __construct() {
        parent::__construct();
    }

    public function MeAPI_Model($model_name) {
        $this->model('../third_party/MeAPI/Models/' . $model_name);
    }

    public function MeAPI_Library($library_name) {
        $this->library('../third_party/MeAPI/Libraries/' . $library_name, FALSE, $library_name);
    }

    public function MeAPI_Helper($helper_name) {
        $this->helper('../third_party/MeAPI/Helpers/' . $helper_name);
    }

}