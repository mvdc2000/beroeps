<?php 

require("libraries/headerContent.php");

$user->redirect_not_admin();

if(!isset($_GET['id']))
	$user->redirect("You must specify a user.", "error", "index.php");

$user_id = $_GET['id'];

//check if user exists.
$query = $db->prepare("SELECT * FROM users WHERE id = :id");
$query->bindParam(":id", $user_id);

if(!$query->execute())
	$user->redirect("An error occured while trying to fetch the user.", "error", "index.php");

if(!$query->rowCount())
	$user->redirect("No user has been found with that id.", "error", "index.php");

$result = $query->fetch(PDO::FETCH_ASSOC);

$link = "edit.php?id=".$user_id;

if(isset($_POST['change_password']))
{
	if(!isset($_POST['password']) || !isset($_POST['repeat_password']))
		$user->redirect("You must specify a password.", "error", $link);

	if($_POST['password'] != $_POST['repeat_password'])
		$user->redirect("The passwords you have entered don't match.", "error", $link);

	if(strlen($_POST['password']) < 3)
		$user->redirect("The password is too short.", "error", $link);

	//hash password.
	$password = sha1($_POST['password']);

	//update password.
	$query = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
	$query->bindParam(":password", $password);
	$query->bindParam(":id", $user_id);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to update the password.", "error", $link);

	$user->redirect("The password has been changed!", "success", $link);
}

if(isset($_POST['change_email']))
{
	if(!isset($_POST['email']))
		$user->redirect("You must specify an email.", "error", $link);

	if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		$user->redirect("You must specify an email.", "error", $link);

	//update email
	$query = $db->prepare("UPDATE users SET email = :email WHERE id = :id");
	$query->bindParam(":email", $_POST['email']);
	$query->bindParam(":id", $user_id);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to update the email.", "error", $link);

	$user->redirect("The email has been changed!", "success", $link);
}

if(isset($_POST['change_level']))
{
	if(!isset($_POST['level']))
		$user->redirect("You must specify a skill level.", "error", $link);

	//change skill level
	$query = $db->prepare("UPDATE users SET level = :level WHERE id = :id");
	$query->bindParam(":level", $_POST['level']);
	$query->bindParam(":id", $user_id);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to update the skill level.", "error", $link);

	$user->redirect("The skill level has been changed!", "success", $link);
}

?>

<h2><?php echo $result['username']; ?>'s Settings</h2>

<div class="card">
	<div class="card-header">Change Password</div>
	<div class="card-body">
	
		<form method="post">

			<div class="form-group">
				<input type="password" class="form-control" placeholder="Password" name="password">
			</div>
			<div class="form-group">
				<input type="password" class="form-control" placeholder="Repeat Password" name="repeat_password">
			</div>

			<input type="submit" class="btn btn-primary" name="change_password" value="Change Password">
		</form>

	</div>
</div>

<br>

<div class="card">
	<div class="card-header">Change The Email (<?php echo $result['email']; ?>)</div>
	<div class="card-body">
	
		<form method="post">
			<div class="form-group">
				<input type="email" class="form-control" placeholder="Email" name="email">
			</div>

			<input type="submit" class="btn btn-primary" name="change_email" value="Change Email">
		</form>

	</div>
</div>

<br>

<div class="card">
	<div class="card-header">Current Skill Level: <b><?php echo strtoupper($result['level']); ?></b></div>
	<div class="card-body">
	
		<form method="post">
			<div class="form-group">
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
			</div>

			<input type="submit" class="btn btn-primary" name="change_level" value="Change Level">
		</form>

	</div>
</div>

<?php require("libraries/footerContent.php"); ?>