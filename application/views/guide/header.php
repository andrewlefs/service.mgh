<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-touch-fullscreen" content="yes">
        <title>Sự Kiện ME</title>
        <link href="/onepiece/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="/onepiece/css/css_mobile.css">
        <script src="/onepiece/js/jquery.min.js"></script>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="/assets/socialnew/js/html5shiv.min.js"></script>
          <script src="/assets/socialnew/js/respond.min.js"></script>
        <![endif]-->
        <script type="text/javascript">
            $(document).ready(function() {
                $('.bottom img').error(function() {
                    $(this).unbind('error').attr('src', 'http://data.mobo.vn/' + $(this).attr('src'));
                });
            });
        </script>
    </head>
    <body style="overflow-x: visible;">        
