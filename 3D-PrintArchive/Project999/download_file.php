<?php

if (!isset($_GET['f'])) {
    die("File parameter 'f' is missing");  // Proper error handling
}

if (!isset($_GET['n'])) {
    die("File parameter 'n' is missing");  // Proper error handling
}

$file = $_GET['f'];
$downloadName = $_GET['n'];
$filename = basename($file);  // Secure the filename to prevent directory traversal
$path = "../files/" . $filename;  // Construct the secure path

if (!file_exists($path) || !is_readable($path)) {
    die("File $filename does not exist or cannot be read on path $path");  // Check if file exists and is readable
}

// Set the appropriate headers
header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . $downloadName . "\"");  // Use $downloadName for the downloaded file name

// Read and output the file contents
readfile($path);  // Use the secure path
exit();
?>
