<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
 <head>
 <title>Find Interesting Videos</title>
 <link type='text/css' rel='stylesheet' href='popup.css'>

 <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
 <script type="text/javascript" src="jquery-1.11.3.min.js"></script>
 <script type="text/javascript" src="youtube-popup.js"></script>
 <script type="text/javascript" src="youtube-explorer.js"></script>
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
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr><td valign="top"><div id="leftbox"><p>&nbsp;</p><p><a href="/"><img id="logoimg" src="images/find-interesting-logo.png" width="480" height="163" alt="Find Interesting Videos" border="0"></a></p>


</div>
</td>

<td width="550">
<div id="rightbox">

<div id="outerbluebox">
  <!-- begin box content -->
  <div id="bluebox">
<div id="inputdiv"><form action="/fi/" method="get">View Favorites from Youtube channel:&nbsp;&nbsp; 
<input id="channelbox" type="text" name="id"> <input id="channelsub" type="submit" border="0" value="Go">&nbsp; &nbsp; 
<span id="explore"></span></form></div>
  
Favorites of:&nbsp;&nbsp; <a href="/fi/?id=geoffmobile">geoffmobile</a>&nbsp; &nbsp; <a href="/fi/?id=geoffpeterstrio">geoffpeterstrio</a>
 </div>
 <!-- end box content --> 
</div>

<div id="searcharea">
<form action="/fi/" method="get"><span class="searchtitle">Search:</span>&nbsp; <input id="searchbox" type="text" name="q"> 
<input id="searchsub" type="submit" border="0" value="Go">&nbsp; &nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; 
<a id="signInLink" title="Sign in using your Youtube account." class="hidestart" href="https://accounts.google.com/o/oauth2/auth?client_id=537332435748.apps.googleusercontent.com&amp;redirect_uri=http://geoffmobile.com/fi/oauth2callback.php&amp;scope=https://www.googleapis.com/auth/youtube&amp;response_type=token">Sign In</a>
<span id="signOutSpan" class="hidestart">Signed in. <a id="signOutLink" href="#" onclick="signOutOfGoogle(); return false;">Sign Out</a></span></form>
</div>

</div>
</td></tr></table>

<img src="images/divider.png" width="1116" height="8" alt="-">
<br>

<div id="instructtop">
<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td><img src="images/double-click-to-watch-video.gif" width="246" height="21" alt="Double click to watch video."></td>
<td width="385">&nbsp;</td></tr></table>
</div>

<div class="inner"></div>
<div id="morediv"><a href="#" onclick="loadMoreStarting(); return false;"><img src="images/load-more-videos.png" width="129" height="93" border="0" alt="Load More Videos"></a></div>
<p>&nbsp;</p>
<div class="help"><p>No videos showing? The user may not have any public favorites. Please try a different username.</p></div>   

<img src="images/divider.png" width="1116" height="8" alt="-">
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
<img src="images/divider.png" width="1116" height="8" alt="-">
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
