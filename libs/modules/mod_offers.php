<?php
class mod_offers {
	public $clsDb;
	function __construct() 
	{
		$this->clsDb = new clsDB();
	}
	function closeConnect() 
	{
		$this->clsDb->close();
	}
	function GetAllOffers() {
		$sql = "SELECT * FROM `offers` WHERE offer_status = 1";
		
		return $this->clsDb->fetchAllArray($sql);
	}	
	function GetOfferAvailable() {
		$sql = "SELECT * FROM `offers` WHERE offer_status = 1 and offer_end_date >= '" . date ( 'Y/m/d' ) . "'";
		
		return $this->clsDb->fetchAllArray($sql);
	}
	function GetOfferById($offerId)
	{
		$sql = "SELECT * FROM `offers` WHERE offer_status = 1 and offer_end_date >= '" . date ( 'Y/m/d' ) . "' and offer_id = " . $offerId;
		
		return $this->clsDb->fetchAllArray($sql);
	}
	function GetOffersByCatId($catId)
	{
		$sql = "SELECT * FROM `offers` WHERE offer_status = 1 and offer_cat_id = " . $catId . " and offer_end_date >= '" . date ( 'Y/m/d' ) . "'";
		
		return $this->clsDb->fetchAllArray($sql);
	}
	
	public function GetDataTable($where, $sort) {
		$sql = "SELECT * FROM `offers`";
		
		if (isset($where)) {
			$sql .= " WHERE " . $where;
		}
		if (isset($sort)) {
			$sql .= " Order by " . $sort;
		}		
		return $this->clsDb->fetchAllArray($sql);
	}
	
	public function GetDataTableLimit($where, $sort, $from, $totalRecord) {
		$sql = "select * from `offers`";
		
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
}
?>