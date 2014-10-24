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

$news_list = NULL; // Initialize page object first

class cnews_list extends cnews {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'news';

	// Page object name
	var $PageObjName = 'news_list';

	// Grid form hidden field names
	var $FormName = 'fnewslist';
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

		// Table object (news)
		if (!isset($GLOBALS["news"]) || get_class($GLOBALS["news"]) == "cnews") {
			$GLOBALS["news"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["news"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "newsadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "newsdelete.php";
		$this->MultiUpdateUrl = "newsupdate.php";

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'news', TRUE);

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
		$this->new_id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
			$this->new_id->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->new_id->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Return basic search SQL
	function BasicSearchSQL($Keyword) {
		$sKeyword = ew_AdjustSql($Keyword);
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->new_title, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->new_description, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->new_content, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->new_img_path, $Keyword);
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
			$this->UpdateSort($this->new_id); // new_id
			$this->UpdateSort($this->new_title); // new_title
			$this->UpdateSort($this->new_description); // new_description
			$this->UpdateSort($this->new_type); // new_type
			$this->UpdateSort($this->new_img_path); // new_img_path
			$this->UpdateSort($this->new_publish_date); // new_publish_date
			$this->UpdateSort($this->new_cat_id); // new_cat_id
			$this->UpdateSort($this->new_link_id); // new_link_id
			$this->UpdateSort($this->new_link_order); // new_link_order
			$this->UpdateSort($this->new_status); // new_status
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
				$this->new_id->setSort("");
				$this->new_title->setSort("");
				$this->new_description->setSort("");
				$this->new_type->setSort("");
				$this->new_img_path->setSort("");
				$this->new_publish_date->setSort("");
				$this->new_cat_id->setSort("");
				$this->new_link_id->setSort("");
				$this->new_link_order->setSort("");
				$this->new_status->setSort("");
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
		$oListOpt->Body = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->new_id->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'></label>";
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
				$item->Body = "<a class=\"ewAction ewCustomAction\" href=\"\" onclick=\"ew_SubmitSelected(document.fnewslist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("new_id")) <> "")
			$this->new_id->CurrentValue = $this->getKey("new_id"); // new_id
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
if (!isset($news_list)) $news_list = new cnews_list();

// Page init
$news_list->Page_Init();

// Page main
$news_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$news_list->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var news_list = new ew_Page("news_list");
news_list.PageID = "list"; // Page ID
var EW_PAGE_ID = news_list.PageID; // For backward compatibility

// Form object
var fnewslist = new ew_Form("fnewslist");
fnewslist.FormKeyCountName = '<?php echo $news_list->FormKeyCountName ?>';

// Form_CustomValidate event
fnewslist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fnewslist.ValidateRequired = true;
<?php } else { ?>
fnewslist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fnewslist.Lists["x_new_cat_id"] = {"LinkField":"x_cat_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_cat_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fnewslist.Lists["x_new_link_id"] = {"LinkField":"x_new_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_new_title","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
var fnewslistsrch = new ew_Form("fnewslistsrch");
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php if ($news_list->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $news_list->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$news_list->TotalRecs = $news->SelectRecordCount();
	} else {
		if ($news_list->Recordset = $news_list->LoadRecordset())
			$news_list->TotalRecs = $news_list->Recordset->RecordCount();
	}
	$news_list->StartRec = 1;
	if ($news_list->DisplayRecs <= 0 || ($news->Export <> "" && $news->ExportAll)) // Display all records
		$news_list->DisplayRecs = $news_list->TotalRecs;
	if (!($news->Export <> "" && $news->ExportAll))
		$news_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$news_list->Recordset = $news_list->LoadRecordset($news_list->StartRec-1, $news_list->DisplayRecs);
$news_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($news->Export == "" && $news->CurrentAction == "") { ?>
<form name="fnewslistsrch" id="fnewslistsrch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<div class="accordion ewDisplayTable ewSearchTable" id="fnewslistsrch_SearchGroup">
	<div class="accordion-group">
		<div class="accordion-heading">
<a class="accordion-toggle" data-toggle="collapse" data-parent="#fnewslistsrch_SearchGroup" href="#fnewslistsrch_SearchBody"><?php echo $Language->Phrase("Search") ?></a>
		</div>
		<div id="fnewslistsrch_SearchBody" class="accordion-body collapse in">
			<div class="accordion-inner">
<div id="fnewslistsrch_SearchPanel">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="news">
<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="btn-group ewButtonGroup">
	<div class="input-append">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="input-large" value="<?php echo ew_HtmlEncode($news_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?></button>
	</div>
	</div>
	<div class="btn-group ewButtonGroup">
	<a class="btn ewShowAll" href="<?php echo $news_list->PageUrl() ?>cmd=reset"><?php echo $Language->Phrase("ShowAll") ?></a>
	</div>
</div>
<div id="xsr_2" class="ewRow">
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="="<?php if ($news_list->BasicSearch->getType() == "=") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("ExactPhrase") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND"<?php if ($news_list->BasicSearch->getType() == "AND") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AllWord") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR"<?php if ($news_list->BasicSearch->getType() == "OR") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AnyWord") ?></label>
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
<?php $news_list->ShowPageHeader(); ?>
<?php
$news_list->ShowMessage();
?>
<table class="ewGrid"><tr><td class="ewGridContent">
<form name="fnewslist" id="fnewslist" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="news">
<div id="gmp_news" class="ewGridMiddlePanel">
<?php if ($news_list->TotalRecs > 0) { ?>
<table id="tbl_newslist" class="ewTable ewTableSeparate">
<?php echo $news->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$news_list->RenderListOptions();

// Render list options (header, left)
$news_list->ListOptions->Render("header", "left");
?>
<?php if ($news->new_id->Visible) { // new_id ?>
	<?php if ($news->SortUrl($news->new_id) == "") { ?>
		<td><div id="elh_news_new_id" class="news_new_id"><div class="ewTableHeaderCaption"><?php echo $news->new_id->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $news->SortUrl($news->new_id) ?>',1);"><div id="elh_news_new_id" class="news_new_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $news->new_id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($news->new_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($news->new_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($news->new_title->Visible) { // new_title ?>
	<?php if ($news->SortUrl($news->new_title) == "") { ?>
		<td><div id="elh_news_new_title" class="news_new_title"><div class="ewTableHeaderCaption"><?php echo $news->new_title->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $news->SortUrl($news->new_title) ?>',1);"><div id="elh_news_new_title" class="news_new_title">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $news->new_title->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($news->new_title->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($news->new_title->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($news->new_description->Visible) { // new_description ?>
	<?php if ($news->SortUrl($news->new_description) == "") { ?>
		<td><div id="elh_news_new_description" class="news_new_description"><div class="ewTableHeaderCaption"><?php echo $news->new_description->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $news->SortUrl($news->new_description) ?>',1);"><div id="elh_news_new_description" class="news_new_description">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $news->new_description->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($news->new_description->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($news->new_description->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($news->new_type->Visible) { // new_type ?>
	<?php if ($news->SortUrl($news->new_type) == "") { ?>
		<td><div id="elh_news_new_type" class="news_new_type"><div class="ewTableHeaderCaption"><?php echo $news->new_type->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $news->SortUrl($news->new_type) ?>',1);"><div id="elh_news_new_type" class="news_new_type">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $news->new_type->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($news->new_type->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($news->new_type->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($news->new_img_path->Visible) { // new_img_path ?>
	<?php if ($news->SortUrl($news->new_img_path) == "") { ?>
		<td><div id="elh_news_new_img_path" class="news_new_img_path"><div class="ewTableHeaderCaption"><?php echo $news->new_img_path->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $news->SortUrl($news->new_img_path) ?>',1);"><div id="elh_news_new_img_path" class="news_new_img_path">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $news->new_img_path->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($news->new_img_path->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($news->new_img_path->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($news->new_publish_date->Visible) { // new_publish_date ?>
	<?php if ($news->SortUrl($news->new_publish_date) == "") { ?>
		<td><div id="elh_news_new_publish_date" class="news_new_publish_date"><div class="ewTableHeaderCaption"><?php echo $news->new_publish_date->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $news->SortUrl($news->new_publish_date) ?>',1);"><div id="elh_news_new_publish_date" class="news_new_publish_date">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $news->new_publish_date->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($news->new_publish_date->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($news->new_publish_date->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($news->new_cat_id->Visible) { // new_cat_id ?>
	<?php if ($news->SortUrl($news->new_cat_id) == "") { ?>
		<td><div id="elh_news_new_cat_id" class="news_new_cat_id"><div class="ewTableHeaderCaption"><?php echo $news->new_cat_id->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $news->SortUrl($news->new_cat_id) ?>',1);"><div id="elh_news_new_cat_id" class="news_new_cat_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $news->new_cat_id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($news->new_cat_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($news->new_cat_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($news->new_link_id->Visible) { // new_link_id ?>
	<?php if ($news->SortUrl($news->new_link_id) == "") { ?>
		<td><div id="elh_news_new_link_id" class="news_new_link_id"><div class="ewTableHeaderCaption"><?php echo $news->new_link_id->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $news->SortUrl($news->new_link_id) ?>',1);"><div id="elh_news_new_link_id" class="news_new_link_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $news->new_link_id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($news->new_link_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($news->new_link_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($news->new_link_order->Visible) { // new_link_order ?>
	<?php if ($news->SortUrl($news->new_link_order) == "") { ?>
		<td><div id="elh_news_new_link_order" class="news_new_link_order"><div class="ewTableHeaderCaption"><?php echo $news->new_link_order->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $news->SortUrl($news->new_link_order) ?>',1);"><div id="elh_news_new_link_order" class="news_new_link_order">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $news->new_link_order->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($news->new_link_order->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($news->new_link_order->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($news->new_status->Visible) { // new_status ?>
	<?php if ($news->SortUrl($news->new_status) == "") { ?>
		<td><div id="elh_news_new_status" class="news_new_status"><div class="ewTableHeaderCaption"><?php echo $news->new_status->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $news->SortUrl($news->new_status) ?>',1);"><div id="elh_news_new_status" class="news_new_status">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $news->new_status->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($news->new_status->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($news->new_status->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$news_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($news->ExportAll && $news->Export <> "") {
	$news_list->StopRec = $news_list->TotalRecs;
} else {

	// Set the last record to display
	if ($news_list->TotalRecs > $news_list->StartRec + $news_list->DisplayRecs - 1)
		$news_list->StopRec = $news_list->StartRec + $news_list->DisplayRecs - 1;
	else
		$news_list->StopRec = $news_list->TotalRecs;
}
$news_list->RecCnt = $news_list->StartRec - 1;
if ($news_list->Recordset && !$news_list->Recordset->EOF) {
	$news_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $news_list->StartRec > 1)
		$news_list->Recordset->Move($news_list->StartRec - 1);
} elseif (!$news->AllowAddDeleteRow && $news_list->StopRec == 0) {
	$news_list->StopRec = $news->GridAddRowCount;
}

// Initialize aggregate
$news->RowType = EW_ROWTYPE_AGGREGATEINIT;
$news->ResetAttrs();
$news_list->RenderRow();
while ($news_list->RecCnt < $news_list->StopRec) {
	$news_list->RecCnt++;
	if (intval($news_list->RecCnt) >= intval($news_list->StartRec)) {
		$news_list->RowCnt++;

		// Set up key count
		$news_list->KeyCount = $news_list->RowIndex;

		// Init row class and style
		$news->ResetAttrs();
		$news->CssClass = "";
		if ($news->CurrentAction == "gridadd") {
		} else {
			$news_list->LoadRowValues($news_list->Recordset); // Load row values
		}
		$news->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$news->RowAttrs = array_merge($news->RowAttrs, array('data-rowindex'=>$news_list->RowCnt, 'id'=>'r' . $news_list->RowCnt . '_news', 'data-rowtype'=>$news->RowType));

		// Render row
		$news_list->RenderRow();

		// Render list options
		$news_list->RenderListOptions();
?>
	<tr<?php echo $news->RowAttributes() ?>>
<?php

// Render list options (body, left)
$news_list->ListOptions->Render("body", "left", $news_list->RowCnt);
?>
	<?php if ($news->new_id->Visible) { // new_id ?>
		<td<?php echo $news->new_id->CellAttributes() ?>>
<span<?php echo $news->new_id->ViewAttributes() ?>>
<?php echo $news->new_id->ListViewValue() ?></span>
<a id="<?php echo $news_list->PageObjName . "_row_" . $news_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($news->new_title->Visible) { // new_title ?>
		<td<?php echo $news->new_title->CellAttributes() ?>>
<span<?php echo $news->new_title->ViewAttributes() ?>>
<?php echo $news->new_title->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($news->new_description->Visible) { // new_description ?>
		<td<?php echo $news->new_description->CellAttributes() ?>>
<span<?php echo $news->new_description->ViewAttributes() ?>>
<?php echo $news->new_description->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($news->new_type->Visible) { // new_type ?>
		<td<?php echo $news->new_type->CellAttributes() ?>>
<span<?php echo $news->new_type->ViewAttributes() ?>>
<?php echo $news->new_type->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($news->new_img_path->Visible) { // new_img_path ?>
		<td<?php echo $news->new_img_path->CellAttributes() ?>>
<span<?php echo $news->new_img_path->ViewAttributes() ?>>
<?php if ($news->new_img_path->LinkAttributes() <> "") { ?>
<?php if (!empty($news->new_img_path->Upload->DbValue)) { ?>
<?php echo $news->new_img_path->ListViewValue() ?>
<?php } elseif (!in_array($news->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($news->new_img_path->Upload->DbValue)) { ?>
<?php echo $news->new_img_path->ListViewValue() ?>
<?php } elseif (!in_array($news->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</td>
	<?php } ?>
	<?php if ($news->new_publish_date->Visible) { // new_publish_date ?>
		<td<?php echo $news->new_publish_date->CellAttributes() ?>>
<span<?php echo $news->new_publish_date->ViewAttributes() ?>>
<?php echo $news->new_publish_date->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($news->new_cat_id->Visible) { // new_cat_id ?>
		<td<?php echo $news->new_cat_id->CellAttributes() ?>>
<span<?php echo $news->new_cat_id->ViewAttributes() ?>>
<?php echo $news->new_cat_id->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($news->new_link_id->Visible) { // new_link_id ?>
		<td<?php echo $news->new_link_id->CellAttributes() ?>>
<span<?php echo $news->new_link_id->ViewAttributes() ?>>
<?php echo $news->new_link_id->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($news->new_link_order->Visible) { // new_link_order ?>
		<td<?php echo $news->new_link_order->CellAttributes() ?>>
<span<?php echo $news->new_link_order->ViewAttributes() ?>>
<?php echo $news->new_link_order->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($news->new_status->Visible) { // new_status ?>
		<td<?php echo $news->new_status->CellAttributes() ?>>
<span<?php echo $news->new_status->ViewAttributes() ?>>
<?php echo $news->new_status->ListViewValue() ?></span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$news_list->ListOptions->Render("body", "right", $news_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($news->CurrentAction <> "gridadd")
		$news_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($news->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($news_list->Recordset)
	$news_list->Recordset->Close();
?>
<div class="ewGridLowerPanel">
<?php if ($news->CurrentAction <> "gridadd" && $news->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($news_list->Pager)) $news_list->Pager = new cPrevNextPager($news_list->StartRec, $news_list->DisplayRecs, $news_list->TotalRecs) ?>
<?php if ($news_list->Pager->RecordCount > 0) { ?>
<table class="ewStdTable"><tbody><tr><td>
	<?php echo $Language->Phrase("Page") ?>&nbsp;
<div class="input-prepend input-append">
<!--first page button-->
	<?php if ($news_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $news_list->PageUrl() ?>start=<?php echo $news_list->Pager->FirstButton->Start ?>"><i class="icon-step-backward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-step-backward"></i></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($news_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $news_list->PageUrl() ?>start=<?php echo $news_list->Pager->PrevButton->Start ?>"><i class="icon-prev"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-prev"></i></a>
	<?php } ?>
<!--current page number-->
	<input class="input-mini" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $news_list->Pager->CurrentPage ?>">
<!--next page button-->
	<?php if ($news_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $news_list->PageUrl() ?>start=<?php echo $news_list->Pager->NextButton->Start ?>"><i class="icon-play"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-play"></i></a>
	<?php } ?>
<!--last page button-->
	<?php if ($news_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $news_list->PageUrl() ?>start=<?php echo $news_list->Pager->LastButton->Start ?>"><i class="icon-step-forward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-step-forward"></i></a>
	<?php } ?>
</div>
	&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $news_list->Pager->PageCount ?>
</td>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $news_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $news_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $news_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($news_list->SearchWhere == "0=101") { ?>
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
	foreach ($news_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
</div>
</td></tr></table>
<script type="text/javascript">
fnewslistsrch.Init();
fnewslist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$news_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$news_list->Page_Terminate();
?>
