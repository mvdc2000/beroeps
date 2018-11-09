<?php 

require("libraries/headerContent.php");

$user->redirect_not_admin();

if(isset($_GET['delete']))
{
	if(!isset($_GET['id']))
		$user->redirect("You must specify a test.", "error", "index.php");

	$question = $_GET['delete'];
	
	$link = "modify.php?id=".$_GET['id'];

	if(!is_numeric($_GET['delete']))
		$user->redirect("An error has occured while trying to delete the question. 1", "error", $link);

	$query = $db->prepare("SELECT id FROM questions WHERE id = :id");
	$query->bindParam(":id", $question);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to delete the question. 2", "error", $link);

	if(!$query->rowCount())
		$user->redirect("An error has occured while trying to delete the question. 3", "error", $link);


	//delete question

	$query = $db->prepare("DELETE FROM questions WHERE id = :id");
	$query->bindParam(":id", $question);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to delete the question. 4", "error", $link);

	if(!$query->rowCount())
		$user->redirect("An error has occured while trying to delete the question. 5", "error", $link);


	//delete answers

	$query = $db->prepare("DELETE FROM answers WHERE for_question = :id");
	$query->bindParam(":id", $question);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to delete the question. 6", "error", $link);

	if(!$query->rowCount())
		$user->redirect("An error has occured while trying to delete the question. 7", "error", $link);


	$user->redirect("The question has been deleted.", "success", $link);
}
else 
{
	if(!isset($_GET['id']))
		$user->redirect("You must specify a test.", "error", "index.php");

	$test_id = $_GET['id'];

	$query = $db->prepare("SELECT * FROM tests WHERE id = :id");
	$query->bindParam(":id", $test_id);

	if(!$query->execute())
		$user->redirect("An error has occured while fetching data.", "error", "tests.php");

	if(!$query->rowCount())
		$user->redirect("An error has occured while fetching data.", "error", "tests.php");

	$results = $query->fetch(PDO::FETCH_ASSOC);

	$query = $db->prepare("SELECT * FROM questions WHERE for_test = :test");
	$query->bindParam(":test", $results['id']);

	if(!$query->execute())
		$user->redirect("An error has occured while fetching data.", "error", "tests.php");

	if(!$query->rowCount())
		$user->redirect("An error has occured while fetching data.", "error", "tests.php");
}

?>

<h2>Edit Test</h2>

<div class="card">
	<div class="card-header">Manage Test</div>
	<div class="card-body">
	
		<table class="table table-striped">
			<thead>
				<tr>
					<th scope="col">Question</th>
					<th scope="col">Type</th>
					<th scope="col">Added By</th>
					<th scope="col">Options</th>
				</tr>
			</thead>
			<tbody>
				<?php while($row = $query->fetch(PDO::FETCH_ASSOC)) { ?>
				<tr>
					<th scope="row"><?php echo $row['question']; ?></th>
					<td><?php echo $row['type']; ?></td>
					<td><?php echo get_name($row['added_by']); ?></td>
					<td><a href="modify.php?id=<?php echo $test_id; ?>&delete=<?php echo $row['id']; ?>"><i class="fas fa-times-circle text-danger"></i></a></td>
				</tr>

				<?php } ?>
			</tbody>
		</table>

	</div>
</div>

<?php require("libraries/footerContent.php"); ?>