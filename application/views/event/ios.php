<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-touch-fullscreen" content="yes">
        <title>Mộng 2017</title>       
        <script src="/js/jquery.min.js"></script>  
    </head>
    <body>
        <?php
        $ch = curl_init("http://data.mobo.vn/home/get_post_id/6249/1");        
        $datas = curl_exec($ch);
        curl_close($ch);
        echo $datas;
        ?>
    </body>
</html>