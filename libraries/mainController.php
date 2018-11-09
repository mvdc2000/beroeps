<?php 

//enable error reporting

error_reporting(E_ALL);
ini_set('display_errors', 1);


//include everything in the project.
require("libraries/databaseController.php");
require("libraries/functionController.php");

//create the user class.
$user = new userController();

require("libraries/sessionController.php");
