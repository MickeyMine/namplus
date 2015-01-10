<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "userfn10.php" ?>
<?php
	ew_Header(TRUE);
	$conn = ew_Connect();
	$Language = new cLanguage();

	// Security
	$Security = new cAdvancedSecurity();
	if (!$Security->IsLoggedIn()) $Security->AutoLogin();
	$Security->LoadUserLevel(); // Load User Level
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $Language->Phrase("MobileMenu") ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="<?php echo ew_jQueryFile("jquery.mobile-%v.min.css") ?>">
<link rel="stylesheet" type="text/css" href="<?php echo EW_PROJECT_STYLESHEET_FILENAME ?>">
<link rel="stylesheet" type="text/css" href="phpcss/ewmobile.css">
<script type="text/javascript" src="<?php echo ew_jQueryFile("jquery-%v.min.js") ?>"></script>
<script type="text/javascript">

	//$(document).bind("mobileinit", function() {
	//	jQuery.mobile.ajaxEnabled = false;
	//	jQuery.mobile.ignoreContentEnabled = true;
	//});

</script>
<script type="text/javascript" src="<?php echo ew_jQueryFile("jquery.mobile-%v.min.js") ?>"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="generator" content="PHPMaker v10.0.4">
</head>
<body>
<div data-role="page">
	<div data-role="header">
		<h1><?php echo $Language->ProjectPhrase("BodyTitle") ?></h1>
	</div>
	<div data-role="content">
<?php $RootMenu = new cMenu("RootMenu", TRUE); ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(1, $Language->MenuPhrase("1", "MenuText"), "categorieslist.php", -1, "", AllowListMenu('{621448A2-A15A-4302-8B90-FC8E171BD28F}categories'), FALSE);
$RootMenu->AddMenuItem(3, $Language->MenuPhrase("3", "MenuText"), "image_gallerylist.php", -1, "", AllowListMenu('{621448A2-A15A-4302-8B90-FC8E171BD28F}image_gallery'), FALSE);
$RootMenu->AddMenuItem(4, $Language->MenuPhrase("4", "MenuText"), "newslist.php", -1, "", AllowListMenu('{621448A2-A15A-4302-8B90-FC8E171BD28F}news'), FALSE);
$RootMenu->AddMenuItem(9, $Language->MenuPhrase("9", "MenuText"), "offerslist.php", -1, "", AllowListMenu('{621448A2-A15A-4302-8B90-FC8E171BD28F}offers'), FALSE);
$RootMenu->AddMenuItem(7, $Language->MenuPhrase("7", "MenuText"), "offer_locationslist.php", -1, "", AllowListMenu('{621448A2-A15A-4302-8B90-FC8E171BD28F}offer_locations'), FALSE);
$RootMenu->AddMenuItem(8, $Language->MenuPhrase("8", "MenuText"), "offer_questionslist.php", -1, "", AllowListMenu('{621448A2-A15A-4302-8B90-FC8E171BD28F}offer_questions'), FALSE);
$RootMenu->AddMenuItem(5, $Language->MenuPhrase("5", "MenuText"), "offer_answerslist.php", -1, "", AllowListMenu('{621448A2-A15A-4302-8B90-FC8E171BD28F}offer_answers'), FALSE);
$RootMenu->AddMenuItem(22, $Language->MenuPhrase("22", "MenuText"), "offer_voucherslist.php", -1, "", AllowListMenu('{621448A2-A15A-4302-8B90-FC8E171BD28F}offer_vouchers'), FALSE);
$RootMenu->AddMenuItem(21, $Language->MenuPhrase("21", "MenuText"), "offer_cus_answerslist.php", -1, "", AllowListMenu('{621448A2-A15A-4302-8B90-FC8E171BD28F}offer_cus_answers'), FALSE);
$RootMenu->AddMenuItem(2, $Language->MenuPhrase("2", "MenuText"), "customerslist.php", -1, "", AllowListMenu('{621448A2-A15A-4302-8B90-FC8E171BD28F}customers'), FALSE);
$RootMenu->AddMenuItem(15, $Language->MenuPhrase("15", "MenuText"), "register_formlist.php", -1, "", AllowListMenu('{621448A2-A15A-4302-8B90-FC8E171BD28F}register_form'), FALSE);
$RootMenu->AddMenuItem(16, $Language->MenuPhrase("16", "MenuText"), "payment_typelist.php", -1, "", AllowListMenu('{621448A2-A15A-4302-8B90-FC8E171BD28F}payment_type'), FALSE);
$RootMenu->AddMenuItem(10, $Language->MenuPhrase("10", "MenuText"), "subscriptionslist.php", -1, "", AllowListMenu('{621448A2-A15A-4302-8B90-FC8E171BD28F}subscriptions'), FALSE);
$RootMenu->AddMenuItem(11, $Language->MenuPhrase("11", "MenuText"), "userslist.php", -1, "", AllowListMenu('{621448A2-A15A-4302-8B90-FC8E171BD28F}users'), FALSE);
$RootMenu->AddMenuItem(28, $Language->MenuPhrase("28", "MenuText"), "contactuslist.php", -1, "", AllowListMenu('{621448A2-A15A-4302-8B90-FC8E171BD28F}contactus'), FALSE);
$RootMenu->AddMenuItem(-1, $Language->Phrase("Logout"), "logout.php", -1, "", IsLoggedIn());
$RootMenu->AddMenuItem(-1, $Language->Phrase("Login"), "login.php", -1, "", !IsLoggedIn() && substr(@$_SERVER["URL"], -1 * strlen("login.php")) <> "login.php");
$RootMenu->Render();
?>
	</div><!-- /content -->
</div><!-- /page -->
</body>
</html>
<?php

	 // Close connection
	$conn->Close();
?>
