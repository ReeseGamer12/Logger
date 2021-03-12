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

    $("#AddLine").click( function() {
        // do site validation quickly. 
        var count = 0;
        if($("#Platform").val() == -1){
            count++;
            $("#perr").show();
        }
        if($("#Category").val() == -1){
            count++;
            $("#cerr").show();
        }
        if($("#Message").val() == ''){
            count++;
            $("#merr").show();
        }

        if($("#Message").val().length > maxPostLength){
            count++;
            $("#merrmax").show();
        }

        if(count > 0){ 
            return false;
        }
    });
});