<?

// returns true if it could be favorited
function addFavorite($authToken, $videoId)
{
	
	$videoAtom = <<<EOS
<?xml version="1.0" encoding="UTF-8"?>
	<entry xmlns="http://www.w3.org/2005/Atom">
	  <id>$videoId</id>
	</entry>
EOS;
	
	$url = 'https://gdata.youtube.com/feeds/api/users/default/favorites';
	$ch = curl_init($url);
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/atom+xml", "Authorization: Bearer $authToken", "GData-Version: 2", "X-GData-Key: key=AI39si5VKSGpFhd3NPrG1YG_roRmtK89dXD2vq76xiiwCFkC55q5wnA2v1-5jwh1ogPoje-6ZFLUvcrfNn1WZUSImNeO4Y4VNQ"));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $videoAtom);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	 
	$response = curl_exec($ch);
	
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	curl_close($ch);
	
	
	//print($httpCode);	
	//print($response);
	   
	if ($httpCode >= 200 && $httpCode < 300) {
	    return true;
	}
	else if ($httpCode == 400 && strpos($response, 'Video already in favorite list') !== false)
	{
		return true;
	}
	else
	{
		return false;
	}
}

?>