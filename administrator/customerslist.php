<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "customersinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$customers_list = NULL; // Initialize page object first

class ccustomers_list extends ccustomers {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'customers';

	// Page object name
	var $PageObjName = 'customers_list';

	// Grid form hidden field names
	var $FormName = 'fcustomerslist';
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

		// Table object (customers)
		if (!isset($GLOBALS["customers"]) || get_class($GLOBALS["customers"]) == "ccustomers") {
			$GLOBALS["customers"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["customers"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "customersadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "customersdelete.php";
		$this->MultiUpdateUrl = "customersupdate.php";

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'customers', TRUE);

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
		$this->customer_id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
			$this->customer_id->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->customer_id->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Return basic search SQL
	function BasicSearchSQL($Keyword) {
		$sKeyword = ew_AdjustSql($Keyword);
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->customer_code, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->customer_email, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->customer_pass, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->customer_first_name, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->customer_last_name, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->customer_profession, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->customer_phone, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->customer_address, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->customer_facebook, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->customer_author_uid, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->customer_provider, $Keyword);
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
			$this->UpdateSort($this->customer_id); // customer_id
			$this->UpdateSort($this->customer_code); // customer_code
			$this->UpdateSort($this->customer_email); // customer_email
			$this->UpdateSort($this->customer_pass); // customer_pass
			$this->UpdateSort($this->customer_first_name); // customer_first_name
			$this->UpdateSort($this->customer_last_name); // customer_last_name
			$this->UpdateSort($this->customer_profession); // customer_profession
			$this->UpdateSort($this->customer_phone); // customer_phone
			$this->UpdateSort($this->customer_address); // customer_address
			$this->UpdateSort($this->subscription_id); // subscription_id
			$this->UpdateSort($this->customer_facebook); // customer_facebook
			$this->UpdateSort($this->customer_author_uid); // customer_author_uid
			$this->UpdateSort($this->customer_provider); // customer_provider
			$this->UpdateSort($this->customer_payment_type); // customer_payment_type
			$this->UpdateSort($this->customer_status); // customer_status
			$this->UpdateSort($this->customer_first_login); // customer_first_login
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
				$this->customer_id->setSort("");
				$this->customer_code->setSort("");
				$this->customer_email->setSort("");
				$this->customer_pass->setSort("");
				$this->customer_first_name->setSort("");
				$this->customer_last_name->setSort("");
				$this->customer_profession->setSort("");
				$this->customer_phone->setSort("");
				$this->customer_address->setSort("");
				$this->subscription_id->setSort("");
				$this->customer_facebook->setSort("");
				$this->customer_author_uid->setSort("");
				$this->customer_provider->setSort("");
				$this->customer_payment_type->setSort("");
				$this->customer_status->setSort("");
				$this->customer_first_login->setSort("");
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
		$oListOpt->Body = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->customer_id->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'></label>";
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
				$item->Body = "<a class=\"ewAction ewCustomAction\" href=\"\" onclick=\"ew_SubmitSelected(document.fcustomerslist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
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
		$this->customer_id->setDbValue($rs->fields('customer_id'));
		$this->customer_code->setDbValue($rs->fields('customer_code'));
		$this->customer_email->setDbValue($rs->fields('customer_email'));
		$this->customer_pass->setDbValue($rs->fields('customer_pass'));
		$this->customer_first_name->setDbValue($rs->fields('customer_first_name'));
		$this->customer_last_name->setDbValue($rs->fields('customer_last_name'));
		$this->customer_profession->setDbValue($rs->fields('customer_profession'));
		$this->customer_phone->setDbValue($rs->fields('customer_phone'));
		$this->customer_address->setDbValue($rs->fields('customer_address'));
		$this->subscription_id->setDbValue($rs->fields('subscription_id'));
		$this->customer_facebook->setDbValue($rs->fields('customer_facebook'));
		$this->customer_author_uid->setDbValue($rs->fields('customer_author_uid'));
		$this->customer_provider->setDbValue($rs->fields('customer_provider'));
		$this->customer_payment_type->setDbValue($rs->fields('customer_payment_type'));
		$this->customer_status->setDbValue($rs->fields('customer_status'));
		$this->customer_first_login->setDbValue($rs->fields('customer_first_login'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->customer_id->DbValue = $row['customer_id'];
		$this->customer_code->DbValue = $row['customer_code'];
		$this->customer_email->DbValue = $row['customer_email'];
		$this->customer_pass->DbValue = $row['customer_pass'];
		$this->customer_first_name->DbValue = $row['customer_first_name'];
		$this->customer_last_name->DbValue = $row['customer_last_name'];
		$this->customer_profession->DbValue = $row['customer_profession'];
		$this->customer_phone->DbValue = $row['customer_phone'];
		$this->customer_address->DbValue = $row['customer_address'];
		$this->subscription_id->DbValue = $row['subscription_id'];
		$this->customer_facebook->DbValue = $row['customer_facebook'];
		$this->customer_author_uid->DbValue = $row['customer_author_uid'];
		$this->customer_provider->DbValue = $row['customer_provider'];
		$this->customer_payment_type->DbValue = $row['customer_payment_type'];
		$this->customer_status->DbValue = $row['customer_status'];
		$this->customer_first_login->DbValue = $row['customer_first_login'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("customer_id")) <> "")
			$this->customer_id->CurrentValue = $this->getKey("customer_id"); // customer_id
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
		// customer_id
		// customer_code
		// customer_email
		// customer_pass
		// customer_first_name
		// customer_last_name
		// customer_profession
		// customer_phone
		// customer_address
		// subscription_id
		// customer_facebook
		// customer_author_uid
		// customer_provider
		// customer_payment_type
		// customer_status
		// customer_first_login

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// customer_id
			$this->customer_id->ViewValue = $this->customer_id->CurrentValue;
			$this->customer_id->ViewCustomAttributes = "";

			// customer_code
			$this->customer_code->ViewValue = $this->customer_code->CurrentValue;
			$this->customer_code->ViewCustomAttributes = "";

			// customer_email
			$this->customer_email->ViewValue = $this->customer_email->CurrentValue;
			$this->customer_email->ViewCustomAttributes = "";

			// customer_pass
			$this->customer_pass->ViewValue = "********";
			$this->customer_pass->ViewCustomAttributes = "";

			// customer_first_name
			$this->customer_first_name->ViewValue = $this->customer_first_name->CurrentValue;
			$this->customer_first_name->ViewCustomAttributes = "";

			// customer_last_name
			$this->customer_last_name->ViewValue = $this->customer_last_name->CurrentValue;
			$this->customer_last_name->ViewCustomAttributes = "";

			// customer_profession
			$this->customer_profession->ViewValue = $this->customer_profession->CurrentValue;
			$this->customer_profession->ViewCustomAttributes = "";

			// customer_phone
			$this->customer_phone->ViewValue = $this->customer_phone->CurrentValue;
			$this->customer_phone->ViewCustomAttributes = "";

			// customer_address
			$this->customer_address->ViewValue = $this->customer_address->CurrentValue;
			$this->customer_address->ViewCustomAttributes = "";

			// subscription_id
			if (strval($this->subscription_id->CurrentValue) <> "") {
				$sFilterWrk = "`subscription_id`" . ew_SearchString("=", $this->subscription_id->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `subscription_id`, `subscription_type` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `subscriptions`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->subscription_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->subscription_id->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->subscription_id->ViewValue = $this->subscription_id->CurrentValue;
				}
			} else {
				$this->subscription_id->ViewValue = NULL;
			}
			$this->subscription_id->ViewCustomAttributes = "";

			// customer_facebook
			$this->customer_facebook->ViewValue = $this->customer_facebook->CurrentValue;
			$this->customer_facebook->ViewCustomAttributes = "";

			// customer_author_uid
			$this->customer_author_uid->ViewValue = $this->customer_author_uid->CurrentValue;
			$this->customer_author_uid->ViewCustomAttributes = "";

			// customer_provider
			$this->customer_provider->ViewValue = $this->customer_provider->CurrentValue;
			$this->customer_provider->ViewCustomAttributes = "";

			// customer_payment_type
			$this->customer_payment_type->ViewValue = $this->customer_payment_type->CurrentValue;
			$this->customer_payment_type->ViewCustomAttributes = "";

			// customer_status
			if (strval($this->customer_status->CurrentValue) <> "") {
				switch ($this->customer_status->CurrentValue) {
					case $this->customer_status->FldTagValue(1):
						$this->customer_status->ViewValue = $this->customer_status->FldTagCaption(1) <> "" ? $this->customer_status->FldTagCaption(1) : $this->customer_status->CurrentValue;
						break;
					case $this->customer_status->FldTagValue(2):
						$this->customer_status->ViewValue = $this->customer_status->FldTagCaption(2) <> "" ? $this->customer_status->FldTagCaption(2) : $this->customer_status->CurrentValue;
						break;
					case $this->customer_status->FldTagValue(3):
						$this->customer_status->ViewValue = $this->customer_status->FldTagCaption(3) <> "" ? $this->customer_status->FldTagCaption(3) : $this->customer_status->CurrentValue;
						break;
					case $this->customer_status->FldTagValue(4):
						$this->customer_status->ViewValue = $this->customer_status->FldTagCaption(4) <> "" ? $this->customer_status->FldTagCaption(4) : $this->customer_status->CurrentValue;
						break;
					default:
						$this->customer_status->ViewValue = $this->customer_status->CurrentValue;
				}
			} else {
				$this->customer_status->ViewValue = NULL;
			}
			$this->customer_status->ViewCustomAttributes = "";

			// customer_first_login
			if (strval($this->customer_first_login->CurrentValue) <> "") {
				switch ($this->customer_first_login->CurrentValue) {
					case $this->customer_first_login->FldTagValue(1):
						$this->customer_first_login->ViewValue = $this->customer_first_login->FldTagCaption(1) <> "" ? $this->customer_first_login->FldTagCaption(1) : $this->customer_first_login->CurrentValue;
						break;
					case $this->customer_first_login->FldTagValue(2):
						$this->customer_first_login->ViewValue = $this->customer_first_login->FldTagCaption(2) <> "" ? $this->customer_first_login->FldTagCaption(2) : $this->customer_first_login->CurrentValue;
						break;
					default:
						$this->customer_first_login->ViewValue = $this->customer_first_login->CurrentValue;
				}
			} else {
				$this->customer_first_login->ViewValue = NULL;
			}
			$this->customer_first_login->ViewCustomAttributes = "";

			// customer_id
			$this->customer_id->LinkCustomAttributes = "";
			$this->customer_id->HrefValue = "";
			$this->customer_id->TooltipValue = "";

			// customer_code
			$this->customer_code->LinkCustomAttributes = "";
			$this->customer_code->HrefValue = "";
			$this->customer_code->TooltipValue = "";

			// customer_email
			$this->customer_email->LinkCustomAttributes = "";
			$this->customer_email->HrefValue = "";
			$this->customer_email->TooltipValue = "";

			// customer_pass
			$this->customer_pass->LinkCustomAttributes = "";
			$this->customer_pass->HrefValue = "";
			$this->customer_pass->TooltipValue = "";

			// customer_first_name
			$this->customer_first_name->LinkCustomAttributes = "";
			$this->customer_first_name->HrefValue = "";
			$this->customer_first_name->TooltipValue = "";

			// customer_last_name
			$this->customer_last_name->LinkCustomAttributes = "";
			$this->customer_last_name->HrefValue = "";
			$this->customer_last_name->TooltipValue = "";

			// customer_profession
			$this->customer_profession->LinkCustomAttributes = "";
			$this->customer_profession->HrefValue = "";
			$this->customer_profession->TooltipValue = "";

			// customer_phone
			$this->customer_phone->LinkCustomAttributes = "";
			$this->customer_phone->HrefValue = "";
			$this->customer_phone->TooltipValue = "";

			// customer_address
			$this->customer_address->LinkCustomAttributes = "";
			$this->customer_address->HrefValue = "";
			$this->customer_address->TooltipValue = "";

			// subscription_id
			$this->subscription_id->LinkCustomAttributes = "";
			$this->subscription_id->HrefValue = "";
			$this->subscription_id->TooltipValue = "";

			// customer_facebook
			$this->customer_facebook->LinkCustomAttributes = "";
			$this->customer_facebook->HrefValue = "";
			$this->customer_facebook->TooltipValue = "";

			// customer_author_uid
			$this->customer_author_uid->LinkCustomAttributes = "";
			$this->customer_author_uid->HrefValue = "";
			$this->customer_author_uid->TooltipValue = "";

			// customer_provider
			$this->customer_provider->LinkCustomAttributes = "";
			$this->customer_provider->HrefValue = "";
			$this->customer_provider->TooltipValue = "";

			// customer_payment_type
			$this->customer_payment_type->LinkCustomAttributes = "";
			$this->customer_payment_type->HrefValue = "";
			$this->customer_payment_type->TooltipValue = "";

			// customer_status
			$this->customer_status->LinkCustomAttributes = "";
			$this->customer_status->HrefValue = "";
			$this->customer_status->TooltipValue = "";

			// customer_first_login
			$this->customer_first_login->LinkCustomAttributes = "";
			$this->customer_first_login->HrefValue = "";
			$this->customer_first_login->TooltipValue = "";
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
if (!isset($customers_list)) $customers_list = new ccustomers_list();

// Page init
$customers_list->Page_Init();

// Page main
$customers_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$customers_list->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var customers_list = new ew_Page("customers_list");
customers_list.PageID = "list"; // Page ID
var EW_PAGE_ID = customers_list.PageID; // For backward compatibility

// Form object
var fcustomerslist = new ew_Form("fcustomerslist");
fcustomerslist.FormKeyCountName = '<?php echo $customers_list->FormKeyCountName ?>';

// Form_CustomValidate event
fcustomerslist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcustomerslist.ValidateRequired = true;
<?php } else { ?>
fcustomerslist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fcustomerslist.Lists["x_subscription_id"] = {"LinkField":"x_subscription_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_subscription_type","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
var fcustomerslistsrch = new ew_Form("fcustomerslistsrch");
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php if ($customers_list->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $customers_list->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$customers_list->TotalRecs = $customers->SelectRecordCount();
	} else {
		if ($customers_list->Recordset = $customers_list->LoadRecordset())
			$customers_list->TotalRecs = $customers_list->Recordset->RecordCount();
	}
	$customers_list->StartRec = 1;
	if ($customers_list->DisplayRecs <= 0 || ($customers->Export <> "" && $customers->ExportAll)) // Display all records
		$customers_list->DisplayRecs = $customers_list->TotalRecs;
	if (!($customers->Export <> "" && $customers->ExportAll))
		$customers_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$customers_list->Recordset = $customers_list->LoadRecordset($customers_list->StartRec-1, $customers_list->DisplayRecs);
$customers_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($customers->Export == "" && $customers->CurrentAction == "") { ?>
<form name="fcustomerslistsrch" id="fcustomerslistsrch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<div class="accordion ewDisplayTable ewSearchTable" id="fcustomerslistsrch_SearchGroup">
	<div class="accordion-group">
		<div class="accordion-heading">
<a class="accordion-toggle" data-toggle="collapse" data-parent="#fcustomerslistsrch_SearchGroup" href="#fcustomerslistsrch_SearchBody"><?php echo $Language->Phrase("Search") ?></a>
		</div>
		<div id="fcustomerslistsrch_SearchBody" class="accordion-body collapse in">
			<div class="accordion-inner">
<div id="fcustomerslistsrch_SearchPanel">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="customers">
<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="btn-group ewButtonGroup">
	<div class="input-append">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="input-large" value="<?php echo ew_HtmlEncode($customers_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?></button>
	</div>
	</div>
	<div class="btn-group ewButtonGroup">
	<a class="btn ewShowAll" href="<?php echo $customers_list->PageUrl() ?>cmd=reset"><?php echo $Language->Phrase("ShowAll") ?></a>
	</div>
</div>
<div id="xsr_2" class="ewRow">
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="="<?php if ($customers_list->BasicSearch->getType() == "=") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("ExactPhrase") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND"<?php if ($customers_list->BasicSearch->getType() == "AND") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AllWord") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR"<?php if ($customers_list->BasicSearch->getType() == "OR") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AnyWord") ?></label>
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
<?php $customers_list->ShowPageHeader(); ?>
<?php
$customers_list->ShowMessage();
?>
<table class="ewGrid"><tr><td class="ewGridContent">
<form name="fcustomerslist" id="fcustomerslist" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="customers">
<div id="gmp_customers" class="ewGridMiddlePanel">
<?php if ($customers_list->TotalRecs > 0) { ?>
<table id="tbl_customerslist" class="ewTable ewTableSeparate">
<?php echo $customers->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$customers_list->RenderListOptions();

// Render list options (header, left)
$customers_list->ListOptions->Render("header", "left");
?>
<?php if ($customers->customer_id->Visible) { // customer_id ?>
	<?php if ($customers->SortUrl($customers->customer_id) == "") { ?>
		<td><div id="elh_customers_customer_id" class="customers_customer_id"><div class="ewTableHeaderCaption"><?php echo $customers->customer_id->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $customers->SortUrl($customers->customer_id) ?>',1);"><div id="elh_customers_customer_id" class="customers_customer_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $customers->customer_id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($customers->customer_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($customers->customer_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($customers->customer_code->Visible) { // customer_code ?>
	<?php if ($customers->SortUrl($customers->customer_code) == "") { ?>
		<td><div id="elh_customers_customer_code" class="customers_customer_code"><div class="ewTableHeaderCaption"><?php echo $customers->customer_code->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $customers->SortUrl($customers->customer_code) ?>',1);"><div id="elh_customers_customer_code" class="customers_customer_code">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $customers->customer_code->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($customers->customer_code->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($customers->customer_code->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($customers->customer_email->Visible) { // customer_email ?>
	<?php if ($customers->SortUrl($customers->customer_email) == "") { ?>
		<td><div id="elh_customers_customer_email" class="customers_customer_email"><div class="ewTableHeaderCaption"><?php echo $customers->customer_email->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $customers->SortUrl($customers->customer_email) ?>',1);"><div id="elh_customers_customer_email" class="customers_customer_email">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $customers->customer_email->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($customers->customer_email->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($customers->customer_email->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($customers->customer_pass->Visible) { // customer_pass ?>
	<?php if ($customers->SortUrl($customers->customer_pass) == "") { ?>
		<td><div id="elh_customers_customer_pass" class="customers_customer_pass"><div class="ewTableHeaderCaption"><?php echo $customers->customer_pass->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $customers->SortUrl($customers->customer_pass) ?>',1);"><div id="elh_customers_customer_pass" class="customers_customer_pass">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $customers->customer_pass->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($customers->customer_pass->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($customers->customer_pass->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($customers->customer_first_name->Visible) { // customer_first_name ?>
	<?php if ($customers->SortUrl($customers->customer_first_name) == "") { ?>
		<td><div id="elh_customers_customer_first_name" class="customers_customer_first_name"><div class="ewTableHeaderCaption"><?php echo $customers->customer_first_name->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $customers->SortUrl($customers->customer_first_name) ?>',1);"><div id="elh_customers_customer_first_name" class="customers_customer_first_name">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $customers->customer_first_name->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($customers->customer_first_name->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($customers->customer_first_name->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($customers->customer_last_name->Visible) { // customer_last_name ?>
	<?php if ($customers->SortUrl($customers->customer_last_name) == "") { ?>
		<td><div id="elh_customers_customer_last_name" class="customers_customer_last_name"><div class="ewTableHeaderCaption"><?php echo $customers->customer_last_name->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $customers->SortUrl($customers->customer_last_name) ?>',1);"><div id="elh_customers_customer_last_name" class="customers_customer_last_name">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $customers->customer_last_name->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($customers->customer_last_name->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($customers->customer_last_name->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($customers->customer_profession->Visible) { // customer_profession ?>
	<?php if ($customers->SortUrl($customers->customer_profession) == "") { ?>
		<td><div id="elh_customers_customer_profession" class="customers_customer_profession"><div class="ewTableHeaderCaption"><?php echo $customers->customer_profession->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $customers->SortUrl($customers->customer_profession) ?>',1);"><div id="elh_customers_customer_profession" class="customers_customer_profession">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $customers->customer_profession->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($customers->customer_profession->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($customers->customer_profession->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($customers->customer_phone->Visible) { // customer_phone ?>
	<?php if ($customers->SortUrl($customers->customer_phone) == "") { ?>
		<td><div id="elh_customers_customer_phone" class="customers_customer_phone"><div class="ewTableHeaderCaption"><?php echo $customers->customer_phone->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $customers->SortUrl($customers->customer_phone) ?>',1);"><div id="elh_customers_customer_phone" class="customers_customer_phone">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $customers->customer_phone->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($customers->customer_phone->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($customers->customer_phone->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($customers->customer_address->Visible) { // customer_address ?>
	<?php if ($customers->SortUrl($customers->customer_address) == "") { ?>
		<td><div id="elh_customers_customer_address" class="customers_customer_address"><div class="ewTableHeaderCaption"><?php echo $customers->customer_address->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $customers->SortUrl($customers->customer_address) ?>',1);"><div id="elh_customers_customer_address" class="customers_customer_address">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $customers->customer_address->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($customers->customer_address->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($customers->customer_address->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($customers->subscription_id->Visible) { // subscription_id ?>
	<?php if ($customers->SortUrl($customers->subscription_id) == "") { ?>
		<td><div id="elh_customers_subscription_id" class="customers_subscription_id"><div class="ewTableHeaderCaption"><?php echo $customers->subscription_id->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $customers->SortUrl($customers->subscription_id) ?>',1);"><div id="elh_customers_subscription_id" class="customers_subscription_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $customers->subscription_id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($customers->subscription_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($customers->subscription_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($customers->customer_facebook->Visible) { // customer_facebook ?>
	<?php if ($customers->SortUrl($customers->customer_facebook) == "") { ?>
		<td><div id="elh_customers_customer_facebook" class="customers_customer_facebook"><div class="ewTableHeaderCaption"><?php echo $customers->customer_facebook->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $customers->SortUrl($customers->customer_facebook) ?>',1);"><div id="elh_customers_customer_facebook" class="customers_customer_facebook">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $customers->customer_facebook->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($customers->customer_facebook->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($customers->customer_facebook->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($customers->customer_author_uid->Visible) { // customer_author_uid ?>
	<?php if ($customers->SortUrl($customers->customer_author_uid) == "") { ?>
		<td><div id="elh_customers_customer_author_uid" class="customers_customer_author_uid"><div class="ewTableHeaderCaption"><?php echo $customers->customer_author_uid->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $customers->SortUrl($customers->customer_author_uid) ?>',1);"><div id="elh_customers_customer_author_uid" class="customers_customer_author_uid">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $customers->customer_author_uid->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($customers->customer_author_uid->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($customers->customer_author_uid->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($customers->customer_provider->Visible) { // customer_provider ?>
	<?php if ($customers->SortUrl($customers->customer_provider) == "") { ?>
		<td><div id="elh_customers_customer_provider" class="customers_customer_provider"><div class="ewTableHeaderCaption"><?php echo $customers->customer_provider->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $customers->SortUrl($customers->customer_provider) ?>',1);"><div id="elh_customers_customer_provider" class="customers_customer_provider">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $customers->customer_provider->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($customers->customer_provider->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($customers->customer_provider->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($customers->customer_payment_type->Visible) { // customer_payment_type ?>
	<?php if ($customers->SortUrl($customers->customer_payment_type) == "") { ?>
		<td><div id="elh_customers_customer_payment_type" class="customers_customer_payment_type"><div class="ewTableHeaderCaption"><?php echo $customers->customer_payment_type->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $customers->SortUrl($customers->customer_payment_type) ?>',1);"><div id="elh_customers_customer_payment_type" class="customers_customer_payment_type">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $customers->customer_payment_type->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($customers->customer_payment_type->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($customers->customer_payment_type->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($customers->customer_status->Visible) { // customer_status ?>
	<?php if ($customers->SortUrl($customers->customer_status) == "") { ?>
		<td><div id="elh_customers_customer_status" class="customers_customer_status"><div class="ewTableHeaderCaption"><?php echo $customers->customer_status->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $customers->SortUrl($customers->customer_status) ?>',1);"><div id="elh_customers_customer_status" class="customers_customer_status">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $customers->customer_status->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($customers->customer_status->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($customers->customer_status->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($customers->customer_first_login->Visible) { // customer_first_login ?>
	<?php if ($customers->SortUrl($customers->customer_first_login) == "") { ?>
		<td><div id="elh_customers_customer_first_login" class="customers_customer_first_login"><div class="ewTableHeaderCaption"><?php echo $customers->customer_first_login->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $customers->SortUrl($customers->customer_first_login) ?>',1);"><div id="elh_customers_customer_first_login" class="customers_customer_first_login">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $customers->customer_first_login->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($customers->customer_first_login->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($customers->customer_first_login->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$customers_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($customers->ExportAll && $customers->Export <> "") {
	$customers_list->StopRec = $customers_list->TotalRecs;
} else {

	// Set the last record to display
	if ($customers_list->TotalRecs > $customers_list->StartRec + $customers_list->DisplayRecs - 1)
		$customers_list->StopRec = $customers_list->StartRec + $customers_list->DisplayRecs - 1;
	else
		$customers_list->StopRec = $customers_list->TotalRecs;
}
$customers_list->RecCnt = $customers_list->StartRec - 1;
if ($customers_list->Recordset && !$customers_list->Recordset->EOF) {
	$customers_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $customers_list->StartRec > 1)
		$customers_list->Recordset->Move($customers_list->StartRec - 1);
} elseif (!$customers->AllowAddDeleteRow && $customers_list->StopRec == 0) {
	$customers_list->StopRec = $customers->GridAddRowCount;
}

// Initialize aggregate
$customers->RowType = EW_ROWTYPE_AGGREGATEINIT;
$customers->ResetAttrs();
$customers_list->RenderRow();
while ($customers_list->RecCnt < $customers_list->StopRec) {
	$customers_list->RecCnt++;
	if (intval($customers_list->RecCnt) >= intval($customers_list->StartRec)) {
		$customers_list->RowCnt++;

		// Set up key count
		$customers_list->KeyCount = $customers_list->RowIndex;

		// Init row class and style
		$customers->ResetAttrs();
		$customers->CssClass = "";
		if ($customers->CurrentAction == "gridadd") {
		} else {
			$customers_list->LoadRowValues($customers_list->Recordset); // Load row values
		}
		$customers->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$customers->RowAttrs = array_merge($customers->RowAttrs, array('data-rowindex'=>$customers_list->RowCnt, 'id'=>'r' . $customers_list->RowCnt . '_customers', 'data-rowtype'=>$customers->RowType));

		// Render row
		$customers_list->RenderRow();

		// Render list options
		$customers_list->RenderListOptions();
?>
	<tr<?php echo $customers->RowAttributes() ?>>
<?php

// Render list options (body, left)
$customers_list->ListOptions->Render("body", "left", $customers_list->RowCnt);
?>
	<?php if ($customers->customer_id->Visible) { // customer_id ?>
		<td<?php echo $customers->customer_id->CellAttributes() ?>>
<span<?php echo $customers->customer_id->ViewAttributes() ?>>
<?php echo $customers->customer_id->ListViewValue() ?></span>
<a id="<?php echo $customers_list->PageObjName . "_row_" . $customers_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($customers->customer_code->Visible) { // customer_code ?>
		<td<?php echo $customers->customer_code->CellAttributes() ?>>
<span<?php echo $customers->customer_code->ViewAttributes() ?>>
<?php echo $customers->customer_code->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($customers->customer_email->Visible) { // customer_email ?>
		<td<?php echo $customers->customer_email->CellAttributes() ?>>
<span<?php echo $customers->customer_email->ViewAttributes() ?>>
<?php echo $customers->customer_email->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($customers->customer_pass->Visible) { // customer_pass ?>
		<td<?php echo $customers->customer_pass->CellAttributes() ?>>
<span<?php echo $customers->customer_pass->ViewAttributes() ?>>
<?php echo $customers->customer_pass->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($customers->customer_first_name->Visible) { // customer_first_name ?>
		<td<?php echo $customers->customer_first_name->CellAttributes() ?>>
<span<?php echo $customers->customer_first_name->ViewAttributes() ?>>
<?php echo $customers->customer_first_name->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($customers->customer_last_name->Visible) { // customer_last_name ?>
		<td<?php echo $customers->customer_last_name->CellAttributes() ?>>
<span<?php echo $customers->customer_last_name->ViewAttributes() ?>>
<?php echo $customers->customer_last_name->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($customers->customer_profession->Visible) { // customer_profession ?>
		<td<?php echo $customers->customer_profession->CellAttributes() ?>>
<span<?php echo $customers->customer_profession->ViewAttributes() ?>>
<?php echo $customers->customer_profession->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($customers->customer_phone->Visible) { // customer_phone ?>
		<td<?php echo $customers->customer_phone->CellAttributes() ?>>
<span<?php echo $customers->customer_phone->ViewAttributes() ?>>
<?php echo $customers->customer_phone->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($customers->customer_address->Visible) { // customer_address ?>
		<td<?php echo $customers->customer_address->CellAttributes() ?>>
<span<?php echo $customers->customer_address->ViewAttributes() ?>>
<?php echo $customers->customer_address->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($customers->subscription_id->Visible) { // subscription_id ?>
		<td<?php echo $customers->subscription_id->CellAttributes() ?>>
<span<?php echo $customers->subscription_id->ViewAttributes() ?>>
<?php echo $customers->subscription_id->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($customers->customer_facebook->Visible) { // customer_facebook ?>
		<td<?php echo $customers->customer_facebook->CellAttributes() ?>>
<span<?php echo $customers->customer_facebook->ViewAttributes() ?>>
<?php echo $customers->customer_facebook->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($customers->customer_author_uid->Visible) { // customer_author_uid ?>
		<td<?php echo $customers->customer_author_uid->CellAttributes() ?>>
<span<?php echo $customers->customer_author_uid->ViewAttributes() ?>>
<?php echo $customers->customer_author_uid->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($customers->customer_provider->Visible) { // customer_provider ?>
		<td<?php echo $customers->customer_provider->CellAttributes() ?>>
<span<?php echo $customers->customer_provider->ViewAttributes() ?>>
<?php echo $customers->customer_provider->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($customers->customer_payment_type->Visible) { // customer_payment_type ?>
		<td<?php echo $customers->customer_payment_type->CellAttributes() ?>>
<span<?php echo $customers->customer_payment_type->ViewAttributes() ?>>
<?php echo $customers->customer_payment_type->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($customers->customer_status->Visible) { // customer_status ?>
		<td<?php echo $customers->customer_status->CellAttributes() ?>>
<span<?php echo $customers->customer_status->ViewAttributes() ?>>
<?php echo $customers->customer_status->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($customers->customer_first_login->Visible) { // customer_first_login ?>
		<td<?php echo $customers->customer_first_login->CellAttributes() ?>>
<span<?php echo $customers->customer_first_login->ViewAttributes() ?>>
<?php echo $customers->customer_first_login->ListViewValue() ?></span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$customers_list->ListOptions->Render("body", "right", $customers_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($customers->CurrentAction <> "gridadd")
		$customers_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($customers->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($customers_list->Recordset)
	$customers_list->Recordset->Close();
?>
<div class="ewGridLowerPanel">
<?php if ($customers->CurrentAction <> "gridadd" && $customers->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($customers_list->Pager)) $customers_list->Pager = new cPrevNextPager($customers_list->StartRec, $customers_list->DisplayRecs, $customers_list->TotalRecs) ?>
<?php if ($customers_list->Pager->RecordCount > 0) { ?>
<table class="ewStdTable"><tbody><tr><td>
	<?php echo $Language->Phrase("Page") ?>&nbsp;
<div class="input-prepend input-append">
<!--first page button-->
	<?php if ($customers_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $customers_list->PageUrl() ?>start=<?php echo $customers_list->Pager->FirstButton->Start ?>"><i class="icon-step-backward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-step-backward"></i></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($customers_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $customers_list->PageUrl() ?>start=<?php echo $customers_list->Pager->PrevButton->Start ?>"><i class="icon-prev"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-prev"></i></a>
	<?php } ?>
<!--current page number-->
	<input class="input-mini" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $customers_list->Pager->CurrentPage ?>">
<!--next page button-->
	<?php if ($customers_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $customers_list->PageUrl() ?>start=<?php echo $customers_list->Pager->NextButton->Start ?>"><i class="icon-play"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-play"></i></a>
	<?php } ?>
<!--last page button-->
	<?php if ($customers_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $customers_list->PageUrl() ?>start=<?php echo $customers_list->Pager->LastButton->Start ?>"><i class="icon-step-forward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-step-forward"></i></a>
	<?php } ?>
</div>
	&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $customers_list->Pager->PageCount ?>
</td>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $customers_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $customers_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $customers_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($customers_list->SearchWhere == "0=101") { ?>
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
	foreach ($customers_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
</div>
</td></tr></table>
<script type="text/javascript">
fcustomerslistsrch.Init();
fcustomerslist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$customers_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$customers_list->Page_Terminate();
?>
