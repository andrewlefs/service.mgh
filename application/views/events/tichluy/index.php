<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-touch-fullscreen" content="yes">
        <meta http-equiv="Cache-control" content="no-cache">
        <link rel="stylesheet" href="/mgh2/assets_dev/events/tichluy/css/style.css">
        <script type="text/javascript" src="/mgh2/assets_dev/events/tichluy/scripts/jquery.min.js"></script>
        <script type="text/javascript" src="/mgh2/assets_dev/events/tichluy/scripts/jquery.blockUI.js"></script>
        <script src="/mgh2/assets_dev/events/tichluy/scripts/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
        <script src="/mgh2/assets_dev/events/tichluy/scripts/jquery.spritely.js" type="text/javascript"></script>
        <script src="/mgh2/assets_dev/events/tichluy/scripts/play.js" type="text/javascript"></script>
        <script src="/mgh2/assets_dev/events/tichluy/scripts/main.js"></script>
        <title><?php echo $title; ?> </title>
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
                    if (premiership) {
                        get_top_premiership();
                    }
                    else
                    if (topllserver) {
                        get_top_all_server();
                    }
                    else {
                        get_top();
                    }
                });

                $("#server_list").change(function () {
                    get_top();
                });

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
                            url: "/mgh2/event/tichluy/gift_exchange/?id=" + id,
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
                            url: "/mgh2/event/tichluy/gift_top_exchange/?id=" + id,
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

                    if (action == "gifttoppremier") {
                        //Gift Exchange
                        $(".loading").show();
                        $.ajax({
                            method: "GET",
                            url: "/mgh2/event/tichluy/gift_top_exchange_premiership/?id=" + id,
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
                //Loa The Le
                thele();
            });

            function gift_exchange(s_id, reward_name) {
                id = s_id;
                action = "gift";
                $("#mess_quest").html("Bạn có chắc chắn muốn nhận quà '" + reward_name + "'");
                $.blockUI({css: {width: "65% ", left: '20% '}, message: $('#questioninfo')});
            }

            function gift_top_exchange(s_id) {
                id = s_id;
                action = "gifttop";
                $("#mess_quest").html("Bạn có chắc chắn muốn nhận quà Top");
                $.blockUI({css: {width: "65% ", left: '20% '}, message: $('#questioninfo')});
            }

            function gift_top_exchange_premiership(s_id) {
                id = s_id;
                action = "gifttoppremier";
                $("#mess_quest").html("Bạn có chắc chắn muốn nhận quà Top Ngoại Hạng");
                $.blockUI({css: {width: "65% ", left: '20% '}, message: $('#questioninfo')});
            }

            function thamgia() {
                var tournament_id = $("#tournament_id_1").val();
                $(".loading").show();
                $.ajax({
                    url: "/mgh2/event/tichluy/thamgia?id=" + tournament_id,
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

                if (tournament_id >= 12) {
                    $.ajax({
                        url: "/mgh2/event/tichluy/get_top?id=" + tournament_id + "&server_id=" + server_id,
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
                else {
                    $('#top-content').html('<br /><br /><span style="color: red;">Top chỉ áp dụng từ giải đấu tuần 4</span>');
                    $(".loading").hide();
                }
            }

            function get_top_premiership() {
                $(".loading").show();
                premiership = true;
                topllserver = false;

                $(".tab-button").removeClass("active");
                $(".tab-button2").addClass("active");
                $("#server_list_tr").hide();
                var tournament_id = $("#tournament_id_3").val();

                if (tournament_id >= 12) {
                    $.ajax({
                        url: "/mgh2/event/tichluy/get_top_premiership?id=" + tournament_id,
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
                else {
                    $('#top-content').html('<br /><br /><span style="color: red;">Top chỉ áp dụng từ giải đấu tuần 4</span>');
                    $(".loading").hide();
                }
            }

            function get_top_all_server() {
                $(".loading").show();
                premiership = false;
                topllserver = true;

                $(".tab-button").removeClass("active");
                $(".tab-button3").addClass("active");
                $("#server_list_tr").hide();
                var tournament_id = $("#tournament_id_3").val();

                if (tournament_id >= 14) {
                    $.ajax({
                        url: "/mgh2/event/tichluy/get_top_allserver?id=" + tournament_id,
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
                else {
                    $('#top-content').html('<br /><br /><span style="color: red;">Top Liên Server chỉ áp dụng từ giải đấu tuần 6</span>');
                    $(".loading").hide();
                }
            }

            //Get Top New
            function get_top_arena() {
                $(".loading").show();
                $(".tab-button2").removeClass("active");
                $(".tab-button1").addClass("active");
                var tournament_id = $("#tournament_id_3").val();

                if (tournament_id >= 12) {
                    $.ajax({
                        url: "/mgh2/event/tichluy/get_top_arena?id=" + tournament_id,
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
                else {
                    $('#top-content').html('<br /><br /><span style="color: red;">Top chỉ áp dụng từ giải đấu tuần 4</span>');
                    $(".loading").hide();
                }
            }

            function get_top_battle() {
                $(".loading").show();
                $(".tab-button1").removeClass("active");
                $(".tab-button2").addClass("active");
                var tournament_id = $("#tournament_id_3").val();

                if (tournament_id >= 12) {
                    $.ajax({
                        url: "/mgh2/event/tichluy/get_top_battle?id=" + tournament_id,
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
                else {
                    $('#top-content').html('<br /><br /><span style="color: red;">Top chỉ áp dụng từ giải đấu tuần 4</span>');
                    $(".loading").hide();
                }
            }

            function get_reward_list() {
                $(".loading").show();
                $.ajax({
                    url: "/mgh2/event/tichluy/get_reward_list/",
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                        .done(function (data) {
                            $('#top-content').html(data);
                            $(".loading").hide();                            

                        }).fail(function (data) {
                    $('#top-content').html('Không thể lấy được danh sách phần thưởng, vui lòng thử lại sau.');
                    $(".loading").hide();
                });

            }

            function lichsu() {
                $(".loading").show();
                var tournament_id = $("#tournament_id_2").val();
                $.ajax({
                    url: "/mgh2/event/tichluy/get_exchange_history?id=" + tournament_id,
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
                    url: "/mgh2/event/getnews/content_news?id=5634",
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

            <div class="wrap">
                <div class="header">
                    <a href="javascript:void(0);" rel="the-le" class="menu-item menu-item-item1 active"
                       onclick="showcontent(this)">Thể lệ</a>             
                    <a href="javascript:void(0);" rel="get-top" class="menu-item menu-item-item2"
                       onclick="showcontent(this);
                            get_reward_list();">Tham Gia</a>             
                </div>
                <div id="content">
                    <div class="children">
                        <div style="margin: 0 auto; font-size: 15px">
                            <div id="the-le" class="content">
                                <div id="my_content" style="text-align: justify; width: 95%; margin: auto; margin-top: 10px;"></div>
                            </div>

                            <div id="tham-gia" class="content" style="display: none">                              
                                <div id="team_list"></div>
                            </div>

                            <div id="lich-su" class="content" style="display: none; text-align: center;">                                
                                <div id="history-content"></div>
                            </div>

                            <div id="get-top" class="content" style="display: none; text-align: center;">
                                <div id="top-content"></div>
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
            <img src="/mgh2/assets_dev/events/tichluy/images/loading.gif" />
        </div>
        <div id="questioninfo" style="display: none; cursor: default">
            <h3 id="mess_quest" style="margin: 15px; font-size: 13px;"></h3>

            <div class='controlnumber'>
            </div>
            <div class="controlbutton">
                <input type="button" id="yes_pet" value="Có" />
                <input type="button" id="no" class="btn-close" value="Không" />

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





