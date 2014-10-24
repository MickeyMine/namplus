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

$news_edit = NULL; // Initialize page object first

class cnews_edit extends cnews {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'news';

	// Page object name
	var $PageObjName = 'news_edit';

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

		// Table object (news)
		if (!isset($GLOBALS["news"]) || get_class($GLOBALS["news"]) == "cnews") {
			$GLOBALS["news"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["news"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'news', TRUE);

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
			$this->Page_Terminate("newslist.php");
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();

		// Create form object
		$objForm = new cFormObj();
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
	var $DbMasterFilter;
	var $DbDetailFilter;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Load key from QueryString
		if (@$_GET["new_id"] <> "") {
			$this->new_id->setQueryStringValue($_GET["new_id"]);
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
		if ($this->new_id->CurrentValue == "")
			$this->Page_Terminate("newslist.php"); // Invalid key, return to list

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
					$this->Page_Terminate("newslist.php"); // No matching record, return to list
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
		$this->new_img_path->Upload->Index = $objForm->Index;
		if ($this->new_img_path->Upload->UploadFile()) {

			// No action required
		} else {
			echo $this->new_img_path->Upload->Message;
			$this->Page_Terminate();
			exit();
		}
		$this->new_img_path->CurrentValue = $this->new_img_path->Upload->FileName;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
		if (!$this->new_id->FldIsDetailKey)
			$this->new_id->setFormValue($objForm->GetValue("x_new_id"));
		if (!$this->new_title->FldIsDetailKey) {
			$this->new_title->setFormValue($objForm->GetValue("x_new_title"));
		}
		if (!$this->new_description->FldIsDetailKey) {
			$this->new_description->setFormValue($objForm->GetValue("x_new_description"));
		}
		if (!$this->new_content->FldIsDetailKey) {
			$this->new_content->setFormValue($objForm->GetValue("x_new_content"));
		}
		if (!$this->new_type->FldIsDetailKey) {
			$this->new_type->setFormValue($objForm->GetValue("x_new_type"));
		}
		if (!$this->new_publish_date->FldIsDetailKey) {
			$this->new_publish_date->setFormValue($objForm->GetValue("x_new_publish_date"));
			$this->new_publish_date->CurrentValue = ew_UnFormatDateTime($this->new_publish_date->CurrentValue, 7);
		}
		if (!$this->new_cat_id->FldIsDetailKey) {
			$this->new_cat_id->setFormValue($objForm->GetValue("x_new_cat_id"));
		}
		if (!$this->new_link_id->FldIsDetailKey) {
			$this->new_link_id->setFormValue($objForm->GetValue("x_new_link_id"));
		}
		if (!$this->new_link_order->FldIsDetailKey) {
			$this->new_link_order->setFormValue($objForm->GetValue("x_new_link_order"));
		}
		if (!$this->new_status->FldIsDetailKey) {
			$this->new_status->setFormValue($objForm->GetValue("x_new_status"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->new_id->CurrentValue = $this->new_id->FormValue;
		$this->new_title->CurrentValue = $this->new_title->FormValue;
		$this->new_description->CurrentValue = $this->new_description->FormValue;
		$this->new_content->CurrentValue = $this->new_content->FormValue;
		$this->new_type->CurrentValue = $this->new_type->FormValue;
		$this->new_publish_date->CurrentValue = $this->new_publish_date->FormValue;
		$this->new_publish_date->CurrentValue = ew_UnFormatDateTime($this->new_publish_date->CurrentValue, 7);
		$this->new_cat_id->CurrentValue = $this->new_cat_id->FormValue;
		$this->new_link_id->CurrentValue = $this->new_link_id->FormValue;
		$this->new_link_order->CurrentValue = $this->new_link_order->FormValue;
		$this->new_status->CurrentValue = $this->new_status->FormValue;
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
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// new_id
			$this->new_id->EditCustomAttributes = "";
			$this->new_id->EditValue = $this->new_id->CurrentValue;
			$this->new_id->ViewCustomAttributes = "";

			// new_title
			$this->new_title->EditCustomAttributes = "";
			$this->new_title->EditValue = ew_HtmlEncode($this->new_title->CurrentValue);
			$this->new_title->PlaceHolder = ew_RemoveHtml($this->new_title->FldCaption());

			// new_description
			$this->new_description->EditCustomAttributes = "";
			$this->new_description->EditValue = ew_HtmlEncode($this->new_description->CurrentValue);
			$this->new_description->PlaceHolder = ew_RemoveHtml($this->new_description->FldCaption());

			// new_content
			$this->new_content->EditCustomAttributes = "";
			$this->new_content->EditValue = $this->new_content->CurrentValue;
			$this->new_content->PlaceHolder = ew_RemoveHtml($this->new_content->FldCaption());

			// new_type
			$this->new_type->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->new_type->FldTagValue(1), $this->new_type->FldTagCaption(1) <> "" ? $this->new_type->FldTagCaption(1) : $this->new_type->FldTagValue(1));
			$arwrk[] = array($this->new_type->FldTagValue(2), $this->new_type->FldTagCaption(2) <> "" ? $this->new_type->FldTagCaption(2) : $this->new_type->FldTagValue(2));
			$arwrk[] = array($this->new_type->FldTagValue(3), $this->new_type->FldTagCaption(3) <> "" ? $this->new_type->FldTagCaption(3) : $this->new_type->FldTagValue(3));
			$arwrk[] = array($this->new_type->FldTagValue(4), $this->new_type->FldTagCaption(4) <> "" ? $this->new_type->FldTagCaption(4) : $this->new_type->FldTagValue(4));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->new_type->EditValue = $arwrk;

			// new_img_path
			$this->new_img_path->EditCustomAttributes = "";
			if (!ew_Empty($this->new_img_path->Upload->DbValue)) {
				$this->new_img_path->EditValue = $this->new_img_path->Upload->DbValue;
			} else {
				$this->new_img_path->EditValue = "";
			}
			if (!ew_Empty($this->new_img_path->CurrentValue))
				$this->new_img_path->Upload->FileName = $this->new_img_path->CurrentValue;
			if ($this->CurrentAction == "I" && !$this->EventCancelled) ew_RenderUploadField($this->new_img_path);

			// new_publish_date
			$this->new_publish_date->EditCustomAttributes = "";
			$this->new_publish_date->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->new_publish_date->CurrentValue, 7));
			$this->new_publish_date->PlaceHolder = ew_RemoveHtml($this->new_publish_date->FldCaption());

			// new_cat_id
			$this->new_cat_id->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `cat_id`, `cat_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `viewnews`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->new_cat_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->new_cat_id->EditValue = $arwrk;

			// new_link_id
			$this->new_link_id->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `new_id`, `new_title` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `viewnewstops`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->new_link_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->new_link_id->EditValue = $arwrk;

			// new_link_order
			$this->new_link_order->EditCustomAttributes = "";
			$this->new_link_order->EditValue = ew_HtmlEncode($this->new_link_order->CurrentValue);
			$this->new_link_order->PlaceHolder = ew_RemoveHtml($this->new_link_order->FldCaption());

			// new_status
			$this->new_status->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->new_status->FldTagValue(1), $this->new_status->FldTagCaption(1) <> "" ? $this->new_status->FldTagCaption(1) : $this->new_status->FldTagValue(1));
			$arwrk[] = array($this->new_status->FldTagValue(2), $this->new_status->FldTagCaption(2) <> "" ? $this->new_status->FldTagCaption(2) : $this->new_status->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->new_status->EditValue = $arwrk;

			// Edit refer script
			// new_id

			$this->new_id->HrefValue = "";

			// new_title
			$this->new_title->HrefValue = "";

			// new_description
			$this->new_description->HrefValue = "";

			// new_content
			$this->new_content->HrefValue = "";

			// new_type
			$this->new_type->HrefValue = "";

			// new_img_path
			$this->new_img_path->HrefValue = "";
			$this->new_img_path->HrefValue2 = $this->new_img_path->UploadPath . $this->new_img_path->Upload->DbValue;

			// new_publish_date
			$this->new_publish_date->HrefValue = "";

			// new_cat_id
			$this->new_cat_id->HrefValue = "";

			// new_link_id
			$this->new_link_id->HrefValue = "";

			// new_link_order
			$this->new_link_order->HrefValue = "";

			// new_status
			$this->new_status->HrefValue = "";
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
		if (!$this->new_title->FldIsDetailKey && !is_null($this->new_title->FormValue) && $this->new_title->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->new_title->FldCaption());
		}
		if (!$this->new_description->FldIsDetailKey && !is_null($this->new_description->FormValue) && $this->new_description->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->new_description->FldCaption());
		}
		if (!$this->new_content->FldIsDetailKey && !is_null($this->new_content->FormValue) && $this->new_content->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->new_content->FldCaption());
		}
		if (!$this->new_type->FldIsDetailKey && !is_null($this->new_type->FormValue) && $this->new_type->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->new_type->FldCaption());
		}
		if (is_null($this->new_img_path->Upload->Value) && !$this->new_img_path->Upload->KeepFile) {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->new_img_path->FldCaption());
		}
		if (!$this->new_publish_date->FldIsDetailKey && !is_null($this->new_publish_date->FormValue) && $this->new_publish_date->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->new_publish_date->FldCaption());
		}
		if (!ew_CheckEuroDate($this->new_publish_date->FormValue)) {
			ew_AddMessage($gsFormError, $this->new_publish_date->FldErrMsg());
		}
		if (!$this->new_cat_id->FldIsDetailKey && !is_null($this->new_cat_id->FormValue) && $this->new_cat_id->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->new_cat_id->FldCaption());
		}
		if (!ew_CheckInteger($this->new_link_order->FormValue)) {
			ew_AddMessage($gsFormError, $this->new_link_order->FldErrMsg());
		}
		if (!$this->new_status->FldIsDetailKey && !is_null($this->new_status->FormValue) && $this->new_status->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->new_status->FldCaption());
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

			// new_title
			$this->new_title->SetDbValueDef($rsnew, $this->new_title->CurrentValue, "", $this->new_title->ReadOnly);

			// new_description
			$this->new_description->SetDbValueDef($rsnew, $this->new_description->CurrentValue, "", $this->new_description->ReadOnly);

			// new_content
			$this->new_content->SetDbValueDef($rsnew, $this->new_content->CurrentValue, "", $this->new_content->ReadOnly);

			// new_type
			$this->new_type->SetDbValueDef($rsnew, $this->new_type->CurrentValue, 0, $this->new_type->ReadOnly);

			// new_img_path
			if (!($this->new_img_path->ReadOnly) && !$this->new_img_path->Upload->KeepFile) {
				$this->new_img_path->Upload->DbValue = $rs->fields('new_img_path'); // Get original value
				if ($this->new_img_path->Upload->FileName == "") {
					$rsnew['new_img_path'] = NULL;
				} else {
					$rsnew['new_img_path'] = $this->new_img_path->Upload->FileName;
				}
			}

			// new_publish_date
			$this->new_publish_date->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->new_publish_date->CurrentValue, 7), ew_CurrentDate(), $this->new_publish_date->ReadOnly);

			// new_cat_id
			$this->new_cat_id->SetDbValueDef($rsnew, $this->new_cat_id->CurrentValue, 0, $this->new_cat_id->ReadOnly);

			// new_link_id
			$this->new_link_id->SetDbValueDef($rsnew, $this->new_link_id->CurrentValue, NULL, $this->new_link_id->ReadOnly);

			// new_link_order
			$this->new_link_order->SetDbValueDef($rsnew, $this->new_link_order->CurrentValue, NULL, $this->new_link_order->ReadOnly);

			// new_status
			$this->new_status->SetDbValueDef($rsnew, $this->new_status->CurrentValue, 0, $this->new_status->ReadOnly);
			if (!$this->new_img_path->Upload->KeepFile) {
				if (!ew_Empty($this->new_img_path->Upload->Value)) {
					if ($this->new_img_path->Upload->FileName == $this->new_img_path->Upload->DbValue) { // Overwrite if same file name
						$this->new_img_path->Upload->DbValue = ""; // No need to delete any more
					} else {
						$rsnew['new_img_path'] = ew_UploadFileNameEx(ew_UploadPathEx(TRUE, $this->new_img_path->UploadPath), $rsnew['new_img_path']); // Get new file name
					}
				}
			}

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
					if (!$this->new_img_path->Upload->KeepFile) {
						if (!ew_Empty($this->new_img_path->Upload->Value)) {
							$this->new_img_path->Upload->SaveToFile($this->new_img_path->UploadPath, $rsnew['new_img_path'], TRUE);
						}
						if ($this->new_img_path->Upload->DbValue <> "")
							@unlink(ew_UploadPathEx(TRUE, $this->new_img_path->OldUploadPath) . $this->new_img_path->Upload->DbValue);
					}
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

		// new_img_path
		ew_CleanUploadTempPath($this->new_img_path, $this->new_img_path->Upload->Index);
		return $EditRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "newslist.php", $this->TableVar, TRUE);
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
if (!isset($news_edit)) $news_edit = new cnews_edit();

// Page init
$news_edit->Page_Init();

// Page main
$news_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$news_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var news_edit = new ew_Page("news_edit");
news_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = news_edit.PageID; // For backward compatibility

// Form object
var fnewsedit = new ew_Form("fnewsedit");

// Validate form
fnewsedit.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_new_title");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($news->new_title->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_new_description");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($news->new_description->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_new_content");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($news->new_content->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_new_type");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($news->new_type->FldCaption()) ?>");
			felm = this.GetElements("x" + infix + "_new_img_path");
			elm = this.GetElements("fn_x" + infix + "_new_img_path");
			if (felm && elm && !ew_HasValue(elm))
				return this.OnError(felm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($news->new_img_path->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_new_publish_date");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($news->new_publish_date->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_new_publish_date");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($news->new_publish_date->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_new_cat_id");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($news->new_cat_id->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_new_link_order");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($news->new_link_order->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_new_status");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($news->new_status->FldCaption()) ?>");

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
fnewsedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fnewsedit.ValidateRequired = true;
<?php } else { ?>
fnewsedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fnewsedit.Lists["x_new_cat_id"] = {"LinkField":"x_cat_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_cat_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fnewsedit.Lists["x_new_link_id"] = {"LinkField":"x_new_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_new_title","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $news_edit->ShowPageHeader(); ?>
<?php
$news_edit->ShowMessage();
?>
<form name="fnewsedit" id="fnewsedit" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="news">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table class="ewGrid"><tr><td>
<table id="tbl_newsedit" class="table table-bordered table-striped">
<?php if ($news->new_id->Visible) { // new_id ?>
	<tr id="r_new_id">
		<td><span id="elh_news_new_id"><?php echo $news->new_id->FldCaption() ?></span></td>
		<td<?php echo $news->new_id->CellAttributes() ?>>
<span id="el_news_new_id" class="control-group">
<span<?php echo $news->new_id->ViewAttributes() ?>>
<?php echo $news->new_id->EditValue ?></span>
</span>
<input type="hidden" data-field="x_new_id" name="x_new_id" id="x_new_id" value="<?php echo ew_HtmlEncode($news->new_id->CurrentValue) ?>">
<?php echo $news->new_id->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($news->new_title->Visible) { // new_title ?>
	<tr id="r_new_title">
		<td><span id="elh_news_new_title"><?php echo $news->new_title->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $news->new_title->CellAttributes() ?>>
<span id="el_news_new_title" class="control-group">
<input type="text" data-field="x_new_title" name="x_new_title" id="x_new_title" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($news->new_title->PlaceHolder) ?>" value="<?php echo $news->new_title->EditValue ?>"<?php echo $news->new_title->EditAttributes() ?>>
</span>
<?php echo $news->new_title->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($news->new_description->Visible) { // new_description ?>
	<tr id="r_new_description">
		<td><span id="elh_news_new_description"><?php echo $news->new_description->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $news->new_description->CellAttributes() ?>>
<span id="el_news_new_description" class="control-group">
<input type="text" data-field="x_new_description" name="x_new_description" id="x_new_description" size="30" maxlength="200" placeholder="<?php echo ew_HtmlEncode($news->new_description->PlaceHolder) ?>" value="<?php echo $news->new_description->EditValue ?>"<?php echo $news->new_description->EditAttributes() ?>>
</span>
<?php echo $news->new_description->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($news->new_content->Visible) { // new_content ?>
	<tr id="r_new_content">
		<td><span id="elh_news_new_content"><?php echo $news->new_content->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $news->new_content->CellAttributes() ?>>
<span id="el_news_new_content" class="control-group">
<textarea data-field="x_new_content" class="editor" name="x_new_content" id="x_new_content" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($news->new_content->PlaceHolder) ?>"<?php echo $news->new_content->EditAttributes() ?>><?php echo $news->new_content->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("fnewsedit", "x_new_content", 35, 4, <?php echo ($news->new_content->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
<?php echo $news->new_content->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($news->new_type->Visible) { // new_type ?>
	<tr id="r_new_type">
		<td><span id="elh_news_new_type"><?php echo $news->new_type->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $news->new_type->CellAttributes() ?>>
<span id="el_news_new_type" class="control-group">
<select data-field="x_new_type" id="x_new_type" name="x_new_type"<?php echo $news->new_type->EditAttributes() ?>>
<?php
if (is_array($news->new_type->EditValue)) {
	$arwrk = $news->new_type->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($news->new_type->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $news->new_type->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($news->new_img_path->Visible) { // new_img_path ?>
	<tr id="r_new_img_path">
		<td><span id="elh_news_new_img_path"><?php echo $news->new_img_path->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $news->new_img_path->CellAttributes() ?>>
<span id="el_news_new_img_path" class="control-group">
<span id="fd_x_new_img_path">
<span class="btn btn-small fileinput-button"<?php if ($news->new_img_path->ReadOnly || $news->new_img_path->Disabled) echo " style=\"display: none;\""; ?>>
	<span><?php echo $Language->Phrase("ChooseFile") ?></span>
	<input type="file" data-field="x_new_img_path" name="x_new_img_path" id="x_new_img_path">
</span>
<input type="hidden" name="fn_x_new_img_path" id= "fn_x_new_img_path" value="<?php echo $news->new_img_path->Upload->FileName ?>">
<?php if (@$_POST["fa_x_new_img_path"] == "0") { ?>
<input type="hidden" name="fa_x_new_img_path" id= "fa_x_new_img_path" value="0">
<?php } else { ?>
<input type="hidden" name="fa_x_new_img_path" id= "fa_x_new_img_path" value="1">
<?php } ?>
<input type="hidden" name="fs_x_new_img_path" id= "fs_x_new_img_path" value="150">
</span>
<table id="ft_x_new_img_path" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $news->new_img_path->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($news->new_publish_date->Visible) { // new_publish_date ?>
	<tr id="r_new_publish_date">
		<td><span id="elh_news_new_publish_date"><?php echo $news->new_publish_date->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $news->new_publish_date->CellAttributes() ?>>
<span id="el_news_new_publish_date" class="control-group">
<input type="text" data-field="x_new_publish_date" name="x_new_publish_date" id="x_new_publish_date" placeholder="<?php echo ew_HtmlEncode($news->new_publish_date->PlaceHolder) ?>" value="<?php echo $news->new_publish_date->EditValue ?>"<?php echo $news->new_publish_date->EditAttributes() ?>>
<?php if (!$news->new_publish_date->ReadOnly && !$news->new_publish_date->Disabled && @$news->new_publish_date->EditAttrs["readonly"] == "" && @$news->new_publish_date->EditAttrs["disabled"] == "") { ?>
<button id="cal_x_new_publish_date" name="cal_x_new_publish_date" class="btn" type="button"><img src="phpimages/calendar.png" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" style="border: 0;"></button><script type="text/javascript">
ew_CreateCalendar("fnewsedit", "x_new_publish_date", "%d/%m/%Y");
</script>
<?php } ?>
</span>
<?php echo $news->new_publish_date->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($news->new_cat_id->Visible) { // new_cat_id ?>
	<tr id="r_new_cat_id">
		<td><span id="elh_news_new_cat_id"><?php echo $news->new_cat_id->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $news->new_cat_id->CellAttributes() ?>>
<span id="el_news_new_cat_id" class="control-group">
<select data-field="x_new_cat_id" id="x_new_cat_id" name="x_new_cat_id"<?php echo $news->new_cat_id->EditAttributes() ?>>
<?php
if (is_array($news->new_cat_id->EditValue)) {
	$arwrk = $news->new_cat_id->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($news->new_cat_id->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fnewsedit.Lists["x_new_cat_id"].Options = <?php echo (is_array($news->new_cat_id->EditValue)) ? ew_ArrayToJson($news->new_cat_id->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $news->new_cat_id->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($news->new_link_id->Visible) { // new_link_id ?>
	<tr id="r_new_link_id">
		<td><span id="elh_news_new_link_id"><?php echo $news->new_link_id->FldCaption() ?></span></td>
		<td<?php echo $news->new_link_id->CellAttributes() ?>>
<span id="el_news_new_link_id" class="control-group">
<select data-field="x_new_link_id" id="x_new_link_id" name="x_new_link_id"<?php echo $news->new_link_id->EditAttributes() ?>>
<?php
if (is_array($news->new_link_id->EditValue)) {
	$arwrk = $news->new_link_id->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($news->new_link_id->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fnewsedit.Lists["x_new_link_id"].Options = <?php echo (is_array($news->new_link_id->EditValue)) ? ew_ArrayToJson($news->new_link_id->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $news->new_link_id->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($news->new_link_order->Visible) { // new_link_order ?>
	<tr id="r_new_link_order">
		<td><span id="elh_news_new_link_order"><?php echo $news->new_link_order->FldCaption() ?></span></td>
		<td<?php echo $news->new_link_order->CellAttributes() ?>>
<span id="el_news_new_link_order" class="control-group">
<input type="text" data-field="x_new_link_order" name="x_new_link_order" id="x_new_link_order" size="30" placeholder="<?php echo ew_HtmlEncode($news->new_link_order->PlaceHolder) ?>" value="<?php echo $news->new_link_order->EditValue ?>"<?php echo $news->new_link_order->EditAttributes() ?>>
</span>
<?php echo $news->new_link_order->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($news->new_status->Visible) { // new_status ?>
	<tr id="r_new_status">
		<td><span id="elh_news_new_status"><?php echo $news->new_status->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $news->new_status->CellAttributes() ?>>
<span id="el_news_new_status" class="control-group">
<select data-field="x_new_status" id="x_new_status" name="x_new_status"<?php echo $news->new_status->EditAttributes() ?>>
<?php
if (is_array($news->new_status->EditValue)) {
	$arwrk = $news->new_status->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($news->new_status->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $news->new_status->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("EditBtn") ?></button>
</form>
<script type="text/javascript">
fnewsedit.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$news_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$news_edit->Page_Terminate();
?>
