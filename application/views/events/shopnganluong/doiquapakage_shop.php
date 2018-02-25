<div id="exchange_top" class="content" style="text-align: center; margin-top: 10px">
                <table style="width: 288px; margin: auto; font-size: 12px;">
                    <tr>
                        <td style="text-align: center;">*Ngân Lượng: <span id="user_point_2" style="color: red; font-weight: bold;"><?php echo $user_point; ?></span> - 
                            <?php if ($controler->local_filter()) { ?>
                            <a href="javascript:void(0);" rel="nap-the" class="point-button" onclick="showcontent(this);napthe();">Nạp thẻ</a>
                            <?php } ?>
                            <a href="javascript:void(0);" rel="lich-su" class="point-button2"
            onclick="showcontent(this);lichsu();">Lịch sử</a></td>
                        <td style="text-align: center;"></td>
                        <td style="text-align: right;"></td>
                    </tr>
                </table>
            </div>
<div class="h-content">
    <div style="margin-top: 5px; padding: 5px;">

        <?php
         if(count($gift_list) > 0){
        foreach ($gift_list as $key => $value) {             
        ?>
        <table style="width: 100%; margin-top: 5px;border: 1px solid #E46C0A;">
            <tr>
                <td style="padding: 4px;" colspan="2">                    
                    <span style="font-weight: bold; color: #C20C0C; font-size: 11px;">Yêu cầu <?php echo $value["reuqets_vip"]; ?></span> - <span style="font-size: 11px; color: #B66D31; font-weight: bold;">Đổi tối đa trong ngày: <span style="color: #E40A3C;"><?php if($value["gift_buy_max"] == 0){echo "Không giới hạn";} else { echo $value["gift_buy_max"]; } ?></span></span>
                </td>
            </tr>
            <tr>
                <td>
                    <img src="<?php echo $value["gift_img"]; ?>" />
                </td>
                <td>
                    <?php if($user_vip_point < $value["gift_vip_point"]) {?>
                    <a href="javascript:void(0);" class="pet-button disable" id="gift-button-<?php echo $value["id"]; ?>">Chưa đủ</a>
                    <?php } else { ?>                                      
                    <a href="javascript:void(0);" class="exchange-button" id="gift_button" onclick="exchange_gift_pakage_by('<?php echo $value["id"]; ?>', '<?php echo $value["gift_name"]; ?>', '<?php echo $value["gift_price"]; ?>')">Đổi</a>
                    <?php } ?>
                </td>
            </tr>
             <tr>
                 <td style="padding: 4px;" colspan="2">
                    <span style="font-weight: bold; color: #C20C0C; font-size: 13px"><?php echo $value["gift_name"]; ?></span> - <span id="gift_price">Giá: <?php echo $value["gift_price"]; ?></span>
                 </td>
             </tr>
        </table>
        <?php } ?>
            <?php } else { ?>
            <span style="font-size: 11px; color: #B66D31; font-weight: bold; padding: 10px;">Vật phẩm sẽ được cập nhật sau 1 tuần, bạn vui lòng quay lại sau.</span>
            <?php } ?>
    </div>
</div>