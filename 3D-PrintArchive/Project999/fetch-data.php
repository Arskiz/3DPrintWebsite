<?php
require('config.php');

// Get data passed with the URL
$fetchType = filter_input(INPUT_GET, 'type');
$lastValue = filter_input(INPUT_GET, 'id') ?? 0;
$sortBy = filter_input(INPUT_GET, 'sort') ?? 'name';
$sortType = filter_input(INPUT_GET, 'ascDesc') ?? 'asc';
$limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ?? 5;

// Get target string's out of the one's passed with the URL
$formatFetch = [
    "id" => "PostID",
    "name" => "PostName",
    "accId" => "AccID",
    "accName" => "AccName"
];

$sql = "";
$sortDirection = in_array($sortType, ['asc', 'desc']) ? $sortType : 'asc';
$operator = ($sortDirection === "asc") ? ">" : "<";

// Print item queries, PostID sorts items by the id of the post and PostName sorts them by Name
// TODO: Join customors c to the query to get full name of the client. 
$printSortQueries = [
    "PostID" => "SELECT p.ID, p.name, p.FK_CustomorsID as AUTHOR_ID, p.material, p.color, p.Comments, CONCAT(p.Hash, '.', p.fileType) as FileName, CONCAT(p.name, '.', p.fileType) as FILE_CORRECT, p.Hash, p.private as PRIVATE, u.userName as AUTHOR_NAME, u.role as ROLE, p.imageExtension as IMAGE_EXTENSION FROM prints_job p JOIN users u ON p.FK_CustomorsID = u.ID WHERE p.ID $operator ? ORDER BY p.ID $sortDirection LIMIT ?",

    "PostName" => "SELECT p.ID, p.name, p.FK_CustomorsID as AUTHOR_ID, p.material, p.color, p.Comments, CONCAT(p.Hash, '.', p.fileType) as FileName, CONCAT(p.name, '.', p.fileType) as FILE_CORRECT, p.Hash, p.private as PRIVATE, u.userName as AUTHOR_NAME, u.role as ROLE, p.imageExtension as IMAGE_EXTENSION  FROM prints_job p JOIN users u ON p.FK_CustomorsID = u.ID WHERE p.name $operator ? ORDER BY p.name $sortDirection LIMIT ?",
];

// Essentially same as above, but for accounts. Used in admin panel.
$accSortQueries = [
    "AccID" => "SELECT u.id, u.userName, u.applyForModeration, u.role, u.statusReason, u.userStatus, u.dateCreated, c.name as REAL_NAME, c.Email, c.phone from users u JOIN customors c ON c.ID = u.ID where u.ID $operator ? ORDER BY u.ID $sortDirection LIMIT ?",

    "AccName" => "SELECT u.id, u.userName, u.applyForModeration, u.role, u.statusReason, u.userStatus, u.dateCreated, c.name as REAL_NAME, c.Email, c.phone from users u JOIN customors c ON c.ID = u.ID where u.userName $operator ? ORDER BY u.userName $sortDirection LIMIT ?",
];

// Validate the fetch type and get the appropriate SQL query
switch($fetchType){
    case "print":
        if (!array_key_exists($formatFetch[$sortBy], $printSortQueries)) {
            http_response_code(400); // Bad request
            die(json_encode(["error" => "Invalid fetch type."]));
        }
        $sql = $printSortQueries[$formatFetch[$sortBy]];
    break;

    case "acc":
        if (!array_key_exists($formatFetch[$sortBy], $accSortQueries)) {
            http_response_code(400); // Bad request
            die(json_encode(["error" => "Invalid fetch type."]));
        }
        $sql = $accSortQueries[$formatFetch[$sortBy]];
    break;
}


// Make a connection to the server
$conn = connect($servername, $serverUsername, $serverPassword, $dbname);
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    http_response_code(500); // Internal Server Error
    die(json_encode(["error" => $conn->error]));
}

// Bind parameters and execute the statement
$stmt->bind_param('si', $lastValue, $limit);
$stmt->execute();
$result = $stmt->get_result();

header('Content-Type: application/json');
$data = $result->fetch_all(MYSQLI_ASSOC);

// Return the data back to the requester if it is NOT empty
if (empty($data)) {
    echo json_encode(["message" => "No more items available."]);
} else {
    echo json_encode($data);
}

// Connect-function, shorthand for always rewriting the connections.
function connect($servername, $username, $password, $dbname) {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        http_response_code(500); // Internal Server Error
        die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
    }
    return $conn;
}
?>
