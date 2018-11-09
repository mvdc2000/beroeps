<?php 

//this is used as an API for the automatic saving of test questions.

require('databaseController.php');

//checks if request is POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die("Page not found.");
}

if(!isset($_POST['action']))
	die("Page not found.");

//checks if the action parameter contains a value
if($_POST['action'] == "save")
{
	if(!isset($_POST['user_id']) || !isset($_POST['question_id']) || !isset($_POST['answer']) || !isset($_POST['test_id']))
		die("Page not found.");

	if(empty($_POST['user_id']) || empty($_POST['question_id']) || empty($_POST['answer']) || empty($_POST['test_id']))
		die("Page not found.");

	$user_id = $_POST['user_id'];
	$question_id = $_POST['question_id'];
	$answer = $_POST['answer'];
	$test_id = $_POST['test_id'];

	//checks if the question id has already been saved or not.

	$query = $db->prepare("SELECT `id` FROM `progress` WHERE `user_id` = :id AND `question_id` = :question_id AND `test_id` = :test_id");
	$query->bindParam(":id", $user_id);
	$query->bindParam(":question_id", $question_id);
	$query->bindParam(":test_id", $test_id);

	if(!$query->execute())
		die("Page not found.");

	if($query->rowCount()) //found, update
	{
		$result = $query->fetch(PDO::FETCH_ASSOC);

		$query = $db->prepare("UPDATE `progress` SET `answer` = :answer WHERE `id` = :id");
		$query->bindParam(":answer", $answer);
		$query->bindParam(":id", $result['id']);

		if(!$query->execute())
			die("Page not found.");
	}
	else //not found, create
	{
		$query = $db->prepare("INSERT into `progress` (`user_id`, `answer`, `test_id`, `question_id`) VALUES (:id, :answer, :test_id, :question_id)");
		$query->bindParam(":id", $user_id);
		$query->bindParam(":answer", $answer);
		$query->bindParam(":question_id", $question_id);
		$query->bindParam(":test_id", $test_id);

		if(!$query->execute())
			die("Page not found.");
	}

}
else 
{
	die("Page not found.");
}