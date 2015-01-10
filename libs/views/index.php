<?php 
	$href = BASE_NAME;
	$hrefName = 'home';
	
	include PATH_CLASS_VIEW . 'getListImage.php';
	
	//Check image gallery has item ?
	if(count($listImage) <= 0)
	{
		$listImage = array(
				'0' => array(
						'img_id' => '0',
						'img_path' => 'contentimage.jpg',
						'img_description' => 'NAM Plus',
						'img_cat_id' => 'NULL',
						'img_new_id' => 'NULL',
						'img_offer_id' => 'NULL',
						'img_nam_archive' => '0',
						'img_order' => '1',
						'img_status' => '1',
				),
		);
	}
?>

<div class="div-image-head">
	<?php 
	$isGallery = false;
	if(isset($_GET['pItem']))
	{
		$arr = split('-', $_GET['pItem']);
		$id = $arr[count($arr) - 1];
	
		$modNews = new mod_news();
		$currNews = $modNews->GetNewsById($id);
		
		if(count($currNews) == 1)
		{
			if($currNews[0]['new_type'] == 3 && $_GET['p'] == 'news')
			{
				$isGallery = true;
			}
		}		
		$modNews->closeConnect();
	}
	if(!$isGallery)
	{
		$randArray = array_rand($listImage);
		$selectImage = $listImage[$randArray];
	?>
		<img src="<?php echo BASE_NAME . 'uploads/' . $selectImage['img_path'];?>" alt="<?php echo $selectImage['img_description'];?>" />
	<?php 
	}
	else 
	{
	?>
		<script type="text/javascript">
		$(document).ready(function() {
		    $('.ad-gallery').adGallery({
			    update_window_hash: false,
			    enable_keyboard_move: true, 
			    display_next_and_prev: false,
			});

		   $('.ad-image-wrapper').click(function(e){
			   e.preventDefault();
			   $img = $(this).find('.ad-image').find('img');
			   //alert($(this).find('.ad-image').find('img').attr('src'));
			   $pathCurrImage = $img.attr('src').split('/');
			   $.ajax({
				   url: 'ajax/zoomImage.php',
				   type: 'POST',
				   data: {
					   currImage : $pathCurrImage[$pathCurrImage.length - 1],	
					   listImage : <?php echo json_encode($listImage);?>,
					   				   
				   },
				   beforeSend: function(){
						$('#my_popup').popup({
							autoopen: true,
							pagecontainer: '.wrapper-map',
							onopen: function(){
								$('.wrapper-map').html('Loading.....');
							},
						});
					},
				   success: function(result){
					    //alert(result + $img.attr('src'));
					    $('#my_popup').popup('hide');
						
    					$('#my_popup').popup({
    						closeelement: '.my_popup_close',
    						autoopen: true,
    						pagecontainer: '.wrapper-map',
    						onopen: function(){
    							$('.my_popup_close').css('display', 'block');
    							$('.wrapper-map').html(result);
    							$('.wrapper-map').focus();
    						},
    						onclose: function(){
    							$('.my_popup_close').css('display', 'none');
    						}					
    					});
				   },
			   });
		   });
		});
		$(window).resize(function () {	
			window.location.reload(true);
		});
		</script>
		<div class="ad-gallery">
		    <div class="ad-controls"></div>
		    <div class="ad-image-wrapper"></div>
		    <div class="ad-nav">
		        <div class="ad-thumbs">
		            <ul class="ad-thumb-list">
		            <?php 
					foreach ($listImage as $image)
					{
					    $arrImg = split(',', $image['img_path']);
					    foreach ($arrImg as $linkImage)
					    {
					?>
		                <li>
		                    <a href="<?php echo BASE_NAME . 'uploads/' . $linkImage;?>">
		                        <img src="<?php echo BASE_NAME . 'uploads/' . $linkImage;?>" >
		                    </a>
		                </li>
		             <?php 
					    }
					}
		             ?>
		            </ul>
		        </div>
		    </div>
		</div>
	<?php 
	}
	?>
</div>

<div class="page-content">
	<div class="div-sitemap desktop-only">
		<ul class="hasGradient">
			<li>			
				<a href="<?php echo BASE_NAME;?>" class="icon-home">
					<img src="<?php echo BASE_NAME . 'images/home.png'?>" alt="namplus" />
				</a>
			</li>
			<?php 
			if(isset($_GET['p']) && ($_GET['p'] == 'contact-us' || $_GET['p'] == 'register' || $_GET['p'] == 'nam-archive'))
			{
			?>
				<li>			
					<span class="arr-r1">&nbsp;</span>
				</li>
				<li>			
					<a href="<?php echo $href . '/';?>"><?php echo strtoupper($hrefName);?></a>
				</li>
			<?php 
			}
			if(isset($_GET['pSub']))
			{
			?>
			<li>			
				<span class="arr-r1">&nbsp;</span>
			</li>
			<li>			
				<a href="<?php echo $href;?>"><?php echo strtoupper($hrefName);?></a>
			</li>
			<?php 
			}
			?>
			<?php 
			if(isset($_GET['pItem']))
			{
			?>
			<li>			
				<span class="arr-r1">&nbsp;</span>
			</li>
			<li>			
				<a href="<?php echo $hrefSub;?>"><?php echo $hrefNameSub;?></a>
			</li>
			<?php 
			}
			?>
		</ul>
	</div>
<?php
if(isset($_POST['searchcontent']))
{
    include (PATH_CLASS_VIEW . 'searchpage.php');
}
else if(isset($_GET['p']))
{
	$p = $_GET['p'];
	if($p == 'manager')
	{
		include PATH_CLASS_VIEW . 'manager/index.php';
	}
	if($p == 'contact-us')
	{
		include PATH_CLASS_VIEW . 'contactus.php';
	}
	else if($p == 'register')
	{
		include PATH_CLASS_VIEW . 'register.php';
	}
	else if($p == 'nam-archive')
	{
		include PATH_CLASS_VIEW . 'namarchive.php';
	}
	else if($p == 'my-account')
	{
		include PATH_CLASS_VIEW . 'myaccount.php';
	}
	else if($p == 'change-pass')
	{
		include PATH_CLASS_VIEW . 'changepass.php';
	}
	else if($p == 'news' || $p == 'offers')
	{
		$isDetails = false;
		
		if(isset($_GET['pSub']))
		{
			$_GET['pSub'] = $_GET['pSub'];
		}
		if(isset($_GET['pItem'])){
			$_GET['pItem'] = $_GET['pItem'];
			$isDetails = true;
		}
		
		if($isDetails == true)
		{
			include PATH_CLASS_VIEW . $p . 'details.php';
		}
		else
		{
			include PATH_CLASS_VIEW . $p . '.php';
		}	 
	}
	else
	{
		$array = split('-',trim($p));
		if($array[0] == 'invite')
		{
			include PATH_CLASS_VIEW . 'invitefriend.php';
		}
	}
}
else 
{
	include PATH_CLASS_VIEW . 'home.php';
}
?>

</div>