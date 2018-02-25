window.onload = function() {
    // Animate loader off screen
    $(".se-pre-con").fadeOut("slow");
};


(function($) {
    jQuery.scrollSpeed = function(step, speed, easing) {
        var $document = $(document),
            $window = $(window),
            $body = $('html, body'),
            option = easing || 'default',
            root = 0,
            scroll = false,
            scrollY, scrollX, view;
        if (window.navigator.msPointerEnabled)
            return false;
        $document.on('ready', function() {
            root = $window.scrollTop();;
        });
        $window.on('mousewheel DOMMouseScroll', function(e) {
            var deltaY = e.originalEvent.wheelDeltaY,
                detail = e.originalEvent.detail;
            scrollY = $document.height() > $window.height();
            scrollX = false;
            scroll = true;
            if (scrollY) {
                view = $window.height();
                if (deltaY < 0 || detail > 0)
                    root = (root + view) >= $document.height() ? root : root += step;
                if (deltaY > 0 || detail < 0)
                    root = root <= 0 ? 0 : root -= step;
                $body.stop().animate({
                    scrollTop: root
                }, speed, option, function() {
                    scroll = false;
                });
            }
            return false;
        }).on('scroll', function() {
            if (scrollY && !scroll) root = $window.scrollTop();
            if (scrollX && !scroll) root = $window.scrollLeft();
        }).on('resize', function() {
            if (scrollY && !scroll) view = $window.height();
            if (scrollX && !scroll) view = $window.width();
        });
    };
    jQuery.easing.default = function(x, t, b, c, d) {
        return -c * ((t = t / d - 1) * t * t * t - 1) + b;
    };
})(jQuery);




$(function() {
    jQuery.scrollSpeed(80, 800);
});



var srh = $(window).height();
var apr = srh - 100;
$(window).scroll(function() {
    tcani();
});
$(window).load(function() {
    tcani();
});

function tcani() {
    $('[data-csani]').each(function() {
        var anin = $(this).attr('data-csani');
        var imagePos = $(this).offset().top;
        var topOfWindow = $(window).scrollTop();
        if (imagePos < topOfWindow + apr) {
            $(this).addClass(anin);
            $(this).removeAttr('data-csani');
        }
    });
}








$(document).ready(function() {

    $('.owl-carousel-2').owlCarousel({
        loop: true,
        margin: 130,
        //autoWidth:true,
        // autoHeight:true,
        items: 4,
        dots: false,
        nav: true,
        navText: [
            "<i class='glyphicon glyphicon-menu-left' style='font-size:22px'></i>",
            "<i class='glyphicon glyphicon-menu-right' style='font-size:22px'></i>"
        ],
        responsive: {
            1000: {
                items: 4,
            }
        }

    })

});
