<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "offer_detailsinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$offer_details_delete = NULL; // Initialize page object first

class coffer_details_delete extends coffer_details {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'offer_details';

	// Page object name
	var $PageObjName = 'offer_details_delete';

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

		// Table object (offer_details)
		if (!isset($GLOBALS["offer_details"]) || get_class($GLOBALS["offer_details"]) == "coffer_details") {
			$GLOBALS["offer_details"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["offer_details"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'offer_details', TRUE);

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
			$this->Page_Terminate("offer_detailslist.php");
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->offer_detail_id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
			$this->Page_Terminate("offer_detailslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in offer_details class, offer_detailsinfo.php

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
		$this->offer_detail_id->setDbValue($rs->fields('offer_detail_id'));
		$this->offer_id->setDbValue($rs->fields('offer_id'));
		$this->offer_top_image->Upload->DbValue = $rs->fields('offer_top_image');
		$this->offer_top_image->CurrentValue = $this->offer_top_image->Upload->DbValue;
		$this->offer_bottom_image->Upload->DbValue = $rs->fields('offer_bottom_image');
		$this->offer_bottom_image->CurrentValue = $this->offer_bottom_image->Upload->DbValue;
		$this->offer_start_date->setDbValue($rs->fields('offer_start_date'));
		$this->offer_end_date->setDbValue($rs->fields('offer_end_date'));
		$this->offer_start_time->setDbValue($rs->fields('offer_start_time'));
		$this->offer_end_time->setDbValue($rs->fields('offer_end_time'));
		$this->offer_rules->setDbValue($rs->fields('offer_rules'));
		$this->offer_value->setDbValue($rs->fields('offer_value'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->offer_detail_id->DbValue = $row['offer_detail_id'];
		$this->offer_id->DbValue = $row['offer_id'];
		$this->offer_top_image->Upload->DbValue = $row['offer_top_image'];
		$this->offer_bottom_image->Upload->DbValue = $row['offer_bottom_image'];
		$this->offer_start_date->DbValue = $row['offer_start_date'];
		$this->offer_end_date->DbValue = $row['offer_end_date'];
		$this->offer_start_time->DbValue = $row['offer_start_time'];
		$this->offer_end_time->DbValue = $row['offer_end_time'];
		$this->offer_rules->DbValue = $row['offer_rules'];
		$this->offer_value->DbValue = $row['offer_value'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// offer_detail_id
		// offer_id
		// offer_top_image
		// offer_bottom_image
		// offer_start_date
		// offer_end_date
		// offer_start_time
		// offer_end_time
		// offer_rules
		// offer_value

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// offer_detail_id
			$this->offer_detail_id->ViewValue = $this->offer_detail_id->CurrentValue;
			$this->offer_detail_id->ViewCustomAttributes = "";

			// offer_id
			if (strval($this->offer_id->CurrentValue) <> "") {
				$sFilterWrk = "`offer_id`" . ew_SearchString("=", $this->offer_id->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `offer_id`, `offer_title` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `offers`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->offer_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->offer_id->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->offer_id->ViewValue = $this->offer_id->CurrentValue;
				}
			} else {
				$this->offer_id->ViewValue = NULL;
			}
			$this->offer_id->ViewCustomAttributes = "";

			// offer_top_image
			if (!ew_Empty($this->offer_top_image->Upload->DbValue)) {
				$this->offer_top_image->ImageWidth = 80;
				$this->offer_top_image->ImageHeight = 0;
				$this->offer_top_image->ImageAlt = $this->offer_top_image->FldAlt();
				$this->offer_top_image->ViewValue = ew_UploadPathEx(FALSE, $this->offer_top_image->UploadPath) . $this->offer_top_image->Upload->DbValue;
			} else {
				$this->offer_top_image->ViewValue = "";
			}
			$this->offer_top_image->ViewCustomAttributes = "";

			// offer_bottom_image
			if (!ew_Empty($this->offer_bottom_image->Upload->DbValue)) {
				$this->offer_bottom_image->ImageWidth = 80;
				$this->offer_bottom_image->ImageHeight = 0;
				$this->offer_bottom_image->ImageAlt = $this->offer_bottom_image->FldAlt();
				$this->offer_bottom_image->ViewValue = ew_UploadPathEx(FALSE, $this->offer_bottom_image->UploadPath) . $this->offer_bottom_image->Upload->DbValue;
			} else {
				$this->offer_bottom_image->ViewValue = "";
			}
			$this->offer_bottom_image->ViewCustomAttributes = "";

			// offer_start_date
			$this->offer_start_date->ViewValue = $this->offer_start_date->CurrentValue;
			$this->offer_start_date->ViewValue = ew_FormatDateTime($this->offer_start_date->ViewValue, 7);
			$this->offer_start_date->ViewCustomAttributes = "";

			// offer_end_date
			$this->offer_end_date->ViewValue = $this->offer_end_date->CurrentValue;
			$this->offer_end_date->ViewValue = ew_FormatDateTime($this->offer_end_date->ViewValue, 7);
			$this->offer_end_date->ViewCustomAttributes = "";

			// offer_start_time
			$this->offer_start_time->ViewValue = $this->offer_start_time->CurrentValue;
			$this->offer_start_time->ViewValue = ew_FormatDateTime($this->offer_start_time->ViewValue, 3);
			$this->offer_start_time->ViewCustomAttributes = "";

			// offer_end_time
			$this->offer_end_time->ViewValue = $this->offer_end_time->CurrentValue;
			$this->offer_end_time->ViewValue = ew_FormatDateTime($this->offer_end_time->ViewValue, 3);
			$this->offer_end_time->ViewCustomAttributes = "";

			// offer_rules
			$this->offer_rules->ViewValue = $this->offer_rules->CurrentValue;
			$this->offer_rules->ViewCustomAttributes = "";

			// offer_value
			$this->offer_value->ViewValue = $this->offer_value->CurrentValue;
			$this->offer_value->ViewCustomAttributes = "";

			// offer_detail_id
			$this->offer_detail_id->LinkCustomAttributes = "";
			$this->offer_detail_id->HrefValue = "";
			$this->offer_detail_id->TooltipValue = "";

			// offer_id
			$this->offer_id->LinkCustomAttributes = "";
			$this->offer_id->HrefValue = "";
			$this->offer_id->TooltipValue = "";

			// offer_top_image
			$this->offer_top_image->LinkCustomAttributes = "";
			$this->offer_top_image->HrefValue = "";
			$this->offer_top_image->HrefValue2 = $this->offer_top_image->UploadPath . $this->offer_top_image->Upload->DbValue;
			$this->offer_top_image->TooltipValue = "";

			// offer_bottom_image
			$this->offer_bottom_image->LinkCustomAttributes = "";
			$this->offer_bottom_image->HrefValue = "";
			$this->offer_bottom_image->HrefValue2 = $this->offer_bottom_image->UploadPath . $this->offer_bottom_image->Upload->DbValue;
			$this->offer_bottom_image->TooltipValue = "";

			// offer_start_date
			$this->offer_start_date->LinkCustomAttributes = "";
			$this->offer_start_date->HrefValue = "";
			$this->offer_start_date->TooltipValue = "";

			// offer_end_date
			$this->offer_end_date->LinkCustomAttributes = "";
			$this->offer_end_date->HrefValue = "";
			$this->offer_end_date->TooltipValue = "";

			// offer_start_time
			$this->offer_start_time->LinkCustomAttributes = "";
			$this->offer_start_time->HrefValue = "";
			$this->offer_start_time->TooltipValue = "";

			// offer_end_time
			$this->offer_end_time->LinkCustomAttributes = "";
			$this->offer_end_time->HrefValue = "";
			$this->offer_end_time->TooltipValue = "";

			// offer_rules
			$this->offer_rules->LinkCustomAttributes = "";
			$this->offer_rules->HrefValue = "";
			$this->offer_rules->TooltipValue = "";

			// offer_value
			$this->offer_value->LinkCustomAttributes = "";
			$this->offer_value->HrefValue = "";
			$this->offer_value->TooltipValue = "";
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
				$sThisKey .= $row['offer_detail_id'];
				$this->LoadDbValues($row);
				@unlink(ew_UploadPathEx(TRUE, $this->offer_top_image->OldUploadPath) . $row['offer_top_image']);
				@unlink(ew_UploadPathEx(TRUE, $this->offer_bottom_image->OldUploadPath) . $row['offer_bottom_image']);
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
		$Breadcrumb->Add("list", $this->TableVar, "offer_detailslist.php", $this->TableVar, TRUE);
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
if (!isset($offer_details_delete)) $offer_details_delete = new coffer_details_delete();

// Page init
$offer_details_delete->Page_Init();

// Page main
$offer_details_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$offer_details_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var offer_details_delete = new ew_Page("offer_details_delete");
offer_details_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = offer_details_delete.PageID; // For backward compatibility

// Form object
var foffer_detailsdelete = new ew_Form("foffer_detailsdelete");

// Form_CustomValidate event
foffer_detailsdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
foffer_detailsdelete.ValidateRequired = true;
<?php } else { ?>
foffer_detailsdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
foffer_detailsdelete.Lists["x_offer_id"] = {"LinkField":"x_offer_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_offer_title","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($offer_details_delete->Recordset = $offer_details_delete->LoadRecordset())
	$offer_details_deleteTotalRecs = $offer_details_delete->Recordset->RecordCount(); // Get record count
if ($offer_details_deleteTotalRecs <= 0) { // No record found, exit
	if ($offer_details_delete->Recordset)
		$offer_details_delete->Recordset->Close();
	$offer_details_delete->Page_Terminate("offer_detailslist.php"); // Return to list
}
?>
<?php $Breadcrumb->Render(); ?>
<?php $offer_details_delete->ShowPageHeader(); ?>
<?php
$offer_details_delete->ShowMessage();
?>
<form name="foffer_detailsdelete" id="foffer_detailsdelete" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="offer_details">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($offer_details_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_offer_detailsdelete" class="ewTable ewTableSeparate">
<?php echo $offer_details->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($offer_details->offer_detail_id->Visible) { // offer_detail_id ?>
		<td><span id="elh_offer_details_offer_detail_id" class="offer_details_offer_detail_id"><?php echo $offer_details->offer_detail_id->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_details->offer_id->Visible) { // offer_id ?>
		<td><span id="elh_offer_details_offer_id" class="offer_details_offer_id"><?php echo $offer_details->offer_id->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_details->offer_top_image->Visible) { // offer_top_image ?>
		<td><span id="elh_offer_details_offer_top_image" class="offer_details_offer_top_image"><?php echo $offer_details->offer_top_image->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_details->offer_bottom_image->Visible) { // offer_bottom_image ?>
		<td><span id="elh_offer_details_offer_bottom_image" class="offer_details_offer_bottom_image"><?php echo $offer_details->offer_bottom_image->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_details->offer_start_date->Visible) { // offer_start_date ?>
		<td><span id="elh_offer_details_offer_start_date" class="offer_details_offer_start_date"><?php echo $offer_details->offer_start_date->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_details->offer_end_date->Visible) { // offer_end_date ?>
		<td><span id="elh_offer_details_offer_end_date" class="offer_details_offer_end_date"><?php echo $offer_details->offer_end_date->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_details->offer_start_time->Visible) { // offer_start_time ?>
		<td><span id="elh_offer_details_offer_start_time" class="offer_details_offer_start_time"><?php echo $offer_details->offer_start_time->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_details->offer_end_time->Visible) { // offer_end_time ?>
		<td><span id="elh_offer_details_offer_end_time" class="offer_details_offer_end_time"><?php echo $offer_details->offer_end_time->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_details->offer_rules->Visible) { // offer_rules ?>
		<td><span id="elh_offer_details_offer_rules" class="offer_details_offer_rules"><?php echo $offer_details->offer_rules->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_details->offer_value->Visible) { // offer_value ?>
		<td><span id="elh_offer_details_offer_value" class="offer_details_offer_value"><?php echo $offer_details->offer_value->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$offer_details_delete->RecCnt = 0;
$i = 0;
while (!$offer_details_delete->Recordset->EOF) {
	$offer_details_delete->RecCnt++;
	$offer_details_delete->RowCnt++;

	// Set row properties
	$offer_details->ResetAttrs();
	$offer_details->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$offer_details_delete->LoadRowValues($offer_details_delete->Recordset);

	// Render row
	$offer_details_delete->RenderRow();
?>
	<tr<?php echo $offer_details->RowAttributes() ?>>
<?php if ($offer_details->offer_detail_id->Visible) { // offer_detail_id ?>
		<td<?php echo $offer_details->offer_detail_id->CellAttributes() ?>>
<span id="el<?php echo $offer_details_delete->RowCnt ?>_offer_details_offer_detail_id" class="control-group offer_details_offer_detail_id">
<span<?php echo $offer_details->offer_detail_id->ViewAttributes() ?>>
<?php echo $offer_details->offer_detail_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($offer_details->offer_id->Visible) { // offer_id ?>
		<td<?php echo $offer_details->offer_id->CellAttributes() ?>>
<span id="el<?php echo $offer_details_delete->RowCnt ?>_offer_details_offer_id" class="control-group offer_details_offer_id">
<span<?php echo $offer_details->offer_id->ViewAttributes() ?>>
<?php echo $offer_details->offer_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($offer_details->offer_top_image->Visible) { // offer_top_image ?>
		<td<?php echo $offer_details->offer_top_image->CellAttributes() ?>>
<span id="el<?php echo $offer_details_delete->RowCnt ?>_offer_details_offer_top_image" class="control-group offer_details_offer_top_image">
<span>
<?php if ($offer_details->offer_top_image->LinkAttributes() <> "") { ?>
<?php if (!empty($offer_details->offer_top_image->Upload->DbValue)) { ?>
<?php echo ew_GetFileViewTag($offer_details->offer_top_image, $offer_details->offer_top_image->ListViewValue()) ?>
<?php } elseif (!in_array($offer_details->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($offer_details->offer_top_image->Upload->DbValue)) { ?>
<?php echo ew_GetFileViewTag($offer_details->offer_top_image, $offer_details->offer_top_image->ListViewValue()) ?>
<?php } elseif (!in_array($offer_details->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</span>
</td>
<?php } ?>
<?php if ($offer_details->offer_bottom_image->Visible) { // offer_bottom_image ?>
		<td<?php echo $offer_details->offer_bottom_image->CellAttributes() ?>>
<span id="el<?php echo $offer_details_delete->RowCnt ?>_offer_details_offer_bottom_image" class="control-group offer_details_offer_bottom_image">
<span>
<?php if ($offer_details->offer_bottom_image->LinkAttributes() <> "") { ?>
<?php if (!empty($offer_details->offer_bottom_image->Upload->DbValue)) { ?>
<?php echo ew_GetFileViewTag($offer_details->offer_bottom_image, $offer_details->offer_bottom_image->ListViewValue()) ?>
<?php } elseif (!in_array($offer_details->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($offer_details->offer_bottom_image->Upload->DbValue)) { ?>
<?php echo ew_GetFileViewTag($offer_details->offer_bottom_image, $offer_details->offer_bottom_image->ListViewValue()) ?>
<?php } elseif (!in_array($offer_details->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</span>
</td>
<?php } ?>
<?php if ($offer_details->offer_start_date->Visible) { // offer_start_date ?>
		<td<?php echo $offer_details->offer_start_date->CellAttributes() ?>>
<span id="el<?php echo $offer_details_delete->RowCnt ?>_offer_details_offer_start_date" class="control-group offer_details_offer_start_date">
<span<?php echo $offer_details->offer_start_date->ViewAttributes() ?>>
<?php echo $offer_details->offer_start_date->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($offer_details->offer_end_date->Visible) { // offer_end_date ?>
		<td<?php echo $offer_details->offer_end_date->CellAttributes() ?>>
<span id="el<?php echo $offer_details_delete->RowCnt ?>_offer_details_offer_end_date" class="control-group offer_details_offer_end_date">
<span<?php echo $offer_details->offer_end_date->ViewAttributes() ?>>
<?php echo $offer_details->offer_end_date->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($offer_details->offer_start_time->Visible) { // offer_start_time ?>
		<td<?php echo $offer_details->offer_start_time->CellAttributes() ?>>
<span id="el<?php echo $offer_details_delete->RowCnt ?>_offer_details_offer_start_time" class="control-group offer_details_offer_start_time">
<span<?php echo $offer_details->offer_start_time->ViewAttributes() ?>>
<?php echo $offer_details->offer_start_time->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($offer_details->offer_end_time->Visible) { // offer_end_time ?>
		<td<?php echo $offer_details->offer_end_time->CellAttributes() ?>>
<span id="el<?php echo $offer_details_delete->RowCnt ?>_offer_details_offer_end_time" class="control-group offer_details_offer_end_time">
<span<?php echo $offer_details->offer_end_time->ViewAttributes() ?>>
<?php echo $offer_details->offer_end_time->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($offer_details->offer_rules->Visible) { // offer_rules ?>
		<td<?php echo $offer_details->offer_rules->CellAttributes() ?>>
<span id="el<?php echo $offer_details_delete->RowCnt ?>_offer_details_offer_rules" class="control-group offer_details_offer_rules">
<span<?php echo $offer_details->offer_rules->ViewAttributes() ?>>
<?php echo $offer_details->offer_rules->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($offer_details->offer_value->Visible) { // offer_value ?>
		<td<?php echo $offer_details->offer_value->CellAttributes() ?>>
<span id="el<?php echo $offer_details_delete->RowCnt ?>_offer_details_offer_value" class="control-group offer_details_offer_value">
<span<?php echo $offer_details->offer_value->ViewAttributes() ?>>
<?php echo $offer_details->offer_value->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$offer_details_delete->Recordset->MoveNext();
}
$offer_details_delete->Recordset->Close();
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
foffer_detailsdelete.Init();
</script>
<?php
$offer_details_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$offer_details_delete->Page_Terminate();
?>
