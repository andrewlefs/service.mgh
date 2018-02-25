<?php
class navigation_model extends CI_Model {
    private $_db_slave;
    private $_db_master;
    function __construct()
    {
        parent::__construct();
        if (empty($this->_db_slave))
            $this->_db_slave = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);
        if (empty($this->_db_master))
            $this->_db_master = $this->load->database(array('db' => 'system_info', 'type' => 'master'), true);
        //$this->load->model('cms/dblog_model');
    }
    function onGet($id) {
        $query = $this->_db_slave
                ->where('service_id', $id)
                ->get('service_navigation_event');
        return $query->row();
    }
    function onGets() {
        $query = $this->_db_slave
                ->from('service_navigation_event')
                ->order_by('service_order', 'ASC')
                ->get();
        return $query->result();
    }
    function onInsert($params) {
        $this->_db_master->set('service_insert', 'NOW()', FALSE);
        @$this->_db_master->insert('service_navigation_event', $params);
        @$count = $this->_db_master->affected_rows(); //should return the number of rows affected by the last query
        if ($count == 1)
            return true;
        return false;
    }
    function onUpdate($id, $params) {
        $this->_db_master->set('service_update', 'NOW()', FALSE);
        $this->_db_master->where('service_id', $id);
        @$this->_db_master->update('service_navigation_event', $params);
        @$count = $this->_db_master->affected_rows(); //should return the number of rows affected by the last query
        if ($count == 1)
            return true;
        return false;
    }
}
?>
