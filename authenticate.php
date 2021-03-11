<?php 

/**
*
* authenticate.php
*
* Authenticate is used for all PUSH requests for the site. 
*
*/

if(empty($_POST)){
    http_response_code(405);
    exit("ERROR 405: Method Not Found");
}

require_once("config.php");

if(isset($_POST['AddLine'])){
    // this is a line to add.

    header('location:index.php?s=TRUE'); // return to the homepage and place result.
    die(); // we're done here. 
}

if(isset($_POST['AddPlatform'])){
    // this is a platform to add.

    header('location:index.php?addp=TRUE'); // return to the homepage and place result.
    die(); // we're done here. 
}

if(isset($_POST['AddCategory'])){
    $manager = new manager();

    $retval = $manager->addCategory($_POST['CategoryName']);

    header('location:index.php?addp=' . ($retval ? 'true' : 'false')); // return to the homepage and place result.
    die(); // we're done here. 
}

if(isset($_POST['MakeCSV'])){
    // more complicated, we'll need to come up with something. 

}
