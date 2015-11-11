<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
 <head>
 <title>Youtube Video Explorer</title>
 <link type='text/css' rel='stylesheet' href='popup.css'>

 <!-- Copyright (c) 2012 Geoff Peters. -->

 <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
 <script type="text/javascript" src="youtube-popup.js"></script>
 <script type="text/javascript" src="jquery-1.7.2.min.js"></script>
 <script type="text/javascript" src="swfobject.js"></script>   


 <script type="text/javascript">

function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name) {
	createCookie(name,"",-1);
}
 
function favoriteIsDone(favoriteOk)
{				
	if (favoriteOk)
	{
		$('#userMessage').html("The favorite was added.");
	}
	else
	{
		$('#userMessage').html("Sorry, the favorite could not be added.");
	}
}

var COOKIE_TOKEN = "accessToken";
var FAVORITES_URL = 'favorite.php';

$(document).ready(function()
{
    var myLocation;
    
    if (window.location.hash) {
	    var lochash = window.location.hash.substr(1),
            myAccessToken = lochash.substr(lochash.indexOf('access_token='))
                  .split('&')[0]
                  .split('=')[1];
                  
        var myState = '';
        
        if( lochash.indexOf('state=') != -1)
        {
	        myState = lochash.substr(lochash.indexOf('state='))
                  .split('&')[0]
                  .split('=')[1];
        }
              
        if (myAccessToken !== '') {
	        createCookie(COOKIE_TOKEN, myAccessToken, 5);
        }    
        
        if (myState !== '') {
	        
	        $.post(FAVORITES_URL, { authToken: readCookie(COOKIE_TOKEN), videoId: myState },
	             function(data) {
	               var ok = (data == "ok");
	              favoriteIsDone(ok);
	             });
        }
        else
        {
	        window.location.replace("http://geoffmobile.com/fi/");	    
        }
	}
});

 </script>
 </head>
<body>
<p><span id="userMessage">Please wait a moment...</span></p>
</body>
</html>