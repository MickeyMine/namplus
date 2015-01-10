<?php 
include ('../config.php');
?>

<script type="text/javascript">
    $(document).ready(function () {
    	var maxWidth = $(window).width() - 2;
    	var maxHeight = $(window).height() + 2;
    	$('.page-zoom').css('width', maxWidth);
    	$('.page-zoom').css('height', maxHeight);
    	$('.page-zoom').css('background-color', '#fff');        
    });
    /* How to use with Window Load (For Webkit Browser like safari and Chrome) */	
	$(window).load(function () {
		var maxWidth = $(window).width() - 2;
		var maxHeight = $(window).height() + 2;
		$('.page-zoom').css('width', maxWidth);
		$('.page-zoom').css('height', maxHeight);
		$('.page-zoom').css('background-color', '#fff');
	});
	
	/* How to use on Window resize */
	
	$(window).resize(function () {
		var maxWidth = $(window).width() - 2;
		var maxHeight = $(window).height() + 2;
		$('.page-zoom').css('width', maxWidth);
		$('.page-zoom').css('height', maxHeight);
		$('.page-zoom').css('background-color', '#fff');
	});
</script>
<div class="page-zoom t-center">
<?php 
if(isset($_POST['listImage']) && isset($_POST['listImage']))
{
    $currImgPath = $_POST['currImage'];
    $arr = $_POST['listImage'];
    
    $newListImg = array();
    foreach ($arr as $img)
    {
        $listImg = split(',', $img['img_path']);
        
        foreach ($listImg as $pathImg)
        {
            array_push($newListImg, $pathImg);
        }
    }
    
    if(count($newListImg) > 0)
    {
?>
    <img class="showImage" src="<?php echo BASE_NAME; ?>uploads/<?php echo $currImgPath;?>" alt="Nam plus" />

    <ul class="gallery">
 <?php 
        foreach ($newListImg as $selectImg)
        {
?>
        <li>
            <a href="<?php echo (BASE_NAME . 'uploads/' . $selectImg) ; ?>"></a>
        </li>
<?php 
        }
?>   
    </ul>
<?php 
    }
}
?>
</div>

<div class="img_nav">
    <div class="previous">
	    <a class="arr-l white" href="./">Previous image</a>
	</div>
	<div class="next">
	    <a class="arr-r white" href="./">Next image</a>
	</div>
</div>

<script type="text/javascript">

var imgShow = $('.showImage');
var imgList = $('.page-zoom .gallery').find('a');
var totalImg = imgList.length;

$('#my_popup, .wrapper-map, .page-zoom').keypress(function(e){
	e.preventDefault();

	if(e.key == 'Right')
	{
		for(var i =0; i<imgList.length; i++)
		{
			imgLink = decodeURIComponent(imgList[i]);
			if(imgLink == imgShow.attr('src'))
			{
				var imgIndex = i + 1;
				//alert('img index ' + imgIndex + ' - max index : ' + (imgList.length));
				if(imgIndex >= imgList.length)
				{
					imgIndex = 0;
				}
				imgShow.attr('src', decodeURIComponent(imgList[imgIndex]));

				//image_resize();
				 
				return;
			}
		}
	}
	if(e.key == 'Left')
	{
		for(var i =0; i<imgList.length; i++)
		{
			imgLink = decodeURIComponent(imgList[i]);
			if(imgLink == imgShow.attr('src'))
			{
				var imgIndex = i-1;
				if(imgIndex < 0)
				{
					imgIndex = imgList.length - 1;
				}
				imgShow.attr('src', decodeURIComponent(imgList[imgIndex]));

				//image_resize();
				 
				return;
			}
		}
	}
});

$('.img_nav .previous').click(function(e){
	e.preventDefault();
	
	for(var i =0; i<imgList.length; i++)
	{
		imgLink = decodeURIComponent(imgList[i]);
		if(imgLink == imgShow.attr('src'))
		{
			var imgIndex = i-1;
			if(imgIndex < 0)
			{
				imgIndex = imgList.length - 1;
			}
			imgShow.attr('src', decodeURIComponent(imgList[imgIndex]));

			//image_resize();
			 
			return;
		}
	}
});
$('.img_nav .next').click(function(e){
	e.preventDefault();
	
	for(var i =0; i<imgList.length; i++)
	{
		imgLink = decodeURIComponent(imgList[i]);
		if(imgLink == imgShow.attr('src'))
		{
			var imgIndex = i + 1;
			//alert('img index ' + imgIndex + ' - max index : ' + (imgList.length));
			if(imgIndex >= imgList.length)
			{
				imgIndex = 0;
			}
			imgShow.attr('src', decodeURIComponent(imgList[imgIndex]));

			//image_resize();
			 
			return;
		}
	}
});
</script>