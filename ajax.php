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

/*if(isset($_POST[''])){

}*/



?>