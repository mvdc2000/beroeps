<?php 

require("libraries/headerContent.php");

$user->redirect_not_logged();


if(isset($_POST['add_test']))
{
	if(!isset($_POST['title']) || !isset($_POST['level']))
		$user->redirect("Not all parameters have been entered.", "error", "create.php");


	$count = 0;

	$questions = array();

	//check if every field has been filled in.
	foreach($_POST['question'] as $ques)
	{
		if(empty($ques))
			$user->redirect("You haven't filled in all the parameters.", "error", "create.php");

		array_push($questions, $ques);

		$count ++;
	}

	$count = $count / 6;

	//main test

	//insert the main test in the database.
	$query = $db->prepare("INSERT INTO tests (title, level) VALUES (:title, :level)");
	$query->bindParam(":title", $_POST['title']);
	$query->bindParam(":level", $_POST['level']);

	if(!$query->execute())
		$user->redirect("There has appeared an error while processing your request!", "error", "create.php");

	//save the test id for later.
	$test_id = $db->lastInsertId();

	//loop through the questions
	for($int = 0; $int < sizeof($questions); $int += 6)
	{
		//process queries.

		//check the type of question.
		if($questions[$int + 1] != "MPC" && $questions[$int + 1] != "OPEN")
		{
			//delete test
			
			$query_2 = $db->prepare("DELETE FROM tests WHERE id = :id");
			$query->bindParam(":id", $test_id);
			$query->execute();

			$user->redirect("The type parameter can only be MPC or OPEN.", "error", "create.php");
		}

		$question_type = -1;

		if($questions[$int + 1] == "MPC")
			$question_type = 0;
		else if($questions[$int + 1] == "OPEN")
			$question_type = 1;

		//question

		$query = $db->prepare("INSERT INTO questions (question, type, for_test, added_by) VALUES (:question, :type, :test, :added_by)");
		$query->bindParam(":question", $questions[$int]);
		$query->bindParam(":type", $question_type);
		$query->bindParam(":test", $test_id);
		$query->bindParam(":added_by", $user->table['id']);

		if(!$query->execute())
			$user->redirect("There has appeared an error while processing your request!", "error", "create.php");

		$question_id = $db->lastInsertId();

		//answers
		
		$answer_type = 1;

		$query = $db->prepare("INSERT INTO answers (answer, for_question, for_test, type) VALUES (:answer, :question, :test, :type)");
		$query->bindParam(":answer", $questions[$int + 2]);
		$query->bindParam(":question", $question_id);
		$query->bindParam(":test", $test_id);
		$query->bindParam(":type", $answer_type);

		if(!$query->execute())
			$user->redirect("There has appeared an error while processing your request! #1", "error", "create.php");

		$answer_type = 0;

		$query = $db->prepare("INSERT INTO answers (answer, for_question, for_test, type) VALUES (:answer, :question, :test, :type)");
		$query->bindParam(":answer", $questions[$int + 3]);
		$query->bindParam(":question", $question_id);
		$query->bindParam(":test", $test_id);
		$query->bindParam(":type", $answer_type);

		if(!$query->execute())
			$user->redirect("There has appeared an error while processing your request! #1", "error", "create.php");

		$query = $db->prepare("INSERT INTO answers (answer, for_question, for_test, type) VALUES (:answer, :question, :test, :type)");
		$query->bindParam(":answer", $questions[$int + 4]);
		$query->bindParam(":question", $question_id);
		$query->bindParam(":test", $test_id);
		$query->bindParam(":type", $answer_type);

		if(!$query->execute())
			$user->redirect("There has appeared an error while processing your request! #1", "error", "create.php");

		$query = $db->prepare("INSERT INTO answers (answer, for_question, for_test, type) VALUES (:answer, :question, :test, :type)");
		$query->bindParam(":answer", $questions[$int + 5]);
		$query->bindParam(":question", $question_id);
		$query->bindParam(":test", $test_id);
		$query->bindParam(":type", $answer_type);

		if(!$query->execute())
			$user->redirect("There has appeared an error while processing your request! #1", "error", "create.php");
	}

	$user->redirect("The test has been created!", "success", "create.php");
}


?>

<script>
	
	//function to add a new question field.

	var add = '<hr>' 
		+ '<input type="text" class="form-control" placeholder="Question" name="question[]"><br>'
		+ '<div class="push-left"><input type="text" class="form-control" placeholder="MPC / OPEN" name="question[]"></div><br>' 
		+ '<div class="push-left"><input type="text" class="form-control" placeholder="Right Answer" name="question[]"></div><br>' 
		+ '<div class="push-left"><input type="text" class="form-control" placeholder="Fake Answer" name="question[]"></div><br>' 
		+ '<div class="push-left"><input type="text" class="form-control" placeholder="Fake Answer" name="question[]"></div><br>' 
		+ '<div class="push-left"><input type="text" class="form-control" placeholder="Fake Answer" name="question[]"></div><br>';

	function add_question()
	{
		$('.questions').append(add);
	}

</script>

<h2>Tests | Create</h2>

<hr>

<div class="card">
	<div class="card-header">Create Test</div>
	<div class="card-body">
	
		<form method="post">
			<div class="form-group">
				<input type="text" class="form-control" placeholder="Title" name="title"><br>

				<select name="level" class="form-control">
					<option value="a1">A1</option>
					<option value="a2">A2</option>
					<option value="a3">A3</option>
					<option value="a4">A4</option>
					<option value="b1">B1</option>
					<option value="b2">B2</option>
					<option value="b3">B3</option>
					<option value="b4">B4</option>
					<option value="c1">C1</option>
					<option value="c2">C2</option>
					<option value="c3">C3</option>
					<option value="c4">C4</option>
				</select>

				<br>

				<div class="push-left">

					<div class="questions">
					
						<input type="text" class="form-control" placeholder="Question" name="question[]"><br>
						<div class="push-left"><input type="text" class="form-control" placeholder="MPC / OPEN" name="question[]"></div><br>
						<div class="push-left"><input type="text" class="form-control" placeholder="Right Answer" name="question[]"></div><br>
						<div class="push-left"><input type="text" class="form-control" placeholder="Fake Answer" name="question[]"></div><br>
						<div class="push-left"><input type="text" class="form-control" placeholder="Fake Answer" name="question[]"></div><br>
						<div class="push-left"><input type="text" class="form-control" placeholder="Fake Answer" name="question[]"></div><br>

					</div>

				</div>

				<i class="fas fa-plus-circle text-success" onclick="add_question();"></i> Add Question<br>
			</div>
			<input type="submit" class="btn btn-primary" name="add_test" value="Add Test">
		</form>

	</div>
</div>

<?php require("libraries/footerContent.php"); ?>