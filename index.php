<?php

require_once("config.php");

$manager = new manager();

//$date = new DateTime('2019-12-24');
//echo $date->format('Y-m-d H:i:s');

$timezone  = -5; //(GMT -5:00) EST (U.S. & Canada) 

$now = time() + 3600*($timezone+date("I"));
//echo gmdate("Y-m-d H:i:s", $now); 



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
        <h3>Add Message</h3>
        <form action="authenticate.php" method="POST" enctype="multipart/form-data" id="messageform">
            <label>Platform</label><div id="PlaftormChecks">
                <?php outputPlatformCheck(); ?>
            </div><span id="perr" class="error">At least 1 platform required.</span><br />
            <label for="Category">Category</label> <select name="Category" id="Category">
                <option value="-1">- Select -</option>
                <?php outputCategoryOptions(); ?>
            </select><span id="cerr" class="error">Category Required.</span><br />
            
            <label for="Message">Message<span id="lim"></span></label> <textarea name="Message" id="Message"></textarea><span id="merr" class="error">Message Required.</span><span id="merrmax" class="error">Message Too Long.</span><br />
            <label for="UseDateTime">Use Specific date/time?</label> <input name="UseDateTime" id="UseDateTime" type="checkbox" /><br />
            <div id="datetime">
                <label>Date & Time</label> 
                <select name="DateMonth"> <?php outputMonths(); ?> </select>
                <select name="DateDay"> <?php outputDays(); ?> </select>  
                <select name="DateYear"> <?php outputYear(); ?> </select>
                <select name="DateTime"> <?php outputOnlyTimes(); ?> </select>
            </div>
            
            <label for="Priority">Priority (1-100)</label> <input name="Priority" id="Priority" type="text" value="1" /><br />
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
            <label>Associated Categories</label><div id="PlaftormChecks">
                <?php outputCategoryCheck(); ?>
            </div>
            <label for="APILink">API Link</label> <input name="APILink" id="APILink" type="text" /><br />
            <label for="RecycleLimit">Recycle Limit</label> <input name="RecycleLimit" id="RecycleLimit" type="text" value="200" /><br />
            <label for="CharacterLimit">Character Limit</label> <input name="CharacterLimit" id="CharacterLimit" type="text" /><br />
            <input type="submit" name="AddPlatform" />
        </form>

        <h3>Add Platform Schedule Time</h3>
        <p>
        <em>Scheduled time from monday-friday when messages will be sent. </em>
        </p>
        <form action="authenticate.php" method="POST" enctype="multipart/form-data">
            <label>Platform</label><div id="PlaftormChecks">
                <?php outputPlatformCheck(); ?>
            </div>
            <label for="PTime">Time</label>
            <select name="PTime" id="PTime"> <?php outputTime(); ?> </select>
            <input type="submit" name="AddPlatformTime" />
        </form>

        <h3>Add Category</h3>
        <p>
        <em>Category of message to be displayed. Post frequency is considered as post this category for every X posts created (so Frequency 3 would be every 4th message). This number may be ignored if sufficient messages do not exist. Associated platforms are for linking categories. ONLY categories that are linked will receive messages from this category.</em>
        </p>
        <form action="authenticate.php" method="POST" enctype="multipart/form-data">
            <label for="CategoryName">Category Name</label> <input name="CategoryName" id="CategoryName" type="text" /><br />
            <label>Associated Platforms</label><div id="PlaftormChecks">
                <?php outputPlatformCheck(); ?>
            </div>
            <label for="PostFrequency">Post Frequency</label> <input name="PostFrequency" id="PostFrequency" type="text" /><br />
            <input type="submit" name="AddCategory" />
        </form>

        <h3> Export CSV </h3>

        <p><em>To Be Built</em></p>

        <form action="authenticate.php" method="POST" enctype="multipart/form-data">
        <label for="Platform">Platform</label> <select name="Platform" id="Platform">
                <option value="-1">- Select -</option>
                <?php outputPlatformOptions(); ?>
            </select><span id="perr" class="error">Platform Required.</span><br />
        <div id="datetimecsv">
        <label>Start Date</label> 
            <select name="DateMonth">  <?php outputMonths(); ?> </select>
            <select name="DateDay"> <?php outputDays(); ?> </select>  
            <select name="DateYear"> <?php outputYear(); ?> </select>
        </div>
            <label for="DaysToCreate">Days To Create</label> <input name="DaysToCreate" id="DaysToCreate" value="28" type="text" /><br />
            <input type="submit" name="MakeCSV" />
        </form>

        </div>
        <div id="sidebar">
            <h3>Categories:</h3>
            <?php
                $manager = new manager(); // generate manager to get info. 
                $categories = $manager->getCategories(); // get all cats as id->name

                foreach($categories as $k => $v){
                    // output the data on a per-category basis.
                    echo '<p><strong>' . $v . '</strong></p>';

                    $MSG = $manager->getCatByID($k);

                    foreach($MSG as $q => $a){
                        echo $q . ': ' . $a . '<br />';
                    }

                }
            ?>
        </div>
    </body>
</html>