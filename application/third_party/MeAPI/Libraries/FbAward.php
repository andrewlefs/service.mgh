<?php

class FbAward {

    /**

     *

     * @var CI_Controller

     */
    private $CI;

    public function __construct() {

        $this->CI = & get_instance();
    }

    public function like($server_id, $character_id, $info = NULL) {

        $this->CI->load->MeAPI_Helper('transaction');

        $transaction_id = make_transaction('fblike');

        return array(
            'status' => TRUE,
            'data' => array(
                'transaction_id' => $transaction_id,
            )
        );

        // array return fail

        return array(
            'status' => 0,
            'data' => NULL
        );
    }

    public function share($server_id, $character_id, $info = NULL) {

        $this->CI->load->MeAPI_Helper('transaction');

        $transaction_id = make_transaction('fbshare');

        return array(
            'status' => TRUE,
            'data' => array(
                'transaction_id' => $transaction_id,
            )
        );

        // array return fail

        return array(
            'status' => 0,
            'data' => NULL
        );
    }
}