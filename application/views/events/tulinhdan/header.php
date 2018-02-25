<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-touch-fullscreen" content="yes">
        <title>Sự Kiện ME</title>
        <link href="/mgh2/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="/mgh2/css/social.css">
        <script src="/mgh2/js/jquery.min.js"></script>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="/master_template/js/html5shiv.min.js"></script>
          <script src="/master_template/js/respond.min.js"></script>
        <![endif]-->
        <title><?php echo $title; ?> </title>
        <script>var base_url = "http://game.mobo.vn/mgh2/";</script>

        <link rel="stylesheet" href="/mgh2/assets/events/tulinhdan/css/style.css">
        <script type="text/javascript" src="/mgh2/assets/events/tulinhdan/scripts/jquery.min.js"></script>
        <script type="text/javascript" src="/mgh2/assets/events/tulinhdan/scripts/jquery.blockUI.js"></script>
        <script src="/mgh2/assets/events/tulinhdan/scripts/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
        <script src="/mgh2/assets/events/tulinhdan/scripts/jquery.spritely.js" type="text/javascript"></script>
        <script src="/mgh2/assets/events/tulinhdan/scripts/play.js" type="text/javascript"></script>
        <script src="/mgh2/assets/events/tulinhdan/scripts/main.js"></script>
        <script src="/mgh2/js/mp.js"></script>

        <script>
            var action = "";
            var id;
            var premiership = false;
            var topllserver = false;

            $(function () {
                $("#tournament_id_1").change(function () {
                    thamgia();
                });

                $("#tournament_id_2").change(function () {
                    lichsu();
                });

                $("#tournament_id_3").change(function () {
                    get_top();
                });

                $('.btn-close').click(function () {
                    $.unblockUI();
                    return false;
                });

                $('#yes_pet').click(function () {
                    $.unblockUI();
                    if (action == "play") {
                        //Gift Exchange
                        id = $("#tournament_id_1").val();
                        $(".loading").show();
                        $.ajax({
                            method: "GET",
                            url: "/mgh2/event/tulinhdan/play_now/?id=" + id,
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

                    if (action == "gifttop") {
                        //Gift Exchange
                        $(".loading").show();
                        $.ajax({
                            method: "GET",
                            url: "/mgh2/event/tulinhdan/gift_top_exchange/?id=" + id,
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
                //Load tournament list
                $.ajax({
                    method: "GET",
                    url: "/mgh2/event/tulinhdan/tournament_list_ex",
                    contentType: 'application/json; charset=utf-8',
                    success: function (data) {
                        var obj = JSON.parse(data);
                        var tourlist = "";
                        $.each(obj, function (key, value) {
                            tourlist += '<option value="' + value["id"] + '" >' + value["tournament_name"] + '</option>';
                        });

                        $("#tournament_id_1").html(tourlist);
                        $("#tournament_id_2").html(tourlist);
                        $("#tournament_id_3").html(tourlist);
                    },
                    error: function (data) {
                        var obj = $.parseJSON(data);
                    }
                });

                //Loa The Le
                thele();
            });

            function play_now() {
                action = "play";
                $("#mess_quest").html("Bạn có chắc chắn muốn tích lũy");
                $.blockUI({ css: { width: "65% ", left: '20% ' }, message: $('#questioninfo') });
            }

            function gift_top_exchange(s_id) {
                id = s_id;
                action = "gifttop";
                $("#mess_quest").html("Bạn có chắc chắn muốn nhận quà Top");
                $.blockUI({ css: { width: "65% ", left: '20% ' }, message: $('#questioninfo') });
            }

            function thamgia() {
                var tournament_id = $("#tournament_id_1").val();
                console.log(tournament_id);
                $(".loading").show();
                $.ajax({
                    url: "/mgh2/event/tulinhdan/thamgia?id=" + tournament_id,
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                    .done(function (data) {
                        $('#team_list').html(data);
                        $(".loading").hide();

                    }).fail(function (data) {
                    $('#history-content').html('Không thể tham gia, vui lòng thử lại sau.');
                    $(".loading").hide();
                });
            }

            function get_top() {
                $(".loading").show();
                $("server_list_tr").show();
                premiership = false;
                $(".tab-button").removeClass("active");
                $(".tab-button1").addClass("active");
                $("#server_list_tr").show();
                var tournament_id = $("#tournament_id_3").val();
                var server_id = $("#server_list").val();

                $.ajax({
                    url: "/mgh2/event/tulinhdan/get_top?id=" + tournament_id + "&server_id=" + server_id,
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                    .done(function (data) {
                        $('#top-content').html(data);
                        $(".loading").hide();

                    }).fail(function (data) {
                    $('#top-content').html('Không thể lấy được danh sách Top, vui lòng thử lại sau.');
                    $(".loading").hide();
                });
            }

            function lichsu() {
                $(".loading").show();
                var tournament_id = $("#tournament_id_2").val();
                $.ajax({
                    url: "/mgh2/event/tulinhdan/get_exchange_history?id=" + tournament_id,
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                    .done(function (data) {
                        $('#history-content').html(data);
                        $(".loading").hide();

                    }).fail(function (data) {
                    $('#history-content').html('Không thể lấy được lịch sử, vui lòng thử lại sau.');
                    $(".loading").hide();
                });
            }

            function thele() {
                $(".loading").show();
                $.ajax({
                    url: "/mgh2/event/getnews/content_news?id=581",
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                    .done(function (data) {
                        $('#my_content').html(data);
                        $(".loading").hide();

                    }).fail(function (data) {
                    $('#my_content').html('Không thể lấy được thể lệ, vui lòng thử lại sau.');
                    $(".loading").hide();
                });
            }

            function napthe() {
                $(".loading").show();

                $.ajax({
                    url: "/mgh2/event/tulinhdan/napthe?id=" + $("#tournament_id_1").val(),
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                    .done(function (data) {
                        $('#napthe-content').html(data);
                        $(".menu-item").removeClass('active');
                        $(".menu-item-item3").addClass('active');
                        $(".loading").hide();

                    }).fail(function (data) {
                    $('#napthe-content').html('Không thể nạp thẻ lúc này, vui lòng thử lại sau.');
                    $(".loading").hide();
                });
            }

            function charging() {
                if ($("#cardtype").val() == "") {
                    show_message("Bạn chưa loại thẻ", 0);
                }
                else if ($("#card_seri").val() == "") {
                    show_message("Bạn chưa nhập số seri", 0);
                }
                else if ($("#card_code").val() == "") {
                    show_message("Bạn chua nhập mã số thẻ", 0);
                }
                else {
                    $(".loading").show();
                    var cardtype = $("#cardtype").val();
                    var data = "card[code]=" + $("#card_code").val() + "&card[seri]=" + $("#card_seri").val() + "&card[type]=" + cardtype + "&card[name]=&card[event]=1";
                    mp.init();
                    mp.sendRequest(POST, Urls.AJAX_URL + "/shopnganluong_charging", data, function (response) {
                        //mp.sendRequest(POST, "/mgh2/event/shopnganluong/shopnganluong_charging", data, function (response) {
                        if (typeof response == 'object') {
                            if (response.error == 0) {
                                napthe();
                                show_message(response.message);
                                $(".loading").hide();
                            }
                            else {
                                show_message(response.message);
                                $(".loading").hide();
                            }
                            $(".loading").hide();
                            return false;
                        }
                        else {
                            show_message("Lỗi hệ thống xin vui lòng thử lại sau");
                            $(".loading").hide();
                            return false;
                        }
                    });
                }

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

        <style>
            a.title{ color: #0a2c5d;font-weight: 500; font-size: 20px; }
            @media (max-width: 768px) {
                a.title{font-size: 15px; font-weight: bold;}
            }
        </style>
    </head>
    <body>        
        <div id="loading" class="loading">
            <div class="loading-bg">
                <img class="loading-img" src="/mgh2/img/loading.gif">
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div id="main-menu-bt">
                    <ul>
                        <li class="col-xs-3"><a  href="/mgh2/social?<?php
                            $params = $_GET;
                            unset($params["p"]);
                            echo http_build_query($params);
                            ?>">Sự kiện</a></li>                     
                        <li class="col-xs-9"><a>Nhân vật: <?php echo $user->character_name ?>, Máy chủ: <?php echo $user->server_id ?></a></li>
                    </ul>
                </div>

                <div class="header">
                    <div class="col-xs-12">
                    <a href="javascript:void(0);" rel="the-le" class="menu-item menu-item-item1 active"
                       onclick="showcontent(this)">Thể lệ</a>
                    <a href="javascript:void(0);" rel="tham-gia" class="menu-item menu-item-item2 "
                       onclick="showcontent(this);thamgia();">Tham gia</a>
                    <a href="javascript:void(0);" rel="lich-su" class="menu-item menu-item-item4"
                       onclick="showcontent(this);lichsu();">Lịch sử</a>
                    </div>
                </div>




