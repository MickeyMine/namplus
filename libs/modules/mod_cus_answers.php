<?php
class mod_cus_answers
{
	public $clsDb;
	
	function __construct()
	{
		$this->clsDb = new clsDB();
	}
	
	function closeConnect()
	{
		$this->clsDb->close();
	}
	
	function GetAllOfferVoucher()
	{
		$sql = "SELECT * FROM `sbd_offer_vouchers` WHERE `voucher_status` = 1";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetOfferVoucherByOfferId($offerId)
	{
		$sql = "SELECT * FROM `sbd_offer_vouchers` WHERE `voucher_offer_id` = " . $offerId . " and `voucher_status` = 1";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	public  function GetDataTable($where, $sort)
	{
		$sql = 	"SELECT * FROM `sbd_offer_vouchers`";
	
		if(isset($where))
		{
			$sql .= " WHERE " . $where;
		}
		if(isset($sort))
		{
			$sql .= " Order by " . $sort;
		}
	
		return $this->clsDb->fetchAllArray($sql);
	}
}
?>