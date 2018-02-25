<script>
    var height = screen.height - 170;

    $(function () {
        var $hlist = $('.h-list');  
        var $window = $(window).on('resize', function () {          
            $hlist.height(height);
        }).trigger('resize'); //on page load
    });
</script>
<div class="h-content">
    <div style="clear: both"></div>
    <div id="tournament" style="margin: auto; width: 288px; font-size: 13px;">       
        <?php                                 
        foreach ($tournament as $key => $value) {                                    
        ?>
         <?php  if($server_id == 0) {?>
        <div style="margin-top: 10px; margin-bottom: 10px;">
            <table style="width: 288px; border: 1px solid #F79646; padding: 4px; margin-top: 10px;" cellpadding="4" cellspacing="0">
                <tr>
                    <td>*Bắt đầu:</td>
                    <td style="text-align: right;"><span style="font-weight: bold;"><?php $date = new DateTime($value["tournament_date_start"]); echo $date->format('d-m-Y H:i:s'); ?></span></td>
                </tr>
                <tr>
                    <td>*Kết thúc: </td>
                    <td style="text-align: right;"><span style="font-weight: bold;"><?php $date = new DateTime($value["tournament_date_end"]); echo $date->format('d-m-Y H:i:s'); ?></span></td>
                </tr>
            </table>
        </div>
        <?php } ?> 
        <?php } ?>                   
    </div> 
           <div class="h-list" style="overflow-y: scroll; overflow-x: scroll; margin-top: 10px; height: 270px;">
        <table id="customers" class="table-role" style="margin-top: 0 !important;">
            <tr>
                <th>Top</th>
                <th>Nhân Vật</th>                
                <th>Level</th>
                <th>Exp</th>               
            </tr>
            <?php                                 
              foreach ($TopArena as $key => $value) {                                    
            ?>
            <tr>
                <td><?php echo $key + 1; ?></td>
                <td><?php echo $value["customName"]; ?></td>                
                <td><?php echo $value["level"]; ?></td>
                <td><?php echo $value["xp"]; ?></td>               
            </tr>
            <?php } ?>
        </table>
    </div> 
    <?php //echo var_dump($history"]) ?>
    <!--<div class="message success">Tạm thời chưa có dữ liệu. </div>-->
</div>
