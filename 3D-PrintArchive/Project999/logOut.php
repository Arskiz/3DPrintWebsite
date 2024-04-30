<?php
session_start();


// Connection
$conn = new mysqli("localhost", "root", "", "three_d");

if ($conn->connect_error) {
    die();
}
$_TOKEN = $_SESSION['Token'];
$sql = "SELECT * from users where Token = '$_TOKEN'";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()){
    $row['Token'] = NULL;
}

$conn->close();
session_destroy();
header("Location: main.php");
die();
?>