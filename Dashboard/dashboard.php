<?php 
	session_start();
	require_once '../functions/functionHelper.php';
	require_once '../Errors/signupError.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoseIt</title>
    <link rel="stylesheet" href="../CSS/Fstyle.css">
</head>
	<body>
    	<nav>
			<a href="#first">Trackers</a>
			<a href="#second">Events</a>
			<a href="#third">Settings</a>
			<a href="#fourth">Log Out</a>
      	</nav>
	
		<div class= 'container'> 
			<section id= 'first'>
				<?php include 'graphs.php'; ?>
				
			</section>

			<section id= 'second'>
				<?php include 'eventScheduler.php'; ?>
			</section>

			<section id= 'third'>
				<?php include 'settings.php'?>
			</section>

			<section id= 'fourth'>
				<?php include 'logout.php'?>
			</section>
		</div>
	</body>
</html>