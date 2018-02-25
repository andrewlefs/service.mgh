<?php
use GraphShare\Definition;
use GraphShare\Object\Values\BaseLinks;
use GraphShare\Object\Values\InviteRoles;
use GraphShare\Object\Values\BaseViews;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Chia sẽ Facebook</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />  
        <meta name="apple-mobile-web-app-capable" content="yes"/>
        <meta name="apple-touch-fullscreen" content="yes"/>
        <meta name="copyright" content="Copyright ME 2012"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
        <link href="<?php echo BaseLinks::BASE_ASSET_URL ?>/css/bootstrap.min.css" type="text/css" rel="stylesheet" /> 
        <link href="<?php echo BaseLinks::BASE_ASSET_URL ?>/css/main.css" type="text/css" rel="stylesheet" /> 
        <link href="<?php echo BaseLinks::BASE_ASSET_URL ?>/css/jquery-confirm.css" type="text/css" rel="stylesheet" /> 
        <script src="<?php echo BaseLinks::BASE_ASSET_URL ?>/js/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo BaseLinks::BASE_ASSET_URL ?>/js/bootstrap.min.js" type="text/javascript"></script>        
        <script src="<?php echo BaseLinks::BASE_ASSET_URL ?>/js/jquery.countdown.js" type="text/javascript"></script>
        <script src="<?php echo BaseLinks::BASE_ASSET_URL ?>/js/main.js" type="text/javascript"></script> 
        <script type="text/javascript">
            var base_url = "<?php echo BaseLinks::BASE_URI ?>";
            var base_home_url = "<?php echo BaseLinks::BASE_HOME_URI ?>";
            var key = "<?php echo $key ?>";
            var max_invite = <?php echo InviteRoles::MAX_INVITE ?>;
            var paging = <?php echo BaseLinks::PAGING ?>;
            var form = "<?php echo $form ?>";
        </script>
    </head>
    <body>
        <div class="overplay"></div>
        <div id="loading" class="loading">           
            <img class="icon-loading" src="<?php echo BaseLinks::BASE_ASSET_URL ?>/images/loading.gif"></img>
        </div>
        <div id="dialog" class="dialog">
            <div class="d-header">                
                <span class="d-title">Thông báo</span>  
                <img class="d-img-close" src="<?php echo BaseLinks::BASE_ASSET_URL ?>/images/close_btn.png"></img>
            </div>
            <div class="d-body">
                <div id="d-message" class="d-message">
                    Lỗi
                </div>
            </div>
            <div class="d-footer">
                <a href="#" class="btn-confirm d-confirm">Đồng ý</a>
            </div>
        </div> 
        
