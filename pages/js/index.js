$(document).ready(function (){
	var i = 0;
	var interval;
    var lef = ["-382px","-55%","-45%","-50%"];
    var lef2 = ["-382px","-50%","-50%","-50%"];
    function run() {
    	timer = setInterval(move,300);
    }
    function move() {
    	$('.loading-char').eq(i).css({"color":"#fff","text-shadow":"0px 0px 10px #fff, 0px 0px 20px #fff, 0px 0px 40px #9dd"});
    	if (i==5) {
    		clearTimeout(timer);
            $('#loading').css("display","none");
    		$('#nav').animate({"opacity":"1"},500);
    		$('#header').animate({height:"70px"},500,function () {
    			$('.header-phone').animate({"margin-left":lef2[0],"opacity":"1"},250,function () {
    				$('.background-3').animate({"margin-left":lef2[3],"opacity":"1"},250,function () {
                        $('.background').each(function(i){
                            $(this).animate({"margin-left":lef[i],"opacity":"1"},250);
                            $(this).animate({"margin-left":lef2[i]},250);
                        });
    				});
    			});
    		});
    	};
    	i++;
    }
    run();
});