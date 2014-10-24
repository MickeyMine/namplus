<?php

if(!isset($_SESSION))
{
	session_start();
}

$app_id = FACEBOOK_APP_ID;
$app_secret = FACEBOOK_APP_SECRET;
$site_url = 'http://www.namplus.com.vn/index.php';

try{
	require_once 'facebook.php';
}
catch (Exception $e)
{
	
}

$facebook = new Facebook(array(
	'appId' => $app_id,
	'secret' => $app_secret
));

$user = $facebook->getUser();

if($user)
{
	$logout_url = $facebook->getLogoutUrl();
}
else 
{
	$login_url = $facebook->getLoginUrl(array(
		'scope'         => 'email, read_stream, publish_stream, user_birthday, user_location, user_work_history, user_hometown, user_photos',
		//'scope'			=> 'read_stream, publish_stream, user_birthday, user_location, user_work_history, user_hometown, user_photos',
		'redirect_uri'	=> $site_url,
		));
}

?>