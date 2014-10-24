<?php
require PATH_CLASS_MODEL . 'APIclient.class.php';

$api = new JoomagApiClient(JOOMGA_API_KEY);

$res = $api->listMagazines();

foreach ($res->response() as $value)
{
	//Get each magazine in account
	$arr = json_decode(json_encode($value[0]), true);
	
	//Get list issues in magazine id 
	$listIssues = $api->listIssues($arr['ID']);
	$currIssues = $listIssues->response();
	
	/*
	echo ("<pre>");
	//Convert magazine to array , items title, id, url;
	//$listIssues->response()['issues']
	
	print_r($currIssues['issues']);
	echo ("</pre>");
	*/
?>
<div class="j-magazine-view">
<?php 
	if(count($currIssues) > 0)
	{
		foreach ($currIssues as $issues)
		{
			foreach ($issues as $issue)
			{
				$arrIssue = json_decode(json_encode($issue), true);
				
?>
				<div class="j-magazine">
					<a title="<?php echo $arrIssue['title'];?>" href="<?php echo BASE_NAME . 'magazine/' . $arrIssue['ID'];?>" rel="<?php echo $arrIssue['url'];?>" >
						<img src="<?php echo $arrIssue['cover'];?>" alt="<?php echo $arrIssue['desc'];?>" />
					</a>
					<div class="j-magazine-title">
						<?php echo $arrIssue['title']; ?>
					</div>
				</div>
<?php 
			}
		}
	}
	else
	{
		echo 'Can not get Issues from Joomga';
	}
}

?>
	<div style="clear: both; height: 1px;">
		&nbsp;
	</div>
</div>

<!-- 
<iframe id="frameMagazine" width="100%" height="500px" src="http://www.joomag.com/magazine/nam-magazine-no-40-2014/0199639001406695596">
</iframe>

<script type="text/javascript">
	$(document).ready(function(e){
		var url = $(location).attr('href');
		var iframe = $('#frameMagazine');

		iframe.load(function(){
			parent.location.hash = '#frameMagazine';
			parent.location = iframe.contents().find('head').find('base').attr('href');
			
			iframe.contents().find('head').find('base').attr('href', url);
		});
	});	
</script>
-->