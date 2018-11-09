<?php 


require("libraries/headerContent.php");

if(!isset($_GET['id']))
	$user->redirect("The results you requested are not available.", "error", "index.php");

$user->redirect_not_logged();

$result_id = $_GET['id'];

//check if result id exists.

$query = $db->prepare("SELECT * FROM results WHERE id = :id and user_id = :user");
$query->bindParam(":id", $result_id);
$query->bindParam(":user", $user->table['id']);

if(!$query->execute())
	$user->redirect("The results you requested are not available.", "error", "index.php");

if(!$query->rowCount())
	$user->redirect("The results you requested are not available. #1", "error", "index.php");

$result = $query->fetch(PDO::FETCH_ASSOC);

// grab results

$query = $db->prepare("SELECT * FROM results_qa WHERE result_id = :id");
$query->bindParam(":id", $result['id']);

if(!$query->execute())
	$user->redirect("The results you requested are not available. #2", "error", "index.php");

if(!$query->rowCount())
	$user->redirect("The results you requested are not available. #3", "error", "index.php");

$grade = $user->get_grade($result['test_id']);

?>

<h2>Tests | Results</h2>

<center>

	<?php if($grade >= 5.5) { ?>
		<b><p style="font-size: 60px;" class="text-success"><?php echo $grade; ?></p></b>
	<?php } else { ?>
		<b><p style="font-size: 60px;" class="text-danger"><?php echo $grade; ?></p></b>
	<?php } ?>


</center>

<table class="table">
	<thead>
		<tr>
			<th scope="col">#</th>
			<th scope="col">Question</th>
			<th scope="col">Your Answer</th>
			<th scope="col">Good Answer</th>
			<th scope="col">Status</th>
		</tr>
	</thead>
	<tbody>

		<?php 

			$count = 0;

			while($result = $query->fetch(PDO::FETCH_ASSOC)) { 
			$count ++;

		?>

		<tr>
			<th scope="row"><?php echo $count; ?></th>
			<td><?php echo $result['question']; ?></td>
			<td><?php echo $result['answer']; ?></td>
			<td><?php echo $result['right_answer']; ?></td>
			<td>
				
				<?php if($result['status']) { ?>
					<i class="fas fa-check-circle text-success"></i>
				<?php } else { ?>
					<i class="fas fa-times-circle text-danger"></i>
				<?php } ?>

			</td>
		</tr>

		<?php } ?>
	</tbody>
</table>


<?php require("libraries/footerContent.php"); ?>