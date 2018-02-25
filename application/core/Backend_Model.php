<?php 
if (!class_exists('Backend_Model')) {
class Backend_Model extends CI_Model {
    private $configs;
    private $_db_slave;
    private $_db_master;
    function __construct($table = '', $prefix = '',$colid='id') {
        parent::__construct();
        $this->table = $table;
        $this->prefix = $prefix;
		$this->colid = $colid;
		$this->configs["strQuery"] = "SELECT  SQL_CALC_FOUND_ROWS * from $table ";
        $this->configs["strWhere"] = "WHERE TRUE";
        $this->configs["strGroupBy"] = "";
        $this->configs["strOrderBy"] = "";
        $this->configs["usingLimit"] = true;
        $this->configs["filterfields"] = array();
        $this->configs["fields"] = array();
        $this->configs["datefields"] = array();
        if (empty($this->_db_slave))
            $this->_db_slave = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);
        if (empty($this->_db_master))
            $this->_db_master = $this->load->database(array('db' => 'system_info', 'type' => 'master'), true);
    }

    function _get_last_insert_id() {
        $query = $this->_db_slave->query("SELECT LAST_INSERT_ID() as last_insert_id ;");
        $row = $query->row();
        return $row->last_insert_id;
    }

    function _get($id) {
        $query = $this->_db_slave
                ->where($this->prefix . '_id', $id)
                ->get($this->table);
        return $query->row();
    }
    function onGet($id) {
        $query = $this->_db_slave
                ->where("$this->prefix$this->colid", $id)
                ->get($this->table);
        return $query->row();
    }
    function gets() {
        $query = $this->_db_slave
                ->from($this->table)
                ->order_by($this->prefix . '_insert', 'DESC')
                ->get();
        return $query->result();
    }
    function onGets() {
        $query = $this->_db_slave
                ->from($this->table)
                ->order_by($this->prefix . 'insert', 'DESC')
                ->get();
        return $query->result();
    }
    function _insert($params) {
        $this->_db_master->set($this->prefix . '_insert', 'NOW()', FALSE);
        @$this->_db_master->insert($this->table, $params);
        @$count = $this->_db_master->affected_rows(); //should return the number of rows affected by the last query
        if ($count == 1)
            return true;
        return false;
    }
    function onInsert($params) {
        $this->_db_master->set($this->prefix . 'insert', 'NOW()', FALSE);
        @$this->_db_master->insert($this->table, $params);
        @$count = $this->_db_master->affected_rows(); //should return the number of rows affected by the last query
        if ($count == 1)
            return true;
        return false;
    }
    function _delete($id) {
//        $this->db->set($this->prefix.'_delete', 'NOW()', FALSE);
        $where = array($this->prefix . '_id' => $id);
//        $this->db->where($where);
//        $this->db->update($this->table); 
        $this->_db_master->delete($this->table, $where);
        $count = $this->_db_master->affected_rows(); //should return the number of rows affected by the last query
        if ($count == 1)
            return true;
        return false;
    }
    function onDelete($id) {
//        $this->db->set($this->prefix.'_delete', 'NOW()', FALSE);
        $where = array("$this->prefix$this->colid" => $id);
//        $this->db->where($where);
//        $this->db->update($this->table); 
        $this->_db_master->delete($this->table, $where);
        $count = $this->_db_master->affected_rows(); //should return the number of rows affected by the last query
        if ($count == 1)
            return true;
        return false;
    }
    function _retore($id) {
        $this->_db_master->set($this->prefix . '_delete', 'NULL', FALSE);
        $where = array("$this->prefix$this->colid" => $id);
        $this->_db_master->where($where);
        $this->_db_master->update($this->table);
        $count = $this->_db_master->affected_rows(); //should return the number of rows affected by the last query
        if ($count == 1)
            return true;
        return false;
    }
    function _update($id, $params) {
        $this->_db_master->set($this->prefix . '_update', 'NOW()', FALSE);
        $this->_db_master->where($this->prefix . '_id', $id);
        @$this->_db_master->update($this->table, $params);
        @$count = $this->_db_master->affected_rows(); //should return the number of rows affected by the last query
        if ($count == 1)
            return true;
        return false;
    }
    function onUpdate($id, $params) {
        $this->_db_master->set($this->prefix . 'update', 'NOW()', FALSE);
        $this->_db_master->where("$this->prefix$this->colid", $id);
        @$this->_db_master->update($this->table, $params);
        @$count = $this->_db_master->affected_rows(); //should return the number of rows affected by the last query
        if ($count == 1)
            return true;
        return false;
    }
    function init($configs) {
        if (isset($configs["strQuery"]))
            $this->configs["strQuery"] = $configs["strQuery"];
        if (isset($configs["strWhere"]))
            $this->configs["strWhere"] = $configs["strWhere"];
        if (isset($configs["strOrderBy"]))
            $this->configs["strOrderBy"] = $configs["strOrderBy"];
        if (isset($configs["strGroupBy"]))
            $this->configs["strGroupBy"] = $configs["strGroupBy"];
        if (isset($configs["fields"]))
            $this->configs["fields"] = $configs["fields"];
        if (isset($configs["datefields"]))
            $this->configs["datefields"] = $configs["datefields"];
        if (isset($configs["filterfields"]))
            $this->configs["filterfields"] = $configs["filterfields"];
        if (isset($configs["usingLimit"]))
            $this->configs["usingLimit"] = $configs["usingLimit"];
    }
    function databinding(){
        if(!isset($this->datatables_config)){
            if(empty($this->table)){
                echo json_encode(array('result'=>-1,'message'=>"Table invalid !"));
                die;
            }
            $this->datatables_config=array(
                    "table"=>$this->table,
                    "order_by"=>"ORDER BY `{$this->prefix}Insert` DESC",
                    "columnmaps"=>array(
                    )            
            );
        }
        return $this->_bindingdata();
    }
    function _bindingdata(){
        $method=$_REQUEST;
        if(isset($method['sEcho']) ||
           isset($method['sSearch']) ||
           isset($method['iColumns'])
           )
        {
            return $this->datatableBinding();
        }
        else{
            return $this->jqxBinding();
        }
    }
     function jqxBinding() {
        $method=$_GET;
        $pagenum = isset($method['pagenum']) ? $method['pagenum'] : 0;
        $pagesize = isset($method['pagesize']) ? $method['pagesize'] : 10;
        $start = $pagenum * $pagesize;
        if(!empty($this->datatables_config)){
            if(!empty($this->datatables_config["select"])){
                $FstrSQL = $select = (!empty($this->datatables_config["select"])?$this->datatables_config["select"]:"")
                    ." ".
                    (!empty($this->datatables_config["from"])?$this->datatables_config["from"]:"");
            }else{
                $FstrSQL="SELECT SQL_CALC_FOUND_ROWS `{$this->datatables_config["table"]}`.* FROM `{$this->datatables_config["table"]}`";
            }
            $where = (!empty($this->datatables_config["where"])?$this->datatables_config["where"]:"Where true");
            $strgroupby = (!empty($this->datatables_config["group_by"])?$this->datatables_config["group_by"]:"");
            $orderby = (!empty($this->datatables_config["order_by"])?$this->datatables_config["order_by"]:"");
            $fields = (!empty($this->datatables_config["columnmaps"])?$this->datatables_config["columnmaps"]:array());
            $datefields = (!empty($this->datatables_config["datefields"])?$this->datatables_config["datefields"]:array());
            $limit = "";
            if (isset($this->datatables_config["limit"]) && $this->datatables_config["limit"]) {
                $limit = "LIMIT $start, $pagesize";
            }else{
                if($pagesize==10)
                    $pagesize=1000;
                $limit = "LIMIT $start, $pagesize";
            }
        }else{
            $FstrSQL = $this->configs["strQuery"];
            $select = $this->configs["strQuery"];
            $where = $this->configs["strWhere"];
            $strgroupby = $this->configs["strGroupBy"];
            $orderby = $this->configs["strOrderBy"];
            $fields = $this->configs["fields"];
            $datefields = $this->configs["datefields"];
            $limit = "";
            if (isset($this->configs["usingLimit"]) && $this->configs["usingLimit"]) {
                $limit = "LIMIT $start, $pagesize";
            }else{
                $limit = "LIMIT 1000";
            }
        }
        
        

        if (isset($method['filterscount'])) {
            $filterscount = $method['filterscount'];

            if ($filterscount > 0) {
                $where.= " AND (";
                $tmpdatafield = "";
                $tmpfilteroperator = "";
                for ($i = 0; $i < $filterscount; $i++) {
                    // get the filter's value.
                    $filtervalue = $method["filtervalue" . $i];
                    // get the filter's condition.
                    $filtercondition = $method["filtercondition" . $i];
                    // get the filter's column.
                    $filterdatafield = $method["filterdatafield" . $i];
                    // get the filter's operator.
                    $filteroperator = $method["filteroperator" . $i];

                    if ($filterdatafield[0] === "_" && $filterdatafield[strlen($filterdatafield) - 1] === "_") {
                        $filterdatafield = substr($filterdatafield, 1, -1);
                    }


                    if (count($datefields) > 0 && in_array($filterdatafield, $datefields)) {
                        $tmp = explode("GMT", $filtervalue);
                        if (isset($tmp[0])) {
                            $filtervalue = date("Y-m-d H:i:s", strtotime($tmp[0]));
                        }
                    }
                    $filtervalue = $this->db->escape_str($filtervalue);
                    if (count($fields) > 0 && isset($fields[$filterdatafield])) {
                        $filterdatafield = $fields[$filterdatafield];
                    } else {
                        $filterdatafield = "`$filterdatafield`";
                    }

                    //check filterdatafield
                    if ($tmpdatafield == "") {
                        $tmpdatafield = $filterdatafield;
                    } else if ($tmpdatafield <> $filterdatafield) {
                        $where .= " ) AND ( ";
                    } else if ($tmpdatafield == $filterdatafield) {
                        if ($tmpfilteroperator == 0) {
                            $where .= " AND ";
                        }
                        else
                            $where .= " OR ";
                    }

                    // build the "WHERE" clause depending on the filter's condition, value and datafield.
                    // possible conditions for string filter: 
                    //      'EMPTY', 'NOT_EMPTY', 'CONTAINS', 'CONTAINS_CASE_SENSITIVE',
                    //      'DOES_NOT_CONTAIN', 'DOES_NOT_CONTAIN_CASE_SENSITIVE', 
                    //      'STARTS_WITH', 'STARTS_WITH_CASE_SENSITIVE',
                    //      'ENDS_WITH', 'ENDS_WITH_CASE_SENSITIVE', 'EQUAL', 
                    //      'EQUAL_CASE_SENSITIVE', 'NULL', 'NOT_NULL'
                    // 
                    // possible conditions for numeric filter: 'EQUAL', 'NOT_EQUAL', 'LESS_THAN',
                    //  'LESS_THAN_OR_EQUAL', 'GREATER_THAN', 'GREATER_THAN_OR_EQUAL', 
                    //  'NULL', 'NOT_NULL'
                    //  
                    // possible conditions for date filter: 'EQUAL', 'NOT_EQUAL', 'LESS_THAN', 
                    // 'LESS_THAN_OR_EQUAL', 'GREATER_THAN', 'GREATER_THAN_OR_EQUAL', 'NULL', 
                    // 'NOT_NULL'                         

                    switch ($filtercondition) {
                        case "NULL":
                            $where .= " ($filterdatafield is null)";
                            break;
                        case "EMPTY":
                            $where .= " ($filterdatafield is null) or ($filterdatafield='')";
                            break;
                        case "NOT_NULL":
                            $where .= " ($filterdatafield is not null)";
                            break;
                        case "NOT_EMPTY":
                            $where .= " ($filterdatafield is not null) and ($filterdatafield <>'')";
                            break;
                        case "CONTAINS_CASE_SENSITIVE":
                        case "CONTAINS":
                            if($filterdatafield =='`GiftCode`'){
                                $where .= " giftcode_log.$filterdatafield LIKE '%$filtervalue%'";
                            }
                            else
                                $where .= " $filterdatafield LIKE '%$filtervalue%'";
                            break;
                        case "DOES_NOT_CONTAIN_CASE_SENSITIVE":
                        case "DOES_NOT_CONTAIN":
                            $where .= " $filterdatafield NOT LIKE '%$filtervalue%'";
                            break;
                        case "EQUAL_CASE_SENSITIVE":
                        case "EQUAL":
                            $where .= " $filterdatafield = '$filtervalue'";
                            break;
                        case "NOT_EQUAL":
                            $where .= " $filterdatafield <> '$filtervalue'";
                            break;
                        case "GREATER_THAN":
                            $where .= " $filterdatafield > '$filtervalue'";
                            break;
                        case "LESS_THAN":
                            $where .= " $filterdatafield < '$filtervalue'";
                            break;
                        case "GREATER_THAN_OR_EQUAL":
                            $where .= " $filterdatafield >= '$filtervalue'";
                            break;
                        case "LESS_THAN_OR_EQUAL":
                            $where .= " $filterdatafield <= '$filtervalue'";
                            break;
                        case "STARTS_WITH_CASE_SENSITIVE":
                        case "STARTS_WITH":
                            $where .= " $filterdatafield LIKE '$filtervalue%'";
                            break;
                        case "ENDS_WITH_CASE_SENSITIVE":
                        case "ENDS_WITH":
                            $where .= " $filterdatafield LIKE '%$filtervalue'";
                            break;
                        default:
                            $where .= " $filterdatafield LIKE '%$filtervalue%'";
                    }

                    if ($i == $filterscount - 1) {
                        $where .= ")";
                    }

                    $tmpfilteroperator = $filteroperator;
                    $tmpdatafield = $filterdatafield;
                }
                // build the query.
            }
        }
        if (isset($method['sortdatafield'])) {
            $sortfield = $method['sortdatafield'];
            //fix sortfield
            if ($sortfield[0] === "_" && $sortfield[strlen($sortfield) - 1] === "_") {
                $sortfield = substr($sortfield, 1, -1);
            }

            if (count($fields) > 0 && isset($fields[$sortfield])) {
                $sortfield = $fields[$sortfield];
            } else {
                $sortfield = "`$sortfield`";
            }
            $sortorder = $method['sortorder'];

            if ($sortorder == "desc") {
                $SQLquery = "$FstrSQL $where $strgroupby ORDER BY $sortfield DESC $limit";
            } elseif ($sortorder == "asc") {
                $SQLquery = "$FstrSQL $where $strgroupby ORDER BY $sortfield ASC $limit";
            } else {
                $SQLquery = "$FstrSQL $where $strgroupby $orderby $limit";
            }
        } else {
            $SQLquery = "$FstrSQL $where $strgroupby $orderby $limit";
        }
        $_SESSION["debug"]["SQLquery"] = $SQLquery;
        $result['sQuery']=$SQLquery;
        $query = $this->_db_slave->query($SQLquery);
        $result['rows'] = $query->result();
        $sql = "SELECT FOUND_ROWS() AS `found_rows`;";
        $query = $this->_db_slave->query($sql);
        $rows = $query->row_array();
        $total_rows = (int)$rows['found_rows'];
        $result['totalrecords'] = $total_rows;
        $result['pagenum'] = (int)$pagenum;
        $result['pagesize'] = (int)$pagesize;
        $result['totalpages'] = ceil($result['totalrecords'] / $result['pagesize']);
        return $result;
        //$data['total_rows']=$total_rows;
    }
    function datatableBinding() {
        /* Array of database columns which should be read and sent back to DataTables. Use a space where
         * you want to insert a non-database field (for example a counter or static image)
         */
        //$aColumns = array( 'engine', 'browser', 'platform', 'version', 'grade' );

        /* Indexed column (used for fast and accurate table cardinality) */
        //$sIndexColumn = "id";

        /* DB table to use */
        $sTable = $this->datatables_config['table'];

        if (isset($this->datatables_config['where']))
            $sWhere = $this->datatables_config['where'];
        else
            $sWhere = 'WHERE true ';

        $sFrom = "FROM `$sTable`";
        if (isset($this->datatables_config['from'])) {
            $sFrom = $this->datatables_config['from'];
        }
        $method=$_REQUEST;
        $iColumns = isset($aColumns) ? count($aColumns) : (isset($method['iColumns']) ? $method['iColumns'] : 0);
        if (!isset($aColumns)) {
            for ($i = 0; $i < $iColumns; $i++) {
                if (isset($method["mDataProp_$i"]))
                    $aColumns[$i] = $method["mDataProp_$i"];
            }
        }


        /*
         * Paging
         */
        $sLimit = "";
        if (isset($method['iDisplayStart']) && $method['iDisplayLength'] != '-1') {
            $sLimit = "LIMIT " . intval($method['iDisplayStart']) . ", " .
                    intval($method['iDisplayLength']);
        }else{
            $sLimit = "LIMIT 1000";
        }


        /*
         * Ordering
         */
        $sOrder = "";
        if (isset($method['iSortCol_0'])) {
            $sOrder = "ORDER BY  ";
            for ($i = 0; $i < intval($method['iSortingCols']); $i++) {
                if ($method['bSortable_' . intval($method['iSortCol_' . $i])] == "true") {
                    if (isset($aColumns[intval($method['iSortCol_' . $i])])) {
                        $_colum = $aColumns[intval($method['iSortCol_' . $i])];
                    } elseif (isset($method["mDataProp_$i"])) {
                        $_colum = $method["mDataProp_$i"];
                    }
                    if (!empty($_colum)) {
                        if (isset($this->datatables_config['columnmaps'][$_colum])) {
                            $_colum = $this->datatables_config['columnmaps'][$_colum];
                        } else {
                            if ($_colum[0] !== "`" || $_colum[strlen($_colum) - 1] !== "`") {
                                $_colum = "`$_colum`";
                            }
                        }
                        $sOrder .= "$_colum " . ($method['sSortDir_' . $i] === 'asc' ? 'asc' : 'desc') . ", ";
                    }
                }
            }

            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY") {
                $sOrder = "";
            }
        }
        if ($sOrder == "")
            if (isset($this->datatables_config['order_by']))
                $sOrder = $this->datatables_config['order_by'];


        /*
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        if (isset($method['sSearch']) && $method['sSearch'] != "") {
            
            if (isset($aColumns)) {
                $sWhere .= " AND (";
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (isset($method['bSearchable_' . $i]) && $method['bSearchable_' . $i] == "true") {
                        if (isset($aColumns[$i])) {
                            $_colum = $aColumns[$i];
                        } elseif (isset($method["mDataProp_$i"])) {
                            $_colum = $method["mDataProp_$i"];
                        }
                        if (!empty($_colum)) {
                            if (isset($this->datatables_config['columnmaps'][$_colum])) {
                                $_colum = $this->datatables_config['columnmaps'][$_colum];
                            } else {
                                if ($_colum[0] !== "`" || $_colum[strlen($_colum) - 1] !== "`") {
                                    $_colum = "`$_colum`";
                                }
                            }
                            $sWhere .= $_colum . " LIKE '%" . $this->_db_slave->escape_str($method['sSearch']) . "%' OR ";
                        }
                    }
                }
                $sWhere = substr_replace($sWhere, "", -3);
                $sWhere .= ')';
            }
            
        }

        /* Individual column filtering */
        if (isset($aColumns)) {
            for ($i = 0; $i < count($aColumns); $i++) {
                if (
                        isset($method['bSearchable_' . $i]) && $method['bSearchable_' . $i] == "true" &&
                        $method['sSearch_' . $i] != ''
                ) {
                    if ($sWhere == "") {
                        $sWhere = "WHERE ";
                    } else {
                        $sWhere .= " AND ";
                    }
                    if (isset($aColumns[$i])) {
                        $_colum = $aColumns[$i];
                    } elseif (isset($method["mDataProp_$i"])) {
                        $_colum = $method["mDataProp_$i"];
                    }
                    if (!empty($_colum)) {
                        if (isset($this->datatables_config['columnmaps'][$_colum])) {
                            $_colum = $this->datatables_config['columnmaps'][$_colum];
                        } else {
                            if ($_colum[0] !== "`" || $_colum[strlen($_colum) - 1] !== "`") {
                                $_colum = "`$_colum`";
                            }
                        }
                        $sWhere .= $aColumns[$i] . " LIKE '%" . $this->_db_slave->escape_str($method['sSearch_' . $i]) . "%' ";
                    }
                }
            }
        } else {
            
        }

        /*
         * SQL queries
         * Get data to display
         */
        if (isset($aColumns)) {

            $sSelect = "SELECT SQL_CALC_FOUND_ROWS  ";
            for ($i = 0; $i < count($aColumns); $i++) {
                $_colum = $aColumns[$i];
                if (isset($this->datatables_config['columnmaps'][$_colum])) {
                    $_colum = $this->datatables_config['columnmaps'][$_colum];
                }
                if ($_colum[0] !== "`" || $_colum[strlen($_colum) - 1] !== "`") {
                    $_colum = "`$_colum`";
                }
                $sSelect.= "$_colum , ";
            }
            $sSelect = substr_replace($sSelect, "", -3);
            $sQuery = "
				$sSelect
				$sFrom
				$sWhere
				$sOrder
				$sLimit
			";
        } else {
            $sQuery = "
				SELECT SQL_CALC_FOUND_ROWS *
				$sFrom
				$sWhere
				$sOrder
				$sLimit
			";
        }
        //echo $sQuery;
        $query = $this->_db_slave->query($sQuery);
        $rows = $query->result();
        $sql = "SELECT FOUND_ROWS() AS `found_rows`;";
        $query = $this->_db_slave->query($sql);
        $tmp = $query->row_array();
        $total_rows = $tmp['found_rows'];
        /*
         * Output
         */
        $output = array(
            "sEcho" => isset($method['sEcho']) ? $method['sEcho'] : 0,
            "iTotalRecords" => (int)$total_rows,
            "iTotalDisplayRecords" => (int)$total_rows,
            "aaData" => $rows,
            "sWuery" => $sQuery
        );
        return $output;
    }

}
}