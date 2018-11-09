<?php 

require("libraries/mainController.php");

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

	<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
	
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" href="notifications/dist/css/lobibox.min.css"/>

  	<script src="notifications/dist/js/lobibox.min.js"></script>

  	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

  	<script>
  		
  		//function to show a notification. (later used with php)

  		function show_notification(type, msg)
  		{
  			Lobibox.notify(type, {
  				icon: false,
			    continueDelayOnInactiveTab: true,
			    msg: msg
			});
  		}


  	</script>

  	<title>Lekker Toetsen! | Home</title>

</head>
<body>

<div class="wrapper">
    <!-- Sidebar  -->
    <nav id="sidebar">
		<i class="menu-open" onclick="openmenu()"></i>
        <ul class="components" id="navelements">
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
			<?php 

				//displays the admin page only if you're an admin & logged in.

				if($user->logged() && $user->admin())
				{
					echo '<li><a href="admin.php"><i class="fas fa-shield-alt"></i> Admin</a></li>';
					// echo '<li><a href="questions.php"><i class="fas fa-question"></i> Questions</a></li>';
				}


			?>
			<li><a href="tests.php"><i class="fas fa-book"></i> Tests</a></li>
			<li><a href="settings.php"><i class="fas fa-cogs"></i> Settings</a></li>

			<!-- displays the logout button only if you're logged in -->
			<?php if($user->logged()) { ?>

				<li><a href="?logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>

			<?php } ?>
        </ul>
    </nav>

<div id="content">

	<?php 

	//checks for messages in the session. (if they have been set using the redirect function)
	//if a message has been found, it displays them using the javascript function.

	if(isset($_SESSION['success']))
	{
		echo '

			<script type="text/javascript">show_notification("success", "'.$_SESSION['success'].'");</script>

		';
		unset($_SESSION['success']);
	}
	else if(isset($_SESSION['error']))
	{
		echo '

			<script type="text/javascript">show_notification("error", "'.$_SESSION['error'].'");</script>

		';
		unset($_SESSION['error']);
	}
	else if(isset($_SESSION['warning']))
	{
		echo '

			<script type="text/javascript">show_notification("warning", "'.$_SESSION['warning'].'");</script>

		';
		unset($_SESSION['warning']);
	}

	?>