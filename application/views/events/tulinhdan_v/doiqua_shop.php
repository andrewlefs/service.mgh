<style>
#gift_list {width: 100%; max-width: 600px;margin: auto;}
#gift_item {width: 100px; display: inline-table;margin-top: 15px;border: 1px solid #E46C0A;padding: 5px;}
#gift_img {width: 100%}
#gift_name {font-weight: bold;font-size: 13px;color: #E40A3C;height: 30px;}
#gift_price {font-size: 13px;font-weight: bold;}    
</style>
<div id="exchange_top" class="content" style="text-align: center; margin-top: 55px">
                <table style="width: 100%; margin: auto; font-size: 12px;">
                    <tr>
                        <td style="text-align: center;">*Điểm tích lũy của bạn: <span id="user_point_2" style="color: red; font-weight: bold;"><?php echo $user_point; ?></span> 
<!--                           - <a href="javascript:void(0);" rel="nap-the" class="btn btn-success btn-xs" onclick="transfer_np();">Chuyển Điểm tích lũy của bạn</a>                            -->
</td>
                    </tr>
                </table>
            </div>
<div class="h-content">
    <div class="modaldoiqua">
        <div id="gift_list">
            <?php   
              if(count($gift_list) > 0) {
            foreach ($gift_list as $key => $value) {                                    
            ?>
            <div id="gift_item">
                <span style="font-size: 11px; color: #B66D31; font-weight: bold;">Tối đa: <span style="color: #E40A3C;"><?php if($value["gift_buy_max"] == 0){echo "Không giới hạn";} else { echo $value["gift_buy_max"]; } ?></span></span>
                <img id="gift_img" width="100px" height="100px" src="<?php echo $value["gift_img"]; ?>" />
                <div id="gift_name"><?php echo $value["gift_name"]; ?></div>
                <div id="gift_price">Giá: <?php echo $value["gift_price"]; ?></div>
                <div><input value='SL...' onblur='if (this.value == "") {this.value = "SL...";}' onfocus='if (this.value == "SL...") {this.value = "";}' id='quantity_<?php echo $value["id"]; ?>' type='text' style='width: 70px;text-align: center;color: #615B5B;border: 3px solid #f79646;height: 30px; margin-bottom: 5px;'></div>
                <a href="javascript:void(0);" class="btn btn-primary" id="gift_button" onclick="exchange_gift_by('<?php echo $value["id"]; ?>', '<?php echo $value["gift_name"]; ?>', '<?php echo $value["gift_price"]; ?>')">Đổi Quà</a>
            </div>
            <?php } } else { ?>
        <div style="text-align: center;font-weight: bold;color: #F44336;">Chưa có Item</div>
        <?php } ?>
        </div>
    </div>
</div>