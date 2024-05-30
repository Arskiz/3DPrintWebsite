<?php

// The file hash name itself, to get the file from server
if (!isset($_GET['f'])) {
    die("File parameter 'f' is missing");  
}

// Get n-parameter for the file name to download the file as
if (!isset($_GET['n'])) {
    die("File parameter 'n' is missing"); 
}

$file = $_GET['f'];
$downloadName = $_GET['n'];
$filename = basename($file);  // Secure the filename to prevent directory traversal
$path = "../files/" . $filename;  // Construct the secure path

// Check if file exists and is readable to aviod problems
if (!file_exists($path) || !is_readable($path)) {
    die("File $filename does not exist or cannot be read on path $path");  
}

// Set the appropriate headers for the file downloading
header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . $downloadName . "\"");  // Use $downloadName for the downloaded file's name

// Read and output the file contents
readfile($path);
exit();
?>
