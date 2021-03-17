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
    private $categories; // used for export. 

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
            ), false) == false ){
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
                        ), false);

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

    function addPlatform($name, $apilink, $recyclelimit, $charlimit, $category){ // RETURN BOOL
        if($name == ''){
            // there is nothing to use 
            return false;
        }
        
        if( $this->sql->sqlCommand("INSERT INTO Platforms (PlatformName, APILink, RecycleLimit, CharacterLimit) VALUES (:name, :api, :recyc, :char)", 
            array(
                ':name' => $name,
                ':api' => $apilink,
                ':recyc' => $recyclelimit,
                ':char' => $charlimit,
            ), false) ){

                $id = $this->sql->lastInsert(); // last inserted ID. 

                foreach($category as $c){
                    $this->sql->sqlCommand("INSERT INTO PCAssociations ( PlatformID, CategoryID ) VALUES (:plat, :cat)", array(':plat' => $id, ':cat' => $c), false);
                }

                return true;
            }

        return false; // if somehow we don't return. This should never fire. 
    }

    function addPlatformTime($platform, $time){ // RETURN BOOL
        // add platform times for later export.

        foreach($platform as $p){
            if( !$this->sql->sqlCommand("INSERT INTO PlatformTime ( PlatformID, PlatformTime ) VALUES (:plat, :time)", array(':plat' => $p, ':time' => $time), true)){
                return false;
            }
        }
        return true;
    }

    function addCategory($name, $frequency, $platforms){ // RETURN BOOL
        if($name == ''){
            // there is nothing to use 
            return false;
        }

        // add this line to the DB. 
        if( $this->sql->sqlCommand("INSERT INTO Category ( CategoryName, Frequency ) VALUES (:cat, :freq)", array(':cat' => $name, ':freq' => $frequency), false) == true){
            // if the insert worked, we'll next add all of the platform associations. 

            $id = $this->sql->lastInsert(); // last inserted ID. 

            foreach($platforms as $p){
                $this->sql->sqlCommand("INSERT INTO PCAssociations ( PlatformID, CategoryID ) VALUES (:plat, :cat)", array(':plat' => $p, ':cat' => $id), false);
            }

            return true;
        }



        return false; // if somehow we don't return. This should never fire. 
    }

    
    function getPlatforms(){ // RETURN ARRAY(K->V) OR FALSE
        // return the list of platforms as an array
        
        if($this->sql->sqlCommand("SELECT ID, PlatformName FROM Platforms", array(), false) ){

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
           
            if($this->sql->sqlCommand("SELECT CharacterLimit FROM Platforms WHERE ID = :id", array(":id" => $id), false) ){
                $ret = $this->sql->returnResults();

                return $ret['CharacterLimit'];
            }
        }

        return false;
    }

    function getCategories(){ // RETURN ARRAY(K-V)
        // return the list of categories as an array
        
        if($this->sql->sqlCommand("SELECT ID, CategoryName, Frequency FROM Category", array(), false) ){

            $res = $this->sql->returnAllResults();

            $retval = array();

            $this->categories = $res;

            foreach ($res as $r){
                $retval[$r['ID']] = $r['CategoryName'];
                //$this->categories[$r['CategoryName']] = $r['Frequency'];
            }

            if(count($retval) > 0){
                return $retval;
            }
        }

        // this failed, for some reason.
        return false;
    }

    function getTimesForPlatform($platform){ // RETURN ARRAY(V) or FALSE
        // return a list of time objects for the given platform. 

        if($this->sql->sqlCommand("SELECT PlatformTime FROM PlatformTime WHERE PlatformID = :id", array(':id' => $platform), false) ){

            $res = $this->sql->returnAllResults();

            $retval = array();

            foreach ($res as $r){
                $retval[] = $r['PlatformTime'];
            }

            if(count($retval) > 0){
                return $retval;
            }
            
            return false;
        }

        // this failed, for some reason.
        return false;

    }

    function exportCSV($platform, $dateStart = false, $days = 28){ // RETURN BOOL
        // create a folder with the images and a CSV of 

        // timestamp is used to create a unique upload file name. This is intended to be deleted after each creation and upload. 
        // images are copied in, so this could take a lot of space. 
        $date = new DateTime();
        $this->getCategories();
        $folder = "uploads/Export-" . date('Y-m-d-H-m-s', $date->getTimestamp()) . "/";
        
        if(mkdir($folder, 0777, true)){ // NAME NEEDS TO CHANGE
            // once the folder is made, we'll need to create the CSV.
            // while generating the CSV, also copy over images
            // output the CSV to the folder. 
            
            if(!$dateStart){ $dateStart = time(); } // set the date to day if not given. 

            // first create a list of dates that need to be referenced 
            $daysToCreate = array();

            for($i = 0; $i < $days; $i++){
                // we'll manually iterate i. 
                //echo date('w',  . '<br />';

                $nextDate = $dateStart + 24*60*60*$i;
                $dayOfWeek = date('w', $nextDate);

                if($dayOfWeek != 0 && $dayOfWeek != 6){
                    // valid day to create data for. 
                    $daysToCreate[] = $nextDate;
                }
            }

            // now that we have a list of days, we'll need to get the list of times. 
            $times = $this->getTimesForPlatform($platform);
            
            // we now have days and times to work with. To proceed, we'll need to 
            // create a loop to run through the days.

            /*  /// this determines unique categories for enforcing appropriate spacing. 
            // determine the categories for each, for even distribution. 
            $totalCatCount = count($daysToCreate) * count($times);

            // declare and fill blank category array.
            $categoryArray = array();
            $categoryArray = array_fill(0, $totalCatCount, -1);

            // go through each category, and determine each spot in the array.
            foreach($this->categories as $cat){
                for($i = 0; $i < $totalCatCount; $i += $cat['Frequency']){
                    $didAdd = false;
                    $x = 0;
                    while(!$didAdd){
                        if($categoryArray[$i + $x] == -1 || $i + $x > $totalCatCount){
                            $didAdd = true;
                            $categoryArray[$i + $x] = intval($cat['ID']);
                        } 
                        $x++;
                    }
                }
            }
            
            // go through, for each undefined selection add a random item. 
            for($i = 0; $i < $totalCatCount; $i++){
                if($categoryArray[$i] == -1){
                    $categoryArray[$i] = intval($this->categories[mt_rand(0, count($this->categories)-1)]['ID']);
                }
            }

            var_dump($categoryArray);

            foreach($categoryArray as $c){
                echo $c . '<br />';
            }

            die();
            */

            // blank CSV variable to write in later. 
            $csvContent = '';
            $msgNum = 0;

            foreach($daysToCreate as $day){
                // for each day, cycle through the times and pull message. 
                foreach($times as $time){
                    $message = $this->getMessage($platform, $day, $time);
                    
                    // temp output to see what we get 
                    echo date('Y-m-d', $day) . ' ' . $time . '<br />';
                    echo 'ID: ' . $message['ID'] . ' CAT: ' . $message['CategoryID'] . ' Msg: ' . $message['Message'] . '<br />';
                    
                    // move images to the new folder, renamed as Image<msg count>-<image x of x>
                    $imageNames = $this->getImages($message['ID'], $msgNum, $folder);
                    
                    var_dump($imageNames);
                    echo '<br /><br />';

                    //output data as CSV content. 
                    // we'll use , and " as delimiters. 
                    //---------------------------------------------------------------------------!!
                    $csvContent .= '\r';
                    $msgNum++;
                }
            }




            die ();// WE'll clear this later so we can see results. 


            // we're done, folder is complete! 
            return true;
        }


        return false; // somehow failed. 
    }    

    private function getImages($id, $msgNum, $folder){ // RETURN ARRAY(V)
         // get the image ID's
        $this->sql->sqlCommand("SELECT Image FROM Image WHERE MessageID = :id", array(':id' => $id), false);
        $rets = $this->sql->returnAllResults();

        $names = array();
        $imgcount = 0;
        foreach($rets as $r){
            // for each image, create a file name then copy to the folder. 
            $theext = explode('.', $r['Image']);
            $ext = strtolower(array_pop($theext));
            $imagename = 'Message-' . $msgNum . '-' . $imgcount . '.' . $ext;
            
            $names[] = $imagename;

            // copy images from main area to this folder.
            copy($r['Image'], $folder . $imagename);
            $imgcount++;
        }

        return $names; 
    }


    private function getMessage($platform, $day, $time){ // RETURN ARRAY(K-V)
        
        $lastSendNo = $this->getLastSend($platform);
        $noSchedule = '1000-01-01 00:00:00'; // for determining unscheduled messages.
        // get the message information and return array.

        // 1: check for any message on day at time, check for all, set up highest priority. 
        $this->sql->sqlCommand("SELECT * FROM Message 
                    WHERE PlatformID = :id
                    AND DateTime = :datetime
                    ORDER BY Priority DESC", 
            array(
                ':id' => $platform,
                ':datetime' => gmdate("Y-m-d", $day) . ' ' . str_pad($time, 8, '0', STR_PAD_LEFT)
            ), false);

        $ret = $this->sql->returnAllResults();
        
        if(count($ret) > 0){
            // there is at least 1 result.
            // 1b: if other messages are at the same priority, we'll need to push one back by a day. Do so for later install. 
            // 1c: if message is a repeat, schedule this one then reschedule as per repeat.
            // TO DO
            
            echo '<br />area 1<br />';

            // update the messsage in the DB. 
            $this->updateMessage($ret[0]['ID'], $lastSendNo, $ret[0]['DateTime'],
                    $ret[0]['RepeatBool'], $ret[0]['RepeatTimes'], $ret[0]['RepeatDays'] );

            if(count($ret) > 1){
                // for each, delayMessage($id)
                for($i = 1; $i < count($ret); $i++){
                    // for each message, apply the delay.
                    $this->delayMessage($ret[$i]['ID'], $ret[$i]['DateTime']);
                }
            }
            
            return $ret[0]; 
        } else {

            // 2: if no message, next check to find all messages that haven't been sent. we'll pick from those. 
            // 2b: check messages for the last time sent, if above the "resend" number, we can send that category again. 
            // 2c: if none are above that threshhold, check 3

            $this->sql->sqlCommand("SELECT * FROM Message AS M
                    WHERE M.PlatformID = :id
                    AND M.SendNo = -1
                    AND M.DateTime = :datetime
                    AND (:lastSend - 
                        (SELECT SendNo FROM Message AS E 
                            WHERE E.PlatformID = :id 
                            AND E.CategoryID = M.CategoryID 
                            ORDER BY SendNo DESC LIMIT 1) >=
                        (SELECT Frequency FROM Category 
                            WHERE ID = M.CategoryID LIMIT 1) OR
                        (SELECT SendNo FROM Message as E 
                            WHERE E.PlatformID = :id 
                            AND E.CategoryID = M.CategoryID 
                            ORDER BY SendNo DESC LIMIT 1) <= 0)               
                    ORDER BY Priority DESC", 
            array(
                ':id' => $platform,
                ':datetime' => $noSchedule,
                ':lastSend' => $lastSendNo
            ), false);

            $ret = $this->sql->returnAllResults();
        
            if(count($ret) > 0){
                // there is at least 1 result.
                
                echo '<br />area 2<br />';
            } else {

                // 3: if all messages are already sent once or in unusable categories, check for recycled messages.
        
                $this->sql->sqlCommand("SELECT * FROM Message as M
                        WHERE M.PlatformID = :id
                        AND M.DateTime = :datetime
                        AND :lastSend - M.SendNo >= 
                            (SELECT RecycleLimit FROM Platforms WHERE ID = :id LIMIT 1) 
                        AND (:lastSend - 
                            (SELECT SendNo FROM Message as E 
                                WHERE E.PlatformID = :id 
                                AND E.CategoryID = M.CategoryID 
                                ORDER BY SendNo DESC LIMIT 1) >=
                            (SELECT Frequency FROM Category 
                                WHERE ID = M.CategoryID LIMIT 1) OR
                            (SELECT SendNo FROM Message as E 
                                WHERE E.PlatformID = :id 
                                AND E.CategoryID = M.CategoryID 
                                ORDER BY SendNo DESC LIMIT 1) <= 0)               
                        ORDER BY Priority DESC", 
                array(
                    ':id' => $platform,
                    ':datetime' => $noSchedule,
                    ':lastSend' => $lastSendNo
                ), false);

                $ret = $this->sql->returnAllResults();

                if(count($ret) > 0){
                    // there is at least 1 result.
                    
                    echo '<br />area 3<br />';
                } else {

                    // 4: if no recycled messages available, go to 2 (then 3) but find the category with the least "wait" time for next message,
                    // 4b: return that message. 
                    
                    $this->sql->sqlCommand("SELECT * FROM Message as M
                            WHERE M.PlatformID = :id
                            AND M.SendNo = -1
                            AND M.DateTime = :datetime 
                            ORDER BY Priority DESC, 
                            :lastSend - 
                            (SELECT SendNo FROM Message as E 
                                WHERE E.PlatformID = :id 
                                AND E.CategoryID = M.CategoryID 
                                ORDER BY SendNo DESC LIMIT 1) ASC", 
                    array(
                        ':id' => $platform,
                        ':datetime' => $noSchedule,
                        ':lastSend' => $lastSendNo
                    ), false);
                    
                    $ret = $this->sql->returnAllResults();

                    if(count($ret) > 0){
                        // there is at least 1 result.
                        
                        echo '<br />area 4<br />';
                    } else {
                        // 5: really, any message at this point is OK. 

                        $this->sql->sqlCommand("SELECT * FROM Message as M
                                WHERE M.PlatformID = :id
                                AND M.DateTime = :datetime 
                    AND :lastSend - M.SendNo >= 
                            (SELECT RecycleLimit FROM Platforms WHERE ID = :id LIMIT 1) 
                                ORDER BY Priority DESC, 
                            :lastSend - 
                            (SELECT SendNo FROM Message as E 
                                WHERE E.PlatformID = :id 
                                AND E.CategoryID = M.CategoryID 
                                ORDER BY SendNo DESC LIMIT 1) ASC", 
                        array(
                            ':id' => $platform,
                            ':datetime' => $noSchedule,
                            ':lastSend' => $lastSendNo
                        ), false);

                        $ret = $this->sql->returnAllResults();

                        if(count($ret) > 0){
                            // there is at least 1 result.
                            // 1b: if other messages are at the same priority, we'll need to push one back by a day. Do so for later install. 
                            // 1c: if message is a repeat, schedule this one then reschedule as per repeat.
                            // TO DO
                            
                            echo '<br />area 5<br />';
                        } 

                        // there should ALWAYS be a message to send ideally. If nothing is valid through 4, return false and an error to user 
                        // that messages do not exist and to ABORT process. 
                        
                    }
                }
            }
        }

        // take returned content, and from that choose a random one to be used, based on priority. 
        // we'll eliminate everything below the highest priority before placing. 

        $maxPriority = $ret[0]['Priority']; 

        $newRet = array();
        for($i = 0; $i < count($ret); $i++){
            // for each message, check the priority. if less than MAX, remove from running. 
            if($ret[$i]['Priority'] >= $maxPriority){
                $newRet[] = $ret[$i]; // transfer the return for the next part.
            }
        }
        $ret = '';

        $pick = mt_rand(0, count($newRet) - 1);
        
        $this->updateMessage($newRet[$pick]['ID'], $lastSendNo, $newRet[$pick]['DateTime'],
                    $newRet[$pick]['RepeatBool'], $newRet[$pick]['RepeatTimes'], $newRet[$pick]['RepeatDays'] );

        return $newRet[$pick];
    }

    private function getLastSend($platform){ // RETURN INT
        // get the last send number
        $this->sql->sqlCommand("SELECT SendNo FROM Message WHERE PlatformID = :id ORDER BY SendNo DESC LIMIT 1", 
            array(':id' => $platform), false);

        $res = $this->sql->returnResults();

        return $res['SendNo'];
    }

    private function updateMessage($id, $lastsend, $datetime, $repeatbool, $repeattimes, $repeatdays){
        // update the message to the newest send ID, as well as set the repeat date and count, if needed. 
        
        $cmd = "UPDATE Message SET SendNo = :sendNo";
        $attsArr = array(':sendNo' => ($lastsend > -1 ? $lastsend + 1 : 1));
        
        $valtest = false;

        if($repeatbool == 1 || $repeatbool == true){
            
            //echo 'DO REPEAT <br />';
            $valtest = true; 
            // there is a repeat to occur. 
            $newTime = strtotime($datetime) + 24*60*60*$repeatdays;
            
            // check time is not on a weekend 
            $weekend = false;
            while(!$weekend){
                $dayOfWeek = date('w', $newTime);
                if($dayOfWeek != 0 && $dayOfWeek != 6){
                    $weekend = true; // this is OK. 
                } else {
                    $newTime += 24*60*60; // add a day. 
                }
            }
            
            $cmd .= ", DateTime = :datetime";
            $attsArr[':datetime'] = date("Y-m-d H:i:s", $newTime);

            if($repeattimes <= 0){
                // that was actually the last one. 
                $cmd .= ", RepeatBool = 0"; // set it to false
            } else {
                // just reduce by one. 
                $cmd .= ", RepeatTimes = :times";
                $attsArr[':times'] = $repeattimes - 1;
            }

        }
        
        

        $cmd .= " WHERE ID = :id";
        $attsArr[':id'] = $id;

        echo $cmd . '<br />';

        $this->sql->sqlCommand($cmd, $attsArr, $valtest);
    
    }

    private function delayMessage($id, $datetime){
        // set a message delay of 24 hours on the current timed message.
        

        $newTime = strtotime($datetime) + 24*60*60;

        $weekend = false;
        while(!$weekend){
            $dayOfWeek = date('w', $newTime);
            if($dayOfWeek != 0 && $dayOfWeek != 6){
                $weekend = true; // this is OK. 
            } else {
                $newTime += 24*60*60; // add a day. 
            }
        }

        $cmd = "UPDATE Message SET DateTime = :datetime WHERE ID = :id";
        $attsArr = array(':id' => $id, ':datetime' => $newTime);

        $this->sql->sqlCommand($cmd, $attsArr, false);
    }
}