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
        <title><?php echo $title; ?> </title>
        <script>
            var action = "";
            var id;
            var premiership = false;
            var topllserver = false;
            var quantity;

            $(function () {
                $('.btn-close').click(function () {
                    $.unblockUI();
                    return false;
                });

                $('#yes_pet').click(function () {                    
                    if (action == "gift") {
                        //Gift Exchange
                        //show_message('Gift Ok ID:' + id);
                        $(".loading").show();
                        setTimeout(function () {
                            $.ajax({
                                method: "GET",
                                url: "/mgh2/event/tulinhdan/gift_exchange/",
                                contentType: 'application/json; charset=utf-8',
                                success: function (data) {
                                    json_data = $.parseJSON(data);
                                    if (json_data.code != null && json_data.code != "0") {
                                        show_message(json_data.message);
                                        $(".loading").hide();
                                    }
                                    else {
                                        //Reload Point
                                        load_user_point();
                                        show_message(json_data.message);
                                        $(".loading").hide();
                                    }
                                },
                                error: function (data) {
                                    console.log(data);
                                    $(".loading").hide();
                                }
                            });
                        }, 3000);
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

                    if (action == "gift_shop") {
                        $.ajax({
                            method: "GET",
                            url: "/mgh2/event/tulinhdan/exchange_gift_by_shop/?id=" + id + "&quantity=" + quantity
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
                                    load_user_point();
                                    exchange_gift_shop();
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

            });

            function load_user_point() {
                $.ajax({
                    method: "GET",
                    url: "/mgh2/event/tulinhdan/load_user_point/",
                    contentType: 'application/json; charset=utf-8',
                    success: function (data) {
                        json_data = $.parseJSON(data);
                        if (json_data.code != null && json_data.code != "0") {
                            $("#user_point").html(json_data.message);
                        }
                        else {
                            //Reload Point 
                            $("#user_point").html(json_data.message);
                        }
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            }

            function gift_exchange() {
                action = "gift";
                show_message_confirm("Bạn có chắc chắn muốn đào kho báu ?");              
            }

            function thamgia() {
                $(".loading").show();
                $.ajax({
                    url: "/mgh2/event/tulinhdan/thamgia",
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                        .done(function (data) {
                            $('#tham-gia').html(data);
                            $("#piechart").show()
                            $(".loading").hide();

                        }).fail(function (data) {
                    $('#history-content').html('Không thể tham gia, vui lòng thử lại sau.');
                    $(".loading").hide();
                });
            }

            function exchange_gift_shop() {
                $(".loading").show();

                $.ajax({
                    url: "/mgh2/event/tulinhdan/exchange_gift_shop?id=1",
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                        .done(function (data) {
                            $('#lich-su').html(data);
                            $(".loading").hide();

                        }).fail(function (data) {
                    $('#history-content').html('<div style="margin-top: 50px;">Không thể vào Shop đổi quà, bạn vui lòng thử lại sau.</div>');
                    $(".loading").hide();
                });
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

            function gift_top_exchange(s_id) {
                id = s_id;
                action = "gifttop";
                $("#mess_quest").html("Bạn có chắc chắn muốn nhận quà Top");
                $.blockUI({css: {width: "65% ", left: '20% '}, message: $('#questioninfo')});
            }

            function exchange_gift_by(s_id, s_gift_name, s_gift_price) {
                id = s_id;
                gift_name = s_gift_name;
                gift_price = s_gift_price;

                action = "gift_shop";
                quantity = $("#quantity_" + s_id).val();
                if (quantity == "" || quantity == "SL...") {
                    show_message("Bạn chưa nhập số lượng cần đổi");
                }
                else {
                    if (!allnumeric(quantity)) {
                        show_message('Số lượng cần đổi phải là số nguyên');
                    }
                    else
                    if (quantity < 0 || quantity == 0) {
                        show_message("Số lượng cần đổi phải lớn hơn 0");
                    }
                    else {
                        show_message_confirm("Bạn có chắc chắn muốn đổi '" + quantity + " " + s_gift_name + "' với giá '" + (quantity * s_gift_price) + "' Điểm ?");                       
                    }
                }
            }

            function show_message(messgage) {
                $(".popup-message").html(messgage);
                $("#caution_button").show();
                $("#confirm_button").hide();
                $(".popup-overlay").fadeIn();
            }
            
            function show_message_confirm(messgage) {
                $(".popup-message").html(messgage);
                $("#confirm_button").show();
                $("#caution_button").hide();
                $(".popup-overlay").fadeIn();
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
            </div><br><style>
                a.title{ color: #0a2c5d;font-weight: 500; font-size: 20px; }
                @media (max-width: 768px) {
                    a.title{font-size: 15px; font-weight: bold;}
                }            
                #gift_item {display: inline-table; margin-top: 3px; margin-bottom: 3px;}               
                #gift_name {font-weight: bold;font-size: 13px;color: #E40A3C;}
                #gift_price {font-size: 13px;font-weight: bold;}
                .blockUI input[type="button"] {width: 100px; height: 35px;}
                .blockUI.blockPage {border: 1px solid #7A0107 !important; background-color: #FFF !important;padding: 10px !important;width: 288px !important;left: 50% !important;margin-left: -155px !important;}
                .blockUI.blockMsg.blockPage h1 {color: white;}
                .blockUI.blockPage h1 {font-size: 1.1em;}
                .header {text-align: center;}
                .loading {display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,.7); z-index: 1000; text-align: center; padding-top: 15%;}
                .modal-marker{position: fixed;top: 0;left: 0;right: 0;bottom: 0;background-color: rgba(0,0,0,.7);z-index: 1000;}            
                .modal-dialog{background-color: #ebebeb;z-index: 1001;position: relative;border-radius: 3px;-webkit-border-radius: 3px;-moz-border-radius: 3px;box-shadow: 0 0 10px #5a0303;-webkit-box-shadow: 0 0 10px #5a0303;-moz-box-shadow: 0 0 10px #5a0303;}
                .modal-header{border-bottom: 1px solid #5a0303;padding: 4px;line-height: 30px; background: #ebebeb; position: relative; z-index: 10;border-top-left-radius: 9px;-webkit-border-top-left-radius: 9px;-moz-border-top-left-radius: 9px;
                              border-top-right-radius: 9px;-webkit-border-top-right-radius: 9px;-moz-border-top-right-radius: 9px;}
                .modal-footer{border-top: 1px solid #5a0303;padding: 4px;line-height: 30px;text-align: center}
                .modal-content{text-align: center;padding: 30px;}
                @media (min-width: 320px){ul.tab-bar>li>a,ul.tab-bar>li>span{padding: 8px 20px}
                                          .popup,.modal-dialog{position: fixed;width: 280px;left: 50%;margin-left: -140px;background-color: #ebebeb;}}
                @media (min-width: 480px){ul.tab-bar>li>a,ul.tab-bar>li>span{padding: 8px 20px}.popup,.modal-dialog{position: fixed;width: 440px;left: 50%;margin-left: -220px;background-color: #ebebeb;}}
                @media (max-width: 319px){.popup,.modal-dialog{position: fixed;left:12px;right: 12px;}}
                @media (max-height: 319px){.popup,.modal-dialog{position: fixed;top:12px;bottom: 12px;}}
                @media (min-height: 320px){.popup,.modal-dialog{top: 50%;margin-top: -140px;min-height: 160px;background-color: #ebebeb;}}
            </style><div class="container">
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
                <div class="wrap">
                    <div id="content">
                        <div class="children">
                            <div style="margin: 0 auto; font-size: 15px">                            
                                <div id="tournament" style="margin: auto; width: 95%; font-size: 13px;">                                                                                         
                                    <div style="margin-top: 5px;width: 100%;margin: 0 auto;">
                                        <div id="tham-gia" class="content">
                                            <?php
                                            foreach ($tournament as $key => $value) {
                                                ?>
                                                <div style="font-size: 18px; font-weight: bold; color: #C20C0C; margin-top: 50px;">
                                                    <?php echo $value["tournament_name"]; ?>
                                                </div>
                                                <div>
                                                    <table style="width: 100%; border: 1px solid #F79646;padding: 4px;margin-top: 10px;" cellpadding="4" cellspacing="0">
                                                        <tr>
                                                            <td style="padding: 4px;">*Bắt đầu sự kiện:</td>
                                                            <td style="text-align: right; padding: 4px;"><span style="font-weight: bold;"><?php
                                                                    $date = new DateTime($value["tournament_date_start"]);
                                                                    echo $date->format('d-m-Y H:i:s');
                                                                    ?></span></td>               
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 4px;">*Kết thúc sự kiện: </td>
                                                            <td style="text-align: right; padding: 4px;"><span style="font-weight: bold;"><?php
                                                                    $date = new DateTime($value["tournament_date_end"]);
                                                                    echo $date->format('d-m-Y H:i:s');
                                                                    ?></span></td>               
                                                        </tr> 
                                                        <tr>
                                                            <td style="padding: 4px;">*Đào Rương tốn: </td>
                                                            <td style="text-align: right; padding: 4px;"><span style="font-weight: bold; color: #F27711;"><?php echo $value["tournament_money"]; ?></span> Ngân lượng</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            <?php } ?>

                                            <div style="margin-top: 10px;">
                                                *Điểm tích lũy của bạn: <span style="font-weight: bold; color: #1649E8;" id="user_point"><?php echo $user_point; ?></span> - <a href="javascript:void(0);" rel="lich-su" onclick="showcontent(this);
                                                        exchange_gift_shop();" class="btn btn-primary btn-sm">Đổi Quà</a>
                                            </div> 

<!--                                        <div style="text-align: center;margin-top: 145px; position: absolute;margin-left: 320px;"><a href="javascript:void(0);" class="pet-button" id="gift-button-<?php echo $value["id"]; ?>" onclick="gift_exchange();">Quay</a></div>       -->
<!--                                        <div id="loading_pie" style="position: absolute; z-index: 1; margin-top: -125px; margin-left: -25px; display: none;"><img src="/mgh2/assets_dev/events/tulinhdan/images/loading_game.gif" style="width: 345px;"></div>
                                        <div id="piechart" style="width: 650px; height: 500px; position: absolute; z-index: -1; margin-top: -70px;margin-left: -178px;"></div> -->
                                            <div class="h-content" style="text-align: center;">
                                                <div class="modaldoiqua">
                                                    <div id="gift_list" style="background-color: #FDE7CB; margin-top: 10px">
                                                        <?php
                                                        foreach ($reward_list as $key => $value) {
                                                            ?>
                                                            <div id="gift_item">                
                                                                <img id="gift_img" width="60px" heigh="60px" src="<?php echo $value["reward_img"]; ?>" />               
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <div style="margin-top: 5px; text-align: center; margin-bottom: 10px">        
                                                    <a href="javascript:void(0);" onclick="gift_exchange();" class="btn btn-success btn-lg" id="gift-button-<?php echo $value["id"]; ?>">Đào</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="lich-su" class="content" style="display: none; text-align: center;">                                       
                                            <div id="history-content"></div>                         
                                        </div>
                                    </div>
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

            <script type="text/javascript">
                $(function () {
                    $(".popup-overlay").click(function () {
                        $(".popup-overlay").fadeOut();
                    });
                });
            </script>
            <div id="popup" class="popup-overlay" style="display: none;">
                <div class="popup">
                    <div class="popup-title">Thông báo</div>
                    <div class="popup-content">
                        <div class="popup-message">                  
                        </div>
                    </div>
                    <div id="confirm_button" style="display: none; text-align: center; margin-bottom: 10px;"><button id="yes_pet" type="button" class="btn btn-success">Đồng ý</button> <button id="btn-close" type="button" class="btn btn-danger">Hủy Bỏ</button></div>
                    <div id="caution_button" style="display: none; text-align: center; margin-bottom: 10px;"><button id="btn-success" type="button" class="btn btn-success">Đồng ý</button></div>
                </div>
            </div>
    </body>
</html>






