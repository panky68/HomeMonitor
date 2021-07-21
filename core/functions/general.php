<?php
    function testInput($data){      //to prevent sql injection
        global $conn;

        $data = trim($data);                //remove white spaces
        $data = stripcslashes($data);       //remove slashes
        $data = htmlspecialchars($data);    //return all input into string format, ie turn all possible input html elements as string
        return $data;                       //return modified data
    }
    function createBtn($GPIO){  //create and format button
        global $board;          //global var for board id
        //$GPIO = $GPIO;
        $btn = null;    //initialise var
        $result = getOutputByBoardGPIO($board, $GPIO);  //get information by board and gpio
        if($result){
            $row = $result->fetch_assoc();  
            $button_checked = ($row["state"] == '1') ? 'checked' : '' ; //if button 'on', checkbox value 'checked'
            //create button and label, onchange event handler to run function updateOutput
            $btn .=
                '<h3><a id="'.$row["id"].'" class="editSwitchLabel">'.$row["name"].'</a></h3>
                <label class="switch">
                    <input type="checkbox" onchange="updateOutput(this)" id="'.$row["id"].'" '.$button_checked .'>
                    <span class="slider"></span>
                </label>';
        }
        return $btn;
    }
?>