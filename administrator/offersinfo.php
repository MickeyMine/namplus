<?php

// Global variable for table object
$offers = NULL;

//
// Table class for offers
//
class coffers extends cTable {
	var $offer_id;
	var $offer_title;
	var $offer_description;
	var $offer_content;
	var $offer_question_content;
	var $offer_image_path;
	var $offer_top_image;
	var $offer_bottom_image;
	var $offer_start_date;
	var $offer_end_date;
	var $offer_start_time;
	var $offer_end_time;
	var $offer_rules;
	var $offer_value;
	var $offer_cat_id;
	var $offer_status;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'offers';
		$this->TableName = 'offers';
		$this->TableType = 'TABLE';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)
		$this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
		$this->ExportPageSize = "a4"; // Page size (PDF only)
		$this->DetailAdd = FALSE; // Allow detail add
		$this->DetailEdit = FALSE; // Allow detail edit
		$this->DetailView = FALSE; // Allow detail view
		$this->ShowMultipleDetails = FALSE; // Show multiple details
		$this->GridAddRowCount = 5;
		$this->AllowAddDeleteRow = ew_AllowAddDeleteRow(); // Allow add/delete row
		$this->UserIDAllowSecurity = 0; // User ID Allow
		$this->BasicSearch = new cBasicSearch($this->TableVar);

		// offer_id
		$this->offer_id = new cField('offers', 'offers', 'x_offer_id', 'offer_id', '`offer_id`', '`offer_id`', 3, -1, FALSE, '`offer_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->offer_id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['offer_id'] = &$this->offer_id;

		// offer_title
		$this->offer_title = new cField('offers', 'offers', 'x_offer_title', 'offer_title', '`offer_title`', '`offer_title`', 200, -1, FALSE, '`offer_title`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['offer_title'] = &$this->offer_title;

		// offer_description
		$this->offer_description = new cField('offers', 'offers', 'x_offer_description', 'offer_description', '`offer_description`', '`offer_description`', 200, -1, FALSE, '`offer_description`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['offer_description'] = &$this->offer_description;

		// offer_content
		$this->offer_content = new cField('offers', 'offers', 'x_offer_content', 'offer_content', '`offer_content`', '`offer_content`', 201, -1, FALSE, '`offer_content`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['offer_content'] = &$this->offer_content;

		// offer_question_content
		$this->offer_question_content = new cField('offers', 'offers', 'x_offer_question_content', 'offer_question_content', '`offer_question_content`', '`offer_question_content`', 201, -1, FALSE, '`offer_question_content`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['offer_question_content'] = &$this->offer_question_content;

		// offer_image_path
		$this->offer_image_path = new cField('offers', 'offers', 'x_offer_image_path', 'offer_image_path', '`offer_image_path`', '`offer_image_path`', 200, -1, TRUE, '`offer_image_path`', FALSE, FALSE, FALSE, 'IMAGE');
		$this->fields['offer_image_path'] = &$this->offer_image_path;

		// offer_top_image
		$this->offer_top_image = new cField('offers', 'offers', 'x_offer_top_image', 'offer_top_image', '`offer_top_image`', '`offer_top_image`', 200, -1, TRUE, '`offer_top_image`', FALSE, FALSE, FALSE, 'IMAGE');
		$this->fields['offer_top_image'] = &$this->offer_top_image;

		// offer_bottom_image
		$this->offer_bottom_image = new cField('offers', 'offers', 'x_offer_bottom_image', 'offer_bottom_image', '`offer_bottom_image`', '`offer_bottom_image`', 200, -1, TRUE, '`offer_bottom_image`', FALSE, FALSE, FALSE, 'IMAGE');
		$this->fields['offer_bottom_image'] = &$this->offer_bottom_image;

		// offer_start_date
		$this->offer_start_date = new cField('offers', 'offers', 'x_offer_start_date', 'offer_start_date', '`offer_start_date`', 'DATE_FORMAT(`offer_start_date`, \'%d/%m/%Y\')', 133, 7, FALSE, '`offer_start_date`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->offer_start_date->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateDMY"));
		$this->fields['offer_start_date'] = &$this->offer_start_date;

		// offer_end_date
		$this->offer_end_date = new cField('offers', 'offers', 'x_offer_end_date', 'offer_end_date', '`offer_end_date`', 'DATE_FORMAT(`offer_end_date`, \'%d/%m/%Y\')', 133, 7, FALSE, '`offer_end_date`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->offer_end_date->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateDMY"));
		$this->fields['offer_end_date'] = &$this->offer_end_date;

		// offer_start_time
		$this->offer_start_time = new cField('offers', 'offers', 'x_offer_start_time', 'offer_start_time', '`offer_start_time`', 'DATE_FORMAT(`offer_start_time`, \'%d/%m/%Y\')', 134, -1, FALSE, '`offer_start_time`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->offer_start_time->FldDefaultErrMsg = $Language->Phrase("IncorrectTime");
		$this->fields['offer_start_time'] = &$this->offer_start_time;

		// offer_end_time
		$this->offer_end_time = new cField('offers', 'offers', 'x_offer_end_time', 'offer_end_time', '`offer_end_time`', 'DATE_FORMAT(`offer_end_time`, \'%d/%m/%Y\')', 134, -1, FALSE, '`offer_end_time`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->offer_end_time->FldDefaultErrMsg = $Language->Phrase("IncorrectTime");
		$this->fields['offer_end_time'] = &$this->offer_end_time;

		// offer_rules
		$this->offer_rules = new cField('offers', 'offers', 'x_offer_rules', 'offer_rules', '`offer_rules`', '`offer_rules`', 201, -1, FALSE, '`offer_rules`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['offer_rules'] = &$this->offer_rules;

		// offer_value
		$this->offer_value = new cField('offers', 'offers', 'x_offer_value', 'offer_value', '`offer_value`', '`offer_value`', 200, -1, FALSE, '`offer_value`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['offer_value'] = &$this->offer_value;

		// offer_cat_id
		$this->offer_cat_id = new cField('offers', 'offers', 'x_offer_cat_id', 'offer_cat_id', '`offer_cat_id`', '`offer_cat_id`', 3, -1, FALSE, '`offer_cat_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->offer_cat_id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['offer_cat_id'] = &$this->offer_cat_id;

		// offer_status
		$this->offer_status = new cField('offers', 'offers', 'x_offer_status', 'offer_status', '`offer_status`', '`offer_status`', 16, -1, FALSE, '`offer_status`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->offer_status->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['offer_status'] = &$this->offer_status;
	}

	// Single column sort
	function UpdateSort(&$ofld) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sSortField = $ofld->FldExpression;
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
			$this->setSessionOrderBy($sSortField . " " . $sThisSort); // Save to Session
		} else {
			$ofld->setSort("");
		}
	}

	// Table level SQL
	function SqlFrom() { // From
		return "`offers`";
	}

	function SqlSelect() { // Select
		return "SELECT * FROM " . $this->SqlFrom();
	}

	function SqlWhere() { // Where
		$sWhere = "";
		$this->TableFilter = "";
		ew_AddFilter($sWhere, $this->TableFilter);
		return $sWhere;
	}

	function SqlGroupBy() { // Group By
		return "";
	}

	function SqlHaving() { // Having
		return "";
	}

	function SqlOrderBy() { // Order By
		return "";
	}

	// Check if Anonymous User is allowed
	function AllowAnonymousUser() {
		switch (@$this->PageID) {
			case "add":
			case "register":
			case "addopt":
				return FALSE;
			case "edit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return FALSE;
			case "delete":
				return FALSE;
			case "view":
				return FALSE;
			case "search":
				return FALSE;
			default:
				return FALSE;
		}
	}

	// Apply User ID filters
	function ApplyUserIDFilters($sFilter) {
		return $sFilter;
	}

	// Check if User ID security allows view all
	function UserIDAllow($id = "") {
		$allow = EW_USER_ID_ALLOW;
		switch ($id) {
			case "add":
			case "copy":
			case "gridadd":
			case "register":
			case "addopt":
				return (($allow & 1) == 1);
			case "edit":
			case "gridedit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return (($allow & 4) == 4);
			case "delete":
				return (($allow & 2) == 2);
			case "view":
				return (($allow & 32) == 32);
			case "search":
				return (($allow & 64) == 64);
			default:
				return (($allow & 8) == 8);
		}
	}

	// Get SQL
	function GetSQL($where, $orderby) {
		return ew_BuildSelectSql($this->SqlSelect(), $this->SqlWhere(),
			$this->SqlGroupBy(), $this->SqlHaving(), $this->SqlOrderBy(),
			$where, $orderby);
	}

	// Table SQL
	function SQL() {
		$sFilter = $this->CurrentFilter;
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->SqlSelect(), $this->SqlWhere(),
			$this->SqlGroupBy(), $this->SqlHaving(), $this->SqlOrderBy(),
			$sFilter, $sSort);
	}

	// Table SQL with List page filter
	function SelectSQL() {
		$sFilter = $this->getSessionWhere();
		ew_AddFilter($sFilter, $this->CurrentFilter);
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->SqlSelect(), $this->SqlWhere(), $this->SqlGroupBy(),
			$this->SqlHaving(), $this->SqlOrderBy(), $sFilter, $sSort);
	}

	// Get ORDER BY clause
	function GetOrderBy() {
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql("", "", "", "", $this->SqlOrderBy(), "", $sSort);
	}

	// Try to get record count
	function TryGetRecordCount($sSql) {
		global $conn;
		$cnt = -1;
		if ($this->TableType == 'TABLE' || $this->TableType == 'VIEW') {
			$sSql = "SELECT COUNT(*) FROM" . preg_replace('/^SELECT\s([\s\S]+)?\*\sFROM/i', "", $sSql);
			$sOrderBy = $this->GetOrderBy();
			if (substr($sSql, strlen($sOrderBy) * -1) == $sOrderBy)
				$sSql = substr($sSql, 0, strlen($sSql) - strlen($sOrderBy)); // Remove ORDER BY clause
		} else {
			$sSql = "SELECT COUNT(*) FROM (" . $sSql . ") EW_COUNT_TABLE";
		}
		if ($rs = $conn->Execute($sSql)) {
			if (!$rs->EOF && $rs->FieldCount() > 0) {
				$cnt = $rs->fields[0];
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// Get record count based on filter (for detail record count in master table pages)
	function LoadRecordCount($sFilter) {
		$origFilter = $this->CurrentFilter;
		$this->CurrentFilter = $sFilter;
		$this->Recordset_Selecting($this->CurrentFilter);

		//$sSql = $this->SQL();
		$sSql = $this->GetSQL($this->CurrentFilter, "");
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $this->LoadRs($this->CurrentFilter)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		$this->CurrentFilter = $origFilter;
		return intval($cnt);
	}

	// Get record count (for current List page)
	function SelectRecordCount() {
		global $conn;
		$origFilter = $this->CurrentFilter;
		$this->Recordset_Selecting($this->CurrentFilter);
		$sSql = $this->SelectSQL();
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $conn->Execute($sSql)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		$this->CurrentFilter = $origFilter;
		return intval($cnt);
	}

	// Update Table
	var $UpdateTable = "`offers`";

	// INSERT statement
	function InsertSQL(&$rs) {
		global $conn;
		$names = "";
		$values = "";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]))
				continue;
			$names .= $this->fields[$name]->FldExpression . ",";
			$values .= ew_QuotedValue($value, $this->fields[$name]->FldDataType) . ",";
		}
		while (substr($names, -1) == ",")
			$names = substr($names, 0, -1);
		while (substr($values, -1) == ",")
			$values = substr($values, 0, -1);
		return "INSERT INTO " . $this->UpdateTable . " ($names) VALUES ($values)";
	}

	// Insert
	function Insert(&$rs) {
		global $conn;
		return $conn->Execute($this->InsertSQL($rs));
	}

	// UPDATE statement
	function UpdateSQL(&$rs, $where = "") {
		$sql = "UPDATE " . $this->UpdateTable . " SET ";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]))
				continue;
			$sql .= $this->fields[$name]->FldExpression . "=";
			$sql .= ew_QuotedValue($value, $this->fields[$name]->FldDataType) . ",";
		}
		while (substr($sql, -1) == ",")
			$sql = substr($sql, 0, -1);
		$filter = $this->CurrentFilter;
		ew_AddFilter($filter, $where);
		if ($filter <> "")	$sql .= " WHERE " . $filter;
		return $sql;
	}

	// Update
	function Update(&$rs, $where = "", $rsold = NULL) {
		global $conn;
		return $conn->Execute($this->UpdateSQL($rs, $where));
	}

	// DELETE statement
	function DeleteSQL(&$rs, $where = "") {
		$sql = "DELETE FROM " . $this->UpdateTable . " WHERE ";
		if ($rs) {
			if (array_key_exists('offer_id', $rs))
				ew_AddFilter($where, ew_QuotedName('offer_id') . '=' . ew_QuotedValue($rs['offer_id'], $this->offer_id->FldDataType));
		}
		$filter = $this->CurrentFilter;
		ew_AddFilter($filter, $where);
		if ($filter <> "")
			$sql .= $filter;
		else
			$sql .= "0=1"; // Avoid delete
		return $sql;
	}

	// Delete
	function Delete(&$rs, $where = "") {
		global $conn;
		return $conn->Execute($this->DeleteSQL($rs, $where));
	}

	// Key filter WHERE clause
	function SqlKeyFilter() {
		return "`offer_id` = @offer_id@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->offer_id->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@offer_id@", ew_AdjustSql($this->offer_id->CurrentValue), $sKeyFilter); // Replace key value
		return $sKeyFilter;
	}

	// Return page URL
	function getReturnUrl() {
		$name = EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL;

		// Get referer URL automatically
		if (ew_ServerVar("HTTP_REFERER") <> "" && ew_ReferPage() <> ew_CurrentPage() && ew_ReferPage() <> "login.php") // Referer not same page or login page
			$_SESSION[$name] = ew_ServerVar("HTTP_REFERER"); // Save to Session
		if (@$_SESSION[$name] <> "") {
			return $_SESSION[$name];
		} else {
			return "offerslist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "offerslist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("offersview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("offersview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl() {
		return "offersadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("offersedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("offersadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("offersdelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->offer_id->CurrentValue)) {
			$sUrl .= "offer_id=" . urlencode($this->offer_id->CurrentValue);
		} else {
			return "javascript:alert(ewLanguage.Phrase('InvalidRecord'));";
		}
		return $sUrl;
	}

	// Sort URL
	function SortUrl(&$fld) {
		if ($this->CurrentAction <> "" || $this->Export <> "" ||
			in_array($fld->FldType, array(128, 204, 205))) { // Unsortable data type
				return "";
		} elseif ($fld->Sortable) {
			$sUrlParm = $this->UrlParm("order=" . urlencode($fld->FldName) . "&amp;ordertype=" . $fld->ReverseSort());
			return ew_CurrentPage() . "?" . $sUrlParm;
		} else {
			return "";
		}
	}

	// Get record keys from $_POST/$_GET/$_SESSION
	function GetRecordKeys() {
		global $EW_COMPOSITE_KEY_SEPARATOR;
		$arKeys = array();
		$arKey = array();
		if (isset($_POST["key_m"])) {
			$arKeys = ew_StripSlashes($_POST["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET["key_m"])) {
			$arKeys = ew_StripSlashes($_GET["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET)) {
			$arKeys[] = @$_GET["offer_id"]; // offer_id

			//return $arKeys; // Do not return yet, so the values will also be checked by the following code
		}

		// Check keys
		$ar = array();
		foreach ($arKeys as $key) {
			if (!is_numeric($key))
				continue;
			$ar[] = $key;
		}
		return $ar;
	}

	// Get key filter
	function GetKeyFilter() {
		$arKeys = $this->GetRecordKeys();
		$sKeyFilter = "";
		foreach ($arKeys as $key) {
			if ($sKeyFilter <> "") $sKeyFilter .= " OR ";
			$this->offer_id->CurrentValue = $key;
			$sKeyFilter .= "(" . $this->KeyFilter() . ")";
		}
		return $sKeyFilter;
	}

	// Load rows based on filter
	function &LoadRs($sFilter) {
		global $conn;

		// Set up filter (SQL WHERE clause) and get return SQL
		//$this->CurrentFilter = $sFilter;
		//$sSql = $this->SQL();

		$sSql = $this->GetSQL($sFilter, "");
		$rs = $conn->Execute($sSql);
		return $rs;
	}

	// Load row values from recordset
	function LoadListRowValues(&$rs) {
		$this->offer_id->setDbValue($rs->fields('offer_id'));
		$this->offer_title->setDbValue($rs->fields('offer_title'));
		$this->offer_description->setDbValue($rs->fields('offer_description'));
		$this->offer_content->setDbValue($rs->fields('offer_content'));
		$this->offer_question_content->setDbValue($rs->fields('offer_question_content'));
		$this->offer_image_path->Upload->DbValue = $rs->fields('offer_image_path');
		$this->offer_top_image->Upload->DbValue = $rs->fields('offer_top_image');
		$this->offer_bottom_image->Upload->DbValue = $rs->fields('offer_bottom_image');
		$this->offer_start_date->setDbValue($rs->fields('offer_start_date'));
		$this->offer_end_date->setDbValue($rs->fields('offer_end_date'));
		$this->offer_start_time->setDbValue($rs->fields('offer_start_time'));
		$this->offer_end_time->setDbValue($rs->fields('offer_end_time'));
		$this->offer_rules->setDbValue($rs->fields('offer_rules'));
		$this->offer_value->setDbValue($rs->fields('offer_value'));
		$this->offer_cat_id->setDbValue($rs->fields('offer_cat_id'));
		$this->offer_status->setDbValue($rs->fields('offer_status'));
	}

	// Render list row values
	function RenderListRow() {
		global $conn, $Security, $gsLanguage;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
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
		// offer_id

		$this->offer_id->ViewValue = $this->offer_id->CurrentValue;
		$this->offer_id->ViewCustomAttributes = "";

		// offer_title
		$this->offer_title->ViewValue = $this->offer_title->CurrentValue;
		$this->offer_title->ViewCustomAttributes = "";

		// offer_description
		$this->offer_description->ViewValue = $this->offer_description->CurrentValue;
		$this->offer_description->ViewCustomAttributes = "";

		// offer_content
		$this->offer_content->ViewValue = $this->offer_content->CurrentValue;
		$this->offer_content->ViewCustomAttributes = "";

		// offer_question_content
		$this->offer_question_content->ViewValue = $this->offer_question_content->CurrentValue;
		$this->offer_question_content->ViewCustomAttributes = "";

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

		// offer_rules
		$this->offer_rules->ViewValue = $this->offer_rules->CurrentValue;
		$this->offer_rules->ViewCustomAttributes = "";

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

		// offer_content
		$this->offer_content->LinkCustomAttributes = "";
		$this->offer_content->HrefValue = "";
		$this->offer_content->TooltipValue = "";

		// offer_question_content
		$this->offer_question_content->LinkCustomAttributes = "";
		$this->offer_question_content->HrefValue = "";
		$this->offer_question_content->TooltipValue = "";

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

		// offer_rules
		$this->offer_rules->LinkCustomAttributes = "";
		$this->offer_rules->HrefValue = "";
		$this->offer_rules->TooltipValue = "";

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

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Aggregate list row values
	function AggregateListRowValues() {
	}

	// Aggregate list row (for rendering)
	function AggregateListRow() {
	}

	// Export data in HTML/CSV/Word/Excel/Email/PDF format
	function ExportDocument(&$Doc, &$Recordset, $StartRec, $StopRec, $ExportPageType = "") {
		if (!$Recordset || !$Doc)
			return;

		// Write header
		$Doc->ExportTableHeader();
		if ($Doc->Horizontal) { // Horizontal format, write header
			$Doc->BeginExportRow();
			if ($ExportPageType == "view") {
				if ($this->offer_id->Exportable) $Doc->ExportCaption($this->offer_id);
				if ($this->offer_title->Exportable) $Doc->ExportCaption($this->offer_title);
				if ($this->offer_description->Exportable) $Doc->ExportCaption($this->offer_description);
				if ($this->offer_content->Exportable) $Doc->ExportCaption($this->offer_content);
				if ($this->offer_question_content->Exportable) $Doc->ExportCaption($this->offer_question_content);
				if ($this->offer_image_path->Exportable) $Doc->ExportCaption($this->offer_image_path);
				if ($this->offer_top_image->Exportable) $Doc->ExportCaption($this->offer_top_image);
				if ($this->offer_bottom_image->Exportable) $Doc->ExportCaption($this->offer_bottom_image);
				if ($this->offer_start_date->Exportable) $Doc->ExportCaption($this->offer_start_date);
				if ($this->offer_end_date->Exportable) $Doc->ExportCaption($this->offer_end_date);
				if ($this->offer_start_time->Exportable) $Doc->ExportCaption($this->offer_start_time);
				if ($this->offer_end_time->Exportable) $Doc->ExportCaption($this->offer_end_time);
				if ($this->offer_rules->Exportable) $Doc->ExportCaption($this->offer_rules);
				if ($this->offer_value->Exportable) $Doc->ExportCaption($this->offer_value);
				if ($this->offer_cat_id->Exportable) $Doc->ExportCaption($this->offer_cat_id);
				if ($this->offer_status->Exportable) $Doc->ExportCaption($this->offer_status);
			} else {
				if ($this->offer_id->Exportable) $Doc->ExportCaption($this->offer_id);
				if ($this->offer_title->Exportable) $Doc->ExportCaption($this->offer_title);
				if ($this->offer_description->Exportable) $Doc->ExportCaption($this->offer_description);
				if ($this->offer_image_path->Exportable) $Doc->ExportCaption($this->offer_image_path);
				if ($this->offer_top_image->Exportable) $Doc->ExportCaption($this->offer_top_image);
				if ($this->offer_bottom_image->Exportable) $Doc->ExportCaption($this->offer_bottom_image);
				if ($this->offer_start_date->Exportable) $Doc->ExportCaption($this->offer_start_date);
				if ($this->offer_end_date->Exportable) $Doc->ExportCaption($this->offer_end_date);
				if ($this->offer_start_time->Exportable) $Doc->ExportCaption($this->offer_start_time);
				if ($this->offer_end_time->Exportable) $Doc->ExportCaption($this->offer_end_time);
				if ($this->offer_value->Exportable) $Doc->ExportCaption($this->offer_value);
				if ($this->offer_cat_id->Exportable) $Doc->ExportCaption($this->offer_cat_id);
				if ($this->offer_status->Exportable) $Doc->ExportCaption($this->offer_status);
			}
			$Doc->EndExportRow();
		}

		// Move to first record
		$RecCnt = $StartRec - 1;
		if (!$Recordset->EOF) {
			$Recordset->MoveFirst();
			if ($StartRec > 1)
				$Recordset->Move($StartRec - 1);
		}
		while (!$Recordset->EOF && $RecCnt < $StopRec) {
			$RecCnt++;
			if (intval($RecCnt) >= intval($StartRec)) {
				$RowCnt = intval($RecCnt) - intval($StartRec) + 1;

				// Page break
				if ($this->ExportPageBreakCount > 0) {
					if ($RowCnt > 1 && ($RowCnt - 1) % $this->ExportPageBreakCount == 0)
						$Doc->ExportPageBreak();
				}
				$this->LoadListRowValues($Recordset);

				// Render row
				$this->RowType = EW_ROWTYPE_VIEW; // Render view
				$this->ResetAttrs();
				$this->RenderListRow();
				$Doc->BeginExportRow($RowCnt); // Allow CSS styles if enabled
				if ($ExportPageType == "view") {
					if ($this->offer_id->Exportable) $Doc->ExportField($this->offer_id);
					if ($this->offer_title->Exportable) $Doc->ExportField($this->offer_title);
					if ($this->offer_description->Exportable) $Doc->ExportField($this->offer_description);
					if ($this->offer_content->Exportable) $Doc->ExportField($this->offer_content);
					if ($this->offer_question_content->Exportable) $Doc->ExportField($this->offer_question_content);
					if ($this->offer_image_path->Exportable) $Doc->ExportField($this->offer_image_path);
					if ($this->offer_top_image->Exportable) $Doc->ExportField($this->offer_top_image);
					if ($this->offer_bottom_image->Exportable) $Doc->ExportField($this->offer_bottom_image);
					if ($this->offer_start_date->Exportable) $Doc->ExportField($this->offer_start_date);
					if ($this->offer_end_date->Exportable) $Doc->ExportField($this->offer_end_date);
					if ($this->offer_start_time->Exportable) $Doc->ExportField($this->offer_start_time);
					if ($this->offer_end_time->Exportable) $Doc->ExportField($this->offer_end_time);
					if ($this->offer_rules->Exportable) $Doc->ExportField($this->offer_rules);
					if ($this->offer_value->Exportable) $Doc->ExportField($this->offer_value);
					if ($this->offer_cat_id->Exportable) $Doc->ExportField($this->offer_cat_id);
					if ($this->offer_status->Exportable) $Doc->ExportField($this->offer_status);
				} else {
					if ($this->offer_id->Exportable) $Doc->ExportField($this->offer_id);
					if ($this->offer_title->Exportable) $Doc->ExportField($this->offer_title);
					if ($this->offer_description->Exportable) $Doc->ExportField($this->offer_description);
					if ($this->offer_image_path->Exportable) $Doc->ExportField($this->offer_image_path);
					if ($this->offer_top_image->Exportable) $Doc->ExportField($this->offer_top_image);
					if ($this->offer_bottom_image->Exportable) $Doc->ExportField($this->offer_bottom_image);
					if ($this->offer_start_date->Exportable) $Doc->ExportField($this->offer_start_date);
					if ($this->offer_end_date->Exportable) $Doc->ExportField($this->offer_end_date);
					if ($this->offer_start_time->Exportable) $Doc->ExportField($this->offer_start_time);
					if ($this->offer_end_time->Exportable) $Doc->ExportField($this->offer_end_time);
					if ($this->offer_value->Exportable) $Doc->ExportField($this->offer_value);
					if ($this->offer_cat_id->Exportable) $Doc->ExportField($this->offer_cat_id);
					if ($this->offer_status->Exportable) $Doc->ExportField($this->offer_status);
				}
				$Doc->EndExportRow();
			}
			$Recordset->MoveNext();
		}
		$Doc->ExportTableFooter();
	}

	// Table level events
	// Recordset Selecting event
	function Recordset_Selecting(&$filter) {

		// Enter your code here	
	}

	// Recordset Selected event
	function Recordset_Selected(&$rs) {

		//echo "Recordset Selected";
	}

	// Recordset Search Validated event
	function Recordset_SearchValidated() {

		// Example:
		//$this->MyField1->AdvancedSearch->SearchValue = "your search criteria"; // Search value

	}

	// Recordset Searching event
	function Recordset_Searching(&$filter) {

		// Enter your code here	
	}

	// Row_Selecting event
	function Row_Selecting(&$filter) {

		// Enter your code here	
	}

	// Row Selected event
	function Row_Selected(&$rs) {

		//echo "Row Selected";
	}

	// Row Inserting event
	function Row_Inserting($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Inserted event
	function Row_Inserted($rsold, &$rsnew) {

		//echo "Row Inserted"
	}

	// Row Updating event
	function Row_Updating($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Updated event
	function Row_Updated($rsold, &$rsnew) {

		//echo "Row Updated";
	}

	// Row Update Conflict event
	function Row_UpdateConflict($rsold, &$rsnew) {

		// Enter your code here
		// To ignore conflict, set return value to FALSE

		return TRUE;
	}

	// Row Deleting event
	function Row_Deleting(&$rs) {

		// Enter your code here
		// To cancel, set return value to False

		return TRUE;
	}

	// Row Deleted event
	function Row_Deleted(&$rs) {

		//echo "Row Deleted";
	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Lookup Selecting event
	function Lookup_Selecting($fld, &$filter) {

		// Enter your code here
	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here	
	}

	// Row Rendered event
	function Row_Rendered() {

		// To view properties of field class, use:
		//var_dump($this-><FieldName>); 

	}

	// User ID Filtering event
	function UserID_Filtering(&$filter) {

		// Enter your code here
	}
}
?>
