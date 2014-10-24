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

$offers_list = NULL; // Initialize page object first

class coffers_list extends coffers {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'offers';

	// Page object name
	var $PageObjName = 'offers_list';

	// Grid form hidden field names
	var $FormName = 'fofferslist';
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

		// Table object (offers)
		if (!isset($GLOBALS["offers"]) || get_class($GLOBALS["offers"]) == "coffers") {
			$GLOBALS["offers"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["offers"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "offersadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "offersdelete.php";
		$this->MultiUpdateUrl = "offersupdate.php";

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'offers', TRUE);

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
		$this->offer_id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
			$this->offer_id->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->offer_id->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Return basic search SQL
	function BasicSearchSQL($Keyword) {
		$sKeyword = ew_AdjustSql($Keyword);
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->offer_title, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->offer_description, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->offer_content, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->offer_question_content, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->offer_image_path, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->offer_top_image, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->offer_bottom_image, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->offer_rules, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->offer_value, $Keyword);
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
			$this->UpdateSort($this->offer_id); // offer_id
			$this->UpdateSort($this->offer_title); // offer_title
			$this->UpdateSort($this->offer_description); // offer_description
			$this->UpdateSort($this->offer_image_path); // offer_image_path
			$this->UpdateSort($this->offer_top_image); // offer_top_image
			$this->UpdateSort($this->offer_bottom_image); // offer_bottom_image
			$this->UpdateSort($this->offer_start_date); // offer_start_date
			$this->UpdateSort($this->offer_end_date); // offer_end_date
			$this->UpdateSort($this->offer_start_time); // offer_start_time
			$this->UpdateSort($this->offer_end_time); // offer_end_time
			$this->UpdateSort($this->offer_value); // offer_value
			$this->UpdateSort($this->offer_cat_id); // offer_cat_id
			$this->UpdateSort($this->offer_status); // offer_status
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
				$this->offer_id->setSort("");
				$this->offer_title->setSort("");
				$this->offer_description->setSort("");
				$this->offer_image_path->setSort("");
				$this->offer_top_image->setSort("");
				$this->offer_bottom_image->setSort("");
				$this->offer_start_date->setSort("");
				$this->offer_end_date->setSort("");
				$this->offer_start_time->setSort("");
				$this->offer_end_time->setSort("");
				$this->offer_value->setSort("");
				$this->offer_cat_id->setSort("");
				$this->offer_status->setSort("");
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
		$oListOpt->Body = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->offer_id->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'></label>";
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
				$item->Body = "<a class=\"ewAction ewCustomAction\" href=\"\" onclick=\"ew_SubmitSelected(document.fofferslist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
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
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

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

			// offer_id
			$this->offer_id->LinkCustomAttributes = "";
			$this->offer_id->HrefValue = "";
			$this->offer_id->TooltipValue = "";

			// offer_title
			$this->offer_title->LinkCustomAttributes = "";
			$this->offer_title->HrefValue = "";
			$this->offer_title->TooltipValue = "";

			// offer_description
			$this->offer_description->LinkCustomAttributes = "";
			$this->offer_description->HrefValue = "";
			$this->offer_description->TooltipValue = "";

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
if (!isset($offers_list)) $offers_list = new coffers_list();

// Page init
$offers_list->Page_Init();

// Page main
$offers_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$offers_list->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var offers_list = new ew_Page("offers_list");
offers_list.PageID = "list"; // Page ID
var EW_PAGE_ID = offers_list.PageID; // For backward compatibility

// Form object
var fofferslist = new ew_Form("fofferslist");
fofferslist.FormKeyCountName = '<?php echo $offers_list->FormKeyCountName ?>';

// Form_CustomValidate event
fofferslist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fofferslist.ValidateRequired = true;
<?php } else { ?>
fofferslist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fofferslist.Lists["x_offer_cat_id"] = {"LinkField":"x_cat_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_cat_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
var fofferslistsrch = new ew_Form("fofferslistsrch");
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php if ($offers_list->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $offers_list->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$offers_list->TotalRecs = $offers->SelectRecordCount();
	} else {
		if ($offers_list->Recordset = $offers_list->LoadRecordset())
			$offers_list->TotalRecs = $offers_list->Recordset->RecordCount();
	}
	$offers_list->StartRec = 1;
	if ($offers_list->DisplayRecs <= 0 || ($offers->Export <> "" && $offers->ExportAll)) // Display all records
		$offers_list->DisplayRecs = $offers_list->TotalRecs;
	if (!($offers->Export <> "" && $offers->ExportAll))
		$offers_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$offers_list->Recordset = $offers_list->LoadRecordset($offers_list->StartRec-1, $offers_list->DisplayRecs);
$offers_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($offers->Export == "" && $offers->CurrentAction == "") { ?>
<form name="fofferslistsrch" id="fofferslistsrch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<div class="accordion ewDisplayTable ewSearchTable" id="fofferslistsrch_SearchGroup">
	<div class="accordion-group">
		<div class="accordion-heading">
<a class="accordion-toggle" data-toggle="collapse" data-parent="#fofferslistsrch_SearchGroup" href="#fofferslistsrch_SearchBody"><?php echo $Language->Phrase("Search") ?></a>
		</div>
		<div id="fofferslistsrch_SearchBody" class="accordion-body collapse in">
			<div class="accordion-inner">
<div id="fofferslistsrch_SearchPanel">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="offers">
<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="btn-group ewButtonGroup">
	<div class="input-append">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="input-large" value="<?php echo ew_HtmlEncode($offers_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?></button>
	</div>
	</div>
	<div class="btn-group ewButtonGroup">
	<a class="btn ewShowAll" href="<?php echo $offers_list->PageUrl() ?>cmd=reset"><?php echo $Language->Phrase("ShowAll") ?></a>
	</div>
</div>
<div id="xsr_2" class="ewRow">
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="="<?php if ($offers_list->BasicSearch->getType() == "=") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("ExactPhrase") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND"<?php if ($offers_list->BasicSearch->getType() == "AND") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AllWord") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR"<?php if ($offers_list->BasicSearch->getType() == "OR") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AnyWord") ?></label>
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
<?php $offers_list->ShowPageHeader(); ?>
<?php
$offers_list->ShowMessage();
?>
<table class="ewGrid"><tr><td class="ewGridContent">
<form name="fofferslist" id="fofferslist" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="offers">
<div id="gmp_offers" class="ewGridMiddlePanel">
<?php if ($offers_list->TotalRecs > 0) { ?>
<table id="tbl_offerslist" class="ewTable ewTableSeparate">
<?php echo $offers->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$offers_list->RenderListOptions();

// Render list options (header, left)
$offers_list->ListOptions->Render("header", "left");
?>
<?php if ($offers->offer_id->Visible) { // offer_id ?>
	<?php if ($offers->SortUrl($offers->offer_id) == "") { ?>
		<td><div id="elh_offers_offer_id" class="offers_offer_id"><div class="ewTableHeaderCaption"><?php echo $offers->offer_id->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $offers->SortUrl($offers->offer_id) ?>',1);"><div id="elh_offers_offer_id" class="offers_offer_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $offers->offer_id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($offers->offer_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($offers->offer_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($offers->offer_title->Visible) { // offer_title ?>
	<?php if ($offers->SortUrl($offers->offer_title) == "") { ?>
		<td><div id="elh_offers_offer_title" class="offers_offer_title"><div class="ewTableHeaderCaption"><?php echo $offers->offer_title->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $offers->SortUrl($offers->offer_title) ?>',1);"><div id="elh_offers_offer_title" class="offers_offer_title">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $offers->offer_title->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($offers->offer_title->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($offers->offer_title->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($offers->offer_description->Visible) { // offer_description ?>
	<?php if ($offers->SortUrl($offers->offer_description) == "") { ?>
		<td><div id="elh_offers_offer_description" class="offers_offer_description"><div class="ewTableHeaderCaption"><?php echo $offers->offer_description->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $offers->SortUrl($offers->offer_description) ?>',1);"><div id="elh_offers_offer_description" class="offers_offer_description">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $offers->offer_description->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($offers->offer_description->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($offers->offer_description->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($offers->offer_image_path->Visible) { // offer_image_path ?>
	<?php if ($offers->SortUrl($offers->offer_image_path) == "") { ?>
		<td><div id="elh_offers_offer_image_path" class="offers_offer_image_path"><div class="ewTableHeaderCaption"><?php echo $offers->offer_image_path->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $offers->SortUrl($offers->offer_image_path) ?>',1);"><div id="elh_offers_offer_image_path" class="offers_offer_image_path">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $offers->offer_image_path->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($offers->offer_image_path->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($offers->offer_image_path->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($offers->offer_top_image->Visible) { // offer_top_image ?>
	<?php if ($offers->SortUrl($offers->offer_top_image) == "") { ?>
		<td><div id="elh_offers_offer_top_image" class="offers_offer_top_image"><div class="ewTableHeaderCaption"><?php echo $offers->offer_top_image->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $offers->SortUrl($offers->offer_top_image) ?>',1);"><div id="elh_offers_offer_top_image" class="offers_offer_top_image">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $offers->offer_top_image->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($offers->offer_top_image->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($offers->offer_top_image->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($offers->offer_bottom_image->Visible) { // offer_bottom_image ?>
	<?php if ($offers->SortUrl($offers->offer_bottom_image) == "") { ?>
		<td><div id="elh_offers_offer_bottom_image" class="offers_offer_bottom_image"><div class="ewTableHeaderCaption"><?php echo $offers->offer_bottom_image->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $offers->SortUrl($offers->offer_bottom_image) ?>',1);"><div id="elh_offers_offer_bottom_image" class="offers_offer_bottom_image">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $offers->offer_bottom_image->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($offers->offer_bottom_image->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($offers->offer_bottom_image->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($offers->offer_start_date->Visible) { // offer_start_date ?>
	<?php if ($offers->SortUrl($offers->offer_start_date) == "") { ?>
		<td><div id="elh_offers_offer_start_date" class="offers_offer_start_date"><div class="ewTableHeaderCaption"><?php echo $offers->offer_start_date->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $offers->SortUrl($offers->offer_start_date) ?>',1);"><div id="elh_offers_offer_start_date" class="offers_offer_start_date">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $offers->offer_start_date->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($offers->offer_start_date->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($offers->offer_start_date->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($offers->offer_end_date->Visible) { // offer_end_date ?>
	<?php if ($offers->SortUrl($offers->offer_end_date) == "") { ?>
		<td><div id="elh_offers_offer_end_date" class="offers_offer_end_date"><div class="ewTableHeaderCaption"><?php echo $offers->offer_end_date->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $offers->SortUrl($offers->offer_end_date) ?>',1);"><div id="elh_offers_offer_end_date" class="offers_offer_end_date">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $offers->offer_end_date->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($offers->offer_end_date->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($offers->offer_end_date->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($offers->offer_start_time->Visible) { // offer_start_time ?>
	<?php if ($offers->SortUrl($offers->offer_start_time) == "") { ?>
		<td><div id="elh_offers_offer_start_time" class="offers_offer_start_time"><div class="ewTableHeaderCaption"><?php echo $offers->offer_start_time->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $offers->SortUrl($offers->offer_start_time) ?>',1);"><div id="elh_offers_offer_start_time" class="offers_offer_start_time">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $offers->offer_start_time->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($offers->offer_start_time->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($offers->offer_start_time->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($offers->offer_end_time->Visible) { // offer_end_time ?>
	<?php if ($offers->SortUrl($offers->offer_end_time) == "") { ?>
		<td><div id="elh_offers_offer_end_time" class="offers_offer_end_time"><div class="ewTableHeaderCaption"><?php echo $offers->offer_end_time->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $offers->SortUrl($offers->offer_end_time) ?>',1);"><div id="elh_offers_offer_end_time" class="offers_offer_end_time">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $offers->offer_end_time->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($offers->offer_end_time->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($offers->offer_end_time->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($offers->offer_value->Visible) { // offer_value ?>
	<?php if ($offers->SortUrl($offers->offer_value) == "") { ?>
		<td><div id="elh_offers_offer_value" class="offers_offer_value"><div class="ewTableHeaderCaption"><?php echo $offers->offer_value->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $offers->SortUrl($offers->offer_value) ?>',1);"><div id="elh_offers_offer_value" class="offers_offer_value">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $offers->offer_value->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($offers->offer_value->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($offers->offer_value->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($offers->offer_cat_id->Visible) { // offer_cat_id ?>
	<?php if ($offers->SortUrl($offers->offer_cat_id) == "") { ?>
		<td><div id="elh_offers_offer_cat_id" class="offers_offer_cat_id"><div class="ewTableHeaderCaption"><?php echo $offers->offer_cat_id->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $offers->SortUrl($offers->offer_cat_id) ?>',1);"><div id="elh_offers_offer_cat_id" class="offers_offer_cat_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $offers->offer_cat_id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($offers->offer_cat_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($offers->offer_cat_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($offers->offer_status->Visible) { // offer_status ?>
	<?php if ($offers->SortUrl($offers->offer_status) == "") { ?>
		<td><div id="elh_offers_offer_status" class="offers_offer_status"><div class="ewTableHeaderCaption"><?php echo $offers->offer_status->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $offers->SortUrl($offers->offer_status) ?>',1);"><div id="elh_offers_offer_status" class="offers_offer_status">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $offers->offer_status->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($offers->offer_status->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($offers->offer_status->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$offers_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($offers->ExportAll && $offers->Export <> "") {
	$offers_list->StopRec = $offers_list->TotalRecs;
} else {

	// Set the last record to display
	if ($offers_list->TotalRecs > $offers_list->StartRec + $offers_list->DisplayRecs - 1)
		$offers_list->StopRec = $offers_list->StartRec + $offers_list->DisplayRecs - 1;
	else
		$offers_list->StopRec = $offers_list->TotalRecs;
}
$offers_list->RecCnt = $offers_list->StartRec - 1;
if ($offers_list->Recordset && !$offers_list->Recordset->EOF) {
	$offers_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $offers_list->StartRec > 1)
		$offers_list->Recordset->Move($offers_list->StartRec - 1);
} elseif (!$offers->AllowAddDeleteRow && $offers_list->StopRec == 0) {
	$offers_list->StopRec = $offers->GridAddRowCount;
}

// Initialize aggregate
$offers->RowType = EW_ROWTYPE_AGGREGATEINIT;
$offers->ResetAttrs();
$offers_list->RenderRow();
while ($offers_list->RecCnt < $offers_list->StopRec) {
	$offers_list->RecCnt++;
	if (intval($offers_list->RecCnt) >= intval($offers_list->StartRec)) {
		$offers_list->RowCnt++;

		// Set up key count
		$offers_list->KeyCount = $offers_list->RowIndex;

		// Init row class and style
		$offers->ResetAttrs();
		$offers->CssClass = "";
		if ($offers->CurrentAction == "gridadd") {
		} else {
			$offers_list->LoadRowValues($offers_list->Recordset); // Load row values
		}
		$offers->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$offers->RowAttrs = array_merge($offers->RowAttrs, array('data-rowindex'=>$offers_list->RowCnt, 'id'=>'r' . $offers_list->RowCnt . '_offers', 'data-rowtype'=>$offers->RowType));

		// Render row
		$offers_list->RenderRow();

		// Render list options
		$offers_list->RenderListOptions();
?>
	<tr<?php echo $offers->RowAttributes() ?>>
<?php

// Render list options (body, left)
$offers_list->ListOptions->Render("body", "left", $offers_list->RowCnt);
?>
	<?php if ($offers->offer_id->Visible) { // offer_id ?>
		<td<?php echo $offers->offer_id->CellAttributes() ?>>
<span<?php echo $offers->offer_id->ViewAttributes() ?>>
<?php echo $offers->offer_id->ListViewValue() ?></span>
<a id="<?php echo $offers_list->PageObjName . "_row_" . $offers_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($offers->offer_title->Visible) { // offer_title ?>
		<td<?php echo $offers->offer_title->CellAttributes() ?>>
<span<?php echo $offers->offer_title->ViewAttributes() ?>>
<?php echo $offers->offer_title->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($offers->offer_description->Visible) { // offer_description ?>
		<td<?php echo $offers->offer_description->CellAttributes() ?>>
<span<?php echo $offers->offer_description->ViewAttributes() ?>>
<?php echo $offers->offer_description->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($offers->offer_image_path->Visible) { // offer_image_path ?>
		<td<?php echo $offers->offer_image_path->CellAttributes() ?>>
<span>
<?php if ($offers->offer_image_path->LinkAttributes() <> "") { ?>
<?php if (!empty($offers->offer_image_path->Upload->DbValue)) { ?>
<?php echo ew_GetFileViewTag($offers->offer_image_path, $offers->offer_image_path->ListViewValue()) ?>
<?php } elseif (!in_array($offers->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($offers->offer_image_path->Upload->DbValue)) { ?>
<?php echo ew_GetFileViewTag($offers->offer_image_path, $offers->offer_image_path->ListViewValue()) ?>
<?php } elseif (!in_array($offers->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</td>
	<?php } ?>
	<?php if ($offers->offer_top_image->Visible) { // offer_top_image ?>
		<td<?php echo $offers->offer_top_image->CellAttributes() ?>>
<span>
<?php if ($offers->offer_top_image->LinkAttributes() <> "") { ?>
<?php if (!empty($offers->offer_top_image->Upload->DbValue)) { ?>
<?php echo ew_GetFileViewTag($offers->offer_top_image, $offers->offer_top_image->ListViewValue()) ?>
<?php } elseif (!in_array($offers->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($offers->offer_top_image->Upload->DbValue)) { ?>
<?php echo ew_GetFileViewTag($offers->offer_top_image, $offers->offer_top_image->ListViewValue()) ?>
<?php } elseif (!in_array($offers->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</td>
	<?php } ?>
	<?php if ($offers->offer_bottom_image->Visible) { // offer_bottom_image ?>
		<td<?php echo $offers->offer_bottom_image->CellAttributes() ?>>
<span>
<?php if ($offers->offer_bottom_image->LinkAttributes() <> "") { ?>
<?php if (!empty($offers->offer_bottom_image->Upload->DbValue)) { ?>
<?php echo ew_GetFileViewTag($offers->offer_bottom_image, $offers->offer_bottom_image->ListViewValue()) ?>
<?php } elseif (!in_array($offers->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($offers->offer_bottom_image->Upload->DbValue)) { ?>
<?php echo ew_GetFileViewTag($offers->offer_bottom_image, $offers->offer_bottom_image->ListViewValue()) ?>
<?php } elseif (!in_array($offers->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</td>
	<?php } ?>
	<?php if ($offers->offer_start_date->Visible) { // offer_start_date ?>
		<td<?php echo $offers->offer_start_date->CellAttributes() ?>>
<span<?php echo $offers->offer_start_date->ViewAttributes() ?>>
<?php echo $offers->offer_start_date->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($offers->offer_end_date->Visible) { // offer_end_date ?>
		<td<?php echo $offers->offer_end_date->CellAttributes() ?>>
<span<?php echo $offers->offer_end_date->ViewAttributes() ?>>
<?php echo $offers->offer_end_date->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($offers->offer_start_time->Visible) { // offer_start_time ?>
		<td<?php echo $offers->offer_start_time->CellAttributes() ?>>
<span<?php echo $offers->offer_start_time->ViewAttributes() ?>>
<?php echo $offers->offer_start_time->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($offers->offer_end_time->Visible) { // offer_end_time ?>
		<td<?php echo $offers->offer_end_time->CellAttributes() ?>>
<span<?php echo $offers->offer_end_time->ViewAttributes() ?>>
<?php echo $offers->offer_end_time->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($offers->offer_value->Visible) { // offer_value ?>
		<td<?php echo $offers->offer_value->CellAttributes() ?>>
<span<?php echo $offers->offer_value->ViewAttributes() ?>>
<?php echo $offers->offer_value->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($offers->offer_cat_id->Visible) { // offer_cat_id ?>
		<td<?php echo $offers->offer_cat_id->CellAttributes() ?>>
<span<?php echo $offers->offer_cat_id->ViewAttributes() ?>>
<?php echo $offers->offer_cat_id->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($offers->offer_status->Visible) { // offer_status ?>
		<td<?php echo $offers->offer_status->CellAttributes() ?>>
<span<?php echo $offers->offer_status->ViewAttributes() ?>>
<?php echo $offers->offer_status->ListViewValue() ?></span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$offers_list->ListOptions->Render("body", "right", $offers_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($offers->CurrentAction <> "gridadd")
		$offers_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($offers->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($offers_list->Recordset)
	$offers_list->Recordset->Close();
?>
<div class="ewGridLowerPanel">
<?php if ($offers->CurrentAction <> "gridadd" && $offers->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($offers_list->Pager)) $offers_list->Pager = new cPrevNextPager($offers_list->StartRec, $offers_list->DisplayRecs, $offers_list->TotalRecs) ?>
<?php if ($offers_list->Pager->RecordCount > 0) { ?>
<table class="ewStdTable"><tbody><tr><td>
	<?php echo $Language->Phrase("Page") ?>&nbsp;
<div class="input-prepend input-append">
<!--first page button-->
	<?php if ($offers_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $offers_list->PageUrl() ?>start=<?php echo $offers_list->Pager->FirstButton->Start ?>"><i class="icon-step-backward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-step-backward"></i></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($offers_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $offers_list->PageUrl() ?>start=<?php echo $offers_list->Pager->PrevButton->Start ?>"><i class="icon-prev"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-prev"></i></a>
	<?php } ?>
<!--current page number-->
	<input class="input-mini" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $offers_list->Pager->CurrentPage ?>">
<!--next page button-->
	<?php if ($offers_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $offers_list->PageUrl() ?>start=<?php echo $offers_list->Pager->NextButton->Start ?>"><i class="icon-play"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-play"></i></a>
	<?php } ?>
<!--last page button-->
	<?php if ($offers_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $offers_list->PageUrl() ?>start=<?php echo $offers_list->Pager->LastButton->Start ?>"><i class="icon-step-forward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-step-forward"></i></a>
	<?php } ?>
</div>
	&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $offers_list->Pager->PageCount ?>
</td>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $offers_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $offers_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $offers_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($offers_list->SearchWhere == "0=101") { ?>
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
	foreach ($offers_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
</div>
</td></tr></table>
<script type="text/javascript">
fofferslistsrch.Init();
fofferslist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$offers_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$offers_list->Page_Terminate();
?>
