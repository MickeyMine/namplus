<?php
if(!isset($_SESSION))
{
	session_start();
}

include ('../config.php');
include ('../libs/modules/class.common.php');
include ('../libs/modules/clsDB.php');
include ('../libs/modules/mod_offers.php');
include ('../libs/modules/mod_customers.php');

if(isset($_POST['offerid']) && isset($_POST['answer']))
{
	$offerid = $_POST['offerid'];
	$answer = $_POST['answer'];
	
	$modCustomer = new mod_customers();
	
	if(get_magic_quotes_gpc() == false)
	{
		$offerid = mysqli_real_escape_string($modCustomer->clsDb->link, $offerid);
		$answer = mysqli_real_escape_string($modCustomer->clsDb->link, $answer);
	}
	
	echo ('<script>alert("offer id : ' . $offerid .  ' - answer : ' . $answer . '");</script>');
}

//Chuyen trang
?>