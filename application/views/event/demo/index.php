<?php
//var_dump(APPPATH . 'views/event/demo/header.php');die;
//var_dump(file_exists(APPPATH . 'views/event/demo/header.php'));
//die;
include APPPATH . 'views/event/demo/header.php';
include APPPATH . 'views/event/demo/navigation.php';
?>
<style type="text/css">
    #main-content{
        padding-top: 10px;
    }
</style>     
<script type="text/javascript">
    $(document).ready(function() {
        $('#main-content img').error(function() {
            $(this).unbind('error').attr('src', 'http://data.mobo.vn/' + $(this).attr('src'));

        });
    });
</script>
<div id="main-content"  class="col-xs-12">
    <div>               
        <div class="" style="text-align: center;">
            <?php
            if ($list == false) {
                echo "Không thể truy vấn được thông tin user";
            } else {
                foreach ($list as $key => $info) {
                    ?> 
                    <table id="customers">
                        <tr>                    
                            <th>Field Name</th>
                            <th>Value</th>                            
                        </tr>
                        <?php
                        foreach ($info as $k => $value) {
                            ?>
                            <tr>                    
                                <td><?php echo $k ?></td>
                                <td><?php echo $value ?></td>              
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <?php
                }
            }
            ?>                               
        </div>        
    </div>    
</div>
<?php
include APPPATH . 'views/event/demo/footer.php';
?>
