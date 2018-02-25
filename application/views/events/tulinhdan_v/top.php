<div class="h-content">
    <div style="clear: both"></div>
    <div id="tournament" style="margin: auto; width: 288px; font-size: 13px;">
        <div style="margin-top: 10px; text-align: left;">
            *Điểm tích lũy của bạn: <span style="font-weight: bold; color: #1649E8;"><?php echo $user_point; ?></span>, Hạng: <span style="font-weight: bold; color: #1649E8;"><?php echo $user_rank; ?></span>
        </div>
        <?php
        if($tournament)
        foreach ($tournament as $key => $value) {                                    
        ?>       
        <div style="margin-top: 10px; margin-bottom: 10px;">
            <table style="width: 288px; border: 1px solid #F79646; padding: 4px; margin-top: 10px;" cellpadding="4" cellspacing="0">
                <tr>
                    <td>*Bắt đầu giải đấu:</td>
                    <td style="text-align: right;"><span style="font-weight: bold;"><?php $date = new DateTime($value["tournament_date_start"]); echo $date->format('d-m-Y H:i:s'); ?></span></td>
                </tr>
                <tr>
                    <td>*Kết thúc giải đấu: </td>
                    <td style="text-align: right;"><span style="font-weight: bold;"><?php $date = new DateTime($value["tournament_date_end"]); echo $date->format('d-m-Y H:i:s'); ?></span></td>
                </tr>
            </table>
        </div>      
        <?php } ?>    
        <a href="javascript:void(0);" class="pet-button" id="gift-button" onclick="gift_top_exchange('<?php echo $value["id"]; ?>');">Nhận quà</a>     
    <div class="h-list" style="overflow-y: scroll; overflow-x: scroll; margin-top: 10px; height: 270px;">
        <table id="customers" class="table-role" style="margin-top: 0 !important;">
            <tr>
                <th>Top</th>
                <th>Nhân Vật</th>
                <th>Server</th>
                <th>Điểm</th>
            </tr>
            <?php
            if($tournament_top)
            foreach ($tournament_top as $key => $value) {                                    
            ?>
            <tr>
                <td><?php echo $value["rank"]; ?></td>
                <td><?php echo $value["char_name"]; ?></td>     
                <td><?php echo $value["server_id"]; ?></td>           
                <td><?php echo $value["u_money"]; ?></td>             
            </tr>
            <?php } ?>
        </table>
    </div>
    </div>    
</div>
