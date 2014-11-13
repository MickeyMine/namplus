<?php 
if (! isset ( $_SESSION )) {
    session_start ();
}
?>

<script type="text/javascript">
	$(document).ready(function(){
		image_also_resize('.also-like-inner-box');
	});

	/* How to use with Window Load (For Webkit Browser like safari and Chrome) */	
	$(window).load(function () {			
		image_also_resize('.also-like-inner-box');
	});

	
	/* How to use on Window resize */	
	$(window).resize(function () {	
		image_also_resize('.also-like-inner-box');
	});
	
</script>
<div class="new-content">
	<div class="new-context">
	<?php 
	$contentTitle = '';
	$contentDesc = '';
	
	if(isset($_GET['pItem']))
	{
		$arr = split('-', $_GET['pItem']);
		$id = $arr[count($arr) - 1];
		
		if(!isset($_SESSION['like-also']))
		{
		    $_SESSION['like-also'] = $id . ',';
		}
		else
		{
		  $_SESSION['like-also'] .= $id . ',';
		}
		
		$modNews = new mod_news();
		$currNews = $modNews->GetNewsById($id);		
		
		if(count($currNews) == 1)
		{
			$contentTitle = $currNews[0]['new_title'];
			$contentDesc = $currNews[0]['new_description'];
			
			$newsType = $currNews[0]['new_type'];
			//echo $newsType;
			if($newsType == 0 || $newsType == 3)
			{
				include 'newstemplatedefault.php';
			}
			else if($newsType == 1)
			{
				include 'newstemplate1.php';
			}
			else 
			{
				include 'newstemplate2.php';
			}			
			
	?>
			
			<div id="fb-root"></div>
			<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
			<fb:comments href="<?php echo $current_page_URL;?>" num_posts="5"  width="100%"></fb:comments>
	<?php 
		}
		else 
		{
			echo 'Can not get this news !!';
		}
		
		$modNews->closeConnect();
	}
	else 
	{
		echo 'Please verify your information !!';
	}
	?>
	</div>
</div>

<?php 
include 'newslikealso.php';
?>
