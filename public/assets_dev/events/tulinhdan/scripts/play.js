
    /**
     * Global variables
     */
    var posArr = [
        8.33, //0
        24.9, //1 
        0, //2
        16.63, //3
        41.6, //4
        33.3, //5
        58.2, //6
        66.4, //7
        74.9, //8
        83.2, //9
        91.4, //10
        99.8, //11
    ];

//    $("#control1").click(function() {
//        
//        Play("#slot1", 10, 25, 1, 4000, posArr[2]);
//        Play("#slot2", 10, 40, 1, 5000, posArr[3]);
//        Play("#slot3", 10, 55, 1, 6000, posArr[5]);
//        Play("#slot4", 10, 70, 1, 7000, posArr[1]);
//        Play("#slot5", 10, 85, 1, 8000, posArr[9]);
//    });

    function Play(el, minSpeed, maxSpeed, step, duration, win_position)
    {
        var start_speed = minSpeed;

        $(el).pan({
            fps: 30,
            dir: 'down'
        });

        $(el).addClass('motion');
        $(el).spStart();

        var start = window.setInterval(function() {
            if (start_speed < maxSpeed) {
                start_speed += step;
                $(el).spSpeed(start_speed);
            }
        }, 100);

        setTimeout(function() {
            $(el).spSpeed(0);
            $(el).spStop();
            clearInterval(start);
            $(el).removeClass('motion');

            $(el).animate({
                //backgroundPosition: "(" + bgPos + ")"
                'background-position': "0% "+ win_position + "%"
            }, {
                duration: 0,
                complete: function() {
                    $(el).css('background-position', '0 '+ win_position + '%');
                }
            });
        }, duration);

    }