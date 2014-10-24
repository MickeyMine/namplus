<?php
	/**
	 * Joomag API PHP Wrapper
	 * This class provides simple interface to make Joomag API calls.
	 * 
	 * @version 1.0.2
	 * @author Joomag
	 */
	class JoomagApiClient {
		public $__version = "1.0.2";
		private $__key = "";
		private $__url = "http://www.joomag.com/Frontend/WebService/restAPI.php";
		
		public function __construct($apiKey) {
			$this->__key = $apiKey;
		}
		
		/**
		 * Creates magazine from PDF file
		 * 
		 * @param string $file path to PDF file
		 * @param string $magazineID ID of magazine to which the issue should be created
		 * @param string $volume Volumen for the new issue
		 * @param string $description Description for the new issue (optional)
		 * @param int $category Category ID for the new issue (optional, takes default from the magazine)
		 * @param string $keywords coma separated string of keywords (optional)
		 * @param boolean $allow_print flag to indicate allowing printing for the new issue (optional)
		 * @param boolean $allow_download flag to indicate allowing download of the new issue (optional)
		 * 
		 * @return JoomagApiResponse
		 */
		public function createFromPDF($file, $magazineID, $volume, $description = "", $category = "", $keywords = array(), $allow_print = "", $allow_download = "") {
			if (file_exists($file)) {
				$action = 'createMagazineFromPDF';
				$post = array(
					"key"			=> $this->__key,
					"action"		=> $action,
					"magazine_ID"	=> $magazineID,
					"volume"		=> $volume,
					"description"	=> $description,
					"keywords"		=> implode(',', $keywords),
					"category"		=> $category,
					"allow_print"	=> $allow_print,
					"allow_download"=> $allow_download,
					"pdf"=>"@$file",
				);
				return $this->sendRequest($post);
			}
		}
		
		/**
		 * Checks magazine status
		 * 
		 * @param string $tempID temporary magazine ID fetched from createFromPDF method
		 */
		public function checkPDFStatus($tempID) {
			if ($tempID != "") {
				$action = "checkPDFStatus";
				$post = array(
					"key"			=> $this->__key,
					"action"		=> $action,
					"tempID" 		=> $tempID
				);
				return $this->sendRequest($post);
			}
		}
		
		/**
		 * Lists issues in magazine
		 * 
		 * @param string $magazineID ID 
		 */
		public function listMagazines() {
			$action = "listMagazines";
			$post = array(
				"key"			=> $this->__key,
				"action"		=> $action
			);
			return $this->sendRequest($post);
		}
		
		/**
		 * Lists issues in magazine
		 * 
		 * @param string $magazineID ID 
		 */
		public function listIssues($magazineID, $include_tags = false) {
			$action = "listIssues";
			$post = array(
				"key"			=> $this->__key,
				"action"		=> $action,
				"magazine_ID" 	=> $magazineID
			);
			if ($include_tags) {
				$post['include_tags'] = 1;
			}
			return $this->sendRequest($post);
		}
		
		/**
		 * Deleted the magazine with given ID
		 * 
		 * @param string $issueID ID of issue to be deleted
		 */
		public function deleteIssue($issueID) {
			$action = "deleteIssue";
			$post = array(
				"key"			=> $this->__key,
				"action"		=> $action,
				"issue_ID" 		=> $issueID
			);
			return $this->sendRequest($post);
		}
		
		private function sendRequest($postVars) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_VERBOSE, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
			curl_setopt($ch, CURLOPT_URL, $this->__url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postVars);
			$response = curl_exec($ch);
			return new JoomagApiResponse($response);
		}
	}
	
	class JoomagApiResponse {
		private $__message = "";
		private $__success = false;
		private $__json = "";
		private $__resp = "";
		
		public function __construct($json) {
			$this->__json = $json;
			$obj = json_decode($json);
			if (isset($obj->error) && $obj->error == 0)
				$this->__success = true;
			if (isset($obj->msg))
				$this->__message = $obj->msg;
			if (isset($obj->response)) {
				$this->__resp = (array)$obj->response;
			}
		}
		
		public function message() {
			return $this->__message;
		}
		
		public function successful() {
			return $this->__success;
		}
		
		public function response() {
			return $this->__resp;
		}
	} 
?>