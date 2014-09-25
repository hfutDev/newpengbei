$(document).ready(function (){

    //页面load后ajax加载列表
    var Json = $.ajax({url:"/pengbei/indexdata",async:false});
    var jsonData = JSON.parse(Json.responseText);

    var deptId;
    switch($('#college>p').html()){
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

    $('.xinwen>div>.title-list').empty();
    for (var i = 0; i < jsonData.yw.length; i++) {
        if(jsonData.yw[i].DeptID == deptId && $('.xinwen>div>.title-list>li').length < 10){
            var item = jsonData.yw[i];
            var date = new Date(parseFloat(item.PublishTime));
            var newDOM = '<li><div><span><a href="/pengbei/article/id/' + item.ID + '" target="_blank">' + item.Title + '</a></span><span class="date">&nbsp;' + date.getMonth()+1 + '-' + date.getDate() + '</span></div></li>'
            $('.xinwen>div>.title-list').append(newDOM);
        }
    };


    $('.zixun>div>.title-list').empty();
    for (var i = 0; i < jsonData.tz.length; i++) {
        if(jsonData.tz[i].DeptID == deptId && $('.zixun>div>.title-list>li').length < 10){
            var item = jsonData.tz[i];
            var date = new Date(parseFloat(item.PublishTime));
            var newDOM = '<li><div><span><a href="/pengbei/article/id/' + item.ID + '" target="_blank">' + item.Title + '</a></span><span class="date">&nbsp;' + date.getMonth()+1 + '-' + date.getDate() + '</span></div></li>'
            $('.zixun>div>.title-list').append(newDOM);
        }
    };

    //Macbook Air内容切换
    $('.xinwen').before($('.zixun').clone(true));

    $('#prev').click(function(){
        $('#ul').animate({"left":"0px"},function () {
            var side = $('#ul>li:eq(0)').clone(true);
            var middle = $('#ul>li:eq(1)').clone(true);
            $('#ul').empty().append(middle).append(side).append($('#ul>li:eq(0)').clone(true)).css("left","-630px");;
        });
    });

    $('#next').click(function(){
        $('#ul').animate({"left":"-1260px"},function () {
            var side = $('#ul>li:eq(0)').clone(true);
            var middle = $('#ul>li:eq(1)').clone(true);
            $('#ul').empty().append(middle).append(side).append($('#ul>li:eq(0)').clone(true)).css("left","-630px");;
        });
    });


    //加载页面时启动定时器
    function next(){
        $('#next').click();
    }

    var timer2=setInterval(next, 5000);

    $('#ul').mouseover(function(){
        clearInterval(timer2);
    });
    $('#ul').mouseout(function(){
        timer2=setInterval(next, 3500);
    });


    //左侧栏目列表点击
    $(".left-list-link").each(function(index){
        $(this).click(function () {
            $(this).parent().addClass("active");
            $(this).parent().nextAll().removeClass("active");
            $(this).parent().prevAll().removeClass("active");
            var thisCkick = $(this);

            thisCkick.parent().parent().next().empty();

            var jsondata;
            switch(thisCkick.html()){
                case "通知":
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
                case "要闻":
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
                if(jsondata[i].DeptID == deptId && $('.append').length < 10){
                    var item = jsondata[i];
                    console.log(item);
                    var date = new Date(parseFloat(item.PublishTime));
                    var newDOM = '<li class="append"><div><span><a href="/pengbei/article/id/' + item.ID + '" target="_blank">' + item.Title + '</a></span><span class="date">&nbsp;' + date.getMonth()+1 + '-' + date.getDate() + '</span></div></li>'
                    thisCkick.parent().parent().next().append(newDOM);
                }
            };
        });
    });

    //图片
    $('.photo-ctrl li').each(function(index){
        $(this).click(function () {
            clearInterval(timer2);
            if(parseInt($('.photo').attr("src").substring(52,53)) != (index+1)){
                $('.photo').attr("src","/images/pengbei/banner/"+(index+1)+".jpg").animate({"opacity":"1"},250).css("display","block");
            } else{
                $('.photo').click();
            }
        })
    });

    $('.photo').click(function () {
        timer2=setInterval(next, 3500);
        $(this).animate({"opacity":"0"},250,function () {
            $(this).css("display","none");
            $('.photo').attr("src","/images/pengbei/banner/0.jpg");
        });
    });
});