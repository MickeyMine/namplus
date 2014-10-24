<?php
class mod_categories
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
	
	function GetAllCategories()
	{
		$sql = "select * from categories where cat_status = 1";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetParentCategories()
	{
		$sql = "select * from categories where cat_status = 1 and cat_parent_id IS NULL order by cat_order";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetCategory($catId)
	{
		$sql = "SELECT * FROM categories WHERE cat_status = 1 AND cat_parent_id IS NOT NULL AND cat_id = " . $catId . " ORDER BY cat_order";
		
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetCategoriesParentId($catParentId)
	{
		$sql = "select * from categories where cat_status = 1 and cat_parent_id = " . $catParentId . " order by cat_order";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	public  function GetDataTable($where, $sort)
	{
		$sql = 	"SELECT * FROM `categories`";
	
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
	
	public  function GetDataTableLimit($where, $sort, $from, $totalRecord)
	{
		$sql = 	"select * from `categories`";
	
		if(isset($where))
		{
			$sql .= " Where " . $where;
		}
		if(isset($sort))
		{
			$sql .= " Order by " . $sort;
		}
		if(isset($from) && isset($totalRecord))
		{
			$sql .= " LIMIT " . $from . "," . $totalRecord;
		}
	
		return $this->clsDb->fetchAllArray($sql);
	}
}
?>