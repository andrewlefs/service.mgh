<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-touch-fullscreen" content="yes">
        <title>Sự Kiện ME</title>
        <link href="/mathan/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="/mathan/css/social.css">        
        <script src="/mathan/js/jquery.min.js"></script>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="/master_template/js/html5shiv.min.js"></script>
          <script src="/master_template/js/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>        
        <div id="loading" class="loading">
            <div class="loading-bg">
                <img class="loading-img" src="/mathan/img/loading.gif">
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div id="main-menu-bt">
                    <ul>
                        <li class="col-xs-3"><a  href="/mathan/index.php/social?<?php
                            $params = $_GET;
                            unset($params["p"]);
                            echo http_build_query($params);
                            ?>">Sự kiện</a></li>                     
                        <li class="col-xs-9"><a>Nhân vật: <?php echo $user->character_name ?>, Máy chủ: <?php echo $user->server_id ?></a></li>                       
                    </ul>
                </div>     

