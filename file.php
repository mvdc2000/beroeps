<?php 

require("libraries/headerContent.php");

$user->redirect_not_logged();
$user->redirect_not_admin();

if(!isset($_GET['file']))
	$user->redirect("An error has occured while trying to fetch file.", "error", "admin.php");

if($_GET['file'] == ".htaccess")
	$user->redirect("An error has occured while trying to fetch file.", "error", "admin.php");

$name = "files/".$_GET['file'];

if(!file_exists($name))
	$user->redirect("An error has occured while trying to fetch file.", "error", "admin.php");

//read files from directory

$file = fopen($name, "r");
$text = fread($file, filesize($name));
fclose($file);

?>

<h2><a href="admin.php">Go back</a> | <?php echo $_GET['file']; ?></h2>

<?php echo "<pre>".$text."</pre>"; ?>

<?php require("libraries/footerContent.php"); ?>