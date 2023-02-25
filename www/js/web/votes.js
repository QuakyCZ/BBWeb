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
