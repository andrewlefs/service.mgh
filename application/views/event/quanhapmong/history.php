<div class="h-content">
    <div style="clear: both"></div>
    <div class="h-list">        
        <div style="font-size: 13px; font-weight: bold; margin-top: 10px;">Nhận Quà Game</div>
        <table id="customers" class="table-role">
            <tr>
                <th>Mã GD</th>
                <th>Thời gian</th>                
                <th>Điểm</th>
                <th>Quà</th>
            </tr>
            <?php                                 
            foreach ($gift_exchange_history as $key => $value) {                                    
            ?>
            <tr>
                <td><?php echo $value["id"]; ?></td>
                <td><?php $date = new DateTime($value["exchange_gift_date"]); echo $date->format('d-m-Y H:i:s'); ?></td>               
                <td><?php echo $value["exchange_gift_point"]; ?></td>                
                <td><?php echo $value["gift_name"]; ?></td>
            </tr>
            <?php } ?>
        </table>       
    </div>

    <?php //echo var_dump($this->data["history"]) ?>
    <!--<div class="message success">Tạm thời chưa có dữ liệu. </div>-->
</div>
