<?php
//var_dump(APPPATH . 'views/event/template/header.php');die;
//var_dump(file_exists(APPPATH . 'views/event/template/header.php'));
//die;
include APPPATH . 'views/event/template/header.php';
include APPPATH . 'views/event/template/navigation.php';
?>
<style type="text/css">
    #main-content{
        padding-top: 10px;
    }
</style>      
<script type="text/javascript">

    function show_loading() {
        $("#loading").fadeIn("fast");
        return true;
    }
</script>
<div id="main-content"  class="col-xs-12">
    <div>               
        <div class="" style="text-align: center;">
            <table id="customers">
                <tr>                    
                    <th>Mệnh giá nạp</th>
                    <th>Kim Cương</th>
                    <th>Khuyến mãi</th>
                    <th>Kim Cương KM</th>                    
                </tr>
                <tr>                    
                    <td>20,000</td>
                    <td>200</td>
                    <td>50%</td>
                    <td>100</td>                    
                </tr>
                <tr>                    
                    <td>20,000</td>
                    <td>200</td>
                    <td>50%</td>
                    <td>100</td>                    
                </tr>
                <tr>                    
                    <td>20,000</td>
                    <td>200</td>
                    <td>50%</td>
                    <td>100</td>                    
                </tr>
            </table>           
            <div class="line"></div>
            <div class="message-error"><?php echo $message ?></div>       
        </div>        
    </div>

    <?php
    include APPPATH . 'views/event/template/footer.php';
    ?>
