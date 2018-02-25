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
    <div class="modaldoiqua">
        <div id="gift_list">
            <?php
            if(count($gift_list) > 0){
            foreach ($gift_list as $key => $value) {                                    
            ?>
            <div id="gift_item">
                <span style="font-size: 11px; color: #B66D31; font-weight: bold;">Được đổi tối đa: <span style="color: #E40A3C;"><?php if($value["gift_buy_max"] == 0){echo "Không giới hạn";} else { echo $value["gift_buy_max"]; } ?></span></span>
                <img id="gift_img" width="100px" height="100px" src="<?php echo $value["gift_img"]; ?>" />
                <div id="gift_name"><?php echo $value["gift_name"]; ?></div>
                <div id="gift_price">Giá: <?php echo $value["gift_price"]; ?></div>
                <div><input value='Số lượng...' onblur='if (this.value == "") {this.value = "Số lượng...";}' onfocus='if (this.value == "Số lượng...") {this.value = "";}' id='quantity_<?php echo $value["id"]; ?>' type='text' style='width: 70px;text-align: center;color: #615B5B;border: 3px solid #f79646;height: 30px;'></div>
                <a href="javascript:void(0);" class="exchange-button" id="gift_button" onclick="exchange_gift_by('<?php echo $value["id"]; ?>', '<?php echo $value["gift_name"]; ?>', '<?php echo $value["gift_price"]; ?>')">Đổi</a>
            </div>
            <?php } ?>
            <?php } else { ?>
            <span style="font-size: 11px; color: #B66D31; font-weight: bold; padding: 10px;">Vật phẩm sẽ được cập nhật sau 1 tuần, bạn vui lòng quay lại sau.</span>
            <?php } ?>
        </div>
    </div>
</div>