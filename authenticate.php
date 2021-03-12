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
    $manager = new manager();

    if(isset($_POST['UseDateTime']) && $_POST['UseDateTime'] == 'on'){
        // this is a used datetime. 
        $datetime = $_POST['DateYear'] . '-' . str_pad($_POST['DateMonth'], 2, "0", STR_PAD_LEFT) . '-' . str_pad($_POST['DateDay'], 2, "0", STR_PAD_LEFT) . ' ' . $_POST['DateTime'];
    } else {
        $datetime = false;
    }

    $repeat = ((isset($_POST['RepeatMessage']) && $_POST['RepeatMessage'] == 'on') ? true : false);

    $retval = $manager->insertLine(
            $_POST['Platform'], 
            $_POST['Category'], 
            $_POST['Message'], 
            false, // images go here.  
            $_POST['Priority'], 
            $datetime, 
            $repeat, 
            $_POST['RepeatDays'], 
            $_POST['RepeatCount']
        );



    header('location:index.php?addm=' . ($retval ? 'true' : 'false')); // return to the homepage and place result.
    die(); // we're done here. 
}

if(isset($_POST['AddPlatform'])){
    // this is a platform to add.
    $manager = new manager();

    $retval = $manager->addPlatform($_POST['PlatformName'], $_POST['APILink'], $_POST['RecycleLimit'], $_POST['CharacterLimit']);

    header('location:index.php?addp=' . ($retval ? 'true' : 'false')); // return to the homepage and place result.
    die(); // we're done here. 
}

if(isset($_POST['AddCategory'])){
    $manager = new manager();

    $retval = $manager->addCategory($_POST['CategoryName']);

    header('location:index.php?addc=' . ($retval ? 'true' : 'false')); // return to the homepage and place result.
    die(); // we're done here. 
}

if(isset($_POST['MakeCSV'])){
    // more complicated, we'll need to come up with something. 

}
