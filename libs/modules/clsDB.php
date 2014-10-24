<?php
class clsDB
{
	public $result = NULL;
	
	public $hostname;	
	public $username;
	public $password;
	public $database;
	
	public $error = "";
	public $lastQuery = "";
	
	public $link;
		
	function __construct($conn = 1)
	{
		global $hostname, $username, $password, $database;
				
		if($conn == 1)
		{
			$this->hostname = $hostname;
			$this->username = $username;
			$this->password = $password;
			$this->database = $database;
			
			$this->connect();
		}
	}
	
	function __destruct()
	{
		$this->close();
	}
	
	public function connect()
	{
		$this->link = mysqli_connect($this->hostname, $this->username, $this->password, $this->database) 
			or die("Can not connect server ! Error : " . mysqli_connect_error()); 
		
		$this->link->set_charset("utf8");
	}
	
	public function close()
	{
		@mysqli_close($this->link);
	}
	
	function getdata($sql)
	{
		$this->result = $this->link->query($sql);
		
		if($this->result)
		{
			$this->lastQuery = $sql;
			return $this->result;
		}
		else
		{
			$this->error = "Could not execute query ! " . mysqli_error($this->link);
			return false;
		}
	}
	
	function fetchRow(){
		$row = mysqli_fetch_assoc($this->result);
		return  $row;
	}
	
	function fetchAllArray($sql)
	{
		$data = array();
		$this->getdata($sql);
		while($row = $this->fetchRow())
		{
			$data[] = $row;
		}
		
		return $data;
	}
	
	public function lastInserID()
	{		
		return mysqli_insert_id();
	}
	
}
?>