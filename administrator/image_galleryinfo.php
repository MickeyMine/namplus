<?php

// Global variable for table object
$image_gallery = NULL;

//
// Table class for image_gallery
//
class cimage_gallery extends cTable {
	var $img_id;
	var $img_path;
	var $img_description;
	var $img_cat_id;
	var $img_new_id;
	var $img_offer_id;
	var $img_nam_archive;
	var $img_is_banner;
	var $img_order;
	var $img_status;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'image_gallery';
		$this->TableName = 'image_gallery';
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

		// img_id
		$this->img_id = new cField('image_gallery', 'image_gallery', 'x_img_id', 'img_id', '`img_id`', '`img_id`', 3, -1, FALSE, '`img_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->img_id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['img_id'] = &$this->img_id;

		// img_path
		$this->img_path = new cField('image_gallery', 'image_gallery', 'x_img_path', 'img_path', '`img_path`', '`img_path`', 200, -1, TRUE, '`img_path`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->img_path->UploadMultiple = TRUE;
		$this->fields['img_path'] = &$this->img_path;

		// img_description
		$this->img_description = new cField('image_gallery', 'image_gallery', 'x_img_description', 'img_description', '`img_description`', '`img_description`', 200, -1, FALSE, '`img_description`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['img_description'] = &$this->img_description;

		// img_cat_id
		$this->img_cat_id = new cField('image_gallery', 'image_gallery', 'x_img_cat_id', 'img_cat_id', '`img_cat_id`', '`img_cat_id`', 3, -1, FALSE, '`img_cat_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->img_cat_id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['img_cat_id'] = &$this->img_cat_id;

		// img_new_id
		$this->img_new_id = new cField('image_gallery', 'image_gallery', 'x_img_new_id', 'img_new_id', '`img_new_id`', '`img_new_id`', 3, -1, FALSE, '`img_new_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->img_new_id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['img_new_id'] = &$this->img_new_id;

		// img_offer_id
		$this->img_offer_id = new cField('image_gallery', 'image_gallery', 'x_img_offer_id', 'img_offer_id', '`img_offer_id`', '`img_offer_id`', 3, -1, FALSE, '`img_offer_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->img_offer_id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['img_offer_id'] = &$this->img_offer_id;

		// img_nam_archive
		$this->img_nam_archive = new cField('image_gallery', 'image_gallery', 'x_img_nam_archive', 'img_nam_archive', '`img_nam_archive`', '`img_nam_archive`', 16, -1, FALSE, '`img_nam_archive`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['img_nam_archive'] = &$this->img_nam_archive;

		// img_is_banner
		$this->img_is_banner = new cField('image_gallery', 'image_gallery', 'x_img_is_banner', 'img_is_banner', '`img_is_banner`', '`img_is_banner`', 16, -1, FALSE, '`img_is_banner`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->img_is_banner->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['img_is_banner'] = &$this->img_is_banner;

		// img_order
		$this->img_order = new cField('image_gallery', 'image_gallery', 'x_img_order', 'img_order', '`img_order`', '`img_order`', 3, -1, FALSE, '`img_order`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->img_order->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['img_order'] = &$this->img_order;

		// img_status
		$this->img_status = new cField('image_gallery', 'image_gallery', 'x_img_status', 'img_status', '`img_status`', '`img_status`', 16, -1, FALSE, '`img_status`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->img_status->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['img_status'] = &$this->img_status;
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
		return "`image_gallery`";
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
		return "`img_id` DESC";
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
	var $UpdateTable = "`image_gallery`";

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
			if (array_key_exists('img_id', $rs))
				ew_AddFilter($where, ew_QuotedName('img_id') . '=' . ew_QuotedValue($rs['img_id'], $this->img_id->FldDataType));
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
		return "`img_id` = @img_id@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->img_id->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@img_id@", ew_AdjustSql($this->img_id->CurrentValue), $sKeyFilter); // Replace key value
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
			return "image_gallerylist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "image_gallerylist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("image_galleryview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("image_galleryview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl() {
		return "image_galleryadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("image_galleryedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("image_galleryadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("image_gallerydelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->img_id->CurrentValue)) {
			$sUrl .= "img_id=" . urlencode($this->img_id->CurrentValue);
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
			$arKeys[] = @$_GET["img_id"]; // img_id

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
			$this->img_id->CurrentValue = $key;
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
		$this->img_id->setDbValue($rs->fields('img_id'));
		$this->img_path->Upload->DbValue = $rs->fields('img_path');
		$this->img_description->setDbValue($rs->fields('img_description'));
		$this->img_cat_id->setDbValue($rs->fields('img_cat_id'));
		$this->img_new_id->setDbValue($rs->fields('img_new_id'));
		$this->img_offer_id->setDbValue($rs->fields('img_offer_id'));
		$this->img_nam_archive->setDbValue($rs->fields('img_nam_archive'));
		$this->img_is_banner->setDbValue($rs->fields('img_is_banner'));
		$this->img_order->setDbValue($rs->fields('img_order'));
		$this->img_status->setDbValue($rs->fields('img_status'));
	}

	// Render list row values
	function RenderListRow() {
		global $conn, $Security, $gsLanguage;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
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
		// img_id

		$this->img_id->ViewValue = $this->img_id->CurrentValue;
		$this->img_id->ViewCustomAttributes = "";

		// img_path
		if (!ew_Empty($this->img_path->Upload->DbValue)) {
			$this->img_path->ViewValue = $this->img_path->Upload->DbValue;
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

		// img_id
		$this->img_id->LinkCustomAttributes = "";
		$this->img_id->HrefValue = "";
		$this->img_id->TooltipValue = "";

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
				if ($this->img_id->Exportable) $Doc->ExportCaption($this->img_id);
				if ($this->img_path->Exportable) $Doc->ExportCaption($this->img_path);
				if ($this->img_description->Exportable) $Doc->ExportCaption($this->img_description);
				if ($this->img_cat_id->Exportable) $Doc->ExportCaption($this->img_cat_id);
				if ($this->img_new_id->Exportable) $Doc->ExportCaption($this->img_new_id);
				if ($this->img_offer_id->Exportable) $Doc->ExportCaption($this->img_offer_id);
				if ($this->img_nam_archive->Exportable) $Doc->ExportCaption($this->img_nam_archive);
				if ($this->img_is_banner->Exportable) $Doc->ExportCaption($this->img_is_banner);
				if ($this->img_order->Exportable) $Doc->ExportCaption($this->img_order);
				if ($this->img_status->Exportable) $Doc->ExportCaption($this->img_status);
			} else {
				if ($this->img_id->Exportable) $Doc->ExportCaption($this->img_id);
				if ($this->img_path->Exportable) $Doc->ExportCaption($this->img_path);
				if ($this->img_description->Exportable) $Doc->ExportCaption($this->img_description);
				if ($this->img_cat_id->Exportable) $Doc->ExportCaption($this->img_cat_id);
				if ($this->img_new_id->Exportable) $Doc->ExportCaption($this->img_new_id);
				if ($this->img_offer_id->Exportable) $Doc->ExportCaption($this->img_offer_id);
				if ($this->img_nam_archive->Exportable) $Doc->ExportCaption($this->img_nam_archive);
				if ($this->img_is_banner->Exportable) $Doc->ExportCaption($this->img_is_banner);
				if ($this->img_order->Exportable) $Doc->ExportCaption($this->img_order);
				if ($this->img_status->Exportable) $Doc->ExportCaption($this->img_status);
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
					if ($this->img_id->Exportable) $Doc->ExportField($this->img_id);
					if ($this->img_path->Exportable) $Doc->ExportField($this->img_path);
					if ($this->img_description->Exportable) $Doc->ExportField($this->img_description);
					if ($this->img_cat_id->Exportable) $Doc->ExportField($this->img_cat_id);
					if ($this->img_new_id->Exportable) $Doc->ExportField($this->img_new_id);
					if ($this->img_offer_id->Exportable) $Doc->ExportField($this->img_offer_id);
					if ($this->img_nam_archive->Exportable) $Doc->ExportField($this->img_nam_archive);
					if ($this->img_is_banner->Exportable) $Doc->ExportField($this->img_is_banner);
					if ($this->img_order->Exportable) $Doc->ExportField($this->img_order);
					if ($this->img_status->Exportable) $Doc->ExportField($this->img_status);
				} else {
					if ($this->img_id->Exportable) $Doc->ExportField($this->img_id);
					if ($this->img_path->Exportable) $Doc->ExportField($this->img_path);
					if ($this->img_description->Exportable) $Doc->ExportField($this->img_description);
					if ($this->img_cat_id->Exportable) $Doc->ExportField($this->img_cat_id);
					if ($this->img_new_id->Exportable) $Doc->ExportField($this->img_new_id);
					if ($this->img_offer_id->Exportable) $Doc->ExportField($this->img_offer_id);
					if ($this->img_nam_archive->Exportable) $Doc->ExportField($this->img_nam_archive);
					if ($this->img_is_banner->Exportable) $Doc->ExportField($this->img_is_banner);
					if ($this->img_order->Exportable) $Doc->ExportField($this->img_order);
					if ($this->img_status->Exportable) $Doc->ExportField($this->img_status);
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
