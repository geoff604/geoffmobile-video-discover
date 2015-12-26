function createCookie(name,value,days) {
    var expires;
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        expires = "; expires="+date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name,"",-1);
}
 
var COOKIE_TOKEN = "accessToken";
var VALIDURL = 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=';

var FAVORITES_URL = 'favorite.php';

var CLIENT_ID = "537332435748.apps.googleusercontent.com";
var API_KEY = "AIzaSyD53zp_nPejqdtbZOBCxLGJckGiECc73Lk";

var tokenValid = false;

function setTokenValid(isValid)
{
    tokenValid = isValid;
    if (isValid)
    {
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
           window.open ('https://accounts.google.com/o/oauth2/auth?client_id=537332435748.apps.googleusercontent.com&redirect_uri=http://geoffmobile.com/fi/oauth2callback.php&scope=https://www.googleapis.com/auth/youtube&response_type=token&state=' + videoId, 'newwindow', 'height=300,width=500, toolbar=no, menubar=no, scrollbars=no, resizable=yes,location=no, directories=no, status=no');
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
        // default channel id to show favorites for, on first load
        idstring = "UCYzzY8r36QIYmnDGg5c-54w";
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

        $('span#explore').append('<a href="http://www.youtube.com/channel/' + encodeURIComponent(idstring) + '">Visit channel</a>');   

        getMoreVids(idstring, MODE_FIRST);
    }
}

function gup( name )
{
    name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
    var regexS = "[\\?&]"+name+"=([^&#]*)";
    var regex = new RegExp( regexS );
    var results = regex.exec( window.location.href );
    if ( results === null )
    {
        return "";
    }
    else
    {
        return results[1];
    }
}

function toBottom()
{
    window.scrollTo(0,document.body.scrollHeight);
}
   
function escapeDouble( mystring )
{
    return mystring.replace(/"/g, "&quot;");
}

var loadedChans = [];
var loadedVids = [];

var MODE_FIRST = 0;
var MODE_NORMAL = 1;
var MODE_MORE = 2;

var startingURL = "";

var RESULTS_PER_PAGE = 30;
var nextPageToken = "";

var firstTime = true;

var loadingInProgress = false;

function getSearchVids(searchString, mode)
{
    loadVids('https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&maxResults=' + RESULTS_PER_PAGE + '&q='+ encodeURIComponent(searchString), mode);
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
    
    // see: https://developers.google.com/youtube/v3/guides/implementation/favorites

    var channelInfoURL = "https://www.googleapis.com/youtube/v3/channels?part=contentDetails&id=" + encodeURIComponent(accountid);
    channelInfoURL = appendKey(channelInfoURL);
    $.getJSON(channelInfoURL, function(data) {  
        if (data && data.items && data.items[0]) {
            var item = data.items[0];
            if (item.contentDetails && item.contentDetails.relatedPlaylists) {
                if (item.contentDetails.relatedPlaylists.favorites) {
                    loadFavoritesFromPlaylist(item.contentDetails.relatedPlaylists.favorites, mode);
                } else if (item.contentDetails.relatedPlaylists.likes) {
                    loadFavoritesFromPlaylist(item.contentDetails.relatedPlaylists.likes, mode);
                }
                if (item.contentDetails.relatedPlaylists.uploads) {
                    loadFavoritesFromPlaylist(item.contentDetails.relatedPlaylists.uploads, MODE_NORMAL);
                }
            }
        }
    });
    
    if (mode !== MODE_MORE)
    {
        loadedChans.push(accountid);
    }
}

function loadFavoritesFromPlaylist(playlistId, mode) {
    // Get the list of videos as in: GET https://www.googleapis.com/youtube/v3/playlistItems?part=snippet%2CcontentDetails%2Cstatus&playlistId=FLK8sQmJBp8GCxrOtXWBpyEA&key={YOUR_API_KEY}
    // see: https://developers.google.com/apis-explorer/#p/youtube/v3/youtube.playlistItems.list?part=snippet%252CcontentDetails%252Cstatus&playlistId=FLK8sQmJBp8GCxrOtXWBpyEA&_h=1&
    var playlistVideosURL = "https://www.googleapis.com/youtube/v3/playlistItems?part=" + encodeURIComponent("snippet,id") + "&maxResults=" + RESULTS_PER_PAGE + "&playlistId=" + encodeURIComponent(playlistId);
    loadVids(playlistVideosURL, mode);
}

function loadMoreStarting()
{
    if (startingURL !== "")
    {
        loadVids(startingURL, MODE_MORE);
    }
}

function appendKey(url) {
    return url + '&key=' + API_KEY;
}

function loadVids(requestURL, mode)
{          
    if (mode === MODE_FIRST)
    {
        startingURL = requestURL;
    }

    requestURL = appendKey(requestURL);
    if (mode === MODE_MORE || mode === MODE_FIRST)
    {
        var startString = "";
        if (nextPageToken && nextPageToken !== "") {
            startString = "&pageToken=" + nextPageToken;
        }
        requestURL += startString;
    }       
    
    $.getJSON(requestURL, function(data) 
    {  
        var vidIndex = loadedVids.length;

        var dataToAppend = '';

        if (mode == MODE_MORE || mode == MODE_FIRST)
        {
            nextPageToken = data.nextPageToken;
            var moreobj = document.getElementById('morediv');
            if (nextPageToken && nextPageToken !== "")
            {
                moreobj.style.visibility = "visible";
            }
            else
            {
                moreobj.style.visibility = "hidden";
            }
        }
        if (!data.items) {
            return;
        }
        data.items.forEach(function(item) {     
            if (!item || !item.snippet || !item.snippet.thumbnails || !item.snippet.thumbnails.default) {
                return;
            }

            var thumb = item.snippet.thumbnails.default.url;
            var title = item.snippet.title;
            if (title === "Deleted video") {
                return;
            }

            var videoId;

            if (item.contentDetails && item.contentDetails.videoId) {
                videoId = item.contentDetails.videoId;
            } else if (item.id && item.id.videoId) {
                videoId = item.id.videoId;
            } else if (item.snippet.resourceId && item.snippet.resourceId.videoId) {
                videoId = item.snippet.resourceId.videoId;
            }
            if (!videoId) {
                return;
            }
            var vidchannel = item.snippet.channelId;
            var url = "https://www.youtube.com/watch?v=" + videoId;

            dataToAppend +=
               '<a href="' + encodeURI(url) + '" onclick="loadVideoRow(' + vidIndex + ');return false;" ondblclick="openYouTube(' + vidIndex + ');return false;"><img border="0" title="' + escapeDouble(title) +
               '" width="120" height="90" alt="' + escapeDouble(title) + '" src="' + encodeURI(thumb) + '"></a>\n';

            loadedVids.push([videoId,vidchannel]);
            vidIndex++;
        });

        if (dataToAppend.length !== 0)
        {   
            $('.inner').append(dataToAppend);

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
    var startIndex = vidIndex - 3;
    if (startIndex < 0)
    {
        startIndex = 0;
    }
    var endIndex = vidIndex + 3;
    
    var i;
    for (i = startIndex;i < loadedVids.length && i <= endIndex;i++)
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
    $(document).keydown(function(e) {
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
