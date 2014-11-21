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

$categories_add = NULL; // Initialize page object first

class ccategories_add extends ccategories {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'categories';

	// Page object name
	var $PageObjName = 'categories_add';

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

		// Table object (categories)
		if (!isset($GLOBALS["categories"]) || get_class($GLOBALS["categories"]) == "ccategories") {
			$GLOBALS["categories"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["categories"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'categories', TRUE);

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
			$this->Page_Terminate("categorieslist.php");
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
			if (@$_GET["cat_id"] != "") {
				$this->cat_id->setQueryStringValue($_GET["cat_id"]);
				$this->setKey("cat_id", $this->cat_id->CurrentValue); // Set up key
			} else {
				$this->setKey("cat_id", ""); // Clear key
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
					$this->Page_Terminate("categorieslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "categoriesview.php")
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
		$this->cat_name->CurrentValue = NULL;
		$this->cat_name->OldValue = $this->cat_name->CurrentValue;
		$this->cat_description->CurrentValue = NULL;
		$this->cat_description->OldValue = $this->cat_description->CurrentValue;
		$this->cat_parent_id->CurrentValue = NULL;
		$this->cat_parent_id->OldValue = $this->cat_parent_id->CurrentValue;
		$this->cat_is_gallery->CurrentValue = 0;
		$this->cat_is_offer->CurrentValue = 0;
		$this->cat_is_competition->CurrentValue = 0;
		$this->cat_order->CurrentValue = NULL;
		$this->cat_order->OldValue = $this->cat_order->CurrentValue;
		$this->cat_status->CurrentValue = NULL;
		$this->cat_status->OldValue = $this->cat_status->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->cat_name->FldIsDetailKey) {
			$this->cat_name->setFormValue($objForm->GetValue("x_cat_name"));
		}
		if (!$this->cat_description->FldIsDetailKey) {
			$this->cat_description->setFormValue($objForm->GetValue("x_cat_description"));
		}
		if (!$this->cat_parent_id->FldIsDetailKey) {
			$this->cat_parent_id->setFormValue($objForm->GetValue("x_cat_parent_id"));
		}
		if (!$this->cat_is_gallery->FldIsDetailKey) {
			$this->cat_is_gallery->setFormValue($objForm->GetValue("x_cat_is_gallery"));
		}
		if (!$this->cat_is_offer->FldIsDetailKey) {
			$this->cat_is_offer->setFormValue($objForm->GetValue("x_cat_is_offer"));
		}
		if (!$this->cat_is_competition->FldIsDetailKey) {
			$this->cat_is_competition->setFormValue($objForm->GetValue("x_cat_is_competition"));
		}
		if (!$this->cat_order->FldIsDetailKey) {
			$this->cat_order->setFormValue($objForm->GetValue("x_cat_order"));
		}
		if (!$this->cat_status->FldIsDetailKey) {
			$this->cat_status->setFormValue($objForm->GetValue("x_cat_status"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->cat_name->CurrentValue = $this->cat_name->FormValue;
		$this->cat_description->CurrentValue = $this->cat_description->FormValue;
		$this->cat_parent_id->CurrentValue = $this->cat_parent_id->FormValue;
		$this->cat_is_gallery->CurrentValue = $this->cat_is_gallery->FormValue;
		$this->cat_is_offer->CurrentValue = $this->cat_is_offer->FormValue;
		$this->cat_is_competition->CurrentValue = $this->cat_is_competition->FormValue;
		$this->cat_order->CurrentValue = $this->cat_order->FormValue;
		$this->cat_status->CurrentValue = $this->cat_status->FormValue;
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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// cat_name
			$this->cat_name->EditCustomAttributes = "";
			$this->cat_name->EditValue = ew_HtmlEncode($this->cat_name->CurrentValue);
			$this->cat_name->PlaceHolder = ew_RemoveHtml($this->cat_name->FldCaption());

			// cat_description
			$this->cat_description->EditCustomAttributes = "";
			$this->cat_description->EditValue = ew_HtmlEncode($this->cat_description->CurrentValue);
			$this->cat_description->PlaceHolder = ew_RemoveHtml($this->cat_description->FldCaption());

			// cat_parent_id
			$this->cat_parent_id->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `cat_id`, `cat_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `categories`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->cat_parent_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->cat_parent_id->EditValue = $arwrk;

			// cat_is_gallery
			$this->cat_is_gallery->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->cat_is_gallery->FldTagValue(1), $this->cat_is_gallery->FldTagCaption(1) <> "" ? $this->cat_is_gallery->FldTagCaption(1) : $this->cat_is_gallery->FldTagValue(1));
			$arwrk[] = array($this->cat_is_gallery->FldTagValue(2), $this->cat_is_gallery->FldTagCaption(2) <> "" ? $this->cat_is_gallery->FldTagCaption(2) : $this->cat_is_gallery->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->cat_is_gallery->EditValue = $arwrk;

			// cat_is_offer
			$this->cat_is_offer->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->cat_is_offer->FldTagValue(1), $this->cat_is_offer->FldTagCaption(1) <> "" ? $this->cat_is_offer->FldTagCaption(1) : $this->cat_is_offer->FldTagValue(1));
			$arwrk[] = array($this->cat_is_offer->FldTagValue(2), $this->cat_is_offer->FldTagCaption(2) <> "" ? $this->cat_is_offer->FldTagCaption(2) : $this->cat_is_offer->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->cat_is_offer->EditValue = $arwrk;

			// cat_is_competition
			$this->cat_is_competition->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->cat_is_competition->FldTagValue(1), $this->cat_is_competition->FldTagCaption(1) <> "" ? $this->cat_is_competition->FldTagCaption(1) : $this->cat_is_competition->FldTagValue(1));
			$arwrk[] = array($this->cat_is_competition->FldTagValue(2), $this->cat_is_competition->FldTagCaption(2) <> "" ? $this->cat_is_competition->FldTagCaption(2) : $this->cat_is_competition->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->cat_is_competition->EditValue = $arwrk;

			// cat_order
			$this->cat_order->EditCustomAttributes = "";
			$this->cat_order->EditValue = ew_HtmlEncode($this->cat_order->CurrentValue);
			$this->cat_order->PlaceHolder = ew_RemoveHtml($this->cat_order->FldCaption());

			// cat_status
			$this->cat_status->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->cat_status->FldTagValue(1), $this->cat_status->FldTagCaption(1) <> "" ? $this->cat_status->FldTagCaption(1) : $this->cat_status->FldTagValue(1));
			$arwrk[] = array($this->cat_status->FldTagValue(2), $this->cat_status->FldTagCaption(2) <> "" ? $this->cat_status->FldTagCaption(2) : $this->cat_status->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->cat_status->EditValue = $arwrk;

			// Edit refer script
			// cat_name

			$this->cat_name->HrefValue = "";

			// cat_description
			$this->cat_description->HrefValue = "";

			// cat_parent_id
			$this->cat_parent_id->HrefValue = "";

			// cat_is_gallery
			$this->cat_is_gallery->HrefValue = "";

			// cat_is_offer
			$this->cat_is_offer->HrefValue = "";

			// cat_is_competition
			$this->cat_is_competition->HrefValue = "";

			// cat_order
			$this->cat_order->HrefValue = "";

			// cat_status
			$this->cat_status->HrefValue = "";
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
		if (!$this->cat_name->FldIsDetailKey && !is_null($this->cat_name->FormValue) && $this->cat_name->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->cat_name->FldCaption());
		}
		if (!$this->cat_order->FldIsDetailKey && !is_null($this->cat_order->FormValue) && $this->cat_order->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->cat_order->FldCaption());
		}
		if (!ew_CheckInteger($this->cat_order->FormValue)) {
			ew_AddMessage($gsFormError, $this->cat_order->FldErrMsg());
		}
		if (!$this->cat_status->FldIsDetailKey && !is_null($this->cat_status->FormValue) && $this->cat_status->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->cat_status->FldCaption());
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

		// cat_name
		$this->cat_name->SetDbValueDef($rsnew, $this->cat_name->CurrentValue, "", FALSE);

		// cat_description
		$this->cat_description->SetDbValueDef($rsnew, $this->cat_description->CurrentValue, NULL, FALSE);

		// cat_parent_id
		$this->cat_parent_id->SetDbValueDef($rsnew, $this->cat_parent_id->CurrentValue, NULL, FALSE);

		// cat_is_gallery
		$this->cat_is_gallery->SetDbValueDef($rsnew, $this->cat_is_gallery->CurrentValue, NULL, strval($this->cat_is_gallery->CurrentValue) == "");

		// cat_is_offer
		$this->cat_is_offer->SetDbValueDef($rsnew, $this->cat_is_offer->CurrentValue, NULL, strval($this->cat_is_offer->CurrentValue) == "");

		// cat_is_competition
		$this->cat_is_competition->SetDbValueDef($rsnew, $this->cat_is_competition->CurrentValue, NULL, strval($this->cat_is_competition->CurrentValue) == "");

		// cat_order
		$this->cat_order->SetDbValueDef($rsnew, $this->cat_order->CurrentValue, 0, FALSE);

		// cat_status
		$this->cat_status->SetDbValueDef($rsnew, $this->cat_status->CurrentValue, 0, FALSE);

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
			$this->cat_id->setDbValue($conn->Insert_ID());
			$rsnew['cat_id'] = $this->cat_id->DbValue;
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
		$Breadcrumb->Add("list", $this->TableVar, "categorieslist.php", $this->TableVar, TRUE);
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
if (!isset($categories_add)) $categories_add = new ccategories_add();

// Page init
$categories_add->Page_Init();

// Page main
$categories_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$categories_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var categories_add = new ew_Page("categories_add");
categories_add.PageID = "add"; // Page ID
var EW_PAGE_ID = categories_add.PageID; // For backward compatibility

// Form object
var fcategoriesadd = new ew_Form("fcategoriesadd");

// Validate form
fcategoriesadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_cat_name");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($categories->cat_name->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_cat_order");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($categories->cat_order->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_cat_order");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($categories->cat_order->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_cat_status");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($categories->cat_status->FldCaption()) ?>");

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
fcategoriesadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcategoriesadd.ValidateRequired = true;
<?php } else { ?>
fcategoriesadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fcategoriesadd.Lists["x_cat_parent_id"] = {"LinkField":"x_cat_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_cat_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $categories_add->ShowPageHeader(); ?>
<?php
$categories_add->ShowMessage();
?>
<form name="fcategoriesadd" id="fcategoriesadd" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="categories">
<input type="hidden" name="a_add" id="a_add" value="A">
<table class="ewGrid"><tr><td>
<table id="tbl_categoriesadd" class="table table-bordered table-striped">
<?php if ($categories->cat_name->Visible) { // cat_name ?>
	<tr id="r_cat_name">
		<td><span id="elh_categories_cat_name"><?php echo $categories->cat_name->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $categories->cat_name->CellAttributes() ?>>
<span id="el_categories_cat_name" class="control-group">
<input type="text" data-field="x_cat_name" name="x_cat_name" id="x_cat_name" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($categories->cat_name->PlaceHolder) ?>" value="<?php echo $categories->cat_name->EditValue ?>"<?php echo $categories->cat_name->EditAttributes() ?>>
</span>
<?php echo $categories->cat_name->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($categories->cat_description->Visible) { // cat_description ?>
	<tr id="r_cat_description">
		<td><span id="elh_categories_cat_description"><?php echo $categories->cat_description->FldCaption() ?></span></td>
		<td<?php echo $categories->cat_description->CellAttributes() ?>>
<span id="el_categories_cat_description" class="control-group">
<input type="text" data-field="x_cat_description" name="x_cat_description" id="x_cat_description" size="30" maxlength="200" placeholder="<?php echo ew_HtmlEncode($categories->cat_description->PlaceHolder) ?>" value="<?php echo $categories->cat_description->EditValue ?>"<?php echo $categories->cat_description->EditAttributes() ?>>
</span>
<?php echo $categories->cat_description->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($categories->cat_parent_id->Visible) { // cat_parent_id ?>
	<tr id="r_cat_parent_id">
		<td><span id="elh_categories_cat_parent_id"><?php echo $categories->cat_parent_id->FldCaption() ?></span></td>
		<td<?php echo $categories->cat_parent_id->CellAttributes() ?>>
<span id="el_categories_cat_parent_id" class="control-group">
<select data-field="x_cat_parent_id" id="x_cat_parent_id" name="x_cat_parent_id"<?php echo $categories->cat_parent_id->EditAttributes() ?>>
<?php
if (is_array($categories->cat_parent_id->EditValue)) {
	$arwrk = $categories->cat_parent_id->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($categories->cat_parent_id->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fcategoriesadd.Lists["x_cat_parent_id"].Options = <?php echo (is_array($categories->cat_parent_id->EditValue)) ? ew_ArrayToJson($categories->cat_parent_id->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $categories->cat_parent_id->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($categories->cat_is_gallery->Visible) { // cat_is_gallery ?>
	<tr id="r_cat_is_gallery">
		<td><span id="elh_categories_cat_is_gallery"><?php echo $categories->cat_is_gallery->FldCaption() ?></span></td>
		<td<?php echo $categories->cat_is_gallery->CellAttributes() ?>>
<span id="el_categories_cat_is_gallery" class="control-group">
<select data-field="x_cat_is_gallery" id="x_cat_is_gallery" name="x_cat_is_gallery"<?php echo $categories->cat_is_gallery->EditAttributes() ?>>
<?php
if (is_array($categories->cat_is_gallery->EditValue)) {
	$arwrk = $categories->cat_is_gallery->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($categories->cat_is_gallery->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $categories->cat_is_gallery->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($categories->cat_is_offer->Visible) { // cat_is_offer ?>
	<tr id="r_cat_is_offer">
		<td><span id="elh_categories_cat_is_offer"><?php echo $categories->cat_is_offer->FldCaption() ?></span></td>
		<td<?php echo $categories->cat_is_offer->CellAttributes() ?>>
<span id="el_categories_cat_is_offer" class="control-group">
<select data-field="x_cat_is_offer" id="x_cat_is_offer" name="x_cat_is_offer"<?php echo $categories->cat_is_offer->EditAttributes() ?>>
<?php
if (is_array($categories->cat_is_offer->EditValue)) {
	$arwrk = $categories->cat_is_offer->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($categories->cat_is_offer->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $categories->cat_is_offer->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($categories->cat_is_competition->Visible) { // cat_is_competition ?>
	<tr id="r_cat_is_competition">
		<td><span id="elh_categories_cat_is_competition"><?php echo $categories->cat_is_competition->FldCaption() ?></span></td>
		<td<?php echo $categories->cat_is_competition->CellAttributes() ?>>
<span id="el_categories_cat_is_competition" class="control-group">
<select data-field="x_cat_is_competition" id="x_cat_is_competition" name="x_cat_is_competition"<?php echo $categories->cat_is_competition->EditAttributes() ?>>
<?php
if (is_array($categories->cat_is_competition->EditValue)) {
	$arwrk = $categories->cat_is_competition->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($categories->cat_is_competition->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $categories->cat_is_competition->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($categories->cat_order->Visible) { // cat_order ?>
	<tr id="r_cat_order">
		<td><span id="elh_categories_cat_order"><?php echo $categories->cat_order->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $categories->cat_order->CellAttributes() ?>>
<span id="el_categories_cat_order" class="control-group">
<input type="text" data-field="x_cat_order" name="x_cat_order" id="x_cat_order" size="30" placeholder="<?php echo ew_HtmlEncode($categories->cat_order->PlaceHolder) ?>" value="<?php echo $categories->cat_order->EditValue ?>"<?php echo $categories->cat_order->EditAttributes() ?>>
</span>
<?php echo $categories->cat_order->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($categories->cat_status->Visible) { // cat_status ?>
	<tr id="r_cat_status">
		<td><span id="elh_categories_cat_status"><?php echo $categories->cat_status->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $categories->cat_status->CellAttributes() ?>>
<span id="el_categories_cat_status" class="control-group">
<select data-field="x_cat_status" id="x_cat_status" name="x_cat_status"<?php echo $categories->cat_status->EditAttributes() ?>>
<?php
if (is_array($categories->cat_status->EditValue)) {
	$arwrk = $categories->cat_status->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($categories->cat_status->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $categories->cat_status->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
</form>
<script type="text/javascript">
fcategoriesadd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$categories_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$categories_add->Page_Terminate();
?>
