function remove_unicode(str) {
    str = str.toLowerCase();
    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
    str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
    str = str.replace(/đ/g, "d");
    //str = str.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_/g, "-");
    //str = str.replace(/-+-/g, "-"); //thay thế 2- thành 1-
    //str = str.replace(/^\-+|\-+$/g, "");
    return str.toLowerCase();
}
function getQueryParams(qs) {
    qs = qs.split('+').join(' ');
    var params = {},
            tokens,
            re = /[?&]?([^=]+)=([^&]*)/g;

    while (tokens = re.exec(qs)) {
        params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
    }
    return params;
}

var call = function (func, data, type, callback) {
    var querystring = "/api?k=" + urlParams.k;
    if (type.toLowerCase() != "post") {
        querystring += "&func=" + func + "&type=" + type + "&data=" + JSON.stringify(data);
    }
    console.log(base_url + querystring);
    $.ajax({
        type: type,
        method: type,
        url: base_url + querystring,
        dataType: 'json',
        data: {func: func, data: data},
        success: function (data) {
            callback(data, "success");
        },
        error: function (data) {
            callback(data, "error");
        }
    });
}

var layout = '<span class=\'second-box\'><span class=\'time-font\'> {hnn}:{mnn}:{snn}s</span></span>';
function get_countdown(id, second) {

    var now = new Date();
    now.setSeconds(now.getSeconds() + second);
    $(id).show();
    $(id).countdown({
        until: now,
        // until: liftoffTime,
        //format: 'HMS',
        format: 'yODHMS',
        compact: true,
        layout: layout,
        description: '',
        onExpiry: function () {
            $("#btn-share").show();
            $(id).hide();
        }
    });
}
var invitable_friends = [];
var invitable_friend_tokens = [];
var urlParams;
$(function () {
    urlParams = getQueryParams(window.location.search);
    if (key == "")
        key = urlParams.k;

    var init = function () {
        //console.log("liked");
        call("DownTime", {cat: 'share'}, "get", function (data) {
            //console.log(data);
            if (data.code == 1001023) {
                $("#btn-share").hide();
                get_countdown("#share-countdown", data.data.waiting);
            }
        });
        //console.log("liked");
        call("DownTime", {cat: 'invite'}, "get", function (data) {
            //console.log(data);
            if (data.code == 1001023) {
                $("#btn-invite").hide();
                get_countdown("#invite-countdown", data.data.waiting);
            }
        });
    };
    init();

    $(".ls-award").click(function () {
        var e = this;
        $(".awars-item").fadeOut("fast");

        $(".img-up").fadeOut();
        $(".img-down").fadeIn();
        var data = $(e).attr("data-bind");
        var status = $(e).attr("data-open");
        console.log(status);
        $(".awars-item").fadeOut();
        $(".ls-award").attr("data-open", "closed");
        if (status == "closed") {
            $(e).attr("data-open", "open");
            $("img[data-bind='" + data + "-up']").fadeIn();
            $("img[data-bind='" + data + "-down']").fadeOut();
            $("li[data-bind='row-" + data + "']").fadeIn();
        } else {
            $(e).attr("data-open", "closed");
            $("img[data-bind='" + data + "-up']").fadeOut();
            $("img[data-bind='" + data + "-down']").fadeIn();
            $("li[data-bind='row-" + data + "']").fadeOut();
        }
    });

    $('#intro-content img').error(function () {
        $(this).unbind('error').attr('src', 'http://data.mobo.vn' + $(this).attr('src'));
    });
    $("#intro").click(function () {
        $(".list_event").fadeOut("fast", function () {
            $(".intro").fadeIn();
        });
    });
    $("#close-intro").click(function () {
        $(".intro").fadeOut("fast", function () {
            $(".list_event").fadeIn();
        });
    });
    var message = $("#message-response").val();
    //console.log(message);
    var buttonCloseShow = function () {
        $(".d-img-close").show();
    }
    var buttonCloseHide = function () {
        $(".d-img-close").hide();
    }
    $(".d-img-close,.overplay,.d-confirm").click(function () {
        popupClose();
    });
    var popupShowMessage = function (message, cancelBottonConfirm, cancelButtonClose) {
//        $('html').css('overflow', 'hidden');
//        $('body').bind('touchmove', function (e) {
//            e.preventDefault()
//        });
        $(".d-img-close").show();
        $(".d-confirm").show();
        if (cancelBottonConfirm) {
            $(".d-confirm").hide();
        }
        if (cancelButtonClose) {
            $(".d-img-close").hide();
        }
        $(".overplay").fadeIn();
        $("#d-message").html(message);
        $("#dialog").fadeIn();
    }
    var popupClose = function () {
//        $('html').css('overflow', 'scroll');
//        $('body').unbind('touchmove');
        $(".overplay").fadeOut();
        $("#dialog").fadeOut();
    }
    //console.log(message);
    if (message != "" && message != undefined) {
        buttonCloseHide();
        popupShowMessage(message);
        $(".d-confirm").unbind("click");
        $(".d-confirm").click(function () {
            if (form != "") {
                window.location.href = base_home_url + "/" + form + "?k=" + key;
            } else {
                window.location.href = base_home_url + "?k=" + key;
            }
        });
    }

    var showLoading = function () {
        $(".overplay").fadeIn();
        $("#loading").fadeIn();
    }

    var hideLoading = function () {
        $(".overplay").fadeOut();
        $("#loading").fadeOut();
    }
    $("#btn-share").click(function () {
        call("checkliked", {}, "get", function (data) {
            if (data.code == -1011014 || data.code == 1011012) {
                showLoading();
                window.location.href = base_home_url + "/feed?k=" + key;
            } else {
                popupShowMessage("Bạn phải hoàn thành nhiệm vụ like Fanpage trước khi thực hiện nhiệm vụ này", true);
            }
        });
    });
    $("#btn-invite").click(function () {
        call("checkliked", {}, "get", function (data) {
            if (data.code == -1011014) {
                showLoading();
                window.location.href = base_home_url + "/friends?k=" + key;
            } else {
                popupShowMessage("Bạn phải hoàn thành nhiệm vụ like Fanpage trước khi thực hiện nhiệm vụ này", true);
            }
        });

    });
    $("#paging").change(function () {
        var clss = "." + $(this).attr("data");
        $(clss).addClass("friend-disable");
        var index = $(this).val();
        $(clss + "[page-index=" + index + "]").removeClass("friend-disable");
        $(clss + "[page-index=" + index + "]").addClass("friend-active");
        $.each($(".friend-active"), function (idx, item) {
            var avatar = $(item).find(".avatar");
            var src = $(avatar).attr("data");
            $(avatar).attr("src", src);
        });
    });
    $(".row-friend").click(function () {
        var selected = $(this).attr("data-status-choice");
        //console.log(selected);
        if (selected == "unselected") {
            if (invitable_friends.length >= max_invite) {
                popupShowMessage("Bạn chỉ có thể mời tối đa " + max_invite + " bạn mỗi lần.");
            } else {
                //console.log($(this).find(".btn-status-choice"));
                $(this).find(".btn-status-choice").addClass("UFISelect");
                $(this).find(".btn-status-choice").removeClass("UFIUnSelect");
                $(this).attr("data-status-choice", "selected");
                var token = $(this).attr("data-bind");
                invitable_friends.push(token);
                var token_data = $(this).attr("data-token");
                invitable_friend_tokens.push(token_data);
            }
        } else {
            $(this).find(".btn-status-choice").addClass("UFIUnSelect");
            $(this).find(".btn-status-choice").removeClass("UFISelect");
            $(this).attr("data-status-choice", "unselected");
            var token = $(this).attr("data-bind");
            var index = invitable_friends.indexOf(token);
            if (index != -1)
                invitable_friends.splice(index, 1);

            var token_data = $(this).attr("data-token");
            var token_index = invitable_friend_tokens.indexOf(token_data);
            if (token_index != -1)
                invitable_friend_tokens.splice(token_index, 1);
        }
        //console.log(invitable_friends.length);
    });
    $("#btn-friend-close").click(function () {
        showLoading();
        window.location.href = base_home_url + "?k=" + key;
    });
    $("#btn-friend-invite").click(function () {
        if (invitable_friends.length <= 0) {
            popupShowMessage("Không có bạn nào được chọn, vui lòng chọn bạn trước khi thực hiện.");
            return;
        }
        if (invitable_friends.length > max_invite) {
            popupShowMessage("Bạn chỉ có thể mời tối đa " + max_invite + " bạn mỗi lần.");
            $(".d-confirm").unbind("click");
            $(".d-confirm").click(function () {
                window.location.href = base_home_url + "/invites?k=" + key + "&to=" + JSON.stringify(invitable_friends)
                        + "&excludeds=" + JSON.stringify(invitable_friend_tokens);
            });
        } else {
            showLoading();
            //console.log(base_home_url + "/invites?k=" + key + "&to=" + JSON.stringify(invitable_friends));
            var image_profile = $(".icon-profile").attr("src");
            window.location.href = base_home_url + "/invites?k=" + key
                    + "&to=" + JSON.stringify(invitable_friends)
                    + "&excludeds=" + JSON.stringify(invitable_friend_tokens);
        }
    });
    $("#btn-friend-checkall").click(function () {
        var page = $("#paging").val();
        $("li[page-index=" + page + "]").each(function (idx, e) {
            if (invitable_friends.length >= max_invite) {
                popupShowMessage("Bạn chỉ có thể mời tối đa " + max_invite + " bạn mỗi lần.");
                $(".d-confirm").unbind("click");
                $(".d-confirm").click(function () {
                    window.location.href = base_home_url + "/invites?k=" + key + "&to=" + JSON.stringify(invitable_friends)
                            + "&excludeds=" + JSON.stringify(invitable_friend_tokens);
                });
            } else {
                $(e).find(".btn-status-choice").addClass("UFISelect");
                $(e).find(".btn-status-choice").removeClass("UFIUnSelect");
                $(e).attr("data-status-choice", "selected");
                var token = $(this).attr("data-bind");
                invitable_friends.push(token);
                var token_data = $(e).attr("data-token");
                invitable_friend_tokens.push(token_data);
            }
        });
    });
    $("#search").keyup(function () {
        var value = remove_unicode($(this).val());
        var value_none = $(this).val();
        var clss = "." + $(this).attr("data");
        if (value == "") {
            $(clss).addClass("friend-disable");
            var index = $("#paging").val();
            $(clss + "[page-index=" + index + "]").removeClass("friend-disable");
            $(clss + "[page-index=" + index + "]").addClass("friend-active");
            $.each($(".friend-active"), function (idx, item) {
                var avatar = $(item).find(".avatar");
                var src = $(avatar).attr("data");
                $(avatar).attr("src", src);
            })
            return;
        }
        $(clss).addClass("friend-disable");
        $(clss).removeClass("friend-active");
        $.each($(clss + "[data-name-latin*='" + value + "']," + clss + "[data-name*='" + value_none + "']"), function (idx, item) {
            if (idx > paging)
                return;
            $(item).addClass("friend-active");
            $(item).removeClass("friend-disable");
            $.each($(".friend-active"), function (idx, item) {
                var avatar = $(item).find(".avatar");
                var src = $(avatar).attr("data");
                $(avatar).attr("src", src);
            })
        });
    });
    $("#btn-liked").click(function () {
        //console.log("liked");
        call("liked", {}, "get", function (data) {
            //console.log(data);
            //return;
            if (data.code == -1011014 || data.code == 1011012) {
                window.top.location.href = data.data.fanpage;
            } else {
                popupShowMessage(data.message, true);
            }
        });
    });
    $(".row-accept").click(function () {
        var selected = $(this).attr("data-status-choice");
        //console.log(selected);
        var dataId = $(this).attr("data-id");
        var dataName = $(this).attr("data-name");
        var dataUnique = $(this).attr("data-unique");
        var dataExcludeToken = $(this).attr("data-exclude-token");
        var dataDay = $(this).attr("data-day");
        var dataToken = $(this).attr("token");

        showLoading();
        var e = this;
        call("accepted", {
            id: dataId,
            name: dataName,
            unique: dataUnique,
            excludedToken: dataExcludeToken,
            day: dataDay,
            token: dataToken
        }, "get", function (data) {
            //console.log(data);
            if (data.code == 1001026) {
                hideLoading();
                $(e).fadeOut();
            } else {
                hideLoading();
                popupShowMessage(data.message, true);
            }
        });
    });
});
