<?php 

require("libraries/headerContent.php");

$user->redirect_not_logged();

if(isset($_POST['add_question']))
{
	if(!isset($_POST['type']) || !isset($_POST['answer_1']) || !isset($_POST['answer_2']) || !isset($_POST['answer_3']) || !isset($_POST['answer_4']))
		$user->redirect("You haven't filled in all the parameters.", "error", "questions.php");

	if(empty($_POST['type']) || empty($_POST['answer_1']) || empty($_POST['answer_2']) || empty($_POST['answer_3']) || empty($_POST['answer_4']))
		$user->redirect("You haven't filled in all the parameters.", "error", "questions.php");

	//insert question

	$query = $db->prepare("INSERT INTO questions (question, type, added_by) VALUES (:question, :type, :added_by)");
	$query->bindParam(":question", $_POST['question']);
	$query->bindParam(":type", $_POST['type']);
	$query->bindParam(":added_by", $user->table['id']);

	if(!$query->execute())
		$user->redirect("There has appeared an error while processing your request!", "error", "questions.php");

	$question_id = $db->lastInsertId();

	//insert answers

	$answer_type = 1;

	$query = $db->prepare("INSERT INTO answers (answer, for_question, type) VALUES (:answer, :question, :type)");
	$query->bindParam(":answer", $_POST['answer_1']);
	$query->bindParam(":question", $question_id);
	$query->bindParam(":type", $answer_type);

	if(!$query->execute())
		$user->redirect("There has appeared an error while processing your request! #1", "error", "questions.php");

	$answer_type = 0;

	$query = $db->prepare("INSERT INTO answers (answer, for_question, type) VALUES (:answer, :question, :type)");
	$query->bindParam(":answer", $_POST['answer_2']);
	$query->bindParam(":question", $question_id);
	$query->bindParam(":type", $answer_type);

	if(!$query->execute())
		$user->redirect("There has appeared an error while processing your request! #2", "error", "questions.php");

	$query = $db->prepare("INSERT INTO answers (answer, for_question, type) VALUES (:answer, :question, :type)");
	$query->bindParam(":answer", $_POST['answer_3']);
	$query->bindParam(":question", $question_id);
	$query->bindParam(":type", $answer_type);

	if(!$query->execute())
		$user->redirect("There has appeared an error while processing your request! #3", "error", "questions.php");

	$query = $db->prepare("INSERT INTO answers (answer, for_question, type) VALUES (:answer, :question, :type)");
	$query->bindParam(":answer", $_POST['answer_4']);
	$query->bindParam(":question", $question_id);
	$query->bindParam(":type", $answer_type);

	if(!$query->execute())
		$user->redirect("There has appeared an error while processing your request! #4", "error", "questions.php");

	$user->redirect("Your question has been added to the database!", "success", "questions.php");
}

?>

<h2>Tests</h2>

<a href="create.php"><input type="submit" class="button-primary" value="Create Test"></a>

<div class="divider"></div>

<div class="box small">

	<div class="box-header">
		<b>ADD QUESTIONS</b>
	</div>

	<form method="POST">

		<input type="text" placeholder="Question Text" name="question"><br>
		
		<div class="divider"></div>

		<div class="radio">
			<input type="radio" id="mpc" name="type" value="0" checked>
			<label for="mpc">Multiple Choice</label>

			<br>

			<input type="radio" id="open" name="type" value="1">
			<label for="open">Open Questions</label>
		</div>

		<br>

		<div class="divider"></div>

		<input type="text" placeholder="Answer 1 (the right answer)" name="answer_1"><br>
		<input type="text" placeholder="Answer 2 (a fake answer)" name="answer_2"><br>
		<input type="text" placeholder="Answer 3 (a fake answer)" name="answer_3"><br>
		<input type="text" placeholder="Answer 4 (a fake answer)" name="answer_4"><br>

		<input type="submit" class="button-primary" value="Add" name="add_question">

	</form>
	

</div>


<div class="box">

	<div class="box-header">
		<b>QUESTIONS</b>
	</div>

	<?php 
		$query = $db->prepare("SELECT * FROM questions"); 
		$query->execute();

		while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
	?>

	<?php echo $row['question']; ?>

	<div class="divider"></div>

	<?php } ?>

</div>





<?php require("libraries/footerContent.php"); ?>