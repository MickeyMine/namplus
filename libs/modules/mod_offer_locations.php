<?php
class mod_offer_locations
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
	
	function GetAllLocations()
	{
		$sql = "SELECT * FROM `offer_locations` WHERE `location_status`=1";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetLocationByOfferID($offerid)
	{
		$sql = "SELECT * FROM `offer_locations` WHERE `offer_id` = " . $offerid . " AND `location_status`=1";
	
		return $this->clsDb->fetchAllArray($sql);
	}
}
?>