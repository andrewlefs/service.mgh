<?php

class MeAPI_Log {

    public static function writeCsv($fields, $filename, $group = 'request', $date = 'Y/m/d', $timefield = 'H:i:s') {



        $CI = &get_instance();

        $CI->config->load('log');

        $config = $CI->config->item('log');

        $config = $config[$group];



        if (empty($config) === TRUE)
            die('Empty config log ' . $group);



        try {

            $fields[] = date($timefield);

            if ($date)
                $path = $config . '/' . date($date);
            else
                $path = $config . '/';

            if (!file_exists($path))
                @mkdir($path, 0777, TRUE);

            $fh = @fopen($path . '/' . $filename . '.csv', 'a');

            @fputcsv($fh, $fields);

            @fclose($fh);
        } catch (Exception $ex) {
            
        }
    }
    
    /*
     * Ham ghi log chuc nang cho ung dung
     * author: VietBL
     */
    public static function writeLog($arrData, $folder, $ip, $group = "request"){
        $CI = &get_instance();
        $CI->config->load("log");
        $config = $CI->config->item("log");
        $config = $config[$group];
        $date = "Y/m";

        if (empty($config) === TRUE)
            die("Empty config log " . $group);
        
        try {
            array_unshift($arrData, date("Y-m-d H:i:s"));
            
            $path = $config . "/" . $folder . "/" . date($date);
            
            if (!file_exists($path))
                @mkdir($path, 0777, TRUE);

            $fp = fopen($path . "/" . date("d") . ".txt", "a");
            
            $text = "";
            for ($i = 0; $i < count($arrData); $i++) {
                if ($i != (count($arrData) - 1))
                    $text .= $arrData[$i] . ", ";
                else 
                    $text .= $arrData[$i] . "\r\n";
            }
            fwrite($fp, $text);
            fclose($fp);
        } catch (Exception $ex) {
            
        }
    }

}
?>