<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-touch-fullscreen" content="yes">
        <meta http-equiv="Cache-control" content="no-cache">
        <link rel="stylesheet" href="/mgh2/assets_dev/events/keobuabao/css/style.css">
        <script type="text/javascript" src="/mgh2/assets_dev/events/keobuabao/scripts/jquery.min.js"></script>
        <script type="text/javascript" src="/mgh2/assets_dev/events/keobuabao/scripts/jquery.countdown.js"></script>
        <script type="text/javascript" src="/mgh2/assets_dev/events/keobuabao/scripts/jquery.blockUI.js"></script>
        <script src="/mgh2/assets_dev/events/keobuabao/scripts/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
        <script src="/mgh2/assets_dev/events/keobuabao/scripts/jquery.spritely.js" type="text/javascript"></script>
        <script src="/mgh2/assets_dev/events/keobuabao/scripts/play.js" type="text/javascript"></script>
        <script src="/mgh2/assets_dev/events/keobuabao/scripts/main.js"></script>
        <title><?php echo $title; ?> </title>
        <script>
            $(document).ready(function () {
                //Load The Le
                thele();
            });

            var action = "";
            var sjoin_id = "";
            $(function () {
                $('.btn-close').click(function () {
                    $.unblockUI();
                    return false;
                });
                
                $('#yes_pet_play').click(function () {
                    $.unblockUI();
                    if (action == "play_process") {
                        $(".loading").show();
                        $.ajax({
                            method: "GET",
                            url: "/mgh2/event/keobuabao/play_process/?join_id=" + sjoin_id + "&type_choose=" + $("input:radio[name='imgsel_play']:checked").val()
                            ,
                            contentType: 'application/json; charset=utf-8',
                            success: function (data) {
                                json_data = $.parseJSON(data);
                                if (json_data.code != null && json_data.code != "0") {
                                    show_message(json_data.message);
                                    $(".loading").hide();
                                }
                                else {
                                    thachdau();
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

                $('#yes_pet').click(function () {
                    $.unblockUI();
                    if (action == "join_process") {
                        $(".loading").show();
                        $.ajax({
                            method: "GET",
                            url: "/mgh2/event/keobuabao/join_process/?moccuoc_group=" + $("#moccuoc_group").val() + "&type_choose=" + $("input:radio[name='imgsel']:checked").val()
                            ,
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
            
            function join_details(join_id){
                 $.unblockUI();                 
                 $(".loading").show();
                        $.ajax({
                            method: "GET",
                            url: "/mgh2/event/keobuabao/join_details/?join_id=" + join_id
                            ,
                            contentType: 'application/json; charset=utf-8',
                            success: function (data) {
                                json_data = $.parseJSON(data);
                                if (json_data.code != null && json_data.code != "0") {
                                    show_message(json_data.message);
                                    $(".loading").hide();
                                }
                                else {                                    
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
            
            function play_details(play_id){
                 $.unblockUI();                 
                 $(".loading").show();
                        $.ajax({
                            method: "GET",
                            url: "/mgh2/event/keobuabao/play_details/?play_id=" + play_id
                            ,
                            contentType: 'application/json; charset=utf-8',
                            success: function (data) {
                                json_data = $.parseJSON(data);
                                if (json_data.code != null && json_data.code != "0") {
                                    show_message(json_data.message);
                                    $(".loading").hide();
                                }
                                else {                                    
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

            function join_process() {
                action = "join_process";
                var type_s = "";
                if ($("input:radio[name='imgsel']:checked").val() == "keo") {
                    type_s = "Kéo";
                }
                if ($("input:radio[name='imgsel']:checked").val() == "bua") {
                    type_s = "Búa";
                }
                if ($("input:radio[name='imgsel']:checked").val() == "bao") {
                    type_s = "Bao";
                }
                $("#mess_quest").html("Bạn có chắc chắn muốn đặt cược '" + type_s + " - " + $("#moccuoc_group option:selected").text() + " Ngân Lượng'");
                $.blockUI({css: {width: "65% ", left: '20% '}, message: $('#questioninfo')});
            }

            function play_process(char_name, join_id) {
                sjoin_id = join_id;
                action = "play_process";
                $("#mess_quest_play").html("Lựa chọn của bạn để đấu với '" + char_name + "':");
                $.blockUI({css: {width: "65% ", left: '20% '}, message: $('#questioninfo_play')});
            }

            function gift_exchange(s_id, reward_name) {
                id = s_id;
                action = "gift";
                $("#mess_quest").html("Bạn có chắc chắn muốn nhận quà '" + reward_name + "'");
                $.blockUI({css: {width: "65% ", left: '20% '}, message: $('#questioninfo')});
            }

            function thamgia() {
                $(".loading").show();
                $.ajax({
                    url: "/mgh2/event/keobuabao/thamgia",
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                        .done(function (data) {
                            $(".tab-button").removeClass("active");
                            $(".tab-button1").addClass("active");
                            $('#team_list').html(data);
                            $(".loading").hide();

                        }).fail(function (data) {
                    $('#team_list').html('Không thể tham gia, vui lòng thử lại sau.');
                    $(".loading").hide();
                });
            }

            function thachdau() {
                $(".loading").show();
                $.ajax({
                    url: "/mgh2/event/keobuabao/thachdau?moccuoc_group=" + $("#moccuoc_group").val(),
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                        .done(function (data) {
                            $(".tab-button").removeClass("active");
                            $(".tab-button2").addClass("active");
                            $('#team_list').html(data);
                            $(".loading").hide();

                        }).fail(function (data) {
                    $('#team_list').html('Không thể thách đấu, vui lòng thử lại sau.');
                    $(".loading").hide();
                });
            }

            function lichsu_join() {
                $(".loading").show();
                $.ajax({
                    url: "/mgh2/event/keobuabao/lichsu_join",
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                        .done(function (data) {
                            $(".tab-button").removeClass("active");
                            $(".tab-button3").addClass("active");
                            $('#history-content').html(data);
                            $(".loading").hide();

                        }).fail(function (data) {
                    $('#history-content').html('Không thể lấy được lịch sử, vui lòng thử lại sau.');
                    $(".loading").hide();
                });
            }
            
            function lichsu_play() {
                $(".loading").show();
                $.ajax({
                    url: "/mgh2/event/keobuabao/lichsu_play",
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                        .done(function (data) {
                            $(".tab-button").removeClass("active");
                            $(".tab-button4").addClass("active");
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
                $(".content").hide();
                var tab = $(e).attr("rel");
                $(".menu-item").removeClass('active');
                $(e).addClass("active");
                $("#" + tab).show();
            }
        </script>
    </head>

    <body>
        <div class="wrapper">

            <div class="wrap">
                <div class="header">
                    <a href="javascript:void(0);" rel="the-le" class="menu-item menu-item-item1 active"
                       onclick="showcontent(this)">Thể lệ</a>             
                    <a href="javascript:void(0);" rel="tham-gia" class="menu-item menu-item-item2"
                       onclick="showcontent(this);
                               thamgia();">Tham Gia</a> 
                    <a href="javascript:void(0);" rel="lich-su" class="menu-item menu-item-item3"
                       onclick="showcontent(this);
                               lichsu_join();">Lịch Sử</a>
                </div>
                <div id="content">
                    <div class="children">
                        <div style="margin: 0 auto; font-size: 15px">
                            <div id="the-le" class="content">
                                <div id="my_content" style="text-align: justify; width: 95%; margin: auto; margin-top: 10px;"></div>
                            </div>

                            <div id="tham-gia" class="content" style="display: none; text-align: center;">    
                                <a href="javascript:void(0);" class="tab-button tab-button1 active" onclick="thamgia();">Đặt Cược</a>                          
                                <a href="javascript:void(0);" class="tab-button tab-button2" onclick="thachdau();">Thách Đấu</a>
                                <div id="team_list"></div>
                            </div>

                            <div id="lich-su" class="content" style="display: none; text-align: center;">   
                                <a href="javascript:void(0);" class="tab-button tab-button3 active" onclick="lichsu_join();">Đặt Cược</a>                          
                                <a href="javascript:void(0);" class="tab-button tab-button4" onclick="lichsu_play();">Thách Đấu</a>
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
            <img src="/mgh2/assets_dev/events/keobuabao/images/loading.gif" />
        </div>        
        <div id="questioninfo_play" style="display: none; cursor: default">
            <h3 id="mess_quest_play" style="margin: 15px; font-size: 13px;"></h3>
            <table style="width: 288px; padding: 4px;margin-top: 10px;" cellpadding="4" cellspacing="0"><tbody><tr>
                        <td><img src="/mgh2/assets_dev/events/keobuabao/images/bao.png"></td>
                        <td><img src="/mgh2/assets_dev/events/keobuabao/images/bua.png"></td> 
                        <td><img src="/mgh2/assets_dev/events/keobuabao/images/keo.png"></td>
                    </tr><tr>
                        <td><input type="radio" name="imgsel_play" value="bao"></td>
                        <td><input type="radio" name="imgsel_play" value="bua" checked="checked"></td>
                        <td><input type="radio" name="imgsel_play" value="keo"></td>
                    </tr></tbody></table>
            <div class='controlnumber'>
            </div>
            <div class="controlbutton">
                <input type="button" id="yes_pet_play" value="Đấu" />
                <input type="button" id="no_play" class="btn-close" value="Bỏ" />

                <div class="checknumber"></div>
            </div>
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





