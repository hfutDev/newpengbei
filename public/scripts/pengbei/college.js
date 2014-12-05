$(document).ready(function () {

    //首页高度适应
    //818为页面最小高度
    if ($(window).height() > 818) {
        var h = $(window).height() - 818 + 430;
        $('#content').css("height", h + "px");
    }

    //页面load后ajax加载列表
    var Json = $.ajax({url: "/pengbei/indexdata", async: false});
    var jsonData = JSON.parse(Json.responseText);

    var deptId;
    switch ($('#college>p').html()) {
        case "机械与汽车工程学院":
            deptId = 1;
            break;
        case "电气与自动化工程学院":
            deptId = 2;
            break;
        case "材料科学与工程学院":
            deptId = 3;
            break;
        case "计算机与信息学院":
            deptId = 4;
            break;
        case "土木与水利工程学院":
            deptId = 5;
            break;
        case "化学工程学院":
            deptId = 6;
            break;
        case "马克思主义学院":
            deptId = 7;
            break;
        case "经济学院":
            deptId = 8;
            break;
        case "外国语学院":
            deptId = 9;
            break;
        case "管理学院":
            deptId = 10;
            break;
        case "仪器科学与光电工程学院":
            deptId = 11;
            break;
        case "建筑与艺术学院":
            deptId = 12;
            break;
        case "资源与环境工程学院":
            deptId = 13;
            break;
        case "生物与食品工程学院":
            deptId = 14;
            break;
        case "数学学院":
            deptId = 15;
            break;
        case "电子科学与应用物理学院":
            deptId = 16;
            break;
        case "交通运输工程学院":
            deptId = 17;
            break;
        case "软件学院":
            deptId = 18;
            break;
        case "医学工程学院院":
            deptId = 19;
            break;
        default:
            console.log('Error!');
    }
    for (var i = 0; i < jsonData.yw.length; i++) {
        if (jsonData.yw[i].DeptID == deptId) {
            var item = jsonData.yw[i];
            var date = new Date(parseFloat(item.PublishTime) * 1000);
            var newDOM = '<li><div><span><a href="/pengbei/article/id/' + item.ID + '" target="_blank">' + item.Title.replace(/<br>/g, "") + '</a></span><span class="date">&nbsp;' + (date.getMonth() + 1) + '-' + date.getDate() + '</span></div></li>';
            $('.xinwen>div>.title-list').append(newDOM);
        }
        ;
        if ($('.xinwen>div>.title-list>li').length > 9) {
            break;
        }
        ;
    }
    ;

    for (var i = 0; i < jsonData.tz.length; i++) {
        if (jsonData.tz[i].DeptID == deptId) {
            var item = jsonData.tz[i];
            var date = new Date(parseFloat(item.PublishTime) * 1000);
            var newDOM = '<li><div><span><a href="/pengbei/article/id/' + item.ID + '" target="_blank">' + item.Title.replace(/<br>/g, "") + '</a></span><span class="date">&nbsp;' + (date.getMonth() + 1) + '-' + date.getDate() + '</span></div></li>';
            $('.zixun>div>.title-list').append(newDOM);
        }
        ;
        if ($('.zixun>div>.title-list>li').length > 9) {
            break;
        }
        ;
    }
    ;


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

            for (var i = 0; i < jsondata.length; i++) {
                if (jsondata[i].DeptID == deptId) {
                    var item = jsondata[i];
                    var date = new Date(parseFloat(item.PublishTime) * 1000);
                    var newDOM = '<li><div><span><a href="/pengbei/article/id/' + item.ID + '" target="_blank">' + item.Title.replace(/<br>/g, "") + '</a></span><span class="date">&nbsp;' + (date.getMonth() + 1) + '-' + date.getDate() + '</span></div></li>'
                    thisClick.parent().parent().next().append(newDOM);
                }
                ;
                if (thisClick.parent().parent().next().children().length > 9) {
                    return false;
                }
                ;
            }
            ;
        });

    });

    // console.log(jsonData.top);

    $.each(jsonData.top, function (index, item) {
        var imgUrl = item.ImgUrl.split(",")[0];
        var newDOM = '<li><a href="/pengbei/article/id/' + item.ID + '" target="_blank"><img src=' + imgUrl + '></a></li>';
        $('#top').append(newDOM);
    });

    var count = 0;//图片轮播计数
    var photoCount = $('#top li').length;
    $('#top').css("width", photoCount * 420 + "px");

    $('.top-link').attr("href", "/pengbei/article/id/" + jsonData.top[count].ID);
    $('#link').text(jsonData.top[count].Title);
    $('#count').text("(" + (count + 1) + "/" + photoCount + ")");

    $('#next').click(function () {
        count = count++ >= photoCount - 1 ? 0 : count++;
        $('.top-link').attr("href", "/pengbei/article/id/" + jsonData.top[count].ID);
        $('#link').text(jsonData.top[count].Title);
        $('#count').text("(" + (count + 1) + "/" + photoCount + ")");
        $('#top').animate({"left": count * (-420) + "px"}, 400);
    });

    $('#prev').click(function () {
        count = count-- <= 0 ? photoCount - 1 : count--;
        $('.top-link').attr("href", "/pengbei/article/id/" + jsonData.top[count].ID);
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
});