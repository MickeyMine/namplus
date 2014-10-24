<?php
if(!isset($_SESSION))
{
	session_start();
}

include ('../config.php');
include ('../libs/modules/class.common.php');
include ('../libs/modules/clsDB.php');
include ('../libs/modules/mod_customers.php');

if(isset($_POST['email']) && isset($_POST['password']))
{
	$email = $_POST['email'];
	$pass = $_POST['password'];

	$modCustomer = new mod_customers();
	
	if(get_magic_quotes_gpc() == false){
		$email = mysqli_real_escape_string($modCustomer->clsDb->link, $email);
		$pass = mysqli_real_escape_string($modCustomer->clsDb->link, $pass);
	}
	
	$pass = md5($pass);	
	
	$currCustomer = $modCustomer->CheckCustomer(trim($email), trim($pass));
	$modCustomer->closeConnect();
	
	if(count($currCustomer) == 1)
	{
		if($currCustomer[0]['customer_status'] == 1)
		{
			echo 'block';
		}
		else 
		{
			$_SESSION['username'] = $email;
			echo 'true';
		}
	}
	else 
	{
		echo 'false';
	}
}
else
{
	echo 'false';
}
?>