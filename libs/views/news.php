<?php 
$page = 1;

$maxRecords = MAX_RECORDS;
$totalRecords = 0;
$totalPage = 0;

if(isset($_GET['pSub']))
{
	$arr = split('-', $_GET['pSub']);
	$catId = $arr[count($arr)-1];
	$sql = 'new_status = 1 and new_link_id IS NULL and ';
	
	$modCat = new mod_categories();
	$selectCat = $modCat->GetCategory($catId);
	$modCat->closeConnect();
	
	if(count($selectCat)>0)
	{
	    if($selectCat[0]['cat_is_gallery'] == 1)
	    {
	        include PATH_CLASS_VIEW . 'newsgallery.php';
	    }
	    else 
	    {
	        include PATH_CLASS_VIEW . 'newscontent.php';
	    }
	}
}
?>