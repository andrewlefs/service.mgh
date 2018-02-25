<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-touch-fullscreen" content="yes">
        <meta http-equiv="Cache-control" content="no-cache">
        <link href="/mgh2/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="/mgh2/css/social.css">        
        <script src="/mgh2/js/jquery.min.js"></script> 
        <script type="text/javascript" src="/mgh2/assets_dev/events/tulinhdan/scripts/jquery.blockUI.js"></script>
        <script src="/mgh2/assets_dev/events/tulinhdan/scripts/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
        <script src="/mgh2/assets_dev/events/tulinhdan/scripts/jquery.spritely.js" type="text/javascript"></script>
        <script src="/mgh2/assets_dev/events/tulinhdan/scripts/play.js" type="text/javascript"></script>
        <script src="/mgh2/assets_dev/events/tulinhdan/scripts/main.js"></script>        
        <title><?php echo $title; ?> </title>
        <script>
            var action = "";
            var id;

            $(function () {
                $('.btn-close').click(function () {
                    $.unblockUI();
                    return false;
                });

                $('#yes_pet').click(function () {
                    $.unblockUI();
                    if (action == "gift") {
                        //Gift Exchange
                        //show_message('Gift Ok ID:' + id);

                        $(".loading").show();
                        $.ajax({
                            method: "GET",
                            url: "/mgh2/event/tulinhdan/gift_exchange/?id=" + id,
                            contentType: 'application/json; charset=utf-8',
                            success: function (data) {
                                json_data = $.parseJSON(data);
                                if (json_data.code != null && json_data.code != "0") {
                                    show_message(json_data.message);
                                    $(".loading").hide();
                                }
                                else {
                                    thamgia();
                                    show_message(json_data.message);
                                    $(".loading").hide();
                                }
                            },
                            error: function (data) {
                                console.log(data);
                                $(".loading").hide();
                            }
                        });
                    }
                });
            });

            $(document).ready(function () {
                $.get("/mgh2/event/shopkimbai/content_news?id=<?php echo $content_id; ?>", function (data) {
                    $("#the-le").html(data);
                    $('#content img').error(function () {
                        $(this).unbind('error').attr('src', 'http://data.mobo.vn/' + $(this).attr('src'));
                    });
                });
            });

            function gift_exchange(s_id, reward_name) {
                id = s_id;
                action = "gift";
                $("#mess_quest").html("Bạn có chắc chắn muốn nhận quà '" + reward_name + "'");
                $.blockUI({css: {width: "65% ", left: '20% '}, message: $('#questioninfo')});
            }

             function lichsu(id_his) {
                $(".loading").show();
                $.ajax({
                    url: "/mgh2/event/tulinhdan/get_exchange_history?id_his=" + id_his,
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                        .done(function (data) {
                            $('#lich-su').html(data);
                            $(".loading").hide();

                        }).fail(function (data) {
                    $('#lich-su').html('Không thể lấy được lịch sử, vui lòng thử lại sau.');
                    $(".loading").hide();
                });
            }

            function doiqua_covu() {
                window.location.href = "/mgh2/events/cacuoc_new/index_doiqua?<?php echo http_build_query($_GET); ?>";
            }

            function showcontent(e) {
                $(".loading").show();
                $(".content").hide();
                var tab = $(e).attr("rel");

                if (tab != "receive-code" && tab != "donate-code") {
                    $(".menu-item").removeClass('active');
                    $(".loading").hide();
                }

                if (tab == "receive-code" || tab == "donate-code" || tab == "nhan-luot") {
                    $(".nhanluot").addClass("active");
                    $(".loading").hide();
                }
                else {
                    $(e).addClass("active");
                    $(".loading").hide();
                    $("#" + tab).show();
                }
            }
        </script>
    </head>

    <body>
        <div class="wrapper">
            <div class="container">
                <div class="row">
                    <div id="main-menu-bt">				
                        <ul>
                            <li class="col-xs-3"><a href="/mgh2/social?<?php echo http_build_query($_GET) ?>">Sự kiện</a></li>
                            <li class="col-xs-9"><a>Nhân vật: <?php echo $char_name; ?>, Máy
                                    chủ: <?php echo $server_id; ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <br><style>
                a.title{ color: #0a2c5d;font-weight: 500; font-size: 20px; }
                @media (max-width: 768px) {
                    a.title{font-size: 15px; font-weight: bold;}
                }
            </style>
            <div class="container">
                <div class="row-menu" style="line-height: 20px !important; height: 32px !important;">
                    <div>
                        <a class="normal-link" style="font-weight: bold" href="/mgh2/index.php/event/tulinhdan?<?php echo http_build_query($_GET) ?>"><?php echo $event_name ?></a>
                    </div>
                    <div style="margin-top: 5px;">
                        <a href="javascript:;" rel="lich-su" onclick="showcontent(this); lichsu(1);" class="right-bt">
                            Lịch Sử
                        </a>
                        <a  href="/mgh2/index.php/event/tulinhdan/thamgia?<?php echo http_build_query($_GET) ?>" class="right-bt">
                            Tham gia
                        </a>                              
                    </div>
                </div> 
            </div>


            <div class="wrap">
                <div id="content">
                    <div class="children">
                        <div style="margin: 0 auto; font-size: 15px">                          
                            <div id="the-le" class="content"> 
                            </div>                     

                            <div id="tham-gia" class="content" style="display: none">
                                <div id="team_list"></div>                           
                            </div>

                            <div id="lich-su" class="content" style="display: none; text-align: center;">
                                Lich Su
                                <div id="history-content"></div>                         
                            </div>

                            <div class="clearboth"></div>
                        </div>
                    </div>

                </div>
                <div class="clearboth"></div>
            </div>
            <div class="clearboth"></div>
        </div>
        <div class="loading">
            <img src="/mgh2/assets_dev/events/tulinhdan/images/loading.gif" />
        </div>
        <div id="questioninfo" style="display:none; cursor: default">
            <h3 id="mess_quest" style="margin: 15px;font-size: 13px;"></h3>

            <div class='controlnumber'>
            </div>
            <div class="controlbutton">
                <input type="button" id="yes_pet" value="Có"/>
                <input type="button" id="no" class="btn-close" value="Không"/>

                <div class="checknumber"></div>
            </div>
        </div>
        <div class="modal-marker bg_popup" style="display: none" onclick="closePopup();"></div>
        <div class="modal-dialog bg_popup" style="display: none">
            <div class="modal-header">
                <i class="ico-warning"></i>
                THÔNG BÁO
                <i class="ico-close pull-right" onclick="closePopup();"></i>
            </div>
            <div class="modal-content content_error mess_content">
                Nội dung thông báo
            </div>
            <div class="modal-footer">
                <p style="cursor: pointer" onclick="closePopup();">QUAY LẠI</p>
            </div>
        </div>
    </body>
</html>





