<?php
class mod_subscriptions
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
	
	function GetAllSubscriptions()
	{
		$sql = "select * from subscriptions";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetSubscriptionsById($id)
	{
		$sql = "select * from subscriptions where subscription_id = " . $id;
	
		return $this->clsDb->fetchAllArray($sql);
	}
}
?>