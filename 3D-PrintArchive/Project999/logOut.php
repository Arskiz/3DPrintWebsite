<?php
session_start();
require_once('config.php');

// Connection
$conn = new mysqli($servername, $serverUsername, $serverPassword, $dbname);

if ($conn->connect_error) {
    die();
}

// Get data again from the URL
$TYPE;
$REASON;
if (isset($_GET['t'])) {
    $TYPE = $_GET['t'];
} else {
    $TYPE = "";
}
if (isset($_GET['r'])) {
    $REASON = $_GET['r'];
} else {
    $REASON = "";
}


// Clear token from the current account. (WIP, does not always work.)
$_TOKEN = $_SESSION['Token'];
$sql = "SELECT * from users where Token = '$_TOKEN'";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()){
    $row['Token'] = NULL;
}

$conn->close();
session_destroy();

// Redirect afterwards, else-statement redirects with error codes depicting a punishment.
if($TYPE == "" || $REASON == "")
    header("Location: main.php");
else{
    header("Location: logIn.php?r=$TYPE&br=$REASON");
}
die();
?>