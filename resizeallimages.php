<?php
echo 'Come here';
ini_set('max_execution_time', 0);

//Initial settings, Just specify Source and Destination Image folder.

//$ImagesDirectory    = '/home/public_html/websites/images/'; //Source Image Directory End with Slash
$ImagesDirectory    = dirname(__FILE__) . '/uploads/';
//$DestImagesDirectory    = '/home/public_html/websites/images/new/'; //Destination Image Directory End with Slash
$DestImagesDirectory = $ImagesDirectory ;
$NewImageWidth      = 639; //New Width of Image
$NewImageHeight     = 0; // New Height of Image
$Quality        = 90; //Image Quality

$files = glob("*.{png,jpg,jpeg}", GLOB_BRACE);

foreach ($files as $file)
{
    echo $file . "<br />";
}
?>
