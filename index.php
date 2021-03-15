<?php

require_once("config.php");

$manager = new manager();

?>
<!doctype html>
<html>
    <head>
        <title>Social Media Logger</title>

        <link rel="stylesheet" href="css/styles.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="js/scripts.js"></script>
    </head>
    <body>
        <div id="content">
        <form action="authenticate.php" method="POST" enctype="multipart/form-data" id="messageform">
            <label>Platform</label><div id="PlaftormChecks">
                <?php 
                    // we'll have to get the platforms here.
                    $val = $manager->getPlatforms();

                    foreach($val as $k => $v){
                        echo '<div><input type="checkbox" class="Platform" name="Platform[]" id="' . $k . '" value="' . $k . '" /><label for="' . $k . '">' . $v . '</label></div>';
                    }

                ?>
            </div><span id="perr" class="error">At least 1 platform required.</span><br />
            <label for="Category">Category</label> <select name="Category" id="Category">
                <option value="-1">- Select -</option>
                <?php 
                    // we'll have to get the platforms here. 
                    $val = $manager->getCategories();

                    foreach($val as $k => $v){
                        echo '<option value="' . $k . '">' . $v . '</option>';
                    }

                ?>
            </select><span id="cerr" class="error">Category Required.</span><br />
            
            <label for="Message">Message<span id="lim"></span></label> <textarea name="Message" id="Message"></textarea><span id="merr" class="error">Message Required.</span><span id="merrmax" class="error">Message Too Long.</span><br />
            <label for="UseDateTime">Use Specific date/time?</label> <input name="UseDateTime" id="UseDateTime" type="checkbox" /><br />
            <div id="datetime">
            <label>Date & Time</label> 
            <select name="DateMonth">
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">July</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>
            <select name="DateDay">
                <?php  
                    for($i = 1; $i < 32; $i++)
                        echo '<option value="' . $i . '">' . $i . '</option>';
                ?>
            </select>  
            <select name="DateYear">
                <?php  
                    for($i = date("Y"); $i < date("Y") + 50; $i++)
                        echo '<option value="' . $i . '">' . $i . '</option>';
                ?>
            </select>
            <select name="DateTime">
                <?php  
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

                        echo '<option value="' . $h24time . '">' . $h12time . '</option>';
                    }
                ?>
            </select>
            </div>
            
            <label for="Priority">Priority (1-100)</label> <input name="Priority" id="Priority" type="text" /><br />
            <label for="RepeatMessage">Set Up Message Repeat?</label> <input name="RepeatMessage" id="RepeatMessage" type="checkbox" /><br />
            <div id="repeater">
            <label for="RepeatDays">Days Between Repeat</label> <input name="RepeatDays" id="RepeatDays" type="text" /><br />
            <label for="RepeatCount">Repeat Count</label> <input name="RepeatCount" id="RepeatCount" type="text" /><br />
            </div>
            <label for="Image">Images</label><input type="file" name="Image[]" id="Image" multiple="multiple" /><br />
            <input type="submit" name="AddLine" id="AddLine" />
        </form>

        <h3>Add Platform</h3>
        <p>
        <em>Platform for which message is added, such as MEP's Twitter or Spill Ninja's. API link is for future use. Recycle limit is how many messages should be sent before a message can be reused, if there isn't enough messages existing. Character limit is the platform's limit. </em>
        </p>
        <form action="authenticate.php" method="POST" enctype="multipart/form-data">
            <label for="PlatformName">Platform Name</label> <input name="PlatformName" id="PlatformName" type="text" /><br />
            <label for="APILink">API Link</label> <input name="APILink" id="APILink" type="text" /><br />
            <label for="RecycleLimit">Recycle Limit</label> <input name="RecycleLimit" id="RecycleLimit" type="text" /><br />
            <label for="CharacterLimit">Character Limit</label> <input name="CharacterLimit" id="CharacterLimit" type="text" /><br />
            <input type="submit" name="AddPlatform" />
        </form>

        <h3>Add Category</h3>
        <p>
        <em>Category of message to be displayed. Post frequency is considered as post this category for every X posts created (so Frequency 3 would be every 4th message). This number may be ignored if sufficient messages do not exist. Associated platforms are for linking categories. ONLY categories that are linked will receive messages from this category.</em>
        </p>
        <form action="authenticate.php" method="POST" enctype="multipart/form-data">
            <label for="CategoryName">Category Name</label> <input name="CategoryName" id="CategoryName" type="text" /><br />
            <label>Associated Platforms</label><div id="PlaftormChecks">
                <?php 
                    // we'll have to get the platforms here.
                    $val = $manager->getPlatforms();

                    foreach($val as $k => $v){
                        echo '<div><input type="checkbox" name="Platform[]" id="' . $k . '" value="' . $k . '" /><label for="' . $k . '">' . $v . '</label></div>';
                    }

                ?>
            </div>
            <label for="PostFrequency">Post Frequency</label> <input name="PostFrequency" id="PostFrequency" type="text" /><br />
            <input type="submit" name="AddCategory" />
        </form>

        <h3> Export CSV </h3>

        <p><em>To Be Built</em></p>

        <form action="authenticate.php" method="POST" enctype="multipart/form-data">
        <label for="Platform">Platform</label> <select name="Platform" id="Platform">
                <option value="-1">- Select -</option>
                <?php 
                    // we'll have to get the platforms here.
                    $val = $manager->getPlatforms();

                    foreach($val as $k => $v){
                        echo '<option value="' . $k . '">' . $v . '</option>';
                    }

                ?>
            </select><span id="perr" class="error">Platform Required.</span><br />
        <div id="datetimecsv">
        <label>Start Date</label> 
            <select name="DateMonth">
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">July</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>
            <select name="DateDay">
                <?php  
                    for($i = 1; $i < 32; $i++)
                        echo '<option value="' . $i . '">' . $i . '</option>';
                ?>
            </select>  
            <select name="DateYear">
                <?php  
                    for($i = date("Y"); $i < date("Y") + 50; $i++)
                        echo '<option value="' . $i . '">' . $i . '</option>';
                ?>
            </select>
        </div>
            <label for="DaysToCreate">Days To Create</label> <input name="DaysToCreate" id="DaysToCreate" value="28" type="text" /><br />
            <input type="submit" name="MakeCSV" />
        </form>

        </div>
    </body>
</html>