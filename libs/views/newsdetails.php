<script type="text/javascript">
	$(document).ready(function(){
		image_also_resize('.also-like-inner-box');
	});

	/* How to use with Window Load (For Webkit Browser like safari and Chrome) */	
	$(window).load(function () {			
		image_also_resize('.also-like-inner-box');
	});

	
	/* How to use on Window resize */	
	$(window).resize(function () {	
		image_also_resize('.also-like-inner-box');
	});
	
</script>
<div class="new-content">
	<div class="new-context">
	<?php 
	$contentTitle = '';
	$contentDesc = '';
	
	if(isset($_GET['pItem']))
	{
		$arr = split('-', $_GET['pItem']);
		$id = $arr[count($arr) - 1];
		
		$modNews = new mod_news();
		$currNews = $modNews->GetNewsById($id);		
		
		if(count($currNews) == 1)
		{
			$contentTitle = $currNews[0]['new_title'];
			$contentDesc = $currNews[0]['new_description'];
			
			$newsType = $currNews[0]['new_type'];
			//echo $newsType;
			if($newsType == 0 || $newsType == 3)
			{
				include 'newstemplatedefault.php';
			}
			else if($newsType == 1)
			{
				include 'newstemplate1.php';
			}
			else 
			{
				include 'newstemplate2.php';
			}			
			
	?>
			
			<div id="fb-root"></div>
			<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
			<fb:comments href="<?php echo $current_page_URL;?>" num_posts="5"  width="100%"></fb:comments>
	<?php 
		}
		else 
		{
			echo 'Can not get this news !!';
		}
		
		$modNews->closeConnect();
	}
	else 
	{
		echo 'Please verify your information !!';
	}
	?>
	</div>
</div>
<?php 
	$modNews = new mod_news();
	$alsoLike = $modNews->GetNewsAlsoLike($contentTitle, $contentDesc);
	
	if(count($alsoLike) > 0)
	{
		if(count($alsoLike) > 4)
		{
			$arr = array_rand($alsoLike, 4);
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
		if($newsItem['new_id'] != $id)
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
    				<img alt="NAM PLUS" src="<?php echo BASE_NAME;?>images/namplus_icon_small.png" />
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
	?>
	</div>
</div>
<div style="clear: both; height: 1px;">
		&nbsp;
</div>
<?php 

	}
}
?>
