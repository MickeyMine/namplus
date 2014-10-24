<?php

// Global variable for table object
$customers = NULL;

//
// Table class for customers
//
class ccustomers extends cTable {
	var $customer_id;
	var $customer_code;
	var $customer_email;
	var $customer_pass;
	var $customer_first_name;
	var $customer_last_name;
	var $customer_profession;
	var $customer_phone;
	var $customer_address;
	var $subscription_id;
	var $customer_facebook;
	var $customer_author_uid;
	var $customer_provider;
	var $customer_payment_type;
	var $customer_status;
	var $customer_first_login;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'customers';
		$this->TableName = 'customers';
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

		// customer_id
		$this->customer_id = new cField('customers', 'customers', 'x_customer_id', 'customer_id', '`customer_id`', '`customer_id`', 3, -1, FALSE, '`customer_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->customer_id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['customer_id'] = &$this->customer_id;

		// customer_code
		$this->customer_code = new cField('customers', 'customers', 'x_customer_code', 'customer_code', '`customer_code`', '`customer_code`', 200, -1, FALSE, '`customer_code`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['customer_code'] = &$this->customer_code;

		// customer_email
		$this->customer_email = new cField('customers', 'customers', 'x_customer_email', 'customer_email', '`customer_email`', '`customer_email`', 200, -1, FALSE, '`customer_email`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['customer_email'] = &$this->customer_email;

		// customer_pass
		$this->customer_pass = new cField('customers', 'customers', 'x_customer_pass', 'customer_pass', '`customer_pass`', '`customer_pass`', 200, -1, FALSE, '`customer_pass`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['customer_pass'] = &$this->customer_pass;

		// customer_first_name
		$this->customer_first_name = new cField('customers', 'customers', 'x_customer_first_name', 'customer_first_name', '`customer_first_name`', '`customer_first_name`', 200, -1, FALSE, '`customer_first_name`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['customer_first_name'] = &$this->customer_first_name;

		// customer_last_name
		$this->customer_last_name = new cField('customers', 'customers', 'x_customer_last_name', 'customer_last_name', '`customer_last_name`', '`customer_last_name`', 200, -1, FALSE, '`customer_last_name`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['customer_last_name'] = &$this->customer_last_name;

		// customer_profession
		$this->customer_profession = new cField('customers', 'customers', 'x_customer_profession', 'customer_profession', '`customer_profession`', '`customer_profession`', 200, -1, FALSE, '`customer_profession`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['customer_profession'] = &$this->customer_profession;

		// customer_phone
		$this->customer_phone = new cField('customers', 'customers', 'x_customer_phone', 'customer_phone', '`customer_phone`', '`customer_phone`', 200, -1, FALSE, '`customer_phone`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['customer_phone'] = &$this->customer_phone;

		// customer_address
		$this->customer_address = new cField('customers', 'customers', 'x_customer_address', 'customer_address', '`customer_address`', '`customer_address`', 200, -1, FALSE, '`customer_address`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['customer_address'] = &$this->customer_address;

		// subscription_id
		$this->subscription_id = new cField('customers', 'customers', 'x_subscription_id', 'subscription_id', '`subscription_id`', '`subscription_id`', 3, -1, FALSE, '`subscription_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->subscription_id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['subscription_id'] = &$this->subscription_id;

		// customer_facebook
		$this->customer_facebook = new cField('customers', 'customers', 'x_customer_facebook', 'customer_facebook', '`customer_facebook`', '`customer_facebook`', 200, -1, FALSE, '`customer_facebook`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['customer_facebook'] = &$this->customer_facebook;

		// customer_author_uid
		$this->customer_author_uid = new cField('customers', 'customers', 'x_customer_author_uid', 'customer_author_uid', '`customer_author_uid`', '`customer_author_uid`', 200, -1, FALSE, '`customer_author_uid`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['customer_author_uid'] = &$this->customer_author_uid;

		// customer_provider
		$this->customer_provider = new cField('customers', 'customers', 'x_customer_provider', 'customer_provider', '`customer_provider`', '`customer_provider`', 200, -1, FALSE, '`customer_provider`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['customer_provider'] = &$this->customer_provider;

		// customer_payment_type
		$this->customer_payment_type = new cField('customers', 'customers', 'x_customer_payment_type', 'customer_payment_type', '`customer_payment_type`', '`customer_payment_type`', 3, -1, FALSE, '`customer_payment_type`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->customer_payment_type->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['customer_payment_type'] = &$this->customer_payment_type;

		// customer_status
		$this->customer_status = new cField('customers', 'customers', 'x_customer_status', 'customer_status', '`customer_status`', '`customer_status`', 16, -1, FALSE, '`customer_status`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->customer_status->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['customer_status'] = &$this->customer_status;

		// customer_first_login
		$this->customer_first_login = new cField('customers', 'customers', 'x_customer_first_login', 'customer_first_login', '`customer_first_login`', '`customer_first_login`', 16, -1, FALSE, '`customer_first_login`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->customer_first_login->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['customer_first_login'] = &$this->customer_first_login;
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
		return "`customers`";
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
	var $UpdateTable = "`customers`";

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
			if (array_key_exists('customer_id', $rs))
				ew_AddFilter($where, ew_QuotedName('customer_id') . '=' . ew_QuotedValue($rs['customer_id'], $this->customer_id->FldDataType));
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
		return "`customer_id` = @customer_id@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->customer_id->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@customer_id@", ew_AdjustSql($this->customer_id->CurrentValue), $sKeyFilter); // Replace key value
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
			return "customerslist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "customerslist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("customersview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("customersview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl() {
		return "customersadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("customersedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("customersadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("customersdelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->customer_id->CurrentValue)) {
			$sUrl .= "customer_id=" . urlencode($this->customer_id->CurrentValue);
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
			$arKeys[] = @$_GET["customer_id"]; // customer_id

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
			$this->customer_id->CurrentValue = $key;
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

	// Render list row values
	function RenderListRow() {
		global $conn, $Security, $gsLanguage;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
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
				if ($this->customer_id->Exportable) $Doc->ExportCaption($this->customer_id);
				if ($this->customer_code->Exportable) $Doc->ExportCaption($this->customer_code);
				if ($this->customer_email->Exportable) $Doc->ExportCaption($this->customer_email);
				if ($this->customer_pass->Exportable) $Doc->ExportCaption($this->customer_pass);
				if ($this->customer_first_name->Exportable) $Doc->ExportCaption($this->customer_first_name);
				if ($this->customer_last_name->Exportable) $Doc->ExportCaption($this->customer_last_name);
				if ($this->customer_profession->Exportable) $Doc->ExportCaption($this->customer_profession);
				if ($this->customer_phone->Exportable) $Doc->ExportCaption($this->customer_phone);
				if ($this->customer_address->Exportable) $Doc->ExportCaption($this->customer_address);
				if ($this->subscription_id->Exportable) $Doc->ExportCaption($this->subscription_id);
				if ($this->customer_facebook->Exportable) $Doc->ExportCaption($this->customer_facebook);
				if ($this->customer_author_uid->Exportable) $Doc->ExportCaption($this->customer_author_uid);
				if ($this->customer_provider->Exportable) $Doc->ExportCaption($this->customer_provider);
				if ($this->customer_payment_type->Exportable) $Doc->ExportCaption($this->customer_payment_type);
				if ($this->customer_status->Exportable) $Doc->ExportCaption($this->customer_status);
				if ($this->customer_first_login->Exportable) $Doc->ExportCaption($this->customer_first_login);
			} else {
				if ($this->customer_id->Exportable) $Doc->ExportCaption($this->customer_id);
				if ($this->customer_code->Exportable) $Doc->ExportCaption($this->customer_code);
				if ($this->customer_email->Exportable) $Doc->ExportCaption($this->customer_email);
				if ($this->customer_pass->Exportable) $Doc->ExportCaption($this->customer_pass);
				if ($this->customer_first_name->Exportable) $Doc->ExportCaption($this->customer_first_name);
				if ($this->customer_last_name->Exportable) $Doc->ExportCaption($this->customer_last_name);
				if ($this->customer_profession->Exportable) $Doc->ExportCaption($this->customer_profession);
				if ($this->customer_phone->Exportable) $Doc->ExportCaption($this->customer_phone);
				if ($this->customer_address->Exportable) $Doc->ExportCaption($this->customer_address);
				if ($this->subscription_id->Exportable) $Doc->ExportCaption($this->subscription_id);
				if ($this->customer_facebook->Exportable) $Doc->ExportCaption($this->customer_facebook);
				if ($this->customer_author_uid->Exportable) $Doc->ExportCaption($this->customer_author_uid);
				if ($this->customer_provider->Exportable) $Doc->ExportCaption($this->customer_provider);
				if ($this->customer_payment_type->Exportable) $Doc->ExportCaption($this->customer_payment_type);
				if ($this->customer_status->Exportable) $Doc->ExportCaption($this->customer_status);
				if ($this->customer_first_login->Exportable) $Doc->ExportCaption($this->customer_first_login);
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
					if ($this->customer_id->Exportable) $Doc->ExportField($this->customer_id);
					if ($this->customer_code->Exportable) $Doc->ExportField($this->customer_code);
					if ($this->customer_email->Exportable) $Doc->ExportField($this->customer_email);
					if ($this->customer_pass->Exportable) $Doc->ExportField($this->customer_pass);
					if ($this->customer_first_name->Exportable) $Doc->ExportField($this->customer_first_name);
					if ($this->customer_last_name->Exportable) $Doc->ExportField($this->customer_last_name);
					if ($this->customer_profession->Exportable) $Doc->ExportField($this->customer_profession);
					if ($this->customer_phone->Exportable) $Doc->ExportField($this->customer_phone);
					if ($this->customer_address->Exportable) $Doc->ExportField($this->customer_address);
					if ($this->subscription_id->Exportable) $Doc->ExportField($this->subscription_id);
					if ($this->customer_facebook->Exportable) $Doc->ExportField($this->customer_facebook);
					if ($this->customer_author_uid->Exportable) $Doc->ExportField($this->customer_author_uid);
					if ($this->customer_provider->Exportable) $Doc->ExportField($this->customer_provider);
					if ($this->customer_payment_type->Exportable) $Doc->ExportField($this->customer_payment_type);
					if ($this->customer_status->Exportable) $Doc->ExportField($this->customer_status);
					if ($this->customer_first_login->Exportable) $Doc->ExportField($this->customer_first_login);
				} else {
					if ($this->customer_id->Exportable) $Doc->ExportField($this->customer_id);
					if ($this->customer_code->Exportable) $Doc->ExportField($this->customer_code);
					if ($this->customer_email->Exportable) $Doc->ExportField($this->customer_email);
					if ($this->customer_pass->Exportable) $Doc->ExportField($this->customer_pass);
					if ($this->customer_first_name->Exportable) $Doc->ExportField($this->customer_first_name);
					if ($this->customer_last_name->Exportable) $Doc->ExportField($this->customer_last_name);
					if ($this->customer_profession->Exportable) $Doc->ExportField($this->customer_profession);
					if ($this->customer_phone->Exportable) $Doc->ExportField($this->customer_phone);
					if ($this->customer_address->Exportable) $Doc->ExportField($this->customer_address);
					if ($this->subscription_id->Exportable) $Doc->ExportField($this->subscription_id);
					if ($this->customer_facebook->Exportable) $Doc->ExportField($this->customer_facebook);
					if ($this->customer_author_uid->Exportable) $Doc->ExportField($this->customer_author_uid);
					if ($this->customer_provider->Exportable) $Doc->ExportField($this->customer_provider);
					if ($this->customer_payment_type->Exportable) $Doc->ExportField($this->customer_payment_type);
					if ($this->customer_status->Exportable) $Doc->ExportField($this->customer_status);
					if ($this->customer_first_login->Exportable) $Doc->ExportField($this->customer_first_login);
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
