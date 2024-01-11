<?php
	require_once '../functions/dbFunctions.php';
	require_once '../functions/functionHelper.php';

  
  //getting today and yesterday dates for default input filtering by date later
	$today = date('Y-m-d');
	$yesterday = date('Y-m-d',strtotime("-1 days"));
  $userInfo = getProfileInfo($_SESSION['ID']);
  $weightState = getLastWeight($_SESSION['ID']);
  $calorieState = getLastCalories($_SESSION['ID']);

  $weightState = $userInfo['weight'] - $weightState;
  $calorieState = $userInfo['dailyCI'] - $calorieState;

	if(isset($_POST['submitWeight'])):
		$newWeight = [
			'id' => $_SESSION['ID'],
			'weight' => $_POST['newWeight'],
			'date' => $_POST['weightDate']
		];

    
		//calling the function toadd the new weight
		addNewWeight($newWeight);

		//bringing the user back to the graph section
		redirect("dashboard.php#first");
	endif;

  if(isset($_POST['submitMeal'])):
		$newMeal = [
			'id' => $_SESSION['ID'],
			'calories' => $_POST['newMeal'],
			'date' => $_POST['mealDate']
		];

		//calling the function to add the new meal to the db, which will make a new record for a new day, or add it to an existing record if a meal was input that same day.
		addNewMeal($newMeal);

		//bringing the user back to the graph section
		redirect("dashboard.php#first");
	endif;


?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../CSS/Fstyle.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
</head>
<body>

<?php 
  //getting the user's weight data from the database
	$wData = getUserWeight($_SESSION['ID']);

  //getting the user's calorie data fom the database
  $cData = getUserCalories($_SESSION['ID']);

  //getting array size for parsing data to the graph later
  $len = sizeof($cData['date']);
?>
<div id="weight" style="width: 60%;">
  	<!--div for the graph-->
    <div class="graphs">
		<div class="date-filters">
        <label>From:</label>
	   		<input type="date" onchange="startDateFilter(this)" value= "2023-03-02"  min="2023-01-01" max="<?php echo $today;?>">

        <label>To:</label>
	   		<input type="date" onchange="endDateFilter(this)" value= "<?php echo $today?>" min="2023-01-01" max="<?php echo $today;?>">
	    </div>
    	<canvas id="weightChart"></canvas>
	</div>

	<!--div for the weight input form-->
	<div id="weightinput">
		<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="form" method="post">
			<div class="input-box">
			<label>Enter a new weight measurment</label>
      <!--step set to allow decimal input-->
			<input type="number" step="0.01" name="newWeight" required />
			<br>
			<label>Measurement date</label>
      
			<input type="date" name="weightDate" value="<?php echo $today; ?>" max="<?php echo $today;?>" />
			<br>
			<button name="submitWeight">Add</button>
			</div>
		</form>
	</div>
</div>
  <!--div for the calorie input-->
<div class="calories" style="width: 40%;">
  <div class="graphs">
  <div class="dateFilters">
    <button class="filter-button" style="margin-left: 10;" onclick="setDates(this)" value="day">Today</button>
    
    <button class="filter-button" style="margin-left: 10;" onclick="setDates(this)" value="All">Show All</button>
    </div>
    <canvas id="calorieChart"></canvas>
  </div>

  <div id="calorieinput" style= "height: 70%;">
  <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="form" method="post">
			<div class="input-box">
			<label>Enter your meal's calorie value</label>
      <br>
      <!--step set to allow decimal input-->
			<input type="number" step="0.01" name="newMeal" required />
			<br>
			<label>Measurement date</label>
      <br>
			<input type="date" name="mealDate" value="<?php echo $today; ?>" max="<?php echo $today; ?>" required />
			<br>
			<button name="submitMeal">Add</button>
			</div>
		</form>
    <div class="input-box">
    <h4 class="summary">Your BMI is <?php echo $userInfo['BMI']; ?></h4>
    <?php
        if($weightState > 0) {
            echo "<h4 class='summary'>You have lost ".$weightState." KG since ".$userInfo['date']."</h4>";
        } elseif($weightState < 0) {
            echo "<h4 class='summary'>You have gained ".(-1*$weightState)." KG since ".$userInfo['date']."</h4>";
        }

        if($calorieState > 0) {
            echo "<h4 class='summary'>Today you're ".$calorieState." calories below your daily needed calories</h4>";
        } elseif($calorieState < 0) {
            echo "<h4 class='summary'>Today you're ".(-1*$calorieState)." calories over your daily needed calories</h4>";
        }
    ?>
    </div>
  </div>
</div>
  
<script>
  //converting date array into json
  const dates = <?php echo json_encode($wData['date']); ?>;
	const today = <?php echo json_encode($today); ?>;

  //converting the date array into readable format by the charts 
  const dateChart = dates.map((day, index) => {
	let dayj = new Date(day);
	return dayj.setHours(0, 0, 0, 0);
  });

  //Plotting the weight graph
  const ctxw = document.getElementById('weightChart');

  const wchart = new Chart(ctxw, {
    type: 'line',
    data: {
    labels: dateChart,
    datasets: [{
      label: 'Your weight over a set period of time',
      data: <?php echo json_encode($wData['weight']);?>,
      borderWidth: 1,
      backgroundColor: '#44dd58',
      borderColor: '#399c46'
    }]
  },
  options: {
    Animation: false,
    scales: {
	  x: {
			type: 'time',
			time: {
				unit: 'day'
			}
		},
      y: {
        beginAtZero: true
      }
    }
  }
});

  const calorieDataToday = [
    <?php
      echo '{x: Date.parse("'.$cData['date'][$len-1].'"), y: '.$cData['calories'][$len-1].'}'
    ?>
  ];

  const calorieDataAll = [
    <?php
    for($i = 0; $i < $len; $i++ ) {
      echo '{x: Date.parse("'.$cData['date'][$i].'"), y: '.$cData['calories'][$i].'},';
    }
    ?>
  ];
  //Plotting the calorie graph
  const ctxc = document.getElementById('calorieChart');

  const cchart = new Chart(ctxc, {
    type: 'bar',
    data: {
      //labels: dateChart,
      datasets: [{
        label: 'Calorie Intake',
        data: calorieDataToday,
        borderWidth: 1,
        borderColor: '#17704b',
        backgroundColor: '#3bba85'
      }]
    },
    options: {
      scales: {
      x: {
          type: 'time',
          time: {
            unit: 'day'
          }
        },
        y: {
          beginAtZero: true
        }       
      }
    }
  });

  wchart.update();
  cchart.update();
  //Functions for the filters to function and update in real time
  function startDateFilter(date) {
	const startday = new Date(date.value);
	wchart.options.scales.x.min = startday.setHours(0, 0, 0, 0);
	wchart.update();
  }

  function endDateFilter(date) {
	const endday = new Date(date.value);
	wchart.options.scales.x.max = endday.setHours(0, 0, 0, 0);
	wchart.update();
  }

  function setDates(interval) {
    if(interval.value == 'day') {
      cchart.data.datasets[0].data = calorieDataToday;
    } else cchart.data.datasets[0].data = calorieDataAll;
    cchart.update();
  }
</script>
</body>
</html>