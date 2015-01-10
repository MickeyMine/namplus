
<div style="clear: both; height: 2px;">
		&nbsp;
</div>
<div class="content-page">
<?php 
    $modContactUs = new mod_contact_us();
    $contactUs = $modContactUs->GetContactUs();
    $modContactUs->closeConnect();
    
    if(count($contactUs) == 1)
    {
        echo $contactUs[0]['content'];
    }
    else 
    {
        echo 'Please input your contact !!';
    }
?>
</div>
	