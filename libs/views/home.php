<?php 
$page = 1;

$maxRecords = MAX_RECORDS;
$totalRecords = 0;
$totalPage = 0;

//$modCategories = new mod_categories();

$modNews = new mod_news();

$newList = $modNews->GetAllNews();
$totalRecords = count($newList);

$totalPage = ceil($totalRecords/$maxRecords);

$modNews->closeConnect();
?>
<script type="text/javascript">
	$(document).ready(function(){
		var page = <?php echo $page;?>;
		
		$.ajax({
			type: 'POST',
			url: '<?php echo BASE_NAME;?>ajax/loadhome.php',
			data: {
				'page': page,
				'maxrecords': <?php echo $maxRecords;?>
			},
			beforeSend: function(){
				$('#nextpage').css('display', 'block');
			},
			success: function(result){
				$('#nextpage').css('display', 'none');

				$('.new-content').append(result);
				image_resize('.new-image-right, .new-image-left', '.also-like-inner-box');
			},
		});

		

		var load = 1;		
		var totalPage = <?php echo $totalPage;?>;

		$('#btnLoadContent').click(function(e){
			e.preventDefault();
			
			//alert('Come here <?php echo $page;?>');
			load ++;
			
			//alert('load page : ' + load + ' - total page : ' + totalPage);
			
			if(load <= totalPage)
			{
				$('#nextpage').css('display', 'block');
				
				$.ajax({
					type: 'POST',
					url: '<?php echo BASE_NAME;?>ajax/loadhome.php',
					data: {
						'page': load,
						'maxrecords': <?php echo $maxRecords;?>
					},
					beforeSend: function(){
						$('#nextpage').css('display', 'block');
					},
					success: function(result){
						$('#nextpage').css('display', 'none');

						$('.new-content').append(result);
						image_resize('.new-image-right, .new-image-left', '.also-like-inner-box');
					},
				});
			}

			//image_resize('.new-image-right, .new-image-left', '.also-like-inner-box');
			
			if(load < totalPage)
			{
				$(this).val('Read more');
			}
			else
			{
				$(this).val('End page');
				$(this).attr('disabled', 'disabled');
			}
		});
	});

	/* How to use with Window Load (For Webkit Browser like safari and Chrome) */	
	$(window).load(function () {			
		image_resize('.new-image-right, .new-image-left', '.also-like-inner-box');
	});

	
	/* How to use on Window resize */	
	$(window).resize(function () {	
		image_resize('.new-image-right, .new-image-left', '.also-like-inner-box');
	});
	
</script>

<div class="new-main-content">
	<div class="new-content">	
		 	 
	</div>
	
	<div id="nextpage" style="display: none">
		<img src="<?php echo BASE_NAME?>images/loading4.gif" alt="Loading ..." />
	</div>
	
	<div id="div-loading" style="padding-top: 2px;">
		<input type="button" id="btnLoadContent" value="<?php echo ($page >= $totalPage)? 'End page': 'Read more ...'; ?>" 
			<?php if($page >= $totalPage) echo 'disabled = "disabled"';?> />
	</div>
	
	<div style="clear: both; height: 2px;">
			&nbsp;
	</div>
</div>
