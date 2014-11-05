<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "customersinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$customers_delete = NULL; // Initialize page object first

class ccustomers_delete extends ccustomers {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'customers';

	// Page object name
	var $PageObjName = 'customers_delete';

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

		// Table object (customers)
		if (!isset($GLOBALS["customers"]) || get_class($GLOBALS["customers"]) == "ccustomers") {
			$GLOBALS["customers"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["customers"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'customers', TRUE);

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
			$this->Page_Terminate("customerslist.php");
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->customer_id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
			$this->Page_Terminate("customerslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in customers class, customersinfo.php

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
		$this->customer_id->setDbValue($rs->fields('customer_id'));
		$this->customer_code->setDbValue($rs->fields('customer_code'));
		$this->customer_email->setDbValue($rs->fields('customer_email'));
		$this->customer_pass->setDbValue($rs->fields('customer_pass'));
		$this->customer_first_name->setDbValue($rs->fields('customer_first_name'));
		$this->customer_last_name->setDbValue($rs->fields('customer_last_name'));
		$this->customer_profession->setDbValue($rs->fields('customer_profession'));
		$this->customer_phone->setDbValue($rs->fields('customer_phone'));
		$this->customer_address->setDbValue($rs->fields('customer_address'));
		$this->subscription_id->setDbValue($rs->fields('subscription_id'));
		$this->customer_facebook->setDbValue($rs->fields('customer_facebook'));
		$this->customer_author_uid->setDbValue($rs->fields('customer_author_uid'));
		$this->customer_provider->setDbValue($rs->fields('customer_provider'));
		$this->customer_payment_type->setDbValue($rs->fields('customer_payment_type'));
		$this->customer_status->setDbValue($rs->fields('customer_status'));
		$this->customer_first_login->setDbValue($rs->fields('customer_first_login'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->customer_id->DbValue = $row['customer_id'];
		$this->customer_code->DbValue = $row['customer_code'];
		$this->customer_email->DbValue = $row['customer_email'];
		$this->customer_pass->DbValue = $row['customer_pass'];
		$this->customer_first_name->DbValue = $row['customer_first_name'];
		$this->customer_last_name->DbValue = $row['customer_last_name'];
		$this->customer_profession->DbValue = $row['customer_profession'];
		$this->customer_phone->DbValue = $row['customer_phone'];
		$this->customer_address->DbValue = $row['customer_address'];
		$this->subscription_id->DbValue = $row['subscription_id'];
		$this->customer_facebook->DbValue = $row['customer_facebook'];
		$this->customer_author_uid->DbValue = $row['customer_author_uid'];
		$this->customer_provider->DbValue = $row['customer_provider'];
		$this->customer_payment_type->DbValue = $row['customer_payment_type'];
		$this->customer_status->DbValue = $row['customer_status'];
		$this->customer_first_login->DbValue = $row['customer_first_login'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// customer_id
		// customer_code
		// customer_email
		// customer_pass
		// customer_first_name
		// customer_last_name
		// customer_profession
		// customer_phone
		// customer_address
		// subscription_id
		// customer_facebook
		// customer_author_uid
		// customer_provider
		// customer_payment_type
		// customer_status
		// customer_first_login

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// customer_id
			$this->customer_id->ViewValue = $this->customer_id->CurrentValue;
			$this->customer_id->ViewCustomAttributes = "";

			// customer_code
			$this->customer_code->ViewValue = $this->customer_code->CurrentValue;
			$this->customer_code->ViewCustomAttributes = "";

			// customer_email
			$this->customer_email->ViewValue = $this->customer_email->CurrentValue;
			$this->customer_email->ViewCustomAttributes = "";

			// customer_pass
			$this->customer_pass->ViewValue = "********";
			$this->customer_pass->ViewCustomAttributes = "";

			// customer_first_name
			$this->customer_first_name->ViewValue = $this->customer_first_name->CurrentValue;
			$this->customer_first_name->ViewCustomAttributes = "";

			// customer_last_name
			$this->customer_last_name->ViewValue = $this->customer_last_name->CurrentValue;
			$this->customer_last_name->ViewCustomAttributes = "";

			// customer_profession
			$this->customer_profession->ViewValue = $this->customer_profession->CurrentValue;
			$this->customer_profession->ViewCustomAttributes = "";

			// customer_phone
			$this->customer_phone->ViewValue = $this->customer_phone->CurrentValue;
			$this->customer_phone->ViewCustomAttributes = "";

			// customer_address
			$this->customer_address->ViewValue = $this->customer_address->CurrentValue;
			$this->customer_address->ViewCustomAttributes = "";

			// subscription_id
			if (strval($this->subscription_id->CurrentValue) <> "") {
				$sFilterWrk = "`subscription_id`" . ew_SearchString("=", $this->subscription_id->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `subscription_id`, `subscription_type` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `subscriptions`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->subscription_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->subscription_id->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->subscription_id->ViewValue = $this->subscription_id->CurrentValue;
				}
			} else {
				$this->subscription_id->ViewValue = NULL;
			}
			$this->subscription_id->ViewCustomAttributes = "";

			// customer_facebook
			$this->customer_facebook->ViewValue = $this->customer_facebook->CurrentValue;
			$this->customer_facebook->ViewCustomAttributes = "";

			// customer_author_uid
			$this->customer_author_uid->ViewValue = $this->customer_author_uid->CurrentValue;
			$this->customer_author_uid->ViewCustomAttributes = "";

			// customer_provider
			$this->customer_provider->ViewValue = $this->customer_provider->CurrentValue;
			$this->customer_provider->ViewCustomAttributes = "";

			// customer_payment_type
			$this->customer_payment_type->ViewValue = $this->customer_payment_type->CurrentValue;
			$this->customer_payment_type->ViewCustomAttributes = "";

			// customer_status
			if (strval($this->customer_status->CurrentValue) <> "") {
				switch ($this->customer_status->CurrentValue) {
					case $this->customer_status->FldTagValue(1):
						$this->customer_status->ViewValue = $this->customer_status->FldTagCaption(1) <> "" ? $this->customer_status->FldTagCaption(1) : $this->customer_status->CurrentValue;
						break;
					case $this->customer_status->FldTagValue(2):
						$this->customer_status->ViewValue = $this->customer_status->FldTagCaption(2) <> "" ? $this->customer_status->FldTagCaption(2) : $this->customer_status->CurrentValue;
						break;
					case $this->customer_status->FldTagValue(3):
						$this->customer_status->ViewValue = $this->customer_status->FldTagCaption(3) <> "" ? $this->customer_status->FldTagCaption(3) : $this->customer_status->CurrentValue;
						break;
					case $this->customer_status->FldTagValue(4):
						$this->customer_status->ViewValue = $this->customer_status->FldTagCaption(4) <> "" ? $this->customer_status->FldTagCaption(4) : $this->customer_status->CurrentValue;
						break;
					default:
						$this->customer_status->ViewValue = $this->customer_status->CurrentValue;
				}
			} else {
				$this->customer_status->ViewValue = NULL;
			}
			$this->customer_status->ViewCustomAttributes = "";

			// customer_first_login
			if (strval($this->customer_first_login->CurrentValue) <> "") {
				switch ($this->customer_first_login->CurrentValue) {
					case $this->customer_first_login->FldTagValue(1):
						$this->customer_first_login->ViewValue = $this->customer_first_login->FldTagCaption(1) <> "" ? $this->customer_first_login->FldTagCaption(1) : $this->customer_first_login->CurrentValue;
						break;
					case $this->customer_first_login->FldTagValue(2):
						$this->customer_first_login->ViewValue = $this->customer_first_login->FldTagCaption(2) <> "" ? $this->customer_first_login->FldTagCaption(2) : $this->customer_first_login->CurrentValue;
						break;
					default:
						$this->customer_first_login->ViewValue = $this->customer_first_login->CurrentValue;
				}
			} else {
				$this->customer_first_login->ViewValue = NULL;
			}
			$this->customer_first_login->ViewCustomAttributes = "";

			// customer_id
			$this->customer_id->LinkCustomAttributes = "";
			$this->customer_id->HrefValue = "";
			$this->customer_id->TooltipValue = "";

			// customer_code
			$this->customer_code->LinkCustomAttributes = "";
			$this->customer_code->HrefValue = "";
			$this->customer_code->TooltipValue = "";

			// customer_email
			$this->customer_email->LinkCustomAttributes = "";
			$this->customer_email->HrefValue = "";
			$this->customer_email->TooltipValue = "";

			// customer_pass
			$this->customer_pass->LinkCustomAttributes = "";
			$this->customer_pass->HrefValue = "";
			$this->customer_pass->TooltipValue = "";

			// customer_first_name
			$this->customer_first_name->LinkCustomAttributes = "";
			$this->customer_first_name->HrefValue = "";
			$this->customer_first_name->TooltipValue = "";

			// customer_last_name
			$this->customer_last_name->LinkCustomAttributes = "";
			$this->customer_last_name->HrefValue = "";
			$this->customer_last_name->TooltipValue = "";

			// customer_profession
			$this->customer_profession->LinkCustomAttributes = "";
			$this->customer_profession->HrefValue = "";
			$this->customer_profession->TooltipValue = "";

			// customer_phone
			$this->customer_phone->LinkCustomAttributes = "";
			$this->customer_phone->HrefValue = "";
			$this->customer_phone->TooltipValue = "";

			// customer_address
			$this->customer_address->LinkCustomAttributes = "";
			$this->customer_address->HrefValue = "";
			$this->customer_address->TooltipValue = "";

			// subscription_id
			$this->subscription_id->LinkCustomAttributes = "";
			$this->subscription_id->HrefValue = "";
			$this->subscription_id->TooltipValue = "";

			// customer_facebook
			$this->customer_facebook->LinkCustomAttributes = "";
			$this->customer_facebook->HrefValue = "";
			$this->customer_facebook->TooltipValue = "";

			// customer_author_uid
			$this->customer_author_uid->LinkCustomAttributes = "";
			$this->customer_author_uid->HrefValue = "";
			$this->customer_author_uid->TooltipValue = "";

			// customer_provider
			$this->customer_provider->LinkCustomAttributes = "";
			$this->customer_provider->HrefValue = "";
			$this->customer_provider->TooltipValue = "";

			// customer_payment_type
			$this->customer_payment_type->LinkCustomAttributes = "";
			$this->customer_payment_type->HrefValue = "";
			$this->customer_payment_type->TooltipValue = "";

			// customer_status
			$this->customer_status->LinkCustomAttributes = "";
			$this->customer_status->HrefValue = "";
			$this->customer_status->TooltipValue = "";

			// customer_first_login
			$this->customer_first_login->LinkCustomAttributes = "";
			$this->customer_first_login->HrefValue = "";
			$this->customer_first_login->TooltipValue = "";
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
				$sThisKey .= $row['customer_id'];
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
		$Breadcrumb->Add("list", $this->TableVar, "customerslist.php", $this->TableVar, TRUE);
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
if (!isset($customers_delete)) $customers_delete = new ccustomers_delete();

// Page init
$customers_delete->Page_Init();

// Page main
$customers_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$customers_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var customers_delete = new ew_Page("customers_delete");
customers_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = customers_delete.PageID; // For backward compatibility

// Form object
var fcustomersdelete = new ew_Form("fcustomersdelete");

// Form_CustomValidate event
fcustomersdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcustomersdelete.ValidateRequired = true;
<?php } else { ?>
fcustomersdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fcustomersdelete.Lists["x_subscription_id"] = {"LinkField":"x_subscription_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_subscription_type","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($customers_delete->Recordset = $customers_delete->LoadRecordset())
	$customers_deleteTotalRecs = $customers_delete->Recordset->RecordCount(); // Get record count
if ($customers_deleteTotalRecs <= 0) { // No record found, exit
	if ($customers_delete->Recordset)
		$customers_delete->Recordset->Close();
	$customers_delete->Page_Terminate("customerslist.php"); // Return to list
}
?>
<?php $Breadcrumb->Render(); ?>
<?php $customers_delete->ShowPageHeader(); ?>
<?php
$customers_delete->ShowMessage();
?>
<form name="fcustomersdelete" id="fcustomersdelete" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="customers">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($customers_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_customersdelete" class="ewTable ewTableSeparate">
<?php echo $customers->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($customers->customer_id->Visible) { // customer_id ?>
		<td><span id="elh_customers_customer_id" class="customers_customer_id"><?php echo $customers->customer_id->FldCaption() ?></span></td>
<?php } ?>
<?php if ($customers->customer_code->Visible) { // customer_code ?>
		<td><span id="elh_customers_customer_code" class="customers_customer_code"><?php echo $customers->customer_code->FldCaption() ?></span></td>
<?php } ?>
<?php if ($customers->customer_email->Visible) { // customer_email ?>
		<td><span id="elh_customers_customer_email" class="customers_customer_email"><?php echo $customers->customer_email->FldCaption() ?></span></td>
<?php } ?>
<?php if ($customers->customer_pass->Visible) { // customer_pass ?>
		<td><span id="elh_customers_customer_pass" class="customers_customer_pass"><?php echo $customers->customer_pass->FldCaption() ?></span></td>
<?php } ?>
<?php if ($customers->customer_first_name->Visible) { // customer_first_name ?>
		<td><span id="elh_customers_customer_first_name" class="customers_customer_first_name"><?php echo $customers->customer_first_name->FldCaption() ?></span></td>
<?php } ?>
<?php if ($customers->customer_last_name->Visible) { // customer_last_name ?>
		<td><span id="elh_customers_customer_last_name" class="customers_customer_last_name"><?php echo $customers->customer_last_name->FldCaption() ?></span></td>
<?php } ?>
<?php if ($customers->customer_profession->Visible) { // customer_profession ?>
		<td><span id="elh_customers_customer_profession" class="customers_customer_profession"><?php echo $customers->customer_profession->FldCaption() ?></span></td>
<?php } ?>
<?php if ($customers->customer_phone->Visible) { // customer_phone ?>
		<td><span id="elh_customers_customer_phone" class="customers_customer_phone"><?php echo $customers->customer_phone->FldCaption() ?></span></td>
<?php } ?>
<?php if ($customers->customer_address->Visible) { // customer_address ?>
		<td><span id="elh_customers_customer_address" class="customers_customer_address"><?php echo $customers->customer_address->FldCaption() ?></span></td>
<?php } ?>
<?php if ($customers->subscription_id->Visible) { // subscription_id ?>
		<td><span id="elh_customers_subscription_id" class="customers_subscription_id"><?php echo $customers->subscription_id->FldCaption() ?></span></td>
<?php } ?>
<?php if ($customers->customer_facebook->Visible) { // customer_facebook ?>
		<td><span id="elh_customers_customer_facebook" class="customers_customer_facebook"><?php echo $customers->customer_facebook->FldCaption() ?></span></td>
<?php } ?>
<?php if ($customers->customer_author_uid->Visible) { // customer_author_uid ?>
		<td><span id="elh_customers_customer_author_uid" class="customers_customer_author_uid"><?php echo $customers->customer_author_uid->FldCaption() ?></span></td>
<?php } ?>
<?php if ($customers->customer_provider->Visible) { // customer_provider ?>
		<td><span id="elh_customers_customer_provider" class="customers_customer_provider"><?php echo $customers->customer_provider->FldCaption() ?></span></td>
<?php } ?>
<?php if ($customers->customer_payment_type->Visible) { // customer_payment_type ?>
		<td><span id="elh_customers_customer_payment_type" class="customers_customer_payment_type"><?php echo $customers->customer_payment_type->FldCaption() ?></span></td>
<?php } ?>
<?php if ($customers->customer_status->Visible) { // customer_status ?>
		<td><span id="elh_customers_customer_status" class="customers_customer_status"><?php echo $customers->customer_status->FldCaption() ?></span></td>
<?php } ?>
<?php if ($customers->customer_first_login->Visible) { // customer_first_login ?>
		<td><span id="elh_customers_customer_first_login" class="customers_customer_first_login"><?php echo $customers->customer_first_login->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$customers_delete->RecCnt = 0;
$i = 0;
while (!$customers_delete->Recordset->EOF) {
	$customers_delete->RecCnt++;
	$customers_delete->RowCnt++;

	// Set row properties
	$customers->ResetAttrs();
	$customers->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$customers_delete->LoadRowValues($customers_delete->Recordset);

	// Render row
	$customers_delete->RenderRow();
?>
	<tr<?php echo $customers->RowAttributes() ?>>
<?php if ($customers->customer_id->Visible) { // customer_id ?>
		<td<?php echo $customers->customer_id->CellAttributes() ?>>
<span id="el<?php echo $customers_delete->RowCnt ?>_customers_customer_id" class="control-group customers_customer_id">
<span<?php echo $customers->customer_id->ViewAttributes() ?>>
<?php echo $customers->customer_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($customers->customer_code->Visible) { // customer_code ?>
		<td<?php echo $customers->customer_code->CellAttributes() ?>>
<span id="el<?php echo $customers_delete->RowCnt ?>_customers_customer_code" class="control-group customers_customer_code">
<span<?php echo $customers->customer_code->ViewAttributes() ?>>
<?php echo $customers->customer_code->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($customers->customer_email->Visible) { // customer_email ?>
		<td<?php echo $customers->customer_email->CellAttributes() ?>>
<span id="el<?php echo $customers_delete->RowCnt ?>_customers_customer_email" class="control-group customers_customer_email">
<span<?php echo $customers->customer_email->ViewAttributes() ?>>
<?php echo $customers->customer_email->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($customers->customer_pass->Visible) { // customer_pass ?>
		<td<?php echo $customers->customer_pass->CellAttributes() ?>>
<span id="el<?php echo $customers_delete->RowCnt ?>_customers_customer_pass" class="control-group customers_customer_pass">
<span<?php echo $customers->customer_pass->ViewAttributes() ?>>
<?php echo $customers->customer_pass->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($customers->customer_first_name->Visible) { // customer_first_name ?>
		<td<?php echo $customers->customer_first_name->CellAttributes() ?>>
<span id="el<?php echo $customers_delete->RowCnt ?>_customers_customer_first_name" class="control-group customers_customer_first_name">
<span<?php echo $customers->customer_first_name->ViewAttributes() ?>>
<?php echo $customers->customer_first_name->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($customers->customer_last_name->Visible) { // customer_last_name ?>
		<td<?php echo $customers->customer_last_name->CellAttributes() ?>>
<span id="el<?php echo $customers_delete->RowCnt ?>_customers_customer_last_name" class="control-group customers_customer_last_name">
<span<?php echo $customers->customer_last_name->ViewAttributes() ?>>
<?php echo $customers->customer_last_name->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($customers->customer_profession->Visible) { // customer_profession ?>
		<td<?php echo $customers->customer_profession->CellAttributes() ?>>
<span id="el<?php echo $customers_delete->RowCnt ?>_customers_customer_profession" class="control-group customers_customer_profession">
<span<?php echo $customers->customer_profession->ViewAttributes() ?>>
<?php echo $customers->customer_profession->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($customers->customer_phone->Visible) { // customer_phone ?>
		<td<?php echo $customers->customer_phone->CellAttributes() ?>>
<span id="el<?php echo $customers_delete->RowCnt ?>_customers_customer_phone" class="control-group customers_customer_phone">
<span<?php echo $customers->customer_phone->ViewAttributes() ?>>
<?php echo $customers->customer_phone->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($customers->customer_address->Visible) { // customer_address ?>
		<td<?php echo $customers->customer_address->CellAttributes() ?>>
<span id="el<?php echo $customers_delete->RowCnt ?>_customers_customer_address" class="control-group customers_customer_address">
<span<?php echo $customers->customer_address->ViewAttributes() ?>>
<?php echo $customers->customer_address->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($customers->subscription_id->Visible) { // subscription_id ?>
		<td<?php echo $customers->subscription_id->CellAttributes() ?>>
<span id="el<?php echo $customers_delete->RowCnt ?>_customers_subscription_id" class="control-group customers_subscription_id">
<span<?php echo $customers->subscription_id->ViewAttributes() ?>>
<?php echo $customers->subscription_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($customers->customer_facebook->Visible) { // customer_facebook ?>
		<td<?php echo $customers->customer_facebook->CellAttributes() ?>>
<span id="el<?php echo $customers_delete->RowCnt ?>_customers_customer_facebook" class="control-group customers_customer_facebook">
<span<?php echo $customers->customer_facebook->ViewAttributes() ?>>
<?php echo $customers->customer_facebook->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($customers->customer_author_uid->Visible) { // customer_author_uid ?>
		<td<?php echo $customers->customer_author_uid->CellAttributes() ?>>
<span id="el<?php echo $customers_delete->RowCnt ?>_customers_customer_author_uid" class="control-group customers_customer_author_uid">
<span<?php echo $customers->customer_author_uid->ViewAttributes() ?>>
<?php echo $customers->customer_author_uid->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($customers->customer_provider->Visible) { // customer_provider ?>
		<td<?php echo $customers->customer_provider->CellAttributes() ?>>
<span id="el<?php echo $customers_delete->RowCnt ?>_customers_customer_provider" class="control-group customers_customer_provider">
<span<?php echo $customers->customer_provider->ViewAttributes() ?>>
<?php echo $customers->customer_provider->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($customers->customer_payment_type->Visible) { // customer_payment_type ?>
		<td<?php echo $customers->customer_payment_type->CellAttributes() ?>>
<span id="el<?php echo $customers_delete->RowCnt ?>_customers_customer_payment_type" class="control-group customers_customer_payment_type">
<span<?php echo $customers->customer_payment_type->ViewAttributes() ?>>
<?php echo $customers->customer_payment_type->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($customers->customer_status->Visible) { // customer_status ?>
		<td<?php echo $customers->customer_status->CellAttributes() ?>>
<span id="el<?php echo $customers_delete->RowCnt ?>_customers_customer_status" class="control-group customers_customer_status">
<span<?php echo $customers->customer_status->ViewAttributes() ?>>
<?php echo $customers->customer_status->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($customers->customer_first_login->Visible) { // customer_first_login ?>
		<td<?php echo $customers->customer_first_login->CellAttributes() ?>>
<span id="el<?php echo $customers_delete->RowCnt ?>_customers_customer_first_login" class="control-group customers_customer_first_login">
<span<?php echo $customers->customer_first_login->ViewAttributes() ?>>
<?php echo $customers->customer_first_login->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$customers_delete->Recordset->MoveNext();
}
$customers_delete->Recordset->Close();
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
fcustomersdelete.Init();
</script>
<?php
$customers_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$customers_delete->Page_Terminate();
?>
