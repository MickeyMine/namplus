<?php
class mod_offer_answers
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
	
	function GetAllAnswers()
	{
		$sql = "SELECT * FROM `offer_answers` WHERE `answer_status` = 1";
	
		return $this->clsDb->fetchAllArray($sql);
	}
	
	function GetAnswerByQuestionID($questionId)
	{
		$sql = "SELECT * FROM `offer_answers` WHERE `answer_status` = 1 and `question_id` = " . $questionId;
	
		return $this->clsDb->fetchAllArray($sql);
	}
}
?>