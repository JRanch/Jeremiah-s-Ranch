var JQ = jQuery.noConflict();

// Start new prediction:
function showTOS(title, content) {

	iWidth = JQ(window).width();
	iHeight = JQ(window).height();
	

	JQ("body").append('<div id="darkener"></div>');

	JQ("#darkener").css({
		"position": "fixed",
		"left": "0px",
		"top": "0px",
		"width": iWidth+"px",
		"height": iHeight+"px",
		"background-color": "#000",
		"opacity": 0,
		"z-index": 10002
	});

	JQ("#darkener").animate({"opacity": .7}, "slow").click(function() {
		closePopin();
	});

	JQ("body").append('<div id="popin" style="padding:10px; overflow-y: scroll;"><div style=" text-align:right; height:16px; width:100%; "><a href="#!" onclick="closePopin()" class="btn">Close</a></div><div id="popinbody"></div></div>');

	iPopinWidth = 850;
	iPopinHeight = 400;

	JQ("#popin").css({
		"position": "fixed",
		"top": Math.round((iHeight - iPopinHeight) / 2)+"px",
		"left": Math.round((iWidth - iPopinWidth) / 2)+"px",
		"width": iPopinWidth+"px",
		"height": iPopinHeight+"px",
		"background-color": "#FFF",
		"opacity": 0,
		"z-index": 10003
	});

	JQ("#popin").animate({"opacity": 1}, "slow");
	
	JQ.ajax({
		//this is the php file that processes the data and send mail
		url: "index.php?option=com_ticketmaster&controller=cart&task=showTos&format=raw", 
		//POST method is used
		type: "POST",
		//Do not cache the page	
		cache: false,
		//success			
		success: function (html) {              
			JQ("#popinbody").append(html);
		} 		
	});

}

function closePopin() {

	JQ(document).unbind("keyup");

	JQ("#popin,#darkener").fadeOut("slow", function() {
		JQ("#popin,#darkener").remove();
	});
}