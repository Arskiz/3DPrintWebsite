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
    // main.php / Main achive itself
    $("#sort-select").val(localStorage.getItem("printSort"));
    $("#sort-ascdesc").val(localStorage.getItem("printType"));

    // Admin panel
    $("#admin-select-sort").val(localStorage.getItem("accSort"));
    $("#admin-ascdesc").val(localStorage.getItem("accType"));
})

// Sorting mode
function getVal(sel, type){
    switch(type){
        // main.php / Main archive itself
        case 1:
            localStorage.setItem("printSort", sel.value);
        break;

        case 2:
            localStorage.setItem("printType", sel.value);
        break;

        // Admin panel
        case 3:
            localStorage.setItem("accSort", sel.value);
        break;

        case 4:
            localStorage.setItem("accType", sel.value);
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

// Only used in Detail Window in the close button 
function DestroyPreviewWindow(){
    const html = "";
    $("#detailWindow").html(html);
    $("#detailWindow").css("display", "none");
}

// Function to show a Window about the print-item's details
function WindowPreview(id,name,authorId,material,color,comments,filename,private,authorName, realName){
    let html = "";

    const lineBreak = "<div class='line' style='height:5px;'></div>"
    const containerEnd = "</div>";
    const infoDiv = `<div class='flexRow' style='width:100%; display:flex; flex-direction:column; justify-content:center;'>`;

    const H1Name = `<div class='flexRow' style='justify-content:space-between; align-items:center;'><h1 id='detailsWindowTitle' class='pText white' style='margin: 0px 0px; font-weight:900; font-size:40px;-webkit-text-stroke: 1px black'>Details of "${name}":</h1><div class='closeButton' title='Close this window' onclick='DestroyPreviewWindow()'><p class='pText white'>X</p></div></div>`;

    const pId = `<p class='pText white' style='width: 93%;font-weight:700'>ID:</p><div class='infoBox'><p class='pText white' style='margin:0'>${id}</p></div>`;

    const pName = `<p class='pText white' style='width: 93%;font-weight:700'>Name:</p><div class='infoBox'><p class='pText white' style='margin:0'>${name}</p></div>`;

    const pMaterial = `<p class='pText white' style='width: 93%;font-weight:700'>Material:</p><div class='infoBox'><p class='pText white' style='margin:0'>${material}</p></div>`;

    const pColor = `<p class='pText white' style='width: 93%;font-weight:700'>Color:</p><div class='infoBox'><p class='pText white' style='margin:0'>${color}</p></div>`;

    const pComments = `<p class='pText white' style='width: 93%;font-weight:700'>Comments:</p><div class="infoBox"><p class='pText white' style='margin:0'>${comments}</p></div>`;

    const pAuthorName = `<p class='pText white' style='width: 93%;font-weight:700'>Author:</p><div class='infoBox'><p class='pText white' style='margin:0'>${authorName}</p></div>`;

    const pAuthorId = `<p class='pText white' style='width: 93%;font-weight:700'>Author ID:</p><div class='infoBox'><p class='pText white' style='margin:0'>${authorId}</p></div>`;

    const pFileName = `<p class='pText white' style='width: 93%;font-weight:700'>File Name (In Server):</p><div class='infoBox'><p class='pText white' style='margin:0'>${filename}</p></div>`;

    const pRealName = `<p class='pText white' style='width: 93%;font-weight:700'>Full Name:</p><div class='infoBox'><p class='pText white' style='margin:0'>${realName}</p></div>`;

    // Construction:

    html += H1Name;

    html += lineBreak;
    
    html += infoDiv;
        html += pId;
    html += containerEnd;

    html += infoDiv;
        html += pName;
    html += containerEnd;

    html += infoDiv;
        html += pMaterial;
    html += containerEnd;

    html += infoDiv;
        html += pColor;
    html += containerEnd;


    html += pComments;

    html += infoDiv;
        html += pAuthorName;
    html += containerEnd;
    
    html += infoDiv;
        html += pAuthorId;
    html += containerEnd;

    html += infoDiv;
        html += pFileName;
    html += containerEnd;

    html += infoDiv;
        html += pRealName;
    html += containerEnd;

    // Add the html to the detailWinow-element and set it visible by using jQuery.
    $("#detailWindow").html(html);
    $("#detailWindow").css("display", "flex");
}