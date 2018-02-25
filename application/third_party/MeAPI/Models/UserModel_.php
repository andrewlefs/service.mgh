<?php

/**
 * @property CI_DB_active_record $db
 */
class UserModel extends CI_Model {

    private $_db;
    private $_db_slave;

    public function __construct() {
        
    }

    public function insert_access_token($params) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        $this->_db->insert('access_tokens', $params);
        return $this->_db->insert_id();
    }

    public function get_account($account = NULL, $facebook_id = NULL) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        if (empty($account) === FALSE) {
            $this->_db_slave->or_where('account', $account);
        }
        if (empty($facebook_id) === FALSE) {
            $this->_db_slave->or_where('account', $facebook_id . '@fb.com');
        }
        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get('accounts');

        if (is_object($data))
            return $data->row_array();
    }

    public function get_account_by_id($account_id) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->where('id', $account_id);
        $data = $this->_db_slave->get('accounts');
        if (is_object($data))
            return $data->row_array();
    }

    public function insert_account($params = array()) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        if (empty($params['facebook_id']) === FALSE && is_numeric($params['facebook_id']) === TRUE) {
            $params['account'] = $params['facebook_id'] . '@fb.com';
        }
        unset($params['facebook_id']);
        $this->_db->insert('accounts', $params);
        return $this->_db->insert_id();
    }

    public function get_trial_account($device_id) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->where('device_id', $device_id);
        $this->_db_slave->where('account', NULL);
        $data = $this->_db_slave->get('accounts');
        if (is_object($data))
            return $data->result_array();
    }

    public function get_access_token($access_token) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->where('access_token', $access_token);
        $data = $this->_db_slave->get('access_tokens');
        if (is_object($data))
            return $data->row_array();
    }

    public function get_account_info($account_id) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->where('id', $account_id);
        $data = $this->_db_slave->get('accounts');
        if (is_object($data))
            return $data->row_array();
    }

    public function update_last_login_time($uid) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), true);
        $this->_db->where('id', intval($uid));
        $this->_db->set('last_login_time', 'NOW()', false);
        //$this->_db->limit(1);
        $this->_db->update('accounts');
        return $this->_db->affected_rows();
    }

    public function update_account($uid, $params = array()) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), true);
        $this->_db->where('id', intval($uid));
        //$this->_db->limit(1);
        $this->_db->update('accounts', $params);
        return $this->_db->affected_rows();
    }

    public function store_access_token($params) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), true);
        $query = 'INSERT INTO `notifications`(`account_id`,`character_id`,`server_id`,`device_token`,`platform`) VALUES ('
                . $this->_db->escape($params['account_id']) . ','
                . $this->_db->escape($params['character_id']) . ','
                . $this->_db->escape($params['server_id']) . ','
                . $this->_db->escape($params['device_token']) . ','
                . $this->_db->escape($params['platform']) . ') ON DUPLICATE KEY UPDATE `date_update`= NOW(), `device_token` = ' . $this->_db->escape($params['device_token']);
        $this->_db->query($query);
        //echo $this->_db->last_query();die;
        return $this->_db->affected_rows();
    }

    /*
      public function get_user($username) {
      if (!$this->_db_slave)
      $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
      $this->_db_slave->where('username', $username);
      $this->_db_slave->limit(1);
      $data = $this->_db_slave->get('team_user');
      if (is_object($data))
      return $data->row_array();
      }

      public function get_register_with_key_by_username($username) {
      if (!$this->_db_slave)
      $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
      $this->_db_slave->where('username', $username);
      $this->_db_slave->limit(1);
      $data = $this->_db_slave->get('reg_user');
      if (is_object($data))
      return $data->row_array();
      }

      public function get_register_with_key_by_username_by_key($key) {
      if (!$this->_db_slave)
      $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
      $this->_db_slave->where('regkey', $key);
      $this->_db_slave->limit(1);
      $data = $this->_db_slave->get('reg_user');
      if (is_object($data))
      return $data->row_array();
      }

      public function register($params = array()) {
      if (!$this->_db)
      $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
      $this->_db->set('regdate', 'NOW()', false);
      $this->_db->insert('team_user', $params);
      return $this->_db->insert_id();
      }

      public function get_register_server_private_history($phone) {
      if (!$this->_db_slave)
      $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
      $this->_db_slave->where('phone', $phone);
      $this->_db_slave->limit(1);
      $data = $this->_db_slave->get('reg_history');
      if (is_object($data))
      return $data->row_array();
      }

      public function insert_register_server_private_history($params = array()) {
      if (!$this->_db)
      $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
      $this->_db->set('regtime', 'NOW()', false);
      $this->_db->insert('reg_history', $params);
      return $this->_db->insert_id();
      }

      public function insert_register_with_key($params = array()) {
      if (!$this->_db)
      $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
      $this->_db->set('regdate', 'NOW()', false);
      $this->_db->insert('reg_user', $params);
      return $this->_db->insert_id();
      }

      public function update_user($uid, $params = array()) {
      if (!$this->_db)
      $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), true);
      $this->_db->where('id', intval($uid));
      $this->_db->limit(1);
      $this->_db->update('team_user', $params);
      return $this->_db->affected_rows();
      }

      public function delete_register_with_key($key) {
      if (!$this->_db)
      $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), true);
      $this->_db->delete('reg_user', array('regkey' => $key));
      return $this->_db->affected_rows();
      }

      public function create_password_history($username, $service_id, $description) {
      if (!$this->_db)
      $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
      $params = array('username' => $username, 'service_id' => $service_id, 'description' => $description);
      $this->_db->set('datecreate', 'NOW()', false);
      $this->_db->insert('password_history_' . intval(date('m')) . '_' . intval(date('Y')), $params);
      return $this->_db->insert_id();
      }

      public function get_password_history($username, $month, $year) {
      if (!$this->_db_slave)
      $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
      $this->_db_slave->where('username', $username);
      $this->_db_slave->order_by('datecreate','DESC');
      $data = $this->_db_slave->get('password_history_' . intval($month) . '_' . intval($year));
      if (is_object($data))
      return $data->result_array();
      }
     */

    public function getError() {
        return $this->_db->_error_message();
    }

}