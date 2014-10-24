<?php
$page = 1;

$maxRecords = MAX_RECORDS;
$totalRecords = 0;
$totalPage = 0;

if(isset($_GET['pSub']))
{	
	$modOffers = new mod_offers();
	
	$arr = split('-', $_GET['pSub']);

	$catId = (int)$arr[count($arr) - 1];
	$sql = "offer_status = 1 and offer_end_date >= '"  . date ( 'Y/m/d' ) . "' and offer_cat_id = " . $catId;
	
	$offerList = $modOffers->GetDataTable($sql, "offer_start_date");	
	
	$totalRecords = count($offerList);
	
	$totalPage = ceil($totalRecords/$maxRecords);	
	
	$modOffers->closeConnect();
?>
<script type="text/javascript">
	$(document).ready(function(){
		var page = <?php echo $page;?>;
		
		$.ajax({
			type: 'POST',
			url: '<?php echo BASE_NAME;?>ajax/loadoffers.php',
			data: {
				'page': page,
				'maxrecords': <?php echo $maxRecords;?>,
				'sql': "<?php echo $sql;?>",
				'pSub': "<?php echo $_GET['pSub']; ?>",
			},
			beforeSend: function(){
				$('#nextpage').css('display', 'block');
			},
			success: function(result){
				$('#nextpage').css('display', 'none');

				$('.offer-content').append(result);
				image_also_resize('.rightsection, .leftsection');
			},
		});
		
	});

	/* How to use with Window Load (For Webkit Browser like safari and Chrome) */	
	$(window).load(function () {			
		image_also_resize('.rightsection, .leftsection');
	});

	
	/* How to use on Window resize */	
	$(window).resize(function () {	
		image_also_resize('.rightsection, .leftsection');
	});
</script>

<div class="new-main-content">
	<div class="offer-content">	
		 	 
	</div>
	
	<div id="nextpage" style="display: none">
		<img src="<?php echo BASE_NAME?>images/loading4.gif" alt="Loading ..." />
	</div>
	
	<div id="div-loading" style="padding-top: 2px;">
		<input type="button" id="btnLoadOffer" value="<?php echo ($page >= $totalPage)? 'End page': 'Read more ...'; ?>" 
			<?php if($page >= $totalPage) echo 'disabled = "disabled"';?> />
	</div>
	
	<div style="clear: both; height: 2px;">
			&nbsp;
	</div>
</div>
<?php 
}
?>
		