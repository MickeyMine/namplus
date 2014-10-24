<?php
if(!isset($_SESSION))
{
	session_start();
}

include ('../config.php');
include ('../libs/modules/class.common.php');
include ('../libs/modules/clsDB.php');
include ('../libs/modules/mod_users.php');

if(isset($_POST['username']) && isset($_POST['password']))
{
	$user = $_POST['username'];
	$pass = $_POST['password'];
	
	$modUser = new mod_users();
	
	if(get_magic_quotes_gpc() == false)
	{
		$user = mysqli_real_escape_string($modUser->clsDb->link, $user);
		$pass = mysqli_real_escape_string($modUser->clsDb->link, $pass);
	}	
	
	$currUser = $modUser->CheckUser(trim($user), md5(trim($pass)));
	
	$modUser->closeConnect();
	
	if(count($currUser) == 1)
	{
		$_SESSION['useradmin'] = $user;
		echo 'true';
	}
	else 
	{
		echo 'Your can not login this page !!';
	}
}
else 
{
	echo 'Verify your information !!';
}
?>