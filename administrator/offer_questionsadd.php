<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "offer_questionsinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$offer_questions_add = NULL; // Initialize page object first

class coffer_questions_add extends coffer_questions {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'offer_questions';

	// Page object name
	var $PageObjName = 'offer_questions_add';

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

		// Table object (offer_questions)
		if (!isset($GLOBALS["offer_questions"]) || get_class($GLOBALS["offer_questions"]) == "coffer_questions") {
			$GLOBALS["offer_questions"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["offer_questions"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'offer_questions', TRUE);

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
			$this->Page_Terminate("offer_questionslist.php");
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
			if (@$_GET["question_id"] != "") {
				$this->question_id->setQueryStringValue($_GET["question_id"]);
				$this->setKey("question_id", $this->question_id->CurrentValue); // Set up key
			} else {
				$this->setKey("question_id", ""); // Clear key
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
					$this->Page_Terminate("offer_questionslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "offer_questionsview.php")
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
		$this->question_content->CurrentValue = NULL;
		$this->question_content->OldValue = $this->question_content->CurrentValue;
		$this->question_type->CurrentValue = 0;
		$this->offer_id->CurrentValue = NULL;
		$this->offer_id->OldValue = $this->offer_id->CurrentValue;
		$this->question_status->CurrentValue = NULL;
		$this->question_status->OldValue = $this->question_status->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->question_content->FldIsDetailKey) {
			$this->question_content->setFormValue($objForm->GetValue("x_question_content"));
		}
		if (!$this->question_type->FldIsDetailKey) {
			$this->question_type->setFormValue($objForm->GetValue("x_question_type"));
		}
		if (!$this->offer_id->FldIsDetailKey) {
			$this->offer_id->setFormValue($objForm->GetValue("x_offer_id"));
		}
		if (!$this->question_status->FldIsDetailKey) {
			$this->question_status->setFormValue($objForm->GetValue("x_question_status"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->question_content->CurrentValue = $this->question_content->FormValue;
		$this->question_type->CurrentValue = $this->question_type->FormValue;
		$this->offer_id->CurrentValue = $this->offer_id->FormValue;
		$this->question_status->CurrentValue = $this->question_status->FormValue;
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
		$this->question_id->setDbValue($rs->fields('question_id'));
		$this->question_content->setDbValue($rs->fields('question_content'));
		$this->question_type->setDbValue($rs->fields('question_type'));
		$this->offer_id->setDbValue($rs->fields('offer_id'));
		$this->question_status->setDbValue($rs->fields('question_status'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->question_id->DbValue = $row['question_id'];
		$this->question_content->DbValue = $row['question_content'];
		$this->question_type->DbValue = $row['question_type'];
		$this->offer_id->DbValue = $row['offer_id'];
		$this->question_status->DbValue = $row['question_status'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("question_id")) <> "")
			$this->question_id->CurrentValue = $this->getKey("question_id"); // question_id
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
		// question_id
		// question_content
		// question_type
		// offer_id
		// question_status

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// question_id
			$this->question_id->ViewValue = $this->question_id->CurrentValue;
			$this->question_id->ViewCustomAttributes = "";

			// question_content
			$this->question_content->ViewValue = $this->question_content->CurrentValue;
			$this->question_content->ViewCustomAttributes = "";

			// question_type
			if (strval($this->question_type->CurrentValue) <> "") {
				switch ($this->question_type->CurrentValue) {
					case $this->question_type->FldTagValue(1):
						$this->question_type->ViewValue = $this->question_type->FldTagCaption(1) <> "" ? $this->question_type->FldTagCaption(1) : $this->question_type->CurrentValue;
						break;
					case $this->question_type->FldTagValue(2):
						$this->question_type->ViewValue = $this->question_type->FldTagCaption(2) <> "" ? $this->question_type->FldTagCaption(2) : $this->question_type->CurrentValue;
						break;
					default:
						$this->question_type->ViewValue = $this->question_type->CurrentValue;
				}
			} else {
				$this->question_type->ViewValue = NULL;
			}
			$this->question_type->ViewCustomAttributes = "";

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

			// question_status
			if (strval($this->question_status->CurrentValue) <> "") {
				switch ($this->question_status->CurrentValue) {
					case $this->question_status->FldTagValue(1):
						$this->question_status->ViewValue = $this->question_status->FldTagCaption(1) <> "" ? $this->question_status->FldTagCaption(1) : $this->question_status->CurrentValue;
						break;
					case $this->question_status->FldTagValue(2):
						$this->question_status->ViewValue = $this->question_status->FldTagCaption(2) <> "" ? $this->question_status->FldTagCaption(2) : $this->question_status->CurrentValue;
						break;
					default:
						$this->question_status->ViewValue = $this->question_status->CurrentValue;
				}
			} else {
				$this->question_status->ViewValue = NULL;
			}
			$this->question_status->ViewCustomAttributes = "";

			// question_content
			$this->question_content->LinkCustomAttributes = "";
			$this->question_content->HrefValue = "";
			$this->question_content->TooltipValue = "";

			// question_type
			$this->question_type->LinkCustomAttributes = "";
			$this->question_type->HrefValue = "";
			$this->question_type->TooltipValue = "";

			// offer_id
			$this->offer_id->LinkCustomAttributes = "";
			$this->offer_id->HrefValue = "";
			$this->offer_id->TooltipValue = "";

			// question_status
			$this->question_status->LinkCustomAttributes = "";
			$this->question_status->HrefValue = "";
			$this->question_status->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// question_content
			$this->question_content->EditCustomAttributes = "";
			$this->question_content->EditValue = $this->question_content->CurrentValue;
			$this->question_content->PlaceHolder = ew_RemoveHtml($this->question_content->FldCaption());

			// question_type
			$this->question_type->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->question_type->FldTagValue(1), $this->question_type->FldTagCaption(1) <> "" ? $this->question_type->FldTagCaption(1) : $this->question_type->FldTagValue(1));
			$arwrk[] = array($this->question_type->FldTagValue(2), $this->question_type->FldTagCaption(2) <> "" ? $this->question_type->FldTagCaption(2) : $this->question_type->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->question_type->EditValue = $arwrk;

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

			// question_status
			$this->question_status->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->question_status->FldTagValue(1), $this->question_status->FldTagCaption(1) <> "" ? $this->question_status->FldTagCaption(1) : $this->question_status->FldTagValue(1));
			$arwrk[] = array($this->question_status->FldTagValue(2), $this->question_status->FldTagCaption(2) <> "" ? $this->question_status->FldTagCaption(2) : $this->question_status->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->question_status->EditValue = $arwrk;

			// Edit refer script
			// question_content

			$this->question_content->HrefValue = "";

			// question_type
			$this->question_type->HrefValue = "";

			// offer_id
			$this->offer_id->HrefValue = "";

			// question_status
			$this->question_status->HrefValue = "";
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
		if (!$this->question_content->FldIsDetailKey && !is_null($this->question_content->FormValue) && $this->question_content->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->question_content->FldCaption());
		}
		if (!$this->question_type->FldIsDetailKey && !is_null($this->question_type->FormValue) && $this->question_type->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->question_type->FldCaption());
		}
		if (!$this->offer_id->FldIsDetailKey && !is_null($this->offer_id->FormValue) && $this->offer_id->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->offer_id->FldCaption());
		}
		if (!$this->question_status->FldIsDetailKey && !is_null($this->question_status->FormValue) && $this->question_status->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->question_status->FldCaption());
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

		// question_content
		$this->question_content->SetDbValueDef($rsnew, $this->question_content->CurrentValue, "", FALSE);

		// question_type
		$this->question_type->SetDbValueDef($rsnew, $this->question_type->CurrentValue, 0, strval($this->question_type->CurrentValue) == "");

		// offer_id
		$this->offer_id->SetDbValueDef($rsnew, $this->offer_id->CurrentValue, 0, FALSE);

		// question_status
		$this->question_status->SetDbValueDef($rsnew, $this->question_status->CurrentValue, 0, FALSE);

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
			$this->question_id->setDbValue($conn->Insert_ID());
			$rsnew['question_id'] = $this->question_id->DbValue;
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
		$Breadcrumb->Add("list", $this->TableVar, "offer_questionslist.php", $this->TableVar, TRUE);
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
if (!isset($offer_questions_add)) $offer_questions_add = new coffer_questions_add();

// Page init
$offer_questions_add->Page_Init();

// Page main
$offer_questions_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$offer_questions_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var offer_questions_add = new ew_Page("offer_questions_add");
offer_questions_add.PageID = "add"; // Page ID
var EW_PAGE_ID = offer_questions_add.PageID; // For backward compatibility

// Form object
var foffer_questionsadd = new ew_Form("foffer_questionsadd");

// Validate form
foffer_questionsadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_question_content");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offer_questions->question_content->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_question_type");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offer_questions->question_type->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_offer_id");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offer_questions->offer_id->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_question_status");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offer_questions->question_status->FldCaption()) ?>");

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
foffer_questionsadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
foffer_questionsadd.ValidateRequired = true;
<?php } else { ?>
foffer_questionsadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
foffer_questionsadd.Lists["x_offer_id"] = {"LinkField":"x_offer_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_offer_title","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $offer_questions_add->ShowPageHeader(); ?>
<?php
$offer_questions_add->ShowMessage();
?>
<form name="foffer_questionsadd" id="foffer_questionsadd" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="offer_questions">
<input type="hidden" name="a_add" id="a_add" value="A">
<table class="ewGrid"><tr><td>
<table id="tbl_offer_questionsadd" class="table table-bordered table-striped">
<?php if ($offer_questions->question_content->Visible) { // question_content ?>
	<tr id="r_question_content">
		<td><span id="elh_offer_questions_question_content"><?php echo $offer_questions->question_content->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offer_questions->question_content->CellAttributes() ?>>
<span id="el_offer_questions_question_content" class="control-group">
<textarea data-field="x_question_content" class="editor" name="x_question_content" id="x_question_content" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($offer_questions->question_content->PlaceHolder) ?>"<?php echo $offer_questions->question_content->EditAttributes() ?>><?php echo $offer_questions->question_content->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("foffer_questionsadd", "x_question_content", 35, 4, <?php echo ($offer_questions->question_content->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
<?php echo $offer_questions->question_content->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offer_questions->question_type->Visible) { // question_type ?>
	<tr id="r_question_type">
		<td><span id="elh_offer_questions_question_type"><?php echo $offer_questions->question_type->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offer_questions->question_type->CellAttributes() ?>>
<span id="el_offer_questions_question_type" class="control-group">
<select data-field="x_question_type" id="x_question_type" name="x_question_type"<?php echo $offer_questions->question_type->EditAttributes() ?>>
<?php
if (is_array($offer_questions->question_type->EditValue)) {
	$arwrk = $offer_questions->question_type->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($offer_questions->question_type->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $offer_questions->question_type->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offer_questions->offer_id->Visible) { // offer_id ?>
	<tr id="r_offer_id">
		<td><span id="elh_offer_questions_offer_id"><?php echo $offer_questions->offer_id->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offer_questions->offer_id->CellAttributes() ?>>
<span id="el_offer_questions_offer_id" class="control-group">
<select data-field="x_offer_id" id="x_offer_id" name="x_offer_id"<?php echo $offer_questions->offer_id->EditAttributes() ?>>
<?php
if (is_array($offer_questions->offer_id->EditValue)) {
	$arwrk = $offer_questions->offer_id->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($offer_questions->offer_id->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
foffer_questionsadd.Lists["x_offer_id"].Options = <?php echo (is_array($offer_questions->offer_id->EditValue)) ? ew_ArrayToJson($offer_questions->offer_id->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $offer_questions->offer_id->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offer_questions->question_status->Visible) { // question_status ?>
	<tr id="r_question_status">
		<td><span id="elh_offer_questions_question_status"><?php echo $offer_questions->question_status->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offer_questions->question_status->CellAttributes() ?>>
<span id="el_offer_questions_question_status" class="control-group">
<select data-field="x_question_status" id="x_question_status" name="x_question_status"<?php echo $offer_questions->question_status->EditAttributes() ?>>
<?php
if (is_array($offer_questions->question_status->EditValue)) {
	$arwrk = $offer_questions->question_status->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($offer_questions->question_status->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $offer_questions->question_status->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
</form>
<script type="text/javascript">
foffer_questionsadd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$offer_questions_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$offer_questions_add->Page_Terminate();
?>
