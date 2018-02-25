<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

$db['system_info'] = array(
    'cfg' => array('master' => 1, 'master_random' => false, 'slave_random' => false),
    'db' => array(
        gen_cfg_db('localhost', 'root', '', 'service.mgh2.mobo.vn'),
        gen_cfg_db('localhost', 'root', '', 'service.mgh2.mobo.vn'),
    )
);
//gen_cfg_db('10.10.20.134', 'service_mgh2', 'yxrYzsCEDdHK', 'service_mgh2_mobo_vn'),
$db['db_cache'] = array(
    'cfg' => array('master' => 1, 'master_random' => false, 'slave_random' => false),
    'db' => array(
        gen_cfg_db('10.10.32.2', 'eventacc', 'dfNcsODJVoEbKXX', 'fa_cache'),
        gen_cfg_db('10.10.32.2', 'eventacc', 'dfNcsODJVoEbKXX', 'fa_cache'),
    )
);

$db['db_cache_qhv'] = array(
    'cfg' => array('master' => 1, 'master_random' => false, 'slave_random' => false),
    'db' => array(
        gen_cfg_db('10.10.32.2', 'eventacc', 'dfNcsODJVoEbKXX', 'inside_giangma'),
        gen_cfg_db('10.10.32.2', 'eventacc', 'dfNcsODJVoEbKXX', 'inside_giangma'),
    )
);

$db['db_nap'] = array(
    'cfg' => array('master' => 1, 'master_random' => false, 'slave_random' => false),
    'db' => array(
        gen_cfg_db('10.10.32.2', 'eventacc', 'dfNcsODJVoEbKXX', 'gapi_mobo_vn'),
        gen_cfg_db('10.10.32.2', 'eventacc', 'dfNcsODJVoEbKXX', 'gapi_mobo_vn'),
    )
);

$db['db_cache_mgh2'] = array(
    'cfg' => array('master' => 1, 'master_random' => false, 'slave_random' => false),
    'db' => array(
        gen_cfg_db('10.10.32.2', 'eventacc', 'dfNcsODJVoEbKXX', 'mgh2_cache'),
        gen_cfg_db('10.10.32.2', 'eventacc', 'dfNcsODJVoEbKXX', 'mgh2_cache'),
    )
);
/* End of file database.php */
/* Location: ./application/config/database.php */
