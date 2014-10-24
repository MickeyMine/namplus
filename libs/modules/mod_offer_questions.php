<?php
class mod_offer_questions
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
	
	function GetAllQuestions()
	{
		$sql = "SELECT * FROM `offer_questions` WHERE `question_status` = 1";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetQuestionByOfferId($offerId)
	{
		$sql = "SELECT * FROM `offer_questions` WHERE `question_status` = 1 and `offer_id`=" . $offerId;
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	public  function GetDataTable($where, $sort)
	{
		$sql = 	"SELECT * FROM `offer_questions`";
	
		if(isset($where))
		{
			$sql .= " WHERE " . $where;
		}
		if(isset($sort))
		{
			$sql .= " Order by " . $sort;
		}
	
		return $this->clsDb->fetchAllArray($sql);
	}
}
?>