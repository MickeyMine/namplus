<?php
class mod_payment_type
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
	
	function GetAllPaymentType()
	{
		$sql = "select * from payment_type";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetPaymentTypeById($id)
	{
		$sql = "select * from payment_type where payment_id=" . $id;
		 
		return $this->clsDb->fetchAllArray($sql);
	}
}
?>