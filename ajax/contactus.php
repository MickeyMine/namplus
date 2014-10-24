<?php
session_start();
include('../config.php');
include ('../libs/modules/email.php');
include ('../libs/modules/mod_customers.php');

if(isset($_POST['contactemail']) && isset($_POST['contactname']) && isset($_POST['contacttitle']) && isset($_POST['contactcontent'])
 && isset($_POST['contactcaptcha']))
{
	$email = $_POST['contactemail'];
	$name = $_POST['contactname'];
	$title = $_POST['contacttitle'];
	$content = $_POST['contactcontent'];
	$captcha = $_POST['contactcaptcha'];
	
	$modCus = new mod_customers();
	
	if(get_magic_quotes_gpc() == false)
	{
		$email = mysqli_real_escape_string($modCus->clsDb->link, $email);
		$name = mysqli_real_escape_string($modCus->clsDb->link, $name);
		$title = mysqli_real_escape_string($modCus->clsDb->link, $title);
		$content = mysqli_real_escape_string($modCus->clsDb->link, trim($content));
		$captcha = mysqli_real_escape_string($modCus->clsDb->link, $captcha);
	}
	
	$modCus->closeConnect();
	
	$order   = array("\\r\\n", "\\n", "\\r");
	$replace = "<br />";
	$content = str_replace($order, $replace, $content);
	
	//echo (str_replace($order, $replace,$content));
	
	if($_SESSION['verifycaptcha'] != md5(md5(strtolower(trim($captcha))).'tshirt'))
	{
		echo 'verify';
	}
	else
	{
		//$logo = '<img src="' . BASE_NAME . 'images/banner.png" />';
		$emailBody = readTemplateFile(BASE_NAME . 'template/contactus.html');
	
		//Replace value in template file
		//$emailBody = str_replace('#logo#', $logo, $emailBody);
		$emailBody = str_replace('#email#', $email, $emailBody);
		$emailBody = str_replace('#title#', $title, $emailBody);
		$emailBody = str_replace('#content#', $content, $emailBody);
	
		$now=date('d-m-Y h:i:s A');
		$emailBody = str_replace('#date#', $now, $emailBody);
	
		$emailStatus = sendEmail($name, $email, 'info@namplus.com.vn', 'Contact us from ' . $email, $emailBody);
	
		if ($emailStatus == false) {
			echo "An error occured while sending email. Please try again later.";
		}
		else{
			echo "true";
		}
	}
}
else 
{
	echo "false";
}
?>