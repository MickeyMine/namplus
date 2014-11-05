<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "newsinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$news_view = NULL; // Initialize page object first

class cnews_view extends cnews {

	// Page ID
	var $PageID = 'view';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'news';

	// Page object name
	var $PageObjName = 'news_view';

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

		// Table object (news)
		if (!isset($GLOBALS["news"]) || get_class($GLOBALS["news"]) == "cnews") {
			$GLOBALS["news"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["news"];
		}
		$KeyUrl = "";
		if (@$_GET["new_id"] <> "") {
			$this->RecKey["new_id"] = $_GET["new_id"];
			$KeyUrl .= "&amp;new_id=" . urlencode($this->RecKey["new_id"]);
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
			define("EW_TABLE_NAME", 'news', TRUE);

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
			$this->Page_Terminate("newslist.php");
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->new_id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
			if (@$_GET["new_id"] <> "") {
				$this->new_id->setQueryStringValue($_GET["new_id"]);
				$this->RecKey["new_id"] = $this->new_id->QueryStringValue;
			} else {
				$sReturnUrl = "newslist.php"; // Return to list
			}

			// Get action
			$this->CurrentAction = "I"; // Display form
			switch ($this->CurrentAction) {
				case "I": // Get a record to display
					if (!$this->LoadRow()) { // Load record based on key
						if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
						$sReturnUrl = "newslist.php"; // No matching record, return to list
					}
			}
		} else {
			$sReturnUrl = "newslist.php"; // Not page request, return to list
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
		$this->new_id->setDbValue($rs->fields('new_id'));
		$this->new_title->setDbValue($rs->fields('new_title'));
		$this->new_description->setDbValue($rs->fields('new_description'));
		$this->new_content->setDbValue($rs->fields('new_content'));
		$this->new_type->setDbValue($rs->fields('new_type'));
		$this->new_img_path->Upload->DbValue = $rs->fields('new_img_path');
		$this->new_img_path->CurrentValue = $this->new_img_path->Upload->DbValue;
		$this->new_publish_date->setDbValue($rs->fields('new_publish_date'));
		$this->new_cat_id->setDbValue($rs->fields('new_cat_id'));
		$this->new_link_id->setDbValue($rs->fields('new_link_id'));
		$this->new_link_order->setDbValue($rs->fields('new_link_order'));
		$this->new_status->setDbValue($rs->fields('new_status'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->new_id->DbValue = $row['new_id'];
		$this->new_title->DbValue = $row['new_title'];
		$this->new_description->DbValue = $row['new_description'];
		$this->new_content->DbValue = $row['new_content'];
		$this->new_type->DbValue = $row['new_type'];
		$this->new_img_path->Upload->DbValue = $row['new_img_path'];
		$this->new_publish_date->DbValue = $row['new_publish_date'];
		$this->new_cat_id->DbValue = $row['new_cat_id'];
		$this->new_link_id->DbValue = $row['new_link_id'];
		$this->new_link_order->DbValue = $row['new_link_order'];
		$this->new_status->DbValue = $row['new_status'];
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
		// new_id
		// new_title
		// new_description
		// new_content
		// new_type
		// new_img_path
		// new_publish_date
		// new_cat_id
		// new_link_id
		// new_link_order
		// new_status

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// new_id
			$this->new_id->ViewValue = $this->new_id->CurrentValue;
			$this->new_id->ViewCustomAttributes = "";

			// new_title
			$this->new_title->ViewValue = $this->new_title->CurrentValue;
			$this->new_title->ViewCustomAttributes = "";

			// new_description
			$this->new_description->ViewValue = $this->new_description->CurrentValue;
			$this->new_description->ViewCustomAttributes = "";

			// new_content
			$this->new_content->ViewValue = $this->new_content->CurrentValue;
			$this->new_content->ViewCustomAttributes = "";

			// new_type
			if (strval($this->new_type->CurrentValue) <> "") {
				switch ($this->new_type->CurrentValue) {
					case $this->new_type->FldTagValue(1):
						$this->new_type->ViewValue = $this->new_type->FldTagCaption(1) <> "" ? $this->new_type->FldTagCaption(1) : $this->new_type->CurrentValue;
						break;
					case $this->new_type->FldTagValue(2):
						$this->new_type->ViewValue = $this->new_type->FldTagCaption(2) <> "" ? $this->new_type->FldTagCaption(2) : $this->new_type->CurrentValue;
						break;
					case $this->new_type->FldTagValue(3):
						$this->new_type->ViewValue = $this->new_type->FldTagCaption(3) <> "" ? $this->new_type->FldTagCaption(3) : $this->new_type->CurrentValue;
						break;
					case $this->new_type->FldTagValue(4):
						$this->new_type->ViewValue = $this->new_type->FldTagCaption(4) <> "" ? $this->new_type->FldTagCaption(4) : $this->new_type->CurrentValue;
						break;
					default:
						$this->new_type->ViewValue = $this->new_type->CurrentValue;
				}
			} else {
				$this->new_type->ViewValue = NULL;
			}
			$this->new_type->ViewCustomAttributes = "";

			// new_img_path
			if (!ew_Empty($this->new_img_path->Upload->DbValue)) {
				$this->new_img_path->ViewValue = $this->new_img_path->Upload->DbValue;
			} else {
				$this->new_img_path->ViewValue = "";
			}
			$this->new_img_path->ViewCustomAttributes = "";

			// new_publish_date
			$this->new_publish_date->ViewValue = $this->new_publish_date->CurrentValue;
			$this->new_publish_date->ViewValue = ew_FormatDateTime($this->new_publish_date->ViewValue, 7);
			$this->new_publish_date->ViewCustomAttributes = "";

			// new_cat_id
			if (strval($this->new_cat_id->CurrentValue) <> "") {
				$sFilterWrk = "`cat_id`" . ew_SearchString("=", $this->new_cat_id->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `cat_id`, `cat_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `viewnews`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->new_cat_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->new_cat_id->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->new_cat_id->ViewValue = $this->new_cat_id->CurrentValue;
				}
			} else {
				$this->new_cat_id->ViewValue = NULL;
			}
			$this->new_cat_id->ViewCustomAttributes = "";

			// new_link_id
			if (strval($this->new_link_id->CurrentValue) <> "") {
				$sFilterWrk = "`new_id`" . ew_SearchString("=", $this->new_link_id->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `new_id`, `new_title` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `viewnewstops`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->new_link_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->new_link_id->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->new_link_id->ViewValue = $this->new_link_id->CurrentValue;
				}
			} else {
				$this->new_link_id->ViewValue = NULL;
			}
			$this->new_link_id->ViewCustomAttributes = "";

			// new_link_order
			$this->new_link_order->ViewValue = $this->new_link_order->CurrentValue;
			$this->new_link_order->ViewCustomAttributes = "";

			// new_status
			if (strval($this->new_status->CurrentValue) <> "") {
				switch ($this->new_status->CurrentValue) {
					case $this->new_status->FldTagValue(1):
						$this->new_status->ViewValue = $this->new_status->FldTagCaption(1) <> "" ? $this->new_status->FldTagCaption(1) : $this->new_status->CurrentValue;
						break;
					case $this->new_status->FldTagValue(2):
						$this->new_status->ViewValue = $this->new_status->FldTagCaption(2) <> "" ? $this->new_status->FldTagCaption(2) : $this->new_status->CurrentValue;
						break;
					default:
						$this->new_status->ViewValue = $this->new_status->CurrentValue;
				}
			} else {
				$this->new_status->ViewValue = NULL;
			}
			$this->new_status->ViewCustomAttributes = "";

			// new_id
			$this->new_id->LinkCustomAttributes = "";
			$this->new_id->HrefValue = "";
			$this->new_id->TooltipValue = "";

			// new_title
			$this->new_title->LinkCustomAttributes = "";
			$this->new_title->HrefValue = "";
			$this->new_title->TooltipValue = "";

			// new_description
			$this->new_description->LinkCustomAttributes = "";
			$this->new_description->HrefValue = "";
			$this->new_description->TooltipValue = "";

			// new_content
			$this->new_content->LinkCustomAttributes = "";
			$this->new_content->HrefValue = "";
			$this->new_content->TooltipValue = "";

			// new_type
			$this->new_type->LinkCustomAttributes = "";
			$this->new_type->HrefValue = "";
			$this->new_type->TooltipValue = "";

			// new_img_path
			$this->new_img_path->LinkCustomAttributes = "";
			$this->new_img_path->HrefValue = "";
			$this->new_img_path->HrefValue2 = $this->new_img_path->UploadPath . $this->new_img_path->Upload->DbValue;
			$this->new_img_path->TooltipValue = "";

			// new_publish_date
			$this->new_publish_date->LinkCustomAttributes = "";
			$this->new_publish_date->HrefValue = "";
			$this->new_publish_date->TooltipValue = "";

			// new_cat_id
			$this->new_cat_id->LinkCustomAttributes = "";
			$this->new_cat_id->HrefValue = "";
			$this->new_cat_id->TooltipValue = "";

			// new_link_id
			$this->new_link_id->LinkCustomAttributes = "";
			$this->new_link_id->HrefValue = "";
			$this->new_link_id->TooltipValue = "";

			// new_link_order
			$this->new_link_order->LinkCustomAttributes = "";
			$this->new_link_order->HrefValue = "";
			$this->new_link_order->TooltipValue = "";

			// new_status
			$this->new_status->LinkCustomAttributes = "";
			$this->new_status->HrefValue = "";
			$this->new_status->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "newslist.php", $this->TableVar, TRUE);
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
if (!isset($news_view)) $news_view = new cnews_view();

// Page init
$news_view->Page_Init();

// Page main
$news_view->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$news_view->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var news_view = new ew_Page("news_view");
news_view.PageID = "view"; // Page ID
var EW_PAGE_ID = news_view.PageID; // For backward compatibility

// Form object
var fnewsview = new ew_Form("fnewsview");

// Form_CustomValidate event
fnewsview.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fnewsview.ValidateRequired = true;
<?php } else { ?>
fnewsview.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fnewsview.Lists["x_new_cat_id"] = {"LinkField":"x_cat_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_cat_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fnewsview.Lists["x_new_link_id"] = {"LinkField":"x_new_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_new_title","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<div class="ewViewExportOptions">
<?php $news_view->ExportOptions->Render("body") ?>
<?php if (!$news_view->ExportOptions->UseDropDownButton) { ?>
</div>
<div class="ewViewOtherOptions">
<?php } ?>
<?php
	foreach ($news_view->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<?php $news_view->ShowPageHeader(); ?>
<?php
$news_view->ShowMessage();
?>
<form name="fnewsview" id="fnewsview" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="news">
<table class="ewGrid"><tr><td>
<table id="tbl_newsview" class="table table-bordered table-striped">
<?php if ($news->new_id->Visible) { // new_id ?>
	<tr id="r_new_id">
		<td><span id="elh_news_new_id"><?php echo $news->new_id->FldCaption() ?></span></td>
		<td<?php echo $news->new_id->CellAttributes() ?>>
<span id="el_news_new_id" class="control-group">
<span<?php echo $news->new_id->ViewAttributes() ?>>
<?php echo $news->new_id->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($news->new_title->Visible) { // new_title ?>
	<tr id="r_new_title">
		<td><span id="elh_news_new_title"><?php echo $news->new_title->FldCaption() ?></span></td>
		<td<?php echo $news->new_title->CellAttributes() ?>>
<span id="el_news_new_title" class="control-group">
<span<?php echo $news->new_title->ViewAttributes() ?>>
<?php echo $news->new_title->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($news->new_description->Visible) { // new_description ?>
	<tr id="r_new_description">
		<td><span id="elh_news_new_description"><?php echo $news->new_description->FldCaption() ?></span></td>
		<td<?php echo $news->new_description->CellAttributes() ?>>
<span id="el_news_new_description" class="control-group">
<span<?php echo $news->new_description->ViewAttributes() ?>>
<?php echo $news->new_description->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($news->new_content->Visible) { // new_content ?>
	<tr id="r_new_content">
		<td><span id="elh_news_new_content"><?php echo $news->new_content->FldCaption() ?></span></td>
		<td<?php echo $news->new_content->CellAttributes() ?>>
<span id="el_news_new_content" class="control-group">
<span<?php echo $news->new_content->ViewAttributes() ?>>
<?php echo $news->new_content->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($news->new_type->Visible) { // new_type ?>
	<tr id="r_new_type">
		<td><span id="elh_news_new_type"><?php echo $news->new_type->FldCaption() ?></span></td>
		<td<?php echo $news->new_type->CellAttributes() ?>>
<span id="el_news_new_type" class="control-group">
<span<?php echo $news->new_type->ViewAttributes() ?>>
<?php echo $news->new_type->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($news->new_img_path->Visible) { // new_img_path ?>
	<tr id="r_new_img_path">
		<td><span id="elh_news_new_img_path"><?php echo $news->new_img_path->FldCaption() ?></span></td>
		<td<?php echo $news->new_img_path->CellAttributes() ?>>
<span id="el_news_new_img_path" class="control-group">
<span<?php echo $news->new_img_path->ViewAttributes() ?>>
<?php if ($news->new_img_path->LinkAttributes() <> "") { ?>
<?php if (!empty($news->new_img_path->Upload->DbValue)) { ?>
<?php echo $news->new_img_path->ViewValue ?>
<?php } elseif (!in_array($news->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($news->new_img_path->Upload->DbValue)) { ?>
<?php echo $news->new_img_path->ViewValue ?>
<?php } elseif (!in_array($news->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($news->new_publish_date->Visible) { // new_publish_date ?>
	<tr id="r_new_publish_date">
		<td><span id="elh_news_new_publish_date"><?php echo $news->new_publish_date->FldCaption() ?></span></td>
		<td<?php echo $news->new_publish_date->CellAttributes() ?>>
<span id="el_news_new_publish_date" class="control-group">
<span<?php echo $news->new_publish_date->ViewAttributes() ?>>
<?php echo $news->new_publish_date->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($news->new_cat_id->Visible) { // new_cat_id ?>
	<tr id="r_new_cat_id">
		<td><span id="elh_news_new_cat_id"><?php echo $news->new_cat_id->FldCaption() ?></span></td>
		<td<?php echo $news->new_cat_id->CellAttributes() ?>>
<span id="el_news_new_cat_id" class="control-group">
<span<?php echo $news->new_cat_id->ViewAttributes() ?>>
<?php echo $news->new_cat_id->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($news->new_link_id->Visible) { // new_link_id ?>
	<tr id="r_new_link_id">
		<td><span id="elh_news_new_link_id"><?php echo $news->new_link_id->FldCaption() ?></span></td>
		<td<?php echo $news->new_link_id->CellAttributes() ?>>
<span id="el_news_new_link_id" class="control-group">
<span<?php echo $news->new_link_id->ViewAttributes() ?>>
<?php echo $news->new_link_id->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($news->new_link_order->Visible) { // new_link_order ?>
	<tr id="r_new_link_order">
		<td><span id="elh_news_new_link_order"><?php echo $news->new_link_order->FldCaption() ?></span></td>
		<td<?php echo $news->new_link_order->CellAttributes() ?>>
<span id="el_news_new_link_order" class="control-group">
<span<?php echo $news->new_link_order->ViewAttributes() ?>>
<?php echo $news->new_link_order->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($news->new_status->Visible) { // new_status ?>
	<tr id="r_new_status">
		<td><span id="elh_news_new_status"><?php echo $news->new_status->FldCaption() ?></span></td>
		<td<?php echo $news->new_status->CellAttributes() ?>>
<span id="el_news_new_status" class="control-group">
<span<?php echo $news->new_status->ViewAttributes() ?>>
<?php echo $news->new_status->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
</form>
<script type="text/javascript">
fnewsview.Init();
</script>
<?php
$news_view->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$news_view->Page_Terminate();
?>
