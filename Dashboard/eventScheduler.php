<?php
	require_once '../functions/dbFunctions.php';
	require_once '../functions/functionHelper.php';

	if(isset($_POST['addEvent'])) {
		$newEvent = [
			'id' => $_SESSION['ID'],
			'name' => trim($_POST['eName']),
			'desc' => trim($_POST['eDesc']),
			'date' => $_POST['eTime']
		];
		
	addNewEvent($newEvent);
	}
	
	if(isset($_GET['del'])) {
		$id = $_GET['del'];
		deleteEvent($id);
	}
?>
<!--form for the user to add a new event-->
<div id="eventform">
    <form action="<?php echo 'dashboard.php#second'; ?>" class ="form" method="post">
		
		<!--event name input-->

		<div class="input-box">
		<h3 id="eventformtitle" style="text-align: center;">New event</h3>
		<label>Event name</label>
		<br>
		<input type="text" name="eName" required />
		<br>

		<!--event description-->
		<label>Description</label>
		<br>
		<textarea rows="8" columns="10" style="resize:none" name="eDesc" 
		maxlength="126" required>
		</textarea>
		<br>

		<!--date input-->
		<label>Time</label>
		<br>
		<input type="datetime-local" name="eTime"/>

		<br>
		<button name="addEvent">Add Event</button>
		</div>
    </form>
</div>
<div id="list">
	<ul style="list-style: none;">
	<?php
		$itemsclass = 'eItems';
		$date = date("'Y-m-d'");
		
		//getting events from the database using the function
		$events = getUserEvents($_SESSION['ID']);
		foreach($events as $data)
		{   
			//button to call the timer function
			echo "
			<li class='".$itemsclass."'>";
			if($data[3]) {
				echo "<h4>".$data[1]."</h4>
				<br>
				<p>".$data[3]."</p>
				<br>";
			} else echo "<h3>".$data[1]."</h3> <br>";
			echo "<p id='".$data[0]."'>".$data[2]."</p>
			<button onclick='timers(".json_encode($data[2]).",".$data[0].")'>Show Time Left</button>
			<a style='color: red'; href='dashboard.php?del=".$data[0]."#second'>delete</a>
			</li>";
		}
		
	?>
	</ul>
</div>
<script>
	function timers(date,id) {
		var count_id = date;
		var countDownDate = new Date(count_id).getTime();
		var x = setInterval(function(){
			var now = new Date().getTime();
			var distance = countDownDate - now;
			var days = Math.floor(distance/(1000 * 60 * 60 * 24));
			var hours = Math.floor((distance%(1000 * 60 * 60 * 24))/(1000 * 60 * 60));
			var minutes = Math.floor((distance%(1000 * 60 * 60))/(1000 * 60));
			var seconds = Math.floor((distance%(1000 * 60))/1000);

			if (distance < 60000*24*60) {
				document.getElementById(id).style.color="red";
				document.getElementById(id).innerHTML = hours + ':' +  minutes + ':' + seconds;
			} else document.getElementById(id).innerHTML = days + 'd ' + hours + ':' +  minutes + ':' + seconds;

			if(distance < 0) {
				clearInterval(x);
				document.getElementById(id).innerHTML = 'Times up!';
			} 
		},1000);
	}
</script>
</body>
</html>