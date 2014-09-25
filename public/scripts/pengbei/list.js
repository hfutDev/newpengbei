$(document).ready(function (){
    if(window.location.pathname.lastIndexOf("xw") != -1){
        $('#which').html("新闻");
    } else {
        $('#which').html("资讯");
    }
});