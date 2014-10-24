<?php
include ('../config.php');
include ('../libs/modules/class.common.php');
include ('../libs/modules/clsDB.php');
include ('../libs/modules/mod_customers.php');
include ('../libs/modules/email.php');

session_start();

$modCustomer = new mod_customers();
$modCommon = new SBD_Common();

if(isset($_POST['newpass']))
{
	$newpass = $_POST['newpass'];
	
	$email = $_SESSION['username'];
	
	$flag = 'true';
	if(isset($_POST['oldpass'])){
		$oldpass = $_POST['oldpass'];
		if(get_magic_quotes_gpc() == false)
		{
			$oldpass = mysqli_real_escape_string($modCustomer->clsDb->link, $oldpass);
		}

		$currCus = $modCustomer->GetCustomerByEmail($email);
		
		if(count($currCus) == 1 && ($currCus[0]['customer_pass']) != md5(trim($oldpass)))
		{
			$flag = 'false';
		}			
	}
	//echo 'Come here ' . $flag . ' - email ' . $email;
	
	if($flag == 'true')
	{
		if(get_magic_quotes_gpc() == false)
		{
			$newpass = mysqli_real_escape_string($modCustomer->clsDb->link, $newpass);
		}
			
		$result = $modCustomer->UpdateCustomer('', $_SESSION['username'], md5(trim($newpass)), '', '', '', '', '', '', '', '', '', '', '', 1);
		
		if($result)
		{
			echo 'true';
		}
		else
		{
			echo 'false';
		}
	}
	else 
	{
		echo 'Please verify your password !!';
	}
}
else if(isset($_POST['Status']))
{		
	$email = $_POST['Email'];
		
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname']; 	
	$facebook = $_POST['facebook'];
	$phone = $_POST['phone'];
	
	$address = isset($_POST['address']) ? $_POST['address'] : '';
	$payment = isset($_POST['Payments']) ? $_POST['Payments'] : '';
	$subscription = isset($_POST['Subscriptions']) ? $_POST['Subscriptions'] : '';
	
	$profession = isset($_POST['profession']) ? $_POST['profession'] : '';
	
	$status = $_POST['Status'];
	
	$currCus = $modCustomer->GetCustomerByEmail($email);
	
	$isActive = false;
	
	if(count($currCus) == 1)
	{
		$currStatus = $currCus[0]['customer_status'];
		$isActive = ($currCus == -1) ? true: false;
		
		//Create customer code follow template NAM20140807172530
		$cusCode = $currCus[0]['customer_code'] == '' ? 'NAM' . date("YmdHis") : '';
		
		//Create pass random with length is 7 
		$cusPass = $currCus[0]['customer_pass'] == '' ? $modCommon->rand_string(7) : '';
		
		$result = $modCustomer->UpdateCustomer($cusCode, $email, md5(trim($cusPass)), $firstname, $lastname, $profession, $phone, $address, $subscription, $facebook, '', '', $payment, $status, 0);
		
		if($result && ($isActive == false))
		{
			//Send mail for customer 
			//Change title, template when change status of customers
			$emailBody = readTemplateFile(BASE_NAME . 'template/registersuccess.html');
			$name = $firstname . ' ' . $lastname;
			$emailBody = str_replace('#name#', trim($name) , $emailBody);
			$emailBody = str_replace('#username#', $email , $emailBody);		
			$emailBody = str_replace('#password#', trim($cusPass), $emailBody);
				
			$emailStatusCus = sendEmail('NAMPlus', 'info@namplus.com.vn', $email, 'Register successed', $emailBody);
			
			if($emailStatusCus == true)
			{
				echo 'true';
			}
			else 
			{
				echo 'Send mail error !!';
			}
		}
		else 
		{
			echo 'Update customer information error !!';
		}
	}
	else 
	{
		echo 'Can not get customer information !!';
	}
}
else 
{
	echo 'Verify your information';
}
$modCustomer->closeConnect();
?>