// JavaScript Document
var win= null;
function NewWindow(mypage,myname,w,h){
  var winl = (screen.width-w)/2;
  var wint = (screen.height-h)/2;
  var settings  ='height='+h+',';
      settings +='width='+w+',';
      settings +='top='+wint+',';
      settings +='left='+winl+',';
      settings +='scrollbars=yes,';
      settings +='resizable=yes';
      //alert(settings);
  win=window.open(mypage,myname,settings);
  if(parseInt(navigator.appVersion) >= 4){win.window.focus();}
}
function NewWindowm(mypage,myname,w,h){
  var winl = (screen.width-w)/2;
  var wint = (screen.height-h)/2;
  var settings  ='height='+h+',';
      settings +='width='+w+',';
      settings +='top='+wint+',';
      settings +='left='+winl+',';
      settings +='scrollbars=yes,';
      settings +='resizable=yes';
  win=window.open(mypage,myname,settings);
  if(parseInt(navigator.appVersion) >= 4){win.window.focus();}
}

	
			$(function(){
				$(".popup").live("click",function (){ 
								NewWindow($(this).attr("href"),$(this).attr("title"),600,600);
	 							return false;
   	 	})});	
			$(function(){
				$(".popupm").live("click",function (){ 
								NewWindowm($(this).attr("href"),$(this).attr("title"),$(this).attr("w"),$(this).attr("h"));
	 							return false;
   	 	})});