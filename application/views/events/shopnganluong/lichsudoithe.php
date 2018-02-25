<div class="h-content">
    <div style="clear: both"></div>
    <div class="h-list" style="overflow: scroll;">
        <div style="font-size: 13px; font-weight: bold; margin-top: 10px;">Lịch Sử Đổi Thẻ</div>
        <table id="customers" class="table-role">
            <tr>
                <th>Mã GD</th>                
                <th>Thẻ</th>
                <th>Mệnh Giá</th>
                <th>Mã Thẻ</th>
                <th>Serial</th>
                <th>Thời Gian</th>
            </tr>
            <?php                                 
            foreach ($card_exchange_history as $key => $value) {                                    
            ?>
            <tr>
                <td><?php echo $value["id"]; ?></td>               
                <td><?php if($value["card_type"] == "vms"){echo "Mobifone";}
                          if($value["card_type"] == "vina"){echo "Vinaphone";}
                          if($value["card_type"] == "viettel"){echo "Viettel";}
                          if($value["card_type"] == "gate"){echo "Gate";}?></td>
                <td><?php echo $value["card_value"]; ?></td>
                <td><?php echo $value["card_code"]; ?></td>
                <td><?php echo $value["card_serial"]; ?></td>
                <td><?php $date = new DateTime($value["exchange_card_date"]); echo $date->format('d-m-Y H:i:s'); ?></td>
            </tr>
            <?php } ?>
        </table>        
    </div>

    <?php //echo var_dump($history) ?>
    <!--<div class="message success">Tạm thời chưa có dữ liệu. </div>-->
</div>
