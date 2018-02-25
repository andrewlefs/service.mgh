<div class="h-content">
    <div style="clear: both"></div>
    <div class="h-list">
        <div style="font-size: 13px; font-weight: bold; margin-top: 10px;">Lịch Sử Nạp Thẻ</div>
        <table id="customers" class="table-role">
            <tr>
                <th>Mã GD</th>
                <th>Thời Gian</th>
                <th>Thẻ</th>
                <th>Giá</th>
            </tr>
            <?php                                 
            foreach ($charging_history as $key => $value) {                                    
            ?>
            <tr>
                <td><?php echo $value["tranidcard"]; ?></td>
                <td><?php $date = new DateTime($value["insertdate"]); echo $date->format('d-m-Y H:i:s'); ?></td>
                <td><?php if($value["cardtype"] == "vms"){echo "Mobifone";}
                          if($value["cardtype"] == "vina"){echo "Vinaphone";}
                          if($value["cardtype"] == "viettel"){echo "Viettel";}
                          if($value["cardtype"] == "gate"){echo "Gate";}?></td>
                <td><?php echo $value["cardvalue"]; ?></td>
            </tr>
            <?php } ?>
        </table>        
    </div>

    <?php //echo var_dump($history) ?>
    <!--<div class="message success">Tạm thời chưa có dữ liệu. </div>-->
</div>
