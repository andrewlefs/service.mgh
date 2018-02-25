<?php
//var_dump(APPPATH . 'views/guide/header.php');die;
//var_dump(file_exists(APPPATH . 'views/guide/header.php'));
//die;
include APPPATH . 'views/guide/header.php';
?>   
<div class="menu">
    <a class="sukien" href="/onepiece/su-kien">Sự kiện</a>
    <a class="tintuc" href="/onepiece/tin-tuc">Thông báo</a>
    <a class="huongdan active" href="/onepiece/huong-dan">Hướng dẫn</a>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#main-content img').error(function() {
            $(this).unbind('error').attr('src', 'http://data.mobo.vn/' + $(this).attr('src'));
        });
    });
</script>
<div class="bottom">
    <div class="sub ing">
        <div class="content">
            <div class="clear"></div>
            <?php
            $datas = get_link_contents("http://data.mobo.vn/home/get_post_id/" . $cid . "/1");
            echo $datas;
            ?>            
        </div>
    </div>   
</div>  
<?php
include APPPATH . 'views/guide/footer.php';
?>
