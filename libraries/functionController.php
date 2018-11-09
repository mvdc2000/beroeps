<?php

//this function generates a random number between the two parameters.
function generateRandomNumber($start, $end)
{
	return mt_rand($start, $end);
}

//this function generates a random string with the given length.
function generateRandomString($length)
{
	$string = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";

	$password = NULL;

	for($i = 0; $i < $length; $i++)
	{
		$rand = mt_rand(0, strlen($string) - 1);
		$password = $password.$string[$rand];
	}

	return $password;

}

//this function returns true if the user has completed the specified test.
function has_user_completed_test($user_id, $test_id)
{
	global $db;

	$query = $db->prepare("SELECT id FROM results WHERE user_id = :user AND test_id = :test");
	$query->bindParam(":user", $user_id);
	$query->bindParam(":test", $test_id);

	if($query->execute())
	{
		if($query->rowCount())
			return true;
	}

	return false;
}

function get_name($user_id)
{
	global $db;

	$name = "None";

	$query = $db->prepare("SELECT username FROM users WHERE id = :id");
	$query->bindParam(":id", $user_id);
	
	if($query->execute())
	{
		$row = $query->fetch(PDO::FETCH_ASSOC);
		$name = $row['username'];
	}

	return $name;
}

class userController
{

	//this function redirects the user with a message using the headers & sessions.
	function redirect($message, $type = "success", $page = "/")
	{
		$_SESSION[$type] = $message;
		header("Location: ".$page);
		die();
	}

	//this function checks if a user is logged in or not.
	function redirect_not_logged()
	{
		if(!isset($_SESSION['username']) || !isset($_SESSION['password']))
		{
			$this->redirect("You must be logged in to view this page.", "error", "login.php");
		}
	}

	//this function grabs the result id from the database. (later used to display the results page)
	function get_result_id($test_id)
	{
		global $db;

		//grab id

		$query = $db->prepare("SELECT id FROM results WHERE user_id = :id AND test_id = :test");
		$query->bindParam(":id", $this->table['id']);
		$query->bindParam(":test", $test_id);

		if(!$query->execute())
			$this->redirect("An error has occured while trying to fetch the results.", "error", "index.php");

		if(!$query->rowCount())
			$this->redirect("No results have been found for that test.", "error", "index.php");

		$row = $query->fetch(PDO::FETCH_ASSOC);

		return $row['id'];

	}

	//this function returns the percentage you made for a test.
	function get_test_score($test_id)
	{
		global $db;

		$percent = 100;

		if(!$this->has_completed_test($test_id))
		{
			$query_2 = $db->prepare("SELECT * FROM progress WHERE user_id = :user AND test_id = :test");
			$query_2->bindParam(":user", $this->table['id']);
			$query_2->bindParam(":test", $test_id);
			$query_2->execute();

			$result = $query_2->rowCount();

			$query_2 = $db->prepare("SELECT * FROM questions WHERE for_test = :test");
			$query_2->bindParam(":test", $test_id);
			$query_2->execute();

			$questions = $query_2->rowCount();

			$percent = 100 / $questions * $result;
		}

		return $percent;
	}

	//this function redirects you if you're not an admin.
	function redirect_not_admin()
	{
		$this->redirect_not_logged();

		if(!$this->admin())
		{
			$this->redirect("You don't have sufficient permission to view this page.", "error", "index.php");
		}
	}

	//this function checks if you're logged in or not.
	function logged()
	{
		if(isset($_SESSION['username']) && isset($_SESSION['password']))
			return true;

		return false;
	}

	function get_grade_for_user($user_id, $test_id)
	{
		global $db;

		$this->redirect_not_logged();

		$grade = 10;


		//grab total questions
		$query = $db->prepare("SELECT id FROM questions WHERE for_test = :test");
		$query->bindParam(":test", $test_id);
		$query->execute();

		$questions = $query->rowCount();


		//grab result id.
		$query = $db->prepare("SELECT id FROM results WHERE test_id = :test AND user_id = :user");
		$query->bindParam(":test", $test_id);
		$query->bindParam(":user", $user_id);
		$query->execute();

		$result = $query->fetch(PDO::FETCH_ASSOC);


		//grab right answers.
		$query = $db->prepare("SELECT id FROM results_qa WHERE test_id = :test AND result_id = :result AND status = 1");
		$query->bindParam(":test", $test_id);
		$query->bindParam(":result", $result['id']);
		$query->execute();

		$answers = $query->rowCount();

		$grade = round(10 / $questions * $answers, 1);

		if(!$grade)
			$grade = 1;

		return $grade;
	}

	function get_grade($test_id)
	{
		global $db;

		$this->redirect_not_logged();

		$grade = 10;


		//grab total questions
		$query = $db->prepare("SELECT id FROM questions WHERE for_test = :test");
		$query->bindParam(":test", $test_id);
		$query->execute();

		$questions = $query->rowCount();


		//grab result id.
		$query = $db->prepare("SELECT id FROM results WHERE test_id = :test AND user_id = :user");
		$query->bindParam(":test", $test_id);
		$query->bindParam(":user", $this->table['id']);
		$query->execute();

		$result = $query->fetch(PDO::FETCH_ASSOC);


		//grab right answers.
		$query = $db->prepare("SELECT id FROM results_qa WHERE test_id = :test AND result_id = :result AND status = 1");
		$query->bindParam(":test", $test_id);
		$query->bindParam(":result", $result['id']);
		$query->execute();

		$answers = $query->rowCount();

		$grade = round(10 / $questions * $answers, 1);

		if(!$grade)
			$grade = 1;

		return $grade;
	}

	//this function checks if you completed a test or not.
	function has_completed_test($test_id)
	{
		global $db;

		$this->redirect_not_logged();

		$query = $db->prepare("SELECT id FROM results WHERE user_id = :user AND test_id = :test");
		$query->bindParam(":user", $this->table['id']);
		$query->bindParam(":test", $test_id);

		if($query->execute())
		{
			if($query->rowCount())
				return true;
		}

		return false;
	}

	//this function checks if you're an admin or not.
	function admin()
	{
		if(!isset($_SESSION['username']) || !isset($_SESSION['password']))
			return false;

		return $this->table['admin'];
	}

	public $table = NULL;
}