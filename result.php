<?php 


require("libraries/headerContent.php");

$user->redirect_not_admin();

if(!isset($_GET['id']))
	$user->redirect("The results you requested are not available.", "error", "index.php");

$result_id = $_GET['id'];

//check if test exists.

$query = $db->prepare("SELECT * FROM tests WHERE id = :id");
$query->bindParam(":id", $result_id);

if(!$query->execute())
	$user->redirect("The results you requested are not available.", "error", "index.php");

if(!$query->rowCount())
	$user->redirect("The results you requested are not available.", "error", "index.php");

$result = $query->fetch(PDO::FETCH_ASSOC);

//check for users with the skill level.

$query = $db->prepare("SELECT * FROM users WHERE level = :level");
$query->bindParam(":level", $result['level']);

if(!$query->execute())
	$user->redirect("The results you requested are not available.", "error", "index.php");

if(!$query->rowCount())
	$user->redirect("No one has the right skill level to complete this test.", "error", "index.php");

?>

<h2>Tests | Results</h2>


<table class="table">
	<thead>
		<tr>
			<th scope="col">#</th>
			<th scope="col">Username</th>
			<th scope="col">Email</th>
			<th scope="col">Test Status</th>
		</tr>
	</thead>
	<tbody>
		<?php while($result = $query->fetch(PDO::FETCH_ASSOC)) { ?>

		<tr>
			<th scope="row"><?php echo $result['id']; ?></th>
			<td><?php echo $result['username']; ?></td>
			<td><?php echo $result['email']; ?></td>
			<td>

				<?php if(has_user_completed_test($result['id'], $result_id)) { ?>

					<i class="fas fa-check-circle text-success"></i>

					<?php $grade = $user->get_grade_for_user($result['id'], $result_id); ?>

					<?php if($grade >= 5.5) { ?>
						<span class="text-success"><?php echo $grade; ?></span>
					<?php } else { ?>
						<span class="text-danger"><?php echo $grade; ?></span>
					<?php } ?>

				<?php } else { ?>

					<i class="fas fa-times-circle text-danger"></i>

				<?php } ?>

			</td>
		</tr>

		<?php } ?>
	</tbody>
</table>

<?php require("libraries/footerContent.php"); ?>