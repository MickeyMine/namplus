<?php
include '../config.php';
include '../libs/modules/clsDB.php';
include '../libs/modules/mod_news.php';

if(isset($_POST['newsid']))
{
	$modNews = new mod_news();
	$currNew = $modNews->GetNewsById($_POST['newsid']);
	
	if(count($currNew) == 1)
	{
?>
		<h2 style="padding: 5px 0px; margin: 0px;">
			<?php echo $currNew[0]['new_title'];?>
		</h2>
		<div class="new-description">
		<?php
		echo $currNew[0]['new_description'];
		?>
		</div>
		<div class="new-content">
		<?php
		echo $currNew[0]['new_content'];
		?>
		</div>
<?php
	}
	else
	{
		echo 'Can not get this news !!';
	} 
}
else 
{
	echo 'Verify your information !!';
}
?>