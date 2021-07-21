<?php
    //****Functions for Output****
    function updateOutput($id, $state) {    //set state of a GPIO
        global $conn;
        $sql = "UPDATE Outputs SET state='" . $state . "' WHERE id='$id'";  //create query to set GPIO state
        if($query_run = mysqli_query($conn, $sql)) {    //run and check if query ran 
            return "Output state updated successfully"; 
        }else{
            return "Error: ".$sql."<br>".$conn->error;
        }
    }
    function updateSwitchLabelById($id, $newLabel) {    //modify switch label in dB
        global $conn;
        $sql = "UPDATE `Outputs` SET `name` = '$newLabel' WHERE `id`='$id'";
        if($query_run = mysqli_query($conn, $sql)) {    //run and check if query ran 
            return $query_run;
        }else{
            return false;
        }
    }
    function getOutputById($id) {   //get Board and GPIO Details for Webpage toggle switch 
        global $conn;
        $sql = "SELECT `id`, `name`, `board`, `gpio`, `state` FROM `Outputs` WHERE `id`='$id'";
        if($query_run = mysqli_query($conn, $sql)) {    //run and check if query ran 
            return $query_run;
        }else{
            return false;
        }
    }
    function getOutputByBoardGPIO($board, $GPIO) {  //get GPIO details
        global $conn;
        $sql = "SELECT `id`, `name`, `board`, `gpio`, `state` FROM `Outputs` WHERE `board`='$board' AND `gpio`='$GPIO'";
        if($query_run = mysqli_query($conn, $sql)) {    //run and check if query ran 
            return $query_run;
        }else{
            return false;
        }
    }
    function getAllOutputs() {      //get all GPIO details
        global $conn;
        $sql = "SELECT id, name, board, gpio, state FROM Outputs ORDER BY board";
        if($query_run = mysqli_query($conn, $sql)) {    //run and check if query ran 
            return $query_run;
        }else{
            return false;
        }
    }
    function getAllOutputStates($board) {   //get a boards GPIO Pin and its state
        global $conn;
        $sql = "SELECT gpio, state FROM Outputs WHERE board='$board'";
        if($query_run = mysqli_query($conn, $sql)) {    //run and check if query ran 
            return $query_run;
        }else{
            return false;
        }
    }
    function updateLastBoardTime($board) {  //update last request time for board
        global $conn;
        $sql = "UPDATE Boards SET last_request=now() WHERE board='$board'";
        if($query_run = mysqli_query($conn, $sql)) {    //run and check if query ran 
            return "Output state updated successfully";
        }else{
            return "Error: ".$sql."<br>".$conn->error;
        }
    }
    function getAllBoards() {       //get Board details from dB
        global $conn;
        $sql = "SELECT board, last_request FROM Boards ORDER BY board";
        if($query_run = mysqli_query($conn, $sql)) {    //run and check if query ran 
            return $query_run;
        }else{
            return false;
        }
    }
    function getBoard($board) {     //get board information 
        global $conn;
        $sql = "SELECT board, last_request FROM Boards WHERE board='$board'";   //query dB to get board and board last request time 
        if($query_run = mysqli_query($conn, $sql)) {    //run and check if query ran 
            return $query_run;          //send board and last request time back
        }else{
            return false;
        }
    }
    
    //****Functions for Temparature, Humidity and Pressure inputs****
    function getLastReadings($table) {          //get sensor details from dB
        global $conn;
        $sql = "SELECT id, sensor, location, value1, value2, value3, reading_time FROM $table order by reading_time desc limit 1" ; 
        if($query_run = mysqli_query($conn, $sql)){     //run and check if query ran 
            return mysqli_fetch_assoc($query_run);
        }else{
            return false;
        }
    }
    function insertReading($sensor, $location, $value1, $value2, $value3, $table) {     //insert sensor details into dB
        global $conn;
        $sql = "INSERT INTO $table (sensor, location, value1, value2, value3) VALUES ('$sensor', '$location', '$value1', '$value2', '$value3')";
        if ($query_run = mysqli_query($conn, $sql)) {   //run and check if query ran 
            return "New record created successfully";
        }else {
            return "Error: ".$sql."<br>".$conn->error;
        }
    }
?>