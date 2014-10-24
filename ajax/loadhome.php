<?php
include ('../config.php');
include ('../libs/modules/clsDB.php');
include ('../libs/modules/class.common.php');
include ('../libs/modules/mod_categories.php');
include ('../libs/modules/mod_news.php');

//$page = 1;
if(isset($_POST['page']) && isset($_POST['maxrecords']))
{
	$page = $_POST['page'];
	$maxRecords = $_POST['maxrecords'];

	$from = ($page - 1) * $maxRecords;
	
	$isLeft = false;
	if(($maxRecords % 2) == 0)
	{
		$isLeft = true;
	}
	else
	{
		if($page%2 == 1)
		{
			$isLeft = true;
		}
		else
		{
			$isLeft = false;
		}
	}
	
	$modNews = new mod_news();
	$sql = 'new_status = 1 and new_link_id IS NULL';
	$listNew = $modNews->GetDataTableLimit($sql, 'new_publish_date desc', $from, $maxRecords);
	
	$modNews->closeConnect();
	
	if(count($listNew) > 0)
	{
		$clsCommon = new SBD_Common();
		
		$modCategories = new mod_categories();
		foreach ($listNew as $new)
		{			
			$currCategory = $modCategories->GetCategory($new['new_cat_id']);
			
			$pathCat = $clsCommon->text_rewrite($currCategory[0]['cat_name']) . '-' . $currCategory[0]['cat_id'];
			
			$link = BASE_NAME . 'news/' . $pathCat . '/' . $clsCommon->text_rewrite($new['new_title']) . '-' . $new['new_id'] . '/';		
?>
			<article class="new-article">
				<section class="<?php echo ($isLeft)?'new-content-left':'new-content-right';?>">
					<div class="<?php echo ($isLeft)?'section-left':'section-right';?>">
						<div class="display-table">
							<div class="display-table-cell">
								<div class="div-new-content">
									<div class="title-new-cat"><?php echo $currCategory[0]['cat_name'];?></div>
									<h3 class="title-new-content">
										<?php echo $new['new_title'];?>
									</h3>
									<div class="title-new-context">
										<?php									
										echo $new['new_description'];
										?>
									</div>
									
								</div>
							</div>
						</div>
					</div>
				</section>
				<section class="<?php echo ($isLeft)?'new-line-right':'new-line-left';?>">
					<div>
		    			<img alt="NAM PLUS" src="<?php echo BASE_NAME;?>images/namplus_icon_small.png" />
					</div>	
				</section>
				<section class="<?php echo ($isLeft)?'new-image-right':'new-image-left';?>">
					<a href="<?php echo $link;?>">
		    			<img class="img-new" alt="<?php echo $new['new_title'];?>" src="<?php echo BASE_NAME . 'uploads/' . $new['new_img_path'];?>" />
		    		</a>
				</section>
			</article>
<?php	
			$isLeft = !$isLeft;
		}
		$modCategories->closeConnect();
	}
	else 
	{
		echo '<span class="message-span">Do not has any news !</span>';
	}
}
else
{
	echo '<span class="message-span">Can not get information !</span>';
}
?>
