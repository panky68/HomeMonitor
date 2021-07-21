<?php
    //With label ID, get label title into modal, and upon close, send label title back to dB
    if(isset($_POST["labelId"])){   //if ajax label has been set
        include 'core/init.php';    //include all functions for db connections, user data and general functions
        include_once('dB.php'); //functions to recieve and send data to the db
        $output = '';   //initialise string
        $result = getOutputById($_POST["labelId"]); //run query to get data for output switch
        if($result){
            $row = mysqli_fetch_assoc($result); //fetch switch data from dB
            //create output for modal
            $output .= ' 
            <div class="form-group">
                <label for="btnLabel">Button Label</label>
                <input type="text" value="'.$row['name'].'" class="form-control" id="btnLabel" name="btnLabel">
                <input type="text" value="'.$row['id'].'" class="form-control d-none" id="btnLabel" name="switchId">
            </div>';
        }
        echo $output;   //return the output data
    }
?>