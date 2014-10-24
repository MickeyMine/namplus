<?php
include ('../config.php');
include ('../libs/modules/class.common.php');
include ('../libs/modules/clsDB.php');
include ('../libs/modules/mod_customers.php');

if(isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['Email']) && isset($_POST['facebook']) && isset($_POST['phone']) && isset($_POST['address']))
{
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$email = $_POST['Email'];
	$facebook = $_POST['facebook'];
	$phone = $_POST['phone'];
	$address = $_POST['address'];
	$subscription = $_POST['Subscriptions'];
	$payments = $_POST['Payments'];
	
	$modCus = new mod_customers();
	
	if(get_magic_quotes_gpc() == false)
	{
		$firstname = mysqli_real_escape_string($modCus->clsDb->link, $firstname);
		$lastname = mysqli_real_escape_string($modCus->clsDb->link,$lastname);
		$email = mysqli_real_escape_string($modCus->clsDb->link,$email);
		$facebook = mysqli_real_escape_string($modCus->clsDb->link,$facebook);
		$phone = mysqli_real_escape_string($modCus->clsDb->link,$phone);
		$address = mysqli_real_escape_string($modCus->clsDb->link,$address);
	}
	
	$result = false;
	if(trim($_POST['action']) == 'insert')
	{
		$result = $modCus->InsertCustomer(NULL, $email, '', $firstname, $lastname, '', $phone, $address, $subscription, $facebook, '', '', $payments, -1, 0);
	}
	
	if($result == true){
		echo 'true';
	}
	else 
	{
		echo $modCus->clsDb->error . ' - false';
	}
	
	$modCus->closeConnect();
}
elseif (isset($_POST['firstnamepro']) && isset($_POST['lastnamepro']) && isset($_POST['EmailPro']) && isset($_POST['profession']) && isset($_POST['phonepro']) && isset($_POST['facebookpro']))
{
	$firstname = $_POST['firstnamepro'];
	$lastname = $_POST['lastnamepro'];
	$profession = $_POST['profession'];
	$phone = $_POST['phonepro'];
	$email = $_POST['EmailPro'];
	$facebook = $_POST['facebookpro'];	

	$modCus = new mod_customers();
	
	if(get_magic_quotes_gpc() == false)
	{
		$firstname = mysqli_real_escape_string($modCus->clsDb->link,$firstname);
		$lastname = mysqli_real_escape_string($modCus->clsDb->link,$lastname);
		$profession = mysqli_real_escape_string($modCus->clsDb->link,$profession);
		$phone = mysqli_real_escape_string($modCus->clsDb->link,$phone);
		$email = mysqli_real_escape_string($modCus->clsDb->link,$email);
		$facebook = mysqli_real_escape_string($modCus->clsDb->link,$facebook);
	}
	
	$result = false;
	if(trim($_POST['action']) == 'insert')
	{
		$result = $modCus->InsertCustomer(NULL, $email, '', $firstname, $lastname, $profession, $phone, '', 0, $facebook , '', '', 1, -1, 0);		
	}
	
	if($result == true){
		echo 'true';
	}
	else
	{
		echo $modCus->clsDb->error . ' - false';
	}
	
	$modCus->closeConnect();
}
else
{
	echo 'false';
}
?>