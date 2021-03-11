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

class manager{

    private $sql;

    function __construct(){ // RETURN VOID
        
        // create the SQL object. 
        $this->sql = new sqlControl();
        error_log("loaded class manager");

    }

    function insertLine(){ // RETURN BOOL
        
    }

    function addPlatform(){ // RETURN BOOL

    }

    function addCategory($name){ // RETURN BOOL
        if($name == ''){
            // there is nothing to use 
            return false;
        }

        // add this line to the DB. 
        return $this->sql->sqlCommand("INSERT INTO Category ( CategoryName ) VALUES (:cat)", array(':cat' => $name), true); 

        return false; // if somehow we don't return. This should never fire. 
    }

    function exportCSV($plaftorm, $dateStart = false, $dateEnd = false){ // RETURN CSV CONTENT as JSON


    }

    
}