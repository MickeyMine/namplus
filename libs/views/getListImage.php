<?php
$listImage = array();
$modImageGallery = new mod_image_gallery();

if(!isset($_GET['p']) || $_GET['p'] == 'manager')
{
	$listImage = $modImageGallery->getAllImageBanner();
}
else if(isset($_GET['p']))
{
	$p = $_GET['p'];
	
	if(isset($_GET['pSub']))
	{
		$p = $_GET['pSub'];
	}

	if($p == 'contact-us' || $p == 'register' || $p == 'nam-archive' || $p == 'my-account' || $p == 'change-pass')
	{
		$href .= $p;
		$hrefName = str_replace('-', ' ', $p);

		if($p == 'nam-archive')
		{
			$listImage = $modImageGallery->getImageGalleryBannerByNamArchive();
		}
		else
		{
			$listImage = $modImageGallery->getAllImageBanner();
		}
	}
	else
	{
		$arr = split('-', $p);
		$id = $arr[count($arr) - 1];
			
		$modCategories = new mod_categories();
		$currCate = $modCategories->GetCategory($id);
			
		if(count($currCate) === 1)
		{
			if($currCate[0]['cat_is_offer'] == 0 && $currCate[0]['cat_is_competition'] == 0)
			{
				$href .=  'news/';
			}
			else
			{
				$href .=  'offers/';
			}
			$href .= $clsCommon->text_rewrite($currCate[0]['cat_name']) . '-' . $currCate[0]['cat_id'] . '/' ;
			 
			$hrefName = $currCate[0]['cat_name'];
		}
			
		$modCategories->closeConnect();
			
		if(isset($_GET['pItem']))
		{
			$arr = split('-', $_GET['pItem']);
			$id = $arr[count($arr) - 1];
			//Verify pItem is news or offer
			if($_GET['p'] == 'news')
			{
				$modNews = new mod_news();
				$currNews = $modNews->GetNewsById($id);
				$modNews->closeConnect();
				
				if($currNews[0]['new_type'] == 3)
				{
					$listImage = $modImageGallery->getImageGalleryByNewID($id);
				}
				else 
				{
					$listImage = $modImageGallery->getImageGalleryBannerByNewId($id);
				}
				
				
				$hrefSub = $href . $clsCommon->text_rewrite($currNews[0]['new_title']) . '-' . $currNews[0]['new_id'] . '/';
				$hrefNameSub = $currNews[0]['new_title'];
			}
			else
			{
				$listImage = $modImageGallery->getImageGalleryBannerByOfferId($id);
				
				$modOffers = new mod_offers();
				$currOffer = $modOffers->GetOfferById($id);
				$modOffers->closeConnect();
				
				$hrefSub = $href . $clsCommon->text_rewrite($currOffer[0]['offer_title']) . '-' . $currOffer[0]['offer_id'] . '/';
				$hrefNameSub = $currOffer[0]['offer_title'];
			}			
			
		}
		else 
		{
			$listImage = $modImageGallery->getImageGalleryBannerByCatId($id);
		}
	}
}

$modImageGallery->closeConnect();
?>