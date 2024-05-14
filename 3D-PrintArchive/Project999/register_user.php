<?php
require_once('config.php');

if (isset($_POST['Send'])) {
    $over = 0;
    $username = $_POST["userIn"];
    $password = $_POST["passIn"];
    $email = $_POST['emailIn'];
    $realName = $_POST['nameIn'];
    $phoneNumber = $_POST['phoneNumberIn'];

    if (strlen($username) > 0 && strlen($password) > 0 && strlen($email) > 0 && strlen($realName) > 0 && strlen($phoneNumber) > 0) {
        $over = 2;
    }

    if ($over == 2)
        CheckCredentials($username, $password, $email, $realName, $phoneNumber, $servername, $serverUsername, $serverPassword, $dbname);
    else {
        header("Location:register.php?r=field_error");
        die();
    }
}

function CheckCredentials($userName, $passWord, $email, $realname, $phoneNumber, $targetServer, $serverUser, $serverPass, $serverDb)
{
    // Make a connection
    $connection = connect($targetServer, $serverUser, $serverPass, $serverDb);
    $hashedPass = hash("sha256", $passWord);

    // Make a query
    $getHighestId = "SELECT MAX(id) as last_id from users";
    $getLastId = $connection->query($getHighestId)->fetch_assoc()['last_id'] + 1;
    $getUserAmount = "SELECT * FROM users";
    $amountOfUsers = $connection->query($getUserAmount);

    if ($amountOfUsers->num_rows > 0) {
        while ($row = $amountOfUsers->fetch_assoc()) {
            if (strcasecmp($row['userName'], $userName) == 0) 
            {
                // Account name already taken
                header("Location: register.php?r=accountname_mismatch");
                break;
            } else {
                $applying = NULL;
                //Insert values to database (users)
                if(isset($_POST['applyForModeration']))
                {
                    $applying = true;
                } else {
                    $applying = NULL;
                }

                $sql = "INSERT into users (ID,userName,password,role, userStatus, statusReason,Token, applyForModeration) VALUES ('$getLastId','$userName','$hashedPass','Regular', '','',NULL, '$applying')";
                $connection->query($sql);

                //Insert values to database (customers or customors whatever)
                $sql = "INSERT into customors (ID,name,Email,phone) VALUES ('$getLastId', '$realname', '$email', '$phoneNumber')";
                $connection->query($sql);

                // Redirect back
                header("Location: logIn.php?r=account_created_success");
                die();
            }
        }
    }
    else{
        //Insert values to database (users)
        $sql = "INSERT into users (ID,userName,password,role, userStatus, statusReason,Token) VALUES ('$currentUserID','$userName','$hashedPass','Regular', '','',NULL)";
        $connection->query($sql);

        //Insert values to database (customers or customors whatever)
        $sql = "INSERT into customors (ID,name,Email,phone) VALUES ('$currentUserID', '$realname', '$email', '$phoneNumber')";
        $connection->query($sql);

        // Redirect back
        header("Location: logIn.php?r=account_created_success");
    }
}

function connect($servername, $username, $password, $dbname)
{
    // Connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die();
    }
    return $conn;
}
?>

<!-- Copyright© Aron Särkioja to Mercantec, Inc. 2024. All rights reserved. -->