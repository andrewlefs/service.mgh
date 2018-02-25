<?php
include $controller->getPathView() . '/header.php';
include $controller->getPathView() . '/navigation.php';

use MigEvents\Tripledes;
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
            <?php
            if ($list == true && is_array($list)) {
                ?>
                <table id="customers">
                    <tr>     
                        <?php
                        if (count($list) > 0) {
                            $headers = array();
                            foreach ($list[0] as $key => $value) {
                                $headers[] = $key;
                                echo "<th>{$key}</th>";
                            }
                        }
                        ?>                        
                    </tr>
                    <?php foreach ($list as $key => $value) {
                        ?>
                        <tr>
                            <?php
                            foreach ($headers as $k => $val) {
                                ?>
                                <td><?php
                                    if (is_array($value[$val])) {
                                        echo json_encode($value[$val]);
                                    } else{
                                        echo $value[$val];
                                    }
                                        ?></td>
                                <?php
                            }
                            ?>                          
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <?php
            }
            ?>                                      
        </div>  
    </div>        
</div>
<?php
include $controller->getPathView() . '/footer.php';
?>
