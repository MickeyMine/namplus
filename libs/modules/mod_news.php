<?php
class mod_news
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

	function GetAllNews()
	{
		$sql = "select * from news where new_status = 1 and new_link_id IS NULL order by new_publish_date desc";

		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetNewsById($newsID)
	{
		$sql = "select * from news where new_status = 1 and new_id = " . $newsID;
		
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetNewsByLinkId($newsID)
	{
		$sql = "select * from news where new_status = 1 and new_link_id = " . $newsID . " order by new_link_order asc";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetNewsByCatID($catId)
	{
		$sql = "select * from news where new_status = 1 and new_link_id IS NULL and new_cat_id = " . $catId . " order by new_publish_date desc";
		
		return $this->clsDb->fetchAllArray($sql);
	}

	function GetNewsFollowLink($linkId)
	{
		$sql = "select * from news where new_status = 1 and new_link_id = " . $linkId . " order by new_link_order desc";
		
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetNewsAlsoLike($contentTitle, $contentDesc, $listNewsId)
	{
		$sql = "select * from news n where n.new_status = 1 and n.new_link_id IS NULL and" ;
        
		if(isset($listNewsId))
		{
		    $sql .= " n.new_id not in (" . $listNewsId . ") and";
		}
		$sql .=	" n.new_id in (select new_id from news where new_cat_id = n.new_cat_id or new_title like N'%" . 
		  $contentTitle . "%' or new_description like N'%" . $contentTitle . "%' or new_title like N'%" . 
		  $contentDesc . "%' or new_description like N'%" . $contentDesc . "%') order by new_publish_date desc";
		
		return $this->clsDb->fetchAllArray($sql);
	}
	
	public  function GetDataTable($where, $sort)
	{
		$sql = 	"SELECT * FROM `news`";
	
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
		$sql = 	"select * from `news`";
	
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