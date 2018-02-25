$(document).ready(function() {
    var $w = $(window).width();
    if ($w < 700)
        $('body').css({'zoom': $w / 700});
    $('#menu-left li div div').click(function() {
        var rel = $(this).attr('rel');
        $('#menu-left li div div.active').removeClass('active');
        $(this).addClass('active');
        switch (rel) {
            case 'the-le':
                viewPageTheLe();
                break;
            case 'phan-thuong':
                viewPagePhanThuong();
                break;
            case 'tham-gia':
                viewPageThamGia();
                break;
            case 'lich-su':
                viewPageLichSu();
                break;
            case 'giftcode':
                viewPageGiftcode();
                break;
        }
    });
    viewPageThamGia();
    get_history();
    execute();
});

function viewPageTheLe() {
    $('.modal').hide();
    $('.content-detail').hide();
    $('#page-the-le').show();
}

function viewPagePhanThuong() {
    $('.modal').hide();
    $('.content-detail').hide();
    $('#page-phan-thuong').show();
}

function viewPageThamGia() {
    $('.content-detail').hide();
    $('.modal').show();
}

function viewPageLichSu() {
    $('.modal').hide();
    $('.content-detail').hide();
    $('#page-lich-su').show();
}

function viewPageGiftcode() {
    $('.modal').hide();
    $('.content-detail').hide();
    $('#page-giftcode').show();
}

function execute() {
    //-- Xử lý khi bấm vào hộp quá ---------------------------------------------
    var type = '';
    $('.gift div a').click(function(e) {
        e.preventDefault();
        type = $(this).attr('rel');
        var message = '';
        switch (type) {
            case 'dong':
                message = 'Dùng 1 khóa đồng để mở rương này ?';
                break;
            case 'bac':
                message = 'Dùng 1 khóa bạc để mở rương này ?';
                break;
            case 'bach-ngan':
                message = 'Dùng 3 khóa bạc để mở rương này ?';
                break;
            case 'vang':
                message = 'Dùng 1 khóa vàng để mở rương này ?';
                break;
        }
        $('#question').find('h3').text(message);
        $.blockUI({message: $('#question')});
    });

    $('#yes').click(function() {
        console.log('Wheeling .... ');
        $.blockUI({message: "<h1>Vui lòng chờ trong giây lát</h1>"});
        $.ajax({
            url: "/social/wheel",
            type: "post",
            data: {
                type: type
            },
            success: function(data) {
                var obj = JSON.parse(data);
                console.log(obj);
                $.unblockUI();
                //if(obj.code == 0){
                $.blockUI({message: "<h1>" + obj.message + "</h1><br><input type=\"button\" id=\"no2\" value=\"Đóng\" /> "});
                //}
                if (obj.code == 0) {
                    //-- call ajax update key ----------------------------------
                    $.ajax({
                        url: "/social/get_ajax_key",
                        type: "post",
                        success: function(data) {
                            var obj = JSON.parse(data);
                            $('.dong-key').text(obj.dong);
                            $('.bac-key').text(obj.bac);
                            $('.vang-key').text(obj.vang);
                        }
                    });
                    //-- call ajax get history ---------------------------------
                    get_history();
                }


                $('.blockOverlay').click($.unblockUI);
                $('#no2').click(function() {
                    $.unblockUI();
                    return false;
                });
            },
            error: function(data) {
                console.log(data);
                $.unblockUI();
            }
        });
    });

    //-- Xử lý khi bấm vào mua khóa --------------------------------------------
    var typekey = '';
    $('.gift div.buy-key').click(function(e) {
        e.preventDefault();
        typekey = $(this).attr('rel');

        switch (typekey) {
            case 'bac':
                $.blockUI({message: "<h1>Bạn đồng ý dùng 1.000.000 Bạc để mua 1 Khóa Bạc không?</h1><br><input type=\"button\" id=\"yes2\" value=\"Có\" /><input type=\"button\" class=\"btn-close\" value=\"Không\" /> "});
                break;
            case 'vang':
                $.blockUI({message: "<h1>Bạn đồng ý dùng 100 Vàng để mua 1 Khóa Vàng không?</h1><br><input type=\"button\" id=\"yes2\" value=\"Có\" /><input type=\"button\" class=\"btn-close\" value=\"Không\" /> "});
                break;
        }


        $('#yes2').click(function() {
            console.log('Buy key .... ');
            $.ajax({
                url: "/social/buy_key",
                type: "post",
                data: {
                    type: typekey
                },
                success: function(data) {
                    var obj = JSON.parse(data);
                    console.log(obj);
                    $.unblockUI();
                    $.blockUI({message: "<h1>" + obj.message + "</h1><br><input type=\"button\" class=\"btn-close\" id=\"no2\" value=\"Đóng\" /> "});
                    $('.blockOverlay').click($.unblockUI);
                    $('#no2').click(function() {
                        $.unblockUI();
                        return false;
                    });
                    //-- call ajax update key ----------------------------------
                    $.ajax({
                        url: "/social/get_ajax_key",
                        type: "post",
                        success: function(data) {
                            var obj = JSON.parse(data);
                            $('.dong-key').text(obj.dong);
                            $('.bac-key').text(obj.bac);
                            $('.vang-key').text(obj.vang);
                        }
                    });

                },
                error: function(data) {
                    console.log(data);
                    $.unblockUI();
                }
            });
        });


        $('.btn-close').click(function() {
            $.unblockUI();
            return false;
        });
    });




    $('.btn-close').click(function() {
        $.unblockUI();
        return false;
    });

    jQuery('#page-giftcode .use-code').click(function() {
        jQuery.blockUI({message: "Vui lòng đợi...."});
        jQuery.ajax({
            url: "/social/use_giftcode",
            type: "post",
            data: {
                giftcode: jQuery('input[name=giftcode]').val(),
                server_id: jQuery('input[name=server_id]').val(),
                character_id: jQuery('input[name=character_id]').val(),
            },
            success: function(response) {
                jQuery.blockUI({message: response, onOverlayClick: $.unblockUI });
                return false;
            }
        });
    });
}

function get_history() {
    $.ajax({
        url: "/social/get_history",
        type: "post",
        success: function(data) {
            var obj = JSON.parse(data);
            if (obj.code == 0) {
                $row = '';
                $('#tb-history .con').remove();
                for (var i = 0; i < obj.data.length; i++) {
                    $row += '<tr class="con"><td>' + (i + 1) + '</td><td>' + obj.data[i]._itemname + '</td><td style="text-transform:uppercase">' + obj.data[i]._giftcode + '</td><td>' + obj.data[i]._insert + '</td></tr>';
                }
                $('#tb-history').append($row);
            }
        }
    });
}

