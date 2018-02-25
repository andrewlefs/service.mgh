<?php

/**

 * @property CI_DB_active_record $db

 */
class FacebookModel extends CI_Model {

    private $_db;
    private $_db_slave;

    public function __construct() {
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
    }
    public function get_accesstoken($mobo_service_id,$server_id){
        $query = $this->_db->select("*")->where('mobo_service_id',$mobo_service_id)->where('server_id',$server_id)->get("facebook_access_tokens");
        if ($query == true && $query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return false;
        }
    }
    public function insert_accesstoken($mobo_service_id,$server_id,$access_token,$fid,$name){
        $time = date("y-m-d H:i:s", time());
        $this->_db->query("INSERT INTO facebook_access_tokens (fid,`name`,mobo_service_id, server_id, access_token,create_date) VALUES ($fid,'".$name."',$mobo_service_id, $server_id, '".$access_token."','".$time."') "."ON DUPLICATE KEY UPDATE `access_token`='".$access_token."',fid=$fid,`name`='".$name."'");
        if ($this->_db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function get_config() {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        $query = $this->_db->select("id, name, status, client_id")->get("facebook_config");
        if ($query == true && $query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function get_config_by_id($server_id) {
	    if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        $query = $this->_db->select("client_id, client_secret")
            ->where("status", 1)
            ->where("server_id", $server_id)
            ->get("facebook_config");
        if ($query == true && $query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return false;
        }
    }
    
    public function get_config_none($server_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        $query = $this->_db->select("id, client_id, client_secret")
            ->where("status", 0)
            ->where("server_id", $server_id)
            ->limit(1)
            ->get("facebook_config");
        if ($query == true && $query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return false;
        }
    }

    public function set_config($id) {

        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), true);

        $this->_db->where('status', 1);
        $this->_db->update('facebook_config', array("status" => 2));

        $this->_db->where('id', $id);
        $this->_db->update('facebook_config', array("status" => 1));
        return $this->_db->affected_rows();
    }

    public function insert_access_token($fid, $accesstoken, $client_ip) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $query = $this->_db->select("id, fid")->where("fid", $fid)->get("facebook_access_tokens");

        if ($query == true && $query->num_rows() > 0) {
            $params = array(
                "accesstoken" => $accesstoken,
                "client_ip" => $client_ip,
                "create_date" => date("y-m-d H:i:s", time())
            );
            $this->_db->where("fid", $fid)
                    ->update('facebook_access_tokens', $params);

            if ($this->_db->affected_rows() > 0) {
                $row = $query->row_array();
                return $row["id"];
            } else {
                return 0;
            }
        } else {
            $params = array("fid" => $fid,
                "accesstoken" => $accesstoken,
                "client_ip" => $client_ip,
                "create_date" => date("y-m-d H:i:s", time())
            );
            $this->_db->insert('facebook_access_tokens', $params);

            return $this->_db->insert_id();
        }
    }

    public function query_access_token($fid) {
        $query = $this->_db->select("fid, access_token")->where("fid", $fid)->get("facebook_access_tokens");
        if ($query == true && $query->num_rows() > 0) {
            $row = $query->row_array();
            return $row["access_token"];
        } else {
            return "";
        }
    }
	public function checkinvite($server_id, $character_id,$typeinvite){
		if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        $query = $this->_db->select("*")
		->where('server_id',$server_id)
		->where("character_id",$character_id)
		->where("create_date >=", date("y-m-d 00:00:00", time()))
        ->where("create_date <=", date("y-m-d 23:59:59", time()))
		->where('type',$typeinvite)
		->get("facebook_giftcode");
        if ($query == true && $query->num_rows() > 0) {
            return true;
        }else{
			return false;
		}
		//->where('date(`create_date`)',date('Y-m-d',time()))
		
		return true;
	}
    public function check_invite($from_fid, $to_fid) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $query = $this->_db->select("id, to_fname")
                ->where("from_fid", $from_fid)
                ->where("to_fid", $to_fid)
                ->get("facebook_invites");

        if ($query == true && $query->num_rows() > 0) {
            $row = $query->row_array();
            return array("code" => 1, "name" => $row["to_fname"], "is_aready" => 1);
        } else {
            return array("code" => 0, "name" => "");
        }
    }

    public function get_list_invite($fid) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $query = $this->_db->select("id, from_fid, from_fname")
                ->where("to_fid", $fid)
                ->where("is_accept", 0)
                ->get("facebook_invites");
        return $query->result_array();
    }

    public function get_invite_success($character_id, $fid, $server_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $query = $this->_db->select("to_fid")
                ->where("from_fid", $fid)
                ->where("character_id", $character_id)
                ->where("server_id", $server_id)
                ->where("is_accept", 1)
                ->get("facebook_invites");
        return $query->result_array();
    }
    
    public function get_excluded($fid) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $query = $this->_db->select("type,to_invitable_friends")
                ->where("fid", $fid)
                ->where("create_date >=", date("y-m-d 00:00:00", time()))
                ->where("create_date <=", date("y-m-d 23:59:59", time()))
                ->get("facebook_excluded");
        //$this->_db->close();
        return $query->result_array();
    }
    
    public function get_giftcode($character_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
//
//        $query = $this->_db->select("id,code,type,times,create_date,accept_name")
//                ->where("character_id", $character_id)
//                ->where("is_used", 0)
//                ->order_by("create_date", "desc")
//                ->get("facebook_giftcode");
//        return $query->result_array();
        
        $query = "SELECT *
              FROM facebook_giftcode as p
        WHERE p.is_used = 0 AND p.type != 'accept' AND p.character_id = '$character_id' ORDER BY p.create_date DESC";
        
        $result=$this->_db->query($query);      
        return $result->result_array();  
    }
    
     public function get_list_invite_id($id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $query = $this->_db->select("character_id,character_name,server_id, to_fname as accept_name, mobo_service_id", false)
                ->where("id", $id)              
                ->get("facebook_invites");
         if ($query == true && $query->num_rows() > 0) {            
            return $query->row_array();
        } else {
            return false;
        }
        
    }
     
     public function get_select_giftcode($type, $times) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $query = $this->_db->select("id, code")
                ->where("is_used", 0)  
                ->where("times", $times)  
                ->where("type", $type)
                ->limit(1)
                ->get("facebook_store_giftcode");
         if ($query == true && $query->num_rows() > 0) {            
            return $query->row_array();
        } else {
            return false;
        }        
    }

    public function check_share($server_id, $character_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $query = $this->_db->select("create_date")
                ->where("server_id", $server_id)
                ->where("character_id", $character_id)
                ->order_by("create_date", "desc")
                ->limit(1)
                ->get("facebook_shares");

        if ($query == true && $query->num_rows() > 0) {
            $row = $query->row_array();
            return $row["create_date"];
        } else {
            return false;
        }
    }
	

    public function check_count_accept_in_day($server_id, $mobo_service_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $query = $this->_db->select("count(*) as times", false)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("invite_date >=", date("y-m-d 00:00:00", time()))
                ->where("invite_date <=", date("y-m-d 23:59:59", time()))
                ->get("facebook_invites");
        //var_dump($query->row_array());die;
        //$this->_db->close();
        if ($query == true && $query->num_rows() > 0) {
            $row = $query->row_array();
            return $row["times"];
        } else {
            return 0;
        }
    }
    
    public function check_accept_limit_in_day($server_id, $character_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $query = $this->_db->select("count(0) as times", false)
                ->where("server_id", $server_id)
                ->where("character_id", $character_id)
                ->where("create_date >=", date("y-m-d 00:00:00", time()))
                ->where("create_date <=", date("y-m-d 23:59:59", time()))
                ->where("type", "accept")
                ->limit(1)
                ->get("facebook_giftcode");
        //$this->_db->close();
        if ($query == true && $query->num_rows() > 0) {
            $row = $query->row_array();
            return $row["times"] + 1;
        } else {
            return 0;
        }
    }
    
    public function check_exist_data($character_id, $server_id, $data, $used = 0) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $query = $this->_db->select("id", false)
                ->where("create_date >=", date("y-m-d 00:00:00", time()))
                ->where("create_date <=", date("y-m-d 23:59:59", time()))
                ->where("server_id", $server_id)
                ->where("character_id", $character_id)
                ->where_in("type", $data)
                ->where("is_used", $used)
                ->limit(1)
                ->get("facebook_giftcode");
        ////$this->_db->close();
        //echo $this->_db->last_query();die;
        if ($query == true && $query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    public function check_share_rule($server_id, $mobo_service_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $query = $this->_db->select("count(0) as times", false)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("create_date >=", date("y-m-d 00:00:00", time()))
                ->where("create_date <=", date("y-m-d 23:59:59", time()))
                /*->where("is_award", 1)
                ->limit(1)*/
                ->get("facebook_shares");
       
        if ($query == true && $query->num_rows() > 0) {
            $row = $query->row_array();           
            return $row["times"];
        } else {
            return 0;
        }
    }
	public function checkshare($server_id, $character_id,$typeinvite){
        $query = $this->_db->select("*")
			->where('server_id',$server_id)
			->where("character_id",$character_id)
			->where('type',$typeinvite)
			->where('date(`create_date`)',date('Y-m-d',time()))
			->get("facebook_giftcode");
        if ($query == true && $query->num_rows() > 0) {
            return true;
        }else{
			return false;
		}
		
		return true;
	}
    
    public function insert_excluded($params) {

        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $this->_db->insert('facebook_excluded', $params);

        $newid = $this->_db->insert_id();
        //$this->_db->close();
        return $newid;
    }

    public function insert_like($params) {

        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $this->_db->insert('facebook_likes', $params);

        return $this->_db->insert_id();
    }

    public function insert_share($params) {

        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $this->_db->insert('facebook_shares', $params);

        return $this->_db->insert_id();
    }

    public function update_share($id, $is_award) {

        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), true);

        $this->_db->where('id', $id);
        $this->_db->update('facebook_shares', array("is_award" => $is_award));
        return $this->_db->affected_rows();
    }
    
    public function update_store_giftcode($id) {

        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), true);

        $this->_db->where('id', $id);
        $this->_db->update('facebook_store_giftcode', array("is_used" => 1, "used_date" => date("y-m-d H:i:s", time())));
        return $this->_db->affected_rows();
    }

    public function update_invite($id, $fbid, $fromid, $accept_server_id, $accept_character_id) {

        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), true);

        $this->_db->where('to_fid', $fbid);
        $this->_db->where('from_fid', $fromid);
        $this->_db->update('facebook_invites', array("is_accept" => 2, "server_accept" => $accept_server_id, "character_accept" => $accept_character_id, "accept_date" => date("y-m-d H:i:s")));

        $this->_db->where('id', $id);
        $this->_db->update('facebook_invites', array("is_accept" => 1));
        $update_id = $this->_db->affected_rows();
        //$this->_db->close();
        return $update_id;
    }
    
    public function rollback_invite($id) {

        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), true);

        $this->_db->where('id', $id);
        $this->_db->update('facebook_invites', array("is_accept" => 0));
        $update_id = $this->_db->affected_rows();
        //$this->_db->close();
        return $update_id;
    }

    public function insert_invite($params) {

        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $this->_db->insert('facebook_invites', $params);

        return $this->_db->affected_rows();
    }
    
     public function insert_use_giftcode($params) {

        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $this->_db->insert('facebook_giftcode', $params);

        return $this->_db->affected_rows();
    }

    public function update_like($account_id, $character_id, $server_id, $params) {

        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), true);

        $this->_db_slave->where('account_id', $account_id);

        $this->_db_slave->where('character_id', $character_id);

        $this->_db_slave->where('server_id', $server_id);

        $this->_db_slave->limit(1);

        $this->_db->update('facebook_likes', $params);

        return $this->_db->affected_rows();
    }

    public function get_like($account_id, $character_id, $server_id) {

        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);

        $this->_db_slave->where('account_id', $account_id);

        $this->_db_slave->where('character_id', $character_id);

        $this->_db_slave->where('server_id', $server_id);

        $this->_db_slave->limit(1);

        $data = $this->_db_slave->get('facebook_likes');

        if (is_object($data))
            return $data->row_array();

        return FALSE;
    }

    public function get_message() {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $query = $this->_db_slave->select("id, name,message,type,photo,link")
                ->where("status", 1)
                ->get("facebook_share_data");

        if ($query == true && $query->num_rows() > 0) {
            $count = $query->num_rows();
            $random = rand(0, $count - 1);
            $row = $query->result_array();
            return $row[$random];
        } else {
            return false;
        }
    }

    public function getError() {

        return $this->_db->_error_message();
    }
    
    //Tuan Duong Edited    
    public function check_like($character_id, $server_id) {

        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
		
		//$data = $this->_db_slave->query("SELECT * FROM facebook_likes WHERE (character_id = {$character_id} AND server_id= {$server_id}) OR from_fid ={$fid}");
        $this->_db_slave->where('character_id', $character_id);
        $this->_db_slave->where('server_id', $server_id);    
     
        $data = $this->_db_slave->get('facebook_likes');
		
        if (is_object($data))
            return $data->row_array();

        return FALSE;
    }
    
    public function update_like_new($id) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);

        $this->_db_slave->where('id', $id);
        $this->_db_slave->update('facebook_likes', array("send_code_status" => 1));
        return $this->_db_slave->affected_rows();
    }
    
    //Invite Point    
    function user_check_point_invite_exist($char_id, $server_id, $mobo_service_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        //$start = date("Y-m-d 00:00:00", time());
        //$end = date("Y-m-d 23:59:59", time());
        
        $query = $this->_db->select("*")
                ->from("facebook_invites_point")
                ->where("char_id", $char_id)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                //->where("point_date >=", $start)
                //->where("point_date <=", $end)
                ->get();

        return $query->result_array();
    }
    
    function get_user_point_invite($char_id, $server_id, $mobo_service_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        //$start = date("Y-m-d 00:00:00", time());
        //$end = date("Y-m-d 23:59:59", time());
        
        $query = $this->_db->select("*")
                ->from("facebook_invites_point")
                ->where("char_id", $char_id)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                //->where("point_date >=", $start)
                //->where("point_date <=", $end)
                ->get();

        return $query->result_array();
    }
    
    function user_list_point_invite() {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        //$start = date("Y-m-d 00:00:00", time());
        //$end = date("Y-m-d 23:59:59", time());
        
        $query = $this->_db->select("*")
                ->from("facebook_invites_point")              
                //->where("point_date >=", $start)
                //->where("point_date <=", $end)
                ->order_by("user_point", "desc")
                ->order_by("point_date", "asc")
                ->limit(100)
                ->get();

        return $query->result_array();
    }    
   
    function add_point_invite($char_id, $server_id, $mobo_service_id, $pet_point) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        //$start = date("Y-m-d 00:00:00", time());
        //$end = date("Y-m-d 23:59:59", time());
        $point_date = Date('Y-m-d H:i:s');
        $this->_db
                ->set("user_point", "`user_point` + $pet_point", false)
                ->set("point_date", $point_date)
                ->where("char_id", $char_id)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id);
                //->where("point_date >=", $start)
                //->where("point_date <=", $end);

        $this->_db->update("facebook_invites_point");
        return $this->_db->affected_rows();
    }
    
    function update_point_invite($char_id, $server_id, $mobo_service_id, $pet_point) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        
        //$start = date("Y-m-d 00:00:00", time());
        //$end = date("Y-m-d 23:59:59", time());
        
        $this->_db
                ->set("user_point", "user_point - $pet_point", false)
                ->where("char_id", $char_id)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id);
                //->where("point_date >=", $start)
                //->where("point_date <=", $end);

        $this->_db->update("facebook_invites_point");
        return $this->_db->affected_rows();
    }
    
    public function get_user_current_point($char_id, $server_id, $mobo_service_id)
    {   
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        
        $query = "SELECT c.*
                    FROM
                    (
                        SELECT @ranking:= @ranking + 1 rank, a.*
                        FROM 
                        (
                            SELECT *
                            FROM    `facebook_invites_point`       
                            ORDER BY user_point DESC, `point_date`
                        ) a,
                        (SELECT @ranking := 0) b
                    ) c
                    WHERE c.mobo_service_id = '$mobo_service_id' AND c.server_id = $server_id AND c.char_id = '$char_id'";
        
    	$result=$this->_db->query($query); 
        //echo  $this->_db->last_query(); die;
        return $result->result_array(); 
    }
    
    //Invite Point Crystal
    function user_check_point_invite_crystal_exist($char_id, $server_id, $mobo_service_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $start = date("Y-m-d 00:00:00", time());
        $end = date("Y-m-d 23:59:59", time());
        
        $query = $this->_db->select("*")
                ->from("facebook_invites_point_crystal")
                ->where("char_id", $char_id)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("point_date >=", $start)
                ->where("point_date <=", $end)
                ->get();

        //echo $this->_db->last_query(); die;
        return $query->result_array();
    }
    
    function get_user_point_invite_crystal($char_id, $server_id, $mobo_service_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $start = date("Y-m-d 00:00:00", time());
        $end = date("Y-m-d 23:59:59", time());
        
        $query = $this->_db->select("*")
                ->from("facebook_invites_point_crystal")
                ->where("char_id", $char_id)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("point_date >=", $start)
                ->where("point_date <=", $end)
                ->get();

        return $query->result_array();
    }
    
    function add_point_crystal_invite($char_id, $server_id, $mobo_service_id, $pet_point) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $start = date("Y-m-d 00:00:00", time());
        $end = date("Y-m-d 23:59:59", time());
        $point_date = Date('Y-m-d H:i:s');
        $this->_db
                ->set("user_point", "`user_point` + $pet_point", false)
                ->set("point_date", $point_date)
                ->where("char_id", $char_id)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("point_date >=", $start)
                ->where("point_date <=", $end);

        $this->_db->update("facebook_invites_point_crystal");
        return $this->_db->affected_rows();
    }
    
    function update_point_crystal_receive($char_id, $server_id, $mobo_service_id, $received){
        $start = date("Y-m-d 00:00:00", time());
        $end = date("Y-m-d 23:59:59", time());
        
        $this->_db
                ->set("received", "received + $received", false)               
                ->where("char_id", $char_id)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("point_date >=", $start)
                ->where("point_date <=", $end);

        $this->_db->update("facebook_invites_point_crystal");
        //echo $this->_db->last_query();die;
        return $this->_db->affected_rows();
    }
    
    public function check_exist_gift_exchange($char_id, $server_id, $mobo_service_id){
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);   
        
        $query = $this->_db->select("*")
                ->from("facebook_invite_top_exchange_history")
                ->where("char_id", $char_id)
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->get(); 
        
        if ($query->num_rows() > 0) {            
            return true;
        } else {
            return false;
        }        
    }
    
    public function update_exchange_history($id, $data_send, $data_result){
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE); 
        
        $this->_db
                ->set("data_send", $data_send)
                ->set("data_result", $data_result)
                ->where("id", $id);

        $this->_db->update("facebook_invite_top_exchange_history");
        return $this->_db->affected_rows();
	}
    
    ///////////////////////////////////
    function insert($table, $data) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        
        $query = $this->_db->insert($table, $data);
        if ($this->_db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function insert_id($table, $data) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        
        $query = $this->_db->insert($table, $data);
        $idinsert = $this->_db->insert_id();
        if ($this->_db->affected_rows() > 0) {
            return $idinsert;
        } else {
            return false;
        }
    }
    
    //Mission
    public function insert_use_gift_mission($params) {

        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $this->_db->insert('facebook_gift_mission', $params);

        return $this->_db->affected_rows();
    }
    
    function user_check_gift_mission($server_id, $mobo_service_id, $type) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $start = date("Y-m-d 00:00:00", time());
        $end = date("Y-m-d 23:59:59", time());
        
        $query = $this->_db->select("*")
                ->from("facebook_gift_mission")               
                ->where("server_id", $server_id)
                ->where("mobo_service_id", $mobo_service_id)
                ->where("type", $type)
                ->where("create_date >=", $start)
                ->where("create_date <=", $end)
                ->get();

         if ($query->num_rows() > 0) {            
            return true;
        } else {
            return false;
        }
    }

}
