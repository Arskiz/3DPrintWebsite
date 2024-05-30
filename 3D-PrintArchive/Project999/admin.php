<?php

// THIS PART OF THE WEBSITE IS UNFINISHED. PLEASE TAKE NOTE, AND EITHER DELETE OR FIX THE FILE. MY TIME RAN OUT DOING THIS. THANKS //
session_start(); // Start the session
require_once ('config.php'); // Include the configuration file for server, which holds properties for the connection itself

// Make an User-class, which holds all the approppriate info about the user
class User
{
    public $token;
    public $name;
    public $realName;
    public $userType;
}

// Make a colors-class for the user colors
class Colors
{
    public $regular = 'green';
    public $visitor = 'red';
    public $admin = 'orange';
}
$colors = new Colors();

$_NOT_LOGGED_IN = "Not Signed In";
$user = new User();

$user->token = isset($_SESSION['Token']) ? $_SESSION['Token'] : null;

if(is_null($user->token)){
    header("Location: logOut.php?t=insufficient_permissions&r=unknown_user");
}
// Connect to the database itself. (gets params from config.php)
$conn = new mysqli($servername, $serverUsername, $serverPassword, $dbname);
// Fail
if ($conn->connect_error) {
    die("error");
}
// On handshake
else {
    $sql = "SELECT * from users where Token = '$user->token'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['userStatus'] != "ban" && $row['userStatus'] != "cooldown") {
            $ID = $row['ID'];
            $_SESSION["userType"] = $row["role"];
            $user->userType = $row["role"];
            $user->name = $row["userName"];

            $sql = "SELECT * from customors where id = '$ID'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $roww = $result->fetch_assoc();
                $user->realName = $roww["name"];
            }

            if (!GetAdministrator(2)) { // Check if the user has sufficient permissions
                header("Location: logOut.php?t=insufficient_permissions");
            }
        } else {
            $type;
            if ($row["userStatus"] == "ban") { // Check if user is banned
                $type = "user_banned";
            } else if ($row["userStatus"] == "cooldown") { // Check if user is on cooldown
                $type = "on_cooldown";
            }

            $reason = $row["statusReason"];

            header("Location: logOut.php?t=$type&r=$reason"); // Redirect to logout page with type and reason if the user is not welcome
            exit();
        }
    }
}

// Get user color based on userType
function statusColor($target, $method)
{
    switch ($method) {
        case 0:
            if (!empty($target)) {
                if ($target != 'Visitor') {
                    return "green";
                } else {
                    return "red";
                }

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

// Get Username
function getUser()
{
    global $user;
    return $user->name ?: "Visitor";
}

// Get the personal name of an user
function getRealUser()
{
    global $user;
    return $user->realName ?: "Visitor";
}

// Get User Type
function getUserType()
{
    global $user;
    global $_NOT_LOGGED_IN;
    return $user->userType ?: $_NOT_LOGGED_IN;
}

// Get admin-status. 1: moderator, 2: >=admin
function GetAdministrator($type = 1)
{
    global $user;
    switch ($type) {
        case 1:
            switch ($user->userType) {
                case "Admin":
                case "Owner":
                case "Developer":
                case "Moderator":
                    return true;
                default:
                    return false;
            }
        case 2:
            switch ($user->userType) {
                case "Admin":
                case "Owner":
                case "Developer":
                    return true;
                default:
                    return false;
            }
    }
}

// Self explanatory
function getLoggedInStatus() // Define a function to check if user is logged in
{
    global $user; // Use the global user object
    global $_NOT_LOGGED_IN; // Use the global not logged in constant
    return getUser() != $_NOT_LOGGED_IN && getUser() != "Visitor"; // Return true if user is logged in, false otherwise
}

// A function to retrieve the token of the user
function getToken($type) // Define a function to get the token
{
    global $user; // Use the global user object
    global $_NOT_LOGGED_IN; // Use the global not logged in constant
    $token = $user->token; // Assign the token
    switch ($type) { // Switch based on the type
        case 0:
            return $token ? substr($token, 0, 10) . "..." : $_NOT_LOGGED_IN; // Return token substring or not logged in constant
        case 1:
            return $token; // Return the full token
        default:
            return "Error occurred."; // Return an error message
    }
}
?>
<!DOCTYPE html>
<html id="html" lang="en">

<head>
    <link rel="stylesheet" href="assets/style-shared.css"> <!-- Link to shared styles -->
    <link rel="stylesheet" href="assets/style-admin.css"> <!-- Link to admin styles -->
    <meta charset="UTF-8"> <!-- Set character encoding to UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Set viewport for responsive design -->
    <link rel="preconnect" href="https://fonts.googleapis.com"> <!-- Preconnect to Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Preconnect to Google Fonts with cross-origin -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet"> <!-- Link to Nunito font -->
    <title>Admin Panel - Archive of 3D-Prints</title> <!-- Set the title of the page -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> <!-- Include jQuery -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Link to Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com"> <!-- Preconnect to Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Preconnect to Google Fonts with cross-origin -->
    <link href="https://fonts.googleapis.com/css2?family=Signika+Negative:wght@300..700&display=swap" rel="stylesheet">
    <!-- Link to Signika Negative font -->

</head>

<body id="body">
    <header id="header">
        <!-- Left side of the header -->
        <div id="HeaderLeft">
            <div title="Redirect to Mercantec school website" style="margin-left: 15px;" class="flexRow Hoverable"
                onclick="redirect(0)">
                <img src="assets/icons/mercantec-logo-white.png" alt="Mercantec Logo" width="170px"
                    style="margin-right:15px"> <!-- Mercantec logo -->
                <div class="betaTag">
                    <p class="nonSelectable betaTagText white">BETA</p> <!-- Beta tag text -->
                </div>
            </div>
        </div>

        <p id="centerHeaderText" class="white Titles"><?php echo "Hello, " . getRealUser() . "!"; ?></p>
        <!-- Display greeting with real user name -->

        <!-- Right side of the header -->
        <div id="HeaderRight">
            <div id="HeaderRightNonHamburger">

                <div class="Hoverable OtherBtn" style="margin-right: 20px;" onclick="redirect(2)">
                    <h1 class="nonSelectable HeaderElementText">To Archive</h1> <!-- Button to redirect to archive -->
                    <img src="assets/icons/arrow-right.png"
                        style="transform: rotate(-90deg); filter:invert(100%); user-select: none;" class="start"
                        height="25px" id="Upload-Icon" alt="upload-icon-btn"> <!-- Arrow icon -->
                </div>

                <div class="Hoverable" style="margin-right: 20px;" id="UploadBtn" onclick="redirect(4)">
                    <h1 class="nonSelectable HeaderElementText">Upload</h1> <!-- Button to redirect to upload -->
                    <img src="assets/icons/arrow-right.png"
                        style="transform: rotate(-90deg); filter:invert(100%); user-select: none;" class="start"
                        height="20px" id="Upload-Icon" alt="upload-icon-btn"> <!-- Arrow icon -->
                </div>

                <div class="Hoverable" style="margin-right: 20px;" id="LogOutBtn">
                    <h1 id="headerRightLoggedUsedName" class="nonSelectable HeaderElementText">User</h1>
                    <!-- Display user name -->
                    <img src="assets/icons/character.png" class="start"
                        style="transform: rotate(360deg); filter:invert(100%); user-select: none;" height="20px"
                        id="Character-Icon" alt="Character-Icon-Png"> <!-- Character icon -->
                </div>

                <div class="Hoverable" id="HomeBtn" onclick="redirect(999)">
                    <h1 class="nonSelectable HeaderElementText">About This Website</h1>
                    <!-- Button to redirect to about page -->
                    <img src="assets/icons/exit-icon-white.png" class="start" height="20px" id="Exit-Icon"
                        alt="home-icon-btn"> <!-- Exit icon -->
                </div>
            </div>
            <div id="HeaderRightHamburger" class="Hoverable">
                <img id="HamburgerBTNImg" src="../Project999/assets/icons/hamburger.svg" alt="hamburger-icon"
                    style="filter:invert(100%)" width="50px"> <!-- Hamburger icon -->
            </div>
        </div>
    </header>
    <div id="fC1" class="flexRow" style="width:100%">
        <h1 class="pText white">
            Sort Results By:
        </h1>
        <div class="flexRow" style="justify-content:center; gap:10px">

            <select onchange="getVal(this, 3)" class="dropDown" name="sortMode" id="admin-select-sort">
                <option value="accName">
                    <p class="pText">User Name</p>
                </option>
                <option value="accId">
                    <p class="pText">User ID</p>
                </option>
            </select>

            <select onchange="getVal(this, 4)" class="dropDown" name="sortType" id="admin-ascdesc">
                <option value="asc">
                    <p class="pText">Ascending</p>
                </option>
                <option value="desc">
                    <p class="pText">Descending</p>
                </option>
            </select>
        </div>
    </div>
    </div>
    <button id="loadMoreBTN" onclick="fetchPost('name')" class="buttonBNR Hoverable">
        <p class="white pText">
            Load More <!-- Load more button -->
        </p>
    </button>

    <div id="HamburgerContent" style="display:none">
        <div style="margin-top: 5px;">
            <p class="white pText textAlignCenter" style="font-size: 50px;">
                Menu <!-- Menu title -->
            </p>

            <div class="lineLonger" style="margin-bottom: 10px;"></div>
        </div>

        <button class="Hoverable buttonBNR nonSelectable" onclick="redirect(2)" style="margin-bottom: 5px;">
            <p class="pText white" style="font-size: 30px;">
                To Archive <!-- Button to redirect to archive -->
            </p>
        </button>

        <button class="Hoverable buttonBNR nonSelectable" onclick="redirect(4)" style="margin-bottom: 5px;">
            <p class="pText white" style="font-size: 30px;">
                Upload <!-- Button to redirect to upload -->
            </p>
        </button>

        <button class="Hoverable buttonBNR nonSelectable" onclick="redirect(1)" style="margin-bottom: 5px;">
            <p class="pText white" style="font-size: 30px;">
                Log In <!-- Button to redirect to login -->
            </p>
        </button>

        <button class="Hoverable buttonBNR nonSelectable" onclick="redirect(999)">
            <p class="pText white" style="font-size: 30px;">
                About this website <!-- Button to redirect to about page -->
            </p>
        </button>
    </div>

    <div id="accountWindow">
        <p class="windowAccountInfo white pText">
            Account Information <!-- Account information title -->
        </p>
        <div class="lineLonger"></div>

        <!-- Username -->
        <div class="flexRow">
            <img src="assets/icons/character.png" class="start"
                style="transform: rotate(360deg); filter:invert(100%); user-select: none;" height="35px"
                id="Character-Icon" alt="Character-Icon-Png"> <!-- Character icon -->
            <p style="font-family: 'Signika Negative';"
                class="windowAccountInfo <?php echo statusColor($user->name, 0); ?> pText"
                style="font-size: 45px; user-select: none;">
                <?php echo getUser(); ?> <!-- Display user name with status color -->
            </p>
        </div>

        <!-- Account Type -->
        <div class="flexRow">
            <img src="assets/icons/admin-image.png" class="start" style="transform: rotate(360deg); user-select: none;"
                height="45px" id="Character-Icon" alt="Character-Icon-Png"> <!-- Admin icon -->
            <p style="font-family: 'Signika Negative';"
                class="windowAccountInfo <?php echo statusColor($user->userType, 1); ?> pText"
                style="font-size: 35px; user-select: none;">
                <?php echo getUserType(); ?> <!-- Display user type with status color -->
            </p>
        </div>

        <!-- Token -->
        <div class="flexRow">
            <img src="assets/icons/token-image.png" class="start" style="transform: rotate(360deg); user-select: none;"
                height="45px" id="Character-Icon" alt="Character-Icon-Png"> <!-- Token icon -->
            <p style="font-family: 'Signika Negative';"
                class="windowAccountInfo <?php echo statusColor($user->token, 0); ?> pText"
                style="font-size: 35px; user-select: none;" <?php if (getLoggedInStatus()) {
                    echo "title='Token: " . getToken(1) . "'";
                } else {
                    echo "title='Sign In to see your token.'";
                } ?>>
                <?php echo getToken(0); ?> <!-- Display token with status color and title -->
            </p>
        </div>
        <form <?php if (getLoggedInStatus()) {
            echo "action='logOut.php' method='post'";
        } ?>   style="display: flex; justify-content: center;">
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
                    } ?> <!-- Display sign in/out button based on logged in status -->
                </p>
            </button>
        </form>
    </div>

    <footer id="footer">
        <div id="innerFooter">
            <p id="footerText" class="white pText textAlignCenter">
                Copyright © Mercantec, Inc. 2024. All rights reserved. <!-- Footer text -->
            </p>
        </div>
    </footer>

    <div id="blurOverlay" class="blurCard hidden2"></div>
    <canvas id="canvas"></canvas>
</body>
<script src="assets/scripts/js.js"></script> <!-- Include JavaScript file -->
<script src="assets/scripts/canvas.js"></script> <!-- Include Canvas JavaScript file -->
<script src="assets/scripts/main.js"></script> <!-- Include main JavaScript file -->
<script>
    let lastValue;
    let amountToFetchAtATime = 20;
    let lastFetchedItem = {};
    let allFetchedItems = [];
    let lastValueParam = null;

    $(document).ready(function () {
        Fetch(1);
    });

    function Fetch(firstTime = 0) {
        let sort = localStorage.getItem("accSort") ?? "accName";
        fetchPost(sort, firstTime);
    }

    function fetchPost(sorting, firstTime = 0) {
        let ascDesc = localStorage.getItem("accType") ?? "asc";

        if (sorting === 'accId' || sorting === 'accName') {
            switch (sorting) {
                case 'accId':
                    if (ascDesc === 'desc') {
                        lastValueParam = (firstTime === 1) ? 9999999999 : allFetchedItems[allFetchedItems.length - 1].ID;
                    } else {
                        lastValueParam = (firstTime === 1) ? -1 : allFetchedItems[allFetchedItems.length - 1].ID;
                    }
                    break;

                case 'accName':
                    if (ascDesc === 'asc') {
                        lastValueParam = (firstTime === 1) ? '!' : lastFetchedItem.userName;
                    } else if (ascDesc === 'desc') {
                        lastValueParam = (firstTime === 1) ? '~' : lastFetchedItem.userName;
                    }
                    break;
            }
        }

        $.ajax({
            url: `fetch-data.php?type=acc&id=${lastValueParam}&sort=${sorting}&ascDesc=${ascDesc}&limit=${amountToFetchAtATime}`,
            type: "get",
            dataType: 'json',
            success: function (result) {
                if (result.length > 0) {
                    result.forEach(function (item) {
                        
                            $("#fC1").append(generateUserCard(item.ID, item.userName, item.REAL_NAME, item.applyForModeration, item.role, item.userStatus, item.statusReason, item.Email, item.phone, item.dateCreated));
                            allFetchedItems.push(item);
                        
                    });
                    lastFetchedItem = result[result.length - 1];
                    lastValue = sorting === "accId" ? lastFetchedItem.ID : lastFetchedItem.userName;
                    console.log(result);
                } else {
                    alert("No more items found.");
                }
            },
            error: function (xhr, status, error) {
                console.error("Error occurred: " + status + "\nError: \n" + error);
                alert("Error occurred.");
            }
        });
    }

    // --- Generate User Card --- ///
    // User cards, used to display various data to the admin panel.
    // Includes UserName(NAME), First and Last Name(REALNAME),
    // Is the user applying to be a moderator?(AFM=ApplyForModeration),
    // Role(Admin,Moderator,Owner,Developer etc...),
    // Status(Ban status / Cooldown status)
    // Status Reason(Reason for the punishment)
    // Email && Phone number (pretty straight forward) 
    function generateUserCard(ID, NAME, REALNAME, AFM, ROLE, STATUS, STATUSREASON, EMAIL, PHONE, DATE_CREATED)
    {
        let CURRENT_USER = localStorage.getItem("user");
        let html = "";

        
        let CreatedDate = (DATE_CREATED === null) ? "Alpha account or unknown date." : DATE_CREATED;
        let CreatedColor = (DATE_CREATED === null) ? "red" : "green";
        const lineBreak = "<div class='adminLine' style='height:5px'></div>"
        const containerEnd = "</div>";
        const infoDiv = `<div style='width:100%;  display:flex; flex-wrap: wrap;'>`;

        html += "<div class='otherDiv flexColumn' style='width:400px; gap:20px'>";
        html += "<div class='flexRow' style='justify-content:center; margin:0'>";
        html += "<p class='pText white' style='margin: 0px 10px'>User -</p>";
        html += `<p class='pText red' style='margin: 0px 0px'>${NAME}:</p>`;
        html += lineBreak;
        html += containerEnd;
        

        const pRealNameTitle = `<p class='pText white' style='margin:0; font-weight:900; text-align:center'>Full Name:</p>`;
        const pRealName = `<div class='infoBox'><p class='pText green' style='margin:0; text-align:left;white-space: break-space;'>${REALNAME}</p></div>`;

        const pEmailTitle = `<p class='pText white' style='margin:0; font-weight:900; text-align:center'>Email Address:</p>`;
        const pEmail = `<div class='infoBox'><p class='pText orange' style='margin:0; text-align:left;white-space: break-space;'>${EMAIL}</p></div>`;

        const pPhoneTitle = `<p class='pText white' style='margin:0; font-weight:900; text-align:center'>Phone Number:</p>`;
        const pPhone = `<div class='infoBox'><p class='pText red' style='margin:0; text-align:left;white-space: break-space;'>${PHONE}</p></div>`;

        const pSmallText = `<p class='pText white' style='font-size:25px;font-weight:900; margin: 0px 0px'>Account Created:</p><p class='pText ${CreatedColor}' style='font-size:25px; margin: 0px 0px'>${CreatedDate}</p>`;

        html +=infoDiv;
            html += pRealNameTitle;
            html += pRealName;
        html +=containerEnd;

        html +=infoDiv;
            html += pEmailTitle;
            html += pEmail;
        html +=containerEnd;

        html +=infoDiv;
            html += pPhoneTitle;
            html += pPhone;
        html +=containerEnd;

        // Banning needs the target id(ID) and the requester(CURRENT_USER) as parameters
        html += `<form action='punish_user.php?t=ban&u=${ID}&r=${CURRENT_USER}' method='post' style="margin:auto;">`;
            html += `<button title="Ban ${REALNAME}" type='submit' class='buttonBNR white Hoverable' style='padding: 10px'>Ban ${REALNAME}`;
            html += "</button>";
        html += "</form>";
        html += pSmallText;
        html +=containerEnd;

        return html;
    }

    function capitalizeFirstLetter(word) {
        if (!word) return word; // return the original string if it's empty
        return word.charAt(0).toUpperCase() + word.slice(1);
    }


</script>

</html>
<!-- Copyright© Aron Särkioja to Mercantec, Inc. 2024. All rights reserved. -->