<?php 

require("libraries/headerContent.php");

$user->redirect_not_logged();

if(isset($_GET['delete']))
{
	$user->redirect_not_admin();

	$test_id = $_GET['delete'];

	//check if it exists.

	$query = $db->prepare("SELECT id FROM tests WHERE id = :test");
	$query->bindParam(":test", $test_id);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to delete the test. 1", "error", "tests.php");

	if(!$query->rowCount())
		$user->redirect("No test has been found with that id.", "error", "tests.php");

	//process deleting.

	$query = $db->prepare("DELETE FROM tests WHERE id = :test");
	$query->bindParam(":test", $test_id);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to delete the test. 2", "error", "tests.php");

	//process deleting questions.
	
	$query = $db->prepare("DELETE FROM questions WHERE for_test = :test");
	$query->bindParam(":test", $test_id);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to delete the test. 3", "error", "tests.php");

	//process deleting answers
	
	$query = $db->prepare("DELETE FROM answers WHERE for_test = :test");
	$query->bindParam(":test", $test_id);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to delete the test. 4", "error", "tests.php");

	//process deleting user progress.

	$query = $db->prepare("DELETE FROM progress WHERE test_id = :test");
	$query->bindParam(":test", $test_id);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to delete the test. 5", "error", "tests.php");

	//process deleting results.
	
	$query = $db->prepare("DELETE FROM results WHERE test_id = :test");
	$query->bindParam(":test", $test_id);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to delete the test. 6", "error", "tests.php");

	$query = $db->prepare("DELETE FROM results_qa WHERE test_id = :test");
	$query->bindParam(":test", $test_id);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to delete the test. 7", "error", "tests.php");

	$user->redirect("The test has been deleted.", "success", "tests.php");

}

if(isset($_GET['open']))
{
	$user->redirect_not_admin();

	$test_id = $_GET['open'];

	//check if it exists.

	$query = $db->prepare("SELECT id FROM tests WHERE id = :test");
	$query->bindParam(":test", $test_id);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to open the test.", "error", "tests.php");

	if(!$query->rowCount())
		$user->redirect("No test has been found with that id.", "error", "tests.php");

	$row = $query->fetch(PDO::FETCH_ASSOC);

	$query = $db->prepare("UPDATE tests SET status = 1 WHERE id = :test");
	$query->bindParam(":test", $test_id);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to open the test.", "error", "tests.php");

	$user->redirect("The test has been successfully open.", "success", "tests.php");


}

if(isset($_GET['close']))
{
	$user->redirect_not_admin();

	$test_id = $_GET['close'];

	//check if it exists.

	$query = $db->prepare("SELECT id FROM tests WHERE id = :test");
	$query->bindParam(":test", $test_id);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to close the test.", "error", "tests.php");

	if(!$query->rowCount())
		$user->redirect("No test has been found with that id.", "error", "tests.php");

	$row = $query->fetch(PDO::FETCH_ASSOC);

	$query = $db->prepare("UPDATE tests SET status = 0 WHERE id = :test");
	$query->bindParam(":test", $test_id);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to open the test.", "error", "tests.php");

	$user->redirect("The test has been successfully closed.", "success", "tests.php");
}

?>

<h2>Tests</h2>

<?php if($user->admin()) { echo '<a href="create.php"><input type="submit" class="btn btn-primary" value="Create Test"></a><br><br>'; } ?>

<hr>

<center><h3>Your Tests <b>(<?php echo strtoupper($user->table['level']); ?>)</b></h3></center>

<?php 
	$query = $db->prepare("SELECT * FROM tests WHERE level = :level");
	$query->bindParam(":level", $user->table['level']);
	$query->execute();

	while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
?>

<div class="card">
	<div class="card-header">
		Test #<?php echo $row['id']; ?> - <b><?php echo strtoupper($row['level']); ?></b>

		<?php $status = $user->has_completed_test($row['id']); ?>

		<?php if($row['status'] == 0) { ?>

			<span class="badge badge-danger">closed</span>

		<?php } else if($row['status'] == 1) { ?>

			<span class="badge badge-success">open</span>

		<?php } ?>

		<?php if($status) { ?>

			<span class="badge badge-primary">completed</span>

		<?php } ?>

	</div>
	<div class="card-body">
		<h5 class="card-title"><?php echo $row['title']; ?> </h5>
		<div class="progress">
			<div class="progress-bar" role="progressbar" style="width: <?php echo $user->get_test_score($row['id']); ?>%" aria-valuenow="<?php echo $user->get_test_score($row['id']); ?>" aria-valuemin="0" aria-valuemax="100"></div>
		</div>

		<br>

		<?php if($status) { ?>

			<a href="results.php?id=<?php echo $user->get_result_id($row['id']); ?>"><button type="button" class="btn btn-primary">Review</button></a>

		<?php } else { ?>

			<a href="test.php?id=<?php echo $row['id']; ?>"><button type="button" class="btn btn-success">Open</button></a>

		<?php } ?>

		<?php if($user->admin()) { ?>

			<hr>

			<a href="tests.php?delete=<?php echo $row['id']; ?>"><button type="button" class="btn btn-danger">Delete</button></a> 

			<?php if($row['status']) { ?>

				<a href="tests.php?close=<?php echo $row['id']; ?>"><button type="button" class="btn btn-danger">Close</button></a>

			<?php } else if($row['status'] == 0) { ?>

				<a href="tests.php?open=<?php echo $row['id']; ?>"><button type="button" class="btn btn-primary">Open</button></a>

			<?php } ?>

			<a href="result.php?id=<?php echo $row['id']; ?>"><button type="button" class="btn btn-warning">Results</button></a> 

			<a href="modify.php?id=<?php echo $row['id']; ?>"><button type="button" class="btn btn-warning">Edit</button></a> 

		<?php } ?>

	</div>
</div>

<br>

<?php } ?>

<center><h3>Other Tests</h3></center>

<hr>

<?php 
	$query = $db->prepare("SELECT * FROM tests WHERE level != :level");
	$query->bindParam(":level", $user->table['level']);
	$query->execute();

	while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
?>

<div class="card">
	<div class="card-header">
		Test #<?php echo $row['id']; ?> - <b><?php echo strtoupper($row['level']); ?></b>

		<?php $status = $user->has_completed_test($row['id']); ?>

		<?php if($row['status'] == 0) { ?>

			<span class="badge badge-danger">closed</span>

		<?php } else if($row['status'] == 1) { ?>

			<span class="badge badge-success">open</span>

		<?php } ?>

		<?php if($status) { ?>

			<span class="badge badge-primary">completed</span>

		<?php } ?>

	</div>
	<div class="card-body">
		<h5 class="card-title"><?php echo $row['title']; ?> </h5>
		<div class="progress">
			<div class="progress-bar" role="progressbar" style="width: <?php echo $user->get_test_score($row['id']); ?>%" aria-valuenow="<?php echo $user->get_test_score($row['id']); ?>" aria-valuemin="0" aria-valuemax="100"></div>
		</div>

		<br>

		<?php if($status) { ?>

			<a href="results.php?id=<?php echo $user->get_result_id($row['id']); ?>"><button type="button" class="btn btn-primary">Review</button></a>

		<?php } else { ?>

			<a href="test.php?id=<?php echo $row['id']; ?>"><button type="button" class="btn btn-success" disabled>Open</button></a>

		<?php } ?>

		<?php if($user->admin()) { ?>

			<hr>

			<a href="tests.php?delete=<?php echo $row['id']; ?>"><button type="button" class="btn btn-danger">Delete</button></a> 

			<?php if($row['status']) { ?>

				<a href="tests.php?close=<?php echo $row['id']; ?>"><button type="button" class="btn btn-danger">Close</button></a>

			<?php } else if($row['status'] == 0) { ?>

				<a href="tests.php?open=<?php echo $row['id']; ?>"><button type="button" class="btn btn-primary">Open</button></a>

			<?php } ?>

			<a href="result.php?id=<?php echo $row['id']; ?>"><button type="button" class="btn btn-warning">Results</button></a> 

			<a href="modify.php?id=<?php echo $row['id']; ?>"><button type="button" class="btn btn-warning">Edit</button></a> 

		<?php } ?>

	</div>
</div>

<br>

<?php } ?>




<?php require("libraries/footerContent.php"); ?>