<div id="exchange_top" class="content" style="text-align: center; margin-top: 10px">
                <table style="width: 288px; margin: auto; font-size: 12px;">
                    <tr>
                        <td style="text-align: center;">*Điểm Giang Hồ: <span id="user_point_2" style="color: red; font-weight: bold;"><?php echo $user_point; ?></span> 
                            - <a href="javascript:void(0);" rel="nap-the" class="point-button" onclick="showcontent(this);transfer_np();">Chuyển Điểm</a>               
                        </td>
                        <td style="text-align: center;"></td>
                        <td style="text-align: right;"></td>
                    </tr>
                </table>
            </div>
<div class="h-content">
    <div style="margin-top: 5px; padding: 5px;">

        <?php
        foreach ($gift_list as $key => $value) {             
        ?>
        <script>
            $(function () {
                $("#day_count_<?php echo $value["id"]; ?>").change(function () {
                     $("#gift_price_<?php echo $value["id"]; ?>").html($(this).val() * <?php echo $value["gift_price"]; ?>);
                });
            });    
        </script>
        <table style="width: 100%; margin-top: 5px;border: 1px solid #E46C0A;">
            <tr>
                <td style="padding: 4px;" colspan="2">
                    <span style="font-weight: bold; color: #C20C0C; font-size: 13px"><?php echo $value["gift_name"]; ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <img style="height: 52px;" src="<?php echo $value["gift_img"]; ?>" />
                </td>
                <td style="width: 70px;">
                <a href="javascript:void(0);" class="exchange-button" id="gift_button" onclick="exchange_gift_pakage_special_by('<?php echo $value["id"]; ?>', '<?php echo $value["gift_name"]; ?>', '<?php echo $value["gift_price"]; ?>')">Đổi</a>    
                </td>               
            </tr>
             <tr>
                 <td style="padding: 4px;" colspan="2">
                     <span style="font-size: 12px; font-weight: bold">Giá: <span style="color: red" id="gift_price_<?php echo $value["id"]; ?>"><?php echo $value["gift_price"] * 3; ?></span></span>
                    <select id="day_count_<?php echo $value["id"]; ?>" name="day_count_<?php echo $value["id"]; ?>">
                        <option value="3">3 Ngày</option>
                        <option value="7">7 Ngày</option>
                        <option value="15">15 Ngày</option>
                        <option value="30">30 Ngày</option>
                    </select>                    
                 </td>
             </tr>
        </table>
        <?php } ?>
    </div>
</div>