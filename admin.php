<?php 

require("libraries/headerContent.php");

$user->redirect_not_admin();

//check for the delete parameter.
if(isset($_GET['delete']))
{
	$user_id = $_GET['delete'];

	//check if user exists.
	$query = $db->prepare("SELECT username FROM users WHERE id = :id");
	$query->bindParam(":id", $user_id);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to process the usernames.", "error", "admin.php");

	if(!$query->rowCount())
		$user->redirect("That user does not exist.", "error", "admin.php");

	//delete user
	$query = $db->prepare("DELETE FROM users WHERE id = :id");
	$query->bindParam(":id", $user_id);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to process the usernames.", "error", "admin.php");

	$user->redirect("User has been deleted!", "success", "admin.php");
}

if(isset($_POST['generate']))
{
	if(!isset($_POST['number']))
		$user->redirect("You must enter a number.", "error", "admin.php");

	if($_POST['number'] < 1)
		$user->redirect("You must enter a number.", "error", "admin.php");

	if($_POST['number'] > 50)
		$user->redirect("You can only create 50 users at a time.", "error", "admin.php");

	$number = $_POST['number'];

	$string = "";

	//loop through the specified value.
	for($i = 0; $i < $number; $i++)
	{
		//generate a username & password for every index.
		$username = generateRandomNumber(70000, 90000);
		$password = generateRandomString(6);
		$hashed = sha1($password);

		//insert the new user in to the database.
		$query = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
		$query->bindParam(":username", $username);
		$query->bindParam(":password", $hashed);

		if(!$query->execute())
		{
			$user->redirect("An error has occured while trying to process the usernames.", "error", "admin.php");
			break;
		}

		$string = $string.$username." - ".$password."\n";

	}

	//save a file with every user in the secret folder that only admins can view.
	$name = "users_".date("H:i:s_j-n-Y").".txt";

	$file = fopen("files/".$name, "w");

	fwrite($file, $string);

	fclose($file);

	$user->redirect("Users have been generated.", "success", "admin.php");

}

//wait for the admin parameter
if(isset($_GET['admin']))
{
	if(!isset($_GET['set']))
		$user->redirect("An error has occured while trying to update the admin level.", "error", "admin.php");

	if($_GET['set'] > 1 || $_GET['set'] < 0)
		$user->redirect("An error has occured while trying to update the admin level.", "error", "admin.php");

	//verify if user exists.
	
	$query = $db->prepare("SELECT id FROM users WHERE id = :id");
	$query->bindParam(":id", $_GET['admin']);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to update the admin level.", "error", "admin.php");

	if(!$query->rowCount())
		$user->redirect("An error has occured while trying to update the admin level.", "error", "admin.php");

	//process the update query.
	
	$query = $db->prepare("UPDATE users SET admin = :admin WHERE id = :id");
	$query->bindParam(":admin", $_GET['set']);
	$query->bindParam(":id", $_GET['admin']);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to update the admin level.", "error", "admin.php");

	$user->redirect("The admin level has been updated.", "success", "admin.php");
}

//grab files from directory.

$files = array();

foreach (scandir("files") as $file) {
    if ('.' === $file) continue;
    if ('..' === $file) continue;

    $files[] = $file;
}



?>

<h2>Admin</h2>

<h5>Quick Links</h5>

<ul>
	<li><a href="create.php" class="text-primary">Create Test</a></li>
</ul>

<hr>

<div class="card">
	<div class="card-header">Generate Users</div>
	<div class="card-body">
	
		<form method="post">
			<div class="form-group">
				<input type="number" class="form-control" placeholder="Number of users." name="number">
				<small id="emailHelp" class="form-text text-muted">Notice: You can only generate 50 users at a time.</small>
			</div>
			<input type="submit" class="btn btn-primary" name="generate" value="Generate">
		</form>

	</div>
</div>

<br>

<div class="card">
	<div class="card-header">Files</div>
	<div class="card-body">
	
		<?php 

			echo "<ul>";

			for($i = 0; $i < sizeof($files); $i++)
			{
				if($files[$i][0] == ".")
					continue;

				echo "<li><a href='file.php?file=".$files[$i]."'>".$files[$i]."</a></li>";
			}

			echo "</ul>";
			

		?>

	</div>
</div>

<br>

<div class="card">
	<div class="card-header">Manage Users</div>
	<div class="card-body">
	
		<table class="table table-striped">
			<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">Username</th>
					<th scope="col">Level</th>
					<th scope="col">Admin</th>
					<th scope="col">Email</th>
					<th scope="col">Register Date</th>
					<th scope="col">Options</th>
				</tr>
			</thead>
			<tbody>

				<?php 
					$query = $db->prepare("SELECT * FROM users"); 
					$query->execute();

					while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
				?>

				<tr>
					<th scope="row"><?php echo $row['id']; ?></th>
					<td><?php echo $row['username']; ?></td>
					<td><?php echo strtoupper($row['level']); ?></td>
					<td><?php echo $row['admin']; ?></td>
					<td>
						<?php 

							if(strlen($row['email']))
							{
								echo $row['email'];
							}
							else 
							{
								echo "None";
							}

					 	?>
					</td>
					<td><?php echo $row['register']; ?></td>
					<td>
						<a href="admin.php?delete=<?php echo $row['id']; ?>"><i class="fas fa-times-circle text-danger" data-toggle="tooltip" data-placement="top" title="Delete this user."></i></a>

						<?php if($row['admin']) { ?>

							<a href="admin.php?admin=<?php echo $row['id']; ?>&set=0"><i class="fas fa-arrow-circle-down text-danger" data-toggle="tooltip" data-placement="top" title="Set the admin level to 0."></i></a>

						<?php } else { ?>

							<a href="admin.php?admin=<?php echo $row['id']; ?>&set=1"><i class="fas fa-arrow-circle-up text-primary" data-toggle="tooltip" data-placement="top" title="Set the admin level to 1."></i></a>

						<?php } ?>

						<a href="edit.php?id=<?php echo $row['id']; ?>"><i class="fas fa-cog text-primary" data-toggle="tooltip" data-placement="top" title="Edit this user."></i></a>

					</td>
				</tr>

				<?php } ?>
			</tbody>
		</table>

	</div>
</div>

<br>

<?php require("libraries/footerContent.php"); ?>