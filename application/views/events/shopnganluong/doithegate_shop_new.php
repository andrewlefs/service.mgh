<head>
</head>
<div class="h-content" style="text-align: justify; width: 288px; margin: auto; margin-top: 10px;">
    <div style="clear: both"></div>
<div id="exchange_top" class="content" style="text-align: center; margin-top: 10px">
                <table style="width: 288px; margin: auto; font-size: 12px; margin-bottom: 10px;">
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
<div class="h-content" style="background: url('http://service.mgh.mobo.vn/assets/events/dautruong/images/gate_bg2.jpg');">
    <div class="modaldoiqua">
        <div id="gift_list">
           <div class="h-list" style="border: 3px solid #F79646;">        
        <div style="font-size: 20px; font-weight: bold; margin-top: 10px; text-align: center;">Đổi Thẻ Test New</div>        
        <div class="m-content nap-the">            
            <table style="margin: 0 auto; width: 85%;">
                <tr>
                    <td width="65px" class="text" style="white-space: nowrap;">Chọn loại thẻ :</td>
                    <td class="input">
                        <select id="cardtype_ex" class="input" style="width: 100%;">
                            <option class="sc" value="">--Chọn--</option>                            
                            <option class="sc" value="gate">Thẻ Gate</option>
                            <option class="sc" value="vms">Thẻ Mobifone</option>
                            <option class="sc" value="vina">Thẻ Vinaphone</option>
                            <option class="sc" value="viettel">Thẻ Viettel</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td width="65px" style="white-space: nowrap;" class="text">Mệnh giá :</td>
                    <td class="input"><select id="cardvalue" class="input" style="width: 100%;">
                            <option class="sc" value="">--Chọn--</option>
                            <option class="sc" value="10000">10.000</option>
                            <option class="sc" value="20000">20.000</option>
                            <option class="sc" value="50000">50.000</option>
                            <option class="sc" value="100000">100.000</option>
                            <option class="sc" value="200000">200.000</option>
                            <option class="sc" value="500000">500.000</option>
                        </select></td>
                </tr>              
                <tr>                   
                    <td class="input" colspan="2" style="text-align: center"><a id="exchange_card" class="pet-button" type="button" value="Đồng ý" onclick="exchange_card_new()">Đổi Thẻ</a></td>
                </tr>
            </table>
            <div style="margin-top: 10px;font-weight: bold; text-align: center;">
               <table id="customers" class="table-role">
                    <tr>
                        <th>Mệnh Giá Thẻ</th>
                        <th>Ngân Lượng</th>                       
                    </tr>
                    <tr>
                        <td>500.000</td>
                        <td>5.750</td>                      
                    </tr> 
                   <tr>
                        <td>200.000</td>
                        <td>2.300</td>                      
                    </tr>
                   <tr>
                        <td>100.000</td>
                        <td>1.150</td>                      
                    </tr>
                   <tr>
                        <td>50.000</td>
                        <td>575</td>                      
                    </tr>   
                   <tr>
                        <td>20.000</td>
                        <td>230</td>                      
                    </tr>
                   <tr>
                        <td>10.000</td>
                        <td>115</td>                      
                    </tr>                
                </table>
            </div>
            <div id="card_history">                
            </div>
        </div>
    </div>
        </div>
    </div>
</div>
    </div>