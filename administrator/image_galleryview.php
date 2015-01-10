<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "image_galleryinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$image_gallery_view = NULL; // Initialize page object first

class cimage_gallery_view extends cimage_gallery {

	// Page ID
	var $PageID = 'view';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'image_gallery';

	// Page object name
	var $PageObjName = 'image_gallery_view';

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

		// Table object (image_gallery)
		if (!isset($GLOBALS["image_gallery"]) || get_class($GLOBALS["image_gallery"]) == "cimage_gallery") {
			$GLOBALS["image_gallery"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["image_gallery"];
		}
		$KeyUrl = "";
		if (@$_GET["img_id"] <> "") {
			$this->RecKey["img_id"] = $_GET["img_id"];
			$KeyUrl .= "&amp;img_id=" . urlencode($this->RecKey["img_id"]);
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
			define("EW_TABLE_NAME", 'image_gallery', TRUE);

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
			$this->Page_Terminate("image_gallerylist.php");
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->img_id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
			if (@$_GET["img_id"] <> "") {
				$this->img_id->setQueryStringValue($_GET["img_id"]);
				$this->RecKey["img_id"] = $this->img_id->QueryStringValue;
			} else {
				$sReturnUrl = "image_gallerylist.php"; // Return to list
			}

			// Get action
			$this->CurrentAction = "I"; // Display form
			switch ($this->CurrentAction) {
				case "I": // Get a record to display
					if (!$this->LoadRow()) { // Load record based on key
						if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
						$sReturnUrl = "image_gallerylist.php"; // No matching record, return to list
					}
			}
		} else {
			$sReturnUrl = "image_gallerylist.php"; // Not page request, return to list
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
		$this->img_id->setDbValue($rs->fields('img_id'));
		$this->img_path->Upload->DbValue = $rs->fields('img_path');
		$this->img_path->CurrentValue = $this->img_path->Upload->DbValue;
		$this->img_description->setDbValue($rs->fields('img_description'));
		$this->img_cat_id->setDbValue($rs->fields('img_cat_id'));
		$this->img_new_id->setDbValue($rs->fields('img_new_id'));
		$this->img_offer_id->setDbValue($rs->fields('img_offer_id'));
		$this->img_nam_archive->setDbValue($rs->fields('img_nam_archive'));
		$this->img_is_banner->setDbValue($rs->fields('img_is_banner'));
		$this->img_order->setDbValue($rs->fields('img_order'));
		$this->img_status->setDbValue($rs->fields('img_status'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->img_id->DbValue = $row['img_id'];
		$this->img_path->Upload->DbValue = $row['img_path'];
		$this->img_description->DbValue = $row['img_description'];
		$this->img_cat_id->DbValue = $row['img_cat_id'];
		$this->img_new_id->DbValue = $row['img_new_id'];
		$this->img_offer_id->DbValue = $row['img_offer_id'];
		$this->img_nam_archive->DbValue = $row['img_nam_archive'];
		$this->img_is_banner->DbValue = $row['img_is_banner'];
		$this->img_order->DbValue = $row['img_order'];
		$this->img_status->DbValue = $row['img_status'];
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
		// img_id
		// img_path
		// img_description
		// img_cat_id
		// img_new_id
		// img_offer_id
		// img_nam_archive
		// img_is_banner
		// img_order
		// img_status

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// img_id
			$this->img_id->ViewValue = $this->img_id->CurrentValue;
			$this->img_id->ViewCustomAttributes = "";

			// img_path
			if (!ew_Empty($this->img_path->Upload->DbValue)) {
				$this->img_path->ViewValue = $this->img_path->Upload->DbValue;
			} else {
				$this->img_path->ViewValue = "";
			}
			$this->img_path->ViewCustomAttributes = "";

			// img_description
			$this->img_description->ViewValue = $this->img_description->CurrentValue;
			$this->img_description->ViewCustomAttributes = "";

			// img_cat_id
			if (strval($this->img_cat_id->CurrentValue) <> "") {
				$sFilterWrk = "`cat_id`" . ew_SearchString("=", $this->img_cat_id->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `cat_id`, `cat_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `categories`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->img_cat_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->img_cat_id->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->img_cat_id->ViewValue = $this->img_cat_id->CurrentValue;
				}
			} else {
				$this->img_cat_id->ViewValue = NULL;
			}
			$this->img_cat_id->ViewCustomAttributes = "";

			// img_new_id
			if (strval($this->img_new_id->CurrentValue) <> "") {
				$sFilterWrk = "`new_id`" . ew_SearchString("=", $this->img_new_id->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `new_id`, `new_title` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `news`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->img_new_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->img_new_id->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->img_new_id->ViewValue = $this->img_new_id->CurrentValue;
				}
			} else {
				$this->img_new_id->ViewValue = NULL;
			}
			$this->img_new_id->ViewCustomAttributes = "";

			// img_offer_id
			if (strval($this->img_offer_id->CurrentValue) <> "") {
				$sFilterWrk = "`offer_id`" . ew_SearchString("=", $this->img_offer_id->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `offer_id`, `offer_title` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `offers`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->img_offer_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->img_offer_id->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->img_offer_id->ViewValue = $this->img_offer_id->CurrentValue;
				}
			} else {
				$this->img_offer_id->ViewValue = NULL;
			}
			$this->img_offer_id->ViewCustomAttributes = "";

			// img_nam_archive
			if (strval($this->img_nam_archive->CurrentValue) <> "") {
				switch ($this->img_nam_archive->CurrentValue) {
					case $this->img_nam_archive->FldTagValue(1):
						$this->img_nam_archive->ViewValue = $this->img_nam_archive->FldTagCaption(1) <> "" ? $this->img_nam_archive->FldTagCaption(1) : $this->img_nam_archive->CurrentValue;
						break;
					case $this->img_nam_archive->FldTagValue(2):
						$this->img_nam_archive->ViewValue = $this->img_nam_archive->FldTagCaption(2) <> "" ? $this->img_nam_archive->FldTagCaption(2) : $this->img_nam_archive->CurrentValue;
						break;
					default:
						$this->img_nam_archive->ViewValue = $this->img_nam_archive->CurrentValue;
				}
			} else {
				$this->img_nam_archive->ViewValue = NULL;
			}
			$this->img_nam_archive->ViewCustomAttributes = "";

			// img_is_banner
			if (strval($this->img_is_banner->CurrentValue) <> "") {
				switch ($this->img_is_banner->CurrentValue) {
					case $this->img_is_banner->FldTagValue(1):
						$this->img_is_banner->ViewValue = $this->img_is_banner->FldTagCaption(1) <> "" ? $this->img_is_banner->FldTagCaption(1) : $this->img_is_banner->CurrentValue;
						break;
					case $this->img_is_banner->FldTagValue(2):
						$this->img_is_banner->ViewValue = $this->img_is_banner->FldTagCaption(2) <> "" ? $this->img_is_banner->FldTagCaption(2) : $this->img_is_banner->CurrentValue;
						break;
					default:
						$this->img_is_banner->ViewValue = $this->img_is_banner->CurrentValue;
				}
			} else {
				$this->img_is_banner->ViewValue = NULL;
			}
			$this->img_is_banner->ViewCustomAttributes = "";

			// img_order
			$this->img_order->ViewValue = $this->img_order->CurrentValue;
			$this->img_order->ViewValue = ew_FormatNumber($this->img_order->ViewValue, 0, -2, -2, -2);
			$this->img_order->ViewCustomAttributes = "";

			// img_status
			if (strval($this->img_status->CurrentValue) <> "") {
				switch ($this->img_status->CurrentValue) {
					case $this->img_status->FldTagValue(1):
						$this->img_status->ViewValue = $this->img_status->FldTagCaption(1) <> "" ? $this->img_status->FldTagCaption(1) : $this->img_status->CurrentValue;
						break;
					case $this->img_status->FldTagValue(2):
						$this->img_status->ViewValue = $this->img_status->FldTagCaption(2) <> "" ? $this->img_status->FldTagCaption(2) : $this->img_status->CurrentValue;
						break;
					default:
						$this->img_status->ViewValue = $this->img_status->CurrentValue;
				}
			} else {
				$this->img_status->ViewValue = NULL;
			}
			$this->img_status->ViewCustomAttributes = "";

			// img_id
			$this->img_id->LinkCustomAttributes = "";
			$this->img_id->HrefValue = "";
			$this->img_id->TooltipValue = "";

			// img_path
			$this->img_path->LinkCustomAttributes = "";
			$this->img_path->HrefValue = "";
			$this->img_path->HrefValue2 = $this->img_path->UploadPath . $this->img_path->Upload->DbValue;
			$this->img_path->TooltipValue = "";

			// img_description
			$this->img_description->LinkCustomAttributes = "";
			$this->img_description->HrefValue = "";
			$this->img_description->TooltipValue = "";

			// img_cat_id
			$this->img_cat_id->LinkCustomAttributes = "";
			$this->img_cat_id->HrefValue = "";
			$this->img_cat_id->TooltipValue = "";

			// img_new_id
			$this->img_new_id->LinkCustomAttributes = "";
			$this->img_new_id->HrefValue = "";
			$this->img_new_id->TooltipValue = "";

			// img_offer_id
			$this->img_offer_id->LinkCustomAttributes = "";
			$this->img_offer_id->HrefValue = "";
			$this->img_offer_id->TooltipValue = "";

			// img_nam_archive
			$this->img_nam_archive->LinkCustomAttributes = "";
			$this->img_nam_archive->HrefValue = "";
			$this->img_nam_archive->TooltipValue = "";

			// img_is_banner
			$this->img_is_banner->LinkCustomAttributes = "";
			$this->img_is_banner->HrefValue = "";
			$this->img_is_banner->TooltipValue = "";

			// img_order
			$this->img_order->LinkCustomAttributes = "";
			$this->img_order->HrefValue = "";
			$this->img_order->TooltipValue = "";

			// img_status
			$this->img_status->LinkCustomAttributes = "";
			$this->img_status->HrefValue = "";
			$this->img_status->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "image_gallerylist.php", $this->TableVar, TRUE);
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
if (!isset($image_gallery_view)) $image_gallery_view = new cimage_gallery_view();

// Page init
$image_gallery_view->Page_Init();

// Page main
$image_gallery_view->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$image_gallery_view->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var image_gallery_view = new ew_Page("image_gallery_view");
image_gallery_view.PageID = "view"; // Page ID
var EW_PAGE_ID = image_gallery_view.PageID; // For backward compatibility

// Form object
var fimage_galleryview = new ew_Form("fimage_galleryview");

// Form_CustomValidate event
fimage_galleryview.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fimage_galleryview.ValidateRequired = true;
<?php } else { ?>
fimage_galleryview.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fimage_galleryview.Lists["x_img_cat_id"] = {"LinkField":"x_cat_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_cat_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fimage_galleryview.Lists["x_img_new_id"] = {"LinkField":"x_new_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_new_title","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fimage_galleryview.Lists["x_img_offer_id"] = {"LinkField":"x_offer_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_offer_title","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<div class="ewViewExportOptions">
<?php $image_gallery_view->ExportOptions->Render("body") ?>
<?php if (!$image_gallery_view->ExportOptions->UseDropDownButton) { ?>
</div>
<div class="ewViewOtherOptions">
<?php } ?>
<?php
	foreach ($image_gallery_view->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<?php $image_gallery_view->ShowPageHeader(); ?>
<?php
$image_gallery_view->ShowMessage();
?>
<form name="fimage_galleryview" id="fimage_galleryview" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="image_gallery">
<table class="ewGrid"><tr><td>
<table id="tbl_image_galleryview" class="table table-bordered table-striped">
<?php if ($image_gallery->img_id->Visible) { // img_id ?>
	<tr id="r_img_id">
		<td><span id="elh_image_gallery_img_id"><?php echo $image_gallery->img_id->FldCaption() ?></span></td>
		<td<?php echo $image_gallery->img_id->CellAttributes() ?>>
<span id="el_image_gallery_img_id" class="control-group">
<span<?php echo $image_gallery->img_id->ViewAttributes() ?>>
<?php echo $image_gallery->img_id->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($image_gallery->img_path->Visible) { // img_path ?>
	<tr id="r_img_path">
		<td><span id="elh_image_gallery_img_path"><?php echo $image_gallery->img_path->FldCaption() ?></span></td>
		<td<?php echo $image_gallery->img_path->CellAttributes() ?>>
<span id="el_image_gallery_img_path" class="control-group">
<span<?php echo $image_gallery->img_path->ViewAttributes() ?>>
<?php
$Files = explode(",", $image_gallery->img_path->Upload->DbValue);
$HrefValue = $image_gallery->img_path->HrefValue;
$FileCount = count($Files);
for ($i = 0; $i < $FileCount; $i++) {
if ($Files[$i] <> "") {
$image_gallery->img_path->ViewValue = $Files[$i];
$image_gallery->img_path->HrefValue = str_replace("%u", ew_HtmlEncode(ew_UploadPathEx(FALSE, $image_gallery->img_path->UploadPath) . $Files[$i]), $HrefValue);
$Files[$i] = str_replace("%f", ew_HtmlEncode(ew_UploadPathEx(FALSE, $image_gallery->img_path->UploadPath) . $Files[$i]), $image_gallery->img_path->ViewValue);
?>
<?php if ($image_gallery->img_path->LinkAttributes() <> "") { ?>
<?php if (!empty($image_gallery->img_path->Upload->DbValue)) { ?>
<?php echo $image_gallery->img_path->ViewValue ?>
<?php } elseif (!in_array($image_gallery->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($image_gallery->img_path->Upload->DbValue)) { ?>
<?php echo $image_gallery->img_path->ViewValue ?>
<?php } elseif (!in_array($image_gallery->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
<?php
}
}
?>
</span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($image_gallery->img_description->Visible) { // img_description ?>
	<tr id="r_img_description">
		<td><span id="elh_image_gallery_img_description"><?php echo $image_gallery->img_description->FldCaption() ?></span></td>
		<td<?php echo $image_gallery->img_description->CellAttributes() ?>>
<span id="el_image_gallery_img_description" class="control-group">
<span<?php echo $image_gallery->img_description->ViewAttributes() ?>>
<?php echo $image_gallery->img_description->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($image_gallery->img_cat_id->Visible) { // img_cat_id ?>
	<tr id="r_img_cat_id">
		<td><span id="elh_image_gallery_img_cat_id"><?php echo $image_gallery->img_cat_id->FldCaption() ?></span></td>
		<td<?php echo $image_gallery->img_cat_id->CellAttributes() ?>>
<span id="el_image_gallery_img_cat_id" class="control-group">
<span<?php echo $image_gallery->img_cat_id->ViewAttributes() ?>>
<?php echo $image_gallery->img_cat_id->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($image_gallery->img_new_id->Visible) { // img_new_id ?>
	<tr id="r_img_new_id">
		<td><span id="elh_image_gallery_img_new_id"><?php echo $image_gallery->img_new_id->FldCaption() ?></span></td>
		<td<?php echo $image_gallery->img_new_id->CellAttributes() ?>>
<span id="el_image_gallery_img_new_id" class="control-group">
<span<?php echo $image_gallery->img_new_id->ViewAttributes() ?>>
<?php echo $image_gallery->img_new_id->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($image_gallery->img_offer_id->Visible) { // img_offer_id ?>
	<tr id="r_img_offer_id">
		<td><span id="elh_image_gallery_img_offer_id"><?php echo $image_gallery->img_offer_id->FldCaption() ?></span></td>
		<td<?php echo $image_gallery->img_offer_id->CellAttributes() ?>>
<span id="el_image_gallery_img_offer_id" class="control-group">
<span<?php echo $image_gallery->img_offer_id->ViewAttributes() ?>>
<?php echo $image_gallery->img_offer_id->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($image_gallery->img_nam_archive->Visible) { // img_nam_archive ?>
	<tr id="r_img_nam_archive">
		<td><span id="elh_image_gallery_img_nam_archive"><?php echo $image_gallery->img_nam_archive->FldCaption() ?></span></td>
		<td<?php echo $image_gallery->img_nam_archive->CellAttributes() ?>>
<span id="el_image_gallery_img_nam_archive" class="control-group">
<span<?php echo $image_gallery->img_nam_archive->ViewAttributes() ?>>
<?php echo $image_gallery->img_nam_archive->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($image_gallery->img_is_banner->Visible) { // img_is_banner ?>
	<tr id="r_img_is_banner">
		<td><span id="elh_image_gallery_img_is_banner"><?php echo $image_gallery->img_is_banner->FldCaption() ?></span></td>
		<td<?php echo $image_gallery->img_is_banner->CellAttributes() ?>>
<span id="el_image_gallery_img_is_banner" class="control-group">
<span<?php echo $image_gallery->img_is_banner->ViewAttributes() ?>>
<?php echo $image_gallery->img_is_banner->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($image_gallery->img_order->Visible) { // img_order ?>
	<tr id="r_img_order">
		<td><span id="elh_image_gallery_img_order"><?php echo $image_gallery->img_order->FldCaption() ?></span></td>
		<td<?php echo $image_gallery->img_order->CellAttributes() ?>>
<span id="el_image_gallery_img_order" class="control-group">
<span<?php echo $image_gallery->img_order->ViewAttributes() ?>>
<?php echo $image_gallery->img_order->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($image_gallery->img_status->Visible) { // img_status ?>
	<tr id="r_img_status">
		<td><span id="elh_image_gallery_img_status"><?php echo $image_gallery->img_status->FldCaption() ?></span></td>
		<td<?php echo $image_gallery->img_status->CellAttributes() ?>>
<span id="el_image_gallery_img_status" class="control-group">
<span<?php echo $image_gallery->img_status->ViewAttributes() ?>>
<?php echo $image_gallery->img_status->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
</form>
<script type="text/javascript">
fimage_galleryview.Init();
</script>
<?php
$image_gallery_view->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$image_gallery_view->Page_Terminate();
?>
