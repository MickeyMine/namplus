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

$image_gallery_add = NULL; // Initialize page object first

class cimage_gallery_add extends cimage_gallery {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'image_gallery';

	// Page object name
	var $PageObjName = 'image_gallery_add';

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

		// Table object (image_gallery)
		if (!isset($GLOBALS["image_gallery"]) || get_class($GLOBALS["image_gallery"]) == "cimage_gallery") {
			$GLOBALS["image_gallery"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["image_gallery"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'image_gallery', TRUE);

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
			$this->Page_Terminate("image_gallerylist.php");
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
			if (@$_GET["img_id"] != "") {
				$this->img_id->setQueryStringValue($_GET["img_id"]);
				$this->setKey("img_id", $this->img_id->CurrentValue); // Set up key
			} else {
				$this->setKey("img_id", ""); // Clear key
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
					$this->Page_Terminate("image_gallerylist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "image_galleryview.php")
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
		$this->img_path->Upload->Index = $objForm->Index;
		if ($this->img_path->Upload->UploadFile()) {

			// No action required
		} else {
			echo $this->img_path->Upload->Message;
			$this->Page_Terminate();
			exit();
		}
		$this->img_path->CurrentValue = $this->img_path->Upload->FileName;
	}

	// Load default values
	function LoadDefaultValues() {
		$this->img_path->Upload->DbValue = NULL;
		$this->img_path->OldValue = $this->img_path->Upload->DbValue;
		$this->img_path->CurrentValue = NULL; // Clear file related field
		$this->img_description->CurrentValue = NULL;
		$this->img_description->OldValue = $this->img_description->CurrentValue;
		$this->img_cat_id->CurrentValue = NULL;
		$this->img_cat_id->OldValue = $this->img_cat_id->CurrentValue;
		$this->img_new_id->CurrentValue = NULL;
		$this->img_new_id->OldValue = $this->img_new_id->CurrentValue;
		$this->img_offer_id->CurrentValue = NULL;
		$this->img_offer_id->OldValue = $this->img_offer_id->CurrentValue;
		$this->img_nam_archive->CurrentValue = 0;
		$this->img_is_banner->CurrentValue = 0;
		$this->img_order->CurrentValue = NULL;
		$this->img_order->OldValue = $this->img_order->CurrentValue;
		$this->img_status->CurrentValue = NULL;
		$this->img_status->OldValue = $this->img_status->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
		if (!$this->img_description->FldIsDetailKey) {
			$this->img_description->setFormValue($objForm->GetValue("x_img_description"));
		}
		if (!$this->img_cat_id->FldIsDetailKey) {
			$this->img_cat_id->setFormValue($objForm->GetValue("x_img_cat_id"));
		}
		if (!$this->img_new_id->FldIsDetailKey) {
			$this->img_new_id->setFormValue($objForm->GetValue("x_img_new_id"));
		}
		if (!$this->img_offer_id->FldIsDetailKey) {
			$this->img_offer_id->setFormValue($objForm->GetValue("x_img_offer_id"));
		}
		if (!$this->img_nam_archive->FldIsDetailKey) {
			$this->img_nam_archive->setFormValue($objForm->GetValue("x_img_nam_archive"));
		}
		if (!$this->img_is_banner->FldIsDetailKey) {
			$this->img_is_banner->setFormValue($objForm->GetValue("x_img_is_banner"));
		}
		if (!$this->img_order->FldIsDetailKey) {
			$this->img_order->setFormValue($objForm->GetValue("x_img_order"));
		}
		if (!$this->img_status->FldIsDetailKey) {
			$this->img_status->setFormValue($objForm->GetValue("x_img_status"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->img_description->CurrentValue = $this->img_description->FormValue;
		$this->img_cat_id->CurrentValue = $this->img_cat_id->FormValue;
		$this->img_new_id->CurrentValue = $this->img_new_id->FormValue;
		$this->img_offer_id->CurrentValue = $this->img_offer_id->FormValue;
		$this->img_nam_archive->CurrentValue = $this->img_nam_archive->FormValue;
		$this->img_is_banner->CurrentValue = $this->img_is_banner->FormValue;
		$this->img_order->CurrentValue = $this->img_order->FormValue;
		$this->img_status->CurrentValue = $this->img_status->FormValue;
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
				$this->img_path->ImageWidth = 80;
				$this->img_path->ImageHeight = 0;
				$this->img_path->ImageAlt = $this->img_path->FldAlt();
				$this->img_path->ViewValue = ew_UploadPathEx(FALSE, $this->img_path->UploadPath) . $this->img_path->Upload->DbValue;
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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// img_path
			$this->img_path->EditCustomAttributes = "";
			if (!ew_Empty($this->img_path->Upload->DbValue)) {
				$this->img_path->ImageWidth = 80;
				$this->img_path->ImageHeight = 0;
				$this->img_path->ImageAlt = $this->img_path->FldAlt();
				$this->img_path->EditValue = ew_UploadPathEx(FALSE, $this->img_path->UploadPath) . $this->img_path->Upload->DbValue;
			} else {
				$this->img_path->EditValue = "";
			}
			if (!ew_Empty($this->img_path->CurrentValue))
				$this->img_path->Upload->FileName = $this->img_path->CurrentValue;
			if (($this->CurrentAction == "I" || $this->CurrentAction == "C") && !$this->EventCancelled) ew_RenderUploadField($this->img_path);

			// img_description
			$this->img_description->EditCustomAttributes = "";
			$this->img_description->EditValue = ew_HtmlEncode($this->img_description->CurrentValue);
			$this->img_description->PlaceHolder = ew_RemoveHtml($this->img_description->FldCaption());

			// img_cat_id
			$this->img_cat_id->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `cat_id`, `cat_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `categories`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->img_cat_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->img_cat_id->EditValue = $arwrk;

			// img_new_id
			$this->img_new_id->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `new_id`, `new_title` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `news`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->img_new_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->img_new_id->EditValue = $arwrk;

			// img_offer_id
			$this->img_offer_id->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `offer_id`, `offer_title` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `offers`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->img_offer_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->img_offer_id->EditValue = $arwrk;

			// img_nam_archive
			$this->img_nam_archive->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->img_nam_archive->FldTagValue(1), $this->img_nam_archive->FldTagCaption(1) <> "" ? $this->img_nam_archive->FldTagCaption(1) : $this->img_nam_archive->FldTagValue(1));
			$arwrk[] = array($this->img_nam_archive->FldTagValue(2), $this->img_nam_archive->FldTagCaption(2) <> "" ? $this->img_nam_archive->FldTagCaption(2) : $this->img_nam_archive->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->img_nam_archive->EditValue = $arwrk;

			// img_is_banner
			$this->img_is_banner->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->img_is_banner->FldTagValue(1), $this->img_is_banner->FldTagCaption(1) <> "" ? $this->img_is_banner->FldTagCaption(1) : $this->img_is_banner->FldTagValue(1));
			$arwrk[] = array($this->img_is_banner->FldTagValue(2), $this->img_is_banner->FldTagCaption(2) <> "" ? $this->img_is_banner->FldTagCaption(2) : $this->img_is_banner->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->img_is_banner->EditValue = $arwrk;

			// img_order
			$this->img_order->EditCustomAttributes = "";
			$this->img_order->EditValue = ew_HtmlEncode($this->img_order->CurrentValue);
			$this->img_order->PlaceHolder = ew_RemoveHtml($this->img_order->FldCaption());

			// img_status
			$this->img_status->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->img_status->FldTagValue(1), $this->img_status->FldTagCaption(1) <> "" ? $this->img_status->FldTagCaption(1) : $this->img_status->FldTagValue(1));
			$arwrk[] = array($this->img_status->FldTagValue(2), $this->img_status->FldTagCaption(2) <> "" ? $this->img_status->FldTagCaption(2) : $this->img_status->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->img_status->EditValue = $arwrk;

			// Edit refer script
			// img_path

			$this->img_path->HrefValue = "";
			$this->img_path->HrefValue2 = $this->img_path->UploadPath . $this->img_path->Upload->DbValue;

			// img_description
			$this->img_description->HrefValue = "";

			// img_cat_id
			$this->img_cat_id->HrefValue = "";

			// img_new_id
			$this->img_new_id->HrefValue = "";

			// img_offer_id
			$this->img_offer_id->HrefValue = "";

			// img_nam_archive
			$this->img_nam_archive->HrefValue = "";

			// img_is_banner
			$this->img_is_banner->HrefValue = "";

			// img_order
			$this->img_order->HrefValue = "";

			// img_status
			$this->img_status->HrefValue = "";
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
		if (is_null($this->img_path->Upload->Value) && !$this->img_path->Upload->KeepFile) {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->img_path->FldCaption());
		}
		if (!$this->img_description->FldIsDetailKey && !is_null($this->img_description->FormValue) && $this->img_description->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->img_description->FldCaption());
		}
		if (!$this->img_is_banner->FldIsDetailKey && !is_null($this->img_is_banner->FormValue) && $this->img_is_banner->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->img_is_banner->FldCaption());
		}
		if (!$this->img_order->FldIsDetailKey && !is_null($this->img_order->FormValue) && $this->img_order->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->img_order->FldCaption());
		}
		if (!ew_CheckInteger($this->img_order->FormValue)) {
			ew_AddMessage($gsFormError, $this->img_order->FldErrMsg());
		}
		if (!$this->img_status->FldIsDetailKey && !is_null($this->img_status->FormValue) && $this->img_status->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->img_status->FldCaption());
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

		// img_path
		if (!$this->img_path->Upload->KeepFile) {
			if ($this->img_path->Upload->FileName == "") {
				$rsnew['img_path'] = NULL;
			} else {
				$rsnew['img_path'] = $this->img_path->Upload->FileName;
			}
		}

		// img_description
		$this->img_description->SetDbValueDef($rsnew, $this->img_description->CurrentValue, "", FALSE);

		// img_cat_id
		$this->img_cat_id->SetDbValueDef($rsnew, $this->img_cat_id->CurrentValue, NULL, FALSE);

		// img_new_id
		$this->img_new_id->SetDbValueDef($rsnew, $this->img_new_id->CurrentValue, NULL, FALSE);

		// img_offer_id
		$this->img_offer_id->SetDbValueDef($rsnew, $this->img_offer_id->CurrentValue, NULL, FALSE);

		// img_nam_archive
		$this->img_nam_archive->SetDbValueDef($rsnew, $this->img_nam_archive->CurrentValue, 0, strval($this->img_nam_archive->CurrentValue) == "");

		// img_is_banner
		$this->img_is_banner->SetDbValueDef($rsnew, $this->img_is_banner->CurrentValue, 0, strval($this->img_is_banner->CurrentValue) == "");

		// img_order
		$this->img_order->SetDbValueDef($rsnew, $this->img_order->CurrentValue, 0, FALSE);

		// img_status
		$this->img_status->SetDbValueDef($rsnew, $this->img_status->CurrentValue, 0, FALSE);
		if (!$this->img_path->Upload->KeepFile) {
			if (!ew_Empty($this->img_path->Upload->Value)) {
				if ($this->img_path->Upload->FileName == $this->img_path->Upload->DbValue) { // Overwrite if same file name
					$this->img_path->Upload->DbValue = ""; // No need to delete any more
				} else {
					$rsnew['img_path'] = ew_UploadFileNameEx(ew_UploadPathEx(TRUE, $this->img_path->UploadPath), $rsnew['img_path']); // Get new file name
				}
			}
		}

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
				if (!$this->img_path->Upload->KeepFile) {
					if (!ew_Empty($this->img_path->Upload->Value)) {
						$this->img_path->Upload->SaveToFile($this->img_path->UploadPath, $rsnew['img_path'], TRUE);
					}
					if ($this->img_path->Upload->DbValue <> "")
						@unlink(ew_UploadPathEx(TRUE, $this->img_path->OldUploadPath) . $this->img_path->Upload->DbValue);
				}
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
			$this->img_id->setDbValue($conn->Insert_ID());
			$rsnew['img_id'] = $this->img_id->DbValue;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
		}

		// img_path
		ew_CleanUploadTempPath($this->img_path, $this->img_path->Upload->Index);
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "image_gallerylist.php", $this->TableVar, TRUE);
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
if (!isset($image_gallery_add)) $image_gallery_add = new cimage_gallery_add();

// Page init
$image_gallery_add->Page_Init();

// Page main
$image_gallery_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$image_gallery_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var image_gallery_add = new ew_Page("image_gallery_add");
image_gallery_add.PageID = "add"; // Page ID
var EW_PAGE_ID = image_gallery_add.PageID; // For backward compatibility

// Form object
var fimage_galleryadd = new ew_Form("fimage_galleryadd");

// Validate form
fimage_galleryadd.Validate = function() {
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
			felm = this.GetElements("x" + infix + "_img_path");
			elm = this.GetElements("fn_x" + infix + "_img_path");
			if (felm && elm && !ew_HasValue(elm))
				return this.OnError(felm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($image_gallery->img_path->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_img_description");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($image_gallery->img_description->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_img_is_banner");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($image_gallery->img_is_banner->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_img_order");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($image_gallery->img_order->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_img_order");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($image_gallery->img_order->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_img_status");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($image_gallery->img_status->FldCaption()) ?>");

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
fimage_galleryadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fimage_galleryadd.ValidateRequired = true;
<?php } else { ?>
fimage_galleryadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fimage_galleryadd.Lists["x_img_cat_id"] = {"LinkField":"x_cat_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_cat_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fimage_galleryadd.Lists["x_img_new_id"] = {"LinkField":"x_new_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_new_title","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fimage_galleryadd.Lists["x_img_offer_id"] = {"LinkField":"x_offer_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_offer_title","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $image_gallery_add->ShowPageHeader(); ?>
<?php
$image_gallery_add->ShowMessage();
?>
<form name="fimage_galleryadd" id="fimage_galleryadd" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="image_gallery">
<input type="hidden" name="a_add" id="a_add" value="A">
<table class="ewGrid"><tr><td>
<table id="tbl_image_galleryadd" class="table table-bordered table-striped">
<?php if ($image_gallery->img_path->Visible) { // img_path ?>
	<tr id="r_img_path">
		<td><span id="elh_image_gallery_img_path"><?php echo $image_gallery->img_path->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $image_gallery->img_path->CellAttributes() ?>>
<span id="el_image_gallery_img_path" class="control-group">
<span id="fd_x_img_path">
<span class="btn btn-small fileinput-button"<?php if ($image_gallery->img_path->ReadOnly || $image_gallery->img_path->Disabled) echo " style=\"display: none;\""; ?>>
	<span><?php echo $Language->Phrase("ChooseFile") ?></span>
	<input type="file" data-field="x_img_path" name="x_img_path" id="x_img_path">
</span>
<input type="hidden" name="fn_x_img_path" id= "fn_x_img_path" value="<?php echo $image_gallery->img_path->Upload->FileName ?>">
<input type="hidden" name="fa_x_img_path" id= "fa_x_img_path" value="0">
<input type="hidden" name="fs_x_img_path" id= "fs_x_img_path" value="150">
</span>
<table id="ft_x_img_path" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $image_gallery->img_path->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($image_gallery->img_description->Visible) { // img_description ?>
	<tr id="r_img_description">
		<td><span id="elh_image_gallery_img_description"><?php echo $image_gallery->img_description->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $image_gallery->img_description->CellAttributes() ?>>
<span id="el_image_gallery_img_description" class="control-group">
<input type="text" data-field="x_img_description" name="x_img_description" id="x_img_description" size="30" maxlength="150" placeholder="<?php echo ew_HtmlEncode($image_gallery->img_description->PlaceHolder) ?>" value="<?php echo $image_gallery->img_description->EditValue ?>"<?php echo $image_gallery->img_description->EditAttributes() ?>>
</span>
<?php echo $image_gallery->img_description->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($image_gallery->img_cat_id->Visible) { // img_cat_id ?>
	<tr id="r_img_cat_id">
		<td><span id="elh_image_gallery_img_cat_id"><?php echo $image_gallery->img_cat_id->FldCaption() ?></span></td>
		<td<?php echo $image_gallery->img_cat_id->CellAttributes() ?>>
<span id="el_image_gallery_img_cat_id" class="control-group">
<select data-field="x_img_cat_id" id="x_img_cat_id" name="x_img_cat_id"<?php echo $image_gallery->img_cat_id->EditAttributes() ?>>
<?php
if (is_array($image_gallery->img_cat_id->EditValue)) {
	$arwrk = $image_gallery->img_cat_id->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($image_gallery->img_cat_id->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fimage_galleryadd.Lists["x_img_cat_id"].Options = <?php echo (is_array($image_gallery->img_cat_id->EditValue)) ? ew_ArrayToJson($image_gallery->img_cat_id->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $image_gallery->img_cat_id->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($image_gallery->img_new_id->Visible) { // img_new_id ?>
	<tr id="r_img_new_id">
		<td><span id="elh_image_gallery_img_new_id"><?php echo $image_gallery->img_new_id->FldCaption() ?></span></td>
		<td<?php echo $image_gallery->img_new_id->CellAttributes() ?>>
<span id="el_image_gallery_img_new_id" class="control-group">
<select data-field="x_img_new_id" id="x_img_new_id" name="x_img_new_id"<?php echo $image_gallery->img_new_id->EditAttributes() ?>>
<?php
if (is_array($image_gallery->img_new_id->EditValue)) {
	$arwrk = $image_gallery->img_new_id->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($image_gallery->img_new_id->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fimage_galleryadd.Lists["x_img_new_id"].Options = <?php echo (is_array($image_gallery->img_new_id->EditValue)) ? ew_ArrayToJson($image_gallery->img_new_id->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $image_gallery->img_new_id->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($image_gallery->img_offer_id->Visible) { // img_offer_id ?>
	<tr id="r_img_offer_id">
		<td><span id="elh_image_gallery_img_offer_id"><?php echo $image_gallery->img_offer_id->FldCaption() ?></span></td>
		<td<?php echo $image_gallery->img_offer_id->CellAttributes() ?>>
<span id="el_image_gallery_img_offer_id" class="control-group">
<select data-field="x_img_offer_id" id="x_img_offer_id" name="x_img_offer_id"<?php echo $image_gallery->img_offer_id->EditAttributes() ?>>
<?php
if (is_array($image_gallery->img_offer_id->EditValue)) {
	$arwrk = $image_gallery->img_offer_id->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($image_gallery->img_offer_id->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fimage_galleryadd.Lists["x_img_offer_id"].Options = <?php echo (is_array($image_gallery->img_offer_id->EditValue)) ? ew_ArrayToJson($image_gallery->img_offer_id->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $image_gallery->img_offer_id->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($image_gallery->img_nam_archive->Visible) { // img_nam_archive ?>
	<tr id="r_img_nam_archive">
		<td><span id="elh_image_gallery_img_nam_archive"><?php echo $image_gallery->img_nam_archive->FldCaption() ?></span></td>
		<td<?php echo $image_gallery->img_nam_archive->CellAttributes() ?>>
<span id="el_image_gallery_img_nam_archive" class="control-group">
<select data-field="x_img_nam_archive" id="x_img_nam_archive" name="x_img_nam_archive"<?php echo $image_gallery->img_nam_archive->EditAttributes() ?>>
<?php
if (is_array($image_gallery->img_nam_archive->EditValue)) {
	$arwrk = $image_gallery->img_nam_archive->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($image_gallery->img_nam_archive->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $image_gallery->img_nam_archive->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($image_gallery->img_is_banner->Visible) { // img_is_banner ?>
	<tr id="r_img_is_banner">
		<td><span id="elh_image_gallery_img_is_banner"><?php echo $image_gallery->img_is_banner->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $image_gallery->img_is_banner->CellAttributes() ?>>
<span id="el_image_gallery_img_is_banner" class="control-group">
<select data-field="x_img_is_banner" id="x_img_is_banner" name="x_img_is_banner"<?php echo $image_gallery->img_is_banner->EditAttributes() ?>>
<?php
if (is_array($image_gallery->img_is_banner->EditValue)) {
	$arwrk = $image_gallery->img_is_banner->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($image_gallery->img_is_banner->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $image_gallery->img_is_banner->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($image_gallery->img_order->Visible) { // img_order ?>
	<tr id="r_img_order">
		<td><span id="elh_image_gallery_img_order"><?php echo $image_gallery->img_order->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $image_gallery->img_order->CellAttributes() ?>>
<span id="el_image_gallery_img_order" class="control-group">
<input type="text" data-field="x_img_order" name="x_img_order" id="x_img_order" size="30" placeholder="<?php echo ew_HtmlEncode($image_gallery->img_order->PlaceHolder) ?>" value="<?php echo $image_gallery->img_order->EditValue ?>"<?php echo $image_gallery->img_order->EditAttributes() ?>>
</span>
<?php echo $image_gallery->img_order->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($image_gallery->img_status->Visible) { // img_status ?>
	<tr id="r_img_status">
		<td><span id="elh_image_gallery_img_status"><?php echo $image_gallery->img_status->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $image_gallery->img_status->CellAttributes() ?>>
<span id="el_image_gallery_img_status" class="control-group">
<select data-field="x_img_status" id="x_img_status" name="x_img_status"<?php echo $image_gallery->img_status->EditAttributes() ?>>
<?php
if (is_array($image_gallery->img_status->EditValue)) {
	$arwrk = $image_gallery->img_status->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($image_gallery->img_status->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $image_gallery->img_status->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
</form>
<script type="text/javascript">
fimage_galleryadd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$image_gallery_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$image_gallery_add->Page_Terminate();
?>
