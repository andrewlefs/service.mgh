<div class="h-content">
    <div style="clear: both"></div>
    <div id="exchange_top" class="content" style="text-align: center; margin-top: 10px">
                <table style="width: 288px; margin: auto; font-size: 12px; margin-bottom: 10px;">
                    <tr>
                        <td style="text-align: center;">*Ngân Lượng: <span id="user_point_2" style="color: red; font-weight: bold;"><?php echo $user_point; ?></span> - <a href="javascript:void(0);" rel="nap-the" class="point-button"
            onclick="showcontent(this);napthe();">Nạp thẻ</a> <a href="javascript:void(0);" rel="lich-su" class="point-button2"
            onclick="showcontent(this);lichsu();">Lịch sử</a></td>
                        <td style="text-align: center;"></td>
                        <td style="text-align: right;"></td>
                    </tr>
                </table>
            </div>
    <div class="h-list" style="border: 3px solid #F79646;padding-bottom: 10px;">        
        <div style="font-size: 20px; font-weight: bold; margin-top: 10px; text-align: center;">Nạp Thẻ Đổi Ngân Lượng Test New</div>
        <div style="margin-top: 10px; margin-bottom: 10px; font-weight: bold; text-align: center;"><span style="color: red; font-size: 12px; font-weight: normal;">*Tỷ lệ nạp: 100 VNĐ = 1 Ngân Lượng</span></div>
        <div class="m-content nap-the">            
            <table style="margin: 0 auto">
                <tr>
                    <td width="65px" class="text" style="white-space: nowrap;">Chọn loại thẻ :</td>
                    <td class="input">
                        <select id="cardtype" class="input" style="width: 100%;">
                            <option class="sc" value="">--Chọn--</option>
                            <option class="sc" value="vms">Thẻ Mobifone</option>
                            <option class="sc" value="vina">Thẻ Vinaphone</option>
                            <option class="sc" value="viettel">Thẻ Viettel</option>
                            <option class="sc" value="gate">Thẻ Gate</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td width="65px" style="white-space: nowrap;" class="text">Số seri :</td>
                    <td class="input">
                        <input id="card_seri" type="text" /></td>
                </tr>
                <tr>
                    <td class="text" style="white-space: nowrap;">Mã thẻ :</td>
                    <td class="input">
                        <input id="card_code" type="text" /></td>
                </tr>
                <tr>
                    <td class="text" style="white-space: nowrap;"></td>
                    <td class="input"><a id="agree_pay" class="pet-button" type="button" value="Đồng ý" onclick="charging_new()">Nạp Thẻ</a></td>
                </tr>
            </table>
            <div id="card_history"></div>
        </div>
    </div>
    <div style="margin-top: 5px; text-align: center; margin-bottom: 10px">
            <a rel="qua-game" href="javascript:void(0);" class="big-button" onclick="showcontent(this);exchange_gift();">Dùng Ngân Lượng đổi quà</a>
        </div>
    <?php //echo var_dump($this->data["history"]) ?>
    <!--<div class="message success">Tạm thời chưa có dữ liệu. </div>-->
</div>
