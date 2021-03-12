<?php

require_once("config.php");

$manager = new manager();

?>
<!doctype html>
<html>
    <head>
        <title>Social Media Logger</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="js/scripts.js"></script>
    </head>
    <body>

        <form action="authenticate.php" method="POST">
            <label for="Platform">Platform</label> <select name="Platform" id="Platform">
                <option value="-1">- Select -</option>
                <?php 
                    // we'll have to get the platforms here.
                    $val = $manager->getPlatforms();

                    foreach($val as $k => $v){
                        echo '<option value="' . $k . '">' . $v . '</option>';
                    }

                ?>
            </select><br />
            <label for="Category">Category</label> <select name="Category" id="Category">
                <option value="-1">- Select -</option>
                <?php 
                    // we'll have to get the platforms here. 
                    $val = $manager->getCategories();

                    foreach($val as $k => $v){
                        echo '<option value="' . $k . '">' . $v . '</option>';
                    }

                ?>
            </select><br />
            
            <label for="Message">Message</label> <textarea name="Message" id="Message"></textarea><br />
            <label for="UseDateTime">Use Specific date/time?</label> <input name="UseDateTime" id="UseDateTime" type="checkbox" /><br />
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
                        
                        echo '<option value="' . $h24time . '">' . $h12time . '</option>';
                    }
                ?>
            </select>
            <br />
            
            <label for="Priority">Priority (1-100)</label> <input name="Priority" id="Priority" type="text" /><br />
            <label for="RepeatMessage">Set Up Message Repeat?</label> <input name="RepeatMessage" id="RepeatMessage" type="checkbox" /><br />
            <label for="RepeatDays">Days Between Repeat</label> <input name="RepeatDays" id="RepeatDays" type="text" /><br />
            <label for="RepeatCount">Repeat Count</label> <input name="RepeatCount" id="RepeatCount" type="text" /><br />
            
            <div class="imagefields"></div>
            <a href="#" id="addImageField">Add Image</a><br />
            <input type="submit" name="AddLine" />
        </form>

        <h3>Add Platform</h3>
        <form action="authenticate.php" method="POST">
            <label for="PlatformName">Platform Name</label> <input name="PlatformName" id="PlatformName" type="text" /><br />
            <label for="APILink">API Link</label> <input name="APILink" id="APILink" type="text" /><br />
            <label for="RecycleLimit">Recycle Limit</label> <input name="RecycleLimit" id="RecycleLimit" type="text" /><br />
            <label for="CharacterLimit">Character Limit</label> <input name="CharacterLimit" id="CharacterLimit" type="text" /><br />
            <input type="submit" name="AddPlatform" />
        </form>

        <h3>Add Category</h3>
        <form action="authenticate.php" method="POST">
            <label for="CategoryName">Category Name</label> <input name="CategoryName" id="CategoryName" type="text" /><br />
            <input type="submit" name="AddCategory" />
        </form>

        <h3> Export CSV </h3>

        <p><em>To Be Built</em></p>

    </body>
</html>