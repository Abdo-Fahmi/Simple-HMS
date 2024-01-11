<?php
	require_once '../Errors/signupError.php';
	if(isset($_POST['logout'])) {
		session_destroy();
		redirect('../index.php');
	} else if (isset($_POST['cancel'])) {
		redirect('dashboard.php#first');
	}
	?>
<div class="input-box" style="align-self: center;">
	<form action="logout.php" method="post">
	<label>Are you surer you want to log out?</label>
	<br>

	<input class="logout-buttons" style="background-color: #27f264;" type="submit" name="cancel" value="Back to Dashboard"/>
	
	<input class="logout-buttons" type="submit"
	style="background-color: #f22727;" name="logout" value="Log Out"/>
	</form>
</div>