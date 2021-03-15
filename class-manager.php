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

    }

    function insertLine($platformID, $categoryID, $message, $images = false, $priority = 1, $DateTime = false, $repeat = false, $repeatDays = 0, $repeatTimes = 0){ // RETURN BOOL
        if($message == '' || $platformID == -1 || $categoryID == -1){
            // there is insufficient information. 
            return false;
        }
        

        // insert the message. 
        if($this->sql->sqlCommand("INSERT INTO Message (PlatformID, CategoryID, DateTime, Message, Priority, RepeatBool, RepeatDays, RepeatTimes) VALUES (:PlatformID, :CategoryID, :DateTime, :Message, :Priority, :RepeatBool, :RepeatDays, :RepeatTimes)", 
            array(
                ':PlatformID' => $platformID,
                ':CategoryID' => $categoryID,
                ':DateTime' => ($DateTime == false ? '1000-01-01 00:00:00' : $DateTime),
                ':Message' => $message,
                ':Priority' => $priority,
                ':RepeatBool' => $repeat,
                ':RepeatDays' => $repeatDays,
                ':RepeatTimes' => $repeatTimes
            ), true) == false ){
                // insert failed, abort
                return false;
            }

        // last insert number for images, if any. 
        $InsertNo = $this->sql->lastInsert(); 

        // insert the images 
        if($images == false){
            return true;
        } else {
            // there are images to upload. 
            
            $count = count($images['name']); // get number of images uploaded. 
            
            for($i = 0; $i < $count; $i++){
                $errors= array();

                $file_name = $images['name'][$i];
                $file_size = $images['size'][$i];
                $file_tmp =  $images['tmp_name'][$i];
                $file_type = $images['type'][$i];

                // since names can ".", we need to remove the extension and recombine the rest. 
                $trueName = explode('.',$images['name'][$i]);
                $file_ext=strtolower(array_pop($trueName));
                
                $file_nameOnly = implode ('.', $trueName);

                $extensions= array("jpeg","jpg","png", "gif");
                
                if(in_array($file_ext,$extensions)=== false){
                
                    $errors[]="extension not allowed. JPG, PNG, or GIF only";
                
                }
                    
                if($file_size > 2097152){
                
                    $errors[]='File size must be less than 2 MB';
                
                }
                
                if(empty($errors)==true){
                    
                    $date = new DateTime();
                    
                    $filepo = "uploads/" . $file_nameOnly . '_' . $date->getTimestamp() . '.' . $file_ext;

                    move_uploaded_file( $file_tmp, $filepo);
                    
                    // once file is uploaded, we can next move to inserting the image line in DB

                    //$InsertNo
                    $this->sql->sqlCommand("INSERT INTO Image (MessageID, Image) VALUES (:id, :img)", 
                        array(
                            ':id' => $InsertNo,
                            ':img' => $filepo
                        ), true);

                } else {
                    error_log('Error: Failed to upload image: '. $images['name'][$i]);
                    
                    foreach($errors as $e){
                    
                        error_log($e);
                    
                    }
                    // this failed, bail.
                    return false;
                }
            }

            return true;
        } 
        
        return false;
    }

    function addPlatform($name, $apilink, $recyclelimit, $charlimit){ // RETURN BOOL
        if($name == ''){
            // there is nothing to use 
            return false;
        }
        
        return $this->sql->sqlCommand("INSERT INTO Platforms (PlatformName, APILink, RecycleLimit, CharacterLimit) VALUES (:name, :api, :recyc, :char)", 
            array(
                ':name' => $name,
                ':api' => $apilink,
                ':recyc' => $recyclelimit,
                ':char' => $charlimit,
            ), true); 

        return false; // if somehow we don't return. This should never fire. 
    }

    function addCategory($name, $frequency, $platforms){ // RETURN BOOL
        if($name == ''){
            // there is nothing to use 
            return false;
        }

        // add this line to the DB. 
        if( $this->sql->sqlCommand("INSERT INTO Category ( CategoryName, Frequency ) VALUES (:cat, :freq)", array(':cat' => $name, ':freq' => $frequency), true) == true){
            // if the insert worked, we'll next add all of the platform associations. 

            $id = $this->sql->lastInsert(); // last inserted ID. 

            foreach($platforms as $p){
                $this->sql->sqlCommand("INSERT INTO PCAssociations ( PlatformID, CategoryID ) VALUES (:plat, :cat)", array(':plat' => $p, ':cat' => $id), true);
            }
            
            return true;
        }



        return false; // if somehow we don't return. This should never fire. 
    }

    
    function getPlatforms(){ // RETURN ARRAY(K->V) OR FALSE
        // return the list of platforms as an array
        
        if($this->sql->sqlCommand("SELECT ID, PlatformName FROM Platforms", array(), true) ){

            $res = $this->sql->returnAllResults();

            $retval = array();

            foreach ($res as $r){
                $retval[$r['ID']] = $r['PlatformName'];
            }

            if(count($retval) > 0){
                return $retval;
            }
        }

        // this failed, for some reason.
        return false;
    }

    function getTextLimit($id){ // RETURN INT OR FALSE
        // get the text limit per ID. 
        if($id != ''){
           
            if($this->sql->sqlCommand("SELECT CharacterLimit FROM Platforms WHERE ID = :id", array(":id" => $id), true) ){
                $ret = $this->sql->returnResults();

                return $ret['CharacterLimit'];
            }
        }

        return false;
    }

    function getCategories(){ // RETURN ARRAY(K-V)
        // return the list of categories as an array
        
        if($this->sql->sqlCommand("SELECT ID, CategoryName FROM Category", array(), true) ){

            $res = $this->sql->returnAllResults();

            $retval = array();

            foreach ($res as $r){
                $retval[$r['ID']] = $r['CategoryName'];
            }

            if(count($retval) > 0){
                return $retval;
            }
        }

        // this failed, for some reason.
        return false;
    }

    function exportCSV($plaftorm, $dateStart = false, $days = 28){ // RETURN BOOL
        // create a folder with the images and a CSV of 
        
        // timestamp is used to create a unique upload file name. This is intended to be deleted after each creation and upload. 
        // images are copied in, so this could take a lot of space. 
        $date = new DateTime();

        if(mkdir("uploads/Export-" . $date->getTimestamp() . "/", 0777, true)){ // NAME NEEDS TO CHANGE
            // once the folder is made, we'll need to create the CSV.
            // while generating the CSV, also copy over images
            // output the CSV to the folder. 
            
            // we're done, folder is complete! 
            return true;
        }


        return false; // somehow failed. 
    }    
}