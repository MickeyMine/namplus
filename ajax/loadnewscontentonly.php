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