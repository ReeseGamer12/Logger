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

    if(isset($_FILES['Image']) && $_FILES['Image']['name'][0] != '') {
        $image = $_FILES['Image'];
    } else {
        $image = false;
    }

    $repeat = ((isset($_POST['RepeatMessage']) && $_POST['RepeatMessage'] == 'on') ? 1 : 0);

    $retval = true;

    foreach($_POST['Platform'] as $p){
        $retval = $manager->insertLine(
                $p, 
                $_POST['Category'], 
                $_POST['Message'], 
                $image, // images go here.  
                ($_POST['Priority'] != '' ? $_POST['Priority'] : 1), 
                $datetime, 
                $repeat, 
                ($_POST['RepeatDays'] != '' ? $_POST['RepeatDays'] : 0), 
                ($_POST['RepeatCount'] != '' ? $_POST['RepeatCount'] : 0)
            );

        if($retval != true){
            break;
        }
    }


    header('location:index.php?addm=' . ($retval ? 'true' : 'false')); // return to the homepage and place result.
    die(); // we're done here. 
}

if(isset($_POST['AddPlatform'])){
    // this is a platform to add.
    $manager = new manager();

    $retval = $manager->addPlatform($_POST['PlatformName'], $_POST['APILink'], $_POST['RecycleLimit'], $_POST['CharacterLimit'], $_POST['Category']);

    header('location:index.php?addp=' . ($retval ? 'true' : 'false')); // return to the homepage and place result.
    die(); // we're done here. 
}

if(isset($_POST['AddCategory'])){
    $manager = new manager();

    $retval = $manager->addCategory($_POST['CategoryName'], $_POST['PostFrequency'], $_POST['Platform']);

    header('location:index.php?addc=' . ($retval ? 'true' : 'false')); // return to the homepage and place result.
    die(); // we're done here. 
}

if(isset($_POST['AddPlatformTime'])){
    $manager = new manager();

    $retval = $manager->addPlatformTime($_POST['Platform'], $_POST['PTime']);

    header('location:index.php?addpt=' . ($retval ? 'true' : 'false')); // return to the homepage and place result.
    die(); // we're done here. 
}

if(isset($_POST['MakeCSV'])){
    // more complicated, we'll need to come up with something. 

    $manager = new manager();
    
    //exportCSV($plaftorm, $dateStart = false, $days = 28){
    $retval = $manager->exportCSV($_POST['Platform'], mktime(0, 0, 0, $_POST['DateMonth'], $_POST['DateDay'], $_POST['DateYear']), $_POST['DaysToCreate']);

    header('location:index.php?export=' . ($retval ? 'true' : 'false')); // return to the homepage and place result.
    die(); // we're done here. 
}
