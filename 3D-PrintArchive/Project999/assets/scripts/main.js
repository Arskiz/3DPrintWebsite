const fT = ".html";
let signedIn = true;
const notLoggedIn = "Not Logged In! Your actions are heavily restricted!";
const loggedIn = "Good afternoon ${USR_UND}!"
const errUser = "Not Logged In";

let cards = [];

let mobilePhase = 0; /* 0 = Not Mobile */

document.addEventListener("DOMContentLoaded", () => {

    if (!signedIn) {
        $("#headerRightLoggedUsedName").text(errUser);
        $("#centerHeaderText").text(notLoggedIn);
    }
    else {
        $("#headerRightLoggedUsedName").text("User")
        $("#centerHeaderText").text(loggedIn);
    }

    loadItems(false, 5, 5, 100, 500);
})

function checkStorage() {
    if ((d - b) < (tt * 1000)) {
        return true;
    }
    else {
        alert("Event key expired! Please log in again.");
        return false;
    }
}

function loadItems(isMob) {
    const k = document.getElementById("body");
    k.style.overflow = "auto";
    let atl = 100;

    if (!isMob) {
        for (let i = 0; i < atl; i++) {
            const columnId = "fC" + (i + 1);
            const itemName = `TEST`;
            const itemCategory = "ItemTesting";
            const itemArtist = "Arskiz";
            const itemDescription = "Lorem Ipsum...";
            const itemImage = "3dArt-index-649821.jpg";

            cards.push(generateCard(columnId, itemName, itemCategory, itemArtist, itemDescription, itemImage));
        }

    }
    else {

    }
}

const l = document.getElementById("LogOutBtn");
let archLogOpened = false;
let clickable = true;
l.addEventListener("click", () => {
    if (!archLogOpened) {
        if(clickable){
            clickable = false;
            $("#accountWindow").css("display", "flex");
            setTimeout(() => {
                clickable = true;
            }, 240);
        }
    }
    else {
        if (clickable) {
            $("#accountWindow").css("animation", "smoothOpacityOut 0.3s ease-in-out");
            clickable = false;
            setTimeout(() => {
                $("#accountWindow").css("display", "none");
                $("#accountWindow").css("animation", "smoothOpacityIn 0.3s ease-in-out")
                clickable = true;
            }, 240);
        }
    }
    archLogOpened = !archLogOpened;

    /*
    flush();
    alert("Signed out.");
    window.location.href = url("index");
    */
});

function url(a) {
    return a + fT;
}

function flush() {
    localStorage.removeItem("kU");
    localStorage.removeItem("kUa");
    localStorage.removeItem("tK");
}

function generateCard(id, itemName, itemID, author, description, imgName, returnElement) {

    let name = getProperName(itemName);
    const container = document.getElementById(id);
    const card = document.createElement('div');
    card.className = 'printItem itemFont';
    card.id = itemID;

    const cardTitle = document.createElement('p');
    cardTitle.className = 'pText cardTxt Titles itemTitle';
    cardTitle.textContent = name;

    card.innerHTML = `
    <img class="cardImg" src="../Project999/assets/images/items/${imgName}" width="270px">`

    const innerDiv = document.createElement('div');
    innerDiv.className = 'printItemInner';
    innerDiv.innerHTML = `
        <p title="${itemName}" class="white pText cardTxt itemInnerTitle Titles" onclick="previewItem('${itemName}', '${imgName}', '${description}')">${name}</p>
    `;
    card.appendChild(innerDiv);
    container.appendChild(card);


    if (returnElement) {
        return (itemName + " " + itemID + " " + description + " " + imgName + " " + id);
    }
}

function getProperName(n) {
    let maxChars = 20;
    let name = "";
    let overExpected = false;

    if (n.length >= maxChars) {
        overExpected = true;
    }

    if (overExpected) {
        for (let i = 0; i < (maxChars - 3); i++) {
            name += n[i];
        }
        return name + "...";
    }
    else {
        return n;
    }
}


function downloadFile(fileName) {
    if (checkStorage()) {
        // Test objcet to download, to be changed later...
        fetch("../Project999/assets/downloads/lol.txt").then(res => res.blob()).then(blob => {
            const aElement = document.createElement('a');
            const href = URL.createObjectURL(blob);
            aElement.href = href;
            aElement.setAttribute('download', fileName);
            aElement.setAttribute('target', '_blank');
            aElement.click();
            URL.revokeObjectURL(href);
        })
            .catch(error => console.error('Error downloading file:', error));
    }
    else {
        window.location.href = url("logIn");
    }

}

function previewItem(name, imgName, desc) {
    $("#itemPreviewHolder").html("")
    $("#blurOverlay").removeClass("hidden2").addClass("visible2");
    $("#itemPreviewHolder").html(
        `
        <div class="fixedOnTop" id="itemPreview">
        <p id="itemPreviewTitle" class="iPT pText">
            ${name}
        </p>
        <button onclick="clearPreviewItem()" id="itemPreviewExit" class="buttonBNR Hoverable">
            <p class="white pText">
                X
            </p>
        </button>
        <div class="lineLonger"></div>
        <div id="itemPreviewInner" style="overflow-y: scroll; height: 500px;">
            <img id="itemPreviewImage" src="../Project999/assets/images/items/${imgName}" width="700px" alt="Image of Item" style="border-radius: 20px;">
            <p id="itemPreviewDescText" class="white pText">
                ${desc}
            </p>
        </div>
    </div>
        `
    )
}

function clearPreviewItem() {
    $("#blurOverlay").removeClass("visible2").addClass("hidden2");
    $("#itemPreviewHolder").html("");
}


$(window).on("load", function () {
    
    
    const loadMoreBTN = $("#loadMoreBTN");
    const accountInformationButton = $("#logInBtnAccountInformation");

    loadMoreBTN.on("click", function () {
        alert("Work in progress.")
    })

    accountInformationButton.on("click", function(){
        $("#accountWindow").css("animation", "smoothOpacityOut 0.3s ease-in-out");
        setTimeout(() => {
            $("#accountWindow").css("display", "none");
            redirect(1);
        }, 300);
        
    })
})
