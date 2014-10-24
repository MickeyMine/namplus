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

$offer_locations_add = NULL; // Initialize page object first

class coffer_locations_add extends coffer_locations {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'offer_locations';

	// Page object name
	var $PageObjName = 'offer_locations_add';

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
			define("EW_PAGE_ID", 'add', TRUE);

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
		if (!$Security->CanAdd()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("offer_locationslist.php");
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
			if (@$_GET["location_id"] != "") {
				$this->location_id->setQueryStringValue($_GET["location_id"]);
				$this->setKey("location_id", $this->location_id->CurrentValue); // Set up key
			} else {
				$this->setKey("location_id", ""); // Clear key
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
					$this->Page_Terminate("offer_locationslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "offer_locationsview.php")
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
		$this->offer_id->CurrentValue = NULL;
		$this->offer_id->OldValue = $this->offer_id->CurrentValue;
		$this->location_name->CurrentValue = NULL;
		$this->location_name->OldValue = $this->location_name->CurrentValue;
		$this->location_address->CurrentValue = NULL;
		$this->location_address->OldValue = $this->location_address->CurrentValue;
		$this->location_map_x->CurrentValue = NULL;
		$this->location_map_x->OldValue = $this->location_map_x->CurrentValue;
		$this->location_map_y->CurrentValue = NULL;
		$this->location_map_y->OldValue = $this->location_map_y->CurrentValue;
		$this->location_status->CurrentValue = NULL;
		$this->location_status->OldValue = $this->location_status->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->offer_id->FldIsDetailKey) {
			$this->offer_id->setFormValue($objForm->GetValue("x_offer_id"));
		}
		if (!$this->location_name->FldIsDetailKey) {
			$this->location_name->setFormValue($objForm->GetValue("x_location_name"));
		}
		if (!$this->location_address->FldIsDetailKey) {
			$this->location_address->setFormValue($objForm->GetValue("x_location_address"));
		}
		if (!$this->location_map_x->FldIsDetailKey) {
			$this->location_map_x->setFormValue($objForm->GetValue("x_location_map_x"));
		}
		if (!$this->location_map_y->FldIsDetailKey) {
			$this->location_map_y->setFormValue($objForm->GetValue("x_location_map_y"));
		}
		if (!$this->location_status->FldIsDetailKey) {
			$this->location_status->setFormValue($objForm->GetValue("x_location_status"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->offer_id->CurrentValue = $this->offer_id->FormValue;
		$this->location_name->CurrentValue = $this->location_name->FormValue;
		$this->location_address->CurrentValue = $this->location_address->FormValue;
		$this->location_map_x->CurrentValue = $this->location_map_x->FormValue;
		$this->location_map_y->CurrentValue = $this->location_map_y->FormValue;
		$this->location_status->CurrentValue = $this->location_status->FormValue;
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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("location_id")) <> "")
			$this->location_id->CurrentValue = $this->getKey("location_id"); // location_id
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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// offer_id
			$this->offer_id->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `offer_id`, `offer_title` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `offers`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->offer_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->offer_id->EditValue = $arwrk;

			// location_name
			$this->location_name->EditCustomAttributes = "";
			$this->location_name->EditValue = ew_HtmlEncode($this->location_name->CurrentValue);
			$this->location_name->PlaceHolder = ew_RemoveHtml($this->location_name->FldCaption());

			// location_address
			$this->location_address->EditCustomAttributes = "";
			$this->location_address->EditValue = ew_HtmlEncode($this->location_address->CurrentValue);
			$this->location_address->PlaceHolder = ew_RemoveHtml($this->location_address->FldCaption());

			// location_map_x
			$this->location_map_x->EditCustomAttributes = "";
			$this->location_map_x->EditValue = ew_HtmlEncode($this->location_map_x->CurrentValue);
			$this->location_map_x->PlaceHolder = ew_RemoveHtml($this->location_map_x->FldCaption());

			// location_map_y
			$this->location_map_y->EditCustomAttributes = "";
			$this->location_map_y->EditValue = ew_HtmlEncode($this->location_map_y->CurrentValue);
			$this->location_map_y->PlaceHolder = ew_RemoveHtml($this->location_map_y->FldCaption());

			// location_status
			$this->location_status->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->location_status->FldTagValue(1), $this->location_status->FldTagCaption(1) <> "" ? $this->location_status->FldTagCaption(1) : $this->location_status->FldTagValue(1));
			$arwrk[] = array($this->location_status->FldTagValue(2), $this->location_status->FldTagCaption(2) <> "" ? $this->location_status->FldTagCaption(2) : $this->location_status->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->location_status->EditValue = $arwrk;

			// Edit refer script
			// offer_id

			$this->offer_id->HrefValue = "";

			// location_name
			$this->location_name->HrefValue = "";

			// location_address
			$this->location_address->HrefValue = "";

			// location_map_x
			$this->location_map_x->HrefValue = "";

			// location_map_y
			$this->location_map_y->HrefValue = "";

			// location_status
			$this->location_status->HrefValue = "";
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
		if (!$this->offer_id->FldIsDetailKey && !is_null($this->offer_id->FormValue) && $this->offer_id->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->offer_id->FldCaption());
		}
		if (!$this->location_name->FldIsDetailKey && !is_null($this->location_name->FormValue) && $this->location_name->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->location_name->FldCaption());
		}
		if (!$this->location_address->FldIsDetailKey && !is_null($this->location_address->FormValue) && $this->location_address->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->location_address->FldCaption());
		}
		if (!$this->location_map_x->FldIsDetailKey && !is_null($this->location_map_x->FormValue) && $this->location_map_x->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->location_map_x->FldCaption());
		}
		if (!$this->location_map_y->FldIsDetailKey && !is_null($this->location_map_y->FormValue) && $this->location_map_y->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->location_map_y->FldCaption());
		}
		if (!$this->location_status->FldIsDetailKey && !is_null($this->location_status->FormValue) && $this->location_status->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->location_status->FldCaption());
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

		// offer_id
		$this->offer_id->SetDbValueDef($rsnew, $this->offer_id->CurrentValue, 0, FALSE);

		// location_name
		$this->location_name->SetDbValueDef($rsnew, $this->location_name->CurrentValue, "", FALSE);

		// location_address
		$this->location_address->SetDbValueDef($rsnew, $this->location_address->CurrentValue, "", FALSE);

		// location_map_x
		$this->location_map_x->SetDbValueDef($rsnew, $this->location_map_x->CurrentValue, "", FALSE);

		// location_map_y
		$this->location_map_y->SetDbValueDef($rsnew, $this->location_map_y->CurrentValue, "", FALSE);

		// location_status
		$this->location_status->SetDbValueDef($rsnew, $this->location_status->CurrentValue, 0, FALSE);

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
			$this->location_id->setDbValue($conn->Insert_ID());
			$rsnew['location_id'] = $this->location_id->DbValue;
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
		$Breadcrumb->Add("list", $this->TableVar, "offer_locationslist.php", $this->TableVar, TRUE);
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
if (!isset($offer_locations_add)) $offer_locations_add = new coffer_locations_add();

// Page init
$offer_locations_add->Page_Init();

// Page main
$offer_locations_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$offer_locations_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var offer_locations_add = new ew_Page("offer_locations_add");
offer_locations_add.PageID = "add"; // Page ID
var EW_PAGE_ID = offer_locations_add.PageID; // For backward compatibility

// Form object
var foffer_locationsadd = new ew_Form("foffer_locationsadd");

// Validate form
foffer_locationsadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_offer_id");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offer_locations->offer_id->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_location_name");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offer_locations->location_name->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_location_address");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offer_locations->location_address->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_location_map_x");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offer_locations->location_map_x->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_location_map_y");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offer_locations->location_map_y->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_location_status");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offer_locations->location_status->FldCaption()) ?>");

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
foffer_locationsadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
foffer_locationsadd.ValidateRequired = true;
<?php } else { ?>
foffer_locationsadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
foffer_locationsadd.Lists["x_offer_id"] = {"LinkField":"x_offer_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_offer_title","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $offer_locations_add->ShowPageHeader(); ?>
<?php
$offer_locations_add->ShowMessage();
?>
<form name="foffer_locationsadd" id="foffer_locationsadd" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="offer_locations">
<input type="hidden" name="a_add" id="a_add" value="A">
<table class="ewGrid"><tr><td>
<table id="tbl_offer_locationsadd" class="table table-bordered table-striped">
<?php if ($offer_locations->offer_id->Visible) { // offer_id ?>
	<tr id="r_offer_id">
		<td><span id="elh_offer_locations_offer_id"><?php echo $offer_locations->offer_id->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offer_locations->offer_id->CellAttributes() ?>>
<span id="el_offer_locations_offer_id" class="control-group">
<select data-field="x_offer_id" id="x_offer_id" name="x_offer_id"<?php echo $offer_locations->offer_id->EditAttributes() ?>>
<?php
if (is_array($offer_locations->offer_id->EditValue)) {
	$arwrk = $offer_locations->offer_id->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($offer_locations->offer_id->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
foffer_locationsadd.Lists["x_offer_id"].Options = <?php echo (is_array($offer_locations->offer_id->EditValue)) ? ew_ArrayToJson($offer_locations->offer_id->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $offer_locations->offer_id->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offer_locations->location_name->Visible) { // location_name ?>
	<tr id="r_location_name">
		<td><span id="elh_offer_locations_location_name"><?php echo $offer_locations->location_name->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offer_locations->location_name->CellAttributes() ?>>
<span id="el_offer_locations_location_name" class="control-group">
<input type="text" data-field="x_location_name" name="x_location_name" id="x_location_name" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($offer_locations->location_name->PlaceHolder) ?>" value="<?php echo $offer_locations->location_name->EditValue ?>"<?php echo $offer_locations->location_name->EditAttributes() ?>>
</span>
<?php echo $offer_locations->location_name->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offer_locations->location_address->Visible) { // location_address ?>
	<tr id="r_location_address">
		<td><span id="elh_offer_locations_location_address"><?php echo $offer_locations->location_address->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offer_locations->location_address->CellAttributes() ?>>
<span id="el_offer_locations_location_address" class="control-group">
<input type="text" data-field="x_location_address" name="x_location_address" id="x_location_address" size="30" maxlength="200" placeholder="<?php echo ew_HtmlEncode($offer_locations->location_address->PlaceHolder) ?>" value="<?php echo $offer_locations->location_address->EditValue ?>"<?php echo $offer_locations->location_address->EditAttributes() ?>>
</span>
<?php echo $offer_locations->location_address->CustomMsg ?></td>
	</tr>
<?php } ?>
	
	<!-- my code -->
	<tr>
		<td>&nbsp;
			
		</td>
		<td>
			<div id="map" style="width: 630px; height: 410px; overflow: hidden;">
            </div>
		</td>
	</tr>
	<!-- End my code -->

<?php if ($offer_locations->location_map_x->Visible) { // location_map_x ?>
	<tr id="r_location_map_x">
		<td><span id="elh_offer_locations_location_map_x"><?php echo $offer_locations->location_map_x->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offer_locations->location_map_x->CellAttributes() ?>>
<span id="el_offer_locations_location_map_x" class="control-group">
<input type="text" data-field="x_location_map_x" name="x_location_map_x" id="x_location_map_x" size="30" maxlength="14" placeholder="<?php echo ew_HtmlEncode($offer_locations->location_map_x->PlaceHolder) ?>" value="<?php echo $offer_locations->location_map_x->EditValue ?>"<?php echo $offer_locations->location_map_x->EditAttributes() ?>>
</span>
<?php echo $offer_locations->location_map_x->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offer_locations->location_map_y->Visible) { // location_map_y ?>
	<tr id="r_location_map_y">
		<td><span id="elh_offer_locations_location_map_y"><?php echo $offer_locations->location_map_y->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offer_locations->location_map_y->CellAttributes() ?>>
<span id="el_offer_locations_location_map_y" class="control-group">
<input type="text" data-field="x_location_map_y" name="x_location_map_y" id="x_location_map_y" size="30" maxlength="14" placeholder="<?php echo ew_HtmlEncode($offer_locations->location_map_y->PlaceHolder) ?>" value="<?php echo $offer_locations->location_map_y->EditValue ?>"<?php echo $offer_locations->location_map_y->EditAttributes() ?>>
</span>
<?php echo $offer_locations->location_map_y->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offer_locations->location_status->Visible) { // location_status ?>
	<tr id="r_location_status">
		<td><span id="elh_offer_locations_location_status"><?php echo $offer_locations->location_status->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offer_locations->location_status->CellAttributes() ?>>
<span id="el_offer_locations_location_status" class="control-group">
<select data-field="x_location_status" id="x_location_status" name="x_location_status"<?php echo $offer_locations->location_status->EditAttributes() ?>>
<?php
if (is_array($offer_locations->location_status->EditValue)) {
	$arwrk = $offer_locations->location_status->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($offer_locations->location_status->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $offer_locations->location_status->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
</form>
<script type="text/javascript">
foffer_locationsadd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$offer_locations_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$offer_locations_add->Page_Terminate();
?>
