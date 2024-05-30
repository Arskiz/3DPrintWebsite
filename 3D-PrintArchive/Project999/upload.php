<?php
session_start();

$_REASON;
if (isset($_GET['r'])) {
    $_REASON = $_GET['r'];
} else {
    $_REASON = "";
}

// Not logged in? => redirect back as the website's upload system is made for registered and logged in users ONLY.
if (!isset($_SESSION['Token']) && $_SESSION['Token'] == null) {
    header('Location: logIn.php?r=signIn_required');
}

// Handle errors passed with the URL using the r-variable.
function HandleErrors($_REASON_H)
{
    switch ($_REASON_H) {
        case 'field_error':
            return "At least one of the fields is empty!";
        default:
            return '';
    }
}
?>
<!DOCTYPE html>
<html id="html" lang="en">

<head>
    <link rel="stylesheet" href="assets/style-shared.css">
    <link rel="stylesheet" href="assets/style-logIn.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>Upload - Archive of 3D-Prints</title>
</head>

<body id="body">
    <canvas id="canvas"></canvas>
    <header id="header">
        <!-- Left side of the header -->
        <div id="HeaderLeft">
            <div style="margin-left: 15px" class="Hoverable" onclick="redirect(0)" title="Redirect to Mercantec school website"><img
                    src="assets/icons/mercantec-logo-white.png" alt="Mercantec Logo" width="170px"></div>

            <!--<p class="Titles">3D-Print archive</p>-->
        </div>

        <!-- Right side of the header -->
        <div id="HeaderRight">
            <div id="HeaderRightNonHamburger">
                <div class="Hoverable OtherBtn" style="margin-right: 20px" id="BackButton" onclick="redirect(2)">
                    <h1 class="nonSelectable HeaderElementText">To Archive</h1>
                    <img src="assets/icons/arrow-right.png" style="filter: invert(1); transform: rotateZ(-90deg);" class="start" height="20px" id="Exit-Icon" alt="home-icon-btn">
                </div>
                <div class="Hoverable OtherBtn" id="BackButton" onclick="redirect(999)">
                    <h1 class="nonSelectable HeaderElementText">About This
                        Website</h1>
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

    <div id="infoHolder" style="height:100%">
        <form action="upload_file.php" method="post" enctype="multipart/form-data">
            <div id="infoHolderChild1">
                <p id="loginWindowTitle" class="pText nonSelectable"
                    style="color: rgba(255, 255, 255, 0.707); margin-bottom: 5px;">Upload:</p>
                <div class="line"></div>
                <br>
                <p id="fileName" class="logInFormTitle pText nonSelectable" style="margin-bottom: 5px;">File Name:</p>
                <input required title="Type the file name here." style="margin-bottom: 5px;" id="fileNameIn" name="fileNameIn"class="inputBox textAlignCenter" type="text" placeholder="Type here...">
                
                <p id="passWordTitle" class="logInFormTitle pText nonSelectable" style="margin-bottom: 5px;">Material:</p>
                <input required title="Type the used material here." style="margin-bottom: 5px;" id="materialIn" name="materialIn"class="inputBox textAlignCenter" type="text" placeholder="Type here...">
                
                <p id="passWordTitle" class="logInFormTitle pText nonSelectable" style="margin-bottom: 5px;">Color:</p>
                <input required title="Type the color of the item here." style="margin-bottom: 5px;" id="colorIn" name="colorIn" class="inputBox textAlignCenter" type="text" placeholder="Type here...">
                
                <p id="passWordTitle" class="logInFormTitle pText nonSelectable" style="margin-bottom: 5px;">Comments:</p>
                <textarea textarea rows="10" cols="40" title="Type additional comments" style="margin-bottom: 5px; width: 90%; height: 100px;" id="commentsIn" name="commentsIn" class="inputBoxBig textAlignCenter" type="text" placeholder="Type here...">Undefined</textarea>

                
                <p id="passWordTitle" class="logInFormTitle pText nonSelectable" style="margin-bottom: 5px;">File:</p>
                <input required type="file" title="Upload file with this" style="margin-bottom: 5px;" id="fileIn" name="fileIn"class="inputBox textAlignCenter" type="text">

                <!-- <p id="passWordTitle" class="logInFormTitle pText nonSelectable" style="margin-bottom: 5px;">Additional item image:</p>
                <input type="file" title="Upload image file for item" style="margin-bottom: 5px;" id="fileIn" name="imageFileIn" class="inputBox textAlignCenter" type="text"> -->

                <button class="pText Hoverable submitButton" type="submit" name="Send">
                    <p id="logInButtonText" style="margin: auto;" class="pText">
                        Upload
                    </p>
                </button>
        </form>
        <p id="errorHandler" class="pText logInFormText"><?php echo HandleErrors($_REASON); ?></p>
    </div>
    <div id="rc">

    </div>
    </div>

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

        <button class="Hoverable buttonBNR nonSelectable" onclick="redirect(999)">
            <p class="pText white" style="font-size: 30px;">
                About this website
            </p>
        </button>
    </div>

    <footer id="footer">
        <div id="innerFooter">
            <p id="footerText" class="white pText textAlignCenter">
                Copyright © Mercantec, Inc. 2024. All rights reserved.
            </p>
        </div>
    </footer>

</body>
<script src="assets/scripts/js.js"></script>
<script src="assets/scripts/canvas.js"></script>
<script src="assets/scripts/credentials_handler.js"></script>
</html>
<!-- Copyright© Aron Särkioja to Mercantec, Inc. 2024. All rights reserved. -->