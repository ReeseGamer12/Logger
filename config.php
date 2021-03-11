<?php

/**
*
* config.php
*
* Config file for all requirements for every page as well as placeholder for database links. 
* Database links will be removed for online site setup. 
*
*/

// define the SQL data. 
$sqlCFG = array(
    'db' => 'SocialMedia',
    'user' => 'root',
    'pass' => 'root'
);

require_once("class-sql.php");
require_once("class-manager.php");