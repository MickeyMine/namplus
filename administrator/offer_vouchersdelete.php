<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "offer_vouchersinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$offer_vouchers_delete = NULL; // Initialize page object first

class coffer_vouchers_delete extends coffer_vouchers {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'offer_vouchers';

	// Page object name
	var $PageObjName = 'offer_vouchers_delete';

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

		// Table object (offer_vouchers)
		if (!isset($GLOBALS["offer_vouchers"]) || get_class($GLOBALS["offer_vouchers"]) == "coffer_vouchers") {
			$GLOBALS["offer_vouchers"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["offer_vouchers"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'offer_vouchers', TRUE);

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
			$this->Page_Terminate("offer_voucherslist.php");
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->voucher_id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
			$this->Page_Terminate("offer_voucherslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in offer_vouchers class, offer_vouchersinfo.php

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
		$this->voucher_id->setDbValue($rs->fields('voucher_id'));
		$this->voucher_number->setDbValue($rs->fields('voucher_number'));
		$this->voucher_offer_id->setDbValue($rs->fields('voucher_offer_id'));
		$this->voucher_status->setDbValue($rs->fields('voucher_status'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->voucher_id->DbValue = $row['voucher_id'];
		$this->voucher_number->DbValue = $row['voucher_number'];
		$this->voucher_offer_id->DbValue = $row['voucher_offer_id'];
		$this->voucher_status->DbValue = $row['voucher_status'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// voucher_id
		// voucher_number
		// voucher_offer_id
		// voucher_status

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// voucher_id
			$this->voucher_id->ViewValue = $this->voucher_id->CurrentValue;
			$this->voucher_id->ViewCustomAttributes = "";

			// voucher_number
			$this->voucher_number->ViewValue = $this->voucher_number->CurrentValue;
			$this->voucher_number->ViewCustomAttributes = "";

			// voucher_offer_id
			if (strval($this->voucher_offer_id->CurrentValue) <> "") {
				$sFilterWrk = "`offer_id`" . ew_SearchString("=", $this->voucher_offer_id->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `offer_id`, `offer_title` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `offers`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->voucher_offer_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->voucher_offer_id->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->voucher_offer_id->ViewValue = $this->voucher_offer_id->CurrentValue;
				}
			} else {
				$this->voucher_offer_id->ViewValue = NULL;
			}
			$this->voucher_offer_id->ViewCustomAttributes = "";

			// voucher_status
			if (strval($this->voucher_status->CurrentValue) <> "") {
				switch ($this->voucher_status->CurrentValue) {
					case $this->voucher_status->FldTagValue(1):
						$this->voucher_status->ViewValue = $this->voucher_status->FldTagCaption(1) <> "" ? $this->voucher_status->FldTagCaption(1) : $this->voucher_status->CurrentValue;
						break;
					case $this->voucher_status->FldTagValue(2):
						$this->voucher_status->ViewValue = $this->voucher_status->FldTagCaption(2) <> "" ? $this->voucher_status->FldTagCaption(2) : $this->voucher_status->CurrentValue;
						break;
					default:
						$this->voucher_status->ViewValue = $this->voucher_status->CurrentValue;
				}
			} else {
				$this->voucher_status->ViewValue = NULL;
			}
			$this->voucher_status->ViewCustomAttributes = "";

			// voucher_id
			$this->voucher_id->LinkCustomAttributes = "";
			$this->voucher_id->HrefValue = "";
			$this->voucher_id->TooltipValue = "";

			// voucher_number
			$this->voucher_number->LinkCustomAttributes = "";
			$this->voucher_number->HrefValue = "";
			$this->voucher_number->TooltipValue = "";

			// voucher_offer_id
			$this->voucher_offer_id->LinkCustomAttributes = "";
			$this->voucher_offer_id->HrefValue = "";
			$this->voucher_offer_id->TooltipValue = "";

			// voucher_status
			$this->voucher_status->LinkCustomAttributes = "";
			$this->voucher_status->HrefValue = "";
			$this->voucher_status->TooltipValue = "";
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
				$sThisKey .= $row['voucher_id'];
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
		$Breadcrumb->Add("list", $this->TableVar, "offer_voucherslist.php", $this->TableVar, TRUE);
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
if (!isset($offer_vouchers_delete)) $offer_vouchers_delete = new coffer_vouchers_delete();

// Page init
$offer_vouchers_delete->Page_Init();

// Page main
$offer_vouchers_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$offer_vouchers_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var offer_vouchers_delete = new ew_Page("offer_vouchers_delete");
offer_vouchers_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = offer_vouchers_delete.PageID; // For backward compatibility

// Form object
var foffer_vouchersdelete = new ew_Form("foffer_vouchersdelete");

// Form_CustomValidate event
foffer_vouchersdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
foffer_vouchersdelete.ValidateRequired = true;
<?php } else { ?>
foffer_vouchersdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
foffer_vouchersdelete.Lists["x_voucher_offer_id"] = {"LinkField":"x_offer_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_offer_title","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($offer_vouchers_delete->Recordset = $offer_vouchers_delete->LoadRecordset())
	$offer_vouchers_deleteTotalRecs = $offer_vouchers_delete->Recordset->RecordCount(); // Get record count
if ($offer_vouchers_deleteTotalRecs <= 0) { // No record found, exit
	if ($offer_vouchers_delete->Recordset)
		$offer_vouchers_delete->Recordset->Close();
	$offer_vouchers_delete->Page_Terminate("offer_voucherslist.php"); // Return to list
}
?>
<?php $Breadcrumb->Render(); ?>
<?php $offer_vouchers_delete->ShowPageHeader(); ?>
<?php
$offer_vouchers_delete->ShowMessage();
?>
<form name="foffer_vouchersdelete" id="foffer_vouchersdelete" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="offer_vouchers">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($offer_vouchers_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_offer_vouchersdelete" class="ewTable ewTableSeparate">
<?php echo $offer_vouchers->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($offer_vouchers->voucher_id->Visible) { // voucher_id ?>
		<td><span id="elh_offer_vouchers_voucher_id" class="offer_vouchers_voucher_id"><?php echo $offer_vouchers->voucher_id->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_vouchers->voucher_number->Visible) { // voucher_number ?>
		<td><span id="elh_offer_vouchers_voucher_number" class="offer_vouchers_voucher_number"><?php echo $offer_vouchers->voucher_number->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_vouchers->voucher_offer_id->Visible) { // voucher_offer_id ?>
		<td><span id="elh_offer_vouchers_voucher_offer_id" class="offer_vouchers_voucher_offer_id"><?php echo $offer_vouchers->voucher_offer_id->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_vouchers->voucher_status->Visible) { // voucher_status ?>
		<td><span id="elh_offer_vouchers_voucher_status" class="offer_vouchers_voucher_status"><?php echo $offer_vouchers->voucher_status->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$offer_vouchers_delete->RecCnt = 0;
$i = 0;
while (!$offer_vouchers_delete->Recordset->EOF) {
	$offer_vouchers_delete->RecCnt++;
	$offer_vouchers_delete->RowCnt++;

	// Set row properties
	$offer_vouchers->ResetAttrs();
	$offer_vouchers->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$offer_vouchers_delete->LoadRowValues($offer_vouchers_delete->Recordset);

	// Render row
	$offer_vouchers_delete->RenderRow();
?>
	<tr<?php echo $offer_vouchers->RowAttributes() ?>>
<?php if ($offer_vouchers->voucher_id->Visible) { // voucher_id ?>
		<td<?php echo $offer_vouchers->voucher_id->CellAttributes() ?>>
<span id="el<?php echo $offer_vouchers_delete->RowCnt ?>_offer_vouchers_voucher_id" class="control-group offer_vouchers_voucher_id">
<span<?php echo $offer_vouchers->voucher_id->ViewAttributes() ?>>
<?php echo $offer_vouchers->voucher_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($offer_vouchers->voucher_number->Visible) { // voucher_number ?>
		<td<?php echo $offer_vouchers->voucher_number->CellAttributes() ?>>
<span id="el<?php echo $offer_vouchers_delete->RowCnt ?>_offer_vouchers_voucher_number" class="control-group offer_vouchers_voucher_number">
<span<?php echo $offer_vouchers->voucher_number->ViewAttributes() ?>>
<?php echo $offer_vouchers->voucher_number->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($offer_vouchers->voucher_offer_id->Visible) { // voucher_offer_id ?>
		<td<?php echo $offer_vouchers->voucher_offer_id->CellAttributes() ?>>
<span id="el<?php echo $offer_vouchers_delete->RowCnt ?>_offer_vouchers_voucher_offer_id" class="control-group offer_vouchers_voucher_offer_id">
<span<?php echo $offer_vouchers->voucher_offer_id->ViewAttributes() ?>>
<?php echo $offer_vouchers->voucher_offer_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($offer_vouchers->voucher_status->Visible) { // voucher_status ?>
		<td<?php echo $offer_vouchers->voucher_status->CellAttributes() ?>>
<span id="el<?php echo $offer_vouchers_delete->RowCnt ?>_offer_vouchers_voucher_status" class="control-group offer_vouchers_voucher_status">
<span<?php echo $offer_vouchers->voucher_status->ViewAttributes() ?>>
<?php echo $offer_vouchers->voucher_status->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$offer_vouchers_delete->Recordset->MoveNext();
}
$offer_vouchers_delete->Recordset->Close();
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
foffer_vouchersdelete.Init();
</script>
<?php
$offer_vouchers_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$offer_vouchers_delete->Page_Terminate();
?>
