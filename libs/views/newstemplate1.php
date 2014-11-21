<script type="text/javascript">
	$(document).ready(function(){
			$.ajax({
				type: 'POST',
				data: {
					newsid: '<?php echo $currNews[0]['new_id'];?>',
				},
				url: '<?php echo BASE_NAME . 'ajax/loadnewscontent.php'?>',
				success: function(result){
					$('#content-page').html(result);
				},
			});
	});
</script>
<div id="content-page">

</div>
<?php
$listNews = $modNews->GetNewsFollowLink($currNews [0]['new_id']);

$countNews = count($listNews);

if($countNews > 0)
{
?>
<div style="clear: both; height: 2px;">
		&nbsp;
</div>
<div class="content-next-news">
	<div class="div-content-next" id="content-next">
		<div class="content-next-box">
			<div class="content-next-box-left"></div>
			<div class="content-next-box-main next-template1">
			<?php 
				echo 'start with #' . $countNews;
			?>
			</div>
			<div class="content-next-box-right"></div>
		</div>
	</div>
</div>
<div style="clear: both; height: 2px;">
		&nbsp;
</div>
<div class="tabs-line-div">
	<ul>
		<li>
			<div class="div-content-next">
				<div class="content-next-box-small">
					<div class="content-next-box-small-left"></div>
					<div class="content-next-box-small-main" style="font-size: 0.4em; padding-top: 8px; padding-bottom: 6px;">
						<a href="<?php echo $current_page_URL;?>" class="selected">
							<?php 
							echo 'TOP';
							?>						
						</a>	
					</div>
					<div class="content-next-box-small-right"></div>
				</div>
			</div>
		</li>
		<?php 
		$count = $countNews;
		foreach ($listNews as $news)
		{
			$subURL = substr($current_page_URL, 0, -1) . '_' . $news['new_id'] . '/';
		?>
		<li>
			<div class="div-content-next">
				<div class="content-next-box-small">
					<div class="content-next-box-small-left"></div>
					<div class="content-next-box-small-main" style="padding-left: 3px; padding-right: 3px;">
						<a href="<?php echo $subURL;?>" rel="1" style="font-size: 0.6em;">
    					<?php 
    					echo $count;
    					?>						
						</a>		
					</div>
					<div class="content-next-box-small-right"></div>
				</div>
			</div>
		</li>
		<?php 
			$count --;
		}
		?>
	</ul>
</div>
<?php
}
?>
<div style="clear: both; height: 2px;">
		&nbsp;
</div>