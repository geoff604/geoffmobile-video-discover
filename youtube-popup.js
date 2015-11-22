var currentIndex = 0;

var tag = document.createElement('script');
tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

var loadDeferred = $.Deferred();

var ytplayer;
function onYouTubeIframeAPIReady() {
    ytplayer = new YT.Player('ytwrapper', {
        height: '505',
        width: '640',
        events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
        },
        playerVars: {
            autoplay: 1,
            rel: 0
        }
    });
}

function openYouTube(vidIndex) {
    var startId = loadedVids[vidIndex][0];
    var startChannel = loadedVids[vidIndex][1];

    currentIndex = vidIndex;

    //YouTube Player Parameters
    var width = 640;
    var height = 505;
    var FullScreen = "yes";
    var AutoPlay = "yes";
    var HighDef = "yes";

    //Calculate Page width and height
    var pageWidth = window.innerWidth;
    var pageHeight = window.innerHeight;
    if (typeof pageWidth !== "number"){
      if (document.compatMode === "CSS1Compat"){
            pageWidth = document.documentElement.clientWidth;
            pageHeight = document.documentElement.clientHeight;
      } else {
            pageWidth = document.body.clientWidth;
            pageHeight = document.body.clientHeight;
      }
    }
    // Make Background visible...
    var divbg = document.getElementById('bg');
    divbg.style.visibility = "visible";

    //Create dynamic Div container for YouTube Popup Div
    var divobj = document.getElementById('ytdiv');
    divobj.style.visibility = "visible";
    var divWidth = width + 4;
    var divHeight = height + 20;
    divobj.style.width = divWidth + "px";
    divobj.style.height = divHeight + "px";
    var divLeft = (pageWidth - divWidth) / 2;
    var divTop = (pageHeight - divHeight) / 2 - 6;
    divobj.style.left = divLeft + "px";
    divobj.style.top = divTop + "px";

    //Create dynamic Close Button Div
    var closebutton = document.getElementById('closebutton');
    closebutton.style.visibility = "visible";

    //Create watch on youtube link
    var watchbutton = document.getElementById('watch');
    watchbutton.style.visibility = "visible";

    loadDeferred.done(function() {
        ytplayer.loadVideoById(startId, 0, "hd720");
    });

    updatePopLink(startId);
    updateFavoriteLink(startId);
    updateQueueLink(currentIndex);
                    
    var previousObj = document.createElement('div');       
    previousObj.setAttribute('id','previousDiv'); // Set id to YouTube id
    previousObj.style.visibility = "visible";
    previousObj.className = "popup";
    var butWidth = 66;
    var butHeight = 86;
    previousObj.style.width = butWidth + "px";
    previousObj.style.height = butHeight + "px";
    var butLeft = divLeft - butWidth;
    var butTop = divTop + (divHeight - butHeight)/2;
    //Set Left and top coordinates for the div tag
    previousObj.style.left = butLeft + "px";
    previousObj.style.top = butTop + "px";
    previousObj.innerHTML = "<a onclick=\"playNextVideo(false);return false;\" href=\"#\"><img border=\"0\" src=\"images/back-lg.gif\" title=\"Previous\" alt=\"Previous\" width=\"66\" height=\"96\"></a>";
    document.body.appendChild(previousObj);     

    var nextObj = document.createElement('div');       
    nextObj.setAttribute('id','nextDiv'); // Set id to YouTube id
    nextObj.style.visibility = "visible";
    nextObj.className = "popup";
    nextObj.style.width = butWidth + "px";
    nextObj.style.height = butHeight + "px";
    butLeft = divLeft + divWidth;
    butTop = divTop + (divHeight - butHeight)/2;
    //Set Left and top coordinates for the div tag
    nextObj.style.left = butLeft + "px";
    nextObj.style.top = butTop + "px";
    nextObj.innerHTML = "<a onclick=\"playNextVideo(true);return false;\" href=\"#\"><img border=\"0\" src=\"images/forward-lg.gif\" title=\"Next\" alt=\"Next\" width=\"66\" height=\"96\"></a>";
    document.body.appendChild(nextObj); 
}

function onPlayerReady()
{
    ytplayer.setPlaybackQuality('hd720');
    loadDeferred.resolve();
}

function onPlayerStateChange(newState) 
{
    if (newState === 0)
    {
        playNextVideo (true);
    }
}

function playNextVideo(goForward)
{
    // finished playing, so load next song.
    if (ytplayer !== undefined)
    {
        if (goForward)
        {
            if (currentIndex >= loadedVids.length - 1)
            {
                return;
            }
            currentIndex++;
        }
        else
        {
            if (currentIndex <= 0)
            {
                return;
            }
            currentIndex--;
        }
        
        var newId = loadedVids[currentIndex][0];
            
        playVideoWithId(newId, currentIndex);
    }
}

function updatePopLink(newId)
{
    // update the View in Youtube link
    document.getElementById("poplink").href = "http://www.youtube.com/watch?v=" + newId;
}

function updateQueueLink(currentIndex)
{   
    var js = 'loadVideoRow(' + currentIndex + ');return false;';
    var newclick = new Function(js);

    // clears onclick then sets click using jQuery
    $("#queuelink").attr('onclick', '').click(newclick);
}

function updateFavoriteLink(newId)
{   
    favoriteIsAvailable();
    
    // clears onclick then sets click using jQuery
    $("#favoriteLink").attr('onclick', 'addToFavorites("' + newId + '");return false;');
}

function playVideoWithId(newId, currentIndex)
{
    updatePopLink(newId);
    updateFavoriteLink(newId);
    updateQueueLink(currentIndex);

    // load the new video
    ytplayer.loadVideoById(newId, 0, "hd720");
}

function pausePlayer()
{
    if (ytplayer !== undefined)
    {
        ytplayer.pauseVideo();
    }
}

function closeYouTube()
{
    ytplayer.stopVideo();
    
    var mydiv = document.getElementById('bg');
    mydiv.style.visibility = "hidden";
    mydiv = document.getElementById("ytdiv");
    mydiv.style.visibility = "hidden";
    mydiv = document.getElementById("closebutton");
    mydiv.style.visibility = "hidden";
    mydiv = document.getElementById("watch");
    mydiv.style.visibility = "hidden";
    
    mydiv = document.getElementById("previousDiv");
    if (mydiv !== undefined)
        document.body.removeChild(mydiv);
    
    mydiv = document.getElementById("nextDiv");
    if (mydiv !== undefined)
        document.body.removeChild(mydiv);
}