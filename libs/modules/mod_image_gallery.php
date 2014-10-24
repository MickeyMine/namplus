<?php
class mod_image_gallery
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
	
	function getAllImages()
	{
		$sql = "select * from image_gallery where img_status = 1";
		
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function getImageGalleryByCatID($catId)
	{
		$sql = "select * from image_gallery where img_status = 1 and img_is_banner = 0 and img_cat_id = " . $catId . " order by img_order";
		
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function getImageGalleryByNewID($newId)
	{
		$sql = "select * from image_gallery where img_status = 1 and img_is_banner = 0 and img_new_id = " . $newId . " order by img_order";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function getImageGalleryByOfferID($offerId)
	{
		$sql = "select * from image_gallery where img_status = 1 and img_is_banner = 0 and img_offer_id = " . $offerId . " order by img_order";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function getImageGalleryByNamArchive()
	{
		$sql = "select * from image_gallery where img_status = 1 and img_is_banner = 0 and img_nam_archive = 1";
		
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function getAllImageBanner()
	{
		$sql = "select * from image_gallery where img_status = 1 and img_is_banner = 1 order by img_order";
		
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function getImageGalleryBannerByNewId($newId)
	{
		$sql = "select * from image_gallery where img_status = 1 and img_new_id = " . $newId .
			" and img_is_banner = 1 order by img_order";
		
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function getImageGalleryBannerByOfferId($offerId)
	{
		$sql = "select * from image_gallery where img_status = 1 and img_offer_id = " . $offerId . 
			" and img_is_banner = 1 order by img_order";
		
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function getImageGalleryBannerByCatId($catId)
	{
		$sql = "select * from image_gallery where img_status = 1 and img_cat_id = " . $catId . 
			" and img_is_banner = 1 order by img_order";
		
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function getImageGalleryBannerByNamArchive()
	{
		$sql = "select * from image_gallery where img_status = 1 and img_nam_archive = 1 and img_is_banner = 1 order by img_order";
		
		return $this->clsDb->fetchAllArray($sql);
	}
	
	public  function GetDataTable($where, $sort)
	{
		$sql = 	"SELECT * FROM `image_gallery`";
	
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
		$sql = 	"select * from `image_gallery`";
	
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