<?php
require_once ('config.php');

// Get data passed with URL.
$_TYPE;
if (isset($_GET['t'])) {
    $_TYPE = $_GET['t'];
} else {
    $_TYPE = "";
}

$_USER_ID;
if (isset($_GET['u'])) {
    $_USER_ID = $_GET['u'];
} else {
    $_USER_ID = "";
}

$Item_ID;
if(isset($_GET["id"])) {
    $Item_ID = $_GET['id'];
}
else
{
    $Item_ID = "";
}

$_REASON;
if ($_TYPE == "ban") {
    if (isset($_POST['banReason'])) {
        $_REASON = $_POST['banReason'];
    } else {
        $_REASON = "Not given.";
    }
} else if ($_TYPE == "cooldown") {
    if (isset($_POST['cooldownReason'])) {
        $_REASON = $_POST['cooldownReason'];
    } else {
        $_REASON = "Not given.";
    }
}

$_REQUESTER;
if (isset($_GET['r'])) {
    $_REQUESTER = $_GET['r'];
} else {
    $_REQUESTER = "";
}

if($_TYPE == "ban" || $_TYPE == "cooldown")
    punish($_TYPE, $_REQUESTER, $_USER_ID, $_REASON);
else
    SetFilePrivate($_TYPE, $Item_ID);


// A function that makes player either banned or on cooldown. To use this to punish people, call it in forms with passing also argumets: t(type of the ban), u(user_id, the user's id you want to give a punishment to, r(requester, so your own userName))
function punish($type, $requester, $targetUser, $punishmentReason)
{
    global $servername, $serverUsername, $serverPassword, $dbname;
    $conn = connect($servername, $serverUsername, $serverPassword, $dbname);

    $sql = "SELECT * FROM users where userName = '$requester'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        //Check if requester has the proper permissions
        if (strcasecmp($row['role'], 'developer') == 0 || strcasecmp($row['role'], 'admin') == 0 || strcasecmp($row['role'], 'owner') == 0) {

            // Get user with the target's id and ban/cooldown it
            $sql = "UPDATE users SET userStatus = '$type', statusReason = '$punishmentReason' where users.id = '$targetUser'";
            $conn->query($sql);

            // Set banned/cooldowned user's print jobs to private
            $sql = "UPDATE prints_job SET private = '1' where prints_job.FK_CustomorsID = '$targetUser'";
            $conn->query($sql);
            header('Location: main.php');
            die('done');
        } else {
            // No permissions.
            echo "Access denied: No permissions.";
        }
    } else {
        // User not found with such username.
        echo "Task failed: No requester user with such username found.";
    }
}

// Sets all files private by the UserID, ($itemID)
function SetFilePrivate($privateStatus, $itemID)
{
    global $servername, $serverUsername, $serverPassword, $dbname; 
    $conn = connect($servername, $serverUsername, $serverPassword, $dbname);

    switch($privateStatus) {
        case "private":
            $sql = "UPDATE prints_job SET private = '1' WHERE id = '$itemID'";
        break;
        
        case "unprivate":
            $sql = "UPDATE prints_job SET private = '0' WHERE id = '$itemID'";
        break;
    }
    $conn->query($sql);
    header('Location: main.php');
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