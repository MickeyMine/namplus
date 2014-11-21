<script type="text/javascript">
	$(document).ready(function(){
			$.ajax({
				type: 'POST',
				data: {
					newsid: '<?php echo $currNews[0]['new_id'];?>',
				},
				url: '<?php echo BASE_NAME . 'ajax/loadnewscontentonly.php'?>',
				success: function(result){
					$('#new-content2').html(result);
				},
			});
	});
</script>

        
<div id="content-page">
    <h2>
		<?php echo $currNews[0]['new_title'];?>
	</h2>
	<div class="new-description">
	<?php
	echo $currNews[0]['new_description'];
	?>
	</div>
	
	<!-- Paging here -->
	<div class="tabs-line-div">
    	<ul>    
    	   <li>
    			<div class="div-content-next">
    				<div class="content-next-box-small">
    					<div class="content-next-box-small-left"></div>
    					<div class="content-next-box-small-main" style="padding-left: 3px; padding-right: 3px;">
    						<a href="<?php echo $current_page_URL;?>" rel="<?php echo $currNews[0]['new_id'];?>" style="font-size: 0.8em;">
        					<?php 
        					echo '1';
        					?>						
    						</a>		
    					</div>
    					<div class="content-next-box-small-right"></div>
    				</div>
    			</div>
    		</li>
    	<?php
        $listNews = $modNews->GetNewsFollowLink($currNews [0]['new_id']);
        
        $countNews = count($listNews);
        
        if($countNews > 0)
        {
        ?>
    		
    		<?php 
    		$count = 2;
    		foreach ($listNews as $news)
    		{
    			$subURL = substr($current_page_URL, 0, -1) . '_' . $news['new_id'] . '/';
    		?>
    		<li>
    			<div class="div-content-next">
    				<div class="content-next-box-small">
    					<div class="content-next-box-small-left"></div>
    					<div class="content-next-box-small-main" style="padding-left: 3px; padding-right: 3px;">
    						<a href="<?php echo $subURL;?>" rel="<?php echo $news['new_id'];?>" style="font-size: 0.8em;">
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
    			$count ++;
    		}
    		?>
    	
        <?php 
        }
        ?>
        </ul>
    </div>
	<div id="new-content2">
	</div>
	
	<!-- Next button here -->
	<div style="clear: both; height: 1px;">
    		&nbsp;
    </div>
	<div class="content-next-news">
    	<div class="div-content-next" id="content-next">
    		<div class="content-next-box">
    			<div class="content-next-box-left"></div>
    			<div class="content-next-box-main">
    			<?php 
    				echo 'XEM TIẾP';
    			?>
    			</div>
    			<div class="content-next-box-right"></div>
    		</div>
    	</div>
    </div>
    <div style="clear: both; height: 2px;">
    		&nbsp;
    </div>
</div>