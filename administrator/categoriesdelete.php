<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "categoriesinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$categories_delete = NULL; // Initialize page object first

class ccategories_delete extends ccategories {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'categories';

	// Page object name
	var $PageObjName = 'categories_delete';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-error ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<table class=\"ewStdTable\"><tr><td><div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div></td></tr></table>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		$GLOBALS["Page"] = &$this;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (categories)
		if (!isset($GLOBALS["categories"]) || get_class($GLOBALS["categories"]) == "ccategories") {
			$GLOBALS["categories"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["categories"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'categories', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate("login.php");
		}
		$Security->TablePermission_Loading();
		$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName);
		$Security->TablePermission_Loaded();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate("login.php");
		}
		if (!$Security->CanDelete()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("categorieslist.php");
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->cat_id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();
		$this->Page_Redirecting($url);

		 // Close connection
		$conn->Close();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $TotalRecs = 0;
	var $RecCnt;
	var $RecKeys = array();
	var $Recordset;
	var $StartRowCnt = 1;
	var $RowCnt = 0;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load key parameters
		$this->RecKeys = $this->GetRecordKeys(); // Load record keys
		$sFilter = $this->GetKeyFilter();
		if ($sFilter == "")
			$this->Page_Terminate("categorieslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in categories class, categoriesinfo.php

		$this->CurrentFilter = $sFilter;

		// Get action
		if (@$_POST["a_delete"] <> "") {
			$this->CurrentAction = $_POST["a_delete"];
		} else {
			$this->CurrentAction = "I"; // Display record
		}
		switch ($this->CurrentAction) {
			case "D": // Delete
				$this->SendEmail = TRUE; // Send email on delete success
				if ($this->DeleteRows()) { // Delete rows
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("DeleteSuccess")); // Set up success message
					$this->Page_Terminate($this->getReturnUrl()); // Return to caller
				}
		}
	}

// No functions
	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {
		global $conn;

		// Call Recordset Selecting event
		$this->Recordset_Selecting($this->CurrentFilter);

		// Load List page SQL
		$sSql = $this->SelectSQL();
		if ($offset > -1 && $rowcnt > -1)
			$sSql .= " LIMIT $rowcnt OFFSET $offset";

		// Load recordset
		$rs = ew_LoadRecordset($sSql);

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
	}

	// Load row based on key values
	function LoadRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		global $conn;
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->cat_id->setDbValue($rs->fields('cat_id'));
		$this->cat_name->setDbValue($rs->fields('cat_name'));
		$this->cat_description->setDbValue($rs->fields('cat_description'));
		$this->cat_parent_id->setDbValue($rs->fields('cat_parent_id'));
		$this->cat_is_offer->setDbValue($rs->fields('cat_is_offer'));
		$this->cat_is_competition->setDbValue($rs->fields('cat_is_competition'));
		$this->cat_order->setDbValue($rs->fields('cat_order'));
		$this->cat_status->setDbValue($rs->fields('cat_status'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->cat_id->DbValue = $row['cat_id'];
		$this->cat_name->DbValue = $row['cat_name'];
		$this->cat_description->DbValue = $row['cat_description'];
		$this->cat_parent_id->DbValue = $row['cat_parent_id'];
		$this->cat_is_offer->DbValue = $row['cat_is_offer'];
		$this->cat_is_competition->DbValue = $row['cat_is_competition'];
		$this->cat_order->DbValue = $row['cat_order'];
		$this->cat_status->DbValue = $row['cat_status'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// cat_id
		// cat_name
		// cat_description
		// cat_parent_id
		// cat_is_offer
		// cat_is_competition
		// cat_order
		// cat_status

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// cat_id
			$this->cat_id->ViewValue = $this->cat_id->CurrentValue;
			$this->cat_id->ViewCustomAttributes = "";

			// cat_name
			$this->cat_name->ViewValue = $this->cat_name->CurrentValue;
			$this->cat_name->ViewCustomAttributes = "";

			// cat_description
			$this->cat_description->ViewValue = $this->cat_description->CurrentValue;
			$this->cat_description->ViewCustomAttributes = "";

			// cat_parent_id
			if (strval($this->cat_parent_id->CurrentValue) <> "") {
				$sFilterWrk = "`cat_id`" . ew_SearchString("=", $this->cat_parent_id->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `cat_id`, `cat_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `categories`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->cat_parent_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->cat_parent_id->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->cat_parent_id->ViewValue = $this->cat_parent_id->CurrentValue;
				}
			} else {
				$this->cat_parent_id->ViewValue = NULL;
			}
			$this->cat_parent_id->ViewCustomAttributes = "";

			// cat_is_offer
			if (strval($this->cat_is_offer->CurrentValue) <> "") {
				switch ($this->cat_is_offer->CurrentValue) {
					case $this->cat_is_offer->FldTagValue(1):
						$this->cat_is_offer->ViewValue = $this->cat_is_offer->FldTagCaption(1) <> "" ? $this->cat_is_offer->FldTagCaption(1) : $this->cat_is_offer->CurrentValue;
						break;
					case $this->cat_is_offer->FldTagValue(2):
						$this->cat_is_offer->ViewValue = $this->cat_is_offer->FldTagCaption(2) <> "" ? $this->cat_is_offer->FldTagCaption(2) : $this->cat_is_offer->CurrentValue;
						break;
					default:
						$this->cat_is_offer->ViewValue = $this->cat_is_offer->CurrentValue;
				}
			} else {
				$this->cat_is_offer->ViewValue = NULL;
			}
			$this->cat_is_offer->ViewCustomAttributes = "";

			// cat_is_competition
			if (strval($this->cat_is_competition->CurrentValue) <> "") {
				switch ($this->cat_is_competition->CurrentValue) {
					case $this->cat_is_competition->FldTagValue(1):
						$this->cat_is_competition->ViewValue = $this->cat_is_competition->FldTagCaption(1) <> "" ? $this->cat_is_competition->FldTagCaption(1) : $this->cat_is_competition->CurrentValue;
						break;
					case $this->cat_is_competition->FldTagValue(2):
						$this->cat_is_competition->ViewValue = $this->cat_is_competition->FldTagCaption(2) <> "" ? $this->cat_is_competition->FldTagCaption(2) : $this->cat_is_competition->CurrentValue;
						break;
					default:
						$this->cat_is_competition->ViewValue = $this->cat_is_competition->CurrentValue;
				}
			} else {
				$this->cat_is_competition->ViewValue = NULL;
			}
			$this->cat_is_competition->ViewCustomAttributes = "";

			// cat_order
			$this->cat_order->ViewValue = $this->cat_order->CurrentValue;
			$this->cat_order->ViewValue = ew_FormatNumber($this->cat_order->ViewValue, 0, -2, -2, -2);
			$this->cat_order->ViewCustomAttributes = "";

			// cat_status
			if (strval($this->cat_status->CurrentValue) <> "") {
				switch ($this->cat_status->CurrentValue) {
					case $this->cat_status->FldTagValue(1):
						$this->cat_status->ViewValue = $this->cat_status->FldTagCaption(1) <> "" ? $this->cat_status->FldTagCaption(1) : $this->cat_status->CurrentValue;
						break;
					case $this->cat_status->FldTagValue(2):
						$this->cat_status->ViewValue = $this->cat_status->FldTagCaption(2) <> "" ? $this->cat_status->FldTagCaption(2) : $this->cat_status->CurrentValue;
						break;
					default:
						$this->cat_status->ViewValue = $this->cat_status->CurrentValue;
				}
			} else {
				$this->cat_status->ViewValue = NULL;
			}
			$this->cat_status->ViewCustomAttributes = "";

			// cat_id
			$this->cat_id->LinkCustomAttributes = "";
			$this->cat_id->HrefValue = "";
			$this->cat_id->TooltipValue = "";

			// cat_name
			$this->cat_name->LinkCustomAttributes = "";
			$this->cat_name->HrefValue = "";
			$this->cat_name->TooltipValue = "";

			// cat_description
			$this->cat_description->LinkCustomAttributes = "";
			$this->cat_description->HrefValue = "";
			$this->cat_description->TooltipValue = "";

			// cat_parent_id
			$this->cat_parent_id->LinkCustomAttributes = "";
			$this->cat_parent_id->HrefValue = "";
			$this->cat_parent_id->TooltipValue = "";

			// cat_is_offer
			$this->cat_is_offer->LinkCustomAttributes = "";
			$this->cat_is_offer->HrefValue = "";
			$this->cat_is_offer->TooltipValue = "";

			// cat_is_competition
			$this->cat_is_competition->LinkCustomAttributes = "";
			$this->cat_is_competition->HrefValue = "";
			$this->cat_is_competition->TooltipValue = "";

			// cat_order
			$this->cat_order->LinkCustomAttributes = "";
			$this->cat_order->HrefValue = "";
			$this->cat_order->TooltipValue = "";

			// cat_status
			$this->cat_status->LinkCustomAttributes = "";
			$this->cat_status->HrefValue = "";
			$this->cat_status->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	//
	// Delete records based on current filter
	//
	function DeleteRows() {
		global $conn, $Language, $Security;
		if (!$Security->CanDelete()) {
			$this->setFailureMessage($Language->Phrase("NoDeletePermission")); // No delete permission
			return FALSE;
		}
		$DeleteRows = TRUE;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE) {
			return FALSE;
		} elseif ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
			$rs->Close();
			return FALSE;

		//} else {
		//	$this->LoadRowValues($rs); // Load row values

		}
		$conn->BeginTrans();

		// Clone old rows
		$rsold = ($rs) ? $rs->GetRows() : array();
		if ($rs)
			$rs->Close();

		// Call row deleting event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$DeleteRows = $this->Row_Deleting($row);
				if (!$DeleteRows) break;
			}
		}
		if ($DeleteRows) {
			$sKey = "";
			foreach ($rsold as $row) {
				$sThisKey = "";
				if ($sThisKey <> "") $sThisKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
				$sThisKey .= $row['cat_id'];
				$this->LoadDbValues($row);
				$conn->raiseErrorFn = 'ew_ErrorFn';
				$DeleteRows = $this->Delete($row); // Delete
				$conn->raiseErrorFn = '';
				if ($DeleteRows === FALSE)
					break;
				if ($sKey <> "") $sKey .= ", ";
				$sKey .= $sThisKey;
			}
		} else {

			// Set up error message
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("DeleteCancelled"));
			}
		}
		if ($DeleteRows) {
			$conn->CommitTrans(); // Commit the changes
		} else {
			$conn->RollbackTrans(); // Rollback changes
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "categorieslist.php", $this->TableVar, TRUE);
		$PageId = "delete";
		$Breadcrumb->Add("delete", $PageId, ew_CurrentUrl());
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($categories_delete)) $categories_delete = new ccategories_delete();

// Page init
$categories_delete->Page_Init();

// Page main
$categories_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$categories_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var categories_delete = new ew_Page("categories_delete");
categories_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = categories_delete.PageID; // For backward compatibility

// Form object
var fcategoriesdelete = new ew_Form("fcategoriesdelete");

// Form_CustomValidate event
fcategoriesdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcategoriesdelete.ValidateRequired = true;
<?php } else { ?>
fcategoriesdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fcategoriesdelete.Lists["x_cat_parent_id"] = {"LinkField":"x_cat_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_cat_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($categories_delete->Recordset = $categories_delete->LoadRecordset())
	$categories_deleteTotalRecs = $categories_delete->Recordset->RecordCount(); // Get record count
if ($categories_deleteTotalRecs <= 0) { // No record found, exit
	if ($categories_delete->Recordset)
		$categories_delete->Recordset->Close();
	$categories_delete->Page_Terminate("categorieslist.php"); // Return to list
}
?>
<?php $Breadcrumb->Render(); ?>
<?php $categories_delete->ShowPageHeader(); ?>
<?php
$categories_delete->ShowMessage();
?>
<form name="fcategoriesdelete" id="fcategoriesdelete" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="categories">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($categories_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_categoriesdelete" class="ewTable ewTableSeparate">
<?php echo $categories->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($categories->cat_id->Visible) { // cat_id ?>
		<td><span id="elh_categories_cat_id" class="categories_cat_id"><?php echo $categories->cat_id->FldCaption() ?></span></td>
<?php } ?>
<?php if ($categories->cat_name->Visible) { // cat_name ?>
		<td><span id="elh_categories_cat_name" class="categories_cat_name"><?php echo $categories->cat_name->FldCaption() ?></span></td>
<?php } ?>
<?php if ($categories->cat_description->Visible) { // cat_description ?>
		<td><span id="elh_categories_cat_description" class="categories_cat_description"><?php echo $categories->cat_description->FldCaption() ?></span></td>
<?php } ?>
<?php if ($categories->cat_parent_id->Visible) { // cat_parent_id ?>
		<td><span id="elh_categories_cat_parent_id" class="categories_cat_parent_id"><?php echo $categories->cat_parent_id->FldCaption() ?></span></td>
<?php } ?>
<?php if ($categories->cat_is_offer->Visible) { // cat_is_offer ?>
		<td><span id="elh_categories_cat_is_offer" class="categories_cat_is_offer"><?php echo $categories->cat_is_offer->FldCaption() ?></span></td>
<?php } ?>
<?php if ($categories->cat_is_competition->Visible) { // cat_is_competition ?>
		<td><span id="elh_categories_cat_is_competition" class="categories_cat_is_competition"><?php echo $categories->cat_is_competition->FldCaption() ?></span></td>
<?php } ?>
<?php if ($categories->cat_order->Visible) { // cat_order ?>
		<td><span id="elh_categories_cat_order" class="categories_cat_order"><?php echo $categories->cat_order->FldCaption() ?></span></td>
<?php } ?>
<?php if ($categories->cat_status->Visible) { // cat_status ?>
		<td><span id="elh_categories_cat_status" class="categories_cat_status"><?php echo $categories->cat_status->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$categories_delete->RecCnt = 0;
$i = 0;
while (!$categories_delete->Recordset->EOF) {
	$categories_delete->RecCnt++;
	$categories_delete->RowCnt++;

	// Set row properties
	$categories->ResetAttrs();
	$categories->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$categories_delete->LoadRowValues($categories_delete->Recordset);

	// Render row
	$categories_delete->RenderRow();
?>
	<tr<?php echo $categories->RowAttributes() ?>>
<?php if ($categories->cat_id->Visible) { // cat_id ?>
		<td<?php echo $categories->cat_id->CellAttributes() ?>>
<span id="el<?php echo $categories_delete->RowCnt ?>_categories_cat_id" class="control-group categories_cat_id">
<span<?php echo $categories->cat_id->ViewAttributes() ?>>
<?php echo $categories->cat_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($categories->cat_name->Visible) { // cat_name ?>
		<td<?php echo $categories->cat_name->CellAttributes() ?>>
<span id="el<?php echo $categories_delete->RowCnt ?>_categories_cat_name" class="control-group categories_cat_name">
<span<?php echo $categories->cat_name->ViewAttributes() ?>>
<?php echo $categories->cat_name->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($categories->cat_description->Visible) { // cat_description ?>
		<td<?php echo $categories->cat_description->CellAttributes() ?>>
<span id="el<?php echo $categories_delete->RowCnt ?>_categories_cat_description" class="control-group categories_cat_description">
<span<?php echo $categories->cat_description->ViewAttributes() ?>>
<?php echo $categories->cat_description->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($categories->cat_parent_id->Visible) { // cat_parent_id ?>
		<td<?php echo $categories->cat_parent_id->CellAttributes() ?>>
<span id="el<?php echo $categories_delete->RowCnt ?>_categories_cat_parent_id" class="control-group categories_cat_parent_id">
<span<?php echo $categories->cat_parent_id->ViewAttributes() ?>>
<?php echo $categories->cat_parent_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($categories->cat_is_offer->Visible) { // cat_is_offer ?>
		<td<?php echo $categories->cat_is_offer->CellAttributes() ?>>
<span id="el<?php echo $categories_delete->RowCnt ?>_categories_cat_is_offer" class="control-group categories_cat_is_offer">
<span<?php echo $categories->cat_is_offer->ViewAttributes() ?>>
<?php echo $categories->cat_is_offer->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($categories->cat_is_competition->Visible) { // cat_is_competition ?>
		<td<?php echo $categories->cat_is_competition->CellAttributes() ?>>
<span id="el<?php echo $categories_delete->RowCnt ?>_categories_cat_is_competition" class="control-group categories_cat_is_competition">
<span<?php echo $categories->cat_is_competition->ViewAttributes() ?>>
<?php echo $categories->cat_is_competition->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($categories->cat_order->Visible) { // cat_order ?>
		<td<?php echo $categories->cat_order->CellAttributes() ?>>
<span id="el<?php echo $categories_delete->RowCnt ?>_categories_cat_order" class="control-group categories_cat_order">
<span<?php echo $categories->cat_order->ViewAttributes() ?>>
<?php echo $categories->cat_order->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($categories->cat_status->Visible) { // cat_status ?>
		<td<?php echo $categories->cat_status->CellAttributes() ?>>
<span id="el<?php echo $categories_delete->RowCnt ?>_categories_cat_status" class="control-group categories_cat_status">
<span<?php echo $categories->cat_status->ViewAttributes() ?>>
<?php echo $categories->cat_status->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$categories_delete->Recordset->MoveNext();
}
$categories_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</td></tr></table>
<div class="btn-group ewButtonGroup">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("DeleteBtn") ?></button>
</div>
</form>
<script type="text/javascript">
fcategoriesdelete.Init();
</script>
<?php
$categories_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$categories_delete->Page_Terminate();
?>
