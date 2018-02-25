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
    <div class="col-xs-12">
        Trừ Item
    </div>
    <form action="" method="post">
        <div class="col-xs-12">
            <div class="col-xs-3">   
                Item Id
            </div>                
            <div class="col-xs-9">            
                <input name="item_id" class="form-control" value="<?php echo $_POST["item_id"] ?>" >
            </div>   
            <div class="col-xs-3">   
                Item Type
            </div>                
            <div class="col-xs-9">            
                <input name="item_type" class="form-control" value="<?php echo $_POST["item_type"] ?>" >
            </div>  
            <div class="col-xs-3">   
                Count
            </div>                
            <div class="col-xs-9">            
                <input name="count" class="form-control" value="<?php echo $_POST["count"] ?>" >
            </div>     
        </div>   
        <div class="col-xs-12">
            <input name="button" class="form-control" type="submit" value="Submit" >
        </div>
        <div class="col-xs-12">
            <?php echo $message ?>
        </div>
    </form>     
</div>
<?php
include APPPATH . 'views/event/demo/footer.php';
?>
