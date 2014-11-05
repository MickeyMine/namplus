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

$customers_add = NULL; // Initialize page object first

class ccustomers_add extends ccustomers {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'customers';

	// Page object name
	var $PageObjName = 'customers_add';

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
			define("EW_PAGE_ID", 'add', TRUE);

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
		if (!$Security->CanAdd()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("customerslist.php");
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

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
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $Priv = 0;
	var $OldRecordset;
	var $CopyRecord;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Process form if post back
		if (@$_POST["a_add"] <> "") {
			$this->CurrentAction = $_POST["a_add"]; // Get form action
			$this->CopyRecord = $this->LoadOldRecord(); // Load old recordset
			$this->LoadFormValues(); // Load form values
		} else { // Not post back

			// Load key values from QueryString
			$this->CopyRecord = TRUE;
			if (@$_GET["customer_id"] != "") {
				$this->customer_id->setQueryStringValue($_GET["customer_id"]);
				$this->setKey("customer_id", $this->customer_id->CurrentValue); // Set up key
			} else {
				$this->setKey("customer_id", ""); // Clear key
				$this->CopyRecord = FALSE;
			}
			if ($this->CopyRecord) {
				$this->CurrentAction = "C"; // Copy record
			} else {
				$this->CurrentAction = "I"; // Display blank record
				$this->LoadDefaultValues(); // Load default values
			}
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Validate form if post back
		if (@$_POST["a_add"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues(); // Restore form values
				$this->setFailureMessage($gsFormError);
			}
		}

		// Perform action based on action code
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "C": // Copy an existing record
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("customerslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "customersview.php")
						$sReturnUrl = $this->GetViewUrl(); // View paging, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values
				}
		}

		// Render row based on row type
		$this->RowType = EW_ROWTYPE_ADD;  // Render add type

		// Render row
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm;

		// Get upload data
	}

	// Load default values
	function LoadDefaultValues() {
		$this->customer_code->CurrentValue = NULL;
		$this->customer_code->OldValue = $this->customer_code->CurrentValue;
		$this->customer_email->CurrentValue = NULL;
		$this->customer_email->OldValue = $this->customer_email->CurrentValue;
		$this->customer_pass->CurrentValue = NULL;
		$this->customer_pass->OldValue = $this->customer_pass->CurrentValue;
		$this->customer_first_name->CurrentValue = NULL;
		$this->customer_first_name->OldValue = $this->customer_first_name->CurrentValue;
		$this->customer_last_name->CurrentValue = NULL;
		$this->customer_last_name->OldValue = $this->customer_last_name->CurrentValue;
		$this->customer_profession->CurrentValue = NULL;
		$this->customer_profession->OldValue = $this->customer_profession->CurrentValue;
		$this->customer_phone->CurrentValue = NULL;
		$this->customer_phone->OldValue = $this->customer_phone->CurrentValue;
		$this->customer_address->CurrentValue = NULL;
		$this->customer_address->OldValue = $this->customer_address->CurrentValue;
		$this->subscription_id->CurrentValue = NULL;
		$this->subscription_id->OldValue = $this->subscription_id->CurrentValue;
		$this->customer_facebook->CurrentValue = NULL;
		$this->customer_facebook->OldValue = $this->customer_facebook->CurrentValue;
		$this->customer_author_uid->CurrentValue = NULL;
		$this->customer_author_uid->OldValue = $this->customer_author_uid->CurrentValue;
		$this->customer_provider->CurrentValue = NULL;
		$this->customer_provider->OldValue = $this->customer_provider->CurrentValue;
		$this->customer_payment_type->CurrentValue = NULL;
		$this->customer_payment_type->OldValue = $this->customer_payment_type->CurrentValue;
		$this->customer_status->CurrentValue = NULL;
		$this->customer_status->OldValue = $this->customer_status->CurrentValue;
		$this->customer_first_login->CurrentValue = 0;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->customer_code->FldIsDetailKey) {
			$this->customer_code->setFormValue($objForm->GetValue("x_customer_code"));
		}
		if (!$this->customer_email->FldIsDetailKey) {
			$this->customer_email->setFormValue($objForm->GetValue("x_customer_email"));
		}
		if (!$this->customer_pass->FldIsDetailKey) {
			$this->customer_pass->setFormValue($objForm->GetValue("x_customer_pass"));
		}
		if (!$this->customer_first_name->FldIsDetailKey) {
			$this->customer_first_name->setFormValue($objForm->GetValue("x_customer_first_name"));
		}
		if (!$this->customer_last_name->FldIsDetailKey) {
			$this->customer_last_name->setFormValue($objForm->GetValue("x_customer_last_name"));
		}
		if (!$this->customer_profession->FldIsDetailKey) {
			$this->customer_profession->setFormValue($objForm->GetValue("x_customer_profession"));
		}
		if (!$this->customer_phone->FldIsDetailKey) {
			$this->customer_phone->setFormValue($objForm->GetValue("x_customer_phone"));
		}
		if (!$this->customer_address->FldIsDetailKey) {
			$this->customer_address->setFormValue($objForm->GetValue("x_customer_address"));
		}
		if (!$this->subscription_id->FldIsDetailKey) {
			$this->subscription_id->setFormValue($objForm->GetValue("x_subscription_id"));
		}
		if (!$this->customer_facebook->FldIsDetailKey) {
			$this->customer_facebook->setFormValue($objForm->GetValue("x_customer_facebook"));
		}
		if (!$this->customer_author_uid->FldIsDetailKey) {
			$this->customer_author_uid->setFormValue($objForm->GetValue("x_customer_author_uid"));
		}
		if (!$this->customer_provider->FldIsDetailKey) {
			$this->customer_provider->setFormValue($objForm->GetValue("x_customer_provider"));
		}
		if (!$this->customer_payment_type->FldIsDetailKey) {
			$this->customer_payment_type->setFormValue($objForm->GetValue("x_customer_payment_type"));
		}
		if (!$this->customer_status->FldIsDetailKey) {
			$this->customer_status->setFormValue($objForm->GetValue("x_customer_status"));
		}
		if (!$this->customer_first_login->FldIsDetailKey) {
			$this->customer_first_login->setFormValue($objForm->GetValue("x_customer_first_login"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->customer_code->CurrentValue = $this->customer_code->FormValue;
		$this->customer_email->CurrentValue = $this->customer_email->FormValue;
		$this->customer_pass->CurrentValue = $this->customer_pass->FormValue;
		$this->customer_first_name->CurrentValue = $this->customer_first_name->FormValue;
		$this->customer_last_name->CurrentValue = $this->customer_last_name->FormValue;
		$this->customer_profession->CurrentValue = $this->customer_profession->FormValue;
		$this->customer_phone->CurrentValue = $this->customer_phone->FormValue;
		$this->customer_address->CurrentValue = $this->customer_address->FormValue;
		$this->subscription_id->CurrentValue = $this->subscription_id->FormValue;
		$this->customer_facebook->CurrentValue = $this->customer_facebook->FormValue;
		$this->customer_author_uid->CurrentValue = $this->customer_author_uid->FormValue;
		$this->customer_provider->CurrentValue = $this->customer_provider->FormValue;
		$this->customer_payment_type->CurrentValue = $this->customer_payment_type->FormValue;
		$this->customer_status->CurrentValue = $this->customer_status->FormValue;
		$this->customer_first_login->CurrentValue = $this->customer_first_login->FormValue;
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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("customer_id")) <> "")
			$this->customer_id->CurrentValue = $this->getKey("customer_id"); // customer_id
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$this->OldRecordset = ew_LoadRecordset($sSql);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// customer_code
			$this->customer_code->EditCustomAttributes = "";
			$this->customer_code->EditValue = ew_HtmlEncode($this->customer_code->CurrentValue);
			$this->customer_code->PlaceHolder = ew_RemoveHtml($this->customer_code->FldCaption());

			// customer_email
			$this->customer_email->EditCustomAttributes = "";
			$this->customer_email->EditValue = ew_HtmlEncode($this->customer_email->CurrentValue);
			$this->customer_email->PlaceHolder = ew_RemoveHtml($this->customer_email->FldCaption());

			// customer_pass
			$this->customer_pass->EditCustomAttributes = "";
			$this->customer_pass->EditValue = ew_HtmlEncode($this->customer_pass->CurrentValue);

			// customer_first_name
			$this->customer_first_name->EditCustomAttributes = "";
			$this->customer_first_name->EditValue = ew_HtmlEncode($this->customer_first_name->CurrentValue);
			$this->customer_first_name->PlaceHolder = ew_RemoveHtml($this->customer_first_name->FldCaption());

			// customer_last_name
			$this->customer_last_name->EditCustomAttributes = "";
			$this->customer_last_name->EditValue = ew_HtmlEncode($this->customer_last_name->CurrentValue);
			$this->customer_last_name->PlaceHolder = ew_RemoveHtml($this->customer_last_name->FldCaption());

			// customer_profession
			$this->customer_profession->EditCustomAttributes = "";
			$this->customer_profession->EditValue = ew_HtmlEncode($this->customer_profession->CurrentValue);
			$this->customer_profession->PlaceHolder = ew_RemoveHtml($this->customer_profession->FldCaption());

			// customer_phone
			$this->customer_phone->EditCustomAttributes = "";
			$this->customer_phone->EditValue = ew_HtmlEncode($this->customer_phone->CurrentValue);
			$this->customer_phone->PlaceHolder = ew_RemoveHtml($this->customer_phone->FldCaption());

			// customer_address
			$this->customer_address->EditCustomAttributes = "";
			$this->customer_address->EditValue = ew_HtmlEncode($this->customer_address->CurrentValue);
			$this->customer_address->PlaceHolder = ew_RemoveHtml($this->customer_address->FldCaption());

			// subscription_id
			$this->subscription_id->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `subscription_id`, `subscription_type` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `subscriptions`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->subscription_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->subscription_id->EditValue = $arwrk;

			// customer_facebook
			$this->customer_facebook->EditCustomAttributes = "";
			$this->customer_facebook->EditValue = ew_HtmlEncode($this->customer_facebook->CurrentValue);
			$this->customer_facebook->PlaceHolder = ew_RemoveHtml($this->customer_facebook->FldCaption());

			// customer_author_uid
			$this->customer_author_uid->EditCustomAttributes = "";
			$this->customer_author_uid->EditValue = ew_HtmlEncode($this->customer_author_uid->CurrentValue);
			$this->customer_author_uid->PlaceHolder = ew_RemoveHtml($this->customer_author_uid->FldCaption());

			// customer_provider
			$this->customer_provider->EditCustomAttributes = "";
			$this->customer_provider->EditValue = ew_HtmlEncode($this->customer_provider->CurrentValue);
			$this->customer_provider->PlaceHolder = ew_RemoveHtml($this->customer_provider->FldCaption());

			// customer_payment_type
			$this->customer_payment_type->EditCustomAttributes = "";
			$this->customer_payment_type->EditValue = ew_HtmlEncode($this->customer_payment_type->CurrentValue);
			$this->customer_payment_type->PlaceHolder = ew_RemoveHtml($this->customer_payment_type->FldCaption());

			// customer_status
			$this->customer_status->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->customer_status->FldTagValue(1), $this->customer_status->FldTagCaption(1) <> "" ? $this->customer_status->FldTagCaption(1) : $this->customer_status->FldTagValue(1));
			$arwrk[] = array($this->customer_status->FldTagValue(2), $this->customer_status->FldTagCaption(2) <> "" ? $this->customer_status->FldTagCaption(2) : $this->customer_status->FldTagValue(2));
			$arwrk[] = array($this->customer_status->FldTagValue(3), $this->customer_status->FldTagCaption(3) <> "" ? $this->customer_status->FldTagCaption(3) : $this->customer_status->FldTagValue(3));
			$arwrk[] = array($this->customer_status->FldTagValue(4), $this->customer_status->FldTagCaption(4) <> "" ? $this->customer_status->FldTagCaption(4) : $this->customer_status->FldTagValue(4));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->customer_status->EditValue = $arwrk;

			// customer_first_login
			$this->customer_first_login->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->customer_first_login->FldTagValue(1), $this->customer_first_login->FldTagCaption(1) <> "" ? $this->customer_first_login->FldTagCaption(1) : $this->customer_first_login->FldTagValue(1));
			$arwrk[] = array($this->customer_first_login->FldTagValue(2), $this->customer_first_login->FldTagCaption(2) <> "" ? $this->customer_first_login->FldTagCaption(2) : $this->customer_first_login->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->customer_first_login->EditValue = $arwrk;

			// Edit refer script
			// customer_code

			$this->customer_code->HrefValue = "";

			// customer_email
			$this->customer_email->HrefValue = "";

			// customer_pass
			$this->customer_pass->HrefValue = "";

			// customer_first_name
			$this->customer_first_name->HrefValue = "";

			// customer_last_name
			$this->customer_last_name->HrefValue = "";

			// customer_profession
			$this->customer_profession->HrefValue = "";

			// customer_phone
			$this->customer_phone->HrefValue = "";

			// customer_address
			$this->customer_address->HrefValue = "";

			// subscription_id
			$this->subscription_id->HrefValue = "";

			// customer_facebook
			$this->customer_facebook->HrefValue = "";

			// customer_author_uid
			$this->customer_author_uid->HrefValue = "";

			// customer_provider
			$this->customer_provider->HrefValue = "";

			// customer_payment_type
			$this->customer_payment_type->HrefValue = "";

			// customer_status
			$this->customer_status->HrefValue = "";

			// customer_first_login
			$this->customer_first_login->HrefValue = "";
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->customer_email->FldIsDetailKey && !is_null($this->customer_email->FormValue) && $this->customer_email->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->customer_email->FldCaption());
		}
		if (!$this->customer_pass->FldIsDetailKey && !is_null($this->customer_pass->FormValue) && $this->customer_pass->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->customer_pass->FldCaption());
		}
		if (!$this->customer_first_name->FldIsDetailKey && !is_null($this->customer_first_name->FormValue) && $this->customer_first_name->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->customer_first_name->FldCaption());
		}
		if (!$this->customer_last_name->FldIsDetailKey && !is_null($this->customer_last_name->FormValue) && $this->customer_last_name->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->customer_last_name->FldCaption());
		}
		if (!$this->customer_phone->FldIsDetailKey && !is_null($this->customer_phone->FormValue) && $this->customer_phone->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->customer_phone->FldCaption());
		}
		if (!$this->customer_address->FldIsDetailKey && !is_null($this->customer_address->FormValue) && $this->customer_address->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->customer_address->FldCaption());
		}
		if (!$this->customer_facebook->FldIsDetailKey && !is_null($this->customer_facebook->FormValue) && $this->customer_facebook->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->customer_facebook->FldCaption());
		}
		if (!$this->customer_author_uid->FldIsDetailKey && !is_null($this->customer_author_uid->FormValue) && $this->customer_author_uid->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->customer_author_uid->FldCaption());
		}
		if (!$this->customer_provider->FldIsDetailKey && !is_null($this->customer_provider->FormValue) && $this->customer_provider->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->customer_provider->FldCaption());
		}
		if (!$this->customer_payment_type->FldIsDetailKey && !is_null($this->customer_payment_type->FormValue) && $this->customer_payment_type->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->customer_payment_type->FldCaption());
		}
		if (!ew_CheckInteger($this->customer_payment_type->FormValue)) {
			ew_AddMessage($gsFormError, $this->customer_payment_type->FldErrMsg());
		}
		if (!$this->customer_status->FldIsDetailKey && !is_null($this->customer_status->FormValue) && $this->customer_status->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->customer_status->FldCaption());
		}
		if (!$this->customer_first_login->FldIsDetailKey && !is_null($this->customer_first_login->FormValue) && $this->customer_first_login->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->customer_first_login->FldCaption());
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Add record
	function AddRow($rsold = NULL) {
		global $conn, $Language, $Security;

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// customer_code
		$this->customer_code->SetDbValueDef($rsnew, $this->customer_code->CurrentValue, NULL, FALSE);

		// customer_email
		$this->customer_email->SetDbValueDef($rsnew, $this->customer_email->CurrentValue, "", FALSE);

		// customer_pass
		$this->customer_pass->SetDbValueDef($rsnew, $this->customer_pass->CurrentValue, "", FALSE);

		// customer_first_name
		$this->customer_first_name->SetDbValueDef($rsnew, $this->customer_first_name->CurrentValue, "", FALSE);

		// customer_last_name
		$this->customer_last_name->SetDbValueDef($rsnew, $this->customer_last_name->CurrentValue, "", FALSE);

		// customer_profession
		$this->customer_profession->SetDbValueDef($rsnew, $this->customer_profession->CurrentValue, NULL, FALSE);

		// customer_phone
		$this->customer_phone->SetDbValueDef($rsnew, $this->customer_phone->CurrentValue, "", FALSE);

		// customer_address
		$this->customer_address->SetDbValueDef($rsnew, $this->customer_address->CurrentValue, "", FALSE);

		// subscription_id
		$this->subscription_id->SetDbValueDef($rsnew, $this->subscription_id->CurrentValue, NULL, FALSE);

		// customer_facebook
		$this->customer_facebook->SetDbValueDef($rsnew, $this->customer_facebook->CurrentValue, "", FALSE);

		// customer_author_uid
		$this->customer_author_uid->SetDbValueDef($rsnew, $this->customer_author_uid->CurrentValue, "", FALSE);

		// customer_provider
		$this->customer_provider->SetDbValueDef($rsnew, $this->customer_provider->CurrentValue, "", FALSE);

		// customer_payment_type
		$this->customer_payment_type->SetDbValueDef($rsnew, $this->customer_payment_type->CurrentValue, 0, FALSE);

		// customer_status
		$this->customer_status->SetDbValueDef($rsnew, $this->customer_status->CurrentValue, 0, FALSE);

		// customer_first_login
		$this->customer_first_login->SetDbValueDef($rsnew, $this->customer_first_login->CurrentValue, 0, strval($this->customer_first_login->CurrentValue) == "");

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
			}
		} else {
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}

		// Get insert id if necessary
		if ($AddRow) {
			$this->customer_id->setDbValue($conn->Insert_ID());
			$rsnew['customer_id'] = $this->customer_id->DbValue;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
		}
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "customerslist.php", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, ew_CurrentUrl());
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

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($customers_add)) $customers_add = new ccustomers_add();

// Page init
$customers_add->Page_Init();

// Page main
$customers_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$customers_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var customers_add = new ew_Page("customers_add");
customers_add.PageID = "add"; // Page ID
var EW_PAGE_ID = customers_add.PageID; // For backward compatibility

// Form object
var fcustomersadd = new ew_Form("fcustomersadd");

// Validate form
fcustomersadd.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	this.PostAutoSuggest();
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
			elm = this.GetElements("x" + infix + "_customer_email");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($customers->customer_email->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_customer_pass");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($customers->customer_pass->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_customer_first_name");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($customers->customer_first_name->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_customer_last_name");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($customers->customer_last_name->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_customer_phone");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($customers->customer_phone->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_customer_address");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($customers->customer_address->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_customer_facebook");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($customers->customer_facebook->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_customer_author_uid");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($customers->customer_author_uid->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_customer_provider");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($customers->customer_provider->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_customer_payment_type");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($customers->customer_payment_type->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_customer_payment_type");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($customers->customer_payment_type->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_customer_status");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($customers->customer_status->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_customer_first_login");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($customers->customer_first_login->FldCaption()) ?>");

			// Set up row object
			ew_ElementsToRow(fobj);

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
fcustomersadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcustomersadd.ValidateRequired = true;
<?php } else { ?>
fcustomersadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fcustomersadd.Lists["x_subscription_id"] = {"LinkField":"x_subscription_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_subscription_type","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $customers_add->ShowPageHeader(); ?>
<?php
$customers_add->ShowMessage();
?>
<form name="fcustomersadd" id="fcustomersadd" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="customers">
<input type="hidden" name="a_add" id="a_add" value="A">
<table class="ewGrid"><tr><td>
<table id="tbl_customersadd" class="table table-bordered table-striped">
<?php if ($customers->customer_code->Visible) { // customer_code ?>
	<tr id="r_customer_code">
		<td><span id="elh_customers_customer_code"><?php echo $customers->customer_code->FldCaption() ?></span></td>
		<td<?php echo $customers->customer_code->CellAttributes() ?>>
<span id="el_customers_customer_code" class="control-group">
<input type="text" data-field="x_customer_code" name="x_customer_code" id="x_customer_code" size="30" maxlength="21" placeholder="<?php echo ew_HtmlEncode($customers->customer_code->PlaceHolder) ?>" value="<?php echo $customers->customer_code->EditValue ?>"<?php echo $customers->customer_code->EditAttributes() ?>>
</span>
<?php echo $customers->customer_code->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($customers->customer_email->Visible) { // customer_email ?>
	<tr id="r_customer_email">
		<td><span id="elh_customers_customer_email"><?php echo $customers->customer_email->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $customers->customer_email->CellAttributes() ?>>
<span id="el_customers_customer_email" class="control-group">
<input type="text" data-field="x_customer_email" name="x_customer_email" id="x_customer_email" size="30" maxlength="150" placeholder="<?php echo ew_HtmlEncode($customers->customer_email->PlaceHolder) ?>" value="<?php echo $customers->customer_email->EditValue ?>"<?php echo $customers->customer_email->EditAttributes() ?>>
</span>
<?php echo $customers->customer_email->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($customers->customer_pass->Visible) { // customer_pass ?>
	<tr id="r_customer_pass">
		<td><span id="elh_customers_customer_pass"><?php echo $customers->customer_pass->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $customers->customer_pass->CellAttributes() ?>>
<span id="el_customers_customer_pass" class="control-group">
<input type="password" data-field="x_customer_pass" name="x_customer_pass" id="x_customer_pass" size="30" maxlength="50"<?php echo $customers->customer_pass->EditAttributes() ?>>
</span>
<?php echo $customers->customer_pass->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($customers->customer_first_name->Visible) { // customer_first_name ?>
	<tr id="r_customer_first_name">
		<td><span id="elh_customers_customer_first_name"><?php echo $customers->customer_first_name->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $customers->customer_first_name->CellAttributes() ?>>
<span id="el_customers_customer_first_name" class="control-group">
<input type="text" data-field="x_customer_first_name" name="x_customer_first_name" id="x_customer_first_name" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($customers->customer_first_name->PlaceHolder) ?>" value="<?php echo $customers->customer_first_name->EditValue ?>"<?php echo $customers->customer_first_name->EditAttributes() ?>>
</span>
<?php echo $customers->customer_first_name->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($customers->customer_last_name->Visible) { // customer_last_name ?>
	<tr id="r_customer_last_name">
		<td><span id="elh_customers_customer_last_name"><?php echo $customers->customer_last_name->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $customers->customer_last_name->CellAttributes() ?>>
<span id="el_customers_customer_last_name" class="control-group">
<input type="text" data-field="x_customer_last_name" name="x_customer_last_name" id="x_customer_last_name" size="30" maxlength="150" placeholder="<?php echo ew_HtmlEncode($customers->customer_last_name->PlaceHolder) ?>" value="<?php echo $customers->customer_last_name->EditValue ?>"<?php echo $customers->customer_last_name->EditAttributes() ?>>
</span>
<?php echo $customers->customer_last_name->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($customers->customer_profession->Visible) { // customer_profession ?>
	<tr id="r_customer_profession">
		<td><span id="elh_customers_customer_profession"><?php echo $customers->customer_profession->FldCaption() ?></span></td>
		<td<?php echo $customers->customer_profession->CellAttributes() ?>>
<span id="el_customers_customer_profession" class="control-group">
<input type="text" data-field="x_customer_profession" name="x_customer_profession" id="x_customer_profession" size="30" maxlength="150" placeholder="<?php echo ew_HtmlEncode($customers->customer_profession->PlaceHolder) ?>" value="<?php echo $customers->customer_profession->EditValue ?>"<?php echo $customers->customer_profession->EditAttributes() ?>>
</span>
<?php echo $customers->customer_profession->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($customers->customer_phone->Visible) { // customer_phone ?>
	<tr id="r_customer_phone">
		<td><span id="elh_customers_customer_phone"><?php echo $customers->customer_phone->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $customers->customer_phone->CellAttributes() ?>>
<span id="el_customers_customer_phone" class="control-group">
<input type="text" data-field="x_customer_phone" name="x_customer_phone" id="x_customer_phone" size="30" maxlength="12" placeholder="<?php echo ew_HtmlEncode($customers->customer_phone->PlaceHolder) ?>" value="<?php echo $customers->customer_phone->EditValue ?>"<?php echo $customers->customer_phone->EditAttributes() ?>>
</span>
<?php echo $customers->customer_phone->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($customers->customer_address->Visible) { // customer_address ?>
	<tr id="r_customer_address">
		<td><span id="elh_customers_customer_address"><?php echo $customers->customer_address->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $customers->customer_address->CellAttributes() ?>>
<span id="el_customers_customer_address" class="control-group">
<input type="text" data-field="x_customer_address" name="x_customer_address" id="x_customer_address" size="30" maxlength="250" placeholder="<?php echo ew_HtmlEncode($customers->customer_address->PlaceHolder) ?>" value="<?php echo $customers->customer_address->EditValue ?>"<?php echo $customers->customer_address->EditAttributes() ?>>
</span>
<?php echo $customers->customer_address->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($customers->subscription_id->Visible) { // subscription_id ?>
	<tr id="r_subscription_id">
		<td><span id="elh_customers_subscription_id"><?php echo $customers->subscription_id->FldCaption() ?></span></td>
		<td<?php echo $customers->subscription_id->CellAttributes() ?>>
<span id="el_customers_subscription_id" class="control-group">
<select data-field="x_subscription_id" id="x_subscription_id" name="x_subscription_id"<?php echo $customers->subscription_id->EditAttributes() ?>>
<?php
if (is_array($customers->subscription_id->EditValue)) {
	$arwrk = $customers->subscription_id->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($customers->subscription_id->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<script type="text/javascript">
fcustomersadd.Lists["x_subscription_id"].Options = <?php echo (is_array($customers->subscription_id->EditValue)) ? ew_ArrayToJson($customers->subscription_id->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $customers->subscription_id->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($customers->customer_facebook->Visible) { // customer_facebook ?>
	<tr id="r_customer_facebook">
		<td><span id="elh_customers_customer_facebook"><?php echo $customers->customer_facebook->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $customers->customer_facebook->CellAttributes() ?>>
<span id="el_customers_customer_facebook" class="control-group">
<input type="text" data-field="x_customer_facebook" name="x_customer_facebook" id="x_customer_facebook" size="30" maxlength="250" placeholder="<?php echo ew_HtmlEncode($customers->customer_facebook->PlaceHolder) ?>" value="<?php echo $customers->customer_facebook->EditValue ?>"<?php echo $customers->customer_facebook->EditAttributes() ?>>
</span>
<?php echo $customers->customer_facebook->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($customers->customer_author_uid->Visible) { // customer_author_uid ?>
	<tr id="r_customer_author_uid">
		<td><span id="elh_customers_customer_author_uid"><?php echo $customers->customer_author_uid->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $customers->customer_author_uid->CellAttributes() ?>>
<span id="el_customers_customer_author_uid" class="control-group">
<input type="text" data-field="x_customer_author_uid" name="x_customer_author_uid" id="x_customer_author_uid" size="30" maxlength="250" placeholder="<?php echo ew_HtmlEncode($customers->customer_author_uid->PlaceHolder) ?>" value="<?php echo $customers->customer_author_uid->EditValue ?>"<?php echo $customers->customer_author_uid->EditAttributes() ?>>
</span>
<?php echo $customers->customer_author_uid->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($customers->customer_provider->Visible) { // customer_provider ?>
	<tr id="r_customer_provider">
		<td><span id="elh_customers_customer_provider"><?php echo $customers->customer_provider->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $customers->customer_provider->CellAttributes() ?>>
<span id="el_customers_customer_provider" class="control-group">
<input type="text" data-field="x_customer_provider" name="x_customer_provider" id="x_customer_provider" size="30" maxlength="250" placeholder="<?php echo ew_HtmlEncode($customers->customer_provider->PlaceHolder) ?>" value="<?php echo $customers->customer_provider->EditValue ?>"<?php echo $customers->customer_provider->EditAttributes() ?>>
</span>
<?php echo $customers->customer_provider->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($customers->customer_payment_type->Visible) { // customer_payment_type ?>
	<tr id="r_customer_payment_type">
		<td><span id="elh_customers_customer_payment_type"><?php echo $customers->customer_payment_type->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $customers->customer_payment_type->CellAttributes() ?>>
<span id="el_customers_customer_payment_type" class="control-group">
<input type="text" data-field="x_customer_payment_type" name="x_customer_payment_type" id="x_customer_payment_type" size="30" placeholder="<?php echo ew_HtmlEncode($customers->customer_payment_type->PlaceHolder) ?>" value="<?php echo $customers->customer_payment_type->EditValue ?>"<?php echo $customers->customer_payment_type->EditAttributes() ?>>
</span>
<?php echo $customers->customer_payment_type->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($customers->customer_status->Visible) { // customer_status ?>
	<tr id="r_customer_status">
		<td><span id="elh_customers_customer_status"><?php echo $customers->customer_status->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $customers->customer_status->CellAttributes() ?>>
<span id="el_customers_customer_status" class="control-group">
<select data-field="x_customer_status" id="x_customer_status" name="x_customer_status"<?php echo $customers->customer_status->EditAttributes() ?>>
<?php
if (is_array($customers->customer_status->EditValue)) {
	$arwrk = $customers->customer_status->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($customers->customer_status->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
</span>
<?php echo $customers->customer_status->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($customers->customer_first_login->Visible) { // customer_first_login ?>
	<tr id="r_customer_first_login">
		<td><span id="elh_customers_customer_first_login"><?php echo $customers->customer_first_login->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $customers->customer_first_login->CellAttributes() ?>>
<span id="el_customers_customer_first_login" class="control-group">
<select data-field="x_customer_first_login" id="x_customer_first_login" name="x_customer_first_login"<?php echo $customers->customer_first_login->EditAttributes() ?>>
<?php
if (is_array($customers->customer_first_login->EditValue)) {
	$arwrk = $customers->customer_first_login->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($customers->customer_first_login->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
</span>
<?php echo $customers->customer_first_login->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
</form>
<script type="text/javascript">
fcustomersadd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$customers_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$customers_add->Page_Terminate();
?>
