<?php 

/**
*
* functions.php
*
* Generic functions for creating dropdowns etc.
*
*/

function outputMonths(){
    $timezone  = -5;
    $now = time() + 3600*($timezone+date("I"));
    
    $months = array("", "January", "February", "March", "April", "May",
    "June", "July", "August", "September", "October", "November",
    "December");

    foreach($months as $k => $v){
        // output this month.
        if($v == "") continue;
        $s = '';
        if(gmdate("m", $now) == $k){
            $s = ' selected="selected" ';
        }
        echo '<option value="' . $k . '"' . $s . '>' . $v . '</option>';
    }
}

function outputDays() {
    $timezone  = -5;
    $now = time() + 3600*($timezone+date("I"));

    for($i = 1; $i < 32; $i++){
        $s = '';
        if(gmdate("d", $now) == $i){
            $s = ' selected="selected" ';
        }

        echo '<option value="' . $i . '"' . $s . '>' . $i . '</option>';
    }
}

function outputYear(){
    for($i = date("Y"); $i < date("Y") + 50; $i++)
        echo '<option value="' . $i . '">' . $i . '</option>';
}

function outputTime(){
    $timezone  = -5;
    $now = time() + 3600*($timezone+date("I"));

    $thish = gmdate("H", $now) * 2 + 1; // double the current hour + 1
                    
    for($i = 0; $i < 48; $i++){
        // determine the hour and second.
        
        if($i % 2 == 0){
            $h24time = ($i / 2) . ':00:00';
            if($i > 24){
                $h12time = (($i - 24) / 2) . ':00 PM';
            } else {
                $h12time = ($i / 2) . ':00 AM';
            }
        } else {
            $h24time = (($i - 1) / 2) . ':30:00';
            if($i > 24){
                $h12time = (($i - 25) / 2) . ':30 PM';
            } else {
                $h12time = (($i - 1) / 2) . ':30 AM';
            }
        }
        
        if($i == 0) $h12time = '12:00 AM';
        if($i == 1) $h12time = '12:30 AM';
        if($i == 24) $h12time = '12:00 PM';
        if($i == 25) $h12time = '12:30 PM';
        
        $s = '';
        if($thish == $i){
            $s = ' selected="selected" ';
        }

        echo '<option value="' . $h24time . '"' . $s . '>' . $h12time . '</option>';
    }
}

function outputOnlyTimes() {
    // only output the times as saved in the DB. 
    $manager = new manager();

    $times = $manager->getAllTimes();

    foreach($times as $t){
        echo '<option value="' . $t . '">' . $t . '</option>';
    }

}

function outputPlatformCheck(){
    // we'll have to get the platforms here.
    $manager = new manager();

    $val = $manager->getPlatforms();

    foreach($val as $k => $v){
        echo '<div><input type="checkbox" class="Platform" name="Platform[]" id="' . $k . '" value="' . $k . '" /><label for="' . $k . '">' . $v . '</label></div>';
    }
}

function outputPlatformOptions(){
    $manager = new manager();
    $val = $manager->getPlatforms();

    foreach($val as $k => $v){
        echo '<option value="' . $k . '">' . $v . '</option>';
    }
}

function outputCategoryOptions(){
    $manager = new manager();
    $val = $manager->getCategories();

    foreach($val as $k => $v){
        echo '<option value="' . $k . '">' . $v . '</option>';
    }
}

function outputCategoryCheck(){
    $manager = new manager();
    $val = $manager->getCategories();

    foreach($val as $k => $v){
        echo '<div><input type="checkbox" class="Category" name="Category[]" id="' . $v . '" value="' . $k . '" /><label for="' . $v . '">' . $v . '</label></div>';
    }
}