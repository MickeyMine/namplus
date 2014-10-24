<?php
session_start();
include ('../config.php');
include ('../libs/modules/email.php');
include ('../libs/modules/mod_customers.php');

if(isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['Email']) && isset($_POST['facebookurl']))
{
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$email = $_POST['Email'];
	$facebookUrl = $_POST['facebookurl'];
	
	$modCustomer = new mod_customers();
	
	if(get_magic_quotes_gpc() == false)
	{
		$firstname = mysqli_real_escape_string($modCustomer->clsDb->link, $firstname);
		$lastname = mysqli_real_escape_string($modCustomer->clsDb->link, $lastname);
		$email = mysqli_real_escape_string($modCustomer->clsDb->link, $email);
		$facebookUrl = mysqli_real_escape_string($modCustomer->clsDb->link, $facebookUrl);
	}		
	
	if(isset($_SESSION['username']))
	{
		
		$currCus = $modCustomer->GetCustomerByEmail($_SESSION['username']);
		
		if(count($currCus) == 1)
		{
			$name = $currCus[0]['customer_first_name'] . ' ' . $currCus[0]['customer_last_name'];
			$nameinvite = $firstname . ' ' . $lastname;
			$linkregister = BASE_NAME . 'register/';
			$now=date('d-m-Y h:i:s A');
			
			//Read template
			$emailBody = readTemplateFile(BASE_NAME . 'template/invitefriend.html');
			//Replace token with value
			$emailBody = str_replace('#nameinvite#', $nameinvite, $emailBody);
			$emailBody = str_replace('#name#', $name, $emailBody);
			$emailBody = str_replace('#linkregister#', $linkregister, $emailBody);
			$emailBody = str_replace('#date#', $now, $emailBody);
			
			//Send mail
			$emailStatus = sendEmail('NAMPlus', 'info@namplus.com.vn', $email,  'Invite friend from ' . $name, $emailBody);
			
			if ($emailStatus == false) {
				echo "An error occured while sending email. Please try again later.";
			}
			else{
				echo "true";
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
	
}
else
{
	echo 'false';
}
?>