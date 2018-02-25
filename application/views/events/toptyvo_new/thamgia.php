<div id="tournament" style="margin: auto; width: 288px; font-size: 13px;">
    <?php                                 
    foreach ($tournament as $key => $value) {                                    
    ?>
    <div style="font-size: 18px; font-weight: bold; color: #C20C0C; margin-top: 10px;">
        <?php echo $value["tournament_name"]; ?>
    </div>
    <div>
        <table style="width: 288px; border: 1px solid #F79646;padding: 4px;margin-top: 10px;" cellpadding="4" cellspacing="0">
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
    <div style="margin-top:10px; margin-bottom: 10px;">
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
    </div>
    <?php } ?>
    <div>
        *Điểm tích lũy hiện tại: <span style="font-weight: bold; color: #1649E8;"><?php echo $user_point; ?></span>
    </div>
    <?php if($tournament_id <= 13) { ?>
    <div>
        *Chiến thắng: <span style="font-weight: bold; color: #30C016;"><?php echo $user_attackwin; ?></span> 
        - Chiến bại: <span style="font-weight: bold; color: #FA0303;"><?php echo $user_attacklose; ?></span>         
    </div> 
    <div>
        *Thủ thắng: <span style="font-weight: bold; color: #30C016;"><?php echo $user_defendwin; ?></span>
        - Thủ bại: <span style="font-weight: bold; color: #FA0303;"><?php echo $user_defendlose; ?></span>
    </div>
    <?php } else { ?> 
    <div>
        *Chiến thắng: <span style="font-weight: bold; color: #30C016;"><?php echo $user_attackwin; ?></span> 
        - Chiến bại: <span style="font-weight: bold; color: #FA0303;"><?php echo $user_attacklose; ?></span> 
        - Thủ bại: <span style="font-weight: bold; color: #FA0303;"><?php echo $user_defendlose; ?></span>        
    </div>
   
    <?php } ?>
    <div>
        *Điểm cập nhật lúc <span style="font-weight: bold; color: #F27711;">12h</span> và <span style="font-weight: bold; color: #F27711;">21h</span> hằng ngày.
    </div>

    <div style="margin-top: 10px; font-weight: bold;">
        Nhận quà (<span style="color: red; font-size: 12px; font-weight: normal;">*Lưu ý: Bạn chỉ được nhận quà một lần duy nhất cho mỗi giải đấu</span>):
    </div>

    <div style="margin-top: 5px;">

        <?php    
        //$user_point"] = 20000;
        foreach ($reward_list as $key => $value) {  
            //echo $reward_list"][$key]["reward_point"]." - ".$reward_list"][$key+1]["reward_point"];
        ?>
        <table style="width: 100%; margin-top: 5px;">
            <tr>
                <td>
                    <span style="font-weight: bold; color: #C20C0C;"><?php echo $value["reward_name"]; ?></span><br />
                    <img src="http://mgh.mobo.vn<?php echo $value["reward_img"]; ?>" /></td>
                <td>
                    <?php if($user_point < $value["reward_point"] || $user_point == "") {?>
                    <a href="javascript:void(0);" class="pet-button disable" id="gift-button-<?php echo $value["id"]; ?>">Chưa đủ</a>
                    <?php } else { ?>                                      
                    <a href="javascript:void(0);" class="pet-button" id="gift-button-<?php echo $value["id"]; ?>" onclick="gift_exchange('<?php echo $value["id"]; ?>', '<?php echo $value["reward_name"]; ?>');">Nhận</a>
                    <?php } ?>
                </td>
            </tr>
        </table>
        <?php } ?>

    </div>
</div>
