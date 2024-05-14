// Copyright© Aron Särkioja to Mercantec, Inc. 2024. All rights reserved.

// Define file type extension for HTML files
const fileTypeHtml = ".html";

// Logout button functionality
const logoutButton = document.getElementById("LogOutBtn");
let isArchiveLogOpened = false;
let isClickable = true;
logoutButton.addEventListener("click", () => {
    if (!isArchiveLogOpened) {
        if (isClickable) {
            isClickable = false;
            $("#accountWindow").css("display", "flex");
            setTimeout(() => {
                isClickable = true;
            }, 240);
        }
    }
    else {
        if (isClickable) {
            $("#accountWindow").css("animation", "smoothOpacityOut 0.3s ease-in-out");
            isClickable = false;
            setTimeout(() => {
                $("#accountWindow").css("display", "none");
                $("#accountWindow").css("animation", "smoothOpacityIn 0.3s ease-in-out")
                isClickable = true;
            }, 240);
        }
    }
    isArchiveLogOpened = !isArchiveLogOpened;
});


$(document).ready(function(){
    $("#sort-select").val(localStorage.getItem("printSort"));
    $("#sort-ascdesc").val(localStorage.getItem("printType"));
})

// Sorting mode
function getVal(sel, type){
    switch(type){
        case 1:
            localStorage.setItem("printSort", sel.value);
        break;

        case 2:
            localStorage.setItem("printType", sel.value);
        break;
    }
    
    location.reload();
}

// Constructs URLs based on input
function url(page) {
    if (page == "logIn" || page =="main" || page =="register" || page =="upload") {
        return page + ".php";
    }
    else {
        return page + fileTypeHtml;
    }
}
