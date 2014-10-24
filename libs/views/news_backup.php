<?php 
/*
$maxRecords = MAX_RECORDS;

if($maxRecords % 2 == 0)
{
	$isLeft = true;
}
else 
{
	if($page % 2 == 0)
	{
		$isLeft = false;
	}
	else 
	{
		$isLeft = true;
	}
}
*/
$page = 1;
$maxRecords = MAX_RECORDS;
$totalRecords = 0;
$totalPage = 0;

if(isset($_GET['pSub']))
{
	$modNews = new mod_news();
	
	$arr = split('-', $_GET['pSub']);
	
	$sql = 'new_status = 1 and new_id = ' . $arr[count($arr)-1];
	
	$newsList = $modNews->GetDataTable($sql, 'new_publish_date desc');
	
	$totalRecords = count($newsList);
	
	//$totalPage = round($totalRecords/$maxRecords, 0);
	$totalPage = ceil($totalRecords/$maxRecords);
	
	$modNews->closeConnect();
?>
<script type="text/javascript">
	//image_resize('.new-image-right', '.also-like-inner-box');
	
	$(document).ready(function(){
		var page = <?php echo $page;?>;
		
		$.ajax({
			type: 'POST',
			url: '<?php echo BASE_NAME;?>ajax/loadnews.php',
			data: {
				'page': page,
				'maxrecords': <?php echo $maxRecords;?>,
				'sql': '<?php echo $sql;?>',
			},
			beforeSend: function(){
				$('#nextpage').css('display', 'block');
			},
			success: function(result){
				$('#nextpage').css('display', 'none');

				$('.new-content').append(result);
			},
		});

		image_resize('.new-image-right, .new-image-left', '.also-like-inner-box');
		
	});

	/* How to use with Window Load (For Webkit Browser like safari and Chrome) */	
	$(window).load(function () {			
		image_resize('.new-image-right, .new-image-left', '.also-like-inner-box');
	});

	
	/* How to use on Window resize */	
	$(window).resize(function () {	
		image_resize('.new-image-right, .new-image-left', '.also-like-inner-box');
		//window.location.reload(true);
	});
	
</script>

<div class="new-content">	
	 
	<article class="new-article">
		<section class="new-content-left">
			<div class="section-left">
				<div class="display-table">
					<div class="display-table-cell">
					<?php
					if(isset($_GET['pSub']))
					{
						echo $_GET['pSub'];
					}
					?>
					</div>
				</div>
			</div>
		</section>
		<section class="new-line-right">
			<div>
    			<img alt="NAM PLUS" src="<?php echo BASE_NAME;?>images/namplus_icon_small.png" />
			</div>	
		</section>
		<section class="new-image-right">
			<a href="/my-offer-1/">
    			<img class="img-new" alt="my offer" src="<?php echo BASE_NAME;?>uploads/helmet.jpg" />
    		</a>
		</section>
	</article>
	<article class="new-article">
		<section class="new-content-right">
			<div class="section-right">
				<div class="display-table">
					<div class="display-table-cell">
					<?php
					if(isset($_GET['pSub']))
					{
						echo $_GET['pSub'];
					}
					?>
					</div>
				</div>
			</div>
		</section>
		<section class="new-line-left">
			<div>
    			<img alt="NAM PLUS" src="<?php echo BASE_NAME;?>images/namplus_icon_small.png" />
			</div>	
		</section>
		<section class="new-image-left">
			<a href="/my-offer-1/">
    			<img class="img-new" alt="my offer" src="<?php echo BASE_NAME;?>uploads/helmet.jpg" />
    		</a>
		</section>
	</article>
	 
	 
</div>

<div id="nextpage" style="display: none">
	<img src="<?php echo BASE_NAME?>images/loading4.gif" alt="Loading ..." />
</div>

<div id="div-loading">
	<input type="button" id="btnLoadContent" value="dang test" />
</div>

<div style="clear: both; height: 20px;">
		&nbsp;
</div>
<?php 
}
?>

<div class="also-like">
	<div class="also-like-title">
		you may also like :
	</div>
	<div class="also-like-main">
		<article class="also-like-article">
			<section class="also-like-box">
				<div class="also-like-inner-box">
					<a href="/my-offer-1/">
		    			<img class="also-like-img" alt="my offer" src="<?php echo BASE_NAME;?>uploads/helmet.jpg" />
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
						<?php
						if(isset($_GET['pSub']))
						{
							echo $_GET['pSub'];
						}
						?>
						</div>
					</div>
				</div>
			</section>
		</article>
	</div>
</div>
<div style="clear: both; height: 2px;">
		&nbsp;
</div>

