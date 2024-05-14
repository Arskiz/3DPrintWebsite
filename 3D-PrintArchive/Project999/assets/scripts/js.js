// Copyright© Aron Särkioja to Mercantec, Inc. 2024. All rights reserved.
// State tracking for the signup dropdown visibility
let isSignUpDropdownOpened = false;

// Is canvas animation checked
let CanvasAnimation = false;
// Reference to a dynamically updated text area in the body
var activeBodyTextArea = null;

// Toggles the visibility of the signup dropdown
function toggleSignUpDropdown() {
    const signUpDropdown = $("#signUpDropDown");
    const signUpDropdownContent = $("#signUpDropDownInner");

    // Opens the dropdown, setting necessary UI changes
    function openDropdown() {
        updateArrowDirection(1);
        signUpDropdown.removeClass("hidden2");
        signUpDropdownContent.removeClass("hidden2");
    }

    // Closes the dropdown, resetting UI elements
    function closeDropdown() {
        updateArrowDirection(0);
        signUpDropdownContent.addClass("hidden2");
        signUpDropdown.addClass("hidden2");
    }

    // Manages dropdown state with a minimal delay for smooth transitions
    setTimeout(() => {
        if (isSignUpDropdownOpened) {
            closeDropdown();
        } else {
            openDropdown();
        }
        isSignUpDropdownOpened = !isSignUpDropdownOpened;
    }, 50);
}

$(document).ready(function(){
    $("#bgAnimationBtn").click(function(){
        $("#bgAnimationOption").collapse('toggle'); // toggle collapse
    });
})

// State tracking for the main dropdown menu
let isMainDropdownOpened = false;
let isMainDropdownClickable = true;
const mainDropdownElement = $("#signUpDropDown");
const hamburgerMenuButton = $("#HeaderRightHamburger");
const hamburgerMenuContent = $("#HamburgerContent");
const hamburgerButtonImage = $("#HamburgerBTNImg");

let isHamburgerMenuOpened = false;

// Initializes dropdown handlers on window load
$(window).on("load", function ()
{
    let cV = localStorage.getItem("canvasAnimation");
    switch(cV)
    {
        case null:
            localStorage.setItem("canvasAnimation", "true");
            SetCanvas(true);
        break;
    }


    $("#SignUpDropDownButton").on("click", function () {
        if (!isMainDropdownOpened) {
            if (isMainDropdownClickable) {
                updateArrowDirection(1);
                mainDropdownElement.css("animation", "smoothOpacityIn 0.7s ease-in-out, smoothFromRight 0.7s ease-in-out")
                mainDropdownElement.css("display", "flex");
                isMainDropdownClickable = false;
                setTimeout(() => {
                    isMainDropdownClickable = true;
                }, 500);
            }
        } else {
            if (isMainDropdownClickable) {
                mainDropdownElement.css("animation", "smoothOpacityOut 0.7s ease-in-out, smoothToRight 0.7s ease-in-out")
                isMainDropdownClickable = false;
                setTimeout(() => {
                    mainDropdownElement.css("display", "none");
                    mainDropdownElement.css("animation", "smoothOpacityIn 0.7s ease-in-out, smoothFromRight 0.7s ease-in-out")
                    updateArrowDirection(0);
                    isMainDropdownClickable = true;
                }, 500);
            }

        }

        isMainDropdownOpened = !isMainDropdownOpened;
    });

    hamburgerMenuButton.on("click", function () {
        if (isHamburgerMenuOpened) {
            hamburgerMenuContent.css("animation", "smoothOpacityIn 1s ease-in-out, growInFromVoid 1s ease-in-out");
            hamburgerButtonImage.css("transform", "RotateZ(90deg)")

            hamburgerMenuContent.css("display", "flex");
        } else {
            hamburgerButtonImage.css("transform", "RotateZ(0deg)")
            hamburgerMenuContent.css("animation", "growOut 0.5s ease-in-out");
            setTimeout(() => {
                hamburgerMenuContent.css("display", "none");
            }, 500);

        }

        isHamburgerMenuOpened = !isHamburgerMenuOpened;
    })

    $('#canvasAnimation').click(function () {
        if (this.checked) {
            localStorage.setItem("canvasAnimation", "true");
            createCanvas("body");
        }
        else if(!this.checked) {
            SetCanvas(false);
            localStorage.setItem("canvasAnimation", "false");
        }
    })

    $("[type=range]").change(function(){
        let newv=$(this).val();
        $(this).next().text(newv);
    });;

    $("#amountOfStars").change(function(){
        localStorage.setItem("bgStarCount", $(this).val());
    })

    
});

// Updates the arrow icon direction in the UI
function updateArrowDirection(stateId) {
    const arrowIcon = document.getElementById("Arrow_SignUp");
    arrowIcon.classList.toggle("start", stateId === 0);
    arrowIcon.classList.toggle("finish", stateId === 1);
}

// Animates a specified element with custom callbacks
function animateElement(element, animationName, durationInSeconds, callback) {
    if (element != null) {
        element.style.animation = `${animationName} ${durationInSeconds + durationInSeconds * 0.5}s`;
        setTimeout(callback, durationInSeconds * 1000);
    }
}

// Adds and removes CSS classes dynamically
function addAndRemoveClass(elementId, classToAdd, classToRemove) {
    const element = document.getElementById(elementId);
    if (element != null) {
        element.classList.remove(classToRemove);
        element.classList.add(classToAdd);
    }
}

// Removes a CSS class from an element
function removeClass(elementId, classToRemove) {
    const element = document.getElementById(elementId);
    if (element != null) {
        element.classList.remove(classToRemove);
    }
}

// Adds a CSS class to an element
function addClass(elementId, classToAdd) {
    const element = document.getElementById(elementId);
    if (element != null) {
        element.classList.add(classToAdd);
    }
}

// Redirects to a specified URL based on an ID
function redirect(redirectId) {
    const urls = {
        0: "https://mercantec.dk",
        1: "logIn.php",
        2: "main.php",
        3: "register.php",
        4: "upload.php",
        5: "admin.php",
        6: "settings.html",
        999: "index.html"
    };

    const headerElements = {
        header: document.getElementById("header"),
        footer: document.getElementById("footer"),
        headerLeft: document.getElementById("HeaderLeft"),
        headerRight: document.getElementById("HeaderRight"),
        body: document.getElementById("body")
    };

    Object.values(headerElements).forEach(el => animateElement(el, "smoothToTop", 0.5));

    let animationType = "";
    switch (activeBodyTextArea) {
        case "infoHolderChild1" || "archDiv":
            animationType = "growOut";
            break;
        case "HomeText":
            animationType = "smoothToBottom";
            break;
    }

    animateElement(document.getElementById(activeBodyTextArea), animationType, 1);
    animateElement(headerElements.body, "smoothOpacityOut", 0.9, () => {
        window.location.href = urls[redirectId] || "/";
    });

}

// Document ready actions
document.addEventListener("DOMContentLoaded", () => {
    $(document).ready(
        function () {

            if (window.document.title == "Settings - Archive of 3D-Prints") {
                $("#amountOfStars").val(parseInt(localStorage.getItem("bgStarCount")));
                $("#amountOfStars").next().text(parseInt(localStorage.getItem("bgStarCount")));
                switch (localStorage.getItem("canvasAnimation")) {
                    case "true":
                        $('#canvasAnimation').prop('checked', true);
                        break;

                    case "false":
                        $('#canvasAnimation').prop('checked', false);
                        break;
                }
            }

        });

    const archiveSuffix = " - Archive of 3D-Prints";
    const titles = {
        0: "Log In",
        1: "About",
        2: "Archive",
        3: "Register",
        4: "Upload",
        5: "Admin Panel",
        6: "Settings",
    };
    var activeSection = null;

    switch (document.title) {
        case (titles[0] + archiveSuffix || titles[3] + archiveSuffix || titles[4] + archiveSuffix || titles[5] + archiveSuffix || titles[6] + archiveSuffix):
            activeSection = "infoHolderChild1";
            break;

        case (titles[1] + archiveSuffix):
            activeSection = "HomeText";
            break;

        case (titles[2] + archiveSuffix):
            activeSection = "archDiv";
            break;
    }

    activeBodyTextArea = activeSection;
})
