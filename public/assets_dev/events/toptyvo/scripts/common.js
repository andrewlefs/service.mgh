function binding_history() {
    $.ajax({
        url: window.location.origin + "/event/huanguyen/history",
        type: "GET",
        contentType: 'application/json; charset=utf-8'
    })
        .done(function (data) {
            //console.log(data);
            $('#history').html(data);
        }).fail(function (data) {
            $('#history').html('Hiện tại không thể lấy được dữ liệu lịch sữ vui lòng thử lại sau.');
        });
}
function binding_doi_qua() {
    $.ajax({
        url: window.location.origin + "/event/huanguyen/buyitemCorrelate",
        type: "GET",
    })
        .done(function (data) {
            $('#doiqua').html(data);
        }).fail(function (data) {
            $('#doiqua').html('Hiện tại không thể lấy được dữ liệu lịch sữ vui lòng thử lại sau.');
        });
}
function binding_add_bout() {
    $.ajax({
        url: window.location.origin + "/event/huanguyen/bout",
        type: "POST",
        contentType: 'application/json; charset=utf-8'
    })
        .done(function (data) {
            //console.log(data);
            $('#bout').html(data);
        }).fail(function (data) {
            //console.log(data);
            $('#bout').html('Hiện tại không thể lấy được dữ liệu lịch sữ vui lòng thử lại sau.');
        });
}
function binding_adhere() {
    $.ajax({
        url: window.location.origin + "/event/huanguyen/openCorrelate",
        type: "POST",
        contentType: 'application/json; charset=utf-8'
    })
        .done(function (data) {
            //console.log(data);
            $('#adhere').html(data);
        }).fail(function (data) {
            $('#adhere').html('Hiện tại không thể lấy được dữ liệu lịch sữ vui lòng thử lại sau.');
        });
}
function submitcode(e) {
    $("#ralt").text("");
    $('#ralt').removeClass("success");
    $(e).addClass("disabled");
    var code = $("#rcode").val();
    if (code == "") {
        $("#ralt").text("Bạn vui lòng nhập code cần nhận");
        $(e).removeClass("disabled");
        return;
    }
    $.ajax({
        type: "POST",
        url: event_url + "receiveGiftcode",
        data: {"giftcode": code},
        dataType: 'json'
    })
        .done(function (data) {
            if (data.code == 0) {
                $('#ralt').addClass("success");
                $('.invite').html('');
                $.each(data.infoinvite, function (index, val) {
                    html = "<span>" + val["char_name_from"] + "</span><br/>";
                });
                $('.invite').html(html);
            }

            console.log(data.infoinvite);
            $(e).removeClass("disabled");
            $('#ralt').text(data.message);
        }).fail(function (data) {
            $(e).removeClass("disabled");
            $('#ralt').text('Hiện tại hệ thống quá tải vui lòng thử lại sau.');
        });
}
function checkValueNumber(opa) {
    getvaluecurrent = opa;
    if (validText(getvaluecurrent) == false) {
        console.log('Thất bại[0]..!');
        //$('.controlbutton .checknumber').html("<br/><div class='checknumber'>Số lượng không hợp lệ</div>");
        return false;
    }
    if (isNaN(parseInt(getvaluecurrent, 10)) || parseInt(Number(getvaluecurrent)) <= 0 || getvaluecurrent != parseInt(Number(getvaluecurrent))) {
        console.log('Thất bại[03]..!');
        //$('.controlbutton .checknumber').html("<br/><div class='checknumber'>Số lượng không hợp lệ</div>");
        return false;
    }
    return true;
}
function validText(value) {
    var chaos = new Array(" ", "'", ".", "~", "@", "#", "$", "%", "^", "&", "*", ";", "/", "\\", "|");
    var sum = chaos.length;
    for (var i in chaos) {
        if (!Array.prototype[i]) {
            sum += value.lastIndexOf(chaos[i])
        }
    }
    if (sum) {
        return false;
    }
    return true;
}
function random_award(e) {
    $('.infohistory').hide();
    $(e).addClass("disabled");
    getvalue = $('.ipn_quantity').val();

    //console.log(checkValueNumber(getvalue));

    if (checkValueNumber(getvalue) == false) {
        show_dialog('Số lượng không phù hợp', 1);
        $(e).removeClass("disabled");
        return;
    }
    var ctimes = $("#ctimes").val();
    var times = $('#adhere .ipn_quantity').val();
    var ctimes_free = $("#head #atimes").text();
    var ctimes_pri = $("#head #atimes_pri").text();
    if (times > ctimes_free && times > ctimes_pri) {
        show_dialog('Bạn không có đủ lượt mở quà!', 1);
        return;
    }
    $.ajax({
        type: "POST",
        url: event_url + "random_award?data=" + '{"times":"' + times + '","sign":"<?php echo $this->sign ?>"}',
        dataType: 'json',
        contentType: 'application/json; charset=utf-8'
    })
        .done(function (data) {
            //console.log(data);
            if (data.code == 0) {
                $("#atimes").text(data.data.times_free);
                $("#atimes_pri").text(data.data.times_pri);

                if (data.luckyposition.length == 3) {

                    Play("#slot1", 10, 25, 1, 8000, posArr[data.luckyposition[0]]);
                    Play("#slot2", 10, 40, 1, 7000, posArr[data.luckyposition[1]]);
                    Play("#slot3", 10, 55, 1, 6000, posArr[data.luckyposition[2]]);


                    setTimeout(function () {
                        $("#list-award").html(data.data.html);
                        $('.infohistory').show();
                        $(e).removeClass("disabled");
                    }, 8000);


                }
            } else {
                show_dialog(data.message, 1);
                $(e).removeClass("disabled");
            }

            // show_dialog(data.message, 1);
        }).fail(function (data) {
            $(e).removeClass("disabled");
            show_dialog('Hiện tại hệ thống quá tải vui lòng thử lại sau.', 1);
        });
}

function receive_times(e, lv) {
    $("#reg-msg").text("");
    $('#reg-msg').removeClass("success");
    var code = $("#rcode").val();
    if (code == "") {
        $("#ralt").text("Bạn vui lòng nhập code cần nhận");
        return;
    }
    $.ajax({
        type: "POST",
        url: event_url + "receive_times?data=" + '{"level":"' + lv + '","sign":"<?php echo $this->sign ?>"}',
        dataType: 'json',
        contentType: 'application/json; charset=utf-8'
    })
        .done(function (data) {
            ////console.log(data);
            if (data.code == 0) {
                $(e).hide();
                $("#atimes").text(data.data.times);
                $("#ctimes").val(data.data.times);
                $('#reg-msg').addClass("success");
            }
            $('#reg-msg').text(data.message);
        }).fail(function (data) {
            ////console.log(data);
            $('#reg-msg').text('Hiện tại hệ thống quá tải vui lòng thử lại sau.');
        });
}
function showcontent(e) {
    $(".content").hide();
    var tab = $(e).attr("rel");
    if (tab != "receive-code" && tab != "donate-code")
        $(".menu-item").removeClass('active');
    if (tab == "receive-code" || tab == "donate-code" || tab == "nhan-luot")
        $(".nhanluot").addClass("active");
    else
        $(e).addClass("active");
    $("#" + tab).show();
}
function hidePopup() {
    $("#alertmsg").hide();
}
function hideNotication() {
    if ($("#hideNoti").attr("val") == "0") {
        hidePopup();
        $("#notications").show();
    } else {
        hidePopup();
    }
}
function show_dialog(msg, t) {
    $("#hideNoti").attr("val", t);
    $("#alertmsg").hide();
    $(".notications").hide();
    $("#alert_msg").html(msg);
    $("#alertmsg").show();
}

$(function () {
    $("#cancel").hide();
    /*$(".notications").click(function(){
     $(this).hide();
     });*/
    var css = $(".table-one").removeClass("table-one");
    $(".loading").hide();
    css.addClass("table-role");
    $("#agree").click(function () {
        agree();
    });
    $("#right table").attr("style", "width:95%");
    //console.log("test");
    $('#my_content img').error(function () {
        $(this).unbind('error').attr('src', 'http://3t.mobo.vn/' + $(this).attr('src'));
    });
});
