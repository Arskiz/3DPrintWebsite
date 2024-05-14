<?php
require('config.php');

$fetchType = filter_input(INPUT_GET, 'type');
$lastValue = filter_input(INPUT_GET, 'id') ?? '';
$sortBy = filter_input(INPUT_GET, 'sort') ?? 'name';
$sortType = filter_input(INPUT_GET, 'ascDesc') ?? 'asc';

$validSortColumns = [
    'name' => 'p.NAME',
    'id' => 'p.ID',
    'authorId' => 'AUTHOR_ID',
    'authorName' => 'AUTHOR_NAME'
];

$validSortTypes = ['asc', 'desc'];

// Ensure safe sorting parameters
$sortColumn = $validSortColumns[$sortBy] ?? 'p.NAME';
$sortDirection = in_array($sortType, $validSortTypes) ? $sortType : 'asc';

$sqlTemplates = [
    'user' => "SELECT 
                u.ID as AUTHOR_ID, u.userName as AUTHOR_NAME, u.userStatus as USER_STATUS, u.role as ROLE, c.name as FULL_NAME, c.Email as EMAIL, c.phone as PHONE_NUMBER 
                FROM customers c
                JOIN users u ON c.ID = u.ID
                WHERE {$sortColumn} > ? ORDER BY {$sortColumn} {$sortDirection} LIMIT 10",
    'print' => "SELECT 
                p.ID, p.name, p.FK_CustomorsID as AUTHOR_ID, p.material, p.color, p.Comments, 
                CONCAT(p.Hash, '.', p.fileType) as FileName, CONCAT(p.name, '.', p.fileType) as FILE_CORRECT, p.Hash, p.private as PRIVATE, 
                u.userName as AUTHOR_NAME, u.role as ROLE, p.imageExtension as IMAGE_EXTENSION 
                FROM prints_job p
                JOIN users u ON p.FK_CustomorsID = u.ID
                WHERE {$sortColumn} > ? ORDER BY {$sortColumn} {$sortDirection} LIMIT 5"
];

// Fetch SQL based on type or die on invalid type
if (!array_key_exists($fetchType, $sqlTemplates)) {
    http_response_code(400); // Bad request
    die(json_encode(["error" => "Invalid fetch type."]));
}

$sql = $sqlTemplates[$fetchType];

$conn = connect($servername, $serverUsername, $serverPassword, $dbname);
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    http_response_code(500); // Internal Server Error
    die(json_encode(["error" => $conn->error]));
}

$stmt->bind_param('s', $lastValue);
$stmt->execute();
$result = $stmt->get_result();

header('Content-Type: application/json');
$data = $result->fetch_all(MYSQLI_ASSOC);

if (empty($data)) {
    echo json_encode(["message" => "No more items available."]);
} else {
    echo json_encode($data);
}

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
