<?php
session_start();
require_once('config.php');

// Get the variables passed with the URL.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $filename = $_POST['fileNameIn'] ?? 'Not set';
    $HASH = substr(hash('sha256', $filename), 0, 15);
    $material = $_POST['materialIn'] ?? 'Not set';
    $color = $_POST['colorIn'] ?? 'Not set';
    $comments = $_POST['commentsIn'] ?? 'Not set';
    $originalImageName = basename($_FILES['imageFileIn']['name'])  ?? 'Not set';
    $originalFileName = basename($_FILES['fileIn']['name'])  ?? 'Not set';
    $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);

    $file_target_dir = "../files/"; // Location to the uploaded files is the following: "3D-PrintArchive/files/"
    $uploadOk = 1;
    $error_message = '';

    // Check if file directories exist
    if (!file_exists($file_target_dir)) {
        $uploadOk = 0;
        $error_message = 'Target directory does not exist.';
    }

    // Check if the file is a valid upload
    if (!is_uploaded_file($_FILES["fileIn"]["tmp_name"])) {
        $uploadOk = 0;
        $error_message = 'File was not uploaded via POST.';
    }

    $target_file = $file_target_dir . basename($_FILES["fileIn"]["name"]);

    // Check if file already exists
    if (file_exists($target_file)) {
        $uploadOk = 0;
        $error_message = 'File "' . basename($_FILES["fileIn"]["name"]) . '"' . ' already exists.';
    }

    // Check file size
    if ($_FILES["fileIn"]["size"] > 50000000) {
        $uploadOk = 0;
        $error_message = 'File is too large.';
    }

    $newFileName = $HASH . "." . $fileExtension;
    $targetFilePath = $file_target_dir . $newFileName;

    // Attempt to move the uploaded files
    if ($uploadOk == 0) {
        echo "<div style='width:100%; align-items:center;justify-content:center;display:flex; flex-direction:column; padding-right:20px;'><h1 style='color:red'>ERROR:</h1><h1>Sorry, your file was not uploaded. $error_message</h1></div>";
    } else {
        
        if (move_uploaded_file($_FILES["fileIn"]["tmp_name"], $targetFilePath) ) {
            $conn = new mysqli($servername, $serverUsername, $serverPassword, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $token = $_SESSION['Token'];
            $sql = "SELECT ID FROM users WHERE Token = '$token'";
            $result = $conn->query($sql);
            $userID = $result->fetch_assoc()['ID'];

            $date = date('Y-m-d');

            $getHighestId = "SELECT MAX(id) AS last_id FROM prints_job";
            $getLastId = $conn->query($getHighestId)->fetch_assoc()['last_id'] + 1;
        
            $sql = "INSERT INTO prints_job (id, name, fk_customorsid, material, createdat, updatedat, color, private, copyRight, comments, hash, filetype, imageExtension) VALUES ('$getLastId', '$filename', '$userID', '$material', '$date', '$date', '$color', '0', '0', '$comments', '$HASH', '$fileExtension', 'NULL')";
            $status = $conn->query($sql);

            if ($status) {
                header("Location: main.php");
                exit();
            } else {
                echo "<div style='width:100%; align-items:center;justify-content:center;display:flex; flex-direction:column; padding-right:20px;'><h1 style='color:red'>ERROR:</h1><h1>Sorry, there was an error inserting the data into the database.</h1></div>";
            }

            $conn->close();
        } else {
            echo "<div style='width:100%; align-items:center;justify-content:center;display:flex; flex-direction:column; padding-right:20px;'><h1 style='color:red'>ERROR:</h1><h1>Sorry, there was an error uploading your file. Check permissions and file paths.</h1></div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/style-main.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploading File...</title>
</head>
<body>
    
</body>
</html>
