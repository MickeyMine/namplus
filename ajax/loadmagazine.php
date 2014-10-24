<?php
echo '<div class="pageMagazine">';

if(isset($_POST['magazineid']))
{
?>
	<iframe id="frameMagazine" width="100%" src="<?php echo $_POST['magazineid'];?>">
	</iframe>
<?php
}
else 
{
	echo 'Can not get this magazine !!';
} 
echo '</div>';
?>