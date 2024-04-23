const ak = "602a76ab5f2a7518622a79d1";
var isOn = false; // Credentials window
const e = document.getElementById("rc");
var fT = ".html";
var l = [];

var L = 4500;
var L2 = 1000;
function gT(i) {
    let d = hashFnv32a(i.toString(), true);
    let e = hashFnv32a((i + 1).toString(), true);
    let f = hashFnv32a((i + 2).toString(), true);

    return d + e + f;
}

function tryLogIn(c) {
    const f = "flex";
    cc = hashFnv32a(c);
    var r = gT(cc);
    var s = NaN;

    switch (r) {
        case ak:
            s = 0;
            break;
        default:
            s = 1;
            break;
    }
    if (!isOn) {
        switch (s) {
            case 0:
                createInstanceWindow(e, "Correct!", "Redirecting now...", true);
                l.push("kU " + r);
                l.push("tK " + ak);
                l.push("kUa " + Date.now());
                appendStorage(l);
                setTimeout(() => {
                    window.location.href = url("main");
                }, L);
                break;

            case 1:
                createInstanceWindow(e, "Error occurred!", "Incorrect username or password!", false);
                break;

            default:
                console.log("Error generating an error message :D");
        }

        isOn = true;
    }

    const ee = document.getElementById("returnCredentials");
    if (ee.style.display != f) {
        ee.style.display = f;
        setTimeout(() => {
            ee.style.animation = "smoothToRight2 1s forwards";
            setTimeout(() => {
                e.innerHTML = "";
                isOn = false;
            }, L2);
        }, L);
    }
}

function url(url) {
    return url + fT;
}

function hashFnv32a(str, asString, seed) {
    /*jshint bitwise:false */
    var i, l,
        hval = (seed === undefined) ? 0x811c9dc5 : seed;

    for (i = 0, l = str.length; i < l; i++) {
        hval ^= str.charCodeAt(i);
        hval += (hval << 1) + (hval << 4) + (hval << 7) + (hval << 8) + (hval << 24);
    }
    if (asString) {
        return ("0000000" + (hval >>> 0).toString(16)).substr(-8);
    }
    return hval >>> 0;
}

function appendStorage(list) {
    list.forEach(item => {
        const r = item.split(" ");
        localStorage.setItem(r[0], r[1]);
    });

}


document.getElementById("sendCredentials").addEventListener("click", function () {
    if($("#passIn").val().length > 0 && $("#userIn").val().length > 0){
        $("#errorHandler").text("");
        tryLogIn(document.getElementById("passIn").value);
    }
    else
    {
        $("#errorHandler").text("Error: The input fields must not be empty!");
    }
    
});

function createInstanceWindow(e, t1, t2, useDefaultBG) {
    var bg = null;

    useDefaultBG ? (bg = "rgba(100,255,100,0.9)") : (bg = "rgba(255,100,100,0.9)");

    e.innerHTML += `<div id="returnCredentials" class="flexColumn">
    <!-- error title -->
    <p id="rct1" class="nonSelectable pText textAlignCenter" style="font-weight: 900; color: ${bg}">
        ${t1}
    </p>
    <p id="rct2" class="pText textAlignCenter" style="color: rgba(255, 255, 255, 0.2);">
        ${t2}
    </p>

    </div>`;

}

document.addEventListener("DOMContentLoaded", () => {
   /*  const b = localStorage.getItem("kUa");
    const d = Date.now();
    const tt = 1800;

    if (b != null) {
        if ((d - b) < (tt * 1000)) {
            window.location.href = url("main");
        }
    }
*/
});

$(document).ready(function() {
    $("#errorHandler").addClass("textAlignCenter");
    const b = $("#logInButtonText");
    const button = $("#sendCredentials");  // Assuming the button has an ID of logInButton

    // Store initial colors only once
    const originalColorB = b.css("color");
    const translucentColor = "rgba(255, 255, 255, 0.3)";

    // Function to update button state
    function updateButtonState() {
        if ($("#userIn").val().length > 0 && $("#passIn").val().length > 0) {
            button.prop('disabled', false); // Enable the button
            b.css('color', translucentColor); // Set color to translucent
        } else {
            button.prop('disabled', true);  // Disable the button
            b.css('color', originalColorB); // Reset to original color
            
        }
    }

    // Attach event handlers
    $("#userIn, #passIn").on("input", updateButtonState);
});

