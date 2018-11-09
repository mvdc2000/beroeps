<?php

$sql_username = "db-toets";
$sql_password = "toets123";

try 
{
    $db = new PDO('mysql:host=localhost;dbname=toets-site', $sql_username, $sql_password);
} 
catch (PDOException $e) 
{
    print "Whooooops! I have received the following errors! <br><hr>" . $e->getMessage() . "<br/>";
    die();
}
