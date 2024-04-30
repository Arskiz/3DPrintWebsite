<?php

if (!isset($_GET['f'])) {
    // If not, show an error message and exit
    die("File parameter 'f' is missing");
}

$file = $_GET['f'];

// Set the appropriate content type and disposition headers
header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=" . basename($file));

// Read and output the file contents
readfile($file);
exit();
?>
