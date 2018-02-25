<div class="h-content">
    <div style="clear: both"></div>
    <div id="exchange_top" class="content" style="text-align: center; margin-top: 10px">
                <table style="width: 288px; margin: auto; font-size: 12px; margin-bottom: 10px;">
                    <tr>
                        <td style="text-align: center;">*Cống Hiến: <span id="user_point_2" style="color: red; font-weight: bold;"><?php echo $this->data['user_point']; ?></span>
                            - <a href="javascript:void(0);" rel="lich-su" class="point-button" onclick="showcontent(this);get_exchange_history2();">Lịch sử chuyển khoản</a></td>
                        <td style="text-align: center;"></td>
                        <td style="text-align: right;"></td>
                    </tr>
                </table>
            </div>
    <div class="h-list" style="border: 3px solid #F79646;padding-bottom: 10px;">        
        <div style="font-size: 17px; font-weight: bold; margin-top: 10px; text-align: center;">Chuyển Khoản Cống Hiến</div>
        <div style="margin-top: 10px; margin-bottom: 10px; font-weight: bold; text-align: center;"><span style="color: red; font-size: 12px; font-weight: normal;">*Phí chuyển khoản: 5%</span></div>
        <div class="m-content nap-the">            
            <table style="margin: 0 auto">                
                <tr>
                    <td width="65px" style="white-space: nowrap;" class="text">Số lượng chuyển:</td>
                    <td class="input">
                        <input id="nganphieu_trans" type="text" /></td>
                </tr> 
                <tr>
                    <td width="65px" style="white-space: nowrap;" class="text">Mobo ID nhận:</td>
                    <td class="input">
                        <input id="mobo_id_trans" type="text" /></td>
                </tr>
                <tr>                    
                    <td class="input" colspan="2"><a id="agree_pay" class="pet-button" type="button" value="Đồng ý" onclick="transfer_np_process()">Đồng Ý</a></td>
                </tr>
            </table>
            <div id="card_history"></div>
        </div>
    </div>      
</div>
