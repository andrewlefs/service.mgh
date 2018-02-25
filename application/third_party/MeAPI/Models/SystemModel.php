<?php

/**

 * @property CI_DB_active_record $db

 */
class SystemModel extends CI_Model {

    private $db_slave;

    public function __construct() {
        
    }

    public function get_app($app_name) {

        if (!$this->db_slave)
            $this->db_slave = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);

        $this->db_slave->where('app_name', $app_name);

        $this->db_slave->limit(1);

        $data = $this->db_slave->get('scopes');

        if (is_object($data))
            return $data->row_array();
    }

    public function get_service($service) {

        if (!$this->db_slave)
            $this->db_slave = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);

        $this->db_slave->where('service', $service);

        $this->db_slave->limit(1);

        $data = $this->db_slave->get('scopes');

        if (is_object($data))
            return $data->row_array();
    }

    public function get_telcos() {

        if (!$this->db_slave)
            $this->db_slave = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);

        $data = $this->db_slave->get('telcos');

        if (is_object($data))
            return $data->result_array();
    }

}