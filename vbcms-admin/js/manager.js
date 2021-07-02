///////////
// VBcms Manager
/////////

// Par Sofiane Lasri - https://sl-projects.com

function getRandomString(length) {
    var randomChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var result = '';
    for ( var i = 0; i < length; i++ ) {
        result += randomChars.charAt(Math.floor(Math.random() * randomChars.length));
    }
    return result;
}
function resizePageContent(width,side){
	if (side=="left") {
		$(".page-content").css("margin-left", width+"px");
		$(".page-content").attr("leftSidebar", width);
		if ($(".page-content").attr("rightSidebar")!= 0) {
			$(".page-content").css("width", "calc(100% - "+width+"px - "+$('.page-content').attr('rightSidebar')+"px)");
		} else {
			$(".page-content").css("width", "calc(100% - "+width+"px)");
		}
	} else if (side=="right") {
		$(".page-content").attr("rightSidebar", width);
		if ($(".page-content").attr("leftSidebar")!= 0) {
			$(".page-content").css("width", "calc(100% - "+width+"px - "+$('.page-content').attr('leftSidebar')+"px)");
		} else {
			$(".page-content").css("width", "calc(100% - "+width+"px)");
		}
	}
	
}

function getDateTime() {
    var now     = new Date(); 
    var year    = now.getFullYear();
    var month   = now.getMonth()+1; 
    var day     = now.getDate();
    var hour    = now.getHours();
    var minute  = now.getMinutes();
    var second  = now.getSeconds(); 
    if(month.toString().length == 1) {
         month = '0'+month;
    }
    if(day.toString().length == 1) {
         day = '0'+day;
    }   
    if(hour.toString().length == 1) {
         hour = '0'+hour;
    }
    if(minute.toString().length == 1) {
         minute = '0'+minute;
    }
    if(second.toString().length == 1) {
         second = '0'+second;
    }   
    var dateTime = year+'-'+month+'-'+day+' '+hour+':'+minute+':'+second;   
    return dateTime;
}

function b64DecodeUnicode(str) {
	// Going backwards: from bytestream, to percent-encoding, to original string.
	return decodeURIComponent(atob(str).split('').map(function(c) {
		return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
	}).join(''));
}
function isJson(str) {
	try {
		JSON.parse(str);
	} catch (e) {
		return false;
	}
	return true;
}