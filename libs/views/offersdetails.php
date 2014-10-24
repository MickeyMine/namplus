<div class="offer-details-content">
<div id="fb-root"></div>
<script>
	(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=736997686322439&version=v2.0";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
</script>

<?php 
if(isset($_GET['pSub']) && isset($_GET['pItem']))
{
	$arrSub = split('-', $_GET['pSub']);
	$subId = $arrSub[count($arrSub)-1];
	
	$arrItem = split('-', $_GET['pItem']);
	$offerId = $arrItem[count($arrItem)-1];
	
	$clsCommon = new SBD_Common();
	
	$modOffer = new mod_offers();
	$currOffer = $modOffer->GetOfferById($offerId);
	$modOffer->closeConnect();
	
	if(count($currOffer) == 1)
	{
		$modCat = new mod_categories();
		$currCat = $modCat->GetCategory($subId);
		$modCat->closeConnect();
		
		if($currCat[0]['cat_is_offer'] == 1)
		{
			$isOffer = true;
		}
		else if($currCat[0]['cat_is_competition'] == 1)
		{
			$isOffer = false;
		}
		
		$link = BASE_NAME . 'offers/' . $_GET['pSub'] . '/' . $clsCommon->text_rewrite($currOffer[0]['offer_title']) . '-' . $currOffer[0]['offer_id'] . '/';
		$title = $currOffer[0]['offer_title'];
		$description = $currOffer[0]['offer_description'];
		$content = $currOffer[0]['offer_content'];
		
		$image_top = BASE_NAME . 'uploads/' . $currOffer[0]['offer_top_image'];
		$image_bottom = BASE_NAME . 'uploads/' . $currOffer[0]['offer_bottom_image'];
		
		$start_date = $currOffer[0]['offer_start_date'];
		$end_date = $currOffer[0]['offer_end_date'];
		
		$start_time = $currOffer[0]['offer_start_time'];
		$end_time = $currOffer[0]['offer_end_time'];
		
		$offer_rule = $currOffer[0]['offer_rules'];
		$offer_value = $currOffer[0]['offer_value'];
		
		$modImageGallery = new mod_image_gallery();
		$listImage = $modImageGallery->getImageGalleryByOfferID($offerId);
		$modImageGallery->closeConnect();
		
		?>
		<script type="text/javascript">
        var gmarkers = [];
        var map = null;

        function initialize() {
            // create the map
            var myOptions = {
                zoom: 12,
                center: new google.maps.LatLng(10.75912771916204, 106.67350709438324),
                mapTypeControl: true,
                mapTypeControlOptions: { style: google.maps.MapTypeControlStyle.DROPDOWN_MENU },
                navigationControl: true,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            map = new google.maps.Map(document.getElementById("map"),
                                          myOptions);
			
            google.maps.event.addListener(map, 'click', function () {
                infowindow.close();
            });
<?php 
			$modOfferLocation = new mod_offer_locations();
			$listLocation = $modOfferLocation->GetLocationByOfferID($offerId);
			$modOfferLocation->closeConnect();
			
			foreach ($listLocation as $location)
			{
				$location_name = $location['location_name'];
				$location_address = $location['location_address'];
				$map_x = $location['location_map_x'];
				$map_y = $location['location_map_y'];
?>
				var myLat = <?php echo trim($map_y);?>;
				var myLon = <?php echo trim($map_x);?>;
				
	          	var point = new google.maps.LatLng(myLat,myLon);
	            var marker = createMarker(point, "This place", "<div style=' position: relative; padding: 3px'><span style='font-weight: bold;'><?php echo $location_name;?></span> <br \> <address><?php echo $location_address;?></address></div>");
<?php 
			}
?> 
        }

        //Check if has item
        //if ($('#offer_location').length) {
       	google.maps.event.addDomListener(window, 'load', initialize);
        //}
		
       	var infowindow = new google.maps.InfoWindow(
        {
        	size: new google.maps.Size(150, 50)
        });

        // This function picks up the click and opens the corresponding info window
        function showlocation(i) {
            google.maps.event.trigger(gmarkers[i], "click");
        }

        // A function to create the marker and set up the event window function 
        function createMarker(latlng, name, html) {
            var contentString = html;
            var marker = new google.maps.Marker({
                position: latlng,
                map: map,
                //         zIndex: Math.round(latlng.lat() * -100000) << 5
            });
			
            google.maps.event.addListener(marker, 'click', function () {
                infowindow.setContent(contentString);
                infowindow.open(map, marker);
            });
            
            // save the info we need to use later for the side_bar
            gmarkers.push(marker);
            // add a line to the side_bar html
            //    side_bar_html += '<a href="javascript:myclick(' + (gmarkers.length - 1) + ')">' + name + '<\/a><br>';
        }
    	</script>
    	<script type="text/javascript">
        // Semicolon (;) to ensure closing of earlier scripting
        // Encapsulation
        // $ is assigned to jQuery
        ; (function ($) {

            // DOM Ready
            $(function () {

                $('#offer_location').click(function (e) {
                    // Prevents the default action to be triggered. 
                    e.preventDefault();

					$width = $(window).width();
					$height = $(window).height();	
					
					if($width <= 480)
					{
						$width_map = $width;
						$height_map = $width;
					}
					else 
					{
						$width_map = 600;
						$height_map = 450;
					}

					$('#map').css('width', $width_map);
					$('#map').css('height', $height_map);

					
                  	google.maps.event.trigger(map, 'resize');

                  	<?php 
                  	if(count($listLocation) > 0)
                  	{
					?>
                  	var myLat = <?php echo trim($listLocation[0]['location_map_y']);?>;
        			var myLon = <?php echo trim($listLocation[0]['location_map_x']);?>;
        			
                  	var point = new google.maps.LatLng(myLat,myLon);
					map.setCenter(point);
                  	<?php 
					}
					?>
					
                  	google.maps.event.trigger(map, 'resize');
					
                    google.maps.event.addDomListener(window, 'load', initialize); 
                    // Triggering Popup when click event is fired
                    $('#map_popup').popup({
                    	autoopen: true,
                    	pagecontainer: '#map',                    	
    					onclose: function(){
    						$('#map').css('width', '100%');
    						$('#map').css('height', '100%');
    						$('#map').css('background-color', 'transparent');
    					}
                    }); 
                });
            });

        })(jQuery);

    	</script>
    	<script type="text/javascript">
    	$(document).ready(function(){
    		image_also_resize('.toprightcontentimg, .bottomrightcontent');
    	});
    	
    	/* How to use with Window Load (For Webkit Browser like safari and Chrome) */	
    	$(window).load(function () {			
    		image_also_resize('.toprightcontentimg, .bottomrightcontent');
    	});
    	
    	/* How to use on Window resize */	
    	$(window).resize(function () {	
    		image_also_resize('.toprightcontentimg, .bottomrightcontent');
    	});
    	</script>
    	<div class="leftcontent">
			<div class="topleftcontent">	
				<div class="buttonleftcontent">		 
	            	<div class="div-content-next">
						<div class="content-next-box offer-details"  rel="<?php echo $link;?>">
							<div class="content-next-box-left"></div>
							<div class="content-next-box-main">
							<?php 
								echo $isOffer ? 'offers': 'competition';
							?>
							</div>
							<div class="content-next-box-right"></div>
						</div>
					</div>
	             	<h2 style="padding-left: 60px; padding-right: 60px;">
	             		<?php 
	             		echo $title;
	             		?>
	             	</h2>         
	             	<p>
	             		<?php 
	             		echo $description;
	             		?>
	             	</p> 
	            </div> 
	            
	            <div class="sharestyle">
	            	<div class="sharestyleimg">
	                   	<fb:like href="<?php echo $link;?>" data-layout="button_count" data-share="true" send="true" width="450" show_faces="false"></fb:like>                    	
	                </div>
	            </div>
			</div>
			<div class="bottomleftcontent" style="padding-top: 20px;">
	        	<div class="bottomleftcontentmain">
	                <div class="bottomleftcontent1">
	                    <?php 
	                    echo $content;
	                    ?>
	                </div>
	                <div class="slidercontent">
	                	<div class="mainslider">	                
		               	<?php 
		               	if(count($listImage) > 0)
		               	{
		               	?>               		
		                    <img class="mainsliderimage" src="<?php echo BASE_NAME. 'uploads/' . $listImage[0]['image_path'];?>" alt="<?php echo $listImage[0]['image_alt'];?>" />
		                    <ul class="gallery">
		                 	<?php 
		                 	foreach ($listImage as $img)
		                 	{
		                 	?>
		                 		<li>
		                 			<a id="<?php echo $img['image_id']; ?>" href="<?php echo (BASE_NAME . 'uploads/' . $img['image_path']) ; ?>" alt="<?php echo $img['image_alt'];?>">
		                 			</a>
		                 		</li>
		                 	<?php 
		                 	}
		                 	?>
		                    </ul>
		                <?php 
		               	}
		               	else 
		               	{
		                ?>
		                	<img src="<?php echo BASE_NAME; ?>images/logo.png" alt="NAM Plus" />
		                <?php 
		               	}
		                ?>
		                </div>	
		                <?php 
		                if(count($listImage) > 1)
		                {
		                ?>
		                <div class="img_nav">
						    <div class="previous">
						        <a class="arr-l white" href="./">Previous image</a>
						    </div>
						    <div class="next">
						        <a class="arr-r white" href="./">Next image</a>
						    </div>
						</div>   
						<?php 
		                }
						?>					         
	                </div>
	                <div class="offerlogin">
	                	<?php 
	                    if(!isset($_SESSION['username']))
	                    {
	                    ?>
	                    <div class="div-offer-login">
		                    <div class="div-content-next">
								<div class="content-next-box login-offer-details"  rel="<?php echo BASE_NAME . PATH_CLASS_VIEW ; ?>login.php">
									<div class="content-next-box-left"></div>
									<div class="content-next-box-main">
									<?php 
										echo 'log in';
									?>
									</div>
									<div class="content-next-box-right"></div>
								</div>
							</div>
							<div class="div-content-next">
								<div class="content-next-box register-offer-details"  rel="<?php echo BASE_NAME ; ?>register/">
									<div class="content-next-box-left"></div>
									<div class="content-next-box-main">
									<?php 
										echo 'register';
									?>
									</div>
									<div class="content-next-box-right"></div>
								</div>
							</div>
						</div>
	                    <?php
	                    } 
	                    else
	                    {
	                    ?>	
	                    <div class="div-content-next">
							<div id="joinoffer" class="content-next-box offer-details"  rel="<?php echo $offerId;?>">
								<div class="content-next-box-left"></div>
								<div class="content-next-box-main">
								<?php echo $isOffer ? 'GET OFFER': 'JOIN COMPETITION';?>
								</div>
								<div class="content-next-box-right"></div>
							</div>
						</div>
	                    
	                    <?php 
	                    }
	                    ?>         
	                </div>
	            </div>
			</div>
		</div>
		<div class="rightcontent">
			<div class="toprightcontent">         
					<img src="<?php echo $image_top; ?>" alt="<?php echo $title;?>" />
			</div>
			<div class="middlerightcontent" style="text-align:center;">
				<div class="toprightcontentmain">            
					<div class="elementmiddlerightcontent">
	                	<div class="elementcontent">
	                    	<a id="offer_rules" href="<?php echo BASE_NAME; ?>rules/" rel="<?php echo $offerId;?>">
		                    	<img src="<?php echo BASE_NAME; ?>images/questionicon.png" />
		                        <h3 style="padding: 0px; margin: 0px;">
		                        	CÁCH THỨC
		                        </h3>
		                        Đăng ký để<br />
		                       	bắt đầu
	                       	</a>
	                    </div>
	                </div>
	                <div class="elementmiddlerightcontent">
	                <?php 
	                if(count($listLocation) > 0)
	                {
	                ?>
	                	<div class="elementcontent" onclick="showlocation(0)">
	                    	 <a id="offer_location" href="<?php echo BASE_NAME . 'location/' ; ?>location/" rel="<?php echo $offerId;?>">
		                    	<img src="<?php echo BASE_NAME; ?>images/locationicon.png" alt="namplus" />
		                        <h3 style="padding: 0px; margin: 0px;">
		                        	ĐỊA ĐIỂM
		                        </h3>
		                        <?php 
		                        //echo $listLocation[0]['location_name'];
		                        ?>
	                       </a>
	                    </div>
	                <?php 
	                }
	                else 
	                {
	                ?>
	                	<div class="elementcontent">                    	 
	                    	<img src="<?php echo BASE_NAME; ?>images/locationicon.png" />
	                        <h3 style="padding: 0px; margin: 0px;">
	                        	ĐỊA ĐIỂM
	                        </h3>                                            
	                    </div>
	                <?php 
	                }
	                ?>
	                </div>
	                <div class="elementmiddlerightcontent">
	                	<div class="elementcontent">
	                    	<img src="<?php echo BASE_NAME; ?>images/timeicon.png" />
	                        <h3 style="padding: 0px; margin: 0px;">
	                        	THỜI GIAN
	                        </h3>
	                        <?php 
	                        echo date('g:iA', strtotime($start_time)) . ' - ' . date('g:iA', strtotime($end_time));
	                        ?>
	                        <br />
	                        <?php 
	                        echo date('d/m', strtotime($start_date)) . ' - ' . date('d/m', strtotime($end_date));
	                        ?>
	                    </div>
	                </div>
	                <div class="elementmiddlerightcontent">
	               		<div class="elementcontent">
	                    	<img src="<?php echo BASE_NAME; ?>images/dolaicon.png" />
	                        <h3 style="padding: 0px; margin: 0px;">
	                        	GIÁ TRỊ
	                        </h3>
	                        <?php 
	                        echo $offer_value;
	                        ?>
	                    </div>
	                </div>
	            </div>
			</div>
			<div class="bottomrightcontent" style="text-align:center;">
				<div class="toprightcontentmain toprightcontentimg">            
					<img src="<?php echo $image_bottom; ?>" alt="<?php echo $title;?>" />
	            </div>
			</div>
		</div>
		<div id="map_popup">
	        <div id="map">       		
	        </div>       
	    </div>
		<?php 
	}
	else 
	{
		echo 'Can not get this offer';
	}
}

?>
<div style="clear: both; height: 2px;">
			&nbsp;
</div>
</div>