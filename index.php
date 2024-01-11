<?php
	require_once 'functions/dbFunctions.php';
	require_once 'Errors/signupError.php';
	
	if(isset($_POST['login'])) {
	extract($_POST);
	$user = trim($_POST['username']);
	$passw = trim($_POST['password']);
		if(checkUserLogin($user,$passw)) {
			session_start();

			$_SESSION['User'] = $user;
			$id = getUserID($user);

			$_SESSION['ID'] = $id[0];
			$_SESSION['Email'] = $id[1];

			redirect("Dashboard/dashboard.php#first");
		} else flash("register","Invalid Username or password");
	}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <!---Custom CSS File--->
    <link rel="stylesheet" href="CSS/AuthStyle.css" />
  </head>
  <body>
    <section class="container">
<header>Log In</header>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="form" method="post">
	<?php flash('register'); ?>
	<!--Email input-->
	<div class="input-box">
	<label>Username or Email</label>
	<input type="text" name="username" placeholder="Enter username or Email" required />
	</div>

	<!--Password input-->
	<div class="input-box">
	<label>Password</label>
	<input type="password" name="password" placeholder="Enter password" required />
	</div>
	<button name="login" >Log In</button>
	<p class="return">Don't have an account?<a href="register.php"> Sign Up.</a></p>
</form>
</section>
  </body>
</html>  