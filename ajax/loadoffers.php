<?php
include ('../config.php');
include ('../libs/modules/clsDB.php');
include ('../libs/modules/class.common.php');
include ('../libs/modules/mod_categories.php');
include ('../libs/modules/mod_offers.php');

if(isset($_POST['page']) && isset($_POST['maxrecords']) && isset($_POST['sql']))
{
?>
<?php
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
	
	$modOffers = new mod_offers();
	
	$listOffer = $modOffers->GetDataTableLimit($sql, 'offer_start_date', $from, $maxRecords);
	
	$modOffers->closeConnect();
	
	$arr = split('-', $_POST['pSub']);
	
	$modCat = new mod_categories();
	$currCat = $modCat->GetCategory($arr[count($arr) - 1]);
	
	$modCat->closeConnect();
	
	if($currCat[0]['cat_is_offer'] == 1)
	{
		$isOffer = true;
	}
	else if($currCat[0]['cat_is_competition'] == 1)
	{
		$isOffer = false;
	}		
	
	if(count($listOffer) > 0)
	{	
		if($from == 0)
		{
		?>
<script type="text/javascript">
		$('.offer-details').click(function(e){
			e.preventDefault();
			window.location = $(this).attr('rel');
		});
</script>
		
		<?php 
		}	
		$clsCommon = new SBD_Common();

		foreach ($listOffer as $offer)
		{
			$link = BASE_NAME . 'offers/' . $_POST['pSub'] . '/' . $clsCommon->text_rewrite($offer['offer_title']) . '-' . $offer['offer_id'] . '/';
			$title = $offer['offer_title'];
			$description = $offer['offer_description'];
			$image_path = BASE_NAME . 'uploads/' . $offer['offer_image_path'];
		?>
			<article class="offer-article">
				<section class="<?php echo ($isLeft)? 'margin-right' : 'margin-left';?>">
					<div class="<?php echo ($isLeft)? 'sectionright' : 'sectionleft';?>">
						<div class="display-table">
							<div class="display-table-cell">
								<div class="div-offer-content">
									<div class="div-content-next">
										<div class="content-next-box offer-details"   rel="<?php echo $link;?>">
											<div class="content-next-box-left-offer"></div>
											<div class="content-next-box-main-offer">
											<?php 
												echo $isOffer ? 'offers': 'competition';
											?>
											</div>
											<div class="content-next-box-right-offer"></div>
										</div>
									</div>
									<div class="sectiontitle">
						                            <?php echo $title;?>
						            </div>
									<div class="sectiondescription">
						                            <?php echo $description;?>
						            </div>
					            </div>
							</div>
						</div>
					</div>
				</section>
				<section
					class="<?php echo ($isLeft)? 'linesectionright' : 'linesectionleft';?>">
					<div>
						<img src="<?php echo BASE_NAME?>images/namplus_icon_small.png"
							alt="NAM PLUS" />
					</div>
				</section>
				<section
					class="<?php echo ($isLeft)? 'rightsection' : 'leftsection';?>">
					<a href="<?php echo $link;?>"> <img src="<?php echo $image_path; ?>"
						alt="<?php echo $title; ?>" />
					</a>
				</section>
			</article>
<?php 
			$isLeft = !$isLeft;
		}
	}
	else
	{
		echo 'Can not get offer information !!!';
	}
}
?>