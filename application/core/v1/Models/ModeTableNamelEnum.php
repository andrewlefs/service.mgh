<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MigEvents\Models;

use MigEvents\Enum\AbstractEnum;

class ModeTableNamelEnum extends AbstractEnum {
    
    const LEVEL_RACING_OPEN_BETA = "event_level_racing_open_beta";
    const LEVEL_RACING_OPEN_BETA_LOGS = "event_level_racing_open_beta_logs";
    const REIMBURSEMENT = "event_reimbursement";
    const REIMBURSEMENT_LOGS = "event_reimbursement_logs";
	
	//LANDING PAGE
    const LANDING_GIFTCODE = "event_landing_giftcode";
    const LANDING_GIFTCODE_HISTORY = "event_giftcode_history";

}
