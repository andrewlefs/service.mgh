<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class NhatBoThangThien_Model extends CI_Model {

    private $db_slave;
    private $db_master;
    private $current_date;

    public function __construct() {
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $this->current_date = date('Y-m-d H:i:s', time());
        if (!$this->db_slave)
            $this->db_slave = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);
        if (!$this->db_master)
            $this->db_master = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), true);
    }

    public function exec($params) {

        $msg_default = "<br>Hiện tại hiện thống đang quá tải vui lòng thử lại sau.";
        $msg = "";
        $char_id = $params["char_id"];
        $server_id = $params['server_id'];

        $times = $this->check_times($char_id);
        if ($times == -1) {
            $msg = $msg_default;
        } else if ($times >= 10) {
            $msg = "<br>Bạn đã hết lượt tham gia sự kiện này";
        } else {
            $code = $this->get_code($server_id, $times + 1);
            if (empty($code)) {
                $msg = $msg_default;
            } else {
                $flags = $this->update($params, $code, $times + 1, $server_id);
                if ($flags == true) {
                    $msg = "<br>Bạn đã tham gia sự kiện 'Nhất Bộ Thăng Thiên' lần thứ " . ($times + 1) . " bạn còn lại " . (10 - ($times + 1)) . " lần tham gia. code: " . $code;
                } else {
                    $msg = $msg_default;
                }
            }
        }
        $params["result_msg"] = $msg;

        MeAPI_Log::writeCsv(array($this->last_link_request, json_encode($params)), 'nhat_bo_thang_thien' . date('H'));
        return $msg;
    }

    public function get_history($params) {
        $query = $this->db_slave->select("char_name,`level`, delivery_date, code, times")
                ->where("status", 1)
                ->where("char_id", $params['char_id'])
                ->get("event_nhat_bo_thang_thien");
        return $query->result_array();
    }

    public function update($params, $code, $times, $server_id) {
        $channel = $params['channel'];
        $split = explode("|", $channel);
        $provider_id = $split[0];

        $query = $this->db_master->set("status", 1)
                ->set("uid", $params['uid'])
                ->set("order_id", $params['order_id'])
                ->set("char_id", $params['char_id'])
                ->set("level", $params['level'])
                ->set("money", $params['money'])
                ->set("data", $params['data'])
                ->set("provider", $provider_id)
                ->set("delivery_date", $this->current_date)
                ->where("code", $code)
                ->where("times", $times)
                ->update("event_nhat_bo_thang_thien");

        if ($this->db_master->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function get_code($server_id, $times) {
        $total = $this->check_total_code($server_id, $times);
        if (empty($total))
            return "";
        $start = time() % $total;

        $query = $this->db_master->select("code")
                ->where("status", 0)
                ->where("times", $times)
                ->where("server_id", $server_id)
                ->limit(1, $start)
                ->get("event_nhat_bo_thang_thien");
        //$aaa = $this->db_master->last_query();
        if ($query == FALSE) {
            return "";
        } else {
            $result = $query->result();
            if (count($result) > 0)
                return $result[0]->code;
            else
                return "";
        }
    }

    public function check_times($char_id) {
        $query = $this->db_master->select("COUNT(ID) AS MyCount")
                ->where("status", 1)
                ->where("char_id", $char_id)
                ->get("event_nhat_bo_thang_thien");
        if ($query == FALSE) {
            return -1;
        } else {
            $result = $query->result();
            return $result[0]->MyCount;
        }
    }

    public function check_total_code($server_id, $times) {
        $query = $this->db_master->select("COUNT(ID) AS MyCount")
                ->where("status", 0)
                ->where("times", $times)
                ->where("server_id", $server_id)
                ->get("event_nhat_bo_thang_thien");
        if ($query == FALSE) {
            return -1;
        } else {
            $result = $query->result();
            if (count($result) > 0)
                return $result[0]->MyCount;
            else
                return 0;
        }
    }

    public function report($params) {
        $function = strtolower($params["func"]);
        $start = strtolower($params["start"]);
        $end = strtolower($params["end"]);
        switch ($function) {
            case "total":
                $query = $this->db_slave->select("COUNT(*) as `total`, SUM(`money`) as `money`", FALSE)
                        ->where("status", 1)
                        ->where("delivery_date >=", $start)
                        ->where("delivery_date <=", $end)
                        ->get("event_nhat_bo_thang_thien");
                break;
            case "char_id":
                $query = $this->db_slave->select("char_id, COUNT(*) as `total`, SUM(`money`) as `money`", FALSE)
                        ->where("status", 1)
                        ->where("delivery_date >=", $start)
                        ->where("delivery_date <=", $end)
                        ->group_by("char_id")
                        ->get("event_nhat_bo_thang_thien");
                break;
            case "server_id":
                $query = $this->db_slave->select("server_id, server_name, COUNT(*) as `total`, SUM(`money`) as `money`", FALSE)
                        ->where("status", 1)
                        ->where("delivery_date >=", $start)
                        ->where("delivery_date <=", $end)
                        ->group_by("server_id")
                        ->get("event_nhat_bo_thang_thien");
                break;
            case "date":
                $query = $this->db_slave->select("DATE_FORMAT(delivery_date,'%Y-%m-%d') as `date` , COUNT(*) as `total`, SUM(`money`) as `money`", FALSE)
                        ->where("status", 1)
                        ->where("delivery_date >=", $start)
                        ->where("delivery_date <=", $end)
                        ->group_by(array("DATE_FORMAT(delivery_date,'%Y-%m-%d')"))
                        ->get("event_nhat_bo_thang_thien");
                break;
            case "level":
                $query = $this->db_slave->select("`level`, COUNT(*) as `total`, SUM(`money`) as `money`", FALSE)
                        ->where("status", 1)
                        ->where("delivery_date >=", $start)
                        ->where("delivery_date <=", $end)
                        ->group_by("level")
                        ->get("event_nhat_bo_thang_thien");
                break;
            case "times_max":
                $query = $this->db_slave->select("COUNT(*) as `total`, SUM(`money`) as `money`", FALSE)
                        ->where("status", 1)
                        ->where("times", 10)
                        ->where("delivery_date >=", $start)
                        ->where("delivery_date <=", $end)
                        ->get("event_nhat_bo_thang_thien");
                break;
            case "times":
                $query = $this->db_slave->select("times, COUNT(*) as `total`, SUM(`money`) as `money`", FALSE)
                        ->where("status", 1)
                        ->where("delivery_date >=", $start)
                        ->where("delivery_date <=", $end)
                        ->group_by("times")
                        ->get("event_nhat_bo_thang_thien");
                break;                      
            default:
                $query = $this->db_slave->select("char_id,`level`, delivery_date, code, times, money")
                        ->where("status", 1)
                        ->where("delivery_date >=", $start)
                        ->where("delivery_date <=", $end)
                        ->get("event_nhat_bo_thang_thien");
                break;
        }
        return $query->result_array();
    }

}
