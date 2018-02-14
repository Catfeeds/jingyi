// JavaScript Document
$(document).ready(function(){
	var winH=$(window).height();  //屏幕高度
	var winW=$(window).width();  //屏幕宽度
	
	var centerpointH=(winH)/2 ;    //中心点
	var centerpointW=winW/2 ;    //中心点
		
    $width=$(".galaxy").width();
	
	
	circle("mercury",$width*0.1,centerpointW,centerpointH);
	docircle("mercury",$width*0.5,centerpointW,centerpointH);
	circleimg("mercury");
	
	circle("venus",$width*0.30,centerpointW,centerpointH);
	docircle("venus",$width*0.70,centerpointW,centerpointH);
	circleimg("venus");
	
	circle("earth1",$width*0.5,centerpointW,centerpointH);
	docircle("earth1",$width*0.5,centerpointW,centerpointH);
	circleimg("earth1");
	
	circle("mars",$width*0.7,centerpointW,centerpointH);
	docircle("mars",$width*0.7,centerpointW,centerpointH);
	circleimg("mars");
	
	circle("jupiter",$width*0.95,centerpointW,centerpointH);
	docircle("jupiter",$width*0.95,centerpointW,centerpointH);
	circleimg("jupiter");
	
	$(".galaxy").height(winH);	
});

function circle(divname,circleWidth,centerpointW,centerpointH){
	$("."+divname+"-track").height(circleWidth);
	$("."+divname+"-track").width(circleWidth);
	var left=centerpointW-circleWidth/2;
	var top=centerpointH-circleWidth/2;
	$("."+divname+"-track").css({'border-radius':circleWidth/2,'left':left,'top':top});
	
	}
function docircle(divname,circleWidth,centerpointW,centerpointH){
	var divwidth=$("."+divname).width();
	var left=centerpointW + circleWidth/2 - divwidth/2;
	var top=centerpointH-divwidth/2;
	var transformW=-(circleWidth/2-divwidth/2);
	var transformH=divwidth/2;
	var transform=transformW+'px '+transformH+'px';
	$("."+divname).css({'border-radius':circleWidth/2,'left':left,'top':top,"transformOrigin":transform});
	
	}
function circleimg(divname){
	var width=$("."+divname).width();

	$("."+divname).children("img").width(width+"px");
	$("."+divname).children("img").height(width+"px");
	$("."+divname).children("img").css({'border-radius':width/2});
	
	}