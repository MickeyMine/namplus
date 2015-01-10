<?php
include ('../config.php');
include ('../libs/modules/clsDB.php');
include ('../libs/modules/class.common.php');
include ('../libs/modules/mod_news.php');

//$page = 1;
if(isset($_POST['page']) && isset($_POST['maxrecords']) && isset($_POST['sql']))
{
	$page = $_POST['page'];	
	$maxRecords = $_POST['maxrecords'];
	
	$from = ($page - 1) * $maxRecords;
	
	$sql = $_POST['sql'];
	
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
	
	//echo ('<script>alert("sql : ' . $sql . '-' . $isLeft . ' - from : ' . $from . '");</script>');
	
	$modNews = new mod_news();
	$listNew = $modNews->GetDataTableLimit($sql, 'new_publish_date desc', $from, $maxRecords);	

	$modNews->closeConnect();
	
	if(count($listNew) > 0)
	{
		$clsCommon = new SBD_Common();
		
		foreach ($listNew as $new)
		{
			$link = BASE_NAME . 'news/' . $_POST['pSub'] . '/' . $clsCommon->text_rewrite($new['new_title']) . '-' . $new['new_id'] . '/';		
?>
			<article class="new-article">
				<section class="<?php echo ($isLeft)?'new-content-left':'new-content-right';?>">
					<div class="<?php echo ($isLeft)?'section-left':'section-right';?>">
						<div class="display-table">
							<div class="display-table-cell">
								<div class="div-new-content">
								    <a href="<?php echo $link;?>">
									<?php
									echo '<h3 class="title-new-content">' . $new['new_title'].'</h3>';
									echo '<span class="iphone-only">' . $new['new_description'] . '</span>';
									?>
									</a>
								</div>
							</div>
						</div>
					</div>
				</section>
				<section class="<?php echo ($isLeft)?'new-line-right':'new-line-left';?>">
					<div>
		    			<img alt="NAM PLUS" src="<?php echo BASE_NAME;?>images/nam-icon-small.png" />
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