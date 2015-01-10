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

$image_gallery_list = NULL; // Initialize page object first

class cimage_gallery_list extends cimage_gallery {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'image_gallery';

	// Page object name
	var $PageObjName = 'image_gallery_list';

	// Grid form hidden field names
	var $FormName = 'fimage_gallerylist';
	var $FormActionName = 'k_action';
	var $FormKeyName = 'k_key';
	var $FormOldKeyName = 'k_oldkey';
	var $FormBlankRowName = 'k_blankrow';
	var $FormKeyCountName = 'key_count';

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

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "image_galleryadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "image_gallerydelete.php";
		$this->MultiUpdateUrl = "image_galleryupdate.php";

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'image_gallery', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// List options
		$this->ListOptions = new cListOptions();
		$this->ListOptions->TableVar = $this->TableVar;

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['addedit'] = new cListOptions();
		$this->OtherOptions['addedit']->Tag = "div";
		$this->OtherOptions['addedit']->TagClassName = "ewAddEditOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "div";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "div";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";
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
		if (!$Security->CanList()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("login.php");
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();
		$this->img_id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Setup other options
		$this->SetupOtherOptions();

		// Set "checkbox" visible
		if (count($this->CustomActions) > 0)
			$this->ListOptions->Items["checkbox"]->Visible = TRUE;
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

	// Class variables
	var $ListOptions; // List options
	var $ExportOptions; // Export options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 20;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $SearchWhere = ""; // Search WHERE clause
	var $RecCnt = 0; // Record count
	var $EditRowCnt;
	var $StartRowCnt = 1;
	var $RowCnt = 0;
	var $Attrs = array(); // Row attributes and cell attributes
	var $RowIndex = 0; // Row index
	var $KeyCount = 0; // Key count
	var $RowAction = ""; // Row action
	var $RowOldKey = ""; // Row old key (for copy)
	var $RecPerRow = 0;
	var $ColCnt = 0;
	var $DbMasterFilter = ""; // Master filter
	var $DbDetailFilter = ""; // Detail filter
	var $MasterRecordExists;	
	var $MultiSelectKey;
	var $Command;
	var $RestoreSearch = FALSE;
	var $Recordset;
	var $OldRecordset;

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError, $gsSearchError, $Security;

		// Search filters
		$sSrchAdvanced = ""; // Advanced search filter
		$sSrchBasic = ""; // Basic search filter
		$sFilter = "";

		// Get command
		$this->Command = strtolower(@$_GET["cmd"]);
		if ($this->IsPageRequest()) { // Validate request

			// Process custom action first
			$this->ProcessCustomAction();

			// Handle reset command
			$this->ResetCmd();

			// Set up Breadcrumb
			if ($this->Export == "")
				$this->SetupBreadcrumb();

			// Hide list options
			if ($this->Export <> "") {
				$this->ListOptions->HideAllOptions(array("sequence"));
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			} elseif ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
				$this->ListOptions->HideAllOptions();
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			}

			// Hide export options
			if ($this->Export <> "" || $this->CurrentAction <> "")
				$this->ExportOptions->HideAllOptions();

			// Hide other options
			if ($this->Export <> "") {
				foreach ($this->OtherOptions as &$option)
					$option->HideAllOptions();
			}

			// Get basic search values
			$this->LoadBasicSearchValues();

			// Restore search parms from Session if not searching / reset
			if ($this->Command <> "search" && $this->Command <> "reset" && $this->Command <> "resetall" && $this->CheckSearchParms())
				$this->RestoreSearchParms();

			// Call Recordset SearchValidated event
			$this->Recordset_SearchValidated();

			// Set up sorting order
			$this->SetUpSortOrder();

			// Get basic search criteria
			if ($gsSearchError == "")
				$sSrchBasic = $this->BasicSearchWhere();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 20; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

		// Load search default if no existing search criteria
		if (!$this->CheckSearchParms()) {

			// Load basic search from default
			$this->BasicSearch->LoadDefault();
			if ($this->BasicSearch->Keyword != "")
				$sSrchBasic = $this->BasicSearchWhere();
		}

		// Build search criteria
		ew_AddFilter($this->SearchWhere, $sSrchAdvanced);
		ew_AddFilter($this->SearchWhere, $sSrchBasic);

		// Call Recordset_Searching event
		$this->Recordset_Searching($this->SearchWhere);

		// Save search criteria
		if ($this->Command == "search" && !$this->RestoreSearch) {
			$this->setSearchWhere($this->SearchWhere); // Save to Session
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} else {
			$this->SearchWhere = $this->getSearchWhere();
		}

		// Build filter
		$sFilter = "";
		if (!$Security->CanList())
			$sFilter = "(0=1)"; // Filter all records
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Set up filter in session
		$this->setSessionWhere($sFilter);
		$this->CurrentFilter = "";
	}

	// Build filter for all keys
	function BuildKeyFilter() {
		global $objForm;
		$sWrkFilter = "";

		// Update row index and get row key
		$rowindex = 1;
		$objForm->Index = $rowindex;
		$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		while ($sThisKey <> "") {
			if ($this->SetupKeyValues($sThisKey)) {
				$sFilter = $this->KeyFilter();
				if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
				$sWrkFilter .= $sFilter;
			} else {
				$sWrkFilter = "0=1";
				break;
			}

			// Update row index and get row key
			$rowindex++; // Next row
			$objForm->Index = $rowindex;
			$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		}
		return $sWrkFilter;
	}

	// Set up key values
	function SetupKeyValues($key) {
		$arrKeyFlds = explode($GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"], $key);
		if (count($arrKeyFlds) >= 1) {
			$this->img_id->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->img_id->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Return basic search SQL
	function BasicSearchSQL($Keyword) {
		$sKeyword = ew_AdjustSql($Keyword);
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->img_path, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->img_description, $Keyword);
		if (is_numeric($Keyword)) $this->BuildBasicSearchSQL($sWhere, $this->img_nam_archive, $Keyword);
		return $sWhere;
	}

	// Build basic search SQL
	function BuildBasicSearchSql(&$Where, &$Fld, $Keyword) {
		if ($Keyword == EW_NULL_VALUE) {
			$sWrk = $Fld->FldExpression . " IS NULL";
		} elseif ($Keyword == EW_NOT_NULL_VALUE) {
			$sWrk = $Fld->FldExpression . " IS NOT NULL";
		} else {
			$sFldExpression = ($Fld->FldVirtualExpression <> $Fld->FldExpression) ? $Fld->FldVirtualExpression : $Fld->FldBasicSearchExpression;
			$sWrk = $sFldExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING));
		}
		if ($Where <> "") $Where .= " OR ";
		$Where .= $sWrk;
	}

	// Return basic search WHERE clause based on search keyword and type
	function BasicSearchWhere() {
		global $Security;
		$sSearchStr = "";
		if (!$Security->CanSearch()) return "";
		$sSearchKeyword = $this->BasicSearch->Keyword;
		$sSearchType = $this->BasicSearch->Type;
		if ($sSearchKeyword <> "") {
			$sSearch = trim($sSearchKeyword);
			if ($sSearchType <> "=") {
				while (strpos($sSearch, "  ") !== FALSE)
					$sSearch = str_replace("  ", " ", $sSearch);
				$arKeyword = explode(" ", trim($sSearch));
				foreach ($arKeyword as $sKeyword) {
					if ($sSearchStr <> "") $sSearchStr .= " " . $sSearchType . " ";
					$sSearchStr .= "(" . $this->BasicSearchSQL($sKeyword) . ")";
				}
			} else {
				$sSearchStr = $this->BasicSearchSQL($sSearch);
			}
			$this->Command = "search";
		}
		if ($this->Command == "search") {
			$this->BasicSearch->setKeyword($sSearchKeyword);
			$this->BasicSearch->setType($sSearchType);
		}
		return $sSearchStr;
	}

	// Check if search parm exists
	function CheckSearchParms() {

		// Check basic search
		if ($this->BasicSearch->IssetSession())
			return TRUE;
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear basic search parameters
		$this->ResetBasicSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all basic search parameters
	function ResetBasicSearchParms() {
		$this->BasicSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->img_id); // img_id
			$this->UpdateSort($this->img_path); // img_path
			$this->UpdateSort($this->img_description); // img_description
			$this->UpdateSort($this->img_cat_id); // img_cat_id
			$this->UpdateSort($this->img_new_id); // img_new_id
			$this->UpdateSort($this->img_offer_id); // img_offer_id
			$this->UpdateSort($this->img_nam_archive); // img_nam_archive
			$this->UpdateSort($this->img_is_banner); // img_is_banner
			$this->UpdateSort($this->img_order); // img_order
			$this->UpdateSort($this->img_status); // img_status
			$this->setStartRecordNumber(1); // Reset start position
		}
	}

	// Load sort order parameters
	function LoadSortOrder() {
		$sOrderBy = $this->getSessionOrderBy(); // Get ORDER BY from Session
		if ($sOrderBy == "") {
			if ($this->SqlOrderBy() <> "") {
				$sOrderBy = $this->SqlOrderBy();
				$this->setSessionOrderBy($sOrderBy);
				$this->img_id->setSort("DESC");
			}
		}
	}

	// Reset command
	// - cmd=reset (Reset search parameters)
	// - cmd=resetall (Reset search and master/detail parameters)
	// - cmd=resetsort (Reset sort parameters)
	function ResetCmd() {

		// Check if reset command
		if (substr($this->Command,0,5) == "reset") {

			// Reset search criteria
			if ($this->Command == "reset" || $this->Command == "resetall")
				$this->ResetSearchParms();

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->img_id->setSort("");
				$this->img_path->setSort("");
				$this->img_description->setSort("");
				$this->img_cat_id->setSort("");
				$this->img_new_id->setSort("");
				$this->img_offer_id->setSort("");
				$this->img_nam_archive->setSort("");
				$this->img_is_banner->setSort("");
				$this->img_order->setSort("");
				$this->img_status->setSort("");
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

		// Add group option item
		$item = &$this->ListOptions->Add($this->ListOptions->GroupOptionName);
		$item->Body = "";
		$item->OnLeft = FALSE;
		$item->Visible = FALSE;

		// "view"
		$item = &$this->ListOptions->Add("view");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanView();
		$item->OnLeft = FALSE;

		// "edit"
		$item = &$this->ListOptions->Add("edit");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanEdit();
		$item->OnLeft = FALSE;

		// "copy"
		$item = &$this->ListOptions->Add("copy");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanAdd();
		$item->OnLeft = FALSE;

		// "delete"
		$item = &$this->ListOptions->Add("delete");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanDelete();
		$item->OnLeft = FALSE;

		// "checkbox"
		$item = &$this->ListOptions->Add("checkbox");
		$item->Visible = FALSE;
		$item->OnLeft = FALSE;
		$item->Header = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key\" id=\"key\" onclick=\"ew_SelectAllKey(this);\"></label>";
		$item->ShowInDropDown = FALSE;
		$item->ShowInButtonGroup = FALSE;

		// Drop down button for ListOptions
		$this->ListOptions->UseDropDownButton = FALSE;
		$this->ListOptions->DropDownButtonPhrase = $Language->Phrase("ButtonListOptions");
		$this->ListOptions->UseButtonGroup = FALSE;
		$this->ListOptions->ButtonClass = "btn-small"; // Class for button group

		// Call ListOptions_Load event
		$this->ListOptions_Load();
		$item = &$this->ListOptions->GetItem($this->ListOptions->GroupOptionName);
		$item->Visible = $this->ListOptions->GroupOptionVisible();
	}

	// Render list options
	function RenderListOptions() {
		global $Security, $Language, $objForm;
		$this->ListOptions->LoadDefault();

		// "view"
		$oListOpt = &$this->ListOptions->Items["view"];
		if ($Security->CanView())
			$oListOpt->Body = "<a class=\"ewRowLink ewView\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewLink")) . "\" href=\"" . ew_HtmlEncode($this->ViewUrl) . "\">" . $Language->Phrase("ViewLink") . "</a>";
		else
			$oListOpt->Body = "";

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		if ($Security->CanEdit()) {
			$oListOpt->Body = "<a class=\"ewRowLink ewEdit\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("EditLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "copy"
		$oListOpt = &$this->ListOptions->Items["copy"];
		if ($Security->CanAdd()) {
			$oListOpt->Body = "<a class=\"ewRowLink ewCopy\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("CopyLink")) . "\" href=\"" . ew_HtmlEncode($this->CopyUrl) . "\">" . $Language->Phrase("CopyLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "delete"
		$oListOpt = &$this->ListOptions->Items["delete"];
		if ($Security->CanDelete())
			$oListOpt->Body = "<a class=\"ewRowLink ewDelete\"" . "" . " data-caption=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" . $Language->Phrase("DeleteLink") . "</a>";
		else
			$oListOpt->Body = "";

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$oListOpt->Body = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->img_id->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'></label>";
		$this->RenderListOptionsExt();

		// Call ListOptions_Rendered event
		$this->ListOptions_Rendered();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = $options["addedit"];

		// Add
		$item = &$option->Add("add");
		$item->Body = "<a class=\"ewAddEdit ewAdd\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . $Language->Phrase("AddLink") . "</a>";
		$item->Visible = ($this->AddUrl <> "" && $Security->CanAdd());
		$option = $options["action"];

		// Set up options default
		foreach ($options as &$option) {
			$option->UseDropDownButton = FALSE;
			$option->UseButtonGroup = TRUE;
			$option->ButtonClass = "btn-small"; // Class for button group
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["addedit"]->DropDownButtonPhrase = $Language->Phrase("ButtonAddEdit");
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");
	}

	// Render other options
	function RenderOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
			$option = &$options["action"];
			foreach ($this->CustomActions as $action => $name) {

				// Add custom action
				$item = &$option->Add("custom_" . $action);
				$item->Body = "<a class=\"ewAction ewCustomAction\" href=\"\" onclick=\"ew_SubmitSelected(document.fimage_gallerylist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
			}

			// Hide grid edit, multi-delete and multi-update
			if ($this->TotalRecs <= 0) {
				$option = &$options["addedit"];
				$item = &$option->GetItem("gridedit");
				if ($item) $item->Visible = FALSE;
				$option = &$options["action"];
				$item = &$option->GetItem("multidelete");
				if ($item) $item->Visible = FALSE;
				$item = &$option->GetItem("multiupdate");
				if ($item) $item->Visible = FALSE;
			}
	}

	// Process custom action
	function ProcessCustomAction() {
		global $conn, $Language, $Security;
		$sFilter = $this->GetKeyFilter();
		$UserAction = @$_POST["useraction"];
		if ($sFilter <> "" && $UserAction <> "") {
			$this->CurrentFilter = $sFilter;
			$sSql = $this->SQL();
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$rs = $conn->Execute($sSql);
			$conn->raiseErrorFn = '';
			$rsuser = ($rs) ? $rs->GetRows() : array();
			if ($rs)
				$rs->Close();

			// Call row custom action event
			if (count($rsuser) > 0) {
				$conn->BeginTrans();
				foreach ($rsuser as $row) {
					$Processed = $this->Row_CustomAction($UserAction, $row);
					if (!$Processed) break;
				}
				if ($Processed) {
					$conn->CommitTrans(); // Commit the changes
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage(str_replace('%s', $UserAction, $Language->Phrase("CustomActionCompleted"))); // Set up success message
				} else {
					$conn->RollbackTrans(); // Rollback changes

					// Set up error message
					if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

						// Use the message, do nothing
					} elseif ($this->CancelMessage <> "") {
						$this->setFailureMessage($this->CancelMessage);
						$this->CancelMessage = "";
					} else {
						$this->setFailureMessage(str_replace('%s', $UserAction, $Language->Phrase("CustomActionCancelled")));
					}
				}
			}
		}
	}

	function RenderListOptionsExt() {
		global $Security, $Language;
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

	// Load basic search values
	function LoadBasicSearchValues() {
		$this->BasicSearch->Keyword = @$_GET[EW_TABLE_BASIC_SEARCH];
		if ($this->BasicSearch->Keyword <> "") $this->Command = "search";
		$this->BasicSearch->Type = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
	}

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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("img_id")) <> "")
			$this->img_id->CurrentValue = $this->getKey("img_id"); // img_id
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
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

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
		$url = ew_CurrentUrl();
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("list", $this->TableVar, $url, $this->TableVar, TRUE);
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

	// ListOptions Load event
	function ListOptions_Load() {

		// Example:
		//$opt = &$this->ListOptions->Add("new");
		//$opt->Header = "xxx";
		//$opt->OnLeft = TRUE; // Link on left
		//$opt->MoveTo(0); // Move to first column

	}

	// ListOptions Rendered event
	function ListOptions_Rendered() {

		// Example: 
		//$this->ListOptions->Items["new"]->Body = "xxx";

	}

	// Row Custom Action event
	function Row_CustomAction($action, $row) {

		// Return FALSE to abort
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($image_gallery_list)) $image_gallery_list = new cimage_gallery_list();

// Page init
$image_gallery_list->Page_Init();

// Page main
$image_gallery_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$image_gallery_list->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var image_gallery_list = new ew_Page("image_gallery_list");
image_gallery_list.PageID = "list"; // Page ID
var EW_PAGE_ID = image_gallery_list.PageID; // For backward compatibility

// Form object
var fimage_gallerylist = new ew_Form("fimage_gallerylist");
fimage_gallerylist.FormKeyCountName = '<?php echo $image_gallery_list->FormKeyCountName ?>';

// Form_CustomValidate event
fimage_gallerylist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fimage_gallerylist.ValidateRequired = true;
<?php } else { ?>
fimage_gallerylist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fimage_gallerylist.Lists["x_img_cat_id"] = {"LinkField":"x_cat_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_cat_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fimage_gallerylist.Lists["x_img_new_id"] = {"LinkField":"x_new_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_new_title","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fimage_gallerylist.Lists["x_img_offer_id"] = {"LinkField":"x_offer_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_offer_title","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
var fimage_gallerylistsrch = new ew_Form("fimage_gallerylistsrch");
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php if ($image_gallery_list->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $image_gallery_list->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$image_gallery_list->TotalRecs = $image_gallery->SelectRecordCount();
	} else {
		if ($image_gallery_list->Recordset = $image_gallery_list->LoadRecordset())
			$image_gallery_list->TotalRecs = $image_gallery_list->Recordset->RecordCount();
	}
	$image_gallery_list->StartRec = 1;
	if ($image_gallery_list->DisplayRecs <= 0 || ($image_gallery->Export <> "" && $image_gallery->ExportAll)) // Display all records
		$image_gallery_list->DisplayRecs = $image_gallery_list->TotalRecs;
	if (!($image_gallery->Export <> "" && $image_gallery->ExportAll))
		$image_gallery_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$image_gallery_list->Recordset = $image_gallery_list->LoadRecordset($image_gallery_list->StartRec-1, $image_gallery_list->DisplayRecs);
$image_gallery_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($image_gallery->Export == "" && $image_gallery->CurrentAction == "") { ?>
<form name="fimage_gallerylistsrch" id="fimage_gallerylistsrch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<div class="accordion ewDisplayTable ewSearchTable" id="fimage_gallerylistsrch_SearchGroup">
	<div class="accordion-group">
		<div class="accordion-heading">
<a class="accordion-toggle" data-toggle="collapse" data-parent="#fimage_gallerylistsrch_SearchGroup" href="#fimage_gallerylistsrch_SearchBody"><?php echo $Language->Phrase("Search") ?></a>
		</div>
		<div id="fimage_gallerylistsrch_SearchBody" class="accordion-body collapse in">
			<div class="accordion-inner">
<div id="fimage_gallerylistsrch_SearchPanel">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="image_gallery">
<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="btn-group ewButtonGroup">
	<div class="input-append">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="input-large" value="<?php echo ew_HtmlEncode($image_gallery_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?></button>
	</div>
	</div>
	<div class="btn-group ewButtonGroup">
	<a class="btn ewShowAll" href="<?php echo $image_gallery_list->PageUrl() ?>cmd=reset"><?php echo $Language->Phrase("ShowAll") ?></a>
	</div>
</div>
<div id="xsr_2" class="ewRow">
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="="<?php if ($image_gallery_list->BasicSearch->getType() == "=") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("ExactPhrase") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND"<?php if ($image_gallery_list->BasicSearch->getType() == "AND") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AllWord") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR"<?php if ($image_gallery_list->BasicSearch->getType() == "OR") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AnyWord") ?></label>
</div>
</div>
</div>
			</div>
		</div>
	</div>
</div>
</form>
<?php } ?>
<?php } ?>
<?php $image_gallery_list->ShowPageHeader(); ?>
<?php
$image_gallery_list->ShowMessage();
?>
<table class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridUpperPanel">
<?php if ($image_gallery->CurrentAction <> "gridadd" && $image_gallery->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($image_gallery_list->Pager)) $image_gallery_list->Pager = new cPrevNextPager($image_gallery_list->StartRec, $image_gallery_list->DisplayRecs, $image_gallery_list->TotalRecs) ?>
<?php if ($image_gallery_list->Pager->RecordCount > 0) { ?>
<table class="ewStdTable"><tbody><tr><td>
	<?php echo $Language->Phrase("Page") ?>&nbsp;
<div class="input-prepend input-append">
<!--first page button-->
	<?php if ($image_gallery_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $image_gallery_list->PageUrl() ?>start=<?php echo $image_gallery_list->Pager->FirstButton->Start ?>"><i class="icon-step-backward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-step-backward"></i></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($image_gallery_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $image_gallery_list->PageUrl() ?>start=<?php echo $image_gallery_list->Pager->PrevButton->Start ?>"><i class="icon-prev"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-prev"></i></a>
	<?php } ?>
<!--current page number-->
	<input class="input-mini" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $image_gallery_list->Pager->CurrentPage ?>">
<!--next page button-->
	<?php if ($image_gallery_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $image_gallery_list->PageUrl() ?>start=<?php echo $image_gallery_list->Pager->NextButton->Start ?>"><i class="icon-play"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-play"></i></a>
	<?php } ?>
<!--last page button-->
	<?php if ($image_gallery_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $image_gallery_list->PageUrl() ?>start=<?php echo $image_gallery_list->Pager->LastButton->Start ?>"><i class="icon-step-forward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-step-forward"></i></a>
	<?php } ?>
</div>
	&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $image_gallery_list->Pager->PageCount ?>
</td>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $image_gallery_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $image_gallery_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $image_gallery_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($image_gallery_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($image_gallery_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
</div>
<form name="fimage_gallerylist" id="fimage_gallerylist" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="image_gallery">
<div id="gmp_image_gallery" class="ewGridMiddlePanel">
<?php if ($image_gallery_list->TotalRecs > 0) { ?>
<table id="tbl_image_gallerylist" class="ewTable ewTableSeparate">
<?php echo $image_gallery->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$image_gallery_list->RenderListOptions();

// Render list options (header, left)
$image_gallery_list->ListOptions->Render("header", "left");
?>
<?php if ($image_gallery->img_id->Visible) { // img_id ?>
	<?php if ($image_gallery->SortUrl($image_gallery->img_id) == "") { ?>
		<td><div id="elh_image_gallery_img_id" class="image_gallery_img_id"><div class="ewTableHeaderCaption"><?php echo $image_gallery->img_id->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $image_gallery->SortUrl($image_gallery->img_id) ?>',1);"><div id="elh_image_gallery_img_id" class="image_gallery_img_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $image_gallery->img_id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($image_gallery->img_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($image_gallery->img_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($image_gallery->img_path->Visible) { // img_path ?>
	<?php if ($image_gallery->SortUrl($image_gallery->img_path) == "") { ?>
		<td><div id="elh_image_gallery_img_path" class="image_gallery_img_path"><div class="ewTableHeaderCaption"><?php echo $image_gallery->img_path->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $image_gallery->SortUrl($image_gallery->img_path) ?>',1);"><div id="elh_image_gallery_img_path" class="image_gallery_img_path">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $image_gallery->img_path->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($image_gallery->img_path->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($image_gallery->img_path->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($image_gallery->img_description->Visible) { // img_description ?>
	<?php if ($image_gallery->SortUrl($image_gallery->img_description) == "") { ?>
		<td><div id="elh_image_gallery_img_description" class="image_gallery_img_description"><div class="ewTableHeaderCaption"><?php echo $image_gallery->img_description->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $image_gallery->SortUrl($image_gallery->img_description) ?>',1);"><div id="elh_image_gallery_img_description" class="image_gallery_img_description">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $image_gallery->img_description->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($image_gallery->img_description->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($image_gallery->img_description->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($image_gallery->img_cat_id->Visible) { // img_cat_id ?>
	<?php if ($image_gallery->SortUrl($image_gallery->img_cat_id) == "") { ?>
		<td><div id="elh_image_gallery_img_cat_id" class="image_gallery_img_cat_id"><div class="ewTableHeaderCaption"><?php echo $image_gallery->img_cat_id->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $image_gallery->SortUrl($image_gallery->img_cat_id) ?>',1);"><div id="elh_image_gallery_img_cat_id" class="image_gallery_img_cat_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $image_gallery->img_cat_id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($image_gallery->img_cat_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($image_gallery->img_cat_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($image_gallery->img_new_id->Visible) { // img_new_id ?>
	<?php if ($image_gallery->SortUrl($image_gallery->img_new_id) == "") { ?>
		<td><div id="elh_image_gallery_img_new_id" class="image_gallery_img_new_id"><div class="ewTableHeaderCaption"><?php echo $image_gallery->img_new_id->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $image_gallery->SortUrl($image_gallery->img_new_id) ?>',1);"><div id="elh_image_gallery_img_new_id" class="image_gallery_img_new_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $image_gallery->img_new_id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($image_gallery->img_new_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($image_gallery->img_new_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($image_gallery->img_offer_id->Visible) { // img_offer_id ?>
	<?php if ($image_gallery->SortUrl($image_gallery->img_offer_id) == "") { ?>
		<td><div id="elh_image_gallery_img_offer_id" class="image_gallery_img_offer_id"><div class="ewTableHeaderCaption"><?php echo $image_gallery->img_offer_id->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $image_gallery->SortUrl($image_gallery->img_offer_id) ?>',1);"><div id="elh_image_gallery_img_offer_id" class="image_gallery_img_offer_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $image_gallery->img_offer_id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($image_gallery->img_offer_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($image_gallery->img_offer_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($image_gallery->img_nam_archive->Visible) { // img_nam_archive ?>
	<?php if ($image_gallery->SortUrl($image_gallery->img_nam_archive) == "") { ?>
		<td><div id="elh_image_gallery_img_nam_archive" class="image_gallery_img_nam_archive"><div class="ewTableHeaderCaption"><?php echo $image_gallery->img_nam_archive->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $image_gallery->SortUrl($image_gallery->img_nam_archive) ?>',1);"><div id="elh_image_gallery_img_nam_archive" class="image_gallery_img_nam_archive">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $image_gallery->img_nam_archive->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($image_gallery->img_nam_archive->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($image_gallery->img_nam_archive->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($image_gallery->img_is_banner->Visible) { // img_is_banner ?>
	<?php if ($image_gallery->SortUrl($image_gallery->img_is_banner) == "") { ?>
		<td><div id="elh_image_gallery_img_is_banner" class="image_gallery_img_is_banner"><div class="ewTableHeaderCaption"><?php echo $image_gallery->img_is_banner->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $image_gallery->SortUrl($image_gallery->img_is_banner) ?>',1);"><div id="elh_image_gallery_img_is_banner" class="image_gallery_img_is_banner">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $image_gallery->img_is_banner->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($image_gallery->img_is_banner->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($image_gallery->img_is_banner->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($image_gallery->img_order->Visible) { // img_order ?>
	<?php if ($image_gallery->SortUrl($image_gallery->img_order) == "") { ?>
		<td><div id="elh_image_gallery_img_order" class="image_gallery_img_order"><div class="ewTableHeaderCaption"><?php echo $image_gallery->img_order->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $image_gallery->SortUrl($image_gallery->img_order) ?>',1);"><div id="elh_image_gallery_img_order" class="image_gallery_img_order">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $image_gallery->img_order->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($image_gallery->img_order->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($image_gallery->img_order->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($image_gallery->img_status->Visible) { // img_status ?>
	<?php if ($image_gallery->SortUrl($image_gallery->img_status) == "") { ?>
		<td><div id="elh_image_gallery_img_status" class="image_gallery_img_status"><div class="ewTableHeaderCaption"><?php echo $image_gallery->img_status->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $image_gallery->SortUrl($image_gallery->img_status) ?>',1);"><div id="elh_image_gallery_img_status" class="image_gallery_img_status">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $image_gallery->img_status->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($image_gallery->img_status->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($image_gallery->img_status->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$image_gallery_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($image_gallery->ExportAll && $image_gallery->Export <> "") {
	$image_gallery_list->StopRec = $image_gallery_list->TotalRecs;
} else {

	// Set the last record to display
	if ($image_gallery_list->TotalRecs > $image_gallery_list->StartRec + $image_gallery_list->DisplayRecs - 1)
		$image_gallery_list->StopRec = $image_gallery_list->StartRec + $image_gallery_list->DisplayRecs - 1;
	else
		$image_gallery_list->StopRec = $image_gallery_list->TotalRecs;
}
$image_gallery_list->RecCnt = $image_gallery_list->StartRec - 1;
if ($image_gallery_list->Recordset && !$image_gallery_list->Recordset->EOF) {
	$image_gallery_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $image_gallery_list->StartRec > 1)
		$image_gallery_list->Recordset->Move($image_gallery_list->StartRec - 1);
} elseif (!$image_gallery->AllowAddDeleteRow && $image_gallery_list->StopRec == 0) {
	$image_gallery_list->StopRec = $image_gallery->GridAddRowCount;
}

// Initialize aggregate
$image_gallery->RowType = EW_ROWTYPE_AGGREGATEINIT;
$image_gallery->ResetAttrs();
$image_gallery_list->RenderRow();
while ($image_gallery_list->RecCnt < $image_gallery_list->StopRec) {
	$image_gallery_list->RecCnt++;
	if (intval($image_gallery_list->RecCnt) >= intval($image_gallery_list->StartRec)) {
		$image_gallery_list->RowCnt++;

		// Set up key count
		$image_gallery_list->KeyCount = $image_gallery_list->RowIndex;

		// Init row class and style
		$image_gallery->ResetAttrs();
		$image_gallery->CssClass = "";
		if ($image_gallery->CurrentAction == "gridadd") {
		} else {
			$image_gallery_list->LoadRowValues($image_gallery_list->Recordset); // Load row values
		}
		$image_gallery->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$image_gallery->RowAttrs = array_merge($image_gallery->RowAttrs, array('data-rowindex'=>$image_gallery_list->RowCnt, 'id'=>'r' . $image_gallery_list->RowCnt . '_image_gallery', 'data-rowtype'=>$image_gallery->RowType));

		// Render row
		$image_gallery_list->RenderRow();

		// Render list options
		$image_gallery_list->RenderListOptions();
?>
	<tr<?php echo $image_gallery->RowAttributes() ?>>
<?php

// Render list options (body, left)
$image_gallery_list->ListOptions->Render("body", "left", $image_gallery_list->RowCnt);
?>
	<?php if ($image_gallery->img_id->Visible) { // img_id ?>
		<td<?php echo $image_gallery->img_id->CellAttributes() ?>>
<span<?php echo $image_gallery->img_id->ViewAttributes() ?>>
<?php echo $image_gallery->img_id->ListViewValue() ?></span>
<a id="<?php echo $image_gallery_list->PageObjName . "_row_" . $image_gallery_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($image_gallery->img_path->Visible) { // img_path ?>
		<td<?php echo $image_gallery->img_path->CellAttributes() ?>>
<span<?php echo $image_gallery->img_path->ViewAttributes() ?>>
<?php
$Files = explode(",", $image_gallery->img_path->Upload->DbValue);
$HrefValue = $image_gallery->img_path->HrefValue;
$FileCount = count($Files);
for ($i = 0; $i < $FileCount; $i++) {
if ($Files[$i] <> "") {
$image_gallery->img_path->ViewValue = $Files[$i];
$image_gallery->img_path->HrefValue = str_replace("%u", ew_HtmlEncode(ew_UploadPathEx(FALSE, $image_gallery->img_path->UploadPath) . $Files[$i]), $HrefValue);
$Files[$i] = str_replace("%f", ew_HtmlEncode(ew_UploadPathEx(FALSE, $image_gallery->img_path->UploadPath) . $Files[$i]), $image_gallery->img_path->ListViewValue());
?>
<?php if ($image_gallery->img_path->LinkAttributes() <> "") { ?>
<?php if (!empty($image_gallery->img_path->Upload->DbValue)) { ?>
<?php echo $image_gallery->img_path->ListViewValue() ?>
<?php } elseif (!in_array($image_gallery->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($image_gallery->img_path->Upload->DbValue)) { ?>
<?php echo $image_gallery->img_path->ListViewValue() ?>
<?php } elseif (!in_array($image_gallery->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
<?php
}
}
?>
</span>
</td>
	<?php } ?>
	<?php if ($image_gallery->img_description->Visible) { // img_description ?>
		<td<?php echo $image_gallery->img_description->CellAttributes() ?>>
<span<?php echo $image_gallery->img_description->ViewAttributes() ?>>
<?php echo $image_gallery->img_description->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($image_gallery->img_cat_id->Visible) { // img_cat_id ?>
		<td<?php echo $image_gallery->img_cat_id->CellAttributes() ?>>
<span<?php echo $image_gallery->img_cat_id->ViewAttributes() ?>>
<?php echo $image_gallery->img_cat_id->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($image_gallery->img_new_id->Visible) { // img_new_id ?>
		<td<?php echo $image_gallery->img_new_id->CellAttributes() ?>>
<span<?php echo $image_gallery->img_new_id->ViewAttributes() ?>>
<?php echo $image_gallery->img_new_id->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($image_gallery->img_offer_id->Visible) { // img_offer_id ?>
		<td<?php echo $image_gallery->img_offer_id->CellAttributes() ?>>
<span<?php echo $image_gallery->img_offer_id->ViewAttributes() ?>>
<?php echo $image_gallery->img_offer_id->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($image_gallery->img_nam_archive->Visible) { // img_nam_archive ?>
		<td<?php echo $image_gallery->img_nam_archive->CellAttributes() ?>>
<span<?php echo $image_gallery->img_nam_archive->ViewAttributes() ?>>
<?php echo $image_gallery->img_nam_archive->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($image_gallery->img_is_banner->Visible) { // img_is_banner ?>
		<td<?php echo $image_gallery->img_is_banner->CellAttributes() ?>>
<span<?php echo $image_gallery->img_is_banner->ViewAttributes() ?>>
<?php echo $image_gallery->img_is_banner->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($image_gallery->img_order->Visible) { // img_order ?>
		<td<?php echo $image_gallery->img_order->CellAttributes() ?>>
<span<?php echo $image_gallery->img_order->ViewAttributes() ?>>
<?php echo $image_gallery->img_order->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($image_gallery->img_status->Visible) { // img_status ?>
		<td<?php echo $image_gallery->img_status->CellAttributes() ?>>
<span<?php echo $image_gallery->img_status->ViewAttributes() ?>>
<?php echo $image_gallery->img_status->ListViewValue() ?></span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$image_gallery_list->ListOptions->Render("body", "right", $image_gallery_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($image_gallery->CurrentAction <> "gridadd")
		$image_gallery_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($image_gallery->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($image_gallery_list->Recordset)
	$image_gallery_list->Recordset->Close();
?>
<?php if ($image_gallery_list->TotalRecs > 0) { ?>
<div class="ewGridLowerPanel">
<?php if ($image_gallery->CurrentAction <> "gridadd" && $image_gallery->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($image_gallery_list->Pager)) $image_gallery_list->Pager = new cPrevNextPager($image_gallery_list->StartRec, $image_gallery_list->DisplayRecs, $image_gallery_list->TotalRecs) ?>
<?php if ($image_gallery_list->Pager->RecordCount > 0) { ?>
<table class="ewStdTable"><tbody><tr><td>
	<?php echo $Language->Phrase("Page") ?>&nbsp;
<div class="input-prepend input-append">
<!--first page button-->
	<?php if ($image_gallery_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $image_gallery_list->PageUrl() ?>start=<?php echo $image_gallery_list->Pager->FirstButton->Start ?>"><i class="icon-step-backward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-step-backward"></i></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($image_gallery_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $image_gallery_list->PageUrl() ?>start=<?php echo $image_gallery_list->Pager->PrevButton->Start ?>"><i class="icon-prev"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-prev"></i></a>
	<?php } ?>
<!--current page number-->
	<input class="input-mini" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $image_gallery_list->Pager->CurrentPage ?>">
<!--next page button-->
	<?php if ($image_gallery_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $image_gallery_list->PageUrl() ?>start=<?php echo $image_gallery_list->Pager->NextButton->Start ?>"><i class="icon-play"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-play"></i></a>
	<?php } ?>
<!--last page button-->
	<?php if ($image_gallery_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $image_gallery_list->PageUrl() ?>start=<?php echo $image_gallery_list->Pager->LastButton->Start ?>"><i class="icon-step-forward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-step-forward"></i></a>
	<?php } ?>
</div>
	&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $image_gallery_list->Pager->PageCount ?>
</td>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $image_gallery_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $image_gallery_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $image_gallery_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($image_gallery_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($image_gallery_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
</div>
<?php } ?>
</td></tr></table>
<script type="text/javascript">
fimage_gallerylistsrch.Init();
fimage_gallerylist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$image_gallery_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$image_gallery_list->Page_Terminate();
?>
