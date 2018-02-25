<?php
/**
 * Created by Ivoglent Nguyen.
 * User: longnv
 * Date: 11/8/13
 * Time: 2:48 PM
 * Project : api
 * File : ApiResponse.php
 */
class ApiResponse{
    protected $data=array();
    protected $code=403;
    private $config;
    public function __construct($_config){
        $this->config=$_config;
    }
    public  function output(){
        header('Content-Type: application/json');
        $jdata=json_encode(array(
            "code" => $this->code,
            "data"   => $this->data,
            "time"      =>time()
        ));
        $this->log($_SERVER['REQUEST_URI'],$jdata);
        print($jdata);die();

    }
    public function assign($name,$value){
        $this->data[$name]=$value;
    }
    public function code($_code){
        $this->code=$_code;
    }
    private function log($url,$str){
        if($this->config['log']){
            $typedir="";
            $time=time();
            $sub=str_replace("/","",date('Y-m-d',$time));
            $path=LOG_PATH .$typedir. DS . $sub;
            //die($path);
            if(!is_dir($path))
                mkdir($path,0777,true);
            $file="api_response_".str_replace("-","",str_replace("/","",date('Y-m-d')))."_".date('H',$time).".log";
            $f=fopen($path.DIRECTORY_SEPARATOR.$file,"a+");
            fputs($f,date('H:i:s',$time)."\t\t".$url ."\t\t".$str."\n");
            fclose($f);
        }
    }
}