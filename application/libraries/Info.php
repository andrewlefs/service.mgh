<?php
class Info {
    private $key_private = "47120942a61d7cecd88a8588891c6ea5";
    public function extractparam($params)
    {
        $datadecode = json_decode(base64_decode($params["access_token"]), true);
        $userdata = json_decode($params["info"], true);
        $character_id = $userdata["character_id"];
        $character_name = $userdata["character_name"];
        $server_id = $userdata["server_id"];
        $mobo_service_id = $datadecode["mobo_service_id"];
        $mobo_id = $datadecode["mobo_id"];
		$lang_id = strtolower($userdata['lang_id']);
		
        
        return array("device_id"=>$params['device_id'],"lang_id"=>$lang_id,"mobo_id"=>$mobo_id, 'mobo_service_id' => $mobo_service_id, 'server_id' => $server_id, 'character_id' => $character_id, 'character_name' => $character_name);
    }

    function checksign($params)
    {
        $token = trim($params['token']);
        unset($params['token']);
        $valid = md5(implode('', $params) .$this->key_private);
        $_SESSION["oauthtoken"] = base64_encode(json_encode($params));
        $_SESSION["redirect"] = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        if ($valid != $token) {
            return false;
        }
        return true;
    }

    function proper_parse_str($str)
    {
        # result array
        $arr = array();
        # split on outer delimiter
        $pairs = explode('&', $str);
        # loop through each pair
        foreach ($pairs as $i) {
            # split into name and value
            list($name, $value) = explode('=', $i, 2);

            # if name already exists
            if (isset($arr[$name])) {
                # stick multiple values into an array
                if (is_array($arr[$name])) {
                    $arr[$name][] = $value;
                } else {
                    $arr[$name] = array($arr[$name], $value);
                }
            } # otherwise, simply stick it in a scalar
            else {
                $arr[$name] = urldecode($value);
            }
        }
        # return result array
        return $arr;
    }

    function identical_values($arrayA, $arrayB)
    {
        sort($arrayA);
        sort($arrayB);
        return $arrayA == $arrayB;
    }

}
?>