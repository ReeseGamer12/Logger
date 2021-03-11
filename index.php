<?php

require_once("config.php");

?>

<form action="authenticate.php" method="POST">

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