<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no"/>
        <meta name="apple-mobile-web-app-capable" content="yes"/>
        <meta name="apple-touch-fullscreen" content="yes"/>
        <link href="/mgh2/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="/mgh2/css/social.css" rel="stylesheet" type="text/css"/>
        <title>Sự kiện Tân Mộng Giang Hồ</title>
    </head>
    <body>
        <div class="container" style="width: 425px">
            <div class="row">
                <div id="main-menu-bt">
                    <ul class="nav">
                        <li class="col-xs-12"><a class="nav-bar" href="/mgh2/index.php/event/guide?<?php echo http_build_query($_GET) ?>"><img class="icon-menu img-responsive" style="" src="/mgh2/img/sheld.png">Hướng dẫn </a></li>
                        <li class="col-xs-12" style="display: none"><a class="nav-bar" href="/mgh2/index.php/event/event_hot?<?php echo http_build_query($_GET) ?>"><img class="icon-menu img-responsive" style="" src="/mgh2/img/fire.png">Sự kiện hot</a></li>
                    </ul>
                </div>
                <div class="col-xs-12 text-center">
                    <form method="POST">                        
                            <?php                            
                            $ipreal = explode(",", $ip);                            
                            if ($SERVICES) {
                                $i = 0;
                                foreach ($SERVICES as $S) {

                                    if ($S->service_status === 'true') {

                                        $jsoninfo = json_decode($_GET['info'], true);                                        
                                        $server_id = (int) $jsoninfo['server_id'];                                        
                                        if ((empty($S->service_trustip)) or strlen($S->service_trustip) <= 5 or in_array($ipreal[0], array_map('trim', explode(chr(10), $S->service_trustip)))) {

                                            $statuscheck = false;
                                            $service_start = '';
                                            $service_end = '';
                                            if (!empty($S->jsonRule) && strlen($S->jsonRule) >= 5) {
                                                $parseJson = json_decode($S->jsonRule, true)[$server_id];
                                                if (!empty($parseJson) && is_array($parseJson)) {
                                                    $service_start = $parseJson['service_start'];
                                                    $service_end = $parseJson['service_end'];
                                                    $statuscheck = true;
                                                }
                                            } else {
                                                $statuscheck = true;
                                                $service_start = $S->service_start;
                                                $service_end = $S->service_end;
                                            }
                                            if ($statuscheck) {
                                                $package_names = json_decode($S->package_name, true);
                                                $isshow = true;
                                                if (!isset($_GET["package_name"]) && $package_names == true) {
                                                    $isshow = false;
                                                }
                                                if ($package_names == true && isset($_GET["package_name"])) {
                                                    if (!in_array($_GET["package_name"], $package_names)) {
                                                        $isshow = false;
                                                    }
                                                }
                                                if ($isshow && (strtotime($service_start) <= time() AND strtotime($service_end) >= time())) {
													$checkenable = false;
													if($_GET['platform'] =='android'){
														$checkenable = ($S->service_android == 1)?true:false;
													}elseif($_GET['platform'] =='ios'){
														$checkenable = ($S->service_ios == 1)?true:false;
													}elseif($_GET['platform'] =='wp'){
														$checkenable = ($S->service_wp == 1)?true:false;
													}else{
														$checkenable = ($S->service_android == 0 && $S->service_ios == 0 && $S->service_wp == 0)?true:false;
													}
													if($checkenable){
														
                                                    ?>
                                                    <div class="even-item">
                                                        <?php
                                                        if ($S->service_ishot == "is_hot") {
                                                            ?>
                                                            <img src="/mgh2/img/hot.png" class="img-responsive hotnew-label">
                                                        <?php } else if ($S->service_ishot == "is_new") {
                                                            ?>
                                                            <img src="/mgh2/img/new.png" class="img-responsive hotnew-label">
                                                            <?php
                                                        }
                                                        ?>
                                                        <a href="<?php echo $controler->rebuild_http($S->service_url, $_GET) ?>" class="even-list">
                                                            <?php if ($S->service_id == "11") {?>
                                                            <img src="/mgh2/img/tichluy_bg.png" class="img-responsive center-block">
                                                            <span class="text-cover"><?php echo (isset($S->title) && !empty($S->title)) ? $S->title : $S->service_title ?>
                                                            <span style="font-size: 13px;width: 120px;margin-top: -10px;position: absolute;text-align: center;color: #FFEB3B;margin-left: 46px;">Ngân lượng
<span style="position: absolute;margin-top: 15px;text-align: center;margin-left: -51%;"><?php echo $server_total; ?></span></span>
                                                            </span>                                                                
                                                            <?php } else if ($S->service_id == "14" && $server_total_loidai > 0) { ?>
                                                                <img src="/mgh2/img/tichluy_bg.png" class="img-responsive center-block">
                                                            <span class="text-cover"><?php echo (isset($S->title) && !empty($S->title)) ? $S->title : $S->service_title ?>
                                                            <span style="font-size: 13px;width: 120px;margin-top: -10px;position: absolute;text-align: center;color: #FFEB3B;margin-left: 46px;">Ngân lượng
<span style="position: absolute;margin-top: 15px;text-align: center;margin-left: -51%;"><?php echo $server_total_loidai; ?></span></span>
                                                            </span> 
                                                            <?php } else { ?>
                                                            <img src="/mgh2/img/<?php echo $this->arrayimage[$i]; ?>" class="img-responsive center-block">
                                                            <span class="text-cover"><?php echo (isset($S->title) && !empty($S->title)) ? $S->title : $S->service_title ?></span>
                                                             <?php } ?>
                                                            
                                                            
                                                        </a>
                                                    </div>
                                                    <?php
													}//else echo '<div class="item"><div><span>PLATFORM</span></div></div>';
                                                }//else echo '<div class="item"><div><span>TRUST SERVER</span></div></div>';
                                            }// else echo '<div class="item"><div><span>TRUST IP</span></div></div>';
                                        }// else echo '<div class="item"><div><span>TRUST TIME</span></div></div>';
                                    }// else echo '<div class="item"><div><span>TRUST STATUS</span></div></div>';

                                    $i = ($i >= 5 ) ? 0 : ++$i;
                                }
                            } else
                                echo "Sự Kiện Đang Cập Nhật";
                            ?>                        
                    </form>

                </div>
            </div>
        </div>
        <script type="text/javascript" src="/mgh2/js/jquery.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {

                    var Witd = $(".container").width();
                    zoom = $(window).innerWidth() / Witd * 100;
                    document.body.style.zoom = zoom + "%";

                    $(window).on('resize', function () {
                        zoom = $(window).innerWidth() / Witd * 100;
                        document.body.style.zoom = zoom + "%";
                    });

                }
            });
        </script>
    </body>
</html>
