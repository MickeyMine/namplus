<?php
if(!isset($_SESSION))
{
	session_start();
}

include ('../config.php');
include ('../libs/modules/class.common.php');
include ('../libs/modules/clsDB.php');
include ('../libs/modules/mod_customers.php');
include ('../libs/modules/mod_cus_answers.php');
include ('../libs/modules/mod_offer_vouchers.php');

if(isset($_POST['offerid']) && isset($_SESSION['username']))
{
	$offerid = $_POST['offerid'];
	$uname = $_SESSION['username'];
	
	$modCustomer = new mod_customers();
	
	if(get_magic_quotes_gpc() == false)
	{
		$offerid = mysqli_real_escape_string($modCustomer->clsDb->link, $offerid);
		$uname = mysqli_real_escape_string($modCustomer->clsDb->link, $uname);
	}	
	
	$currCus = $modCustomer->GetCustomerByEmail($uname);
	$modCustomer->closeConnect();
	
	if(count($currCus) == 1)
	{
		$modVouchers = new mod_offer_vouchers();
		$listVoucher = $modVouchers->GetOfferVoucherByOfferId($offerid);
		$modVouchers->closeConnect();
		
		if(count($listVoucher) > 0)
		{
			$sql = 'customer_id = ' . $currCus[0]['customer_id'] . ' and offer_id = ' . $offerid;
			
			$modOfferCusAns = new mod_cus_answers();
			$currOfferCusAns = $modOfferCusAns->GetDataTable($sql, null);
			$modOfferCusAns->closeConnect();
			
			if(count($currOfferCusAns) > 0)
			{
				echo 'false';
			}
			else 
			{
				echo 'true';
			}
		}
		else 
		{
			echo 'Sorry customer!!!';
		}
	}
	else 
	{
		echo 'Can not get user information !!!';
	}
}
else 
{	
	echo 'Please verify your information !!!';
}
?>