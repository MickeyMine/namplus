<?php
require_once 'class.phpmailer.php';
require_once 'class.smtp.php';
/*
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
if($_SESSION== null)
{
	session_start();
}
$config['allowhtml']=true;
*/
$senderName = "NAMPlus";
$senderEmail = "no-reply@namplus.com.vn";


#####################################
# Function to send email
#####################################
function sendEmail ($fromName, $fromEmail, $toEmail, $subject, $emailBody) {
	$mail = new PHPMailer();

	//$mail->isSMTP();
	try {
		/*
		$mail->Host = "mail.namplus.com.vn";
		$mail->SMTPDebug = 2;

		$mail->SMTPAuth = "true";
		$mail->SMTPSecure = "tls";
		$mail->Host = "mail.namplus.com.vn";
		$mail->Port = 587;

		$mail->Username = "fi5pedmw";
		$mail->Password = ":Qux4;$frnry@:ms";
		*/
		//$mail->setFrom($fromEmail, $fromName);
		$mail->FromName = $fromName;
		$mail->From = $fromEmail;
		$mail->AddAddress("$toEmail");

		$mail->CharSet = "utf-8";
		$mail->Subject = $subject;
		$mail->Body = $emailBody;
		$mail->isHTML(true);
		$mail->WordWrap = 150;
		$mail->send();

		return true;
	}
	catch (phpmailerException $eMail)
	{
		return $eMail->errorMessage();
	}
	catch (Exception $e){
		return $e->getMessage();
	}
	return false;
}

#####################################
# Function to Read a file
# and store all data into a variable
#####################################
function readTemplateFile($FileName) {
		$fp = fopen($FileName,"r") or exit("Unable to open File ".$FileName);
		$str = "";
		while(!feof($fp)) {
			$str .= fread($fp,1024);
		}
		return $str;
}

?>
