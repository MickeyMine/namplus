<?php
include '../config.php';
include '../libs/modules/clsDB.php';
include '../libs/modules/mod_offers.php';

echo '<div class="message-popup">';
if(isset($_POST['offerid']))
{
	$modOffer = new mod_offers();
	$currOffer = $modOffer->GetDataTable('offer_id = ' . $_POST['offerid'], null);
	$modOffer->closeConnect();
	
	if(count($currOffer) == 1)
	{
		echo $currOffer[0]['offer_rules'];
	}
	else
	{
		echo 'Verify your request !!';
	}
}
else
{
	echo 'Please select offer !!';
}
echo '</div>';
?>