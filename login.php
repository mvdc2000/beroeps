<?php 

require("libraries/headerContent.php");

if(isset($_POST['login']))
{

	//check if the parameters have been filled in.
	if(!isset($_POST['username']) || !isset($_POST['password']) || !strlen($_POST['username']) || !strlen($_POST['password']))
	{
		$user->redirect("You didn't fill in all the fields.", "error", "login.php");
	}

	$username = $_POST['username'];
	$password = sha1($_POST['password']);

	//check if user exists.
	$query = $db->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
	$query->bindParam(":username", $username);
	$query->bindParam(":password", $password);

	if(!$query->execute())
	{
		$user->redirect("There was an error trying to process your request. Please try again later.", "error", "login.php");
	}

	if(!$query->rowCount()) // user bestaat niet (niet gevonden)
	{
		$user->redirect("That username or password combination does not exist.", "error", "login.php");
	}

	//update session data.
	$_SESSION['username'] = $username;
	$_SESSION['password'] = $password;

	$user->redirect("You have logged in successfully.", "success", "index.php");

}

?>

<center>
	
	<div class="card">
		<div class="card-header">
			Login
		</div>
		<div class="card-body">
			<form method="POST">

				<div class="input-group mb-3">
					<input type="text" class="form-control" placeholder="Username" name="username"><br>
				</div>

				<div class="input-group mb-3">
					<input type="password" class="form-control" placeholder="Password" name="password"><br>
				</div>

				<div class="input-group mb-3">
					<input type="submit" class="btn btn-primary" name="login" value="Login">
				</div>

			</form>
		</div>
	</div>

</center>

<?php require("libraries/footerContent.php"); ?>