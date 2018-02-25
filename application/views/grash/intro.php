<?php

use GraphShare\Object\Values\BaseLinks;
use GraphShare\Object\Values\GameApps;

include APPPATH . 'views/grash/header.php';
?>
<script type="text/javascript">
    $(function () {
        $("img[width=632][height=287]").attr("style", "width: 100%; height: auto;");
        $("table[width=450]").css("width", "100%");
    });
</script>
<div class="page_wrapper">
    <div class="col-xs-12 header">        
        <span class="col-xs-3 num1"></span>
        <div class="col-xs-9 account_info">
            <?php if ($isLogin == true) { ?>
                <a href="#"><img class="icon-profile" src="<?php echo $profile["link_picture"] ?>" />
                    <?php echo $profile["fbname"] ?></a>|
                <a href="<?php echo BaseLinks::BASE_HOME_URI ?>/logout?k=<?php echo $key ?>">Đăng xuất</a>
            <?php } else { ?>
                <a href="<?php echo BaseLinks::BASE_HOME_URI ?>/login?k=<?php echo $key ?>">Đăng nhập</a>
            <?php }
            ?>
        </div>
    </div>     
    <div id="intro-content" style="padding: 15px"><?php
        $ch = curl_init("http://data.mobo.vn/home/get_post_id/" . GameApps::GRASH_INTRO_ID . "/1");

        //curl_setopt($ch, CURLOPT_NOBODY, true);
        $datas = curl_exec($ch);
        curl_close($ch);        
        echo $datas;
        ?>
    </div>  
</div>  
<div class="footer-panel">
    <div class="col-xs-12 friend-footer" id="btn-friend-close" style="border-right: 1px solid #fff;">Đóng</div>              
    <div class="clear"></div>		
</div>   

<?php
include APPPATH . 'views/grash/footer.php';
?>
