<div class="h-content">
    <div style="clear: both"></div>
    <div class="h-list">        
        <div style="font-size: 13px; font-weight: bold; margin-top: 10px;">Nhận Quà Game</div>
        <div class="" style="text-align: center;">
            <?php 
            $date_now = date('Y-m-d H:i:s');
            if(count($calendar_bonus) > 0) { ?>            
            <table id="customers" class="table-role">
                <tr> 
                    <th>Quà</th>
                    <th style="width: 60px;"></th>                    
                </tr>
                <?php
                foreach ($calendar_bonus as $key => $value) {
                    ?>
                    <tr>
                        <td><div style="margin-bottom: -9px;">Ngày được nhận: <span style="font-weight: bold;">
                            <?php 
                            $bonus_date_end = date('Y-m-d 23:59:59', strtotime($value["bonus_date"]));
                            $date = new DateTime($value["bonus_date"]);
                echo $date->format('d-m-Y');
                    ?></span></div>
                            <br />
                            <img style="height: 52px;" src="<?php echo $value["gift_img"]; ?>" /></td>                       
                        <td>
                            <?php if(strtotime($date_now) > strtotime($bonus_date_end)) {?>
                            <a style="width: 60px;" href="javascript:void(0);" class="top-button disable" id="receive_button">Bỏ Qua</a>
                            <?php } else  if($value["status_received"] == "1" ) {?>  
                             <a style="width: 60px;" href="javascript:void(0);" class="top-button disable" id="receive_button">Đã Nhận</a>
                            <?php } else { ?>
                            <?php                           
                             $bonus_date = date('Y-m-d H:i:s', strtotime($value["bonus_date"]));                                     
                            if(strtotime($date_now) < strtotime($bonus_date)) { ?> 
                             <a style="width: 60px;" href="javascript:void(0);" class="top-button nonopen" id="receive_button">Chưa Đến</a>
                             <?php } else { ?> 
                             <a style="width: 60px;" href="javascript:void(0);" class="top-button" id="receive_button" onclick="gift_receive_process(<?php echo $value["id"] ?>)">Nhận Quà</a>
                             <?php } } ?> 
                        </td>
                    </tr>
                    <?php } ?>                
            </table> 
            <?php } else { ?> 
            <div class="notification" style="margin-bottom: 10px; font-weight: bold">Bạn không đủ điều kiện.</div>
            <?php } ?> 
        </div>      
    </div>

    <?php //echo var_dump($this->data["history"]) ?>
    <!--<div class="message success">Tạm thời chưa có dữ liệu. </div>-->
</div>
