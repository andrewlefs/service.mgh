<div class="h-content">
    <div style="clear: both"></div>
    <div class="h-list">
        <div style="font-size: 13px; font-weight: bold; margin-top: 10px;">Nhận Quà Tích Lũy Điểm</div>
        <table id="customers" class="table-role">
            <tr>
                <th>Mã GD</th>
                <th>Ngày</th>
                <th>Gói Quà</th>
                <th>Điểm</th>
            </tr>
            <?php                                 
            foreach ($history as $key => $value) {                                    
            ?>
            <tr>
                <td><?php echo $value["id"]; ?></td>
                <td><?php $date = new DateTime($value["exchange_date"]); echo $date->format('d-m-Y H:i:s'); ?></td>
                <td><?php echo $value["reward_name"]; ?></td>
                <td><?php echo $value["reward_point"]; ?></td>
            </tr>
            <?php } ?>
        </table>
        <div style="font-size: 13px; font-weight: bold; margin-top: 10px;">Nhận Quà Top Server</div>
        <table id="customers" class="table-role">
            <tr>
                <th>Mã GD</th>
                <th>Ngày</th>
                <th>Gói Quà</th>
                </tr>
            <?php                                 
            foreach ($history_top as $key => $value) {                                    
            ?>
            <tr>
                <td><?php echo $value["id"]; ?></td>
                <td><?php $date = new DateTime($value["exchange_date"]); echo $date->format('d-m-Y H:i:s'); ?></td>
                <td><?php echo $value["reward_name"]; ?></td>
                </tr>
            <?php } ?>
        </table>
        <div style="font-size: 13px; font-weight: bold; margin-top: 10px;">Nhận Quà Top Ngoại Hạng</div>
        <table id="customers" class="table-role">
            <tr>
                <th>Mã GD</th>
                <th>Ngày</th>
                <th>Gói Quà</th>                
            </tr>
            <?php                                 
            foreach ($history_top_premiership as $key => $value) {                                    
            ?>
            <tr>
                <td><?php echo $value["id"]; ?></td>
                <td><?php $date = new DateTime($value["exchange_date"]); echo $date->format('d-m-Y H:i:s'); ?></td>
                <td><?php echo $value["reward_name"]; ?></td>                
            </tr>
            <?php } ?>
        </table>
    </div>

    <?php //echo var_dump($history"]) ?>
    <!--<div class="message success">Tạm thời chưa có dữ liệu. </div>-->
</div>
