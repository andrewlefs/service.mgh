<script>
    var height = screen.height - 175;

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
        <?php //if($mobo_id"] != "671456185" && $mobo_id"] != "485372761" && $mobo_id"] != "247165485" && $mobo_id"] != "853017650" && $mobo_id"] != "477409422" 
        //&& $mobo_id"] != "857316426" && $mobo_id"] != "666629660" && $mobo_id"] != "886899541"  && $mobo_id"] != "128147013"){} else {?>
        <?php                                 
        foreach ($tournament as $key => $value) {                                    
        ?>       
        <div style="margin-top: 10px; margin-bottom: 10px;">
            <table style="width: 288px; border: 1px solid #F79646; padding: 4px; margin-top: 10px;" cellpadding="4" cellspacing="0">
                <tr>
                    <td>*Bắt đầu nhận quà:</td>
                    <td style="text-align: right;"><span style="font-weight: bold;"><?php $date = new DateTime($value["tournament_date_start_reward"]); echo $date->format('d-m-Y H:i:s'); ?></span></td>
                </tr>
                <tr>
                    <td>*Kết thúc nhận quà: </td>
                    <td style="text-align: right;"><span style="font-weight: bold;"><?php $date = new DateTime($value["tournament_date_end_reward"]); echo $date->format('d-m-Y H:i:s'); ?></span></td>
                </tr>
            </table>
        </div>        
        <?php } ?>  
        <?php //} ?>                        
    </div>  
    <div class="h-list" style="overflow-y: scroll; overflow-x: scroll; margin-top: 10px; height: 270px;">
        <table id="customers" class="table-role" style="margin-top: 0 !important;">
            <tr>
                <th>Top</th>               
                <th>Server</th>             
                <th>Điểm</th>
            </tr>
            <?php                                 
            foreach ($data_top_all_server as $key => $value) {                                    
            ?>
            <tr>                
                <td><?php echo $value["TOP"]; ?></td>     
                <td><?php echo $value["SID"]; ?></td>
                <td><?php echo $value["TOTALPOINT"]; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <?php //echo var_dump($history"]) ?>
    <!--<div class="message success">Tạm thời chưa có dữ liệu. </div>-->
</div>
