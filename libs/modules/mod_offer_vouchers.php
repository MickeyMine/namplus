<?php
class mod_offer_vouchers
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
		$sql = "SELECT * FROM `offer_vouchers` WHERE `voucher_status` = 1";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetOfferVoucherByOfferId($offerId)
	{
		$sql = "SELECT * FROM `offer_vouchers` WHERE `voucher_offer_id` = " . $offerId . " and `voucher_status` = 1";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	public  function GetDataTable($where, $sort)
	{
		$sql = 	"SELECT * FROM `offer_vouchers`";
	
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