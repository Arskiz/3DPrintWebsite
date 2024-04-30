<?php 
session_start();

$_REASON;
if (isset($_GET['r'])) {
    $_REASON = $_GET['r'];
}
else
{
    $_REASON = "";
}



// Already Logged in? -> redirect
if(isset($_SESSION['Token']) && $_SESSION['Token'] != null)
{
    header('Location: main.php');
}

function HandleErrors($_REASON_H){
    switch($_REASON_H){
        case 'fields_empty':
            return 'At least one field is empty!';
        case 'pass_invalid':
            return 'Invalid password!';
        case 'account_notfound':
            return 'The account does not exist!';

        case "zero_accounts":
            return "There are no accounts in the database!";
        case '':
            return "";

        case 'account_created_success':
            return "Your account was successfully created!";
        default:
            return 'Unknown error detected!';
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
    <script type="module" src="https://pyscript.net/releases/2024.1.1/core.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>Log In - Archive of 3D-Prints</title>
</head>

<body id="body">
    <canvas id="canvas"></canvas>
    <header id="header">
        <!-- Left side of the header -->
        <div id="HeaderLeft">
            <div style="margin-left: 15px" class="Hoverable" onclick="redirect(0)"><img
                    src="assets/icons/mercantec-logo-white.png" alt="Mercantec Logo" width="170px"></div>

            <!--<p class="Titles">3D-Print archive</p>-->
        </div>

        <!-- Right side of the header -->
        <div id="HeaderRight">
            <div id="HeaderRightNonHamburger">
                <div class="Hoverable" id="BackButton" onclick="redirect(999)">
                    <p class="nonSelectable HeaderElementText">About This
                        Website</p>
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
        <form action="database_connection.php" method="post">
            <div id="infoHolderChild1">
                <p id="loginWindowTitle" class="pText nonSelectable"
                    style="color: rgba(255, 255, 255, 0.707); margin-bottom: 5px;">Sign
                    In:</p>
                <div class="line"></div>
                <br>

                <p id="userNameTitle" class="logInFormTitle  pText nonSelectable" style="margin-bottom: 5px;">Username:
                </p>
                <input title="Type your username here." style="margin-bottom: 5px;" id="userIn" name="userIn"
                    class="inputBox textAlignCenter" type="text">
                <p id="passWordTitle" class="logInFormTitle pText nonSelectable" style="margin-bottom: 5px;">Password:
                </p>
                <input title="Type your password here." style="margin-bottom: 5px;" id="passIn" name="passIn"
                    class="inputBox textAlignCenter" type="password">
                <button class="pText Hoverable submitButton" type="submit" name="Send">
                    <p id="logInButtonText" style="margin: auto;" class="pText">
                        Sign In
                    </p>
                </button>
                <p style="margin-left: 20px;margin-top: 5px; margin-right: 20px; font-size: 25px" class="pText white">No account? Register <a class="pText white" style="font-size: 25px" href="register.php">here</a>.</p>
                
        </form>
        <p id="errorHandler" style="<?php if($_REASON == "account_created_success"){ echo'color: rgba(100,255,100,0.9)';} else{echo 'color: rgba(255,100,100,0.9);';}?>" class="pText logInFormText"><?php echo HandleErrors($_REASON); ?></p>
    </div>
    <div id="rc">

    </div>
    </div>

    <div id="HamburgerContent">
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