<?php
require_once('config.php');

if (isset($_POST['Send'])) {
    $over = 0;
    $targetUsername = $_POST["userIn"];
    $targetPassword = $_POST["passIn"];
    if (strlen($targetUsername) > 0) {
        $over = 1;
    }
    if (strlen($targetPassword) > 0) {
        $over = 2;
    }

    if ($over == 2)
        CheckCredentials($targetUsername, $targetPassword, $servername, $serverUsername, $serverPassword, $dbname);
    else {
        header("Location:logIn.php?r=fields_empty");
        die();
    }
}

// A function to log to the browser's console. Not in use anymore, but it was handy for debugging earlier in the past.
function console_log($output, $with_script_tags = true)
{
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
        ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}

// Used to generate the token
function random_string($length)
{
    $str = random_bytes($length);
    $str = base64_encode($str);
    $str = str_replace(["+", "/", "="], "", $str);
    $str = substr($str, 0, $length);
    return $str;
}

// Try to log in. Compares username to accounts in server to see if such account even exists. Then proceeds to compare passwords by hashing the user inputted one and then compares it to the already-hashed-one in the server.
function CheckCredentials($user, $pass, $targetServer, $serverUser, $serverPass, $serverDb)
{
    // Make a connection
    $connection = connect($targetServer, $serverUser, $serverPass, $serverDb);
    $hashedPass = hash("sha256", $pass);
    // Make a query

    $sql = "SELECT id, userName, password, role, userStatus, statusReason, token from users";
    $result = $connection->query($sql);


    $accountFound = false;

    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            if (strcasecmp($row["userName"], $user) == 0) {
                // Add Token
                if ($hashedPass == $row["password"]) {
                    // User entered correct password :)

                    if(strcasecmp($row['userStatus'], 'cooldown') == 0)
                    {
                        header("Location:logIn.php?r=on_cooldown&br={$row['statusReason']}");
                        die();
                    }
                    else if(strcasecmp($row['userStatus'], 'ban') == 0)
                    {
                        header("Location:logIn.php?r=user_banned&br={$row['statusReason']}");
                        die();
                    }
                    else
                    {
                        token($row, $user, $connection);
                        $accountFound = true;
                        break; // Exit the loop since account is found
                    }
                    
                } else {
                    header("Location:logIn.php?r=pass_invalid");
                    die();
                }
            }
        }

        // Check if account was not found
        if (!$accountFound) {
            header("Location: logIn.php?r=account_notfound");
            die();
        }
    }
    else
    {
        header("Location: logIn.php?r=zero_accounts");
        die();
    }

}

// Updates the token
function token($row, $user, $connection)
{
    session_start();
    $ID = $row["id"];
    $NAME = $row["userName"];
    $ROLE = $row["role"];
    $REALNAME  = "";

    $sql = "SELECT * from customors where id = '$ID'";
    $result = $connection->query($sql);
    $row = $result->fetch_assoc();
    $REALNAME = $row['name'];

    $token_length = 50;
    $_TOKEN = random_string($token_length);
    $sql = "UPDATE users SET Token='$_TOKEN' WHERE ID='$ID'";

    $connection->query($sql);
    $_SESSION['Token'] = $_TOKEN;
    $_SESSION['User'] = $NAME;
    $_SESSION['Role'] = $ROLE;
    $_SESSION['RealName'] = $REALNAME;
    header("Location:main.php");
    $connection->close();
    die();
}

function connect($servername, $username, $password, $dbname)
{
    // Connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        console_log("failed" . $conn->connect_error);
        die();
    }
    console_log("connected to the server succesfully.");
    return $conn;
}

?>