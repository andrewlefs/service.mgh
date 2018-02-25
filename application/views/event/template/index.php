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
    $(document).ready(function() {        
        $('#main-content img').error(function() {
            $(this).unbind('error').attr('src', 'http://data.mobo.vn/' + $(this).attr('src'));

        });
    });
</script>
<div id="main-content"  class="col-xs-12">
    <div id="thele" class="cke_editable"> 
        <?php
        echo get_link_contents("http://data.mobo.vn/home/get_post_id/{$content_id}/1/");
        ?>
    </div>        
</div>
<?php
include APPPATH . 'views/event/template/footer.php';
?>
