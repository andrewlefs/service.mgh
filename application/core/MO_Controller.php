<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once APPPATH . 'third_party/MeAPI/Autoloader.php';
class MO_Controller extends CI_Controller {
    public $layout;
    public $views;
    public $data;
    public $base_url;
    public $title = "SMS";
    private $ajax_message, $ajax_error = 0,$ajax_data;
    public $app = "phongthan";
    public $access_token;
    public $user;
    public $transaction_id;
    protected $token_callback = "5fef51s1de256f632g35v4f22nfab37c0";

    function __construct() {

        parent::__construct();
//        $this->layout = $this->template . "/layouts/main";
        $this->load->helper('url');
        $this->base_url = base_url();
        $this->log_request();

        if (!isset($_GET['access_token'])) {
            $this->assignd("error", "Bạn không có quyền truy cập trang này.Hoặc là phiên tham gia của bạn đã hết hạn. Vui lòng đăng nhập lại");
        }

        MeAPI_Autoloader::register();
    }

    protected function assignd($name, $value) {
        $this->data[$name] = $value;
    }

    protected function render($view) {
        $this->load->view($this->layout, array("data" => $this->data, "view" => $view, 'cname' => $this->getName()));
    }

    public function log($group = false, $url = false, $data = false) {
        try {
            $date = 'Y/m/d';
            $time = time();
            $sub = str_replace("/", "", date('Y-m-d', $time));
            $path = LOG_PATH . date($date) . DIRECTORY_SEPARATOR;
            //die($path);
            if (!is_dir($path))
                mkdir($path, 0777, true);

            $file = $group . "_" . date('H', $time) . ".csv";
            //@chmod($path . DIRECTORY_SEPARATOR . $file, 0777);
            $f = fopen($path . DIRECTORY_SEPARATOR . $file, "a+");
            //Build log data
            $data = is_array($data) ? json_encode($data) : $data;
            $csv_data[] = date('H:i:s', $time) . "\t";
            $csv_data[] = "IP : " . $_SERVER['REMOTE_ADDR'] . "\t";
            $csv_data[] = "Refer : " . $_SERVER['HTTP_REFERER'] . "\t";
            $csv_data[] = $url . "\t";
            $csv_data[] = $data . "\t";
            //fputs($f, date('H:i:s', $time) . "\t,IP : " . $_SERVER['REMOTE_ADDR'] . "\t," . $url . "\t," . $data . "\n");
            @fputcsv($f, $csv_data);
            fclose($f);
        } catch (Exception $exc) {

        }
    }

    public function log_request($url = null) {
        $this->log("request", $_SERVER['REQUEST_URI'], "");
    }

    //Public call
    public function getName() {
        return strtolower(get_class($this));
    }

    public function redirect($url) {
        header("Location:{$url}");
        exit();
    }

    //ajax call
    public function setAjaxError($error_code) {
        $this->ajax_error = $error_code;
    }
    public function setAjaxData($data) {
        $this->ajax_data = $data;
    }

    public function setAjaxMessage($msg) {
        $this->ajax_message = $msg;
    }

    public function ajaxOutput() {
        header('Content-Type: application/json');
        print(json_encode(array(
            "error" => $this->ajax_error,
            "message" => $this->ajax_message,
            "data" => $this->ajax_data
        )));
        exit();
    }
    public function encrypt($params) {
        if (is_array($params)) {
            $input = json_encode($params);
        } else if (is_string($params)) {
            $input = $params;
        } else {
            throw new Exception('Encrypt data not format.');
        }
        $key_seed = $this->token_callback;
        $input = trim($input);
        $block = mcrypt_get_block_size('tripledes', 'ecb');
        $len = strlen($input);
        $padding = $block - ($len % $block);
        $input .= str_repeat(chr($padding), $padding);
        // generate a 24 byte key from the md5 of the seed
        $key = substr(md5($key_seed), 0, 24);
        $iv_size = mcrypt_get_iv_size(MCRYPT_TRIPLEDES, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        // encrypt
        $encrypted_data = mcrypt_encrypt(MCRYPT_TRIPLEDES, $key, $input, MCRYPT_MODE_ECB, $iv);
        // clean up output and return base64 encoded
        return base64_encode($encrypted_data);
    }
    public function decrypt($input) {
        $key_seed = $this->token_callback;
        $input = base64_decode($input);
        $key = substr(md5($key_seed), 0, 24);
        $text = mcrypt_decrypt(MCRYPT_TRIPLEDES, $key, $input, MCRYPT_MODE_ECB, '12345678');
        $block = mcrypt_get_block_size('tripledes', 'ecb');
        $packing = ord($text{strlen($text) - 1});
        if ($packing and ( $packing < $block)) {
            for ($P = strlen($text) - 1; $P >= strlen($text) - $packing; $P--) {
                if (ord($text{$P}) != $packing) {
                    $packing = 0;
                }
            }
        }
        $text = substr($text, 0, strlen($text) - $packing);
        $data = json_decode($text, true);
        if (is_array($data)) {
            return $data;
        } else {
            return $text;
        }
    }

}
