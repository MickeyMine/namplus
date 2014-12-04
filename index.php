<?php
session_start ();

define('IN_SITE', true);

if (!defined('IN_SITE') ){
	die('Hacking Attemp!');
}


require_once 'config.php';
require PATH_CLASS_MODEL . 'clsDB.php';

include PATH_CLASS_MODEL . 'class.common.php';
include PATH_CLASS_MODEL . 'mod_categories.php';
include PATH_CLASS_MODEL . 'mod_image_gallery.php';
include PATH_CLASS_MODEL . 'mod_news.php';
include PATH_CLASS_MODEL . 'mod_offers.php';
include PATH_CLASS_MODEL . 'mod_offer_locations.php';
include PATH_CLASS_MODEL . 'mod_customers.php';
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" /><base href="<?php echo BASE_NAME; ?>" />
	
	<title>NAM Plus</title>
	
	<?php include_once ('metainclude.php');?>
	
	<link href="<?php echo BASE_NAME; ?>favicon.png" rel="shortcut icon" type="image/x-icon" />
	
	<link href="<?php echo BASE_NAME;?>css/styles.css" rel="stylesheet" type="text/css">	
	
	<!-- Ad Gallery CSS -->
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_NAME;?>css/jquery.ad-gallery.css" media="screen" />
	
	<link href="<?php echo BASE_NAME; ?>css/popup.css" rel="stylesheet" type="text/css" />	
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_NAME;?>css/media.css">		
	<!--[if lte IE 7]>
	<style>
	.content { margin-right: -1px; } /* this 1px negative margin can be placed on any of the columns in this layout with the same corrective effect. */
	ul.nav a { zoom: 1; }  /* the zoom property gives IE the hasLayout trigger it needs to correct extra whiltespace between the links */
	</style>
	<![endif]-->
	
	<!-- 
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.js"></script>
	 -->
	<script type="text/javascript" src="<?php echo BASE_NAME;?>js/jquery-1.10.2.js"></script>
	<script type="text/javascript" src="<?php echo BASE_NAME;?>js/scripts.js"></script>
	<script type="text/javascript" src="<?php echo BASE_NAME; ?>js/jquery.popupoverlay.js"></script>
	<script type="text/javascript" src="<?php echo BASE_NAME; ?>js/jquery.validate.js"></script>
	
	<script src="<?php echo BASE_NAME;?>js/imgLiquid.js"></script>	
	<!-- Ad Gallery plugin -->
	<script type="text/javascript" src="<?php echo BASE_NAME;?>js/jquery.ad-gallery.js"></script>
	<script	src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
	<?php include_once("analyticstracking.php"); ?>
</head>

<body>
	<?php 
	if(isset($_SESSION['username']))
	{
		$modCustomer = new mod_customers();
		$currCustomer = $modCustomer->GetCustomerByEmail($_SESSION['username']);
		$modCustomer->closeConnect();
		
		if(count($currCustomer) == 1)
		{
			$url = $_SERVER['REQUEST_URI'];			
			if($currCustomer[0]['customer_first_login'] == 0 && str_replace('/', '', $url) != 'change-pass')
			{
				echo ('<script>window.location = "' . BASE_NAME . 'change-pass/";</script>');
			}
		}
	}
	?>
	<div id="outer-wrap">
		<div id="inner-wrap">
			<div class="container">
				<div class="header fixed">
					<?php
					require_once PATH_CLASS_VIEW . 'head.php'; 
					?>
					<!-- end .header -->
				</div>
				<div class="mainCotent">
					<div class="sidebar1 fixed">
						<?php 
						require_once PATH_CLASS_VIEW . 'menuleft.php';
						?>

						<!-- end .sidebar1 -->
					</div>
					<div class="content">
						<div class="cotent_display">
							<?php
							if (isset ( $_GET ['p'] )) {
								$_GET ['p'] = $_GET ['p'];
							}
							if(isset($_GET['pSub']))
							{
								$_GET['pSub'] = $_GET['pSub'];
							}
							
							include (PATH_CLASS_VIEW . 'index.php');	
													
							?>  
						</div>
						<div class="footer">
							<?php 
							require_once PATH_CLASS_VIEW . 'footer.php';
							?>
							<!-- end .footer -->
						</div>
						<!-- end .content -->
					</div>
					<div class="sidebar2 desktop-only">
						<?php 
						require_once PATH_CLASS_VIEW . 'ads.php';
						?>
						<!-- end .sidebar2 -->
					</div>
					
				</div>
				
				<!-- end .container -->
				<!-- Element to pop up -->
				<div id="my_popup">					
					<div class="wrapper-map"></div>
				</div>
				<!-- end .container -->
			</div>
		</div>
	</div>
</body>
</html>
