<script>
    var height = screen.height - 170;

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
        <?php                                 
        foreach ($tournament as $key => $value) {                                    
        ?>
         <?php  if($server_id == 0) {?>
        <div style="margin-top: 10px; margin-bottom: 10px;">
            <table style="width: 288px; border: 1px solid #F79646; padding: 4px; margin-top: 10px;" cellpadding="4" cellspacing="0">
                <tr>
                    <td>*Bắt đầu:</td>
                    <td style="text-align: right;"><span style="font-weight: bold;"><?php $date = new DateTime($value["tournament_date_start"]); echo $date->format('d-m-Y H:i:s'); ?></span></td>
                </tr>
                <tr>
                    <td>*Kết thúc: </td>
                    <td style="text-align: right;"><span style="font-weight: bold;"><?php $date = new DateTime($value["tournament_date_end"]); echo $date->format('d-m-Y H:i:s'); ?></span></td>
                </tr>
            </table>
        </div>
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
        <?php } ?> 
         <?php if(count($userpoint) == 0) { ?>
        *Điểm của bạn: <span style="font-weight: bold; color: #1649E8;">0</span>, Hạng: <span style="font-weight: bold; color: #1649E8;">0</span>, Thắng: <span style="font-weight: bold; color: #1649E8;">0</span>
         <?php } else { ?>    
         <div style="margin-top: 10px; text-align: left;">             
            *Điểm của bạn: <span style="font-weight: bold; color: #1649E8;"><?php echo $userpoint[0]["currPoint"]; ?></span>, Hạng: <span style="font-weight: bold; color: #1649E8;"><?php echo $userpoint[0]["TOP"]; ?></span>, Thắng: <span style="font-weight: bold; color: #1649E8;"><?php echo $userpoint[0]["winCount"]; ?></span>
        </div>        
        <?php } ?>    
        <?php } ?>  
    </div> 
    <a href="javascript:void(0);" class="pet-button" id="gift-button" onclick="gift_top_exchange('<?php echo $value["id"]; ?>');">Nhận quà</a>  
           <div class="h-list" style="overflow-y: scroll; overflow-x: scroll; margin-top: 10px; height: 270px;">
        <table id="customers" class="table-role" style="margin-top: 0 !important;">
            <tr>
                <th>Top</th>
                <th>Nhân Vật</th>                  
                <th>Điểm</th>
                <th>Trận Thắng</th>
                <th>Thưởng NL</th>
            </tr>
            <?php                                 
              foreach ($TopArena as $key => $value) {                                    
            ?>
            <tr>
                <td><?php echo $key + 1; ?></td>
                <td><?php echo $value["name"]; ?></td>                             
                <td><?php echo $value["currPoint"]; ?></td>
                <td><?php echo $value["winCount"]; ?></td> 
                <td><?php $bonus = $controler->get_top_percent($key + 1, $tournament_id);
                if($bonus > 0){
                    echo  $bonus;
                } else {
                    echo 0;
                }?></td>
            </tr>
            <?php } ?>
        </table>
    </div> 
    <?php //echo var_dump($history"]) ?>
    <!--<div class="message success">Tạm thời chưa có dữ liệu. </div>-->
</div>
