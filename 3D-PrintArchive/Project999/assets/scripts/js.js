let signUpOpened = false;
var bodyTextArea = null;

function toggleSignUpDropdown() {
    const signUpDropDown = $("#signUpDropDown");
    const signUpDropDownInner = $("#signUpDropDownInner");

    // Function to manage opening the dropdown
    function openDropdown() {
        arrowHandler(1);
        signUpDropDown.removeClass("hidden2");
        signUpDropDownInner.removeClass("hidden2");
    }

    // Function to manage closing the dropdown
    function closeDropdown() {
        arrowHandler(0);
        signUpDropDownInner.addClass("hidden2");
        signUpDropDown.addClass("hidden2");
    }

    // Toggle the state with a slight delay to manage transitions
    setTimeout(() => {
        if (signUpOpened) {
            closeDropdown();
        } else {
            openDropdown();
        }
        signUpOpened = !signUpOpened;
    }, 50);
}

let dropDownOpened = false;
let dropDownClickable = true;
const dropDownElement = $("#signUpDropDown");
const hamburgerMenuElementButton = $("#HeaderRightHamburger");
const hamburgerMenu = $("#HamburgerContent");
const hamburgerBTNImg = $("#HamburgerBTNImg");

let hamburgerOpened = null;

$(window).on("load", function () {
    hamburgerOpened = false;
    hamburgerMenu.css("display", "none");
    $("#SignUpDropDownButton").on("click", function () {
        if (!dropDownOpened) {
            if (dropDownClickable) {
                arrowHandler(1);
                dropDownElement.css("animation", "smoothOpacityIn 0.7s ease-in-out, smoothFromRight 0.7s ease-in-out")
                dropDownElement.css("display", "flex");
                dropDownClickable = false;
                setTimeout(() => {
                    dropDownClickable = true;
                }, 500);
            }
        }
        else {
            if (dropDownClickable) {
                dropDownElement.css("animation", "smoothOpacityOut 0.7s ease-in-out, smoothToRight 0.7s ease-in-out")
                dropDownClickable = false;
                setTimeout(() => {
                    dropDownElement.css("display", "none");
                    dropDownElement.css("animation", "smoothOpacityIn 0.7s ease-in-out, smoothFromRight 0.7s ease-in-out")
                    arrowHandler(0);
                    dropDownClickable = true;
                }, 500);
            }

        }

        dropDownOpened = !dropDownOpened;
    });

    hamburgerMenuElementButton.on("click", function () {
        if (hamburgerOpened) {
            hamburgerMenu.css("animation", "smoothOpacityIn 1s ease-in-out, growInFromVoid 1s ease-in-out");
            hamburgerBTNImg.css("transform", "RotateZ(90deg)")

            hamburgerMenu.css("display", "flex");
        }
        else {
            hamburgerBTNImg.css("transform", "RotateZ(0deg)")
            hamburgerMenu.css("animation", "growOut 0.5s ease-in-out");
            setTimeout(() => {
                hamburgerMenu.css("display", "none");
            }, 500);

        }

        hamburgerOpened = !hamburgerOpened;
    })


});


function arrowHandler(ID) {
    const arrowSignUp = document.getElementById("Arrow_SignUp");
    arrowSignUp.classList.toggle("start", ID === 0);
    arrowSignUp.classList.toggle("finish", ID === 1);
}

function animateElement(element, animation, duration, callback) {
    if (element != null) {
        element.style.animation = animation;
        element.style.animationDuration = `${duration + duration * 0.5}s`;
        setTimeout(callback, duration * 1000);
    }

}

function ACARC(elementID, classToAdd, classToRemove) {
    const element = document.getElementById(elementID);
    if(element != null){
        element.classList.remove(classToRemove)
        element.classList.add(classToAdd);
    }

}

function RC(elementID, classToRemove) {
    if(element != null){
    const element = document.getElementById(elementID);
    element.classList.remove(classToRemove);
}
}

function AC(elementID, classToAdd) {
    if(element != null){
    const element = document.getElementById(elementID);
    element.classList.add(classToAdd);
}
}

// Redirect function simplified for clarity
function redirect(rID) {
    const urls = {
        0: "https://mercantec.dk",
        1: "logIn.html",
        2: "main.html",
        999: "index.html"
    };

    const header = document.getElementById("header");
    const footer = document.getElementById("footer");
    const headerLeft = document.getElementById("HeaderLeft");
    const headerRight = document.getElementById("HeaderRight");
    const body = document.getElementById("body");



    animateElement(headerLeft, "smoothToLeft", 0.5);
    animateElement(headerRight, "smoothToRight", 0.5);
    animateElement(header, "headerAnim2", 1);
    animateElement(footer, "smoothToBottom", 1);
    let t = "";
    switch (bodyTextArea) {
        case "infoHolderChild1" || "archDiv":
            t = "growOut";
            break;
        case "HomeText":
            t = "smoothToBottom";
            break;
    }

    animateElement(document.getElementById(bodyTextArea), t, 1);
    animateElement(body, "smoothOpacityOut", 0.9, () => {
        window.location.href = urls[rID] || "/";
    });

}

document.addEventListener("DOMContentLoaded", () => {
    const a = " - Archive of 3D-Prints";
    const titles = {
        0: "Log In",
        1: "About",
        2: "Archive"
    };
    var b = null

    switch (document.title) {
        case (titles[0] + a):
            b = "infoHolderChild1";
            break;

        case (titles[1] + a):
            b = "HomeText";
            break;

        case (titles[2] + a):
            b = "archDiv";
            break;
    }

    bodyTextArea = b;
    console.log(bodyTextArea)
})




