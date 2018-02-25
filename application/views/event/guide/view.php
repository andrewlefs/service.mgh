<?php
//var_dump(APPPATH . 'views/event/guide/header.php');die;
//var_dump(file_exists(APPPATH . 'views/event/guide/header.php'));
//die;
include APPPATH . 'views/event/guide/header.php';
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
    $(document).ready(function() {        
        $('#main-content img').error(function() {            
            $(this).unbind('error').attr('src', 'http://data.mobo.vn/' + $(this).attr('src'));
        });
    });
</script>
<div id="main-content"  class="col-xs-12">
    <?php
    $datas = get_link_contents("http://data.mobo.vn/home/get_post_id/" . $_GET["ids"] . "/1");
    echo $datas;
    ?>
</div>

<?php
include APPPATH . 'views/event/guide/footer.php';
?>
