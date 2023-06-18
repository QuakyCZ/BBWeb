// Make a GET reuqest to the server to get the votes
$(document).ready(function() {
    $.ajax({
        url: "https://czech-craft.eu/api/server/beastblock-cz/",
        type: "GET",
        headers: { //SET ACCEPT HEADER
            Accept : "application/json; charset=utf-8",
        },  
        dataType: "json",
        success: function(data) {
            $("#czech-craft-votes").html(data.votes);
        }
    });

    $.ajax({
        url: "https://api.minecraftservery.eu/info?id=359",
        type: "GET",
        headers: { //SET ACCEPT HEADER
            Accept : "application/json; charset=utf-8",
        },  
        dataType: "json",
        success: function(data) {
            $("#minecraftservery-votes").html(data.votes);
        }
    });

    $.ajax({
        url: "https://api.craftlist.org/v1/76ekjr73ptasowmrfs3e/info",
        type: "GET",
        headers: { //SET ACCEPT HEADER
            Accept : "application/json; charset=utf-8",
        },  
        dataType: "json",
        success: function(data) {
            $("#craftlist-votes").html(data.votes);
        }
    });

});


function copyFunction() {
    const copyText = document.getElementById("copyButton");
    console.log("Copying Discord tag to clipboard")
    // Add class to button
    copyText.classList.add("btn-success");
    copyText.classList.remove("btn-primary");
    copyText.innerHTML = "Zkopírováno!";
    // Send message into the console
    console.log("Successfully copied Discord tag to clipboard");
    // Write text from bracket to clipboard
    navigator.clipboard.writeText("mc.beastblock.cz");
    // Wait one seconds, then remove class from button
    setTimeout(function() {
        copyText.classList.remove("btn-success");
        copyText.classList.add("btn-primary");
        copyText.innerHTML = "Zkopírovat adresu";
    }, 1000);
}