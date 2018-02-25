<div id="tournament" style="margin: auto; width: 288px; font-size: 13px;">
    <?php
    if($tournament)
    foreach ($tournament as $key => $value) {
    ?>
    <div style="font-size: 18px; font-weight: bold; color: #C20C0C; margin-top: 10px;">
        <?php echo $value["tournament_name"]; ?>
    </div>
    <div style="margin-bottom: 10px;">
        <table style="width: 288px; border: 1px solid #F79646;padding: 4px;margin-top: 10px;" cellpadding="4" cellspacing="0">
            <tr>
                <td>*Bắt đầu giải đấu:</td>
                <td style="text-align: right;"><span style="font-weight: bold;"><?php $date = new DateTime($value["tournament_date_start"]); echo $date->format('d-m-Y H:i:s'); ?></span></td>               
            </tr>
            <tr>
                <td>*Kết thúc giải đấu: </td>
                <td style="text-align: right;"><span style="font-weight: bold;"><?php $date = new DateTime($value["tournament_date_end"]); echo $date->format('d-m-Y H:i:s'); ?></span></td>               
            </tr>  
            <tr>
                <td>*Mỗi lần tích lũy cần: </td>
                <td style="text-align: right;"><span style="font-weight: bold; color: red;"><?php echo $value["tournament_point"];  ?> Ngân Lượng</span></td>               
            </tr>
        </table>
    </div>
<!--    <div style="margin-top:10px; margin-bottom: 10px;">
        <table style="width: 288px; border: 1px solid #F79646;padding: 4px;margin-top: 10px;" cellpadding="4" cellspacing="0">
            <tr>
                <td>*Bắt đầu nhận quà:</td>
                <td style="text-align: right;"><span style="font-weight: bold;"><?php $date = new DateTime($value["tournament_date_start_reward"]); echo $date->format('d-m-Y H:i:s'); ?></span></td>               
            </tr>
            <tr>
                <td>*Kết thúc nhận quà: </td>
                <td style="text-align: right;"><span style="font-weight: bold;"><?php $date = new DateTime($value["tournament_date_end_reward"]); echo $date->format('d-m-Y H:i:s'); ?></span></td>               
            </tr>          
        </table>
    </div>-->
    <?php } ?>
    <div>
        *Ngân lượng của bạn: <span style="font-weight: bold; color: red;"><?php echo $user_nl; ?></span> 
    </div>

    <div>
        *Điểm Nổ Hủ: <span style="font-weight: bold; color: #1649E8;"><?php echo $point_jackpot['item_count']; ?></span>
    </div>
</div>
<div class="h-content" style="text-align: center;">
    <div class="row">
    <div class="modaldoiqua">
        <div id="gift_list" style="background-color: #FDE7CB; margin-top: 10px">
            <?php
            if($gift_list)
            foreach ($gift_list as $key => $value) {
            ?>
            <div id="gift_item" class="col-xs-3">
                <img id="gift_img" width="60px" heigh="60px" src="<?php echo $value["gift_img"]; ?>" />
            </div>
            <?php } ?>
        </div>
    </div>
    </div>
    <div style="margin-top: 15px; text-align: center; margin-bottom: 10px">
        <a href="javascript:play_now();" class="pet-button" id="play-now">Mở Quà</a>
    </div>


</div>
