<?php
 echo'Template 2';
?>

<?php 
$listNews = $modNews->GetNewsByCatID($currNews[0]['new_cat_id']);
if(count($listNews) > 1)
{
	$isEnd = false;
	for($i = 0; $i < count($listNews); $i++)
	{
	if($listNews[$i]['new_id'] == $id)
	{
	if($i == count($listNews) - 1)
	{
	$isEnd = true;
	}
		else
		{
		$arrCurrLink = split('/', $current_page_URL);
			
		$linkNext = '';
		for($j = 0; $j < count($arrCurrLink) - 2 ; $j ++)
		{
		$linkNext .= $arrCurrLink[$j] . '/';
		}
			
		$linkNext .= $clsCommon->text_rewrite($listNews[$i+1]['new_title']) . '-' . $listNews[$i+1]['new_id'] . '/';
		}
		}
		}

		if(!$isEnd)
		{
				?>
			<div class="content-next-news">
				<div class="div-content-next" id="content-next" rel="<?php echo $linkNext;?>">
					<div class="content-next-box">
						<div class="content-next-box-left">
						</div>
						<div class="content-next-box-main">
						<?php 
						if($newsType == 0 || $newsType == 3 || $newsType == 2)
						{
							$textNext = 'xem tiếp';
						}
						else if($newsType == 1)
						{
							$textNext = 'start with';
						}
						echo $textNext;
						?>								
						</div>
						<div class="content-next-box-right">
						</div>
					</div>
				</div>
			</div>
	<?php 
				}
			}

?>