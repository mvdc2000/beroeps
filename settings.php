<?php 

require("libraries/headerContent.php");

$user->redirect_not_logged();

//check if user wants to change email.
if(isset($_POST['change_email']))
{
	//security checks
	if(!isset($_POST['email']))
		$user->redirect("You must specify an email.", "error", "settings.php");

	if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		$user->redirect("You must specify an email.", "error", "settings.php");

	$query = $db->prepare("UPDATE users SET email = :email WHERE id = :id");
	$query->bindParam(":email", $_POST['email']);
	$query->bindParam(":id", $user->table['id']);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to update your email.", "error", "settings.php");

	$user->redirect("Your email has been changed!", "success", "settings.php");
}

//check if user wants to change password.
if(isset($_POST['change_password']))
{
	//security checks
	if(!isset($_POST['password']) || !isset($_POST['repeat_password']))
		$user->redirect("You must specify a password.", "error", "settings.php");

	if($_POST['password'] != $_POST['repeat_password'])
		$user->redirect("The passwords you have entered don't match.", "error", "settings.php");

	if(strlen($_POST['password']) < 3)
		$user->redirect("The password is too short.", "error", "settings.php");

	$password = sha1($_POST['password']);

	//update password in db.
	$query = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
	$query->bindParam(":password", $password);
	$query->bindParam(":id", $user->table['id']);

	if(!$query->execute())
		$user->redirect("An error has occured while trying to update your password.", "error", "settings.php");

	//update session with new password so that the user won't get logged out.
	$_SESSION['password'] = $password;

	$user->redirect("Your password has been changed!", "success", "settings.php");
}


?>

<h2>Settings</h2>

<div class="card">
	<div class="card-header">Change Your Email (<?php echo $user->table['email']; ?>)</div>
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
	<div class="card-header">Change Your Password</div>
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

<?php require("libraries/footerContent.php"); ?>