<?php
/*
 * $id, $maxRecords, $page on page news.php
 */
//echo 'news gallery come here';
$modCat = new mod_categories();
$currCat = $modCat->GetCategory($catId);

$listCat = array();
$count = 0;

if(count($currCat) > 0)
{       
    
    if($currCat[0]['cat_parent_id'] == null)
    {
        $listCategory = $modCat->GetCategoriesParentId($currCat[0]['cat_id']);
        foreach ($listCategory as $category)
        {
            $listSubCat = $modCat->GetCategoriesParentId($category['cat_id']);
            foreach ($listSubCat as $subCat)
            {
                $listCat[$count]['cat_id'] = $subCat['cat_id'];
                $listCat[$count]['cat_name'] = $subCat['cat_name'];
                $listCat[$count]['cat_description'] = $subCat['cat_description'];
                $listCat[$count]['cat_parent_id'] = $subCat['cat_parent_id'];
                $listCat[$count]['cat_order'] = $subCat['cat_order'];
    
                $count++;
            }
        }
    }
    else
    {         
        $listSubCat = $modCat->GetCategoriesParentId($currCat[0]['cat_id']);
        foreach ($listSubCat as $subCat)
        {
            $listCat[$count]['cat_id'] = $subCat['cat_id'];
            $listCat[$count]['cat_name'] = $subCat['cat_name'];
            $listCat[$count]['cat_description'] = $subCat['cat_description'];
            $listCat[$count]['cat_parent_id'] = $subCat['cat_parent_id'];
            $listCat[$count]['cat_order'] = $subCat['cat_order'];
        
            $count++;
        }         
    }
}
$modCat->closeConnect();

if(!isset($_SESSION['alphabest']) || count($_SESSION['alphabest']) <= 0)
{   
    $arrAlpha = array();
    foreach ($listCat as $getCat)
    {
        $arrName = split(' ',$getCat['cat_name']);
        $first_letter = strtoupper($arrName[count($arrName) - 1][0]);
        $arrAlpha[$first_letter][] = $getCat['cat_id'];
    }

    $_SESSION['alphabest'] = $arrAlpha;
}

$arrAlpha = $_SESSION['alphabest'];
ksort($arrAlpha);

if(count($listCat) > 0)
{   
    if(isset($_GET['pLetter']))
    {
        $newsListId = implode(',', $arrAlpha[$_GET['pLetter']]);
        
        $sql .= 'new_cat_id in (' . $newsListId . ')';
    }
    else 
    {
        $sql .= 'new_cat_id in ('; 
        for($i = 0; $i < count($listCat); $i++)
        {
            $sql .= $listCat[$i]['cat_id'];
            if($i < count($listCat) - 1)
            {
                $sql .= ',';
            }
        }
        $sql .= ')';
    }
    
    $modNews = new mod_news();    
    
    $newsList = $modNews->GetDataTable($sql, 'new_publish_date desc');
    
    $totalRecords = count($newsList);
    
    $totalPage = ceil($totalRecords/$maxRecords);
    
    $modNews->closeConnect();   
   
    ?>
    <script type="text/javascript">
    	$(document).ready(function(){
    		var page = <?php echo $page;?>;
    		
    		$.ajax({
    			type: 'POST',
    			url: '<?php echo BASE_NAME;?>ajax/loadnews.php',
    			data: {
    				'page': page,
    				'maxrecords': <?php echo $maxRecords;?>,
    				'sql': '<?php echo $sql;?>',
    				'pSub': '<?php echo $_GET['pSub']; ?>',
    			},
    			beforeSend: function(){
    				$('#nextpage').css('display', 'block');
    			},
    			success: function(result){
    				$('#nextpage').css('display', 'none');
    
    				$('.new-content').append(result);
    				image_resize('.new-image-right, .new-image-left', '.also-like-inner-box');
    			},
    		});		
    
    		var load = 1;		
    		var totalPage = <?php echo $totalPage;?>;
    
    		$('#btnLoadContent').click(function(e){
    			e.preventDefault();
    			    			
    			load ++;
    			
    			if(load <= totalPage)
    			{
    				$('#nextpage').css('display', 'block');
    				
    				$.ajax({
    					type: 'POST',
    					url: '<?php echo BASE_NAME;?>ajax/loadnews.php',
    					data: {
    						'page': load,
    						'maxrecords': <?php echo $maxRecords;?>,
    						'sql': '<?php echo $sql;?>',
    						'pSub': '<?php echo $_GET['pSub']; ?>',
    					},
    					beforeSend: function(){
    						$('#nextpage').css('display', 'block');
    					},
    					success: function(result){
    						$('#nextpage').css('display', 'none');
    
    						$('.new-content').append(result);
    						image_resize('.new-image-right, .new-image-left', '.also-like-inner-box');
    					},
    				});
    			}
    			
    			if(load < totalPage)
    			{
    				$(this).val('Read more');
    			}
    			else
    			{
    				$(this).val('End page');
    				$(this).attr('disabled', 'disabled');
    			}
    		});
    	});
    
    	/* How to use with Window Load (For Webkit Browser like safari and Chrome) */	
    	$(window).load(function () {			
    		image_resize('.new-image-right, .new-image-left', '.also-like-inner-box');
    	});
    
    	
    	/* How to use on Window resize */	
    	$(window).resize(function () {	
    		image_resize('.new-image-right, .new-image-left', '.also-like-inner-box');
    	});
    	
    </script>
    <div class="new-main-content">
    <?php    
    if(count($arrAlpha) > 0)
    {        
        $tempUrl = '';
        if(isset($_GET['pLetter']))
        {
            $arrUrl = split('/', $url);
            for($i = 0; $i < count($arrUrl) - 1; $i++)
            {
                $tempUrl .= $arrUrl[$i] . '/';
            }
        }else 
        {
            $tempUrl = $url;
        }
        $currUrl = $tempUrl;
    ?>
        <div class="new-gallery-sort">
            <ul>
                <li><a href="<?php echo $currUrl; ?>">*</a></li>
            <?php 
            
            foreach ($arrAlpha as $key=>$value)
            {
            ?>
                <li><a href="<?php echo $currUrl . $key . '.html'; ?>"><?php echo $key; ?></a></li>
            <?php 
            }
            ?>
            </ul>
        </div>
    <?php 
    }
    ?>    
    	<div class="new-content">	
    		 	 
    	</div>
    	
    	<div id="nextpage" style="display: none">
    		<img src="<?php echo BASE_NAME?>images/loading4.gif" alt="Loading ..." />
    	</div>
    	
    	<div id="div-loading" style="padding-top: 2px;">
    		<input type="button" id="btnLoadContent" value="<?php echo ($page >= $totalPage)? 'End page': 'Read more ...'; ?>" 
    			<?php if($page >= $totalPage) echo 'disabled = "disabled"';?> />
    	</div>
    	
    	<div style="clear: both; height: 2px;">
    			&nbsp;
    	</div>
    </div>
    <?php 
}
else
{
    echo 'Do not has any news !';
}
    
?>