<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "offer_locationsinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$offer_locations_delete = NULL; // Initialize page object first

class coffer_locations_delete extends coffer_locations {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'offer_locations';

	// Page object name
	var $PageObjName = 'offer_locations_delete';

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

		// Table object (offer_locations)
		if (!isset($GLOBALS["offer_locations"]) || get_class($GLOBALS["offer_locations"]) == "coffer_locations") {
			$GLOBALS["offer_locations"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["offer_locations"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'offer_locations', TRUE);

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
			$this->Page_Terminate("offer_locationslist.php");
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->location_id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
			$this->Page_Terminate("offer_locationslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in offer_locations class, offer_locationsinfo.php

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
		$this->location_id->setDbValue($rs->fields('location_id'));
		$this->offer_id->setDbValue($rs->fields('offer_id'));
		$this->location_name->setDbValue($rs->fields('location_name'));
		$this->location_address->setDbValue($rs->fields('location_address'));
		$this->location_map_x->setDbValue($rs->fields('location_map_x'));
		$this->location_map_y->setDbValue($rs->fields('location_map_y'));
		$this->location_status->setDbValue($rs->fields('location_status'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->location_id->DbValue = $row['location_id'];
		$this->offer_id->DbValue = $row['offer_id'];
		$this->location_name->DbValue = $row['location_name'];
		$this->location_address->DbValue = $row['location_address'];
		$this->location_map_x->DbValue = $row['location_map_x'];
		$this->location_map_y->DbValue = $row['location_map_y'];
		$this->location_status->DbValue = $row['location_status'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// location_id
		// offer_id
		// location_name
		// location_address
		// location_map_x
		// location_map_y
		// location_status

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// location_id
			$this->location_id->ViewValue = $this->location_id->CurrentValue;
			$this->location_id->ViewCustomAttributes = "";

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

			// location_name
			$this->location_name->ViewValue = $this->location_name->CurrentValue;
			$this->location_name->ViewCustomAttributes = "";

			// location_address
			$this->location_address->ViewValue = $this->location_address->CurrentValue;
			$this->location_address->ViewCustomAttributes = "";

			// location_map_x
			$this->location_map_x->ViewValue = $this->location_map_x->CurrentValue;
			$this->location_map_x->ViewCustomAttributes = "";

			// location_map_y
			$this->location_map_y->ViewValue = $this->location_map_y->CurrentValue;
			$this->location_map_y->ViewCustomAttributes = "";

			// location_status
			if (strval($this->location_status->CurrentValue) <> "") {
				switch ($this->location_status->CurrentValue) {
					case $this->location_status->FldTagValue(1):
						$this->location_status->ViewValue = $this->location_status->FldTagCaption(1) <> "" ? $this->location_status->FldTagCaption(1) : $this->location_status->CurrentValue;
						break;
					case $this->location_status->FldTagValue(2):
						$this->location_status->ViewValue = $this->location_status->FldTagCaption(2) <> "" ? $this->location_status->FldTagCaption(2) : $this->location_status->CurrentValue;
						break;
					default:
						$this->location_status->ViewValue = $this->location_status->CurrentValue;
				}
			} else {
				$this->location_status->ViewValue = NULL;
			}
			$this->location_status->ViewCustomAttributes = "";

			// location_id
			$this->location_id->LinkCustomAttributes = "";
			$this->location_id->HrefValue = "";
			$this->location_id->TooltipValue = "";

			// offer_id
			$this->offer_id->LinkCustomAttributes = "";
			$this->offer_id->HrefValue = "";
			$this->offer_id->TooltipValue = "";

			// location_name
			$this->location_name->LinkCustomAttributes = "";
			$this->location_name->HrefValue = "";
			$this->location_name->TooltipValue = "";

			// location_address
			$this->location_address->LinkCustomAttributes = "";
			$this->location_address->HrefValue = "";
			$this->location_address->TooltipValue = "";

			// location_map_x
			$this->location_map_x->LinkCustomAttributes = "";
			$this->location_map_x->HrefValue = "";
			$this->location_map_x->TooltipValue = "";

			// location_map_y
			$this->location_map_y->LinkCustomAttributes = "";
			$this->location_map_y->HrefValue = "";
			$this->location_map_y->TooltipValue = "";

			// location_status
			$this->location_status->LinkCustomAttributes = "";
			$this->location_status->HrefValue = "";
			$this->location_status->TooltipValue = "";
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
				$sThisKey .= $row['location_id'];
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
		$Breadcrumb->Add("list", $this->TableVar, "offer_locationslist.php", $this->TableVar, TRUE);
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
if (!isset($offer_locations_delete)) $offer_locations_delete = new coffer_locations_delete();

// Page init
$offer_locations_delete->Page_Init();

// Page main
$offer_locations_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$offer_locations_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var offer_locations_delete = new ew_Page("offer_locations_delete");
offer_locations_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = offer_locations_delete.PageID; // For backward compatibility

// Form object
var foffer_locationsdelete = new ew_Form("foffer_locationsdelete");

// Form_CustomValidate event
foffer_locationsdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
foffer_locationsdelete.ValidateRequired = true;
<?php } else { ?>
foffer_locationsdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
foffer_locationsdelete.Lists["x_offer_id"] = {"LinkField":"x_offer_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_offer_title","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($offer_locations_delete->Recordset = $offer_locations_delete->LoadRecordset())
	$offer_locations_deleteTotalRecs = $offer_locations_delete->Recordset->RecordCount(); // Get record count
if ($offer_locations_deleteTotalRecs <= 0) { // No record found, exit
	if ($offer_locations_delete->Recordset)
		$offer_locations_delete->Recordset->Close();
	$offer_locations_delete->Page_Terminate("offer_locationslist.php"); // Return to list
}
?>
<?php $Breadcrumb->Render(); ?>
<?php $offer_locations_delete->ShowPageHeader(); ?>
<?php
$offer_locations_delete->ShowMessage();
?>
<form name="foffer_locationsdelete" id="foffer_locationsdelete" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="offer_locations">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($offer_locations_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_offer_locationsdelete" class="ewTable ewTableSeparate">
<?php echo $offer_locations->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($offer_locations->location_id->Visible) { // location_id ?>
		<td><span id="elh_offer_locations_location_id" class="offer_locations_location_id"><?php echo $offer_locations->location_id->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_locations->offer_id->Visible) { // offer_id ?>
		<td><span id="elh_offer_locations_offer_id" class="offer_locations_offer_id"><?php echo $offer_locations->offer_id->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_locations->location_name->Visible) { // location_name ?>
		<td><span id="elh_offer_locations_location_name" class="offer_locations_location_name"><?php echo $offer_locations->location_name->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_locations->location_address->Visible) { // location_address ?>
		<td><span id="elh_offer_locations_location_address" class="offer_locations_location_address"><?php echo $offer_locations->location_address->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_locations->location_map_x->Visible) { // location_map_x ?>
		<td><span id="elh_offer_locations_location_map_x" class="offer_locations_location_map_x"><?php echo $offer_locations->location_map_x->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_locations->location_map_y->Visible) { // location_map_y ?>
		<td><span id="elh_offer_locations_location_map_y" class="offer_locations_location_map_y"><?php echo $offer_locations->location_map_y->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_locations->location_status->Visible) { // location_status ?>
		<td><span id="elh_offer_locations_location_status" class="offer_locations_location_status"><?php echo $offer_locations->location_status->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$offer_locations_delete->RecCnt = 0;
$i = 0;
while (!$offer_locations_delete->Recordset->EOF) {
	$offer_locations_delete->RecCnt++;
	$offer_locations_delete->RowCnt++;

	// Set row properties
	$offer_locations->ResetAttrs();
	$offer_locations->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$offer_locations_delete->LoadRowValues($offer_locations_delete->Recordset);

	// Render row
	$offer_locations_delete->RenderRow();
?>
	<tr<?php echo $offer_locations->RowAttributes() ?>>
<?php if ($offer_locations->location_id->Visible) { // location_id ?>
		<td<?php echo $offer_locations->location_id->CellAttributes() ?>>
<span id="el<?php echo $offer_locations_delete->RowCnt ?>_offer_locations_location_id" class="control-group offer_locations_location_id">
<span<?php echo $offer_locations->location_id->ViewAttributes() ?>>
<?php echo $offer_locations->location_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($offer_locations->offer_id->Visible) { // offer_id ?>
		<td<?php echo $offer_locations->offer_id->CellAttributes() ?>>
<span id="el<?php echo $offer_locations_delete->RowCnt ?>_offer_locations_offer_id" class="control-group offer_locations_offer_id">
<span<?php echo $offer_locations->offer_id->ViewAttributes() ?>>
<?php echo $offer_locations->offer_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($offer_locations->location_name->Visible) { // location_name ?>
		<td<?php echo $offer_locations->location_name->CellAttributes() ?>>
<span id="el<?php echo $offer_locations_delete->RowCnt ?>_offer_locations_location_name" class="control-group offer_locations_location_name">
<span<?php echo $offer_locations->location_name->ViewAttributes() ?>>
<?php echo $offer_locations->location_name->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($offer_locations->location_address->Visible) { // location_address ?>
		<td<?php echo $offer_locations->location_address->CellAttributes() ?>>
<span id="el<?php echo $offer_locations_delete->RowCnt ?>_offer_locations_location_address" class="control-group offer_locations_location_address">
<span<?php echo $offer_locations->location_address->ViewAttributes() ?>>
<?php echo $offer_locations->location_address->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($offer_locations->location_map_x->Visible) { // location_map_x ?>
		<td<?php echo $offer_locations->location_map_x->CellAttributes() ?>>
<span id="el<?php echo $offer_locations_delete->RowCnt ?>_offer_locations_location_map_x" class="control-group offer_locations_location_map_x">
<span<?php echo $offer_locations->location_map_x->ViewAttributes() ?>>
<?php echo $offer_locations->location_map_x->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($offer_locations->location_map_y->Visible) { // location_map_y ?>
		<td<?php echo $offer_locations->location_map_y->CellAttributes() ?>>
<span id="el<?php echo $offer_locations_delete->RowCnt ?>_offer_locations_location_map_y" class="control-group offer_locations_location_map_y">
<span<?php echo $offer_locations->location_map_y->ViewAttributes() ?>>
<?php echo $offer_locations->location_map_y->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($offer_locations->location_status->Visible) { // location_status ?>
		<td<?php echo $offer_locations->location_status->CellAttributes() ?>>
<span id="el<?php echo $offer_locations_delete->RowCnt ?>_offer_locations_location_status" class="control-group offer_locations_location_status">
<span<?php echo $offer_locations->location_status->ViewAttributes() ?>>
<?php echo $offer_locations->location_status->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$offer_locations_delete->Recordset->MoveNext();
}
$offer_locations_delete->Recordset->Close();
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
foffer_locationsdelete.Init();
</script>
<?php
$offer_locations_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$offer_locations_delete->Page_Terminate();
?>
