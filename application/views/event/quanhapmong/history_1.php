<div class="h-content">
    <div style="clear: both"></div>
    <div class="h-list">
       <div style="font-size: 13px; font-weight: bold; margin-top: 10px;">Lịch Sử Chuyển Cống Hiến</div>
        <table id="customers" class="table-role">
            <tr>
                <th>Mã GD</th>
                <th>Thời gian</th>                
                <th>Cống Hiến</th>
                <th>Phí</th>
                <th>Mobo ID nhận</th>
            </tr>
            <?php                                 
            foreach ($this->data["point_transfer_history"] as $key => $value) {                                    
            ?>
            <tr>
                <td><?php echo $value["id"]; ?></td>
                <td><?php $date = new DateTime($value["exchange_date"]); echo $date->format('d-m-Y H:i:s'); ?></td>               
                <td><?php echo $value["value"]; ?></td>                
                <td><?php echo $value["tax_value"]; ?></td>
                <td><?php echo $value["to_mobo_id"]; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <?php //echo var_dump($this->data["history"]) ?>
    <!--<div class="message success">Tạm thời chưa có dữ liệu. </div>-->
</div>
