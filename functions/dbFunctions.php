<?php
require_once 'functionHelper.php';

function checkUserLogin($usr, $pass) {
    $mysqli = new mysqli("localhost","root","","hms");
    if($mysqli->connect_errno){
      include("dbError.php");
      exit;
    }
    $stmt = $mysqli->prepare("SELECT Username, Password FROM Users WHERE Username = ? OR Email = ? ");
    $stmt->bind_param("ss",$usr,$usr);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_row();

    $stmt->close();
    $mysqli->close();
    if(!$res) return 0;
    if(password_verify($pass,$res[1])) {
        return 1;
    } else return 0;    
}

function addUserTransaction($data) {
    if(checkValid($data)) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $mysqli = new mysqli("localhost","root","","hms");
        if($mysqli->connect_errno){
        include("dbError.php");
        exit;
        }

        mysqli_begin_transaction($mysqli);

        try {
            //turning auto-commit off to allow potential rollback
            $mysqli->autocommit(FALSE);

            //inserting into the Users table
            $data['usersPwd'] = password_hash($data['usersPwd'], PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("INSERT INTO users ( Username, Email, Password, Gender ) VALUES ( ?, ?, ?, ? )");
            $stmt->bind_param("ssss",$data['usersName'],$data['usersEmail'],$data['usersPwd'],$data['usersGender']);
            $stmt->execute();

            // getting the id of the new user from the users table 
            $stmtu = $mysqli->prepare("SELECT ID FROM users WHERE Username = ?");
            $stmtu->bind_param("s",$data['usersName']);
            $stmtu->execute();
            $res = $stmtu->get_result()->fetch_assoc();


            $userid = $res['ID'];
            // getting the id of the new user from the users table to insert into the healthinfo table
            

            //Calculating bmi and daily maintenance calories to enter into the table
            $dailyCI = calcDailyCalories($data['usersWeight'], $data['usersHeight'], $data['usersGender']);
            $bmi = calcBMI($data['usersWeight'], $data['usersHeight'], $data['usersGender']);

            //using the obtained id to insert health information into health info
            $stmt2 = $mysqli->prepare("INSERT INTO healthinfo (userID, weight, height, dob, dailyCI, BMI ) VALUES (?, ?, ?, ?, ?, ?)");

            //bindng the params and exec the query
            $stmt2->bind_param("iddsid",$userid,$data['usersWeight'],$data['usersHeight'],$data['usersDoB'],$dailyCI,$bmi);
            $stmt2->execute();
            
            //adding the initial weight to the weight table
            $date = date('Y-m-d');
            $stmt3 = $mysqli->prepare("INSERT INTO userweight VALUES (?, ?, ?)");
            $stmt3->bind_param("ids",$userid,$data['usersWeight'],$date);
            $stmt3->execute();
            
            //adding an initial record of value 0 for the graph plotting 
            $stmt4 = $mysqli->prepare("INSERT INTO calories VALUES (?, 0, ?)");
            $stmt4->bind_param("is",$userid,$date);
            $stmt4->execute();

            //commit and set autocommit to on 
            $mysqli->autocommit(true);

        } catch (mysqli_sql_exception $exception) {
            //rollback incase an error occurs
            mysqli_rollback($mysqli);
            throw $exception;
        }
    } else redirect("../HMS/register.php");

}

function changeAuthInfo ($type,$new,$id) {
    
    $mysqli = new mysqli("localhost","root","","hms");
    if($mysqli->connect_errno) {
        include("dbError.php");
        exit;
    }
    
    //using the $type variable to dtermine which table is bieng inserted into, if it is of type password we hash the password first
    if($type == 'Password') $new = password_hash($new, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("UPDATE users SET ".$type." = ? WHERE ID = ?");
    $stmt->bind_param("si",  $new, $id);
    $stmt->execute();

    //closing all cons
    $stmt->close();
    $mysqli->close();
    
}


function getUserID($username) {
    $mysqli = new mysqli("localhost","root","","hms");
        if($mysqli->connect_errno) {
        include("dbError.php");
        exit;
        }
    // getting the id of the new user from the users table 
    $stmt = $mysqli->prepare("SELECT ID, Email FROM users WHERE Username = ?");
    $stmt->bind_param("s",$username);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_row();

    $stmt->close();
    $mysqli->close();

    return $res;
}

function getProfileInfo($id) {
    $mysqli = new mysqli("localhost","root","","hms");
        if($mysqli->connect_errno) {
        include("dbError.php");
        exit;
        }

    //using the obtained id to insert health information into health info
    $stmt = $mysqli->prepare("SELECT BMI, dailyCI FROM healthinfo WHERE userID = ?");

    //bindng the params and exec the query
    $stmt->bind_param("i",$id);
    $stmt->execute();

    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $info = [
        'BMI' => $res['BMI'],
        'dailyCI' => $res['dailyCI']
    ];

    //getting the weight of the user and ordering by increasing order, the limiting the output to 1 such that we can get the first entry of that user in the table (the farthest date to be compared with the latest)
    $stmt = $mysqli->prepare("SELECT weight, date FROM userweight WHERE id = ? ORDER BY date ASC LIMIT 1");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $info['weight'] = $res['weight'];
    $info['date'] = $res['date'];

    $mysqli->close();
    return $info;
}

function findUserByEmailOrUsername($email, $usr) {
    $mysqli = new mysqli("localhost","root","","hms");
        if($mysqli->connect_errno) {
        include("dbError.php");
        exit;
        }
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE Username = ? OR Email = ?");
    $stmt->bind_param("ss",$user,$email);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_all();
    $stmt->close();
    $mysqli->close();
    if($res) {
        return 1;
    } else return 0;
}

function addNewWeight($input) {

    $mysqli = new mysqli("localhost","root","","hms");
        if($mysqli->connect_errno) {
        include("dbError.php");
        exit;
        }
    
    //Checking if a measurement of the same date exists in the database already 
    $stmtw = $mysqli->prepare("SELECT date FROM userweight WHERE date = ? AND id = ?");
    $stmtw->bind_param("si",$input['date'],$input['id']);
    $stmtw->execute();

    $res = $stmtw->get_result()->fetch_assoc();
    
    
    $stmtw->close();
    if($res['date'] == $input['date']) {
        //there exists an entry for that date, so we update instead of inserting a new entry
        $stmtu = $mysqli->prepare("UPDATE userweight SET weight = ? WHERE date = ? AND id = ?");
        $stmtu->bind_param("dsi",$input["weight"],$input['date'],$input['id']);
        $stmtu->execute();

        $stmtu->close();
        $mysqli->close();
    } else {
        //if there are no record in that date we add one
        $stmt = $mysqli->prepare("INSERT 
        INTO userweight
        VALUES (?, ?, ?)");
        $stmt->bind_param("ids",$input['id'],$input['weight'],$input['date']);
        $stmt->execute();

        $stmt->close();
        $mysqli->close();        
    }
}


function addNewMeal($input) {
    $mysqli = new mysqli("localhost","root","","hms");
        if($mysqli->connect_errno) {
        include("dbError.php");
        exit;
        }

    //Cheking if a measurement of the same date exists in the database already 
    $stmtw = $mysqli->prepare("SELECT * FROM calories WHERE date = ? AND user_id = ?");
    $stmtw->bind_param("si",$input['date'],$input['id']);
    $stmtw->execute();

    $res = $stmtw->get_result()->fetch_assoc();

    
    $stmtw->close();
    if($res) {
        //there exists an entry for that date, so we update instead of inserting a new entry
        $stmtu = $mysqli->prepare("UPDATE calories SET calories = calories + ? WHERE date = ? AND user_id = ?");
        $stmtu->bind_param("isi",$input["calories"],$input['date'],$input['id']);
        $stmtu->execute();

        $stmtu->close();
        $mysqli->close();
    } else {
        //if there are no record in that date we add one
        $stmt = $mysqli->prepare("INSERT 
        INTO calories
        VALUES (?, ?, ?)");
        $stmt->bind_param("ids",$input['id'],$input['calories'],$input['date']);
        $stmt->execute();

        $stmt->close();
        $mysqli->close();        
    }
}



function addNewEvent($input) {
    $mysqli = new mysqli("localhost","root","","hms");
        if($mysqli->connect_errno) {
        include("dbError.php");
        exit;
        }

    $stmt = $mysqli->prepare("INSERT INTO events (event_id, user_id, event_name, date, event_desc) VALUES (null, ?, ?, ?, ?)");

    $stmt->bind_param("isss",$input['id'],$input['name'],$input['date'],$input['desc']);
    
    $stmt->execute();

    $stmt->close();
    $mysqli->close();
}

function getUserEvents($id) {
    $mysqli = new mysqli("localhost","root","","hms");
    if($mysqli->connect_errno){
      include("dbError.php");
      exit;
    }
    $stmt = $mysqli->prepare("SELECT 
        event_id, 
        event_name, 
        date, 
        event_desc
    FROM events
    WHERE user_id = ?
    ORDER BY date
    ");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_all();
    $stmt->close();
    $mysqli->close();

    return $res;
}

function getUserWeight($id) {
    //getting all the weight entries of a user from the database
	$mysqli = new mysqli("localhost","root","","hms");
    if($mysqli->connect_errno){
      include("dbError.php");
      exit;
    }
	$stmt = $mysqli->prepare("SELECT 
		weight, date
	FROM userweight
	WHERE id = ?
	ORDER BY date
	");
	$stmt->bind_param("i",$id);
	$stmt->execute();
	$res = $stmt->get_result();
	
	//inputing the weights into arrays to be used as data in the graph plotting 
	while($data = $res->fetch_assoc())
	{   
		$wData['weight'][] = $data['weight'];
		$wData['date'][] = $data['date'];
	}
	$stmt->close();
	$mysqli->close();

    return $wData;
}

function getUserCalories($id) {
    //getting all the weight entries of a user from the database
	$mysqli = new mysqli("localhost","root","","hms");
    if($mysqli->connect_errno){
      include("dbError.php");
      exit;
    }
	$stmt = $mysqli->prepare("SELECT 
		calories, date
	FROM calories
	WHERE user_id = ?
	ORDER BY date
	");
	$stmt->bind_param("i",$id);
	$stmt->execute();

    
	$res = $stmt->get_result();
	
	//inputing the weights into arrays to be used as data in the graph plotting 
	while($data = $res->fetch_assoc())
	{   
		$cData['calories'][] = $data['calories'];
		$cData['date'][] = $data['date'];
	}
	$stmt->close();
	$mysqli->close();

    return $cData;
}

function deleteEvent($id) {
    $mysqli = new mysqli("localhost","root","","hms");
    if($mysqli->connect_errno){
      include("dbError.php");
      exit;
    }
    $stmt = $mysqli->prepare("DELETE FROM events WHERE event_id = ?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    
    $stmt->close();
    $mysqli->close();
}

function getLastWeight($id) {
    $mysqli = new mysqli("localhost","root","","hms");
    if($mysqli->connect_errno){
      include("dbError.php");
      exit;
    }
    //getting the last weight entered by the user
    $stmt = $mysqli->prepare("SELECT weight FROM userweight WHERE id = ? ORDER BY date DESC LIMIT 1");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $mysqli->close();

    //can't be null since the user enters their weight when they register which is then inserted into the userweight table when they are added to the database
    return $res['weight'];
}

function getLastCalories($id) {
    $today = date("Y-m-d");
    $mysqli = new mysqli("localhost","root","","hms");
    if($mysqli->connect_errno){
      include("dbError.php");
      exit;
    }
    //getting the last calorie bvalue the user had that day
    $stmt = $mysqli->prepare("SELECT calories FROM calories WHERE user_id = ? AND date = ? ORDER BY date DESC LIMIT 1");
    $stmt->bind_param("is",$id, $today);
    $stmt->execute();
    
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $mysqli->close();
    //if res['calories'] is null then the user hasn't entered any value today and we will return 0
    if($res) return $res['calories'];
        else return 0;
    
}