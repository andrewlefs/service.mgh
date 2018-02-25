<?php

namespace GraphShare;

require_once "Definition.php";

class Response {

    protected $_parameters;
    private $_dataJson;
    private $_dataHTML;
    private $_isJson = FALSE;
    private $_code = 1011000;
    private $_message = "";
    private $_responseCodeLists = array(
        1011000 => "Success",
        -1011010 => "Function not found",
        1011012 => "Like success",
        -1011013 => "Like not success",
        -1011014 => "Like exists",
        -1011015 => "Process data",
        -1011016 => "Data empty",
        -1011017 => "System error",
        1001028 => "Đã nhận quá số lượng cho phép trong ngày, vui lòng đợi qua ngày hôm sau."
    );

    public function __construct($parameters = array(), $statusCode = 200, $headers = array()) {

        if (is_array($parameters)) {

            $this->_isJson = TRUE;
        }

        $this->_parameters = $parameters;
    }

    public function getCode() {
        return $this->_code;
    }

    public function getMessage() {
        return $this->_responseCodeLists[$this->_code];
    }

    public function getJson() {

        if ($this->_isJson === FALSE)
            return FALSE;

        if ($this->_dataJson)
            return $this->_dataJson;

        if (!isset($this->_responseCodeLists[$this->_code])) {
            $message = "Message not defined";
        } else {
            $message = $this->getMessage();
        }
        $code = $this->_code;

        $keyCode = Object\Values\MessageCodes::getNameForValue($code);


        $this->_dataJson = json_encode(
                array(
                    "code" => $code,
                    "keyCode" => $keyCode,
                    "message" => $message,
                    "data" => $this->_parameters
                )
        );

        return $this->_dataJson;
    }

    public function getArray() {

        if ($this->_dataJson)
            return $this->_dataJson;

        $this->_dataJson = $this->_parameters;

        return $this->_dataJson;
    }

    public function getHTML() {

        if ($this->_dataHTML === FALSE)
            return FALSE;

        if ($this->_dataHTML)
            return $this->_dataHTML;

        $this->_dataHTML = $this->_parameters;

        return $this->_dataHTML;
    }

    public function setData($data = array()) {
        if (is_array($data)) {

            $this->_isJson = TRUE;
        }
        $this->_parameters = $data;
    }

    public function setCode($code) {          
        $this->_code = $code;
        $this->_message = $this->_responseCodeLists[$code];
    }

    public function end($format = 'json') {

        switch ($format) {

            case 'json':

                @header('Content-type: application/json');

                if ($this->_dataJson) {

                    echo $this->_dataJson;

                    break;
                }

                echo $this->getJson();

                break;

            case 'html':

                if ($this->_dataHTML) {

                    echo $this->_dataHTML;

                    break;
                }

                echo $this->getHTML();

                break;
        }
        die;
    }

}

?>
