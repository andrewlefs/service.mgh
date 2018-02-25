<script>
    var height = screen.height - 220;

    $(function () {
        var $hlist = $('.h-list');  
        var $window = $(window).on('resize', function () {          
            $hlist.height(height);
        }).trigger('resize'); //on page load
    });
</script>
<div class="h-content">
    <div style="clear: both"></div>
    <div id="tournament" style="margin: auto; width: 288px; font-size: 13px;">       
        <div style="margin-top: 10px; text-align: left;">
            <table style="width: 100%;">
                <tr>
                    <td style="text-align: left; width: 100px;">*Điểm của bạn:</td>
                    <td style="text-align: left"><span style="font-weight: bold; color: #1649E8;"><?php echo $user_point; ?></span></td>                  
                </tr>  
                <?php                                 
                foreach ($data_reward_premiership as $key => $value) {                                    
        ?>
                <!--<tr>
                    <td style="text-align: left; padding-top: 10px; width: 100px;">*Phần thưởng:</td>
                    <td style="text-align: left; padding-top: 10px;"><img src="http://mgh.mobo.vn<?php echo $value["reward_img"]; ?>" /></td>                  
                </tr>-->
                <?php } ?>                
            </table>             
        </div>
        <?php //if($mobo_id"] != "671456185" && $mobo_id"] != "485372761" && $mobo_id"] != "247165485" && $mobo_id"] != "853017650" && $mobo_id"] != "477409422" 
        //&& $mobo_id"] != "857316426" && $mobo_id"] != "666629660" && $mobo_id"] != "886899541"  && $mobo_id"] != "128147013"){} else {?>
        <?php                                 
        foreach ($tournament as $key => $value) {                                    
        ?>       
        <div style="margin-top: 10px; margin-bottom: 10px;">
            <table style="width: 288px; border: 1px solid #F79646; padding: 4px; margin-top: 10px;" cellpadding="4" cellspacing="0">
                <tr>
                    <td>*Bắt đầu nhận quà:</td>
                    <td style="text-align: right;"><span style="font-weight: bold;"><?php $date = new DateTime($value["tournament_date_start_reward"]); echo $date->format('d-m-Y H:i:s'); ?></span></td>
                </tr>
                <tr>
                    <td>*Kết thúc nhận quà: </td>
                    <td style="text-align: right;"><span style="font-weight: bold;"><?php $date = new DateTime($value["tournament_date_end_reward"]); echo $date->format('d-m-Y H:i:s'); ?></span></td>
                </tr>
            </table>
        </div>
        <a href="javascript:void(0);" class="pet-button" id="gift-button" onclick="gift_top_exchange_premiership('<?php echo $value["id"]; ?>');">Nhận quà</a> 
        <?php } ?>  
        <?php //} ?>                        
    </div>  
    <div class="h-list" style="overflow-y: scroll; overflow-x: scroll; margin-top: 10px; height: 270px;">
        <table id="customers" class="table-role" style="margin-top: 0 !important;">
            <tr>
                <th>Top</th>
                <th>Nhân Vật</th>
                <th>Server</th>
                <th>Level</th>
                <th>Exp</th>
                <th>Điểm</th>
            </tr>
            <?php                                 
            foreach ($data_top_premiership as $key => $value) {                                    
            ?>
            <tr>
                <td><?php echo ($key+1); ?></td>
                <td><?php echo $value["UNAME"]; ?></td>     
                <td><?php echo $value["SID"]; ?></td>           
                <td><?php echo $value["ULEVEL"]; ?></td>
                <td><?php echo $value["UEXP"]; ?></td>
                <td><?php echo $value["CURRPOINT"]; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <?php //echo var_dump($history"]) ?>
    <!--<div class="message success">Tạm thời chưa có dữ liệu. </div>-->
</div>
