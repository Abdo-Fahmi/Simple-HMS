<?php
require_once 'dbFunctions.php';

function checkValid($data) {
    if(empty($data['usersName']) || empty($data['usersEmail']) || empty($data['usersPwd'])){
        flash("register", "Please fill out all inputs");
        return 0;
    }

    if(!preg_match("/^[a-zA-Z0-9]*$/", $data['usersName'])){
        flash("register", "Invalid Username");
        return 0;
    }

    if(!filter_var($data['usersEmail'], FILTER_VALIDATE_EMAIL)){
        flash("register", "Invalid Email");
        return 0;
    }

    if(strlen($data['usersPwd']) < 6){
        flash("register", "Invalid Password");
        return 0;
    }

    if ($data['usersDoB'] >= date("Y-M-D")) { 
        flash("register", "Invalid Date of Birth");
        return 0;
    } 
    
    if($data['usersWeight'] < 0) {
        flash("register", "Invalid Weight");
        return 0;
    }

    if($data['usersHeight'] < 0) {
        flash("register", "Invalid Height");
        return 0;
    }

    //User with the same email or password already exists
    if(findUserByEmailOrUsername($data['usersEmail'], $data['usersName'])){
        flash("register", "Username or Email already taken");
        return 0;
    }

    return 1;
}

function validateNewUser($newName) {
    if($newName == $_SESSION['User']) {
        flash("register", "You already have that username");
        return 0;
    }
    if(empty($newName)){
        flash("register", "Please fill out all inputs");
        return 0;
    }

    if(!preg_match("/^[a-zA-Z0-9]*$/", $newName)){
        flash("register", "Invalid Username");
        return 0;
    }

    return 1;
}


function validateNewEmail($newEmail) {
    if($newEmail == $_SESSION['User']) {
        flash("register", "You already use that email");
        return 0;
    }
    
    if(empty($newEmail)){
        flash("register", "Please fill out all inputs");
        return 0;
    }

    if(!filter_var($newEmail, FILTER_VALIDATE_EMAIL)){
        flash("register", "Invalid Email");
        return 0;
    }

    return 1;
}

function validateNewPass($usr,$oldPass, $newPass, $newPassR) {
    if($newPass != $newPassR) {
        flash('register', "New passwords aren't the same");
        return 0;
    }
    if(!checkUserLogin($usr,$oldPass)) {
        flash('register', "Incorrect password");
        return 0;
    }

    if(strlen($newPass) < 6){
        flash("register", "Invalid Password");
        return 0;
    }

    return 1;
}

function calcDailyCalories($weight, $height, $gender) {
    $weight *= 2.20462262; //convering to pouds for the equation
    $height *= 0.393700787;
    if($gender = 'm') {
        return 665 + (6.3 * $weight) + (12.9 * $height) - (6.8 * 24);
    } else return 665 + (4.3 * $weight) + (4.7 * $height) - (4.7 * 24);
}

function calcBMI($weight, $height) {
    $height *= 0.01; //converting to meters since input is in cm
    $height *= $height;
    return $weight/$height;
}