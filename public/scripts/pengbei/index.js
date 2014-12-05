$(document).ready(function () {

    //首页高度适应
    $('#header').css("height", $(window).height() + "px");
    //818为页面最小高度
    if ($(window).height() > 818) {
        var h = $(window).height() - 818 + 430;
        $('#content').css("height", h + "px");
    }

    //页面加载完成立即执行的动画
    var i = 0;
    var interval;
    //配置动画的参数
    var lef = ["-55%", "-45%", "-50%"];
    var lef2 = ["-50%", "-50%", "-50%"];

    function run() {
        timer = setInterval(move, 250);
    }

    //可以理解为开机(加载)动画
    function move() {
        $('.loading-char').eq(i).css({
            "color": "#fff",
            "text-shadow": "0px 0px 10px #fff, 0px 0px 20px #fff, 0px 0px 40px #9dd"
        });
        if (i == 5) {
            clearTimeout(timer);
            $('#loading').css("display", "none");
            $('#nav').animate({"opacity": "1"}, 500);
            $('#header').animate({height: "70px"}, 500, function () {
                $('.background-3').animate({"margin-left": lef2[3], "opacity": "1"}, 250, function () {
                    $('.background').each(function (i) {
                        $(this).animate({"margin-left": lef[i], "opacity": "1"}, 250);
                        $(this).animate({"margin-left": lef2[i]}, 250);
                    });
                });
            });
        }
        ;
        i++;
    }

    run();

    //页面load后ajax加载列表
    var Json = $.ajax({url: "/pengbei/indexdata", async: false});
    var jsonData = JSON.parse(Json.responseText);

    $.each(jsonData.yw, function (index, item) {
        var date = new Date(parseFloat(item.PublishTime) * 1000);
        var newDOM = '<li><div><span><a href="pengbei/article/id/' + item.ID + '" target="_blank">' + item.Title.replace(/<br>/g, "") + '</a></span><span class="date">&nbsp;' + (date.getMonth() + 1) + '-' + date.getDate() + '</span></div></li>'
        $('.xinwen>div>.title-list').append(newDOM);
        if (index >= 9) {
            return false;
        }
        ;
    });

    $.each(jsonData.tz, function (index, item) {
        var date = new Date(parseFloat(item.PublishTime) * 1000);
        var newDOM = '<li><div><span><a href="pengbei/article/id/' + item.ID + '" target="_blank">' + item.Title.replace(/<br>/g, "") + '</a></span><span class="date">&nbsp;' + (date.getMonth() + 1) + '-' + date.getDate() + '</span></div></li>'
        $('.zixun>div>.title-list').append(newDOM);
        if (index >= 9) {
            return false;
        }
        ;
    });


    //左侧栏目列表点击
    $(".left-list-link").each(function (index) {
        $(this).click(function () {
            $(this).parent().addClass("active");
            $(this).parent().nextAll().removeClass("active");
            $(this).parent().prevAll().removeClass("active");
            var thisClick = $(this);

            thisClick.parent().parent().next().empty();

            var jsondata;
            switch (thisClick.html()) {
                case "活动":
                    jsondata = jsonData.tz;
                    break;
                case "学术":
                    jsondata = jsonData.xs;
                    break;
                case "就业":
                    jsondata = jsonData.jy;
                    break;
                case "考研":
                    jsondata = jsonData.ky;
                    break;
                case "勤工":
                    jsondata = jsonData.qg;
                    break;
                case "校内":
                    jsondata = jsonData.yw;
                    break;
                case "学院":
                    jsondata = jsonData.xy;
                    break;
                case "团学":
                    jsondata = jsonData.tx;
                    break;
                default:
                    console.log('Error!');
            }

            $.each(jsondata, function (index, item) {
                var date = new Date(parseFloat(item.PublishTime) * 1000);
                var newDOM = '<li><div><span><a href="pengbei/article/id/' + item.ID + '" target="_blank">' + item.Title.replace(/<br>/g, "") + '</a></span><span class="date">&nbsp;' + (date.getMonth() + 1) + '-' + date.getDate() + '</span></div></li>'
                thisClick.parent().parent().next().append(newDOM);
                if (index >= 9) {
                    return false;
                }
                ;
            });
        });
    });

    //全景
    for (var k = 0; k < 8; k++) {
        var fulldata;
        switch (k) {
            case 0:
                fulldata = jsonData.yw;
                break;
            case 1:
                fulldata = jsonData.xy;
                break;
            case 2:
                fulldata = jsonData.tx;
                break;
            case 3:
                fulldata = jsonData.tz;
                break;
            case 4:
                fulldata = jsonData.xs;
                break;
            case 5:
                fulldata = jsonData.jy;
                break;
            case 6:
                fulldata = jsonData.ky;
                break;
            case 7:
                fulldata = jsonData.qg;
                break;
            default:
                console.log('Error!');
        }
        $.each(fulldata, function (index, item) {
            var date = new Date(parseFloat(item.PublishTime) * 1000);
            var newDOM = '<li><div><span><a href="pengbei/article/id/' + item.ID + '" target="_blank">' + item.Title.replace(/<br>/g, "") + '</a></span><span class="date">&nbsp;' + (date.getMonth() + 1) + '-' + date.getDate() + '</span></div></li>';
            $('#full-ul>li').eq(k).children().append(newDOM);
            if (index >= 7) {
                return false;
            }
            ;
        });
    }

     console.log(jsonData.top);

    $.each(jsonData.top, function (index, item) {
        var imgUrl = item.ImgUrl.split(",")[0];
        var newDOM = '<li><a href="pengbei/article/id/' + item.ID + '" target="_blank"><img src=' + imgUrl + '></a></li>';
        $('#top').append(newDOM);
    });

    var count = 0;//图片轮播计数
    var photoCount = $('#top li').length;
    $('#top').css("width", photoCount * 420 + "px");

    $('.top-link').attr("href", "pengbei/article/id/" + jsonData.top[count].ID);
    $('#link').text(jsonData.top[count].Title);
    $('#count').text("(" + (count + 1) + "/" + photoCount + ")");

    $('#next').click(function () {
        count = count++ >= photoCount - 1 ? 0 : count++;
        $('.top-link').attr("href", "pengbei/article/id/" + jsonData.top[count].ID);
        $('#link').text(jsonData.top[count].Title);
        $('#count').text("(" + (count + 1) + "/" + photoCount + ")");
        $('#top').animate({"left": count * (-420) + "px"}, 400);
    });

    $('#prev').click(function () {
        count = count-- <= 0 ? photoCount - 1 : count--;
        $('.top-link').attr("href", "pengbei/article/id/" + jsonData.top[count].ID);
        $('#link').text(jsonData.top[count].Title);
        $('#count').text("(" + (count + 1) + "/" + photoCount + ")");
        $('#top').animate({"left": count * (-420) + "px"}, 400);
    });

    //加载页面时启动定时器
    function next() {
        $('#next').click();
    }

    var timer1 = setInterval(next, 5000);

    $('#new-content').mouseover(function () {
        clearInterval(timer1);
    });
    $('#new-content').mouseout(function () {
        timer1 = setInterval(next, 3500);
    });


    $('#full').click(function () {
        for (var n = 0; n < 8; n++) {
            $('#full-ul>li').eq(n).css("opacity", "0");
        }
        $('#bg').fadeIn();
        $('#full-ul').fadeIn();
        for (var n = 0; n < 8; n++) {
            $('#full-ul>li').eq(n).delay(n * 140).animate({"opacity": "1"}, 250);
        }
    });

    $('#close').click(function () {
        $('#bg').fadeOut();
        $('#full-ul').fadeOut();
    });

    $("#full-ul>li").hover(
        function () {
            $(this).css("background-color", "#297378");
            $(this).find('p').css("color", "#fff");
            $(this).find('span').css("color", "#fff");
            $(this).find('a').css("color", "#fff");
        },
        function () {
            $(this).css("background-color", "#e7ebeb");
            $(this).find('p').css("color", "#747474");
            $(this).find('span').css("color", "#747474");
            $(this).find('a').css("color", "#747474");
        }
    );
});