<?php 
session_start();

//checks if the logout parameter has been found in the link & cleans the session.
if(isset($_GET['logout']))
{
	session_destroy();
	$user->redirect("U bent successvol uitgelogd!", "success", "login.php");
}

//checks if you're logged in.
if(isset($_SESSION['username']) && isset($_SESSION['password']))
{
	$username = $_SESSION['username'];
	$password = $_SESSION['password'];

	$query = $db->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
	$query->bindParam(":username", $username);
	$query->bindParam(":password", $password);

	//if something went wrong with grabbing the user, destroy the session.
	if(!$query->execute())
	{
		session_destroy();
		$user->redirect("Your session has expired.", "warning", "login.php");
	}

	//if no user has been found (due to password change), log the current user out.
	if(!$query->rowCount())
	{
		session_destroy();
		$user->redirect("Your session has expired.", "warning", "login.php");
	}

	//if everything's ok, update the user table with new data from the db.
	$user->table = $query->fetch(PDO::FETCH_ASSOC);


}