<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
 <head>
 <title>Find Interesting Videos</title>
 <link type='text/css' rel='stylesheet' href='popup.css'>

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
 
var COOKIE_TOKEN = "accessToken";
var VALIDURL    =   'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=';

var FAVORITES_URL = 'favorite.php';

var CLIENT_ID = "537332435748.apps.googleusercontent.com";

var tokenValid = false;

function setTokenValid(isValid)
{
    tokenValid = isValid;
    if(isValid) {
       enableSignOut();        
    }
    else
    {
       enableSignIn();
    }
}

function signOutOfGoogle()
{
    eraseCookie(COOKIE_TOKEN);
    setTokenValid(false);
}

function enableSignOut()
{
    $('#signInLink').hide();
    $('#signOutSpan').show();
}

function favoriteIsDone($ok)
{
    $('#favoriteSpan').hide();
    
    if ($ok)
    {
        $('#favoriteDoneSpan').html("&nbsp; |&nbsp; Added to Favorites.&nbsp; &nbsp;");
    }
    else
    {
        $('#favoriteDoneSpan').html("&nbsp; |&nbsp; Error adding to Favorites.&nbsp; &nbsp;");
    }
    
    $('#favoriteDoneSpan').show();
}

function favoriteIsAvailable()
{
    $('#favoriteSpan').show();
    $('#favoriteDoneSpan').hide();
}

function enableSignIn()
{
    $('#signInLink').show();
    $('#signOutSpan').hide();
}

function enableFavoriteButton()
{
    $('#favoriteAllSpan').show();
}

function disableFavoriteButton()
{
    $('#favoriteAllSpan').hide();
}

function addToFavorites(videoId)
{
    var callback = function (isValid) {       
    
       if (isValid) {
          $.post(FAVORITES_URL, { authToken: readCookie(COOKIE_TOKEN), videoId: videoId },
             function(data) {
               var ok = (data == "ok");
              favoriteIsDone(ok);
             });
       }
       else
       {
	       // popup login screen
	       window.open ('https://accounts.google.com/o/oauth2/auth?client_id=537332435748.apps.googleusercontent.com&redirect_uri=http://geoffmobile.com/fi/oauth2callback.php&scope=https://gdata.youtube.com&response_type=token&state=' + videoId, 'newwindow', 'height=300,width=500, toolbar=no, menubar=no, scrollbars=no, resizable=yes,location=no, directories=no, status=no');
       }
    };
    
    validateToken(false, callback);     
}

function validateToken(firstTime, callback)
{
    var myTokenToTry = readCookie(COOKIE_TOKEN);
    if (myTokenToTry === '')
    {
        setTokenValid(false);
        if (firstTime)
        {
            loadFirstTime();
        }
        
        if (callback)
        {
            callback(false);
        }
    
        return;
    }
    
    $.ajax({
        url: VALIDURL + myTokenToTry,
        data: null,
        success: function(data){  

	        var isResponseOk = data.audience === CLIENT_ID;
            setTokenValid(isResponseOk);
            if (firstTime)
            {
                loadFirstTime();
            }
            if (callback)
            {
                callback(isResponseOk);
            }
        },  
        dataType: "jsonp"  
    });
            
}

function loadFirstTime()
{
  var idstring = gup("id");
          
  var noIdSpecified = (idstring.length === 0);
  var usingDefaultFavorites = false;
  
  if (noIdSpecified && !tokenValid)
  {
    idstring = "geoffmobile";         
  }
  else if (noIdSpecified && tokenValid)
  {
    idstring = "default";
    usingDefaultFavorites = true;         
  }
          
  var searchString = gup("q");    
  var targetElement = document.getElementById('searchbox');
  var userString = searchString.replace(/\+/g, ' ');
  targetElement.value = userString;
  
  if (searchString.length > 0)
  {
    getSearchVids(searchString, MODE_FIRST);
  }
  else if (usingDefaultFavorites)
  {
      $('span#explore').append('<a href="http://www.youtube.com/">Visit Youtube</a>');
        
      getMoreVids(idstring, MODE_FIRST); 
  } 
  else
  {           
    targetElement = document.getElementById('channelbox');
    targetElement.value = idstring;
  
    $('span#explore').append('<a href="http://www.youtube.com/user/' + encodeURIComponent(idstring) + '">Visit channel</a>');   
    
    getMoreVids(idstring, MODE_FIRST);
  }

}

function gup( name )
{
  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+name+"=([^&#]*)";
  var regex = new RegExp( regexS );
  var results = regex.exec( window.location.href );
  if( results === null ) {
    return "";
  }
  else{
    return results[1];
  }
}

function toBottom()
{
  window.scrollTo(0,document.body.scrollHeight);
}
   
function escapeDouble ( mystring )
{
  return mystring.replace(/"/g, "&quot;");
}

var loadedok = false;

var loadedChans = new Array();

var loadedVids = new Array();

var MODE_FIRST = 0;
var MODE_NORMAL = 1;
var MODE_MORE = 2;

var startingIndex = 1;
var startingURL = "";

var firstTime = true;

var loadingInProgress = false;

function getSearchVids(searchString, mode)
{
    
  loadVids('http://gdata.youtube.com/feeds/api/videos?alt=json-in-script&callback=?&q=' + encodeURIComponent(searchString) + '&max-results=30&v=2', mode);
}

function getMoreVids(accountid, mode)
{
    if (mode !== MODE_MORE)
    {
        // only load a channel's videos once
        if ($.inArray(accountid, loadedChans) !== -1)
        {
            return;
        }     
    }
    
    $tokenExtra = '';
    if (accountid === 'default')
    {
        $tokenExtra = '&access_token=' + readCookie(COOKIE_TOKEN);
    }
    
    var loadedOk = loadVids('http://gdata.youtube.com/feeds/api/users/' + accountid + '/favorites?v=2&alt=json-in-script&callback=?&max-results=30' + $tokenExtra, mode);
  
    if (mode !== MODE_MORE && loadedOk)
    {
        loadedChans.push (accountid);   
    }
}

function loadMoreStarting ()
{
    if (startingURL !== "")
    {
        loadVids(startingURL, MODE_MORE);
    }   
}

function loadVids(requestURL, mode)
{          
    if (mode === MODE_FIRST)
    {
        startingURL = requestURL;
    }
    if (mode === MODE_MORE || mode === MODE_FIRST)
    {
        var startString = "";
        startString = "&start-index=" + startingIndex;
        startingIndex += 30;
        
        requestURL += startString;
    }       
    
  $.getJSON(requestURL,
    function(data) 
    {  
      var vidIndex = loadedVids.length;
        
      var dataToAppend = '';
      
      var totalResults = data.feed.openSearch$totalResults.$t;
      
      if (mode == MODE_MORE || mode == MODE_FIRST)
      {
          var moreobj = document.getElementById('morediv');
          if (startingIndex <= totalResults)
          {
              moreobj.style.visibility = "visible";
          }
          else
          {
              moreobj.style.visibility = "hidden";
          }
      }
      if (!data.feed.entry) {
          return;
      }
      $.each(data.feed.entry, function(i, item) 
      {      
        var thumbpart = item['media$group']['media$thumbnail'];
        if (thumbpart == undefined)
          return;
        
        var thumb = thumbpart[0].url;
        var title = item['media$group']['media$title'].$t;
        if (title === "Deleted video") {
            return;
        }
        
        var videoId = item['media$group']['yt$videoid'].$t;
        var vidchannel = item['media$group']['media$credit'][0].$t;
        var url = item['link'][0].href;                
        
        dataToAppend +=         
           '<a href="' + encodeURI(url) + '" onclick="loadVideoRow(' + vidIndex + ');return false;" ondblclick="openYouTube(' + vidIndex + ');return false;"><img border="0" title="' + escapeDouble(title)
+ '" width="120" height="90" alt="' + escapeDouble(title) + '" src="' + encodeURI(thumb) + '"></a>\n';
        
        loadedVids.push([videoId,vidchannel]);
        vidIndex++;
      });
      
      if (dataToAppend.length !== 0)      
      {   
        $('.inner').append(dataToAppend);     
        
        loadedok = true;
        
        if (firstTime)
        {
          $('.help').empty();
          firstTime = false;
        }
      }
    });
    
    return true;
}

// load all the video favorites from the current row of videos
function loadVideoRow(vidIndex)
{
    /*
    var rowNum = Math.floor(vidIndex / 9);
    
    var startIndex = rowNum*9;
    var i;
    for(i=startIndex;i < loadedVids.length && i < startIndex+9; i++)
    {
        getMoreVids(loadedVids[i][1], MODE_NORMAL);
    }*/
    
    var startIndex = vidIndex - 3;
    if (startIndex < 0)
    {
        startIndex = 0;
    }
    var endIndex = vidIndex + 3;
    
    var i;
    for(i=startIndex;i < loadedVids.length && i <= endIndex;i++)
    {
        getMoreVids(loadedVids[i][1], MODE_NORMAL);
    }
}

function toggleAbout()
{
  var divbg = document.getElementById('how');
  if (divbg.style.visibility === "visible")
  {
    divbg.style.visibility = "hidden";
  }
  else
  {
    divbg.style.visibility =   "visible";
  }
}
   


$(document).ready(function()
{
    
    
  $(document).keydown(function(e)
  {
    if (e.keyCode === 27)  
    { 
        // escape key
        closeYouTube(); 
        return false;
    }  
    if (e.keyCode === 37) 
    { 
       // left key
       playNextVideo(false);
       return false;
    }
    if (e.keyCode === 39) 
    { 
       // right key
       playNextVideo(true);
       return false;
    }
  });
  
  validateToken(true);
  enableFavoriteButton();

});

 </script>
 </head>
<body>

<div id="bg" class="popup_bg" onclick="closeYouTube()"></div> 
<div id="ytdiv" class="popup">
  <div id="closebutton"><span onclick="closeYouTube()" class="close_button">X</span></div>
  <div id="watch"><span class="watch_button"><a onclick="pausePlayer()" id="poplink" class="grey" href="http://www.youtube.com/watch?v=ID" 
  target="_blank">Watch on Youtube</a>&nbsp; |&nbsp; <a id="queuelink" class="grey" title="Click to load more videos to play later." 
  href="#">Add Similar Videos</a><span id="favoriteAllSpan"><span id="favoriteSpan">&nbsp; |&nbsp; <a id="favoriteLink" class="grey" 
  title="Click to add this video to your favorites." href="#">Add To Favorites</a>&nbsp; &nbsp;</span><span id="favoriteDoneSpan" class="hidestart">&nbsp; 
  |&nbsp; Added to Favorites.&nbsp; &nbsp;</span></span></span></div>
  <div id="ytwrapper" class="ytcontainer">
    <div id="ytapiplayer">
      You need Flash player 8+ and JavaScript enabled to view this video.
    </div>
  </div>
</div>

<div id="outermain">

<table border="0" cellpadding="0" cellspacing="0">
<tr><td>
<script type="text/javascript"><!--
google_ad_client = "pub-0935994156181230";
/* Find Interesting Videos */
google_ad_slot = "1032067771";
google_ad_width = 468;
google_ad_height = 60;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</td>
<td>
<script type="text/javascript"><!--
google_ad_client = "pub-0935994156181230";
/* Find Interesting Videos */
google_ad_slot = "1032067771";
google_ad_width = 468;
google_ad_height = 60;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td valign="top"><div id="leftbox"><p>&nbsp;</p><p><a href="/"><img id="logoimg" src="find-interesting-logo.png" width="480" height="163" alt="Find Interesting Videos" border="0"></a></p>


</div>
</td>

<td width="550">
<div id="rightbox">

<div id="outerbluebox">
  <!-- begin box content -->
  <div id="bluebox">
<div id="inputdiv"><form action="/fi/" method="get">View Favorites from Youtube channel:&nbsp;&nbsp; <input id="channelbox" type="text" name="id"> <input id="channelsub" type="submit" border="0" value="Go">&nbsp; &nbsp; <span id="explore"></span></form></div>
  
Favorites of:&nbsp;&nbsp; <a href="/fi/?id=geoffmobile">geoffmobile</a>&nbsp; &nbsp; <a href="/fi/?id=geoffpeterstrio">geoffpeterstrio</a>
 </div>
 <!-- end box content --> 
</div>

<div id="searcharea">
<form action="/fi/" method="get"><span class="searchtitle">Search:</span>&nbsp; <input id="searchbox" type="text" name="q"> <input id="searchsub" type="submit" border="0" value="Go">&nbsp; &nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; 
<a id="signInLink" title="Sign in using your Youtube account." class="hidestart" href="https://accounts.google.com/o/oauth2/auth?client_id=537332435748.apps.googleusercontent.com&amp;redirect_uri=http://geoffmobile.com/fi/oauth2callback.php&amp;scope=https://gdata.youtube.com&amp;response_type=token">Sign In</a>
<span id="signOutSpan" class="hidestart">Signed in. <a id="signOutLink" href="#" onclick="signOutOfGoogle(); return false;">Sign Out</a></span></form>
</div>

</div>
</td></tr></table>

<img src="divider.png" width="1116" height="8" alt="-">
<br>

<div id="instructtop">
<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td><img src="double-click-to-watch-video.gif" width="246" height="21" alt="Double click to watch video."></td>
<td width="385">&nbsp;</td></tr></table>
</div>

<div class="inner"></div>
<div id="morediv"><a href="#" onclick="loadMoreStarting(); return false;"><img src="load-more-videos.png" width="129" height="93" border="0" alt="Load More Videos"></a></div>
<p>&nbsp;</p>
<div class="help"><p>No videos showing? The user may not have any public favorites. Please try a different username.</p></div>   

<img src="divider.png" width="1116" height="8" alt="-">
<br>
<p><b><span class="larger">FAQ:</span></b></p>
<table border="0" width="100%" cellpadding="0" cellspacing="0">
<tr>
<td width="50%" valign="top">

<p><b>Q: How do I play the videos?</b></p>
<p>A: To play a video, <b>double-click</b> the video's image.<br>(Double-clicking is when you tap twice quickly with your mouse.)</p>
&nbsp;<br>
</td>
<td valign="top">
&nbsp;

</td>
</tr>
</table>
<img src="divider.png" width="1116" height="8" alt="-">
<br>
&nbsp;<br>
<table border="0" width="100%" cellpadding="0" cellspacing="0">
<tr>
<td>
&nbsp;
</td>
<td align="right">
<b>Questions or comments? Please email</b> <a href="mailto:geoff@gpeters.com">geoff@gpeters.com</a>
</td>
</tr>
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</div>

<!--outermain-->
</body>
</html>
