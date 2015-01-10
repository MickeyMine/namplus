<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "categoriesinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$categories_list = NULL; // Initialize page object first

class ccategories_list extends ccategories {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'categories';

	// Page object name
	var $PageObjName = 'categories_list';

	// Grid form hidden field names
	var $FormName = 'fcategorieslist';
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

		// Table object (categories)
		if (!isset($GLOBALS["categories"]) || get_class($GLOBALS["categories"]) == "ccategories") {
			$GLOBALS["categories"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["categories"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "categoriesadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "categoriesdelete.php";
		$this->MultiUpdateUrl = "categoriesupdate.php";

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'categories', TRUE);

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
		$this->cat_id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
			$this->cat_id->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->cat_id->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Return basic search SQL
	function BasicSearchSQL($Keyword) {
		$sKeyword = ew_AdjustSql($Keyword);
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->cat_name, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->cat_description, $Keyword);
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
			$this->UpdateSort($this->cat_id); // cat_id
			$this->UpdateSort($this->cat_name); // cat_name
			$this->UpdateSort($this->cat_description); // cat_description
			$this->UpdateSort($this->cat_parent_id); // cat_parent_id
			$this->UpdateSort($this->cat_is_gallery); // cat_is_gallery
			$this->UpdateSort($this->cat_is_offer); // cat_is_offer
			$this->UpdateSort($this->cat_is_competition); // cat_is_competition
			$this->UpdateSort($this->cat_order); // cat_order
			$this->UpdateSort($this->cat_status); // cat_status
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
				$this->cat_id->setSort("DESC");
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
				$this->cat_id->setSort("");
				$this->cat_name->setSort("");
				$this->cat_description->setSort("");
				$this->cat_parent_id->setSort("");
				$this->cat_is_gallery->setSort("");
				$this->cat_is_offer->setSort("");
				$this->cat_is_competition->setSort("");
				$this->cat_order->setSort("");
				$this->cat_status->setSort("");
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
		$oListOpt->Body = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->cat_id->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'></label>";
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
				$item->Body = "<a class=\"ewAction ewCustomAction\" href=\"\" onclick=\"ew_SubmitSelected(document.fcategorieslist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
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
		$this->cat_id->setDbValue($rs->fields('cat_id'));
		$this->cat_name->setDbValue($rs->fields('cat_name'));
		$this->cat_description->setDbValue($rs->fields('cat_description'));
		$this->cat_parent_id->setDbValue($rs->fields('cat_parent_id'));
		$this->cat_is_gallery->setDbValue($rs->fields('cat_is_gallery'));
		$this->cat_is_offer->setDbValue($rs->fields('cat_is_offer'));
		$this->cat_is_competition->setDbValue($rs->fields('cat_is_competition'));
		$this->cat_order->setDbValue($rs->fields('cat_order'));
		$this->cat_status->setDbValue($rs->fields('cat_status'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->cat_id->DbValue = $row['cat_id'];
		$this->cat_name->DbValue = $row['cat_name'];
		$this->cat_description->DbValue = $row['cat_description'];
		$this->cat_parent_id->DbValue = $row['cat_parent_id'];
		$this->cat_is_gallery->DbValue = $row['cat_is_gallery'];
		$this->cat_is_offer->DbValue = $row['cat_is_offer'];
		$this->cat_is_competition->DbValue = $row['cat_is_competition'];
		$this->cat_order->DbValue = $row['cat_order'];
		$this->cat_status->DbValue = $row['cat_status'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("cat_id")) <> "")
			$this->cat_id->CurrentValue = $this->getKey("cat_id"); // cat_id
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
		// cat_id
		// cat_name
		// cat_description
		// cat_parent_id
		// cat_is_gallery
		// cat_is_offer
		// cat_is_competition
		// cat_order
		// cat_status

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// cat_id
			$this->cat_id->ViewValue = $this->cat_id->CurrentValue;
			$this->cat_id->ViewCustomAttributes = "";

			// cat_name
			$this->cat_name->ViewValue = $this->cat_name->CurrentValue;
			$this->cat_name->ViewCustomAttributes = "";

			// cat_description
			$this->cat_description->ViewValue = $this->cat_description->CurrentValue;
			$this->cat_description->ViewCustomAttributes = "";

			// cat_parent_id
			if (strval($this->cat_parent_id->CurrentValue) <> "") {
				$sFilterWrk = "`cat_id`" . ew_SearchString("=", $this->cat_parent_id->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `cat_id`, `cat_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `categories`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->cat_parent_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->cat_parent_id->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->cat_parent_id->ViewValue = $this->cat_parent_id->CurrentValue;
				}
			} else {
				$this->cat_parent_id->ViewValue = NULL;
			}
			$this->cat_parent_id->ViewCustomAttributes = "";

			// cat_is_gallery
			if (strval($this->cat_is_gallery->CurrentValue) <> "") {
				switch ($this->cat_is_gallery->CurrentValue) {
					case $this->cat_is_gallery->FldTagValue(1):
						$this->cat_is_gallery->ViewValue = $this->cat_is_gallery->FldTagCaption(1) <> "" ? $this->cat_is_gallery->FldTagCaption(1) : $this->cat_is_gallery->CurrentValue;
						break;
					case $this->cat_is_gallery->FldTagValue(2):
						$this->cat_is_gallery->ViewValue = $this->cat_is_gallery->FldTagCaption(2) <> "" ? $this->cat_is_gallery->FldTagCaption(2) : $this->cat_is_gallery->CurrentValue;
						break;
					default:
						$this->cat_is_gallery->ViewValue = $this->cat_is_gallery->CurrentValue;
				}
			} else {
				$this->cat_is_gallery->ViewValue = NULL;
			}
			$this->cat_is_gallery->ViewCustomAttributes = "";

			// cat_is_offer
			if (strval($this->cat_is_offer->CurrentValue) <> "") {
				switch ($this->cat_is_offer->CurrentValue) {
					case $this->cat_is_offer->FldTagValue(1):
						$this->cat_is_offer->ViewValue = $this->cat_is_offer->FldTagCaption(1) <> "" ? $this->cat_is_offer->FldTagCaption(1) : $this->cat_is_offer->CurrentValue;
						break;
					case $this->cat_is_offer->FldTagValue(2):
						$this->cat_is_offer->ViewValue = $this->cat_is_offer->FldTagCaption(2) <> "" ? $this->cat_is_offer->FldTagCaption(2) : $this->cat_is_offer->CurrentValue;
						break;
					default:
						$this->cat_is_offer->ViewValue = $this->cat_is_offer->CurrentValue;
				}
			} else {
				$this->cat_is_offer->ViewValue = NULL;
			}
			$this->cat_is_offer->ViewCustomAttributes = "";

			// cat_is_competition
			if (strval($this->cat_is_competition->CurrentValue) <> "") {
				switch ($this->cat_is_competition->CurrentValue) {
					case $this->cat_is_competition->FldTagValue(1):
						$this->cat_is_competition->ViewValue = $this->cat_is_competition->FldTagCaption(1) <> "" ? $this->cat_is_competition->FldTagCaption(1) : $this->cat_is_competition->CurrentValue;
						break;
					case $this->cat_is_competition->FldTagValue(2):
						$this->cat_is_competition->ViewValue = $this->cat_is_competition->FldTagCaption(2) <> "" ? $this->cat_is_competition->FldTagCaption(2) : $this->cat_is_competition->CurrentValue;
						break;
					default:
						$this->cat_is_competition->ViewValue = $this->cat_is_competition->CurrentValue;
				}
			} else {
				$this->cat_is_competition->ViewValue = NULL;
			}
			$this->cat_is_competition->ViewCustomAttributes = "";

			// cat_order
			$this->cat_order->ViewValue = $this->cat_order->CurrentValue;
			$this->cat_order->ViewValue = ew_FormatNumber($this->cat_order->ViewValue, 0, -2, -2, -2);
			$this->cat_order->ViewCustomAttributes = "";

			// cat_status
			if (strval($this->cat_status->CurrentValue) <> "") {
				switch ($this->cat_status->CurrentValue) {
					case $this->cat_status->FldTagValue(1):
						$this->cat_status->ViewValue = $this->cat_status->FldTagCaption(1) <> "" ? $this->cat_status->FldTagCaption(1) : $this->cat_status->CurrentValue;
						break;
					case $this->cat_status->FldTagValue(2):
						$this->cat_status->ViewValue = $this->cat_status->FldTagCaption(2) <> "" ? $this->cat_status->FldTagCaption(2) : $this->cat_status->CurrentValue;
						break;
					default:
						$this->cat_status->ViewValue = $this->cat_status->CurrentValue;
				}
			} else {
				$this->cat_status->ViewValue = NULL;
			}
			$this->cat_status->ViewCustomAttributes = "";

			// cat_id
			$this->cat_id->LinkCustomAttributes = "";
			$this->cat_id->HrefValue = "";
			$this->cat_id->TooltipValue = "";

			// cat_name
			$this->cat_name->LinkCustomAttributes = "";
			$this->cat_name->HrefValue = "";
			$this->cat_name->TooltipValue = "";

			// cat_description
			$this->cat_description->LinkCustomAttributes = "";
			$this->cat_description->HrefValue = "";
			$this->cat_description->TooltipValue = "";

			// cat_parent_id
			$this->cat_parent_id->LinkCustomAttributes = "";
			$this->cat_parent_id->HrefValue = "";
			$this->cat_parent_id->TooltipValue = "";

			// cat_is_gallery
			$this->cat_is_gallery->LinkCustomAttributes = "";
			$this->cat_is_gallery->HrefValue = "";
			$this->cat_is_gallery->TooltipValue = "";

			// cat_is_offer
			$this->cat_is_offer->LinkCustomAttributes = "";
			$this->cat_is_offer->HrefValue = "";
			$this->cat_is_offer->TooltipValue = "";

			// cat_is_competition
			$this->cat_is_competition->LinkCustomAttributes = "";
			$this->cat_is_competition->HrefValue = "";
			$this->cat_is_competition->TooltipValue = "";

			// cat_order
			$this->cat_order->LinkCustomAttributes = "";
			$this->cat_order->HrefValue = "";
			$this->cat_order->TooltipValue = "";

			// cat_status
			$this->cat_status->LinkCustomAttributes = "";
			$this->cat_status->HrefValue = "";
			$this->cat_status->TooltipValue = "";
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
if (!isset($categories_list)) $categories_list = new ccategories_list();

// Page init
$categories_list->Page_Init();

// Page main
$categories_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$categories_list->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var categories_list = new ew_Page("categories_list");
categories_list.PageID = "list"; // Page ID
var EW_PAGE_ID = categories_list.PageID; // For backward compatibility

// Form object
var fcategorieslist = new ew_Form("fcategorieslist");
fcategorieslist.FormKeyCountName = '<?php echo $categories_list->FormKeyCountName ?>';

// Form_CustomValidate event
fcategorieslist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcategorieslist.ValidateRequired = true;
<?php } else { ?>
fcategorieslist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fcategorieslist.Lists["x_cat_parent_id"] = {"LinkField":"x_cat_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_cat_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
var fcategorieslistsrch = new ew_Form("fcategorieslistsrch");
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php if ($categories_list->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $categories_list->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$categories_list->TotalRecs = $categories->SelectRecordCount();
	} else {
		if ($categories_list->Recordset = $categories_list->LoadRecordset())
			$categories_list->TotalRecs = $categories_list->Recordset->RecordCount();
	}
	$categories_list->StartRec = 1;
	if ($categories_list->DisplayRecs <= 0 || ($categories->Export <> "" && $categories->ExportAll)) // Display all records
		$categories_list->DisplayRecs = $categories_list->TotalRecs;
	if (!($categories->Export <> "" && $categories->ExportAll))
		$categories_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$categories_list->Recordset = $categories_list->LoadRecordset($categories_list->StartRec-1, $categories_list->DisplayRecs);
$categories_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($categories->Export == "" && $categories->CurrentAction == "") { ?>
<form name="fcategorieslistsrch" id="fcategorieslistsrch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<div class="accordion ewDisplayTable ewSearchTable" id="fcategorieslistsrch_SearchGroup">
	<div class="accordion-group">
		<div class="accordion-heading">
<a class="accordion-toggle" data-toggle="collapse" data-parent="#fcategorieslistsrch_SearchGroup" href="#fcategorieslistsrch_SearchBody"><?php echo $Language->Phrase("Search") ?></a>
		</div>
		<div id="fcategorieslistsrch_SearchBody" class="accordion-body collapse in">
			<div class="accordion-inner">
<div id="fcategorieslistsrch_SearchPanel">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="categories">
<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="btn-group ewButtonGroup">
	<div class="input-append">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="input-large" value="<?php echo ew_HtmlEncode($categories_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?></button>
	</div>
	</div>
	<div class="btn-group ewButtonGroup">
	<a class="btn ewShowAll" href="<?php echo $categories_list->PageUrl() ?>cmd=reset"><?php echo $Language->Phrase("ShowAll") ?></a>
	</div>
</div>
<div id="xsr_2" class="ewRow">
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="="<?php if ($categories_list->BasicSearch->getType() == "=") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("ExactPhrase") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND"<?php if ($categories_list->BasicSearch->getType() == "AND") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AllWord") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR"<?php if ($categories_list->BasicSearch->getType() == "OR") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AnyWord") ?></label>
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
<?php $categories_list->ShowPageHeader(); ?>
<?php
$categories_list->ShowMessage();
?>
<table class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridUpperPanel">
<?php if ($categories->CurrentAction <> "gridadd" && $categories->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($categories_list->Pager)) $categories_list->Pager = new cPrevNextPager($categories_list->StartRec, $categories_list->DisplayRecs, $categories_list->TotalRecs) ?>
<?php if ($categories_list->Pager->RecordCount > 0) { ?>
<table class="ewStdTable"><tbody><tr><td>
	<?php echo $Language->Phrase("Page") ?>&nbsp;
<div class="input-prepend input-append">
<!--first page button-->
	<?php if ($categories_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $categories_list->PageUrl() ?>start=<?php echo $categories_list->Pager->FirstButton->Start ?>"><i class="icon-step-backward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-step-backward"></i></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($categories_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $categories_list->PageUrl() ?>start=<?php echo $categories_list->Pager->PrevButton->Start ?>"><i class="icon-prev"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-prev"></i></a>
	<?php } ?>
<!--current page number-->
	<input class="input-mini" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $categories_list->Pager->CurrentPage ?>">
<!--next page button-->
	<?php if ($categories_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $categories_list->PageUrl() ?>start=<?php echo $categories_list->Pager->NextButton->Start ?>"><i class="icon-play"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-play"></i></a>
	<?php } ?>
<!--last page button-->
	<?php if ($categories_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $categories_list->PageUrl() ?>start=<?php echo $categories_list->Pager->LastButton->Start ?>"><i class="icon-step-forward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-step-forward"></i></a>
	<?php } ?>
</div>
	&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $categories_list->Pager->PageCount ?>
</td>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $categories_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $categories_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $categories_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($categories_list->SearchWhere == "0=101") { ?>
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
	foreach ($categories_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
</div>
<form name="fcategorieslist" id="fcategorieslist" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="categories">
<div id="gmp_categories" class="ewGridMiddlePanel">
<?php if ($categories_list->TotalRecs > 0) { ?>
<table id="tbl_categorieslist" class="ewTable ewTableSeparate">
<?php echo $categories->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$categories_list->RenderListOptions();

// Render list options (header, left)
$categories_list->ListOptions->Render("header", "left");
?>
<?php if ($categories->cat_id->Visible) { // cat_id ?>
	<?php if ($categories->SortUrl($categories->cat_id) == "") { ?>
		<td><div id="elh_categories_cat_id" class="categories_cat_id"><div class="ewTableHeaderCaption"><?php echo $categories->cat_id->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $categories->SortUrl($categories->cat_id) ?>',1);"><div id="elh_categories_cat_id" class="categories_cat_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $categories->cat_id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($categories->cat_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($categories->cat_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($categories->cat_name->Visible) { // cat_name ?>
	<?php if ($categories->SortUrl($categories->cat_name) == "") { ?>
		<td><div id="elh_categories_cat_name" class="categories_cat_name"><div class="ewTableHeaderCaption"><?php echo $categories->cat_name->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $categories->SortUrl($categories->cat_name) ?>',1);"><div id="elh_categories_cat_name" class="categories_cat_name">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $categories->cat_name->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($categories->cat_name->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($categories->cat_name->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($categories->cat_description->Visible) { // cat_description ?>
	<?php if ($categories->SortUrl($categories->cat_description) == "") { ?>
		<td><div id="elh_categories_cat_description" class="categories_cat_description"><div class="ewTableHeaderCaption"><?php echo $categories->cat_description->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $categories->SortUrl($categories->cat_description) ?>',1);"><div id="elh_categories_cat_description" class="categories_cat_description">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $categories->cat_description->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($categories->cat_description->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($categories->cat_description->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($categories->cat_parent_id->Visible) { // cat_parent_id ?>
	<?php if ($categories->SortUrl($categories->cat_parent_id) == "") { ?>
		<td><div id="elh_categories_cat_parent_id" class="categories_cat_parent_id"><div class="ewTableHeaderCaption"><?php echo $categories->cat_parent_id->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $categories->SortUrl($categories->cat_parent_id) ?>',1);"><div id="elh_categories_cat_parent_id" class="categories_cat_parent_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $categories->cat_parent_id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($categories->cat_parent_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($categories->cat_parent_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($categories->cat_is_gallery->Visible) { // cat_is_gallery ?>
	<?php if ($categories->SortUrl($categories->cat_is_gallery) == "") { ?>
		<td><div id="elh_categories_cat_is_gallery" class="categories_cat_is_gallery"><div class="ewTableHeaderCaption"><?php echo $categories->cat_is_gallery->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $categories->SortUrl($categories->cat_is_gallery) ?>',1);"><div id="elh_categories_cat_is_gallery" class="categories_cat_is_gallery">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $categories->cat_is_gallery->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($categories->cat_is_gallery->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($categories->cat_is_gallery->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($categories->cat_is_offer->Visible) { // cat_is_offer ?>
	<?php if ($categories->SortUrl($categories->cat_is_offer) == "") { ?>
		<td><div id="elh_categories_cat_is_offer" class="categories_cat_is_offer"><div class="ewTableHeaderCaption"><?php echo $categories->cat_is_offer->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $categories->SortUrl($categories->cat_is_offer) ?>',1);"><div id="elh_categories_cat_is_offer" class="categories_cat_is_offer">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $categories->cat_is_offer->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($categories->cat_is_offer->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($categories->cat_is_offer->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($categories->cat_is_competition->Visible) { // cat_is_competition ?>
	<?php if ($categories->SortUrl($categories->cat_is_competition) == "") { ?>
		<td><div id="elh_categories_cat_is_competition" class="categories_cat_is_competition"><div class="ewTableHeaderCaption"><?php echo $categories->cat_is_competition->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $categories->SortUrl($categories->cat_is_competition) ?>',1);"><div id="elh_categories_cat_is_competition" class="categories_cat_is_competition">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $categories->cat_is_competition->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($categories->cat_is_competition->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($categories->cat_is_competition->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($categories->cat_order->Visible) { // cat_order ?>
	<?php if ($categories->SortUrl($categories->cat_order) == "") { ?>
		<td><div id="elh_categories_cat_order" class="categories_cat_order"><div class="ewTableHeaderCaption"><?php echo $categories->cat_order->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $categories->SortUrl($categories->cat_order) ?>',1);"><div id="elh_categories_cat_order" class="categories_cat_order">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $categories->cat_order->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($categories->cat_order->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($categories->cat_order->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($categories->cat_status->Visible) { // cat_status ?>
	<?php if ($categories->SortUrl($categories->cat_status) == "") { ?>
		<td><div id="elh_categories_cat_status" class="categories_cat_status"><div class="ewTableHeaderCaption"><?php echo $categories->cat_status->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $categories->SortUrl($categories->cat_status) ?>',1);"><div id="elh_categories_cat_status" class="categories_cat_status">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $categories->cat_status->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($categories->cat_status->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($categories->cat_status->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$categories_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($categories->ExportAll && $categories->Export <> "") {
	$categories_list->StopRec = $categories_list->TotalRecs;
} else {

	// Set the last record to display
	if ($categories_list->TotalRecs > $categories_list->StartRec + $categories_list->DisplayRecs - 1)
		$categories_list->StopRec = $categories_list->StartRec + $categories_list->DisplayRecs - 1;
	else
		$categories_list->StopRec = $categories_list->TotalRecs;
}
$categories_list->RecCnt = $categories_list->StartRec - 1;
if ($categories_list->Recordset && !$categories_list->Recordset->EOF) {
	$categories_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $categories_list->StartRec > 1)
		$categories_list->Recordset->Move($categories_list->StartRec - 1);
} elseif (!$categories->AllowAddDeleteRow && $categories_list->StopRec == 0) {
	$categories_list->StopRec = $categories->GridAddRowCount;
}

// Initialize aggregate
$categories->RowType = EW_ROWTYPE_AGGREGATEINIT;
$categories->ResetAttrs();
$categories_list->RenderRow();
while ($categories_list->RecCnt < $categories_list->StopRec) {
	$categories_list->RecCnt++;
	if (intval($categories_list->RecCnt) >= intval($categories_list->StartRec)) {
		$categories_list->RowCnt++;

		// Set up key count
		$categories_list->KeyCount = $categories_list->RowIndex;

		// Init row class and style
		$categories->ResetAttrs();
		$categories->CssClass = "";
		if ($categories->CurrentAction == "gridadd") {
		} else {
			$categories_list->LoadRowValues($categories_list->Recordset); // Load row values
		}
		$categories->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$categories->RowAttrs = array_merge($categories->RowAttrs, array('data-rowindex'=>$categories_list->RowCnt, 'id'=>'r' . $categories_list->RowCnt . '_categories', 'data-rowtype'=>$categories->RowType));

		// Render row
		$categories_list->RenderRow();

		// Render list options
		$categories_list->RenderListOptions();
?>
	<tr<?php echo $categories->RowAttributes() ?>>
<?php

// Render list options (body, left)
$categories_list->ListOptions->Render("body", "left", $categories_list->RowCnt);
?>
	<?php if ($categories->cat_id->Visible) { // cat_id ?>
		<td<?php echo $categories->cat_id->CellAttributes() ?>>
<span<?php echo $categories->cat_id->ViewAttributes() ?>>
<?php echo $categories->cat_id->ListViewValue() ?></span>
<a id="<?php echo $categories_list->PageObjName . "_row_" . $categories_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($categories->cat_name->Visible) { // cat_name ?>
		<td<?php echo $categories->cat_name->CellAttributes() ?>>
<span<?php echo $categories->cat_name->ViewAttributes() ?>>
<?php echo $categories->cat_name->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($categories->cat_description->Visible) { // cat_description ?>
		<td<?php echo $categories->cat_description->CellAttributes() ?>>
<span<?php echo $categories->cat_description->ViewAttributes() ?>>
<?php echo $categories->cat_description->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($categories->cat_parent_id->Visible) { // cat_parent_id ?>
		<td<?php echo $categories->cat_parent_id->CellAttributes() ?>>
<span<?php echo $categories->cat_parent_id->ViewAttributes() ?>>
<?php echo $categories->cat_parent_id->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($categories->cat_is_gallery->Visible) { // cat_is_gallery ?>
		<td<?php echo $categories->cat_is_gallery->CellAttributes() ?>>
<span<?php echo $categories->cat_is_gallery->ViewAttributes() ?>>
<?php echo $categories->cat_is_gallery->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($categories->cat_is_offer->Visible) { // cat_is_offer ?>
		<td<?php echo $categories->cat_is_offer->CellAttributes() ?>>
<span<?php echo $categories->cat_is_offer->ViewAttributes() ?>>
<?php echo $categories->cat_is_offer->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($categories->cat_is_competition->Visible) { // cat_is_competition ?>
		<td<?php echo $categories->cat_is_competition->CellAttributes() ?>>
<span<?php echo $categories->cat_is_competition->ViewAttributes() ?>>
<?php echo $categories->cat_is_competition->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($categories->cat_order->Visible) { // cat_order ?>
		<td<?php echo $categories->cat_order->CellAttributes() ?>>
<span<?php echo $categories->cat_order->ViewAttributes() ?>>
<?php echo $categories->cat_order->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($categories->cat_status->Visible) { // cat_status ?>
		<td<?php echo $categories->cat_status->CellAttributes() ?>>
<span<?php echo $categories->cat_status->ViewAttributes() ?>>
<?php echo $categories->cat_status->ListViewValue() ?></span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$categories_list->ListOptions->Render("body", "right", $categories_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($categories->CurrentAction <> "gridadd")
		$categories_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($categories->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($categories_list->Recordset)
	$categories_list->Recordset->Close();
?>
<?php if ($categories_list->TotalRecs > 0) { ?>
<div class="ewGridLowerPanel">
<?php if ($categories->CurrentAction <> "gridadd" && $categories->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($categories_list->Pager)) $categories_list->Pager = new cPrevNextPager($categories_list->StartRec, $categories_list->DisplayRecs, $categories_list->TotalRecs) ?>
<?php if ($categories_list->Pager->RecordCount > 0) { ?>
<table class="ewStdTable"><tbody><tr><td>
	<?php echo $Language->Phrase("Page") ?>&nbsp;
<div class="input-prepend input-append">
<!--first page button-->
	<?php if ($categories_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $categories_list->PageUrl() ?>start=<?php echo $categories_list->Pager->FirstButton->Start ?>"><i class="icon-step-backward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-step-backward"></i></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($categories_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $categories_list->PageUrl() ?>start=<?php echo $categories_list->Pager->PrevButton->Start ?>"><i class="icon-prev"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-prev"></i></a>
	<?php } ?>
<!--current page number-->
	<input class="input-mini" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $categories_list->Pager->CurrentPage ?>">
<!--next page button-->
	<?php if ($categories_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $categories_list->PageUrl() ?>start=<?php echo $categories_list->Pager->NextButton->Start ?>"><i class="icon-play"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-play"></i></a>
	<?php } ?>
<!--last page button-->
	<?php if ($categories_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $categories_list->PageUrl() ?>start=<?php echo $categories_list->Pager->LastButton->Start ?>"><i class="icon-step-forward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-step-forward"></i></a>
	<?php } ?>
</div>
	&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $categories_list->Pager->PageCount ?>
</td>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $categories_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $categories_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $categories_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($categories_list->SearchWhere == "0=101") { ?>
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
	foreach ($categories_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
</div>
<?php } ?>
</td></tr></table>
<script type="text/javascript">
fcategorieslistsrch.Init();
fcategorieslist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$categories_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$categories_list->Page_Terminate();
?>
