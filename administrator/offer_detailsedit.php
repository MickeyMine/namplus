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

$offer_details_edit = NULL; // Initialize page object first

class coffer_details_edit extends coffer_details {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'offer_details';

	// Page object name
	var $PageObjName = 'offer_details_edit';

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
			define("EW_PAGE_ID", 'edit', TRUE);

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
		if (!$Security->CanEdit()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("offer_detailslist.php");
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();

		// Create form object
		$objForm = new cFormObj();
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
	var $DbMasterFilter;
	var $DbDetailFilter;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Load key from QueryString
		if (@$_GET["offer_detail_id"] <> "") {
			$this->offer_detail_id->setQueryStringValue($_GET["offer_detail_id"]);
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->offer_detail_id->CurrentValue == "")
			$this->Page_Terminate("offer_detailslist.php"); // Invalid key, return to list

		// Validate form if post back
		if (@$_POST["a_edit"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = ""; // Form error, reset action
				$this->setFailureMessage($gsFormError);
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues();
			}
		}
		switch ($this->CurrentAction) {
			case "I": // Get a record to display
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("offer_detailslist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					$sReturnUrl = $this->getReturnUrl();
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed
				}
		}

		// Render the record
		$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm;

		// Get upload data
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

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
		if (!$this->offer_detail_id->FldIsDetailKey)
			$this->offer_detail_id->setFormValue($objForm->GetValue("x_offer_detail_id"));
		if (!$this->offer_id->FldIsDetailKey) {
			$this->offer_id->setFormValue($objForm->GetValue("x_offer_id"));
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
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->offer_detail_id->CurrentValue = $this->offer_detail_id->FormValue;
		$this->offer_id->CurrentValue = $this->offer_id->FormValue;
		$this->offer_start_date->CurrentValue = $this->offer_start_date->FormValue;
		$this->offer_start_date->CurrentValue = ew_UnFormatDateTime($this->offer_start_date->CurrentValue, 7);
		$this->offer_end_date->CurrentValue = $this->offer_end_date->FormValue;
		$this->offer_end_date->CurrentValue = ew_UnFormatDateTime($this->offer_end_date->CurrentValue, 7);
		$this->offer_start_time->CurrentValue = $this->offer_start_time->FormValue;
		$this->offer_end_time->CurrentValue = $this->offer_end_time->FormValue;
		$this->offer_rules->CurrentValue = $this->offer_rules->FormValue;
		$this->offer_value->CurrentValue = $this->offer_value->FormValue;
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
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// offer_detail_id
			$this->offer_detail_id->EditCustomAttributes = "";
			$this->offer_detail_id->EditValue = $this->offer_detail_id->CurrentValue;
			$this->offer_detail_id->ViewCustomAttributes = "";

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
			if ($this->CurrentAction == "I" && !$this->EventCancelled) ew_RenderUploadField($this->offer_top_image);

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
			if ($this->CurrentAction == "I" && !$this->EventCancelled) ew_RenderUploadField($this->offer_bottom_image);

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
			$this->offer_rules->EditValue = ew_HtmlEncode($this->offer_rules->CurrentValue);
			$this->offer_rules->PlaceHolder = ew_RemoveHtml($this->offer_rules->FldCaption());

			// offer_value
			$this->offer_value->EditCustomAttributes = "";
			$this->offer_value->EditValue = ew_HtmlEncode($this->offer_value->CurrentValue);
			$this->offer_value->PlaceHolder = ew_RemoveHtml($this->offer_value->FldCaption());

			// Edit refer script
			// offer_detail_id

			$this->offer_detail_id->HrefValue = "";

			// offer_id
			$this->offer_id->HrefValue = "";

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

	// Update record based on key values
	function EditRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$EditRow = FALSE; // Update Failed
		} else {

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// offer_id
			$this->offer_id->SetDbValueDef($rsnew, $this->offer_id->CurrentValue, 0, $this->offer_id->ReadOnly);

			// offer_top_image
			if (!($this->offer_top_image->ReadOnly) && !$this->offer_top_image->Upload->KeepFile) {
				$this->offer_top_image->Upload->DbValue = $rs->fields('offer_top_image'); // Get original value
				if ($this->offer_top_image->Upload->FileName == "") {
					$rsnew['offer_top_image'] = NULL;
				} else {
					$rsnew['offer_top_image'] = $this->offer_top_image->Upload->FileName;
				}
			}

			// offer_bottom_image
			if (!($this->offer_bottom_image->ReadOnly) && !$this->offer_bottom_image->Upload->KeepFile) {
				$this->offer_bottom_image->Upload->DbValue = $rs->fields('offer_bottom_image'); // Get original value
				if ($this->offer_bottom_image->Upload->FileName == "") {
					$rsnew['offer_bottom_image'] = NULL;
				} else {
					$rsnew['offer_bottom_image'] = $this->offer_bottom_image->Upload->FileName;
				}
			}

			// offer_start_date
			$this->offer_start_date->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->offer_start_date->CurrentValue, 7), ew_CurrentDate(), $this->offer_start_date->ReadOnly);

			// offer_end_date
			$this->offer_end_date->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->offer_end_date->CurrentValue, 7), ew_CurrentDate(), $this->offer_end_date->ReadOnly);

			// offer_start_time
			$this->offer_start_time->SetDbValueDef($rsnew, $this->offer_start_time->CurrentValue, ew_CurrentTime(), $this->offer_start_time->ReadOnly);

			// offer_end_time
			$this->offer_end_time->SetDbValueDef($rsnew, $this->offer_end_time->CurrentValue, ew_CurrentTime(), $this->offer_end_time->ReadOnly);

			// offer_rules
			$this->offer_rules->SetDbValueDef($rsnew, $this->offer_rules->CurrentValue, "", $this->offer_rules->ReadOnly);

			// offer_value
			$this->offer_value->SetDbValueDef($rsnew, $this->offer_value->CurrentValue, "", $this->offer_value->ReadOnly);
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

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = 'ew_ErrorFn';
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew, "", $rsold);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
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
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		$rs->Close();

		// offer_top_image
		ew_CleanUploadTempPath($this->offer_top_image, $this->offer_top_image->Upload->Index);

		// offer_bottom_image
		ew_CleanUploadTempPath($this->offer_bottom_image, $this->offer_bottom_image->Upload->Index);
		return $EditRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "offer_detailslist.php", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, ew_CurrentUrl());
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
if (!isset($offer_details_edit)) $offer_details_edit = new coffer_details_edit();

// Page init
$offer_details_edit->Page_Init();

// Page main
$offer_details_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$offer_details_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var offer_details_edit = new ew_Page("offer_details_edit");
offer_details_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = offer_details_edit.PageID; // For backward compatibility

// Form object
var foffer_detailsedit = new ew_Form("foffer_detailsedit");

// Validate form
foffer_detailsedit.Validate = function() {
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
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offer_details->offer_id->FldCaption()) ?>");
			felm = this.GetElements("x" + infix + "_offer_top_image");
			elm = this.GetElements("fn_x" + infix + "_offer_top_image");
			if (felm && elm && !ew_HasValue(elm))
				return this.OnError(felm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offer_details->offer_top_image->FldCaption()) ?>");
			felm = this.GetElements("x" + infix + "_offer_bottom_image");
			elm = this.GetElements("fn_x" + infix + "_offer_bottom_image");
			if (felm && elm && !ew_HasValue(elm))
				return this.OnError(felm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offer_details->offer_bottom_image->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_offer_start_date");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offer_details->offer_start_date->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_offer_start_date");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($offer_details->offer_start_date->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_offer_end_date");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offer_details->offer_end_date->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_offer_end_date");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($offer_details->offer_end_date->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_offer_start_time");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offer_details->offer_start_time->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_offer_start_time");
			if (elm && !ew_CheckTime(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($offer_details->offer_start_time->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_offer_end_time");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offer_details->offer_end_time->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_offer_end_time");
			if (elm && !ew_CheckTime(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($offer_details->offer_end_time->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_offer_rules");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offer_details->offer_rules->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_offer_value");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($offer_details->offer_value->FldCaption()) ?>");

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
foffer_detailsedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
foffer_detailsedit.ValidateRequired = true;
<?php } else { ?>
foffer_detailsedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
foffer_detailsedit.Lists["x_offer_id"] = {"LinkField":"x_offer_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_offer_title","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $offer_details_edit->ShowPageHeader(); ?>
<?php
$offer_details_edit->ShowMessage();
?>
<form name="foffer_detailsedit" id="foffer_detailsedit" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="offer_details">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table class="ewGrid"><tr><td>
<table id="tbl_offer_detailsedit" class="table table-bordered table-striped">
<?php if ($offer_details->offer_detail_id->Visible) { // offer_detail_id ?>
	<tr id="r_offer_detail_id">
		<td><span id="elh_offer_details_offer_detail_id"><?php echo $offer_details->offer_detail_id->FldCaption() ?></span></td>
		<td<?php echo $offer_details->offer_detail_id->CellAttributes() ?>>
<span id="el_offer_details_offer_detail_id" class="control-group">
<span<?php echo $offer_details->offer_detail_id->ViewAttributes() ?>>
<?php echo $offer_details->offer_detail_id->EditValue ?></span>
</span>
<input type="hidden" data-field="x_offer_detail_id" name="x_offer_detail_id" id="x_offer_detail_id" value="<?php echo ew_HtmlEncode($offer_details->offer_detail_id->CurrentValue) ?>">
<?php echo $offer_details->offer_detail_id->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offer_details->offer_id->Visible) { // offer_id ?>
	<tr id="r_offer_id">
		<td><span id="elh_offer_details_offer_id"><?php echo $offer_details->offer_id->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offer_details->offer_id->CellAttributes() ?>>
<span id="el_offer_details_offer_id" class="control-group">
<select data-field="x_offer_id" id="x_offer_id" name="x_offer_id"<?php echo $offer_details->offer_id->EditAttributes() ?>>
<?php
if (is_array($offer_details->offer_id->EditValue)) {
	$arwrk = $offer_details->offer_id->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($offer_details->offer_id->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
foffer_detailsedit.Lists["x_offer_id"].Options = <?php echo (is_array($offer_details->offer_id->EditValue)) ? ew_ArrayToJson($offer_details->offer_id->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $offer_details->offer_id->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offer_details->offer_top_image->Visible) { // offer_top_image ?>
	<tr id="r_offer_top_image">
		<td><span id="elh_offer_details_offer_top_image"><?php echo $offer_details->offer_top_image->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offer_details->offer_top_image->CellAttributes() ?>>
<span id="el_offer_details_offer_top_image" class="control-group">
<span id="fd_x_offer_top_image">
<span class="btn btn-small fileinput-button"<?php if ($offer_details->offer_top_image->ReadOnly || $offer_details->offer_top_image->Disabled) echo " style=\"display: none;\""; ?>>
	<span><?php echo $Language->Phrase("ChooseFile") ?></span>
	<input type="file" data-field="x_offer_top_image" name="x_offer_top_image" id="x_offer_top_image">
</span>
<input type="hidden" name="fn_x_offer_top_image" id= "fn_x_offer_top_image" value="<?php echo $offer_details->offer_top_image->Upload->FileName ?>">
<?php if (@$_POST["fa_x_offer_top_image"] == "0") { ?>
<input type="hidden" name="fa_x_offer_top_image" id= "fa_x_offer_top_image" value="0">
<?php } else { ?>
<input type="hidden" name="fa_x_offer_top_image" id= "fa_x_offer_top_image" value="1">
<?php } ?>
<input type="hidden" name="fs_x_offer_top_image" id= "fs_x_offer_top_image" value="150">
</span>
<table id="ft_x_offer_top_image" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $offer_details->offer_top_image->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offer_details->offer_bottom_image->Visible) { // offer_bottom_image ?>
	<tr id="r_offer_bottom_image">
		<td><span id="elh_offer_details_offer_bottom_image"><?php echo $offer_details->offer_bottom_image->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offer_details->offer_bottom_image->CellAttributes() ?>>
<span id="el_offer_details_offer_bottom_image" class="control-group">
<span id="fd_x_offer_bottom_image">
<span class="btn btn-small fileinput-button"<?php if ($offer_details->offer_bottom_image->ReadOnly || $offer_details->offer_bottom_image->Disabled) echo " style=\"display: none;\""; ?>>
	<span><?php echo $Language->Phrase("ChooseFile") ?></span>
	<input type="file" data-field="x_offer_bottom_image" name="x_offer_bottom_image" id="x_offer_bottom_image">
</span>
<input type="hidden" name="fn_x_offer_bottom_image" id= "fn_x_offer_bottom_image" value="<?php echo $offer_details->offer_bottom_image->Upload->FileName ?>">
<?php if (@$_POST["fa_x_offer_bottom_image"] == "0") { ?>
<input type="hidden" name="fa_x_offer_bottom_image" id= "fa_x_offer_bottom_image" value="0">
<?php } else { ?>
<input type="hidden" name="fa_x_offer_bottom_image" id= "fa_x_offer_bottom_image" value="1">
<?php } ?>
<input type="hidden" name="fs_x_offer_bottom_image" id= "fs_x_offer_bottom_image" value="150">
</span>
<table id="ft_x_offer_bottom_image" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $offer_details->offer_bottom_image->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offer_details->offer_start_date->Visible) { // offer_start_date ?>
	<tr id="r_offer_start_date">
		<td><span id="elh_offer_details_offer_start_date"><?php echo $offer_details->offer_start_date->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offer_details->offer_start_date->CellAttributes() ?>>
<span id="el_offer_details_offer_start_date" class="control-group">
<input type="text" data-field="x_offer_start_date" name="x_offer_start_date" id="x_offer_start_date" placeholder="<?php echo ew_HtmlEncode($offer_details->offer_start_date->PlaceHolder) ?>" value="<?php echo $offer_details->offer_start_date->EditValue ?>"<?php echo $offer_details->offer_start_date->EditAttributes() ?>>
<?php if (!$offer_details->offer_start_date->ReadOnly && !$offer_details->offer_start_date->Disabled && @$offer_details->offer_start_date->EditAttrs["readonly"] == "" && @$offer_details->offer_start_date->EditAttrs["disabled"] == "") { ?>
<button id="cal_x_offer_start_date" name="cal_x_offer_start_date" class="btn" type="button"><img src="phpimages/calendar.png" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" style="border: 0;"></button><script type="text/javascript">
ew_CreateCalendar("foffer_detailsedit", "x_offer_start_date", "%d/%m/%Y");
</script>
<?php } ?>
</span>
<?php echo $offer_details->offer_start_date->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offer_details->offer_end_date->Visible) { // offer_end_date ?>
	<tr id="r_offer_end_date">
		<td><span id="elh_offer_details_offer_end_date"><?php echo $offer_details->offer_end_date->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offer_details->offer_end_date->CellAttributes() ?>>
<span id="el_offer_details_offer_end_date" class="control-group">
<input type="text" data-field="x_offer_end_date" name="x_offer_end_date" id="x_offer_end_date" placeholder="<?php echo ew_HtmlEncode($offer_details->offer_end_date->PlaceHolder) ?>" value="<?php echo $offer_details->offer_end_date->EditValue ?>"<?php echo $offer_details->offer_end_date->EditAttributes() ?>>
<?php if (!$offer_details->offer_end_date->ReadOnly && !$offer_details->offer_end_date->Disabled && @$offer_details->offer_end_date->EditAttrs["readonly"] == "" && @$offer_details->offer_end_date->EditAttrs["disabled"] == "") { ?>
<button id="cal_x_offer_end_date" name="cal_x_offer_end_date" class="btn" type="button"><img src="phpimages/calendar.png" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" style="border: 0;"></button><script type="text/javascript">
ew_CreateCalendar("foffer_detailsedit", "x_offer_end_date", "%d/%m/%Y");
</script>
<?php } ?>
</span>
<?php echo $offer_details->offer_end_date->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offer_details->offer_start_time->Visible) { // offer_start_time ?>
	<tr id="r_offer_start_time">
		<td><span id="elh_offer_details_offer_start_time"><?php echo $offer_details->offer_start_time->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offer_details->offer_start_time->CellAttributes() ?>>
<span id="el_offer_details_offer_start_time" class="control-group">
<input type="text" data-field="x_offer_start_time" name="x_offer_start_time" id="x_offer_start_time" size="30" placeholder="<?php echo ew_HtmlEncode($offer_details->offer_start_time->PlaceHolder) ?>" value="<?php echo $offer_details->offer_start_time->EditValue ?>"<?php echo $offer_details->offer_start_time->EditAttributes() ?>>
</span>
<?php echo $offer_details->offer_start_time->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offer_details->offer_end_time->Visible) { // offer_end_time ?>
	<tr id="r_offer_end_time">
		<td><span id="elh_offer_details_offer_end_time"><?php echo $offer_details->offer_end_time->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offer_details->offer_end_time->CellAttributes() ?>>
<span id="el_offer_details_offer_end_time" class="control-group">
<input type="text" data-field="x_offer_end_time" name="x_offer_end_time" id="x_offer_end_time" size="30" placeholder="<?php echo ew_HtmlEncode($offer_details->offer_end_time->PlaceHolder) ?>" value="<?php echo $offer_details->offer_end_time->EditValue ?>"<?php echo $offer_details->offer_end_time->EditAttributes() ?>>
</span>
<?php echo $offer_details->offer_end_time->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offer_details->offer_rules->Visible) { // offer_rules ?>
	<tr id="r_offer_rules">
		<td><span id="elh_offer_details_offer_rules"><?php echo $offer_details->offer_rules->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offer_details->offer_rules->CellAttributes() ?>>
<span id="el_offer_details_offer_rules" class="control-group">
<input type="text" data-field="x_offer_rules" name="x_offer_rules" id="x_offer_rules" size="30" maxlength="150" placeholder="<?php echo ew_HtmlEncode($offer_details->offer_rules->PlaceHolder) ?>" value="<?php echo $offer_details->offer_rules->EditValue ?>"<?php echo $offer_details->offer_rules->EditAttributes() ?>>
</span>
<?php echo $offer_details->offer_rules->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($offer_details->offer_value->Visible) { // offer_value ?>
	<tr id="r_offer_value">
		<td><span id="elh_offer_details_offer_value"><?php echo $offer_details->offer_value->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $offer_details->offer_value->CellAttributes() ?>>
<span id="el_offer_details_offer_value" class="control-group">
<input type="text" data-field="x_offer_value" name="x_offer_value" id="x_offer_value" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($offer_details->offer_value->PlaceHolder) ?>" value="<?php echo $offer_details->offer_value->EditValue ?>"<?php echo $offer_details->offer_value->EditAttributes() ?>>
</span>
<?php echo $offer_details->offer_value->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("EditBtn") ?></button>
</form>
<script type="text/javascript">
foffer_detailsedit.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$offer_details_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$offer_details_edit->Page_Terminate();
?>
