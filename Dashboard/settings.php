<?php
    require_once '../functions/dbFunctions.php';
    require_once '../functions/functionHelper.php';
    
    if(isset($_POST['changeUser'])):
        //validate the input before changing the original
        if(validateNewUser($_POST['newUser'])):
            //change original info in the db
            changeAuthInfo('Username',$_POST['newUser'],$_SESSION['ID']);

            //change the info in the session to reflect new infor
            $_SESSION['User'] = $_POST['newUser'];
            flash('register','Username has been changed!');

            //redirect with GET request so that the page can be refreshed with out resending input
            header('location: dashboard.php?d=2#third');
        endif;

        elseif(isset($_POST['changeEmail'])):
            if(validateNewEmail($_POST['newEmail'])):
                changeAuthInfo('Email',$_POST['newEmail'],$_SESSION['ID']);
                $_SESSION['Email'] = $_POST['newEmail'];
                flash('register','Email has been changed');
                header('location: dashboard.php?d=2#third');
            endif;

        elseif(isset($_POST['changePass'])):
            if(validateNewPass($_SESSION['User'], $_POST['oldPass'], $_POST['newPass'], $_POST['newPassR'])):
                changeAuthInfo('Password',$_POST['newPass'],$_SESSION['ID']);
                flash('register','Password has been changed');
                header('location: dashboard.php?d=2#third');
            endif;
    endif;
?>

<div id="profileInfo">
    <form action="dashboard.php#third" id="profileForm" method="post" style="width: 100%;">
    <h2 style="text-align: center;">Welcome, <?php echo $_SESSION['User']; ?></h2>

    <?php flash('register'); ?>
    <input type="text" name="newUser" placeholder="<?php echo $_SESSION['User']; ?>" />
    <input class="changeBtn" type="submit" name="changeUser" value="Change"/>
    <br>
    <input type="text" name="newEmail" placeholder="<?php echo $_SESSION['Email']; ?>" />
    <input class="changeBtn" type="submit" name="changeEmail" value="Change"/>
    <br>
    <input type="password" name="oldPass" placeholder="Old password"/>
    <br>
    <input type="password" name="newPass" placeholder="New password"/>
    <br>
    <input type="password" name="newPassR" placeholder="Repeat new password"/>
    <input class="changeBtn" type="submit" name="changePass" value="Change" style="float: none;"/>
</form>
</div>