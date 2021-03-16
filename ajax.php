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

        $max = 999999;

        $vals = explode('|', $_POST['Platform']);
            
        foreach($vals as $v){
            if($v != ''){
                $tmax = $manager->getTextLimit($v);
        
                if ($tmax < $max) $max = $tmax;
            }
        }

        echo json_encode( array( 'Limit' => $max ) );
        die(); // max one call. 
    }
    
}



?>