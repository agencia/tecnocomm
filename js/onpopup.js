// JavaScript Document

$(function(){
$(window).bind("beforeunload", function(){
										
										window.opener.location.reload();

										
										});


});