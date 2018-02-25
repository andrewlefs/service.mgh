<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace GraphShare\Object\Values;

class DayRoles {
     /**
     * if maximum == -1 không giới hạn số lần share không tính quà
     * 
     * @var int
     */
    static $MAXIMUM = -1;
    
    /**
     *Giới hạng mỗi ngày được share tối đa
     * 
     * @var int
     */
    static $MAX = 1;
    /**
     * if limit == true thì user đặt max sẽ không được share tiếp    
     * @var type 
     */
    static $LIMIT = true;
    /**
     * if count == 0 never
     * unit minute
     * @var int
     */
    static $COUNT_DOWN = 0;
    
    const WAITING = "waiting";
    
    const LIMIT_DAY = 3;
    
    static $TITLE = "Hoàn thành nhiệm vụ ngày nhận vàng";
    static $SHORT_TITLE = "Vàng";
    static $MESSAGE = "Chúc mừng bạn nhận thành công nhiệm vụ ngày";
    static $SHORT_MESSAGE = "100 Vàng và 20 Kim Cương";    
}
