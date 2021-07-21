<?php
    include 'core/init.php';    //include all functions for db connections, user data and general functions
    include_once('dB.php');     //functions to recieve and send data to the db

    $action = $id = $name = $gpio = $state = "";    //initialise vars

    if($_SERVER["REQUEST_METHOD"] == "GET") { //if a HTTP GET request set
        
        $action = testInput($_GET["action"]);   //get type of action to be performed from URL
        
        if($action == "outputs_state") {            //request from arduino for GPIO and states
            $board = testInput($_GET["board"]);     //get board id from URL
            $result = getAllOutputStates($board);   //get all of board-2's gpio's and their states from dB
            if($result) {                           //check valid data returned
                while ($row = $result->fetch_assoc()) {     //itterate through all GPIO's
                    $rows[$row["gpio"]] = $row["state"];    //place in associate array
                }
            }
            echo json_encode($rows);        //encode as JSON and display on page
            $result = getBoard($board);     //get board information
            if($result->fetch_assoc()) {    //check if results 
                updateLastBoardTime($board); //update current time to last request time in dB       
            }
        }else if($action == "output_update") {      //GET Request to update a GPIO state in dB
            $id = testInput($_GET["id"]);           //id of button to update
            $state = testInput($_GET["state"]);     //State of GPIO
            $result = updateOutput($id, $state);    //change state in GPIO in dB
            echo $result;                           
        }else{
            echo "Invalid HTTP request.";
        }
    }
?>
