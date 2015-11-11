<?

require_once("fav-inc.php");

$authToken = $_POST["authToken"];
$videoId = $_POST["videoId"];


if ($authToken == "" || $videoId == "")
{
	print("Sorry.");
	exit(0);
}


$addedOk = addFavorite($authToken, $videoId);

if ($addedOk)
{
	print "ok";
}
else
{
	print "error";
}

?>