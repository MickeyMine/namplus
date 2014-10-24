<?php
class mod_users
{
	public $clsDb;
	function __construct()
	{
		$this->clsDb = new clsDB();
	}
	function closeConnect()
	{
		$this->clsDb->close();
	}
	
	public function GetAllUser()
	{
		$sql = "select * from users where user_status=1";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	public function GetUserByUserName($user)
	{
		$sql = "select * from users where user_name='" . $user . "' user_status=1";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	public function CheckUser($user, $pass)
	{
		$sql = "select * from `users` where user_status=1 and user_name='" . $user . "' and user_pass='" . $pass ."'";
		return $this->clsDb->fetchAllArray($sql);
	}
	
	public function InsertUser($user, $pass, $privilegeId, $status)
	{
		$sql = "INSERT INTO `users`(`user_id`, `user_name`, `user_pass`, `privilege_id`, `user_status`) VALUES (NULL,'". $user ."','". $pass ."', " . $privilegeId . ", " . $status . ")";
		$this->clsDb->getdata($sql);
		if($this->clsDb->result)
		{
			return true;
		}
		return false;
	}
	
	public function DeleteUser($user)
	{
		$sql = "DELETE FROM `users` WHERE user_name='$user'";
		$this->clsDb->getdata($sql);
		if($this->clsDb->result)
		{
			return true;
		}
		return false;
	}
	
	public function UpdateUser($user, $pass, $privilegeId, $status)
	{
		$sql = "UPDATE `users` SET ";
	
		if($pass != '')
		{
			$sql .= "user_pass = '" . $pass . "', ";
		}
		if($privilegeId != '')
		{
			$sql .= "privilege_id=" . $privilegeId . ", ";
		}
		if($status != '')
		{
			$sql .= "user_status=" . $status . ", ";
		}
	
		$sql = substr($sql, 0, -2);
	
		$sql .= ' WHERE user_name="' . $user . '"';
	
		$this->clsDb->getdata($sql);
		if($this->clsDb->result)
		{
			return true;
		}
		return false;
	}
}
?>