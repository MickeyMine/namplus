<?php 
    if(isset($_SESSION['like-also']))
    {
        $listNewsId = substr($_SESSION['like-also'], 0, -1);
    }
    
	$modNews = new mod_news();
	$alsoLike = $modNews->GetNewsAlsoLike($contentTitle, $contentDesc, $listNewsId);
	
	if(count($alsoLike) > 0)
	{
		if(count($alsoLike) > 4)
		{
			$arrTemp = array_rand($alsoLike, 4);
			$arr = array();
			foreach ($arrTemp as $idArr)
			{
			    $arr[] = $alsoLike[$idArr];
			}
		}
		else
		{
			$arr = $alsoLike;
		}
?>
<div class="also-like">
	<div class="also-like-title">
		you may also like :
	</div>
	<div class="also-like-main">
	<?php 
	$clsCommon = new SBD_Common();
	
	$modCategories = new mod_categories();
		
	foreach ($arr as $newsItem)
	{
		if($newsItem['new_id'] != '' && $newsItem['new_id'] != $id)
		{
		$currCategory = $modCategories->GetCategory($newsItem['new_cat_id']);
			
		$pathCat = $clsCommon->text_rewrite($currCategory[0]['cat_name']) . '-' . $currCategory[0]['cat_id'];
			
		$link = BASE_NAME . 'news/' . $pathCat . '/' . $clsCommon->text_rewrite($newsItem['new_title']) . '-' . $newsItem['new_id'] . '/';
	?>
		<article class="also-like-article">
			<section class="also-like-box">
				<div class="also-like-inner-box">
					<a href="<?php echo $link;?>">
		    			<img class="also-like-img" alt="my offer" src="<?php echo BASE_NAME . 'uploads/' . $newsItem['new_img_path'] ;?>" />
		    		</a>
				</div>
			</section>
			<section class="also-like-line">
				<div>
    				<img alt="NAM PLUS" class="desktop-only" src="<?php echo BASE_NAME;?>images/nam-icon-small.png" />
    				<img alt="NAM PLUS" class="device-only" src="<?php echo BASE_NAME;?>images/nam-icon-small-16.png" />
				</div>	
			</section>
			<section class="also-like-box">
				<div class="also-like-inner-box">
					<div class="display-table">
						<div class="display-table-cell">
							<div class="div-new-content">
								<div class="title-new-cat"><?php echo $currCategory[0]['cat_name'];?></div>
								<h3 class="title-new-content">
									<?php echo $newsItem['new_title'];?>
								</h3>
								<div class="title-new-context">
									<?php									
									echo $newsItem['new_description'];
									?>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</section>
		</article>
	<?php 
		}
	}
	?>
	</div>
</div>
<div style="clear: both; height: 1px;">
		&nbsp;
</div>
<?php 

	}

?>