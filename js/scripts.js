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

    
    $(".Platform").change( function() {
        // on platform change, get the current max text and return,
        // store the value in a hidden variable. 
        
        var sendArr = '';

        $(".Platform").each( function() {

            var thisObj = $(this);
            
            if(thisObj.is(":checked")){
                
                sendArr = sendArr + '|' + thisObj.val();
                
            }
        });

        $.ajax({
            method: "POST",
            url: "ajax.php",
            data: { 
                requesting: "maxTextLength", 
                Platform: sendArr
            }
        }).done(function( msg ) {
            
            var obj = $.parseJSON(msg);
            
            maxPostLength = parseInt(obj.Limit);
                
            $("#lim").html(" Max: " + maxPostLength);

        });
        
    });
    

    $("#AddLine").click( function() {
        // do site validation quickly. 
        var count = 0;
        if(!platformCount()){
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

function platformCount(){

    var ret = false;

    $(".Platform").each( function() {

        if($(this).is(":checked")){
            
            if(ret == false){
                ret = true;
            }
        }
    });

    return ret;
}