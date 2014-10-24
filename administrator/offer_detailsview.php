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

$offer_details_view = NULL; // Initialize page object first

class coffer_details_view extends coffer_details {

	// Page ID
	var $PageID = 'view';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'offer_details';

	// Page object name
	var $PageObjName = 'offer_details_view';

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

	// Page URLs
	var $AddUrl;
	var $EditUrl;
	var $CopyUrl;
	var $DeleteUrl;
	var $ViewUrl;
	var $ListUrl;

	// Export URLs
	var $ExportPrintUrl;
	var $ExportHtmlUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportXmlUrl;
	var $ExportCsvUrl;
	var $ExportPdfUrl;

	// Update URLs
	var $InlineAddUrl;
	var $InlineCopyUrl;
	var $InlineEditUrl;
	var $GridAddUrl;
	var $GridEditUrl;
	var $MultiDeleteUrl;
	var $MultiUpdateUrl;

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
		$KeyUrl = "";
		if (@$_GET["offer_detail_id"] <> "") {
			$this->RecKey["offer_detail_id"] = $_GET["offer_detail_id"];
			$KeyUrl .= "&amp;offer_detail_id=" . urlencode($this->RecKey["offer_detail_id"]);
		}
		$this->ExportPrintUrl = $this->PageUrl() . "export=print" . $KeyUrl;
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html" . $KeyUrl;
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel" . $KeyUrl;
		$this->ExportWordUrl = $this->PageUrl() . "export=word" . $KeyUrl;
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml" . $KeyUrl;
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv" . $KeyUrl;
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf" . $KeyUrl;

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'view', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'offer_details', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "div";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "div";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
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
		if (!$Security->CanView()) {
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
	var $ExportOptions; // Export options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 1;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $RecCnt;
	var $RecKey = array();
	var $Recordset;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;
		$sReturnUrl = "";
		$bMatchRecord = FALSE;

		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET["offer_detail_id"] <> "") {
				$this->offer_detail_id->setQueryStringValue($_GET["offer_detail_id"]);
				$this->RecKey["offer_detail_id"] = $this->offer_detail_id->QueryStringValue;
			} else {
				$sReturnUrl = "offer_detailslist.php"; // Return to list
			}

			// Get action
			$this->CurrentAction = "I"; // Display form
			switch ($this->CurrentAction) {
				case "I": // Get a record to display
					if (!$this->LoadRow()) { // Load record based on key
						if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
						$sReturnUrl = "offer_detailslist.php"; // No matching record, return to list
					}
			}
		} else {
			$sReturnUrl = "offer_detailslist.php"; // Not page request, return to list
		}
		if ($sReturnUrl <> "")
			$this->Page_Terminate($sReturnUrl);

		// Render row
		$this->RowType = EW_ROWTYPE_VIEW;
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = &$options["action"];

		// Add
		$item = &$option->Add("add");
		$item->Body = "<a class=\"ewAction ewAdd\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . $Language->Phrase("ViewPageAddLink") . "</a>";
		$item->Visible = ($this->AddUrl <> "" && $Security->CanAdd());

		// Edit
		$item = &$option->Add("edit");
		$item->Body = "<a class=\"ewAction ewEdit\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("ViewPageEditLink") . "</a>";
		$item->Visible = ($this->EditUrl <> "" && $Security->CanEdit());

		// Copy
		$item = &$option->Add("copy");
		$item->Body = "<a class=\"ewAction ewCopy\" href=\"" . ew_HtmlEncode($this->CopyUrl) . "\">" . $Language->Phrase("ViewPageCopyLink") . "</a>";
		$item->Visible = ($this->CopyUrl <> "" && $Security->CanAdd());

		// Delete
		$item = &$option->Add("delete");
		$item->Body = "<a class=\"ewAction ewDelete\" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" . $Language->Phrase("ViewPageDeleteLink") . "</a>";
		$item->Visible = ($this->DeleteUrl <> "" && $Security->CanDelete());

		// Set up options default
		foreach ($options as &$option) {
			$option->UseDropDownButton = FALSE;
			$option->UseButtonGroup = TRUE;
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");
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
		$this->AddUrl = $this->GetAddUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();
		$this->ListUrl = $this->GetListUrl();
		$this->SetupOtherOptions();

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

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "offer_detailslist.php", $this->TableVar, TRUE);
		$PageId = "view";
		$Breadcrumb->Add("view", $PageId, ew_CurrentUrl());
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
if (!isset($offer_details_view)) $offer_details_view = new coffer_details_view();

// Page init
$offer_details_view->Page_Init();

// Page main
$offer_details_view->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$offer_details_view->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var offer_details_view = new ew_Page("offer_details_view");
offer_details_view.PageID = "view"; // Page ID
var EW_PAGE_ID = offer_details_view.PageID; // For backward compatibility

// Form object
var foffer_detailsview = new ew_Form("foffer_detailsview");

// Form_CustomValidate event
foffer_detailsview.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
foffer_detailsview.ValidateRequired = true;
<?php } else { ?>
foffer_detailsview.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
foffer_detailsview.Lists["x_offer_id"] = {"LinkField":"x_offer_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_offer_title","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<div class="ewViewExportOptions">
<?php $offer_details_view->ExportOptions->Render("body") ?>
<?php if (!$offer_details_view->ExportOptions->UseDropDownButton) { ?>
</div>
<div class="ewViewOtherOptions">
<?php } ?>
<?php
	foreach ($offer_details_view->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<?php $offer_details_view->ShowPageHeader(); ?>
<?php
$offer_details_view->ShowMessage();
?>
<form name="foffer_detailsview" id="foffer_detailsview" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="offer_details">
<table class="ewGrid"><tr><td>
<table id="tbl_offer_detailsview" class="table table-bordered table-striped">
<?php if ($offer_details->offer_detail_id->Visible) { // offer_detail_id ?>
	<tr id="r_offer_detail_id">
		<td><span id="elh_offer_details_offer_detail_id"><?php echo $offer_details->offer_detail_id->FldCaption() ?></span></td>
		<td<?php echo $offer_details->offer_detail_id->CellAttributes() ?>>
<span id="el_offer_details_offer_detail_id" class="control-group">
<span<?php echo $offer_details->offer_detail_id->ViewAttributes() ?>>
<?php echo $offer_details->offer_detail_id->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($offer_details->offer_id->Visible) { // offer_id ?>
	<tr id="r_offer_id">
		<td><span id="elh_offer_details_offer_id"><?php echo $offer_details->offer_id->FldCaption() ?></span></td>
		<td<?php echo $offer_details->offer_id->CellAttributes() ?>>
<span id="el_offer_details_offer_id" class="control-group">
<span<?php echo $offer_details->offer_id->ViewAttributes() ?>>
<?php echo $offer_details->offer_id->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($offer_details->offer_top_image->Visible) { // offer_top_image ?>
	<tr id="r_offer_top_image">
		<td><span id="elh_offer_details_offer_top_image"><?php echo $offer_details->offer_top_image->FldCaption() ?></span></td>
		<td<?php echo $offer_details->offer_top_image->CellAttributes() ?>>
<span id="el_offer_details_offer_top_image" class="control-group">
<span>
<?php if ($offer_details->offer_top_image->LinkAttributes() <> "") { ?>
<?php if (!empty($offer_details->offer_top_image->Upload->DbValue)) { ?>
<?php echo ew_GetFileViewTag($offer_details->offer_top_image, $offer_details->offer_top_image->ViewValue) ?>
<?php } elseif (!in_array($offer_details->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($offer_details->offer_top_image->Upload->DbValue)) { ?>
<?php echo ew_GetFileViewTag($offer_details->offer_top_image, $offer_details->offer_top_image->ViewValue) ?>
<?php } elseif (!in_array($offer_details->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($offer_details->offer_bottom_image->Visible) { // offer_bottom_image ?>
	<tr id="r_offer_bottom_image">
		<td><span id="elh_offer_details_offer_bottom_image"><?php echo $offer_details->offer_bottom_image->FldCaption() ?></span></td>
		<td<?php echo $offer_details->offer_bottom_image->CellAttributes() ?>>
<span id="el_offer_details_offer_bottom_image" class="control-group">
<span>
<?php if ($offer_details->offer_bottom_image->LinkAttributes() <> "") { ?>
<?php if (!empty($offer_details->offer_bottom_image->Upload->DbValue)) { ?>
<?php echo ew_GetFileViewTag($offer_details->offer_bottom_image, $offer_details->offer_bottom_image->ViewValue) ?>
<?php } elseif (!in_array($offer_details->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($offer_details->offer_bottom_image->Upload->DbValue)) { ?>
<?php echo ew_GetFileViewTag($offer_details->offer_bottom_image, $offer_details->offer_bottom_image->ViewValue) ?>
<?php } elseif (!in_array($offer_details->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($offer_details->offer_start_date->Visible) { // offer_start_date ?>
	<tr id="r_offer_start_date">
		<td><span id="elh_offer_details_offer_start_date"><?php echo $offer_details->offer_start_date->FldCaption() ?></span></td>
		<td<?php echo $offer_details->offer_start_date->CellAttributes() ?>>
<span id="el_offer_details_offer_start_date" class="control-group">
<span<?php echo $offer_details->offer_start_date->ViewAttributes() ?>>
<?php echo $offer_details->offer_start_date->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($offer_details->offer_end_date->Visible) { // offer_end_date ?>
	<tr id="r_offer_end_date">
		<td><span id="elh_offer_details_offer_end_date"><?php echo $offer_details->offer_end_date->FldCaption() ?></span></td>
		<td<?php echo $offer_details->offer_end_date->CellAttributes() ?>>
<span id="el_offer_details_offer_end_date" class="control-group">
<span<?php echo $offer_details->offer_end_date->ViewAttributes() ?>>
<?php echo $offer_details->offer_end_date->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($offer_details->offer_start_time->Visible) { // offer_start_time ?>
	<tr id="r_offer_start_time">
		<td><span id="elh_offer_details_offer_start_time"><?php echo $offer_details->offer_start_time->FldCaption() ?></span></td>
		<td<?php echo $offer_details->offer_start_time->CellAttributes() ?>>
<span id="el_offer_details_offer_start_time" class="control-group">
<span<?php echo $offer_details->offer_start_time->ViewAttributes() ?>>
<?php echo $offer_details->offer_start_time->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($offer_details->offer_end_time->Visible) { // offer_end_time ?>
	<tr id="r_offer_end_time">
		<td><span id="elh_offer_details_offer_end_time"><?php echo $offer_details->offer_end_time->FldCaption() ?></span></td>
		<td<?php echo $offer_details->offer_end_time->CellAttributes() ?>>
<span id="el_offer_details_offer_end_time" class="control-group">
<span<?php echo $offer_details->offer_end_time->ViewAttributes() ?>>
<?php echo $offer_details->offer_end_time->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($offer_details->offer_rules->Visible) { // offer_rules ?>
	<tr id="r_offer_rules">
		<td><span id="elh_offer_details_offer_rules"><?php echo $offer_details->offer_rules->FldCaption() ?></span></td>
		<td<?php echo $offer_details->offer_rules->CellAttributes() ?>>
<span id="el_offer_details_offer_rules" class="control-group">
<span<?php echo $offer_details->offer_rules->ViewAttributes() ?>>
<?php echo $offer_details->offer_rules->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($offer_details->offer_value->Visible) { // offer_value ?>
	<tr id="r_offer_value">
		<td><span id="elh_offer_details_offer_value"><?php echo $offer_details->offer_value->FldCaption() ?></span></td>
		<td<?php echo $offer_details->offer_value->CellAttributes() ?>>
<span id="el_offer_details_offer_value" class="control-group">
<span<?php echo $offer_details->offer_value->ViewAttributes() ?>>
<?php echo $offer_details->offer_value->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
</form>
<script type="text/javascript">
foffer_detailsview.Init();
</script>
<?php
$offer_details_view->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$offer_details_view->Page_Terminate();
?>
