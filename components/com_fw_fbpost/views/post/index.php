<?php
require_once '../../helpers/facebook/index.php';
$appid 			= $_GET['app_id'];
$appsecret 		= $_GET['secret'];
$content  		= $_GET['content'];
$access_token 	= $_GET['access_token'];
if(!$appid && $appsecret)
{
	echo 3;exit;
}
$facebook 		= new Facebook(array(
    'appId'  => $appid,
    'secret' => $appsecret,
	'cookie' => true ,
	'scope' => 'publish_stream'
    ));
	
//echo $facebook->getAccessToken();exit;
//echo $facebook->getUser();exit;	
try {
	$attachment = array(
			'access_token' => $access_token,
			'message' => $content,
			'name' => 'Read more',
						);
	$result = $facebook->api('/me/feed/','post',$attachment);
	//$result = $facebook->api('/'.$appid.'/feed/', 'post', $attachment);
	if($result)	{
		echo 1;exit;
	}
}
catch (FacebookApiException $e) {
	echo 'valid_token';exit;
}