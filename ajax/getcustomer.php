<?php
include ('../config.php');
include ('../libs/modules/class.common.php');
include ('../libs/modules/clsDB.php');
include ('../libs/modules/mod_customers.php');

if(isset($_POST['email']))
{
	$email = $_POST['email'];
	$modCustomer = new mod_customers();
	
	if(get_magic_quotes_gpc() == false)
	{
		$email = mysqli_real_escape_string($modCustomer->clsDb->link, $email);
	}	
	
	$listCus = $modCustomer->GetCustomerByEmail($email);
	
	$modCustomer->closeConnect();
	
	if(count($listCus) == 0)
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