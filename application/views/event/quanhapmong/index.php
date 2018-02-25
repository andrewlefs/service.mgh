<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-touch-fullscreen" content="yes">
        <meta http-equiv="Cache-control" content="no-cache">
        <link rel="stylesheet" href="/mgh2/assets_dev/events/quanhapmong/css/style.css">
        <script type="text/javascript" src="/mgh2/assets_dev/events/quanhapmong/scripts/jquery.min.js"></script>
        <script type="text/javascript" src="/mgh2/assets_dev/events/quanhapmong/scripts/jquery.blockUI.js"></script>
        <script src="/mgh2/assets_dev/events/quanhapmong/scripts/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
        <script src="/mgh2/assets_dev/events/quanhapmong/scripts/jquery.spritely.js" type="text/javascript"></script>
        <script src="/mgh2/assets_dev/events/quanhapmong/scripts/play.js" type="text/javascript"></script>
        <script src="/mgh2/assets_dev/events/quanhapmong/scripts/main.js"></script>        
        <script src="/js/mp.js"></script>
        <title><?php echo $title; ?> </title>
        <script>
            var action = "";
            var id;
            var premiership = false;
            var topllserver = false;
            var quantity;
            var calendar_id;
            var gift_pakage_id;

            $(function () {
                $("#gifttype").change(function () {
                    exchange_gift();
                });

                $('.btn-close').click(function () {
                    $.unblockUI();
                    return false;
                });

                $('#yes_pet').click(function () {
                    $.unblockUI();

                    if (action == "gift") {
                        $(".loading").show();
                        $.ajax({
                            method: "GET",
                            url: "/mgh2/event/quanhapmong/exchange_gift_by_shop/?id=" + id + "&quantity=" + quantity
                            ,
                            contentType: 'application/json; charset=utf-8',
                            success: function (data) {
                                json_data = $.parseJSON(data);
                                if (json_data.code != null && json_data.code != "0") {
                                    show_message(json_data.message);
                                    $(".loading").hide();
                                }
                                else {
                                    exchange_gift();
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
                    
                    if (action == "giftpakage") {
                        $(".loading").show();
                        $.ajax({
                            method: "GET",
                            url: "/mgh2/event/quanhapmong/exchange_gift_pakage_by_shop/?id=" + id
                            ,
                            contentType: 'application/json; charset=utf-8',
                            success: function (data) {
                                json_data = $.parseJSON(data);
                                if (json_data.code != null && json_data.code != "0") {
                                    show_message(json_data.message);
                                    $(".loading").hide();
                                }
                                else {
                                    exchange_gift();
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
                    
                    if (action == "giftpakages") {
                        $(".loading").show();
                        var day_count = $("#day_count_"+id).val();
                        $.ajax({
                            method: "GET",
                            url: "/mgh2/event/quanhapmong/exchange_gift_pakage_special_by_shop/?id=" + id + "&day_count=" + day_count
                            ,
                            contentType: 'application/json; charset=utf-8',
                            success: function (data) {
                                json_data = $.parseJSON(data);
                                if (json_data.code != null && json_data.code != "0") {
                                    show_message(json_data.message);
                                    $(".loading").hide();
                                }
                                else {
                                    exchange_gift();
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

                    if (action == "giftoutgame") {
                        $(".loading").show();
                        $.ajax({
                            method: "GET",
                            url: "/mgh2/event/quanhapmong/exchange_gift_by_outgame/?id=" + id
                            ,
                            contentType: 'application/json; charset=utf-8',
                            success: function (data) {
                                json_data = $.parseJSON(data);
                                if (json_data.code != null && json_data.code != "0") {
                                    show_message(json_data.message);
                                    $(".loading").hide();
                                }
                                else {
                                    exchange_gift_outgame();
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

                    if (action == "ex_card") {
                        $(".loading").show();
                        $.ajax({
                            method: "GET",
                            url: "/mgh2/event/quanhapmong/card_exchange/?value=" + $("#cardvalue").val() + "&type=" + $("#cardtype_ex").val()
                            ,
                            contentType: 'application/json; charset=utf-8',
                            success: function (data) {
                                json_data = $.parseJSON(data);
                                if (json_data.code != null && json_data.code != "0") {
                                    show_message(json_data.message);
                                    $(".loading").hide();
                                }
                                else {
                                    exchange_gate_card();
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
                    
                    if (action == "transfer_np") {
                        $(".loading").show();
                        $.ajax({
                            method: "GET",
                            url: "/mgh2/event/quanhapmong/transfer_np_process/?value=" + $("#nganphieu_trans").val() + "&to_mobo_id=" + $("#mobo_id_trans").val(),
                            contentType: 'application/json; charset=utf-8',
                            success: function (data) {
                                json_data = $.parseJSON(data);
                                if (json_data.code != null && json_data.code != "0") {
                                    show_message(json_data.message);
                                    $(".loading").hide();
                                }
                                else {
                                    transfer_np();
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
                    
                    if (action == "gift_receive_process") {                      
                        $(".loading").show();
                        $.ajax({
                            method: "GET",
                            url: "/mgh2/event/quanhapmong/gift_receive_process/?calendar_id=" + calendar_id
                            ,
                            contentType: 'application/json; charset=utf-8',
                            success: function (data) {
                                json_data = $.parseJSON(data);
                                if (json_data.code != null && json_data.code != "0") {
                                    show_message(json_data.message);
                                    $(".loading").hide();
                                }
                                else {
                                    gift_receive();
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

            $(document).ready(function () {  //Load tournament list
                //Load Gift Type
                $.ajax({
                    method: "GET",
                    url: "/mgh2/event/quanhapmong/gift_type_list",
                    contentType: 'application/json; charset=utf-8',
                    success: function (data) {
                        var obj = JSON.parse(data);
                        var tourlist = '';
                        $.each(obj, function (key, value) {
                            tourlist += '<option value="' + value["id"] + '" >' + value["type_name"] + '</option>';
                        });
                        $("#gifttype").html(tourlist);

                        //Load Shop Index
                        //exchange_gift();
                        thele();
                    },
                    error: function (data) {
                        var obj = $.parseJSON(data);
                    }
                });
            });

            function gift_exchange(s_id, reward_name) {
                id = s_id;
                action = "gift_join_arena";
                $("#mess_quest").html("Bạn có chắc chắn muốn đổi quà '" + reward_name + "'");
                $.blockUI({css: {width: "65% ", left: '20% '}, message: $('#questioninfo')});
            }

            function napthe() {
                $(".loading").show();

                $.ajax({
                    url: "/mgh2/event/quanhapmong/napthe_shop",
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                        .done(function (data) {
                            $('#napthe-content').html(data);
                            //$(".menu-item").removeClass('active');
                            //$(".menu-item-item3").addClass('active');
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
                    mp.sendRequest(POST, Urls.AJAX_URL + "/quanhapmong_charging", data, function (response) {
                        //mp.sendRequest(POST, "/mgh2/event/quanhapmong/quanhapmong_charging", data, function (response) {    
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

            function get_exchange_history() {
                $(".loading").show();            
                $.ajax({
                    url: "/mgh2/event/quanhapmong/get_exchange_history",
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                        .done(function (data) {
                            $(".tab-button").removeClass("active");
                            $(".tab-button3").addClass("active");
                            $('#history-content').html(data);
                            $(".loading").hide();

                        }).fail(function (data) {
                    $('#history-content').html('Không thể lấy được lịch sử đổi quà, vui lòng thử lại sau.');
                    $(".loading").hide();
                });
            }

            function card_exchange_history() {
                $(".loading").show();
                var tournament_id = $("#tournament_id_2").val();
                $.ajax({
                    url: "/mgh2/event/quanhapmong/card_exchange_history?id=" + tournament_id,
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                        .done(function (data) {
                            $(".tab-button").removeClass("active");
                            $(".tab-button4").addClass("active");
                            $('#history-content').html(data);
                            $(".loading").hide();

                        }).fail(function (data) {
                    $('#history-content').html('Không thể lấy được lịch sử đổi quà, vui lòng thử lại sau.');
                    $(".loading").hide();
                });
            }

            function get_charging_history() {
                $(".loading").show();
                $.ajax({
                    url: "/mgh2/event/quanhapmong/get_charging_history",
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                        .done(function (data) {
                            $(".tab-button").removeClass("active");
                            $(".tab-button2").addClass("active");
                            $('#history-content').html(data);
                            $(".loading").hide();

                        }).fail(function (data) {
                    $('#history-content').html('Không thể lấy được lịch sử nạp thẻ, vui lòng thử lại sau.');
                    $(".loading").hide();
                });
            }

            function lichsu() {
                //get_charging_history();
                get_exchange_history();
                $(".tab-button").removeClass("active");
                $(".tab-button2").addClass("active");
            }

            function exchange_gift_by(s_id, s_gift_name, s_gift_price) {
                id = s_id;
                gift_name = s_gift_name;
                gift_price = s_gift_price;

                action = "gift";
                quantity = $("#quantity_" + s_id).val();
                if (quantity == "" || quantity == "Số lượng...") {
                    show_message("Bạn chưa nhập số lượng cần mua");
                }
                else {
                    if (!allnumeric(quantity)) {
                        show_message('Số lượng cần mua phải là số nguyên');
                    }
                    else
                    if (quantity < 0 || quantity == 0) {
                        show_message("Số lượng cần mua phải lớn hơn 0");
                    }
                    else {
                        $("#mess_quest").html("Bạn có chắc chắn muốn đổi '" + quantity + " " + s_gift_name + "' với giá '" + (quantity * s_gift_price) + "' Cống Hiến ?");
                        $.blockUI({css: {width: "65% ", left: '20% '}, message: $('#questioninfo')});
                    }
                }
            }
            
            function exchange_gift_pakage_by(s_id, s_gift_name, s_gift_price) {
                id = s_id;
                gift_name = s_gift_name;
                gift_price = s_gift_price;

                action = "giftpakage";
                $("#mess_quest").html("Bạn có chắc chắn muốn đổi '" + s_gift_name + "' với giá '" + s_gift_price + "' Cống Hiến ?");
                $.blockUI({css: {width: "65% ", left: '20% '}, message: $('#questioninfo')});
            }
            
            function exchange_gift_pakage_special_by(s_id, s_gift_name, s_gift_price) {
                id = s_id;
                gift_name = s_gift_name;
                gift_price = s_gift_price;

                action = "giftpakages";
                $("#mess_quest").html("Bạn có chắc chắn muốn đổi '" + s_gift_name + "' - [" + $("#day_count_"+s_id).val() + " Ngày] với giá '" + ($("#day_count_"+s_id).val() * s_gift_price) + "' Điểm Giang Hồ ?");
                $.blockUI({css: {width: "65% ", left: '20% '}, message: $('#questioninfo')});
            }

            function exchange_gift_by_outgame(s_id, s_gift_name, s_gift_price) {
                id = s_id;
                gift_name = s_gift_name;
                gift_price = s_gift_price;

                action = "giftoutgame";
                $("#mess_quest").html("Bạn có chắc chắn muốn đổi quà '" + s_gift_name + "' với giá '" + s_gift_price + "' Cống Hiến ?");
                $.blockUI({css: {width: "65% ", left: '20% '}, message: $('#questioninfo')});
            }
            
            function gift_receive_process(s_calendar_id) {
                calendar_id = s_calendar_id;              
                action = "gift_receive_process";
                $("#mess_quest").html("Bạn có chắc chắn muốn nhận quà");
                $.blockUI({css: {width: "65% ", left: '20% '}, message: $('#questioninfo')});
            }

            function exchange_gift() {
                $(".loading").show();
                $.ajax({
                    url: "/mgh2/event/quanhapmong/exchange_gift_shop?id=" + $("#gifttype").val(),
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                        .done(function (data) {
                            //console.log(data);
//                            $(".menu-item").removeClass('active');
//                            $(".menu-item-item2").addClass('active');
                            $('#exchange-gift').html(data);
                            $(".loading").hide();

                        }).fail(function (data) {
                    $('#exchange-gift').html('Không thể đổi quà, vui lòng thử lại sau.');
                    $(".loading").hide();
                });
            }
            
            function gift_receive() {
                $(".loading").show();
                $.ajax({
                    url: "/mgh2/event/quanhapmong/gift_receive",
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                        .done(function (data) {
                            $('#receive_gift').html(data);
                            $(".loading").hide();

                        }).fail(function (data) {
                    $('#receive_gift').html('Không thể đổi quà, vui lòng thử lại sau.');
                    $(".loading").hide();
                });
            }

            function exchange_gift_outgame() {
                $(".loading").show();
                $.ajax({
                    url: "/mgh2/event/quanhapmong/exchange_gift_outgame_shop",
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                        .done(function (data) {
                            //console.log(data);
                            $('#exchange-gift-outgame').html(data);
                            $(".loading").hide();

                        }).fail(function (data) {
                    $('#exchange-gift-outgame').html('Không thể đổi quà, vui lòng thử lại sau.');
                    $(".loading").hide();
                });
            }

            //exchange_gate_card
            function exchange_gate_card() {
                $(".loading").show();
                $.ajax({
                    url: "/mgh2/event/quanhapmong/exchange_gate_card_shop",
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                        .done(function (data) {
                            //console.log(data);
                            $('#exchange-card-gate').html(data);
                            $(".loading").hide();

                        }).fail(function (data) {
                    $('#exchange-card-gate').html('Không thể đổi quà, vui lòng thử lại sau.');
                    $(".loading").hide();
                });
            }

            function exchange_card() {
                if ($("#cardtype_ex").val() == "") {
                    show_message("Bạn chưa chọn loại thẻ", 0);
                }
                else if ($("#cardvalue").val() == "") {
                    show_message("Bạn chưa chọn mệnh giá thẻ", 0);
                }
                else {
                    action = "ex_card";
                    $("#mess_quest").html("Bạn có chắc chắn muốn đổi thẻ mệnh giá '" + $("#cardvalue").val() + "'?");
                    $.blockUI({css: {width: "65% ", left: '20% '}, message: $('#questioninfo')});
                }
            }
            
            function thele() {
            $(".loading").show();
            $.ajax({
                url: "/mgh2/event/quanhapmong/content_news?id=839",
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
        
        function transfer_np() {
                $(".loading").show();

                $(".tab-button").removeClass('active');
                $(".tab-button6").addClass('active');

                $.ajax({
                    url: "/mgh2/event/quanhapmong/transfer_np",
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                        .done(function (data) {
                            $('#napthe-content').html(data);
                            $(".loading").hide();

                        }).fail(function (data) {
                    $('#napthe-content').html('Không thể chuyển Cống Hiến lúc này, vui lòng thử lại sau.');
                    $(".loading").hide();
                });
            }
            
            function transfer_np_process() {
                action = "transfer_np";
                quantity = $("#nganphieu_trans").val();
                if (quantity == "") {
                    show_message("Bạn chưa nhập số lượng Cống Hiến cần chuyển");
                }
                else {
                    if (!allnumeric(quantity)) {
                        show_message('Số lượng Cống Hiến cần chuyển phải là số nguyên');
                    }
                    else
                    if (quantity < 0 || quantity == 0) {
                        show_message("Số lượng Cống Hiến cần chuyển phải lớn hơn 0");
                    }
                    else {
                        if ($("#mobo_id_trans").val() == "") {
                            show_message("Bạn chưa nhập Mobo ID nhận Cống Hiến");
                        }
                        else
                        if (!allnumeric($("#mobo_id_trans").val())) {
                            show_message('Mobo ID nhận Cống Hiến phải là số nguyên');
                        }
                        else
                        {
                            $("#mess_quest").html("Bạn có chắc chắn muốn chuyển '" + quantity + " Cống Hiến' đến tài khoản Mobo Id '" + $("#mobo_id_trans").val() + "' ?");
                            $.blockUI({css: {width: "65% ", left: '20% '}, message: $('#questioninfo')});
                        }
                    }
                }
            }
            
            function get_exchange_history2() {
                $(".loading").show();
                $.ajax({
                    url: "/mgh2/event/quanhapmong/get_exchange_history2",
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

            function allnumeric(inputtxt)
            {
                var numbers = /^[0-9]+$/;
                if (inputtxt.match(numbers) != null)
                {
                    return true;
                }
                else
                {
                    return false;
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
                    <a href="javascript:void(0);" rel="qua-game" class="menu-item menu-item-item2"
                       onclick="showcontent(this);exchange_gift();">Tham Gia</a>   
                    <a href="javascript:void(0);" rel="nhan-qua" class="menu-item menu-item-item3"
                       onclick="showcontent(this);gift_receive();">Nhận Quà</a> 
                    <a href="javascript:void(0);" rel="lich-su" class="menu-item menu-item-item4"
                                        onclick="showcontent(this);lichsu();">Lịch sử</a>
                </div>
                <div id="content">
                    <div class="children">
                        <div style="margin: 0 auto; font-size: 15px">
                            <div id="the-le" class="content">
                            <div id="my_content" style="text-align: justify; width: 95%; margin: auto; margin-top: 10px;"></div>
                            </div>
                            
                            <div id="qua-game" class="content" style="display: none; text-align: center;"> 
                                <div style="text-align: center; margin-top: 10px;font-size: 12px;">*Chọn loại quà: <select id="gifttype" name="gifttype" class="span4 validate[required]"></select></div>
                                <div id="exchange-gift"></div>
                            </div>

                            <div id="the-gate" class="content" style="display: none; text-align: center;">                          
                                <div id="exchange-card-gate"></div>
                            </div>

                            <div id="shop-qua" class="content" style="display: none; text-align: center;">                          
                                <div id="exchange-gift-outgame"></div>
                            </div>

                            <div id="lich-su" class="content" style="display: none; text-align: center;">                                
                                <div id="history-content"></div>
                            </div>

                            <div id="nap-the" class="content" style="display: none;">
                                <div id="napthe-content" style="text-align: center; width: 100%; margin: auto; margin-top: 10px;"></div>
                            </div>
                            
                            <div id="nhan-qua" class="content" style="display: none; text-align: center;">                          
                                <div id="receive_gift"></div>
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
            <img src="/mgh2/assets_dev/events/quanhapmong/images/loading.gif" />
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





