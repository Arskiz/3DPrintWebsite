<?php
session_start();
require_once ('config.php');
class User
{
    public $token;
    public $name;
    public $realName;
    public $userType;
}

$_NOT_LOGGED_IN = "Not Signed In";
$user = new User();

$user->token = isset($_SESSION['Token']) ? $_SESSION['Token'] : '';

$conn = new mysqli($servername, $serverUsername, $serverPassword, $dbname);
if ($conn->connect_error) {
    die("error");
} else {
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
        } else {
            $type;
            // Log user off because of punishment status
            if ($row["userStatus"] == "ban") {
                $type = "user_banned";
            } else if ($row["userStatus"] == "cooldown") {
                $type = "on_cooldown";
            }

            $reason = $row["statusReason"];

            header("Location: logOut.php?t=$type&r=$reason");
            exit();
        }
    } else {
        // Not signed in.
    }
}

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

function getRealUser()
{
    global $user;
    return $user->realName ?: "Visitor";
}

function getUserType()
{
    global $user;
    global $_NOT_LOGGED_IN;
    return $user->userType ?: $_NOT_LOGGED_IN;
}


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
        case 2: // More advanced
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


function isAdmin($level = 1)
{
    // Assuming GetAdministrator function checks admin level
    return GetAdministrator($level);
}

function buildPrivacyToggle($row)
{
    // Toggle item privacy
    return "<form style='margin-top:15px' class='flexColumn' action='punish_user.php?t=" . ($row['PRIVATE'] == '1' ? "unprivate" : "private") . "&id={$row['ID']}' method='post'>"
        . "<button type='submit' class='buttonBNR white Hoverable' style='background-color: " . ($row['PRIVATE'] == '1' ? "rgba(200,100,100,0.7);" : "rgba(100,200,100,0.7);") . " border: 3px solid black; padding: 10px; padding-left: 20px; padding-right: 20px;'>" . ($row['PRIVATE'] == '1' ? "Unprivate {$row['name']}" : "Make {$row['name']} private") . "</button>"
        . "</form>";
}


?>
<!DOCTYPE html>
<html id="html" lang="en">

<head>
    <link rel="stylesheet" href="assets/style-shared.css">
    <link rel="stylesheet" href="assets/style-main.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        <p id="centerHeaderText" class="white Titles"><?php echo "Hello, " . getRealUser() . "!"; ?></p>

        <!-- Right side of the header -->
        <div id="HeaderRight">
            <div id="HeaderRightNonHamburger">
                <?php if (GetAdministrator(2)) {
                    echo '
                    <div class="Hoverable OtherBtn" style="margin-right: 20px;" onclick="redirect(5)">
                        <p class="nonSelectable HeaderElementText">Admin panel</p>
                        <img src="assets/icons/user-admin-panel.png"
                            style="transform: rotate(0deg); filter:invert(100%); user-select: none;" class="start"
                            height="25px" id="Upload-Icon" alt="upload-icon-btn">
                    </div>
                ';
                } ?>

                <div class="Hoverable" style="margin-right: 20px;" id="UploadBtn" onclick="redirect(4)">
                    <p class="nonSelectable HeaderElementText">Upload</p>
                    <img src="assets/icons/arrow-right.png"
                        style="transform: rotate(-90deg); filter:invert(100%); user-select: none;" class="start"
                        height="20px" id="Upload-Icon" alt="upload-icon-btn">
                </div>

                <div class="Hoverable" style="margin-right: 20px;" id="LogOutBtn">
                    <p id="headerRightLoggedUsedName" class="nonSelectable HeaderElementText">User</p>
                    <img src="assets/icons/character.png" class="start"
                        style="transform: rotate(360deg); filter:invert(100%); user-select: none;" height="20px"
                        id="Character-Icon" alt="Character-Icon-Png">
                </div>

                <div class="Hoverable" id="HomeBtn" onclick="redirect(999)">
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
            <div class="flexColumn" style="width:100%; justify-content: center; align-items:center;">
                <h1 class="pText white">
                    Sort Results By:
                </h1>
                <div class="flexRow" style="justify-content:center; gap:10px">
                    <select onchange="getVal(this, 1)" class="dropDown" name="sortMode" id="sort-select">
                        <option value="id"><p class="pText">Post Id</p></option>
                        <option value="name"><p class="pText">Post Name</p></option>
                        <option value="authorId"><p class="pText">Author Id</p></option>
                        <option value="authorName"><p class="pText">Author Name</p></option>
                    </select>

                    <select onchange="getVal(this, 2)" class="dropDown" name="sortType" id="sort-ascdesc">
                        <option value="asc"><p class="pText">Ascending</p></option>
                        <option value="desc"><p class="pText">Descending</p></option>
                    </select>
                </div>
            </div>
            <div id="fC1" class="flexRow">

            </div>

        </div>
        <div style="display:flex; align-items:center; justify-content:center;">
            <button id="loadMoreBTN" onclick="Fetch()" class="buttonBNR Hoverable">
                <p class="white pText">
                    Load More
                </p>
            </button>
        </div>


        <div id="HamburgerContent" style="display:none">
            <div style="margin-top: 5px;">
                <p class="white pText textAlignCenter" style="font-size: 50px;">
                    Menu
                </p>

                <div class="lineLonger" style="margin-bottom: 10px;"></div>
            </div>

            <?php if (GetAdministrator(2)) {
                echo '<button class="Hoverable buttonBNR nonSelectable" onclick="redirect(5)" style="margin-bottom: 5px;">
                <p class="pText white" style="font-size: 30px;">
                    Admin Panel
                </p>
            </button>';
            } ?>

            <button class="Hoverable buttonBNR nonSelectable" onclick="redirect(4)" style="margin-bottom: 5px;">
                <p class="pText white" style="font-size: 30px;">
                    Upload
                </p>
            </button>

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
                        } ?>
                    </p>
                </button>
            </form>
        </div>

        <footer id="footer">
            <div id="innerFooter">
                <p id="footerText" class="white pText textAlignCenter">
                    Copyright © Mercantec, Inc. 2024. All rights reserved.
                </p>
            </div>
        </footer>

        <div id="blurOverlay" class="blurCard hidden2"></div>
        <canvas id="canvas"></canvas>
</body>
<script src="assets/scripts/js.js"></script>
<script src="assets/scripts/canvas.js"></script>
<script src="assets/scripts/main.js"></script>
<script>
let lastValue = '';
let lastItem = {}; // Store the last fetched item

$(document).ready(function () {
    Fetch();
});

function Fetch() {
    let sort = localStorage.getItem("printSort") ?? "name";
    fetchPost(sort);
}

function fetchPost(sorting) {
    let ascDesc = localStorage.getItem("printType") ?? "asc";
    let idParam = sorting === "id" ? lastItem.ID ?? '' : '';
    let nameParam = sorting === "name" ? encodeURIComponent(lastItem.name ?? '') : '';

    $.ajax({
        url: `fetch-data.php?type=print&id=${idParam}&name=${nameParam}&sort=${sorting}&ascDesc=${ascDesc}`,
        type: "get",
        dataType: 'json',
        success: function (result) {
            if (result.length > 0) {
                result.forEach(function (item) {
                    $("#fC1").append(generateUserCard(item.ID, item.name, item.AUTHOR_ID, item.material, item.color, item.Comments, item.FileName, item.PRIVATE, item.AUTHOR_NAME, item.IMAGE_EXTENSION, item.FILE_CORRECT));
                });
                // Update lastItem to the last element in the result array
                lastItem = result[result.length - 1];
                console.log(result);
            } else {
                alert("No more items found.");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error occurred: " + status + " - Error: " + error);
            alert("No more items found.");
        }
    });
}


function updateLastValue(data, sorting) {
    lastValue = data[data.length - 1][sorting]; // Update based on the field sorted by
    console.log("Updated last value to: " + lastValue);
}

    function generateUserCard(ID, NAME, AUTHOR_ID, MATERIAL, COLOR, COMMENTS, FILENAME, PRIVATE, AUTHOR_NAME, IMAGE_EXTENSION, FILENAMECORRECT) {
        let status = (PRIVATE == "0") ? "Not Private" : "Private";
        let statusColor = "";

        switch (status) {
            case "":
                statusColor = "white";
                break;
            case "ban":
                statusColor = "red";
                break;
            case "cooldown":
                statusColor = "orange";
                break;
            default:
                statusColor = "white";
                break;
        }

        let html = "<div class='otherDiv flexColumn' style='width:400px'>"
            + "<div class='flexRow' style='justify-content:center; margin-bottom:5px;'>"
            + "<p class='pText white' style='margin: 0px 10px'>3D Item -</p>"
            + `<h1 class='pText red' style='margin: 0px 0px'>${NAME}:</h1>`
            + "</div>"

            + "<div class='line' style='height:5px'></div>"

            + "<div class='flexRow' style='justify-content: space-between;'>"
            + "<b class='pText white' style='margin: 0px 10px'>Post ID:</b>"
            + "<p class='pText white' style='margin: 0px 10px'>"
            + `${ID}`
            + "</p>"
            + "</div>"

            + "<div class='flexRow' style='justify-content: space-between;'>"
            + "<b class='pText white' style='margin: 0px 10px'>Author:</b>"
            + "<p class='pText white' style='margin: 0px 10px'>"
            + `${AUTHOR_NAME}`
            + "</p>"
            + "</div>"

            + "<div class='flexRow' style='justify-content: space-between;'>"
            + "<b class='pText white' style='margin: 0px 10px'>Author ID:</b>"
            + `<p class='pText white' style='margin: 0px 10px'>${AUTHOR_ID}</p>`
            + "</div>"

            + "<div class='flexRow' style='justify-content: space-between;'>"
            + "<b class='pText white' style='margin: 0px 10px'>Material:</b>"
            + `<p class='pText white' style='margin: 0px 10px'>${MATERIAL}</p>`

            + "<div class='flexRow' style='justify-content: space-between;'>"
            + "<b class='pText white' style='margin: 0px 10px'>Color:</b>"
            + `<p class='pText white' style='margin: 0px 10px'>${COLOR}</p>`

            + "<div class='flexRow' style='justify-content: space-between;'>"
            + "<b class='pText white' style='margin: 0px 10px'>File Name:</b>"
            + `<p class='pText white' style='margin: 0px 10px'>${FILENAME}</p>`

            + "<div class='flexRow' style='justify-content: space-between;'>"
            + "<div class='flexColumn' style='width:100%'>"
            + "<b class='pText white' style='margin: 0px 10px'>Comments:</b>"
            + `<p class='pText white' style='margin: 0px 10px'>${COMMENTS}</p>`
            + "</div>"
            + `<form action='download_file.php?f=${FILENAME}&n=${FILENAMECORRECT}' method='post' style="margin:auto; margin-top:20px">`
            + `<button type='submit' class='buttonBNR white Hoverable' style='padding: 10px; margin-bottom: -20px'>Download ${NAME}`
            + "</button>"
            + "</form>"
            ;

        return html;
    }

    function capitalizeFirstLetter(word) {
        if (!word) return word; // return the original string if it's empty
        return word.charAt(0).toUpperCase() + word.slice(1);
    }

    
</script>

</html>
<!-- Copyright© Aron Särkioja to Mercantec, Inc. 2024. All rights reserved. -->