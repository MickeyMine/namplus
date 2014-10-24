<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "offer_questionsinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$offer_questions_delete = NULL; // Initialize page object first

class coffer_questions_delete extends coffer_questions {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{621448A2-A15A-4302-8B90-FC8E171BD28F}";

	// Table name
	var $TableName = 'offer_questions';

	// Page object name
	var $PageObjName = 'offer_questions_delete';

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

		// Table object (offer_questions)
		if (!isset($GLOBALS["offer_questions"]) || get_class($GLOBALS["offer_questions"]) == "coffer_questions") {
			$GLOBALS["offer_questions"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["offer_questions"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'offer_questions', TRUE);

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
		if (!$Security->CanDelete()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("offer_questionslist.php");
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->question_id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
	var $TotalRecs = 0;
	var $RecCnt;
	var $RecKeys = array();
	var $Recordset;
	var $StartRowCnt = 1;
	var $RowCnt = 0;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load key parameters
		$this->RecKeys = $this->GetRecordKeys(); // Load record keys
		$sFilter = $this->GetKeyFilter();
		if ($sFilter == "")
			$this->Page_Terminate("offer_questionslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in offer_questions class, offer_questionsinfo.php

		$this->CurrentFilter = $sFilter;

		// Get action
		if (@$_POST["a_delete"] <> "") {
			$this->CurrentAction = $_POST["a_delete"];
		} else {
			$this->CurrentAction = "I"; // Display record
		}
		switch ($this->CurrentAction) {
			case "D": // Delete
				$this->SendEmail = TRUE; // Send email on delete success
				if ($this->DeleteRows()) { // Delete rows
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("DeleteSuccess")); // Set up success message
					$this->Page_Terminate($this->getReturnUrl()); // Return to caller
				}
		}
	}

// No functions
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
		$this->question_id->setDbValue($rs->fields('question_id'));
		$this->question_content->setDbValue($rs->fields('question_content'));
		$this->question_type->setDbValue($rs->fields('question_type'));
		$this->offer_id->setDbValue($rs->fields('offer_id'));
		$this->question_status->setDbValue($rs->fields('question_status'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->question_id->DbValue = $row['question_id'];
		$this->question_content->DbValue = $row['question_content'];
		$this->question_type->DbValue = $row['question_type'];
		$this->offer_id->DbValue = $row['offer_id'];
		$this->question_status->DbValue = $row['question_status'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// question_id
		// question_content
		// question_type
		// offer_id
		// question_status

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// question_id
			$this->question_id->ViewValue = $this->question_id->CurrentValue;
			$this->question_id->ViewCustomAttributes = "";

			// question_type
			if (strval($this->question_type->CurrentValue) <> "") {
				switch ($this->question_type->CurrentValue) {
					case $this->question_type->FldTagValue(1):
						$this->question_type->ViewValue = $this->question_type->FldTagCaption(1) <> "" ? $this->question_type->FldTagCaption(1) : $this->question_type->CurrentValue;
						break;
					case $this->question_type->FldTagValue(2):
						$this->question_type->ViewValue = $this->question_type->FldTagCaption(2) <> "" ? $this->question_type->FldTagCaption(2) : $this->question_type->CurrentValue;
						break;
					default:
						$this->question_type->ViewValue = $this->question_type->CurrentValue;
				}
			} else {
				$this->question_type->ViewValue = NULL;
			}
			$this->question_type->ViewCustomAttributes = "";

			// offer_id
			if (strval($this->offer_id->CurrentValue) <> "") {
				$sFilterWrk = "`offer_id`" . ew_SearchString("=", $this->offer_id->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `offer_id`, `offer_title` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `offers`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->offer_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->offer_id->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->offer_id->ViewValue = $this->offer_id->CurrentValue;
				}
			} else {
				$this->offer_id->ViewValue = NULL;
			}
			$this->offer_id->ViewCustomAttributes = "";

			// question_status
			if (strval($this->question_status->CurrentValue) <> "") {
				switch ($this->question_status->CurrentValue) {
					case $this->question_status->FldTagValue(1):
						$this->question_status->ViewValue = $this->question_status->FldTagCaption(1) <> "" ? $this->question_status->FldTagCaption(1) : $this->question_status->CurrentValue;
						break;
					case $this->question_status->FldTagValue(2):
						$this->question_status->ViewValue = $this->question_status->FldTagCaption(2) <> "" ? $this->question_status->FldTagCaption(2) : $this->question_status->CurrentValue;
						break;
					default:
						$this->question_status->ViewValue = $this->question_status->CurrentValue;
				}
			} else {
				$this->question_status->ViewValue = NULL;
			}
			$this->question_status->ViewCustomAttributes = "";

			// question_id
			$this->question_id->LinkCustomAttributes = "";
			$this->question_id->HrefValue = "";
			$this->question_id->TooltipValue = "";

			// question_type
			$this->question_type->LinkCustomAttributes = "";
			$this->question_type->HrefValue = "";
			$this->question_type->TooltipValue = "";

			// offer_id
			$this->offer_id->LinkCustomAttributes = "";
			$this->offer_id->HrefValue = "";
			$this->offer_id->TooltipValue = "";

			// question_status
			$this->question_status->LinkCustomAttributes = "";
			$this->question_status->HrefValue = "";
			$this->question_status->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	//
	// Delete records based on current filter
	//
	function DeleteRows() {
		global $conn, $Language, $Security;
		if (!$Security->CanDelete()) {
			$this->setFailureMessage($Language->Phrase("NoDeletePermission")); // No delete permission
			return FALSE;
		}
		$DeleteRows = TRUE;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE) {
			return FALSE;
		} elseif ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
			$rs->Close();
			return FALSE;

		//} else {
		//	$this->LoadRowValues($rs); // Load row values

		}
		$conn->BeginTrans();

		// Clone old rows
		$rsold = ($rs) ? $rs->GetRows() : array();
		if ($rs)
			$rs->Close();

		// Call row deleting event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$DeleteRows = $this->Row_Deleting($row);
				if (!$DeleteRows) break;
			}
		}
		if ($DeleteRows) {
			$sKey = "";
			foreach ($rsold as $row) {
				$sThisKey = "";
				if ($sThisKey <> "") $sThisKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
				$sThisKey .= $row['question_id'];
				$this->LoadDbValues($row);
				$conn->raiseErrorFn = 'ew_ErrorFn';
				$DeleteRows = $this->Delete($row); // Delete
				$conn->raiseErrorFn = '';
				if ($DeleteRows === FALSE)
					break;
				if ($sKey <> "") $sKey .= ", ";
				$sKey .= $sThisKey;
			}
		} else {

			// Set up error message
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("DeleteCancelled"));
			}
		}
		if ($DeleteRows) {
			$conn->CommitTrans(); // Commit the changes
		} else {
			$conn->RollbackTrans(); // Rollback changes
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "offer_questionslist.php", $this->TableVar, TRUE);
		$PageId = "delete";
		$Breadcrumb->Add("delete", $PageId, ew_CurrentUrl());
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
if (!isset($offer_questions_delete)) $offer_questions_delete = new coffer_questions_delete();

// Page init
$offer_questions_delete->Page_Init();

// Page main
$offer_questions_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$offer_questions_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var offer_questions_delete = new ew_Page("offer_questions_delete");
offer_questions_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = offer_questions_delete.PageID; // For backward compatibility

// Form object
var foffer_questionsdelete = new ew_Form("foffer_questionsdelete");

// Form_CustomValidate event
foffer_questionsdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
foffer_questionsdelete.ValidateRequired = true;
<?php } else { ?>
foffer_questionsdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
foffer_questionsdelete.Lists["x_offer_id"] = {"LinkField":"x_offer_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_offer_title","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($offer_questions_delete->Recordset = $offer_questions_delete->LoadRecordset())
	$offer_questions_deleteTotalRecs = $offer_questions_delete->Recordset->RecordCount(); // Get record count
if ($offer_questions_deleteTotalRecs <= 0) { // No record found, exit
	if ($offer_questions_delete->Recordset)
		$offer_questions_delete->Recordset->Close();
	$offer_questions_delete->Page_Terminate("offer_questionslist.php"); // Return to list
}
?>
<?php $Breadcrumb->Render(); ?>
<?php $offer_questions_delete->ShowPageHeader(); ?>
<?php
$offer_questions_delete->ShowMessage();
?>
<form name="foffer_questionsdelete" id="foffer_questionsdelete" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="offer_questions">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($offer_questions_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_offer_questionsdelete" class="ewTable ewTableSeparate">
<?php echo $offer_questions->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($offer_questions->question_id->Visible) { // question_id ?>
		<td><span id="elh_offer_questions_question_id" class="offer_questions_question_id"><?php echo $offer_questions->question_id->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_questions->question_type->Visible) { // question_type ?>
		<td><span id="elh_offer_questions_question_type" class="offer_questions_question_type"><?php echo $offer_questions->question_type->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_questions->offer_id->Visible) { // offer_id ?>
		<td><span id="elh_offer_questions_offer_id" class="offer_questions_offer_id"><?php echo $offer_questions->offer_id->FldCaption() ?></span></td>
<?php } ?>
<?php if ($offer_questions->question_status->Visible) { // question_status ?>
		<td><span id="elh_offer_questions_question_status" class="offer_questions_question_status"><?php echo $offer_questions->question_status->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$offer_questions_delete->RecCnt = 0;
$i = 0;
while (!$offer_questions_delete->Recordset->EOF) {
	$offer_questions_delete->RecCnt++;
	$offer_questions_delete->RowCnt++;

	// Set row properties
	$offer_questions->ResetAttrs();
	$offer_questions->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$offer_questions_delete->LoadRowValues($offer_questions_delete->Recordset);

	// Render row
	$offer_questions_delete->RenderRow();
?>
	<tr<?php echo $offer_questions->RowAttributes() ?>>
<?php if ($offer_questions->question_id->Visible) { // question_id ?>
		<td<?php echo $offer_questions->question_id->CellAttributes() ?>>
<span id="el<?php echo $offer_questions_delete->RowCnt ?>_offer_questions_question_id" class="control-group offer_questions_question_id">
<span<?php echo $offer_questions->question_id->ViewAttributes() ?>>
<?php echo $offer_questions->question_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($offer_questions->question_type->Visible) { // question_type ?>
		<td<?php echo $offer_questions->question_type->CellAttributes() ?>>
<span id="el<?php echo $offer_questions_delete->RowCnt ?>_offer_questions_question_type" class="control-group offer_questions_question_type">
<span<?php echo $offer_questions->question_type->ViewAttributes() ?>>
<?php echo $offer_questions->question_type->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($offer_questions->offer_id->Visible) { // offer_id ?>
		<td<?php echo $offer_questions->offer_id->CellAttributes() ?>>
<span id="el<?php echo $offer_questions_delete->RowCnt ?>_offer_questions_offer_id" class="control-group offer_questions_offer_id">
<span<?php echo $offer_questions->offer_id->ViewAttributes() ?>>
<?php echo $offer_questions->offer_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($offer_questions->question_status->Visible) { // question_status ?>
		<td<?php echo $offer_questions->question_status->CellAttributes() ?>>
<span id="el<?php echo $offer_questions_delete->RowCnt ?>_offer_questions_question_status" class="control-group offer_questions_question_status">
<span<?php echo $offer_questions->question_status->ViewAttributes() ?>>
<?php echo $offer_questions->question_status->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$offer_questions_delete->Recordset->MoveNext();
}
$offer_questions_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</td></tr></table>
<div class="btn-group ewButtonGroup">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("DeleteBtn") ?></button>
</div>
</form>
<script type="text/javascript">
foffer_questionsdelete.Init();
</script>
<?php
$offer_questions_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$offer_questions_delete->Page_Terminate();
?>
