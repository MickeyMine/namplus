<!-- Begin Main Menu -->
<div class="ewMenu">
<?php $RootMenu = new cMenu(EW_MENUBAR_ID) ?>
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
$RootMenu->AddMenuItem(-1, $Language->Phrase("Logout"), "logout.php", -1, "", IsLoggedIn());
$RootMenu->AddMenuItem(-1, $Language->Phrase("Login"), "login.php", -1, "", !IsLoggedIn() && substr(@$_SERVER["URL"], -1 * strlen("login.php")) <> "login.php");
$RootMenu->Render();
?>
</div>
<!-- End Main Menu -->
