<?php
class mod_customers
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
	
	function GetAllCustomers()
	{
		$sql = "SELECT * FROM `customers`";
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetCustomerByID($cusId)
	{
		$sql = "select * from customers where customer_id = " . $cusId ;
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetCustomerByEmail($email)
	{
		$sql = "select * from customers where customer_email = '" . $email . "'";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetCustomerByFacebook($facebookUrl)
	{
		$sql = "select * from customers where customer_facebook = '" . $facebookUrl . "'";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function CheckCustomer($email, $password)
	{
		$sql = "select * from customers where customer_email = '" . $email . "' and customer_pass = '" . $password . "' and customer_status <> -1";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetCustomerProfession()
	{
		$sql = "select * from customers where customer_profession <> NULL";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetCustomerMember()
	{
		$sql = "select * from customers where customer_profession is NULL";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	public function GetDataTable($where, $sort) {
		$sql = "SELECT * FROM `customers`";
	
		if (isset($where)) {
			$sql .= " WHERE " . $where;
		}
		if (isset($sort)) {
			$sql .= " Order by " . $sort;
		}
		return $this->clsDb->fetchAllArray($sql);
	}
	
	public function GetDataTableLimit($where, $sort, $from, $totalRecord) {
		$sql = "select * from `customers`";
	
		if (isset($where)) {
			$sql .= " Where " . $where;
		}
		if (isset($sort)) {
			$sql .= " Order by " . $sort;
		}
		if (isset($from) && isset($totalRecord)) {
			$sql .= " LIMIT " . $from . "," . $totalRecord;
		}
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function InsertCustomer($cusCode, $cusEmail, $cusPass, $cusFirstName, $cusLastName, $cusPro, $cusPhone, $cusAddress, $supscriptionId, $cusFacebook, $cusAuthorId, $cusProvider, $cusPaymentType, $cusStatus)
	{
		$sql = "INSERT INTO `customers`(`customer_id`, `customer_code`, `customer_email`, `customer_pass`, `customer_first_name`, `customer_last_name`, `customer_profession`, `customer_phone`, `customer_address`, `subscription_id`, `customer_facebook`, `customer_author_uid`, `customer_provider`, `customer_payment_type`, `customer_status`, `customer_first_login`)" .
				"VALUES (NULL,'" . $cusCode . "','" . $cusEmail . "','" . $cusPass . "','" . $cusFirstName . "','" . $cusLastName . "','" . $cusPro . "','" . $cusPhone . "','" . $cusAddress . "'," . $supscriptionId . ",'" . $cusFacebook . "', '" . $cusAuthorId . "', '" . $cusProvider . "', " . $cusPaymentType . "," . $cusStatus . ",0)";
	
		$this->clsDb->getdata($sql);
		if($this->clsDb->result)
		{
			return true;
		}
		return false;
	}
	
	function DeleteCustomerById($id)
	{
		$sql = "DELETE FROM `customers` WHERE customer_id =" . $id ;
	
		$this->clsDb->getdata($sql);
		if($this->clsDb->result)
		{
			return true;
		}
		return false;
	}
	
	function DeleteCustomer($cusEmail)
	{
		$sql = "DELETE FROM `customers` WHERE customer_email ='" . $cusEmail . "'";
	
		$this->clsDb->getdata($sql);
		if($this->clsDb->result)
		{
			return true;
		}
		return false;
	}
	
	function UpdateCustomer($cusCode, $cusEmail, $cusPass, $cusFirstName, $cusLastName, $cusPro, $cusPhone, $cusAddress, $supscriptionId, $cusFacebook, $cusAuthorId, $cusProvider, $cusPaymentType, $cusStatus, $cusFirstLogin)
	{
		$sql = "UPDATE `customers` SET" ;
		//" `customer_pass`=[value-4],`customer_first_name`=[value-5],`customer_last_name`=[value-6],`customer_profession`=[value-7],`customer_phone`=[value-8],`customer_address`=[value-9],`supsctiption_id`=[value-10],`customer_facebook`=[value-11],`customer_status`=[value-12] WHERE `customer_email`=[value-1]";
	
		if($cusCode != '')
		{
			$sql .= " `customer_code` = '" . $cusCode . "',";
		}
		if($cusPass != '')
		{
			$sql .= " `customer_pass`='" . $cusPass ."',";
		}
		if($cusFirstName != '')
		{
			$sql .= " `customer_first_name`='". $cusFirstName . "', ";
		}
		if($cusLastName != '')
		{
			$sql .= " `customer_last_name`='". $cusLastName . "', ";
		}
		if($cusPro != '')
		{
			$sql .= " `customer_profession`='" . $cusPro . "', " ;
		}
		if($cusPhone != '')
		{
			$sql .= " `customer_phone`='" . $cusPhone . "', " ;
		}
		if($cusAddress != '')
		{
			$sql .= " `customer_address`='" . $cusAddress . "', " ;
		}
		if($supscriptionId != '')
		{
			$sql .= " `subscription_id`=" . $supscriptionId . ", " ;
		}
		if($cusFacebook != '')
		{
			$sql .= " `customer_facebook`='" . $cusFacebook . "', " ;
		}
		if($cusAuthorId != '')
		{
			$sql .= " `customer_author_uid`='" . $cusAuthorId ."', ";
		}
		if($cusProvider != '')
		{
			$sql .= " `customer_provider`='" . $cusProvider ."', ";
		}
		if($cusPaymentType != '')
		{
			$sql .= " `customer_payment_type`=" . $cusPaymentType . ", " ;
		}
		if($cusStatus != '')
		{
			$sql .= " `customer_status`=" . $cusStatus . ", " ;
		}
		if($cusFirstLogin != '')
		{
			$sql .= " `customer_first_login`=" . $cusFirstLogin . ", " ;
		}
	
		$sql = substr($sql, 0, -2);
	
		$sql .= " WHERE customer_email='" . $cusEmail . "'";
	
		$this->clsDb->getdata($sql);
		if($this->clsDb->result)
		{
			return true;
		}
		return false;
	}
	
}
?>