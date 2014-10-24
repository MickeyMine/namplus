<?php

// Global variable for table object
$news = NULL;

//
// Table class for news
//
class cnews extends cTable {
	var $new_id;
	var $new_title;
	var $new_description;
	var $new_content;
	var $new_type;
	var $new_img_path;
	var $new_publish_date;
	var $new_cat_id;
	var $new_link_id;
	var $new_link_order;
	var $new_status;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'news';
		$this->TableName = 'news';
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

		// new_id
		$this->new_id = new cField('news', 'news', 'x_new_id', 'new_id', '`new_id`', '`new_id`', 3, -1, FALSE, '`new_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->new_id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['new_id'] = &$this->new_id;

		// new_title
		$this->new_title = new cField('news', 'news', 'x_new_title', 'new_title', '`new_title`', '`new_title`', 200, -1, FALSE, '`new_title`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['new_title'] = &$this->new_title;

		// new_description
		$this->new_description = new cField('news', 'news', 'x_new_description', 'new_description', '`new_description`', '`new_description`', 200, -1, FALSE, '`new_description`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['new_description'] = &$this->new_description;

		// new_content
		$this->new_content = new cField('news', 'news', 'x_new_content', 'new_content', '`new_content`', '`new_content`', 201, -1, FALSE, '`new_content`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['new_content'] = &$this->new_content;

		// new_type
		$this->new_type = new cField('news', 'news', 'x_new_type', 'new_type', '`new_type`', '`new_type`', 16, -1, FALSE, '`new_type`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->new_type->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['new_type'] = &$this->new_type;

		// new_img_path
		$this->new_img_path = new cField('news', 'news', 'x_new_img_path', 'new_img_path', '`new_img_path`', '`new_img_path`', 200, -1, TRUE, '`new_img_path`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['new_img_path'] = &$this->new_img_path;

		// new_publish_date
		$this->new_publish_date = new cField('news', 'news', 'x_new_publish_date', 'new_publish_date', '`new_publish_date`', 'DATE_FORMAT(`new_publish_date`, \'%d/%m/%Y\')', 133, 7, FALSE, '`new_publish_date`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->new_publish_date->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateDMY"));
		$this->fields['new_publish_date'] = &$this->new_publish_date;

		// new_cat_id
		$this->new_cat_id = new cField('news', 'news', 'x_new_cat_id', 'new_cat_id', '`new_cat_id`', '`new_cat_id`', 3, -1, FALSE, '`new_cat_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->new_cat_id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['new_cat_id'] = &$this->new_cat_id;

		// new_link_id
		$this->new_link_id = new cField('news', 'news', 'x_new_link_id', 'new_link_id', '`new_link_id`', '`new_link_id`', 3, -1, FALSE, '`new_link_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->new_link_id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['new_link_id'] = &$this->new_link_id;

		// new_link_order
		$this->new_link_order = new cField('news', 'news', 'x_new_link_order', 'new_link_order', '`new_link_order`', '`new_link_order`', 3, -1, FALSE, '`new_link_order`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->new_link_order->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['new_link_order'] = &$this->new_link_order;

		// new_status
		$this->new_status = new cField('news', 'news', 'x_new_status', 'new_status', '`new_status`', '`new_status`', 3, -1, FALSE, '`new_status`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->new_status->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['new_status'] = &$this->new_status;
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
		return "`news`";
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
	var $UpdateTable = "`news`";

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
			if (array_key_exists('new_id', $rs))
				ew_AddFilter($where, ew_QuotedName('new_id') . '=' . ew_QuotedValue($rs['new_id'], $this->new_id->FldDataType));
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
		return "`new_id` = @new_id@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->new_id->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@new_id@", ew_AdjustSql($this->new_id->CurrentValue), $sKeyFilter); // Replace key value
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
			return "newslist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "newslist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("newsview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("newsview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl() {
		return "newsadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("newsedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("newsadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("newsdelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->new_id->CurrentValue)) {
			$sUrl .= "new_id=" . urlencode($this->new_id->CurrentValue);
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
			$arKeys[] = @$_GET["new_id"]; // new_id

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
			$this->new_id->CurrentValue = $key;
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
		$this->new_id->setDbValue($rs->fields('new_id'));
		$this->new_title->setDbValue($rs->fields('new_title'));
		$this->new_description->setDbValue($rs->fields('new_description'));
		$this->new_content->setDbValue($rs->fields('new_content'));
		$this->new_type->setDbValue($rs->fields('new_type'));
		$this->new_img_path->Upload->DbValue = $rs->fields('new_img_path');
		$this->new_publish_date->setDbValue($rs->fields('new_publish_date'));
		$this->new_cat_id->setDbValue($rs->fields('new_cat_id'));
		$this->new_link_id->setDbValue($rs->fields('new_link_id'));
		$this->new_link_order->setDbValue($rs->fields('new_link_order'));
		$this->new_status->setDbValue($rs->fields('new_status'));
	}

	// Render list row values
	function RenderListRow() {
		global $conn, $Security, $gsLanguage;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
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
				if ($this->new_id->Exportable) $Doc->ExportCaption($this->new_id);
				if ($this->new_title->Exportable) $Doc->ExportCaption($this->new_title);
				if ($this->new_description->Exportable) $Doc->ExportCaption($this->new_description);
				if ($this->new_content->Exportable) $Doc->ExportCaption($this->new_content);
				if ($this->new_type->Exportable) $Doc->ExportCaption($this->new_type);
				if ($this->new_img_path->Exportable) $Doc->ExportCaption($this->new_img_path);
				if ($this->new_publish_date->Exportable) $Doc->ExportCaption($this->new_publish_date);
				if ($this->new_cat_id->Exportable) $Doc->ExportCaption($this->new_cat_id);
				if ($this->new_link_id->Exportable) $Doc->ExportCaption($this->new_link_id);
				if ($this->new_link_order->Exportable) $Doc->ExportCaption($this->new_link_order);
				if ($this->new_status->Exportable) $Doc->ExportCaption($this->new_status);
			} else {
				if ($this->new_id->Exportable) $Doc->ExportCaption($this->new_id);
				if ($this->new_title->Exportable) $Doc->ExportCaption($this->new_title);
				if ($this->new_description->Exportable) $Doc->ExportCaption($this->new_description);
				if ($this->new_type->Exportable) $Doc->ExportCaption($this->new_type);
				if ($this->new_img_path->Exportable) $Doc->ExportCaption($this->new_img_path);
				if ($this->new_publish_date->Exportable) $Doc->ExportCaption($this->new_publish_date);
				if ($this->new_cat_id->Exportable) $Doc->ExportCaption($this->new_cat_id);
				if ($this->new_link_id->Exportable) $Doc->ExportCaption($this->new_link_id);
				if ($this->new_link_order->Exportable) $Doc->ExportCaption($this->new_link_order);
				if ($this->new_status->Exportable) $Doc->ExportCaption($this->new_status);
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
					if ($this->new_id->Exportable) $Doc->ExportField($this->new_id);
					if ($this->new_title->Exportable) $Doc->ExportField($this->new_title);
					if ($this->new_description->Exportable) $Doc->ExportField($this->new_description);
					if ($this->new_content->Exportable) $Doc->ExportField($this->new_content);
					if ($this->new_type->Exportable) $Doc->ExportField($this->new_type);
					if ($this->new_img_path->Exportable) $Doc->ExportField($this->new_img_path);
					if ($this->new_publish_date->Exportable) $Doc->ExportField($this->new_publish_date);
					if ($this->new_cat_id->Exportable) $Doc->ExportField($this->new_cat_id);
					if ($this->new_link_id->Exportable) $Doc->ExportField($this->new_link_id);
					if ($this->new_link_order->Exportable) $Doc->ExportField($this->new_link_order);
					if ($this->new_status->Exportable) $Doc->ExportField($this->new_status);
				} else {
					if ($this->new_id->Exportable) $Doc->ExportField($this->new_id);
					if ($this->new_title->Exportable) $Doc->ExportField($this->new_title);
					if ($this->new_description->Exportable) $Doc->ExportField($this->new_description);
					if ($this->new_type->Exportable) $Doc->ExportField($this->new_type);
					if ($this->new_img_path->Exportable) $Doc->ExportField($this->new_img_path);
					if ($this->new_publish_date->Exportable) $Doc->ExportField($this->new_publish_date);
					if ($this->new_cat_id->Exportable) $Doc->ExportField($this->new_cat_id);
					if ($this->new_link_id->Exportable) $Doc->ExportField($this->new_link_id);
					if ($this->new_link_order->Exportable) $Doc->ExportField($this->new_link_order);
					if ($this->new_status->Exportable) $Doc->ExportField($this->new_status);
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
