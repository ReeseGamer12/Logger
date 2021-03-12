/*

Jquery/javascript for internal order application. 

*/

$(document).ready( function() {

    var maxPostLength = -1; // default argument will be overwritten

    $("#UseDateTime").click( function() {
        $("#datetime").toggle();
    });

    $("#RepeatMessage").click( function() {
        $("#repeater").toggle();
    });

    $("#Platform").change( function() {
        // on platform change, get the current max text and return,
        // store the value in a hidden variable. 
        $.ajax({
            method: "POST",
            url: "ajax.php",
            data: { 
                requesting: "maxTextLength", 
                Platform: $("#Platform").val()
            }
        }).done(function( msg ) {
            //alert( msg );
            
            var obj = $.parseJSON(msg);

            maxPostLength = obj.Limit;
        });
    });

    $("#addImageField").click( function() {
        // on click add a file option. 
        $("#messageform").append('<input type="file" name="Image[]" /><br />');

        return false;
    });
});