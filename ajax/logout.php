<?php

session_start();

try	
{
	if(isset($_SESSION['username']))
	{
		unset($_SESSION['username']);	
	}
	if(isset($_SESSION['useradmin']))
	{
		unset($_SESSION['useradmin']);
	}
	if(isset($user))
	{
		$user = NULL;
	}
	
	session_destroy();
	
	echo 'true';
}
catch (Exception $e)
{
	echo '$e';
}
//header("location:javascript://history.go(-1)");


?>