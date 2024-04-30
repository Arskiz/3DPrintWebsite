<?php
session_start();
class User
{
    public $token;
    public $name;
    public $userType;
}
$_NOT_LOGGED_IN = "Not Signed In";
$user = new User();

$user->name = isset($_SESSION['User']) ? $_SESSION['User'] : 'Visitor';
$user->token = isset($_SESSION['Token']) ? $_SESSION['Token'] : '';
$user->userType = isset($_SESSION['Role']) ? $_SESSION['Role'] : 'Visitor';

function statusColor($target, $method)
{
    switch ($method) {
        case 0:
            if (!empty($target) && $target != 'Visitor') {
                    return "green";
                } else {
                    return "red";
                }

        case 1:
            $green = "Regular";
            $red = "Visitor";
            if (!empty($target)) {
                if ($target != $green && $target != $red) {
                    return "orange";
                } else if ($target == $green) {
                    return "green";
                } else if ($target == $red) {
                    return "red";
                }

            } else {
                return "red";
            }
            break;
    }


}

function getUser()
{
    global $user;
    return $user->name ?: "Visitor";
}

function getUserType()
{
    global $user;
    global $_NOT_LOGGED_IN;
    return $user->userType ?: $_NOT_LOGGED_IN;
}

function getLoggedInStatus()
{
    global $user;
    global $_NOT_LOGGED_IN;
    return getUser() != $_NOT_LOGGED_IN && getUser() != "Visitor";
}

function getToken($type)
{
    global $user;
    global $_NOT_LOGGED_IN;
    $token = $user->token;
    switch ($type) {
        case 0:
            return $token ? substr($token, 0, 10) . "..." : $_NOT_LOGGED_IN;
        case 1:
            return $token;
        default:
            return "Error occurred.";
    }
}

function getData($method)
{
    $server = "192.168.116.229";
    $username = "visitor";
    $password = "userVisitor+";
    $database = "Three_D";

    $conn = mysqli_connect($server, $username, $password, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($method == 0) {
        loadItems($conn);
    }
}

function loadItems($conn)
{
    $_ITEMINFO_QUERY = "SELECT * from prints_job WHERE id IS NOT NULL";
    $_ITEMINFO = $conn->query($_ITEMINFO_QUERY);
    if ($_ITEMINFO) {
        while ($row = $_ITEMINFO->fetch_assoc()) {
            $ID = $row['ID'];
            $NAME = $row['name'];
            $CUSTOMER_ID = $row['FK_CustomorsID'];
            $MATERIAL = $row['material'];
            $COLOR = $row['color'];
            $COMMENTS = $row['Comments'];
            $File = $row['Hash'] . '.' . $row['fileType'];

            echo "
            <img src=''>
            <div class='printItem itemFont'>
            <br><b>Name:</b> $NAME 
            <br><b>ID:</b> $ID
            <br><b>Customer ID:</b> $CUSTOMER_ID
            <br><b>Material:</b> $MATERIAL
            <br><b>Color:</b> $COLOR
            <br><b>File Name:</b> $File
            <div class='printItemInner'>
            <br><b>Comments:</b> $COMMENTS
            <form action='download_file.php?f=$File' method='post'>
            <button type='submit' class='buttonBNR white Hoverable'>Download $NAME</button>
            </form>
            </div>
            </div>
            ";
        }

    }
}
?>
<!DOCTYPE html>
<html id="html" lang="en">

<head>
    <link rel="stylesheet" href="assets/style-shared.css">
    <link rel="stylesheet" href="assets/style-main.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="module" src="https://pyscript.net/releases/2024.1.1/core.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
    <title>Archive - Archive of 3D-Prints</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Signika+Negative:wght@300..700&display=swap" rel="stylesheet">

</head>

<body id="body">
    <header id="header">
        <!-- Left side of the header -->
        <div id="HeaderLeft">
            <div title="Redirect to Mercantec school website" style="margin-left: 15px" class="Hoverable"
                onclick="redirect(0)"><img src="assets/icons/mercantec-logo-white.png" alt="Mercantec Logo"
                    width="170px"></div>
        </div>

        <p id="centerHeaderText" class="white Titles"><?php echo "Hello, " . getUser() . "!"; ?></p>

        <!-- Right side of the header -->
        <div id="HeaderRight">
            <div id="HeaderRightNonHamburger">
                <div class="Hoverable" style="margin-right: 20px;" id="LogOutBtn">
                    <p id="headerRightLoggedUsedName" class="nonSelectable HeaderElementText">User</p>
                    <img src="assets/icons/character.png" class="start"
                        style="transform: rotate(360deg); filter:invert(100%); user-select: none;" height="20px"
                        id="Character-Icon" alt="Character-Icon-Png">
                </div>

                <div class="Hoverable" id="LogOutBtn" onclick="redirect(999)">
                    <p class="nonSelectable HeaderElementText">About This Website</p>
                    <img src="assets/icons/exit-icon-white.png" class="start" height="20px" id="Exit-Icon"
                        alt="home-icon-btn">
                </div>
            </div>
            <div id="HeaderRightHamburger" class="Hoverable">
                <img id="HamburgerBTNImg" src="../Project999/assets/icons/hamburger.svg" alt="hamburger-icon"
                    style="filter:invert(100%)" width="50px">
            </div>
        </div>
    </header>

    <div id="archDiv">
        <div id="fR1">
            <!-- Desktop -->

            <div id="fC1" class="flexColumn">
                <?php echo getData(0); ?>
            </div>
        </div>
        <button id="loadMoreBTN" class="buttonBNR Hoverable">
            <p class="white pText">
                Load More
            </p>
        </button>

        <div id="HamburgerContent">
            <div style="margin-top: 5px;">
                <p class="white pText textAlignCenter" style="font-size: 50px;">
                    Menu
                </p>

                <div class="lineLonger" style="margin-bottom: 10px;"></div>
            </div>

            <button class="Hoverable buttonBNR nonSelectable" onclick="redirect(1)" style="margin-bottom: 5px;">
                <p class="pText white" style="font-size: 30px;">
                    Log In
                </p>
            </button>

            <button class="Hoverable buttonBNR nonSelectable" onclick="redirect(999)">
                <p class="pText white" style="font-size: 30px;">
                    About this website
                </p>
            </button>
        </div>

        <div id="accountWindow">
            <p class="windowAccountInfo white pText">
                Account Information
            </p>
            <div class="lineLonger"></div>

            <!-- Username -->
            <div class="flexRow">
                <img src="assets/icons/character.png" class="start"
                    style="transform: rotate(360deg); filter:invert(100%); user-select: none;" height="35px"
                    id="Character-Icon" alt="Character-Icon-Png">
                <p style="font-family: 'Signika Negative';"
                    class="windowAccountInfo <?php echo statusColor($user->name, 0); ?> pText"
                    style="font-size: 45px; user-select: none;">
                    <?php echo getUser(); ?>
                </p>
            </div>

            <!-- Account Type -->
            <div class="flexRow">
                <img src="assets/icons/admin-image.png" class="start"
                    style="transform: rotate(360deg); user-select: none;" height="45px" id="Character-Icon"
                    alt="Character-Icon-Png">
                <p style="font-family: 'Signika Negative';"
                    class="windowAccountInfo <?php echo statusColor($user->userType, 1); ?> pText"
                    style="font-size: 35px; user-select: none;">
                    <?php echo getUserType(); ?>
                </p>
            </div>

            <!-- Token -->
            <div class="flexRow">
                <img src="assets/icons/token-image.png" class="start"
                    style="transform: rotate(360deg); user-select: none;" height="45px" id="Character-Icon"
                    alt="Character-Icon-Png">
                <p style="font-family: 'Signika Negative';"
                    class="windowAccountInfo <?php echo statusColor($user->token, 0); ?> pText"
                    style="font-size: 35px; user-select: none;" <?php if (getLoggedInStatus()) {
                        echo "title='Token: " . getToken(1) . "'";
                    } else {
                        echo "title='Sign In to see your token.'";
                    } ?>>
                    <?php echo getToken(0); ?>
                </p>
            </div>
            <form <?php if (getLoggedInStatus()) {
                echo "action='logOut.php' method='post'";
            } ?>  style="display: flex; justify-content: center;">
                <button class="coolBTN Hoverable" <?php if (!getLoggedInStatus()) {
                    echo "onclick='redirect(1)' type='button'";
                } else {
                    echo "type='submit'";
                } ?>>
                    <p class="white" style="user-select: none; margin: auto; padding-left: 20px; padding-right: 20px; ">
                        <?php if (getLoggedInStatus()) {
                            echo "Sign Out";
                        } else {
                            echo "Sign In";
                        } ?>
                    </p>
                </button>
            </form>
        </div>

        <!-- <footer id="footer">
            <div id="innerFooter">
                <p id="footerText" class="white pText textAlignCenter">
                    Copyright © Mercantec, Inc. 2024. All rights reserved.
                </p>
            </div>
        </footer>
        -->

        <div id="blurOverlay" class="blurCard hidden2"></div>
        <canvas id="canvas"></canvas>
</body>
<script src="assets/scripts/js.js"></script>
<script src="assets/scripts/canvas.js"></script>
<script src="assets/scripts/main.js"></script>

</html>
<!-- Copyright© Aron Särkioja to Mercantec, Inc. 2024. All rights reserved. -->