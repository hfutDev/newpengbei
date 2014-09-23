$(document).ready(function (){
    //页面load后ajax加载列表
    $.ajax({
            url: "../pages/json/url-list-4.json",
            dataType: "json",
            success: function (data){
                $('.xinwen>div>.title-list').empty();
                $.each(data,function (index,item){
                    if (index<10) {
                        var newDOM = '<li><div><span><a href="' + item.url + '" target="_blank">' + item.title + '</a></span><span class="date">&nbsp;' + item.date + '</span></div></li>'
                        $('.xinwen>div>.title-list').append(newDOM);
                    };
                });
            },
            error: function (){
                console.log('Ajax Error!');
            }
    });
    $.ajax({
            url: "../pages/json/url-list-0.json",
            dataType: "json",
            success: function (data){
                $('.zixun>div>.title-list').empty();
                $.each(data,function (index,item){
                    if (index<10) {
                        var newDOM = '<li><div><span><a href="' + item.url + '" target="_blank">' + item.title + '</a></span><span class="date">&nbsp;' + item.date + '</span></div></li>'
                        $('.zixun>div>.title-list').append(newDOM);
                    };
                });
            },
            error: function (){
                console.log('Ajax Error!');
            }
    });

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

            $.ajax({
                    url: "../pages/json/url-list-" + index + ".json",
                    dataType: "json",
                    success: function (data){
                        thisCkick.parent().parent().next().empty();
                        $.each(data,function (index,item){
                            if (index<10) {
                                var newDOM = '<li><div><span><a href="' + item.url + '" target="_blank">' + item.title + '</a></span><span class="date">&nbsp;' + item.date + '</span></div></li>'
                                thisCkick.parent().parent().next().append(newDOM);
                            };
                        });
                    },
                    error: function (){
                        console.log('Ajax Error!');
                    }
            });
        });
    });

    //图片
    $.ajax({
            url: "../pages/json/photo.json",
            dataType: "json",
            success: function (data){
                $('.photo-ctrl li').each(function(index){
                    $(this).click(function () {
                        clearInterval(timer2);
                        $('.photo').attr("src",data[index].url).animate({"opacity":"0"},250).css("display","block").animate({"opacity":"1"},250);
                    })
                });
            },
            error: function (){
                console.log('Ajax Error!');
            }
    });

    $('.photo').click(function () {
        timer2=setInterval(next, 3500);
        $(this).animate({"opacity":"0"},250,function () {
            $(this).css("display","none");
        });
    });
});