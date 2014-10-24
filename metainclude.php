<?php
$current_page_URL =(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]=="on") ? "https://" : "http://";
$current_page_URL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

$keywords = "www.namplus.com.vn, WWW.NAMPLUS.COM.VN, namplus.com.vn, NAMPLUS.COM.VN, namplus, NAMPLUS, nam magazine, NAM MAGAZINE, tap chi cho nam gioi, TAP CHI CHO NAM GIOI, táº¡p chÃ­ cho nam giá»›i";
$description = "NAM Magazine là tạp chí dành cho phái mạnh, giới thiệu các sản phẩm và vật dụng cho giới mày râu .";
$title = "NAM Plus";
$site_name = "NAM Plus";
$url = $current_page_URL;
$og_description = "NAM Plus";
$image = BASE_NAME . "images/logo.png";


?>
	<meta name="keywords" content="<?php echo $keywords;?>" />
	<meta name="description" content="<?php echo $description;?>" />
	<meta name="robots" content="INDEX, FOLLOW" />
	<meta name="title" content="<?php echo $title;?>" />
	
    <meta property="og:title" content="<?php echo $title;?>"/>
    <meta property="og:site_name" content="<?php echo $site_name;?>"/>
    <meta property="og:url" content="<?php echo $url;?>"/>
    <meta property="og:description" content="<?php echo $og_description;?>"/>
    <meta property="og:type" content="website" />
    <meta property="og:image" content="<?php echo $image;?>" />
    <meta property="fb:app_id" content="736997686322439"/>