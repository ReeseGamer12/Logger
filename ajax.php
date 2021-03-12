<?php 

/**
*
* ajax.php
*
* AJAX Code for handling all calls related to social media app 
*
*/

if(empty($_POST)){
    http_response_code(405);
    exit("ERROR 405: Method Not Found");
}

require_once("config.php");

if(isset($_POST['requesting'])){
    if($_POST['requesting'] == 'maxTextLength'){
        // return the max text length, echoed as a json string.
        $manager = new manager();

        if($_POST['Platform'] == -1){
            $max = 9999;
        } else {
            $max = $manager->getTextLimit($_POST['Platform']);
        }
        
        echo json_encode( array( 'Limit' => $max ) );
        die(); // max one call. 
    }
    
}



?>