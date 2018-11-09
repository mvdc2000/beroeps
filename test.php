<?php 

require("libraries/headerContent.php");

$user->redirect_not_logged();

if(!isset($_GET['id']))
	$user->redirect("No test has been specified.", "error", "index.php");

if(!isset($_GET['question']))
	header("Location: test.php?id=".$_GET['id']."&question=1");

if($_GET['question'] < 1)
	header("Location: test.php?id=".$_GET['id']."&question=1");

$test_id = $_GET['id'];

//grab test

$query = $db->prepare("SELECT * FROM tests WHERE id = :id");
$query->bindParam(":id", $test_id);

if(!$query->execute())
	$user->redirect("An error has occured while processing your request. Please try again later.", "error", "index.php");

if(!$query->rowCount())
	$user->redirect("No test has been found.", "error", "index.php");

$test = $query->fetch(PDO::FETCH_ASSOC);

if($user->has_completed_test($test['id']))
	$user->redirect("You already completed this test.", "error", "tests.php");

if(strtoupper($user->table['level']) != strtoupper($test['level']))
	$user->redirect("Your skill level doesn't match with this test.", "error", "tests.php");

//grab questions

$query = $db->prepare("SELECT * FROM questions WHERE for_test = :test");
$query->bindParam(":test", $test['id']);

if(!$query->execute())
	$user->redirect("An error has occured while processing your request. Please try again later.", "error", "index.php");

if(!$query->rowCount())
	$user->redirect("No questions have been found.", "error", "index.php");

$questions = $query->fetchAll();

if($_GET['question'] > sizeof($questions))
	header("Location: test.php?id=".$_GET['id']."&question=".sizeof($questions));

//grab answers

$answers = array();

//select the answers from the db. (caching)
foreach($questions as $q)
{
	$query = $db->prepare("SELECT * FROM answers WHERE for_question = :question");
	$query->bindParam(":question", $q['id']);

	if(!$query->execute())
		$user->redirect("An error has occured while processing your request. Please try again later.", "error", "index.php");

	if(!$query->rowCount())
		$user->redirect("No test has been found.", "error", "index.php");

	$answer = $query->fetchAll();

	array_push($answers, $answer);
}

//complete the test.
if(isset($_POST['submit']))
{
	// var_dump($_POST);
	$score = 0;
	
	$query = $db->prepare("INSERT INTO results (user_id, test_id) VALUES (:user, :test)");
	$query->bindParam(":user", $user->table['id']);
	$query->bindParam(":test", $test_id);

	if(!$query->execute())
		$user->redirect("There was an error processing your results!", "error", "index.php");

	$result_id = $db->lastInsertId();

	for($i = 0; $i < sizeof($questions); $i++)
	{
		$a = "answer_".$i;

		if(!isset($_POST[$a]))
		{
			$user->redirect("Niet alles is ingevuld!.", "error", "index.php");
		}
		else 
		{
			// print_r($answers[$i]);
			$type = 0;

			$good_answer = "";

			for($z = 0; $z < 4; $z++)
			{
				echo "<br>";

				if($answers[$i][$z]['type']) //is right answer
				{
					$good_answer = $answers[$i][$z]['answer'];

					if($_POST[$a] == $answers[$i][$z]['answer']) //has user same answer
					{
						$score += $answers[$i][$z]['type'];
						$type = 1;
						break;
					}

					break;
				}

			}

			$query = $db->prepare("INSERT INTO results_qa (question, answer, right_answer, status, result_id, test_id) VALUES (:q, :a, :ra, :s, :rid, :test)");
			$query->bindParam(":q", $questions[$i]['question']);
			$query->bindParam(":a", $_POST[$a]);
			$query->bindParam(":ra", $good_answer);
			$query->bindParam(":s", $type);
			$query->bindParam(":rid", $result_id);
			$query->bindParam(":test", $test_id);

			if(!$query->execute())
			{
				$user->redirect("There was an error processing your results!", "error", "index.php");
			}

		}
	}

	//clean the test.
	
	$query = $db->prepare("DELETE FROM progress WHERE test_id = :test AND user_id = :user");
	$query->bindParam(":test", $test_id);
	$query->bindParam(":user", $user->table['id']);

	if(!$query->execute())
	{
		$user->redirect("An error has appeared while processing your request.", "error", "index.php");
	}

	//redirect

	$page = "results.php?id=".$result_id;

	$user->redirect("You have completed the test! Here are your results.", "success", $page);
}

?>

<h2><?php echo $test['title']; ?></h2>

<script>
	
	var at_question = 1; 

	$( document ).ready(function() {

		$(".questions .question").each(function(e) 
		{

	        if (e != 0)
	        {
	        	$(this).hide();
	        }

	    });

	    $("#next").click(function()
	    {
	        if ($(".questions .question:visible").next().length != 0)
	        {

	        	if($(".questions .question:visible input[type='radio']:checked").val())
	        	{
	        		updateProgress('save', <?php echo $user->table['id']; ?>, at_question, $(".questions .question:visible input[type='radio']:checked").val(), <?php echo $test_id; ?>);
	        	}
	        	else if($(".questions div:visible input[type='text']").val())
	        	{
	        		updateProgress('save', <?php echo $user->table['id']; ?>, at_question, $(".questions .question:visible input[type='text']").val(), <?php echo $test_id; ?>);
	        	}

	        	$(".questions .question:visible").next().show().prev().hide();

	        	at_question ++;
	        }

	        return false;
	    });

	    $("#prev").click(function()
	    {

	        if ($(".questions .question:visible").prev().length != 0)
	        {
	        	$(".questions .question:visible").prev().show().next().hide();
	        	at_question --;
	        }

	        return false;
	    });

	});

	function updateProgress(act, user, question, ans, test)
	{
		$.post("https://76431.ict-lab.nl/toets/libraries/queryController.php", { 
        	action: act, 
        	user_id: user, 
        	question_id: question, 
        	answer: ans, 
        	test_id: test 
        });
	}

</script>

<hr>

<form method="POST">

	<div class="questions">

		<?php 

			for($i = 0; $i < sizeof($questions); $i++) 
			{ 
				$html = "";

				$query = $db->prepare("SELECT answer FROM progress WHERE user_id = :user AND question_id = :question AND test_id = :test");
				$query->bindParam(":user", $user->table['id']);
				$query->bindParam(":question", $questions[$i]['id']);
				$query->bindParam(":test", $test_id);

				if($query->execute())
				{
					$info = $query->fetch(PDO::FETCH_ASSOC);
				}

				if($questions[$i]['type'] == 0)
				{
					for($z = 0; $z < 4; $z++)
					{
						if($info != NULL && $info['answer'] == $answers[$i][$z]['answer'])
							$html = "<input type='radio' class='form-control' name='answer_".$i."' value='".$answers[$i][$z]['answer']."' checked> ".$answers[$i][$z]['answer']."<br>".$html;
						else
							$html = "<input type='radio' class='form-control' name='answer_".$i."' value='".$answers[$i][$z]['answer']."'> ".$answers[$i][$z]['answer']."<br>".$html;
					}

				}
				else
				{
					if(strlen($info['answer']))
						$html = "<input type='text' class='form-control' name='answer_".$i."' placeholder='Type your answer here.' value=".$info['answer']."><br>".$html;
					else
						$html = "<input type='text' class='form-control' name='answer_".$i."' placeholder='Type your answer here.'><br>".$html;

				}


				if($i == sizeof($questions) - 1) //last question
				{

					echo "

						<div class='question'>
						
							<div class='card'>
								<div class='card-header'>".$questions[$i]['question']."</div>
								<div class='card-body'>
								
									<div class='form-group'>
										".$html."

										<br>

										<center><input type='submit' class='btn btn-primary' name='submit' value='Results'></center>
									</div>

								</div>
							</div>

						</div>

					";	
				}
				else 
				{
					echo "

						<div class='question'>
						
							<div class='card'>
								<div class='card-header'>".$questions[$i]['question']."</div>
								<div class='card-body'>
								
									<div class='form-group'>
										".$html."
									</div>

								</div>
							</div>

						</div>
					";	
				}
			}

		?>


	</div>


</form>


<center style="padding-top: 10px;">
	
<?php 

	echo '<a id="prev"><i class="fas fa-arrow-left"></i></a> ';

	for($i = 0; $i < sizeof($questions); $i++)
	{
		echo '<i class="fas fa-circle" style="color: #41BCFA;"></i> ';
		// echo $questions[$i]['question']."<br>";

		// for($z = 0; $z < 4; $z++)
		// {
		// 	echo $answers[$i][$z]['answer']."<br>";
		// }
	}

	echo '<a id="next"><i class="fas fa-arrow-right"></i></a> ';

?>


</center>

<?php require("libraries/footerContent.php"); ?>