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
    <div id="tournament" style="margin: auto; width: 288px; font-size: 13px; margin-top: 10px"> 
        Tổng tích lũy: <span style="color: #2196F3; font-weight: bold;"><?php echo $server_total; ?> Ngân Lượng</span>
    </div> 
        <div class="h-list" style="overflow-y: scroll; overflow-x: scroll; margin-top: 10px; height: 270px;">
        <table id="customers" class="table-role" style="margin-top: 0 !important;">
            <tr>
                <th>Thưởng</th>
                <th>Ngân Lượng</th>
            </tr>
            <?php                                 
              foreach ($tournament as $key => $value) {                                    
            ?>
            <tr>
                <td><?php echo $value["tournament_name"]; ?></td>                
                <td><span style="color: #2196F3; font-weight: bold;"><?php echo $value["point_bonus"]; ?></span></td>               
            </tr>
            <?php } ?>
        </table>
    </div> 
    <?php //echo var_dump($history"]) ?>
    <!--<div class="message success">Tạm thời chưa có dữ liệu. </div>-->
</div>
