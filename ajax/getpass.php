<?php
include ('../config.php');
include ('../libs/modules/class.common.php');
include ('../libs/modules/clsDB.php');
include ('../libs/modules/mod_customers.php');

if(!isset($_SESSION))
{
	session_start();
}

if(isset($_POST['passlogin']))
{
	$password = $_POST['passlogin'];
	
	$modCustomer = new mod_customers();
	
	if(get_magic_quotes_gpc() == false)
	{
		$password = mysqli_real_escape_string($modCustomer->clsDb->link, $password);
	}
	
	$listCus = $modCustomer->CheckCustomer(trim($_SESSION['username']), md5(trim($password)));
	$modCustomer->closeConnect();
	
	if(count($listCus) == 1)
	{
		echo "true";
	}
	else
	{
		echo "false";
	}
}
else
{
	echo "false";
}
?>