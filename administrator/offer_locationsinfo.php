<?php

// Global variable for table object
$offer_locations = NULL;

//
// Table class for offer_locations
//
class coffer_locations extends cTable {
	var $location_id;
	var $offer_id;
	var $location_name;
	var $location_address;
	var $location_map_x;
	var $location_map_y;
	var $location_status;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'offer_locations';
		$this->TableName = 'offer_locations';
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

		// location_id
		$this->location_id = new cField('offer_locations', 'offer_locations', 'x_location_id', 'location_id', '`location_id`', '`location_id`', 3, -1, FALSE, '`location_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->location_id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['location_id'] = &$this->location_id;

		// offer_id
		$this->offer_id = new cField('offer_locations', 'offer_locations', 'x_offer_id', 'offer_id', '`offer_id`', '`offer_id`', 3, -1, FALSE, '`offer_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->offer_id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['offer_id'] = &$this->offer_id;

		// location_name
		$this->location_name = new cField('offer_locations', 'offer_locations', 'x_location_name', 'location_name', '`location_name`', '`location_name`', 200, -1, FALSE, '`location_name`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['location_name'] = &$this->location_name;

		// location_address
		$this->location_address = new cField('offer_locations', 'offer_locations', 'x_location_address', 'location_address', '`location_address`', '`location_address`', 200, -1, FALSE, '`location_address`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['location_address'] = &$this->location_address;

		// location_map_x
		$this->location_map_x = new cField('offer_locations', 'offer_locations', 'x_location_map_x', 'location_map_x', '`location_map_x`', '`location_map_x`', 200, -1, FALSE, '`location_map_x`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['location_map_x'] = &$this->location_map_x;

		// location_map_y
		$this->location_map_y = new cField('offer_locations', 'offer_locations', 'x_location_map_y', 'location_map_y', '`location_map_y`', '`location_map_y`', 200, -1, FALSE, '`location_map_y`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['location_map_y'] = &$this->location_map_y;

		// location_status
		$this->location_status = new cField('offer_locations', 'offer_locations', 'x_location_status', 'location_status', '`location_status`', '`location_status`', 16, -1, FALSE, '`location_status`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->location_status->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['location_status'] = &$this->location_status;
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
		return "`offer_locations`";
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
	var $UpdateTable = "`offer_locations`";

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
			if (array_key_exists('location_id', $rs))
				ew_AddFilter($where, ew_QuotedName('location_id') . '=' . ew_QuotedValue($rs['location_id'], $this->location_id->FldDataType));
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
		return "`location_id` = @location_id@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->location_id->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@location_id@", ew_AdjustSql($this->location_id->CurrentValue), $sKeyFilter); // Replace key value
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
			return "offer_locationslist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "offer_locationslist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("offer_locationsview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("offer_locationsview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl() {
		return "offer_locationsadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("offer_locationsedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("offer_locationsadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("offer_locationsdelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->location_id->CurrentValue)) {
			$sUrl .= "location_id=" . urlencode($this->location_id->CurrentValue);
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
			$arKeys[] = @$_GET["location_id"]; // location_id

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
			$this->location_id->CurrentValue = $key;
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
		$this->location_id->setDbValue($rs->fields('location_id'));
		$this->offer_id->setDbValue($rs->fields('offer_id'));
		$this->location_name->setDbValue($rs->fields('location_name'));
		$this->location_address->setDbValue($rs->fields('location_address'));
		$this->location_map_x->setDbValue($rs->fields('location_map_x'));
		$this->location_map_y->setDbValue($rs->fields('location_map_y'));
		$this->location_status->setDbValue($rs->fields('location_status'));
	}

	// Render list row values
	function RenderListRow() {
		global $conn, $Security, $gsLanguage;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// location_id
		// offer_id
		// location_name
		// location_address
		// location_map_x
		// location_map_y
		// location_status
		// location_id

		$this->location_id->ViewValue = $this->location_id->CurrentValue;
		$this->location_id->ViewCustomAttributes = "";

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

		// location_name
		$this->location_name->ViewValue = $this->location_name->CurrentValue;
		$this->location_name->ViewCustomAttributes = "";

		// location_address
		$this->location_address->ViewValue = $this->location_address->CurrentValue;
		$this->location_address->ViewCustomAttributes = "";

		// location_map_x
		$this->location_map_x->ViewValue = $this->location_map_x->CurrentValue;
		$this->location_map_x->ViewCustomAttributes = "";

		// location_map_y
		$this->location_map_y->ViewValue = $this->location_map_y->CurrentValue;
		$this->location_map_y->ViewCustomAttributes = "";

		// location_status
		if (strval($this->location_status->CurrentValue) <> "") {
			switch ($this->location_status->CurrentValue) {
				case $this->location_status->FldTagValue(1):
					$this->location_status->ViewValue = $this->location_status->FldTagCaption(1) <> "" ? $this->location_status->FldTagCaption(1) : $this->location_status->CurrentValue;
					break;
				case $this->location_status->FldTagValue(2):
					$this->location_status->ViewValue = $this->location_status->FldTagCaption(2) <> "" ? $this->location_status->FldTagCaption(2) : $this->location_status->CurrentValue;
					break;
				default:
					$this->location_status->ViewValue = $this->location_status->CurrentValue;
			}
		} else {
			$this->location_status->ViewValue = NULL;
		}
		$this->location_status->ViewCustomAttributes = "";

		// location_id
		$this->location_id->LinkCustomAttributes = "";
		$this->location_id->HrefValue = "";
		$this->location_id->TooltipValue = "";

		// offer_id
		$this->offer_id->LinkCustomAttributes = "";
		$this->offer_id->HrefValue = "";
		$this->offer_id->TooltipValue = "";

		// location_name
		$this->location_name->LinkCustomAttributes = "";
		$this->location_name->HrefValue = "";
		$this->location_name->TooltipValue = "";

		// location_address
		$this->location_address->LinkCustomAttributes = "";
		$this->location_address->HrefValue = "";
		$this->location_address->TooltipValue = "";

		// location_map_x
		$this->location_map_x->LinkCustomAttributes = "";
		$this->location_map_x->HrefValue = "";
		$this->location_map_x->TooltipValue = "";

		// location_map_y
		$this->location_map_y->LinkCustomAttributes = "";
		$this->location_map_y->HrefValue = "";
		$this->location_map_y->TooltipValue = "";

		// location_status
		$this->location_status->LinkCustomAttributes = "";
		$this->location_status->HrefValue = "";
		$this->location_status->TooltipValue = "";

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
				if ($this->location_id->Exportable) $Doc->ExportCaption($this->location_id);
				if ($this->offer_id->Exportable) $Doc->ExportCaption($this->offer_id);
				if ($this->location_name->Exportable) $Doc->ExportCaption($this->location_name);
				if ($this->location_address->Exportable) $Doc->ExportCaption($this->location_address);
				if ($this->location_map_x->Exportable) $Doc->ExportCaption($this->location_map_x);
				if ($this->location_map_y->Exportable) $Doc->ExportCaption($this->location_map_y);
				if ($this->location_status->Exportable) $Doc->ExportCaption($this->location_status);
			} else {
				if ($this->location_id->Exportable) $Doc->ExportCaption($this->location_id);
				if ($this->offer_id->Exportable) $Doc->ExportCaption($this->offer_id);
				if ($this->location_name->Exportable) $Doc->ExportCaption($this->location_name);
				if ($this->location_address->Exportable) $Doc->ExportCaption($this->location_address);
				if ($this->location_map_x->Exportable) $Doc->ExportCaption($this->location_map_x);
				if ($this->location_map_y->Exportable) $Doc->ExportCaption($this->location_map_y);
				if ($this->location_status->Exportable) $Doc->ExportCaption($this->location_status);
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
					if ($this->location_id->Exportable) $Doc->ExportField($this->location_id);
					if ($this->offer_id->Exportable) $Doc->ExportField($this->offer_id);
					if ($this->location_name->Exportable) $Doc->ExportField($this->location_name);
					if ($this->location_address->Exportable) $Doc->ExportField($this->location_address);
					if ($this->location_map_x->Exportable) $Doc->ExportField($this->location_map_x);
					if ($this->location_map_y->Exportable) $Doc->ExportField($this->location_map_y);
					if ($this->location_status->Exportable) $Doc->ExportField($this->location_status);
				} else {
					if ($this->location_id->Exportable) $Doc->ExportField($this->location_id);
					if ($this->offer_id->Exportable) $Doc->ExportField($this->offer_id);
					if ($this->location_name->Exportable) $Doc->ExportField($this->location_name);
					if ($this->location_address->Exportable) $Doc->ExportField($this->location_address);
					if ($this->location_map_x->Exportable) $Doc->ExportField($this->location_map_x);
					if ($this->location_map_y->Exportable) $Doc->ExportField($this->location_map_y);
					if ($this->location_status->Exportable) $Doc->ExportField($this->location_status);
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
