<?php
class mod_register_form
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
	
	function GetAllRegisterForm()
	{
		$sql = "select * from register_form";

		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetRegisterMember()
	{
		$sql = "select * from register_form where register_type = 0";
		
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetRegisterProfession()
	{
		$sql = "select * from register_form where register_type = 1";
	
		return $this->clsDb->fetchAllArray($sql);
	}
}
?>