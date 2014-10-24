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

$categories_edit = NULL; // Initialize page object first

class ccategories_edit extends ccategories {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'categories';

	// Page object name
	var $PageObjName = 'categories_edit';

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
			define("EW_PAGE_ID", 'edit', TRUE);

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
		if (!$Security->CanEdit()) {
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
		$this->cat_id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
		if (@$_GET["cat_id"] <> "") {
			$this->cat_id->setQueryStringValue($_GET["cat_id"]);
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
		if ($this->cat_id->CurrentValue == "")
			$this->Page_Terminate("categorieslist.php"); // Invalid key, return to list

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
					$this->Page_Terminate("categorieslist.php"); // No matching record, return to list
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
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->cat_id->FldIsDetailKey)
			$this->cat_id->setFormValue($objForm->GetValue("x_cat_id"));
		if (!$this->cat_name->FldIsDetailKey) {
			$this->cat_name->setFormValue($objForm->GetValue("x_cat_name"));
		}
		if (!$this->cat_description->FldIsDetailKey) {
			$this->cat_description->setFormValue($objForm->GetValue("x_cat_description"));
		}
		if (!$this->cat_parent_id->FldIsDetailKey) {
			$this->cat_parent_id->setFormValue($objForm->GetValue("x_cat_parent_id"));
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
		$this->LoadRow();
		$this->cat_id->CurrentValue = $this->cat_id->FormValue;
		$this->cat_name->CurrentValue = $this->cat_name->FormValue;
		$this->cat_description->CurrentValue = $this->cat_description->FormValue;
		$this->cat_parent_id->CurrentValue = $this->cat_parent_id->FormValue;
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
		$this->cat_is_offer->DbValue = $row['cat_is_offer'];
		$this->cat_is_competition->DbValue = $row['cat_is_competition'];
		$this->cat_order->DbValue = $row['cat_order'];
		$this->cat_status->DbValue = $row['cat_status'];
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
			$sSqlWrk = "SELECT `cat_id`, `cat_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `viewcategoriesparent`";
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
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// cat_id
			$this->cat_id->EditCustomAttributes = "";
			$this->cat_id->EditValue = $this->cat_id->CurrentValue;
			$this->cat_id->ViewCustomAttributes = "";

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
			$sSqlWrk = "SELECT `cat_id`, `cat_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `viewcategoriesparent`";
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
			// cat_id

			$this->cat_id->HrefValue = "";

			// cat_name
			$this->cat_name->HrefValue = "";

			// cat_description
			$this->cat_description->HrefValue = "";

			// cat_parent_id
			$this->cat_parent_id->HrefValue = "";

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

			// cat_name
			$this->cat_name->SetDbValueDef($rsnew, $this->cat_name->CurrentValue, "", $this->cat_name->ReadOnly);

			// cat_description
			$this->cat_description->SetDbValueDef($rsnew, $this->cat_description->CurrentValue, NULL, $this->cat_description->ReadOnly);

			// cat_parent_id
			$this->cat_parent_id->SetDbValueDef($rsnew, $this->cat_parent_id->CurrentValue, NULL, $this->cat_parent_id->ReadOnly);

			// cat_is_offer
			$this->cat_is_offer->SetDbValueDef($rsnew, $this->cat_is_offer->CurrentValue, NULL, $this->cat_is_offer->ReadOnly);

			// cat_is_competition
			$this->cat_is_competition->SetDbValueDef($rsnew, $this->cat_is_competition->CurrentValue, NULL, $this->cat_is_competition->ReadOnly);

			// cat_order
			$this->cat_order->SetDbValueDef($rsnew, $this->cat_order->CurrentValue, 0, $this->cat_order->ReadOnly);

			// cat_status
			$this->cat_status->SetDbValueDef($rsnew, $this->cat_status->CurrentValue, 0, $this->cat_status->ReadOnly);

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
		return $EditRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "categorieslist.php", $this->TableVar, TRUE);
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
if (!isset($categories_edit)) $categories_edit = new ccategories_edit();

// Page init
$categories_edit->Page_Init();

// Page main
$categories_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$categories_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var categories_edit = new ew_Page("categories_edit");
categories_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = categories_edit.PageID; // For backward compatibility

// Form object
var fcategoriesedit = new ew_Form("fcategoriesedit");

// Validate form
fcategoriesedit.Validate = function() {
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
fcategoriesedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcategoriesedit.ValidateRequired = true;
<?php } else { ?>
fcategoriesedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fcategoriesedit.Lists["x_cat_parent_id"] = {"LinkField":"x_cat_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_cat_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $categories_edit->ShowPageHeader(); ?>
<?php
$categories_edit->ShowMessage();
?>
<form name="fcategoriesedit" id="fcategoriesedit" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="categories">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table class="ewGrid"><tr><td>
<table id="tbl_categoriesedit" class="table table-bordered table-striped">
<?php if ($categories->cat_id->Visible) { // cat_id ?>
	<tr id="r_cat_id">
		<td><span id="elh_categories_cat_id"><?php echo $categories->cat_id->FldCaption() ?></span></td>
		<td<?php echo $categories->cat_id->CellAttributes() ?>>
<span id="el_categories_cat_id" class="control-group">
<span<?php echo $categories->cat_id->ViewAttributes() ?>>
<?php echo $categories->cat_id->EditValue ?></span>
</span>
<input type="hidden" data-field="x_cat_id" name="x_cat_id" id="x_cat_id" value="<?php echo ew_HtmlEncode($categories->cat_id->CurrentValue) ?>">
<?php echo $categories->cat_id->CustomMsg ?></td>
	</tr>
<?php } ?>
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
fcategoriesedit.Lists["x_cat_parent_id"].Options = <?php echo (is_array($categories->cat_parent_id->EditValue)) ? ew_ArrayToJson($categories->cat_parent_id->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $categories->cat_parent_id->CustomMsg ?></td>
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
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("EditBtn") ?></button>
</form>
<script type="text/javascript">
fcategoriesedit.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$categories_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$categories_edit->Page_Terminate();
?>
