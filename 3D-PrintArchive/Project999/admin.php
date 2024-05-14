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

            if (!GetAdministrator(2)) {
                header("Location: logOut.php?t=insufficient_permissions");
                die("insufficient_permissions");
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


?>
<!DOCTYPE html>
<html id="html" lang="en">

<head>
    <link rel="stylesheet" href="assets/style-shared.css">
    <link rel="stylesheet" href="assets/style-admin.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
    <title>Admin Panel - Archive of 3D-Prints</title>
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

                <div class="Hoverable OtherBtn" style="margin-right: 20px;" onclick="redirect(1)">
                    <p class="nonSelectable HeaderElementText">To Archive</p>
                    <img src="assets/icons/arrow-right.png"
                        style="transform: rotate(-90deg); filter:invert(100%); user-select: none;" class="start"
                        height="25px" id="Upload-Icon" alt="upload-icon-btn">
                </div>

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
    <div id="fC1" class="flexRow" style="width:100%">

    </div>
    </div>
    <button id="loadMoreBTN" onclick="fetchPost('name')" class="buttonBNR Hoverable">
        <p class="white pText">
            Load More
        </p>
    </button>

    <div id="HamburgerContent" style="display:none">
        <div style="margin-top: 5px;">
            <p class="white pText textAlignCenter" style="font-size: 50px;">
                Menu
            </p>

            <div class="lineLonger" style="margin-bottom: 10px;"></div>
        </div>

        <button class="Hoverable buttonBNR nonSelectable" onclick="redirect(2)" style="margin-bottom: 5px;">
            <p class="pText white" style="font-size: 30px;">
                To Archive
            </p>
        </button>

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
            <img src="assets/icons/admin-image.png" class="start" style="transform: rotate(360deg); user-select: none;"
                height="45px" id="Character-Icon" alt="Character-Icon-Png">
            <p style="font-family: 'Signika Negative';"
                class="windowAccountInfo <?php echo statusColor($user->userType, 1); ?> pText"
                style="font-size: 35px; user-select: none;">
                <?php echo getUserType(); ?>
            </p>
        </div>

        <!-- Token -->
        <div class="flexRow">
            <img src="assets/icons/token-image.png" class="start" style="transform: rotate(360deg); user-select: none;"
                height="45px" id="Character-Icon" alt="Character-Icon-Png">
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

    $(document).ready(function () {
        fetchPost("name");
    });

    function fetchPost(sorting) {
        $.ajax({
            url: "fetch-data.php?type=user&id=" + encodeURIComponent(lastValue) + "&sort=" + sorting,
            type: "get",
            dataType: 'json',
            success: function (result) {
                if (result.length > 0) {
                    result.forEach(function (user) {
                        $("#fC1").append(generateUserCard(user.ID, user.USER_NAME, user.FULL_NAME, user.EMAIL, user.PHONE_NUMBER, user.ROLE, user.USER_STATUS));
                    });
                    lastValue = result[result.length - 1][sorting === "id" ? "ID" : "name"];
                    console.log(result);
                } else {
                    alert("No more users found.");
                }

            },
            error: function (xhr, status, error) {
                console.error("Error occurred: " + status + " - Error: " + error);
            }
        });
    }

    function generateUserCard(ID, NAME, FULL_NAME, EMAIL, PHONE, ROLE, STATUS) {
        let status = (STATUS == "") ? "Good Condition" : STATUS;
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

        let html = "<div class='otherDiv flexColumn' style='width:600px'>"
            + "<div class='flexRow' style='justify-content:center; margin-bottom:5px;'>"
            + "<p class='pText white' style='margin: 0px 10px'>User</p>"
            + `<h1 class='pText red' style='margin: 0px 0px'>${NAME}:</h1>`
            + "</div>"

            + "<div class='line' style='height:5px'></div>"

            + "<div class='flexRow' style='justify-content: space-between;'>"
            + "<b class='pText white' style='margin: 0px 10px'>User ID:</b>"
            + "<p class='pText white' style='margin: 0px 10px'>"
            + `${ID}`
            + "</p>"
            + "</div>"

            + "<div class='flexRow' style='justify-content: space-between;'>"
            + "<b class='pText white' style='margin: 0px 10px'>First and last name:</b>"
            + "<p class='pText white' style='margin: 0px 10px'>"
            + `${FULL_NAME}`
            + "</p>"
            + "</div>"

            + "<div class='flexRow' style='justify-content: space-between;'>"
            + "<b class='pText white' style='margin: 0px 10px'>Email:</b>"
            + `<p class='pText white' style='margin: 0px 10px'>${EMAIL}</p>`
            + "</div>"

            + "<div class='flexRow' style='justify-content: space-between;'>"
            + "<b class='pText white' style='margin: 0px 10px'>Phone Number:</b>"
            + `<p class='pText white' style='margin: 0px 10px'>${PHONE}</p>`

            + "<div class='flexRow' style='justify-content: space-between;'>"
            + "<b class='pText white' style='margin: 0px 10px'>Role:</b>"
            + `<p class='pText white' style='margin: 0px 10px'>${ROLE}</p>`

            + "<div class='flexRow' style='justify-content: space-between;'>"
            + "<b class='pText white' style='margin: 0px 10px'>Account Status:</b>"
            + `<p class='pText ${statusColor}' style='margin: 0px 10px'>${capitalizeFirstLetter(status)}</p>`;

        return html;
    }

    function capitalizeFirstLetter(word) {
        if (!word) return word; // return the original string if it's empty
        return word.charAt(0).toUpperCase() + word.slice(1);
    }


</script>

</html>
<!-- Copyright© Aron Särkioja to Mercantec, Inc. 2024. All rights reserved. -->