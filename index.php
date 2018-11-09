<?php 

require("libraries/headerContent.php");

$user->redirect_not_logged();

//select tests from db for the specified skill level.
$query = $db->prepare("SELECT * FROM tests WHERE level = :level AND status = 1");
$query->bindParam(":level", $user->table['level']); 

if(!$query->execute())
	$user->redirect("An error has occured while trying to reach the home page. Please try again later.", "error", "login.php");

?>


<!-- display warning message if user has no email. -->
<?php if($user->table['email'] == "None") { ?>

	<div class="alert alert-warning" role="alert">
		You have not set an email yet! You can do that <a href="settings.php">here</a>.
	</div>
<!-- aanpassing voor git -->
<?php } ?>

<div class="jumbotron jumbotron-fluid">
	<div class="container">
		<center>
			
			<h1 class="display-4">Welcome Back, <?php echo $user->table['username']; ?></h1>
			<?php if($query->rowCount()) { ?>
				<p class="lead">There are tests available for your skill level!</p>
			<?php } else { ?>
				<p class="lead">There are currently no tests available for your skill level.</p>
			<?php } ?>

 		</center>
	</div>
</div>

<hr>

<!-- loop through the tests -->
<?php while ($row = $query->fetch(PDO::FETCH_ASSOC)) { ?>

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

	</div>
</div>

<br>

<?php } ?>


<?php require("libraries/footerContent.php"); ?>
