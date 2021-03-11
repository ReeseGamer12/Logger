<?php

/**
 *
 * class-manager.php
 * 
 * The manager class handles all inserts and exports from the user. 
 *
 */

// prevent direct access
if(count(get_included_files()) ==1){
    http_response_code(403);
    exit("ERROR 403: Direct access not permitted.");
}

require_once("class-sql.php");

class manager{

    private $sql;

    function _construct(){ // RETURN VOID
        
        // create the SQL object. 
        $sql = new sqlControl();
        
    }

    function insertLine(){ // RETURN BOOL
        
    }

    function addPlatform(){ // RETURN BOOL

    }

    function addCategory(){ // RETURN BOOL

    }

    function exportCSV($plaftorm, $dateStart = false, $dateEnd = false){ // RETURN CSV CONTENT as JSON


    }

    
}