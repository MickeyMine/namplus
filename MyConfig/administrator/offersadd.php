<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "offersinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$offers_add = NULL; // Initialize page object first

class coffers_add extends coffers {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'offers';

	// Page object name
	var $PageObjName = 'offers_add';

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

		// Table object (offers)
		if (!isset($GLOBALS["offers"]) || get_class($GLOBALS["offers"]) == "coffers") {
			$GLOBALS["offers"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["offers"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'offers', TRUE);

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
			$this->Page_Terminate("offerslist.php");
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
			if (@$_GET["offer_id"] != "") {
				$this->offer_id->setQueryStringValue($_GET["offer_id"]);
				$this->setKey("offer_id", $this->offer_id->CurrentValue); // Set up key
			} else {
				$this->setKey("offer_id", ""); // Clear key
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
					$this->Page_Terminate("offerslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "offersview.php")
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
		$this->offer_image_path->Upload->Index = $objForm->Index;
		if ($this->offer_image_path->Upload->UploadFile()) {

			// No action required
		} else {
			echo $this->offer_image_path->Upload->Message;
			$this->Page_Terminate();
			exit();
		}
		$this->offer_image_path->CurrentValue = $this->offer_image_path->Upload->FileName;
		$this->offer_top_image->Upload->Index = $objForm->Index;
		if ($this->offer_top_image->Upload->UploadFile()) {

			// No action required
		} else {
			echo $this->offer_top_image->Upload->Message;
			$this->Page_Terminate();
			exit();
		}
		$this->offer_top_image->CurrentValue = $this->offer_top_image->Upload->FileName;
		$this->offer_bottom_image->Upload->Index = $objForm->Index;
		if ($this->offer_bottom_image->Upload->UploadFile()) {

			// No action required
		} else {
			echo $this->offer_bottom_image->Upload->Message;
			$this->Page_Terminate();
			exit();
		}
		$this->offer_bottom_image->CurrentValue = $this->offer_bottom_image->Upload->FileName;
	}

	// Load default values
	function LoadDefaultValues() {
		$this->offer_title->CurrentValue = NULL;
		$this->offer_title->OldValue = $this->offer_title->CurrentValue;
		$this->offer_description->CurrentValue = NULL;
		$this->offer_description->OldValue = $this->offer_description->CurrentValue;
		$this->offer_content->CurrentValue = NULL;
		$this->offer_content->OldValue = $this->offer_content->CurrentValue;
		$this->offer_question_content->CurrentValue = NULL;
		$this->offer_question_content->OldValue = $this->offer_question_content->CurrentValue;
		$this->offer_image_path->Upload->DbValue = NULL;
		$this->offer_image_path->OldValue = $this->offer_image_path->Upload->DbValue;
		$this->offer_image_path->CurrentValue = NULL; // Clear file related field
		$this->offer_top_image->Upload->DbValue = NULL;
		$this->offer_top_image->OldValue = $this->offer_top_image->Upload->DbValue;
		$this->offer_top_image->CurrentValue = NULL; // Clear file related field
		$this->offer_bottom_image->Upload->DbValue = NULL;
		$this->offer_bottom_image->OldValue = $this->offer_bottom_image->Upload->DbValue;
		$this->offer_bottom_image->CurrentValue = NULL; // Clear file related field
		$this->offer_start_date->CurrentValue = NULL;
		$this->offer_start_date->OldValue = $this->offer_start_date->CurrentValue;
		$this->offer_end_date->CurrentValue = NULL;
		$this->offer_end_date->OldValue = $this->offer_end_date->CurrentValue;
		$this->offer_start_time->CurrentValue = NULL;
		$this->offer_start_time->OldValue = $this->offer_start_time->CurrentValue;
		$this->offer_end_time->CurrentValue = NULL;
		$this->offer_end_time->OldValue = $this->offer_end_time->CurrentValue;
		$this->offer_rules->CurrentValue = NULL;
		$this->offer_rules->OldValue = $this->offer_rules->CurrentValue;
		$this->offer_value->CurrentValue = NULL;
		$this->offer_value->OldValue = $this->offer_value->CurrentValue;
		$this->offer_cat_id->CurrentValue = NULL;
		$this->offer_cat_id->OldValue = $this->offer_cat_id->CurrentValue;
		$this->offer_status->CurrentValue = NULL;
		$this->offer_status->OldValue = $this->offer_status->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
		if (!$this->offer_title->FldIsDetailKey) {
			$this->offer_title->setFormValue($objForm->GetValue("x_offer_title"));
		}
		if (!$this->offer_description->FldIsDetailKey) {
			$this->offer_description->setFormValue($objForm->GetValue("x_offer_description"));
		}
		if (!$this->offer_content->FldIsDetailKey) {
			$this->offer_content->setFormValue($objForm->GetValue("x_offer_content"));
		}
		if (!$this->offer_question_content->FldIsDetailKey) {
			$this->offer_question_content->setFormValue($objForm->GetValue("x_offer_question_content"));
		}
		if (!$this->offer_start_date->FldIsDetailKey) {
			$this->offer_start_date->setFormValue($objForm->GetValue("x_offer_start_date"));
			$this->offer_start_date->CurrentValue = ew_UnFormatDateTime($this->offer_start_date->CurrentValue, 7);
		}
		if (!$this->offer_end_date->FldIsDetailKey) {
			$this->offer_end_date->setFormValue($objForm->GetValue("x_offer_end_date"));
			$this->offer_end_date->CurrentValue = ew_UnFormatDateTime($this->offer_end_date->CurrentValue, 7);
		}
		if (!$this->offer_start_time->FldIsDetailKey) {
			$this->offer_start_time->setFormValue($objForm->GetValue("x_offer_start_time"));
		}
		if (!$this->offer_end_time->FldIsDetailKey) {
			$this->offer_end_time->setFormValue($objForm->GetValue("x_offer_end_time"));
		}
		if (!$this->offer_rules->FldIsDetailKey) {
			$this->offer_rules->setFormValue($objForm->GetValue("x_offer_rules"));
		}
		if (!$this->offer_value->FldIsDetailKey) {
			$this->offer_value->setFormValue($objForm->GetValue("x_offer_value"));
		}
		if (!$this->offer_cat_id->FldIsDetailKey) {
			$this->offer_cat_id->setFormValue($objForm->GetValue("x_offer_cat_id"));
		}
		if (!$this->offer_status->FldIsDetailKey) {
			$this->offer_status->setFormValue($objForm->GetValue("x_offer_status"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->offer_title->CurrentValue = $this->offer_title->FormValue;
		$this->offer_description->CurrentValue = $this->offer_description->FormValue;
		$this->offer_content->CurrentValue = $this->offer_content->FormValue;
		$this->offer_question_content->CurrentValue = $this->offer_question_content->FormValue;
		$this->offer_start_date->CurrentValue = $this->offer_start_date->FormValue;
		$this->offer_start_date->CurrentValue = ew_UnFormatDateTime($this->offer_start_date->CurrentValue, 7);
		$this->offer_end_date->CurrentValue = $this->offer_end_date->FormValue;
		$this->offer_end_date->CurrentValue = ew_UnFormatDateTime($this->offer_end_date->CurrentValue, 7);
		$this->offer_start_time->CurrentValue = $this->offer_start_time->FormValue;
		$this->offer_end_time->CurrentValue = $this->offer_end_time->FormValue;
		$this->offer_rules->CurrentValue = $this->offer_rules->FormValue;
		$this->offer_value->CurrentValue = $this->offer_value->FormValue;
		$this->offer_cat_id->CurrentValue = $this->offer_cat_id->FormValue;
		$this->offer_status->CurrentValue = $this->offer_status->FormValue;
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
		$this->offer_id->setDbValue($rs->fields('offer_id'));
		$this->offer_title->setDbValue($rs->fields('offer_title'));
		$this->offer_description->setDbValue($rs->fields('offer_description'));
		$this->offer_content->setDbValue($rs->fields('offer_content'));
		$this->offer_question_content->setDbValue($rs->fields('offer_question_content'));
		$this->offer_image_path->Upload->DbValue = $rs->fields('offer_image_path');
		$this->offer_image_path->CurrentValue = $this->offer_image_path->Upload->DbValue;
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
		$this->offer_cat_id->setDbValue($rs->fields('offer_cat_id'));
		$this->offer_status->setDbValue($rs->fields('offer_status'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->offer_id->DbValue = $row['offer_id'];
		$this->offer_title->DbValue = $row['offer_title'];
		$this->offer_description->DbValue = $row['offer_description'];
		$this->offer_content->DbValue = $row['offer_content'];
		$this->offer_question_content->DbValue = $row['offer_question_content'];
		$this->offer_image_path->Upload->DbValue = $row['offer_image_path'];
		$this->offer_top_image->Upload->DbValue = $row['offer_top_image'];
		$this->offer_bottom_image->Upload->DbValue = $row['offer_bottom_image'];
		$this->offer_start_date->DbValue = $row['offer_start_date'];
		$this->offer_end_date->DbValue = $row['offer_end_date'];
		$this->offer_start_time->DbValue = $row['offer_start_time'];
		$this->offer_end_time->DbValue = $row['offer_end_time'];
		$this->offer_rules->DbValue = $row['offer_rules'];
		$this->offer_value->DbValue = $row['offer_value'];
		$this->offer_cat_id->DbValue = $row['offer_cat_id'];
		$this->offer_status->DbValue = $row['offer_status'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("offer_id")) <> "")
			$this->offer_id->CurrentValue = $this->getKey("offer_id"); // offer_id
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
		// offer_id
		// offer_title
		// offer_description
		// offer_content
		// offer_question_content
		// offer_image_path
		// offer_top_image
		// offer_bottom_image
		// offer_start_date
		// offer_end_date
		// offer_start_time
		// offer_end_time
		// offer_rules
		// offer_value
		// offer_cat_id
		// offer_status

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// offer_id
			$this->offer_id->ViewValue = $this->offer_id->CurrentValue;
			$this->offer_id->ViewCustomAttributes = "";

			// offer_title
			$this->offer_title->ViewValue = $this->offer_title->CurrentValue;
			$this->offer_title->ViewCustomAttributes = "";

			// offer_description
			$this->offer_description->ViewValue = $this->offer_description->CurrentValue;
			$this->offer_description->ViewCustomAttributes = "";

			// offer_content
			$this->offer_content->ViewValue = $this->offer_content->CurrentValue;
			$this->offer_content->ViewCustomAttributes = "";

			// offer_question_content
			$this->offer_question_content->ViewValue = $this->offer_question_content->CurrentValue;
			$this->offer_question_content->ViewCustomAttributes = "";

			// offer_image_path
			if (!ew_Empty($this->offer_image_path->Upload->DbValue)) {
				$this->offer_image_path->ImageWidth = 80;
				$this->offer_image_path->ImageHeight = 0;
				$this->offer_image_path->ImageAlt = $this->offer_image_path->FldAlt();
				$this->offer_image_path->ViewValue = ew_UploadPathEx(FALSE, $this->offer_image_path->UploadPath) . $this->offer_image_path->Upload->DbValue;
			} else {
				$this->offer_image_path->ViewValue = "";
			}
			$this->offer_image_path->ViewCustomAttributes = "";

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
			$this->offer_start_time->ViewCustomAttributes = "";

			// offer_end_time
			$this->offer_end_time->ViewValue = $this->offer_end_time->CurrentValue;
			$this->offer_end_time->ViewCustomAttributes = "";

			// offer_rules
			$this->offer_rules->ViewValue = $this->offer_rules->CurrentValue;
			$this->offer_rules->ViewCustomAttributes = "";

			// offer_value
			$this->offer_value->ViewValue = $this->offer_value->CurrentValue;
			$this->offer_value->ViewCustomAttributes = "";

			// offer_cat_id
			if (strval($this->offer_cat_id->CurrentValue) <> "") {
				$sFilterWrk = "`cat_id`" . ew_SearchString("=", $this->offer_cat_id->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `cat_id`, `cat_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `viewoffers`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->offer_cat_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->offer_cat_id->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->offer_cat_id->ViewValue = $this->offer_cat_id->CurrentValue;
				}
			} else {
				$this->offer_cat_id->ViewValue = NULL;
			}
			$this->offer_cat_id->ViewCustomAttributes = "";

			// offer_status
			if (strval($this->offer_status->CurrentValue) <> "") {
				switch ($this->offer_status->CurrentValue) {
					case $this->offer_status->FldTagValue(1):
						$this->offer_status->ViewValue = $this->offer_status->FldTagCaption(1) <> "" ? $this->offer_status->FldTagCaption(1) : $this->offer_status->CurrentValue;
						break;
					case $this->offer_status->FldTagValue(2):
						$this->offer_status->ViewValue = $this->offer_status->FldTagCaption(2) <> "" ? $this->offer_status->FldTagCaption(2) : $this->offer_status->CurrentValue;
						break;
					default:
						$this->offer_status->ViewValue = $this->offer_status->CurrentValue;
				}
			} else {
				$this->offer_status->ViewValue = NULL;
			}
			$this->offer_status->ViewCustomAttributes = "";

			// offer_title
			$this->offer_title->LinkCustomAttributes = "";
			$this->offer_title->HrefValue = "";
			$this->offer_title->TooltipValue = "";

			// offer_description
			$this->offer_description->LinkCustomAttributes = "";
			$this->offer_description->HrefValue = "";
			$this->offer_description->TooltipValue = "";

			// offer_content
			$this->offer_content->LinkCustomAttributes = "";
			$this->offer_content->HrefValue = "";
			$this->offer_content->TooltipValue = "";

			// offer_question_content
			$this->offer_question_content->LinkCustomAttributes = "";
			$this->offer_question_content->HrefValue = "";
			$this->offer_question_content->TooltipValue = "";

			// offer_image_path
			$this->offer_image_path->LinkCustomAttributes = "";
			$this->offer_image_path->HrefValue = "";
			$this->offer_image_path->HrefValue2 = $this->offer_image_path->UploadPath . $this->offer_image_path->Upload->DbValue;
			$this->offer_image_path->TooltipValue = "";

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

			// offer_cat_id
			$this->offer_cat_id->LinkCustomAttributes = "";
			$this->offer_cat_id->HrefValue = "";
			$this->offer_cat_id->TooltipValue = "";

			// offer_status
			$this->offer_status->LinkCustomAttributes = "";
			$this->offer_status->HrefValue = "";
			$this->offer_status->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// offer_title
			$this->offer_title->EditCustomAttributes = "";
			$this->offer_title->EditValue = ew_HtmlEncode($this->offer_title->CurrentValue);
			$this->offer_title->PlaceHolder = ew_RemoveHtml($this->offer_title->FldCaption());

			// offer_description
			$this->offer_description->EditCustomAttributes = "";
			$this->offer_description->EditValue = ew_HtmlEncode($this->offer_description->CurrentValue);
			$this->offer_description->PlaceHolder = ew_RemoveHtml($this->offer_description->FldCaption());

			// offer_content
			$this->offer_content->EditCustomAttributes = "";
			$this->offer_content->EditValue = $this->offer_content->CurrentValue;
			$this->offer_content->PlaceHolder = ew_RemoveHtml($this->offer_content->FldCaption());

			// offer_question_content
			$this->offer_question_content->EditCustomAttributes = "";
			$this->offer_question_content->EditValue = $this->offer_question_content->CurrentValue;
			$this->offer_question_content->PlaceHolder = ew_RemoveHtml($this->offer_question_content->FldCaption());

			// offer_image_path
			$this->offer_image_path->EditCustomAttributes = "";
			if (!ew_Empty($this->offer_image_path->Upload->DbValue)) {
				$this->offer_image_path->ImageWidth = 80;
				$this->offer_image_path->ImageHeight = 0;
				$this->offer_image_path->ImageAlt = $this->offer_image_path->FldAlt();
				$this->offer_image_path->EditValue = ew_UploadPathEx(FALSE, $this->offer_image_path->UploadPath) . $this->offer_image_path->Upload->DbValue;
			} else {
				$this->offer_image_path->EditValue = "";
			}
			if (!ew_Empty($this->offer_image_path->CurrentValue))
				$this->offer_image_path->Upload->FileName = $this->offer_image_path->CurrentValue;
			if (($this->CurrentAction == "I" || $this->CurrentAction == "C") && !$this->EventCancelled) ew_RenderUploadField($this->offer_image_path);

			// offer_top_image
			$this->offer_top_image->EditCustomAttributes = "";
			if (!ew_Empty($this->offer_top_image->Upload->DbValue)) {
				$this->offer_top_image->ImageWidth = 80;
				$this->offer_top_image->ImageHeight = 0;
				$this->offer_top_image->ImageAlt = $this->offer_top_image->FldAlt();
				$this->offer_top_image->EditValue = ew_UploadPathEx(FALSE, $this->offer_top_image->UploadPath) . $this->offer_top_image->Upload->DbValue;
			} else {
				$this->offer_top_image->EditValue = "";
			}
			if (!ew_Empty($this->offer_top_image->CurrentValue))
				$this->offer_top_image->Upload->FileName = $this->offer_top_image->CurrentValue;
			if (($this->CurrentAction == "I" || $this->CurrentAction == "C") && !$this->EventCancelled) ew_RenderUploadField($this->offer_top_image);

			// offer_bottom_image
			$this->offer_bottom_image->EditCustomAttributes = "";
			if (!ew_Empty($this->offer_bottom_image->Upload->DbValue)) {
				$this->offer_bottom_image->ImageWidth = 80;
				$this->offer_bottom_image->ImageHeight = 0;
				$this->offer_bottom_image->ImageAlt = $this->offer_bottom_image->FldAlt();
				$this->offer_bottom_image->EditValue = ew_UploadPathEx(FALSE, $this->offer_bottom_image->UploadPath) . $this->offer_bottom_image->Upload->DbValue;
			} else {
				$this->offer_bottom_image->EditValue = "";
			}
			if (!ew_Empty($this->offer_bottom_image->CurrentValue))
				$this->offer_bottom_image->Upload->FileName = $this->offer_bottom_image->CurrentValue;
			if (($this->CurrentAction == "I" || $this->CurrentAction == "C") && !$this->EventCancelled) ew_RenderUploadField($this->offer_bottom_image);

			// offer_start_date
			$this->offer_start_date->EditCustomAttributes = "";
			$this->offer_start_date->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->offer_start_date->CurrentValue, 7));
			$this->offer_start_date->PlaceHolder = ew_RemoveHtml($this->offer_start_date->FldCaption());

			// offer_end_date
			$this->offer_end_date->EditCustomAttributes = "";
			$this->offer_end_date->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->offer_end_date->CurrentValue, 7));
			$this->offer_end_date->PlaceHolder = ew_RemoveHtml($this->offer_end_date->FldCaption());

			// offer_start_time
			$this->offer_start_time->EditCustomAttributes = "";
			$this->offer_start_time->EditValue = ew_HtmlEncode($this->offer_start_time->CurrentValue);
			$this->offer_start_time->PlaceHolder = ew_RemoveHtml($this->offer_start_time->FldCaption());

			// offer_end_time
			$this->offer_end_time->EditCustomAttributes = "";
			$this->offer_end_time->EditValue = ew_HtmlEncode($this->offer_end_time->CurrentValue);
			$this->offer_end_time->PlaceHolder = ew_RemoveHtml($this->offer_end_time->FldCaption());

			// offer_rules
			$this->offer_rules->EditCustomAttributes = "";
			$this->offer_rules->EditValue = $this->offer_rules->CurrentValue;
			$this->offer_rules->PlaceHolder = ew_RemoveHtml($this->offer_rules->FldCaption());

			// offer_value
			$this->offer_value->EditCustomAttributes = "";
			$this->offer_value->EditValue = ew_HtmlEncode($this->offer_value->CurrentValue);
			$this->offer_value->PlaceHolder = ew_RemoveHtml($this->offer_value->FldCaption());

			// offer_cat_id
			$this->offer_cat_id->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `cat_id`, `cat_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `viewoffers`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->offer_cat_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->offer_cat_id->EditValue = $arwrk;

			// offer_status
			$this->offer_status->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->offer_status->FldTagValue(1), $this->offer_status->FldTagCaption(1) <> "" ? $this->offer_status->FldTagCaption(1) : $this->offer_status->FldTagValue(1));
			$arwrk[] = array($this->offer_status->FldTagValue(2), $this->offer_status->FldTagCaption(2) <> "" ? $this->offer_status->FldTagCaption(2) : $this->offer_status->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->offer_status->EditValue = $arwrk;

			// Edit refer script
			// offer_title

			$this->offer_title->HrefValue = "";

			// offer_description
			$this->offer_description->HrefValue = "";

			// offer_content
			$this->offer_content->HrefValue = "";

			// offer_question_content
			$this->offer_question_content->HrefValue = "";

			// offer_image_path
			$this->offer_image_path->HrefValue = "";
			$this->offer_image_path->HrefValue2 = $this->offer_image_path->UploadPath . $this->offer_image_path->Upload->DbValue;

			// offer_top_image
			$this->offer_top_image->HrefValue = "";
			$this->offer_top_image->HrefValue2 = $this->offer_top_image->UploadPath . $this->offer_top_image->Upload->DbValue;

			// offer_bottom_image
			$this->offer_bottom_image->HrefValue = "";
			$this->offer_bottom_image->HrefValue2 = $this->offer_bottom_image->UploadPath . $this->offer_bottom_image->Upload->DbValue;

			// offer_start_date
			$this->offer_start_date->HrefValue = "";

			// offer_end_date
			$this->offer_end_date->HrefValue = "";

			// offer_start_time
			$this->offer_start_time->HrefValue = "";

			// offer_end_time
			$this->offer_end_time->HrefValue = "";

			// offer_rules
			$this->offer_rules->HrefValue = "";

			// offer_value
			$this->offer_value->HrefValue = "";

			// offer_cat_id
			$this->offer_cat_id->HrefValue = "";

			// offer_status
			$this->offer_status->HrefValue = "";
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
		if (!$this->offer_title->FldIsDetailKey && !is_null($this->offer_title->FormValue) && $this->offer_title->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->offer_title->FldCaption());
		}
		if (!$this->offer_description->FldIsDetailKey && !is_null($this->offer_description->FormValue) && $this->offer_description->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->offer_description->FldCaption());
		}
		if (!$this->offer_content->FldIsDetailKey && !is_null($this->offer_content->FormValue) && $this->offer_content->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->offer_content->FldCaption());
		}
		if (!$this->offer_question_content->FldIsDetailKey && !is_null($this->offer_question_content->FormValue) && $this->offer_question_content->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->offer_question_content->FldCaption());
		}
		if (is_null($this->offer_image_path->Upload->Value) && !$this->offer_image_path->Upload->KeepFile) {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->offer_image_path->FldCaption());
		}
		if (is_null($this->offer_top_image->Upload->Value) && !$this->offer_top_image->Upload->KeepFile) {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->offer_top_image->FldCaption());
		}
		if (is_null($this->offer_bottom_image->Upload->Value) && !$this->offer_bottom_image->Upload->KeepFile) {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->offer_bottom_image->FldCaption());
		}
		if (!$this->offer_start_date->FldIsDetailKey && !is_null($this->offer_start_date->FormValue) && $this->offer_start_date->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->offer_start_date->FldCaption());
		}
		if (!ew_CheckEuroDate($this->offer_start_date->FormValue)) {
			ew_AddMessage($gsFormError, $this->offer_start_date->FldErrMsg());
		}
		if (!$this->offer_end_date->FldIsDetailKey && !is_null($this->offer_end_date->FormValue) && $this->offer_end_date->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->offer_end_date->FldCaption());
		}
		if (!ew_CheckEuroDate($this->offer_end_date->FormValue)) {
			ew_AddMessage($gsFormError, $this->offer_end_date->FldErrMsg());
		}
		if (!$this->offer_start_time->FldIsDetailKey && !is_null($this->offer_start_time->FormValue) && $this->offer_start_time->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->offer_start_time->FldCaption());
		}
		if (!ew_CheckTime($this->offer_start_time->FormValue)) {
			ew_AddMessage($gsFormError, $this->offer_start_time->FldErrMsg());
		}
		if (!$this->offer_end_time->FldIsDetailKey && !is_null($this->offer_end_time->FormValue) && $this->offer_end_time->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->offer_end_time->FldCaption());
		}
		if (!ew_CheckTime($this->offer_end_time->FormValue)) {
			ew_AddMessage($gsFormError, $this->offer_end_time->FldErrMsg());
		}
		if (!$this->offer_rules->FldIsDetailKey && !is_null($this->offer_rules->FormValue) && $this->offer_rules->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->offer_rules->FldCaption());
		}
		if (!$this->offer_value->FldIsDetailKey && !is_null($this->offer_value->FormValue) && $this->offer_value->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->offer_value->FldCaption());
		}
		if (!$this->offer_cat_id->FldIsDetailKey && !is_null($this->offer_cat_id->FormValue) && $this->offer_cat_id->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->offer_cat_id->FldCaption());
		}
		if (!$this->offer_status->FldIsDetailKey && !is_null($this->offer_status->FormValue) && $this->offer_status->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->offer_status->FldCaption());
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

		// offer_title
		$this->offer_title->SetDbValueDef($rsnew, $this->offer_title->CurrentValue, "", FALSE);

		// offer_description
		$this->offer_description->SetDbValueDef($rsnew, $this->offer_description->CurrentValue, "", FALSE);

		// offer_content
		$this->offer_content->SetDbValueDef($rsnew, $this->offer_content->CurrentValue, "", FALSE);

		// offer_question_content
		$this->offer_question_content->SetDbValueDef($rsnew, $this->offer_question_content->CurrentValue, "", FALSE);

		// offer_image_path
		if (!$this->offer_image_path->Upload->KeepFile) {
			if ($this->offer_image_path->Upload->FileName == "") {
				$rsnew['offer_image_path'] = NULL;
			} else {
				$rsnew['offer_image_path'] = $this->offer_image_path->Upload->FileName;
			}
		}

		// offer_top_image
		if (!$this->offer_top_image->Upload->KeepFile) {
			if ($this->offer_top_image->Upload->FileName == "") {
				$rsnew['offer_top_image'] = NULL;
			} else {
				$rsnew['offer_top_image'] = $this->offer_top_image->Upload->FileName;
			}
		}

		// offer_bottom_image
		if (!$this->offer_bottom_image->Upload->KeepFile) {
			if ($this->offer_bottom_image->Upload->FileName == "") {
				$rsnew['offer_bottom_image'] = NULL;
			} else {
				$rsnew['offer_bottom_image'] = $this->offer_bottom_image->Upload->FileName;
			}
		}

		// offer_start_date
		$this->offer_start_date->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->offer_start_date->CurrentValue, 7), ew_CurrentDate(), FALSE);

		// offer_end_date
		$this->offer_end_date->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->offer_end_date->CurrentValue, 7), ew_CurrentDate(), FALSE);

		// offer_start_time
		$this->offer_start_time->SetDbValueDef($rsnew, $this->offer_start_time->CurrentValue, ew_CurrentTime(), FALSE);

		// offer_end_time
		$this->offer_end_time->SetDbValueDef($rsnew, $this->offer_end_time->CurrentValue, ew_CurrentTime(), FALSE);

		// offer_rules
		$this->offer_rules->SetDbValueDef($rsnew, $this->offer_rules->CurrentValue, "", FALSE);

		// offer_value
		$this->offer_value->SetDbValueDef($rsnew, $this->offer_value->CurrentValue, "", FALSE);

		// offer_cat_id
		$this->offer_cat_id->SetDbValueDef($rsnew, $this->offer_cat_id->CurrentValue, 0, FALSE);

		// offer_status
		$this->offer_status->SetDbValueDef($rsnew, $this->offer_status->CurrentValue, 0, FALSE);
		if (!$this->offer_image_path->Upload->KeepFile) {
			if (!ew_Empty($this->offer_image_path->Upload->Value)) {
				if ($this->offer_image_path->Upload->FileName == $this->offer_image_path->Upload->DbValue) { // Overwrite if same file name
					$this->offer_image_path->Upload->DbValue = ""; // No need to delete any more
				} else {
					$rsnew['offer_image_path'] = ew_UploadFileNameEx(ew_UploadPathEx(TRUE, $this->offer_image_path->UploadPath), $rsnew['offer_image_path']); // Get new file name
				}
			}
		}
		if (!$this->offer_top_image->Upload->KeepFile) {
			if (!ew_Empty($this->offer_top_image->Upload->Value)) {
				if ($this->offer_top_image->Upload->FileName == $this->offer_top_image->Upload->DbValue) { // Overwrite if same file name
					$this->offer_top_image->Upload->DbValue = ""; // No need to delete any more
				} else {
					$rsnew['offer_top_image'] = ew_UploadFileNameEx(ew_UploadPathEx(TRUE, $this->offer_top_image->UploadPath), $rsnew['offer_top_image']); // Get new file name
				}
			}
		}
		if (!$this->offer_bottom_image->Upload->KeepFile) {
			if (!ew_Empty($this->offer_bottom_image->Upload->Value)) {
				if ($this->offer_bottom_image->Upload->FileName == $this->offer_bottom_image->Upload->DbValue) { // Overwrite if same file name
					$this->offer_bottom_image->Upload->DbValue = ""; // No need to delete any more
				} else {
					$rsnew['offer_bottom_image'] = ew_UploadFileNameEx(ew_UploadPathEx(TRUE, $this->offer_bottom_image->UploadPath), $rsnew['offer_bottom_image']); // Get new file name
				}
			}
		}

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
				if (!$this->offer_image_path->Upload->KeepFile) {
					if (!ew_Empty($this->offer_image_path->Upload->Value)) {
						$this->offer_image_path->Upload->SaveToFile($this->offer_image_path->UploadPath, $rsnew['offer_image_path'], TRUE);
					}
					if ($this->offer_image_path->Upload->DbValue <> "")
						@unlink(ew_UploadPathEx(TRUE, $this->offer_image_path->OldUploadPath) . $this->offer_image_path->Upload->DbValue);
				}
				if (!$this->offer_top_image->Upload->KeepFile) {
					if (!ew_Empty($this->offer_top_image->Upload->Value)) {
						$this->offer_top_image->Upload->SaveToFile($this->offer_top_image->UploadPath, $rsnew['offer_top_image'], TRUE);
					}
					if ($this->offer_top_image->Upload->DbValue <> "")
						@unlink(ew_UploadPathEx(TRUE, $this->offer_top_image->OldUploadPath) . $this->offer_top_image->Upload->DbValue);
				}
				if (!$this->offer_bottom_image->Upload->KeepFile) {
					if (!ew_Empty($this->offer_bottom_image->Upload->Value)) {
						$this->offer_bottom_image->Upload->SaveToFile($this->offer_bottom_image->UploadPath, $rsnew['offer_bottom_image'], TRUE);
					}
					if ($this->offer_bottom_image->Upload->DbValue <> "")
						@unlink(ew_UploadPathEx(TRUE, $this->offer_bottom_image->OldUploadPath) . $this->offer_bottom_image->Upload->DbValue);
				}
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
			$this->offer_id->setDbValue($conn->Insert_ID());
			$rsnew['offer_id'] = $this->offer_id->DbValue;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
		}

		// offer_image_path
		ew_CleanUploadTempPath($this->offer_image_path, $this->offer_image_path->Upload->Index);

		// offer_top_image
		ew_CleanUploadTempPath($this->offer_top_image, $this->offer_top_image->Upload->Index);

		// offer_bottom_image
		ew_CleanUploadTempPath($this->offer_bottom_image, $this->offer_bottom_image->Upload->Index);
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "offerslist.php", $this->TableVar, TRUE);
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
if (!isset($offers_add)) $offers_add = new coffers_add();

// Page init
$offers_add->Page_Init();

// Page main
$offers_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$offers_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var offers_add = new ew_Page("offers_add");
offers_add.PageID = "add"; // Page ID
var EW_PAGE_ID = offers_add.PageID; // For backward compatibility

// Form object
var foffersadd = new ew_Form("foffersadd");

// Validate form
foffersadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_offer_title");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offers->offer_title->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_offer_description");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offers->offer_description->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_offer_content");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offers->offer_content->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_offer_question_content");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offers->offer_question_content->FldCaption()) ?>");
			felm = this.GetElements("x" + infix + "_offer_image_path");
			elm = this.GetElements("fn_x" + infix + "_offer_image_path");
			if (felm && elm && !ew_HasValue(elm))
				return this.OnError(felm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offers->offer_image_path->FldCaption()) ?>");
			felm = this.GetElements("x" + infix + "_offer_top_image");
			elm = this.GetElements("fn_x" + infix + "_offer_top_image");
			if (felm && elm && !ew_HasValue(elm))
				return this.OnError(felm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offers->offer_top_image->FldCaption()) ?>");
			felm = this.GetElements("x" + infix + "_offer_bottom_image");
			elm = this.GetElements("fn_x" + infix + "_offer_bottom_image");
			if (felm && elm && !ew_HasValue(elm))
				return this.OnError(felm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offers->offer_bottom_image->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_offer_start_date");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offers->offer_start_date->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_offer_start_date");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($offers->offer_start_date->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_offer_end_date");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offers->offer_end_date->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_offer_end_date");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($offers->offer_end_date->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_offer_start_time");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offers->offer_start_time->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_offer_start_time");
			if (elm && !ew_CheckTime(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($offers->offer_start_time->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_offer_end_time");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offers->offer_end_time->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_offer_end_time");
			if (elm && !ew_CheckTime(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($offers->offer_end_time->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_offer_rules");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offers->offer_rules->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_offer_value");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offers->offer_value->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_offer_cat_id");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offers->offer_cat_id->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_offer_status");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offers->offer_status->FldCaption()) ?>");

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
foffersadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
foffersadd.ValidateRequired = true;
<?php } else { ?>
foffersadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
foffersadd.Lists["x_offer_cat_id"] = {"LinkField":"x_cat_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_cat_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $offers_add->ShowPageHeader(); ?>
<?php
$offers_add->ShowMessage();
?>
<form name="foffersadd" id="foffersadd" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="offers">
<input type="hidden" name="a_add" id="a_add" value="A">
<table class="ewGrid"><tr><td>
<table id="tbl_offersadd" class="table table-bordered table-striped">
<?php if ($offers->offer_title->Visible) { // offer_title ?>
	<tr id="r_offer_title">
		<td><span id="elh_offers_offer_title"><?php echo $offers->offer_title->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offers->offer_title->CellAttributes() ?>>
<span id="el_offers_offer_title" class="control-group">
<input type="text" data-field="x_offer_title" name="x_offer_title" id="x_offer_title" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($offers->offer_title->PlaceHolder) ?>" value="<?php echo $offers->offer_title->EditValue ?>"<?php echo $offers->offer_title->EditAttributes() ?>>
</span>
<?php echo $offers->offer_title->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offers->offer_description->Visible) { // offer_description ?>
	<tr id="r_offer_description">
		<td><span id="elh_offers_offer_description"><?php echo $offers->offer_description->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offers->offer_description->CellAttributes() ?>>
<span id="el_offers_offer_description" class="control-group">
<input type="text" data-field="x_offer_description" name="x_offer_description" id="x_offer_description" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($offers->offer_description->PlaceHolder) ?>" value="<?php echo $offers->offer_description->EditValue ?>"<?php echo $offers->offer_description->EditAttributes() ?>>
</span>
<?php echo $offers->offer_description->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offers->offer_content->Visible) { // offer_content ?>
	<tr id="r_offer_content">
		<td><span id="elh_offers_offer_content"><?php echo $offers->offer_content->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offers->offer_content->CellAttributes() ?>>
<span id="el_offers_offer_content" class="control-group">
<textarea data-field="x_offer_content" class="editor" name="x_offer_content" id="x_offer_content" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($offers->offer_content->PlaceHolder) ?>"<?php echo $offers->offer_content->EditAttributes() ?>><?php echo $offers->offer_content->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("foffersadd", "x_offer_content", 35, 4, <?php echo ($offers->offer_content->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
<?php echo $offers->offer_content->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offers->offer_question_content->Visible) { // offer_question_content ?>
	<tr id="r_offer_question_content">
		<td><span id="elh_offers_offer_question_content"><?php echo $offers->offer_question_content->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offers->offer_question_content->CellAttributes() ?>>
<span id="el_offers_offer_question_content" class="control-group">
<textarea data-field="x_offer_question_content" class="editor" name="x_offer_question_content" id="x_offer_question_content" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($offers->offer_question_content->PlaceHolder) ?>"<?php echo $offers->offer_question_content->EditAttributes() ?>><?php echo $offers->offer_question_content->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("foffersadd", "x_offer_question_content", 35, 4, <?php echo ($offers->offer_question_content->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
<?php echo $offers->offer_question_content->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offers->offer_image_path->Visible) { // offer_image_path ?>
	<tr id="r_offer_image_path">
		<td><span id="elh_offers_offer_image_path"><?php echo $offers->offer_image_path->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offers->offer_image_path->CellAttributes() ?>>
<span id="el_offers_offer_image_path" class="control-group">
<span id="fd_x_offer_image_path">
<span class="btn btn-small fileinput-button"<?php if ($offers->offer_image_path->ReadOnly || $offers->offer_image_path->Disabled) echo " style=\"display: none;\""; ?>>
	<span><?php echo $Language->Phrase("ChooseFile") ?></span>
	<input type="file" data-field="x_offer_image_path" name="x_offer_image_path" id="x_offer_image_path">
</span>
<input type="hidden" name="fn_x_offer_image_path" id= "fn_x_offer_image_path" value="<?php echo $offers->offer_image_path->Upload->FileName ?>">
<input type="hidden" name="fa_x_offer_image_path" id= "fa_x_offer_image_path" value="0">
<input type="hidden" name="fs_x_offer_image_path" id= "fs_x_offer_image_path" value="150">
</span>
<table id="ft_x_offer_image_path" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $offers->offer_image_path->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offers->offer_top_image->Visible) { // offer_top_image ?>
	<tr id="r_offer_top_image">
		<td><span id="elh_offers_offer_top_image"><?php echo $offers->offer_top_image->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offers->offer_top_image->CellAttributes() ?>>
<span id="el_offers_offer_top_image" class="control-group">
<span id="fd_x_offer_top_image">
<span class="btn btn-small fileinput-button"<?php if ($offers->offer_top_image->ReadOnly || $offers->offer_top_image->Disabled) echo " style=\"display: none;\""; ?>>
	<span><?php echo $Language->Phrase("ChooseFile") ?></span>
	<input type="file" data-field="x_offer_top_image" name="x_offer_top_image" id="x_offer_top_image">
</span>
<input type="hidden" name="fn_x_offer_top_image" id= "fn_x_offer_top_image" value="<?php echo $offers->offer_top_image->Upload->FileName ?>">
<input type="hidden" name="fa_x_offer_top_image" id= "fa_x_offer_top_image" value="0">
<input type="hidden" name="fs_x_offer_top_image" id= "fs_x_offer_top_image" value="150">
</span>
<table id="ft_x_offer_top_image" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $offers->offer_top_image->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offers->offer_bottom_image->Visible) { // offer_bottom_image ?>
	<tr id="r_offer_bottom_image">
		<td><span id="elh_offers_offer_bottom_image"><?php echo $offers->offer_bottom_image->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offers->offer_bottom_image->CellAttributes() ?>>
<span id="el_offers_offer_bottom_image" class="control-group">
<span id="fd_x_offer_bottom_image">
<span class="btn btn-small fileinput-button"<?php if ($offers->offer_bottom_image->ReadOnly || $offers->offer_bottom_image->Disabled) echo " style=\"display: none;\""; ?>>
	<span><?php echo $Language->Phrase("ChooseFile") ?></span>
	<input type="file" data-field="x_offer_bottom_image" name="x_offer_bottom_image" id="x_offer_bottom_image">
</span>
<input type="hidden" name="fn_x_offer_bottom_image" id= "fn_x_offer_bottom_image" value="<?php echo $offers->offer_bottom_image->Upload->FileName ?>">
<input type="hidden" name="fa_x_offer_bottom_image" id= "fa_x_offer_bottom_image" value="0">
<input type="hidden" name="fs_x_offer_bottom_image" id= "fs_x_offer_bottom_image" value="150">
</span>
<table id="ft_x_offer_bottom_image" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $offers->offer_bottom_image->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offers->offer_start_date->Visible) { // offer_start_date ?>
	<tr id="r_offer_start_date">
		<td><span id="elh_offers_offer_start_date"><?php echo $offers->offer_start_date->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offers->offer_start_date->CellAttributes() ?>>
<span id="el_offers_offer_start_date" class="control-group">
<input type="text" data-field="x_offer_start_date" name="x_offer_start_date" id="x_offer_start_date" placeholder="<?php echo ew_HtmlEncode($offers->offer_start_date->PlaceHolder) ?>" value="<?php echo $offers->offer_start_date->EditValue ?>"<?php echo $offers->offer_start_date->EditAttributes() ?>>
<?php if (!$offers->offer_start_date->ReadOnly && !$offers->offer_start_date->Disabled && @$offers->offer_start_date->EditAttrs["readonly"] == "" && @$offers->offer_start_date->EditAttrs["disabled"] == "") { ?>
<button id="cal_x_offer_start_date" name="cal_x_offer_start_date" class="btn" type="button"><img src="phpimages/calendar.png" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" style="border: 0;"></button><script type="text/javascript">
ew_CreateCalendar("foffersadd", "x_offer_start_date", "%d/%m/%Y");
</script>
<?php } ?>
</span>
<?php echo $offers->offer_start_date->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offers->offer_end_date->Visible) { // offer_end_date ?>
	<tr id="r_offer_end_date">
		<td><span id="elh_offers_offer_end_date"><?php echo $offers->offer_end_date->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offers->offer_end_date->CellAttributes() ?>>
<span id="el_offers_offer_end_date" class="control-group">
<input type="text" data-field="x_offer_end_date" name="x_offer_end_date" id="x_offer_end_date" placeholder="<?php echo ew_HtmlEncode($offers->offer_end_date->PlaceHolder) ?>" value="<?php echo $offers->offer_end_date->EditValue ?>"<?php echo $offers->offer_end_date->EditAttributes() ?>>
<?php if (!$offers->offer_end_date->ReadOnly && !$offers->offer_end_date->Disabled && @$offers->offer_end_date->EditAttrs["readonly"] == "" && @$offers->offer_end_date->EditAttrs["disabled"] == "") { ?>
<button id="cal_x_offer_end_date" name="cal_x_offer_end_date" class="btn" type="button"><img src="phpimages/calendar.png" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" style="border: 0;"></button><script type="text/javascript">
ew_CreateCalendar("foffersadd", "x_offer_end_date", "%d/%m/%Y");
</script>
<?php } ?>
</span>
<?php echo $offers->offer_end_date->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offers->offer_start_time->Visible) { // offer_start_time ?>
	<tr id="r_offer_start_time">
		<td><span id="elh_offers_offer_start_time"><?php echo $offers->offer_start_time->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offers->offer_start_time->CellAttributes() ?>>
<span id="el_offers_offer_start_time" class="control-group">
<input type="text" data-field="x_offer_start_time" name="x_offer_start_time" id="x_offer_start_time" size="30" placeholder="<?php echo ew_HtmlEncode($offers->offer_start_time->PlaceHolder) ?>" value="<?php echo $offers->offer_start_time->EditValue ?>"<?php echo $offers->offer_start_time->EditAttributes() ?>>
</span>
<?php echo $offers->offer_start_time->CustomMsg ?>
			<script>
                $(function() {
                    $('#x_offer_start_time').timepicker({ 
                        'scrollDefaultNow': true,
                        'timeFormat': 'H:i:s',
                        'step': 15 
                    });
                });
            </script>
		</td>
	</tr>
<?php } ?>
<?php if ($offers->offer_end_time->Visible) { // offer_end_time ?>
	<tr id="r_offer_end_time">
		<td><span id="elh_offers_offer_end_time"><?php echo $offers->offer_end_time->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offers->offer_end_time->CellAttributes() ?>>
<span id="el_offers_offer_end_time" class="control-group">
<input type="text" data-field="x_offer_end_time" name="x_offer_end_time" id="x_offer_end_time" size="30" placeholder="<?php echo ew_HtmlEncode($offers->offer_end_time->PlaceHolder) ?>" value="<?php echo $offers->offer_end_time->EditValue ?>"<?php echo $offers->offer_end_time->EditAttributes() ?>>
</span>
<?php echo $offers->offer_end_time->CustomMsg ?>
			<script>
                $(function() {
                    $('#x_offer_end_time').timepicker({ 
                        'scrollDefaultNow': true,
                        'timeFormat': 'H:i:s',
                        'step': 15 
                    });
                });
            </script>
		</td>
	</tr>
<?php } ?>
<?php if ($offers->offer_rules->Visible) { // offer_rules ?>
	<tr id="r_offer_rules">
		<td><span id="elh_offers_offer_rules"><?php echo $offers->offer_rules->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offers->offer_rules->CellAttributes() ?>>
<span id="el_offers_offer_rules" class="control-group">
<textarea data-field="x_offer_rules" name="x_offer_rules" id="x_offer_rules" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($offers->offer_rules->PlaceHolder) ?>"<?php echo $offers->offer_rules->EditAttributes() ?>><?php echo $offers->offer_rules->EditValue ?></textarea>
</span>
<?php echo $offers->offer_rules->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offers->offer_value->Visible) { // offer_value ?>
	<tr id="r_offer_value">
		<td><span id="elh_offers_offer_value"><?php echo $offers->offer_value->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offers->offer_value->CellAttributes() ?>>
<span id="el_offers_offer_value" class="control-group">
<input type="text" data-field="x_offer_value" name="x_offer_value" id="x_offer_value" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($offers->offer_value->PlaceHolder) ?>" value="<?php echo $offers->offer_value->EditValue ?>"<?php echo $offers->offer_value->EditAttributes() ?>>
</span>
<?php echo $offers->offer_value->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offers->offer_cat_id->Visible) { // offer_cat_id ?>
	<tr id="r_offer_cat_id">
		<td><span id="elh_offers_offer_cat_id"><?php echo $offers->offer_cat_id->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offers->offer_cat_id->CellAttributes() ?>>
<span id="el_offers_offer_cat_id" class="control-group">
<select data-field="x_offer_cat_id" id="x_offer_cat_id" name="x_offer_cat_id"<?php echo $offers->offer_cat_id->EditAttributes() ?>>
<?php
if (is_array($offers->offer_cat_id->EditValue)) {
	$arwrk = $offers->offer_cat_id->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($offers->offer_cat_id->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
foffersadd.Lists["x_offer_cat_id"].Options = <?php echo (is_array($offers->offer_cat_id->EditValue)) ? ew_ArrayToJson($offers->offer_cat_id->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $offers->offer_cat_id->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offers->offer_status->Visible) { // offer_status ?>
	<tr id="r_offer_status">
		<td><span id="elh_offers_offer_status"><?php echo $offers->offer_status->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offers->offer_status->CellAttributes() ?>>
<span id="el_offers_offer_status" class="control-group">
<select data-field="x_offer_status" id="x_offer_status" name="x_offer_status"<?php echo $offers->offer_status->EditAttributes() ?>>
<?php
if (is_array($offers->offer_status->EditValue)) {
	$arwrk = $offers->offer_status->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($offers->offer_status->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $offers->offer_status->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
</form>
<script type="text/javascript">
foffersadd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$offers_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$offers_add->Page_Terminate();
?>
