<?php 
  require_once 'functions/dbFunctions.php';
  require_once 'Errors/signupError.php';

  if(isset($_POST['register'])) {
    $data = [
		'usersName' => trim($_POST['username']),
		'usersEmail' => trim($_POST['email']),
		'usersPwd' => trim($_POST['password']),
		'usersWeight' => trim($_POST['weight']),
		'usersHeight' => trim($_POST['height']),
		'usersDoB' => trim($_POST['dateofbirth']),
    'usersGender' => trim($_POST['gender'])
	];
	addUserTransaction($data);
  redirect('index.php');
  }
?>
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
<header>Sign Up</header>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="form" method="post">
  <?php flash('register'); ?>
  <!--username input--> 
  <div class="input-box">
    <label>Username</label>
    <input type="text" name="username" placeholder="Enter full name" required />
  </div>

  <!--Email input-->
  <div class="input-box">
    <label>Email Address</label>
    <input type="text" name="email" placeholder="Enter email address" required />
  </div>

  <!--Password input-->
  <div class="input-box">
    <label>Password</label>
    <input type="password" name="password" placeholder="Enter password" required />
  </div>

  <!--Weight input-->
  <div class="column">
    <div class="input-box">
      <label>Weight (in kg)</label>
      <input type="number" name="weight" placeholder="Enter your weight" required />
    </div>

  <!--Height input-->
  <div class="input-box">
      <label>Height (in cm)</label>
      <input type="number" name="height" placeholder="Enter your height" required />
    </div>
  </div>

  <!--Birthday input-->
  <div class="input-box">
      <label>Birthday</label>
      <input type="date" min="2023-01-01" max="2024-01-01" name="dateofbirth" placeholder="Enter birth date" required />
  </div>

  <!--Gender input-->
  <div class="gender-box">
    <h3>Gender</h3>
    <div class="gender-option">
      <div class="gender">
        <input type="radio" id="check-male" name="gender" value="m"checked />
        <label for="check-male">male</label>
      </div>
      <div class="gender">
        <input type="radio" id="check-female" name="gender" value="f" />
        <label for="check-female">Female</label>
      </div>
    </div>
  </div>
  <button name="register">Submit</button>
  <p class="return">Already have an account?<a href="index.php"> Log In.</a></p>
</form>
</section>
  </body>
</html>    