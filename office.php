<?php
    $board = 1;                     //Aurduino board id
    include 'core/init.php';        //initialisation, general and db functions
    if(userLoggedIn() === true){    //user logged on?
        include_once('dB.php');     //functions to recieve and send data to the db
        
        //Get last values for the temp, humidity and time readings from sensorData table
        $last_reading = getLastReadings('officeSensorData');//Read in Last Temparature, Humidity and Pressure inputs
        $last_reading_temp = $last_reading["value1"];       //Temparature input
        $last_reading_humi = $last_reading["value2"];       //Humidity input
        $last_reading_time = $last_reading["reading_time"]; //Time of Last Read

        //process modal (editSwitchLabel modal) form data
        if(isset($_POST['editLabel'])){     //modal submit
            $newLabel = $_POST["btnLabel"]; //fetch data from modal form, Label tag
            $id = $_POST["switchId"];       //fetch switch id from modal form's hiddden input
            testInput($newLabel);           //prevent sql injection
            if(empty($newLabel)){           //check for empty value from modals label input
                $newLabelErr = 'Please provide value';  //error message
            }else{
                updateSwitchLabelById($id, $newLabel);  //update dB and switch label
            }
        }
        
        //create output button for each ESP32 GPIO
        $esp32GPIOPins = array(2, 4, 15, 3, 16, 32, 33, 17);        //arduino GPIO's pins
        $arrayCount = 1;                                            //initialise array count
        foreach($esp32GPIOPins as $GPIOPin){                        //itterate through array
            ${'btn' . $arrayCount} = createBtn($GPIOPin);           //create a button for each pin, var $btn(x) created, using variable variables
            $arrayCount++;                                          //increment count
        }
        
        $page = 'office'; 
        include 'includes/overall/header.php';  //include header
    ?>
        <!-- editSwitchLabel Modal -->
        <div class="modal fade" id="editSwitchLabelModal" tabindex="-1">
            <div class="modal-dialog">  <!-- sets widths and margins of modal -->
                <div class="modal-content">
                    <!-- Modal header -->
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Button Label</h5>  <!-- Modal Title -->
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">    <!-- Modal Header close button -->
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="office.php" method="POST">
                        <!-- Modal Body -->
                        <div class="modal-body" id="labelDetails"><!-- Modal Body details defined by JS Script, dependent on which label clicked -->
                        </div>
                        <!-- Modal Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> <!-- Footer close button -->
                            <button type="submit" name="editLabel" class="btn btn-primary">Update</button>    <!-- Footer Update Button -->
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="container">
            <h2 class="pageHeader">Office Room</h2> <!-- Page Title -->
            <div class="row">
                <!-- Camera Feed -->
                <div class="col-md-8">      
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="https://rtsp.me/embed/T6iQNGB8/" allowfullscreen allow="autoplay"></iframe>  <!-- office ip camera feed from rtsp server -->
                    </div>
                </div>
                <!-- Output switches -->
                <div class="col-md-4">
                    <div class="row controlsWrapper">
                        <div class="btnWrapper">
                            <?php echo $btn1; ?>     <!-- Display output switch 1 -->
                        </div>
                        <div class="btnWrapper">
                            <?php echo $btn2; ?>     <!-- Display output switch 2 -->
                        </div>
                        <div class="btnWrapper">
                            <?php echo $btn3; ?>     <!-- Display output switch 3 -->
                        </div>
                        <div class="btnWrapper">
                            <?php echo $btn4; ?>     <!-- Display output switch 4 -->
                        </div>
                        <div class="btnWrapper">
                            <?php echo $btn5; ?>     <!-- Display output switch 5 -->
                        </div>
                        <div class="btnWrapper">
                            <?php echo $btn6; ?>     <!-- Display output switch 6 -->
                        </div>
                        <div class="btnWrapper">
                            <?php echo $btn7; ?>     <!-- Display output switch 7 -->
                        </div>
                        <div class="btnWrapper">
                            <?php echo $btn8; ?>     <!-- Display output switch 8 -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Temparature Gauge -->
                <div class="box gauge--1">
                    <h3 class="gaugeHeader">TEMPERATURE</h3>    <!-- Temparature Header -->
                    <div class="mask">  <!-- Temparature Gauge -->
                        <div class="semi-circle"></div>
                        <div class="semi-circle--mask"></div>
                    </div>
                    <p id="temp">--</p>    <!-- Temparature Value -->
                </div>
                <!-- Humidity Gauge -->
                <div class="box gauge--2">
                    <h3 class="gaugeHeader">HUMIDITY</h3>   <!-- Humidity Header -->
                    <div class="mask">  <!-- Humidity Gauge -->
                        <div class="semi-circle"></div>
                        <div class="semi-circle--mask"></div>
                    </div>
                    <p id="humi">--</p>        <!-- Humidity Value -->
                </div>
            </div>
            <p>Last reading: <?php echo $last_reading_time; ?></p>  <!-- Time of Last Readings from table -->
        </div>
        <?php include 'includes/overall/footer.php'; ?>

        <!-- Script to send data (id) to modal and then displaying it details -->
        <script>
            $(document).ready(function(){                   //execute when DOM has loaded all elements
                $('.editSwitchLabel').click(function(){     //when any switch label has been clicked
                    var labelId = $(this).attr('id');       //get switch label's id and store 
                    $.ajax({                                //query code for ajax
                        url:"modalLabelDetails.php",        //server script file to retreive and display label details in modal
                        method:"POST",                      //use POST method to send to modalLabelDetails.php
                        data:{labelId:labelId},             //data to be sent to modalLabelDetails.php 
                        success:function(data){             //if request succeeds, function is called
                            $('#labelDetails').html(data);  //display data in modal body from server file modalLabelDetails.php
                            $('#editSwitchLabelModal').modal('show');   //display modal with id = editSwitchLabelModal
                        }
                    });
                });
            });
        </script>
        <script>
            //Update switches
            function updateOutput(element) { //HTTP Request to SET Toggle Switch Outputs
                var xhr = new XMLHttpRequest(); //create instance for HTTP Request
                if(element.checked){            //check if switch is on/off
                    xhr.open("GET", "officeRoomOutputs.php?action=output_update&id="+element.id+"&state=1", true);  //set GET request to update switch switch to on
                }else{
                    xhr.open("GET", "officeRoomOutputs.php?action=output_update&id="+element.id+"&state=0", true);  //set GET request to update switch switch to off
                }
                xhr.send();
            }
            //For Temparature, Humidity and Pressure inputs
            var value1 = <?php echo $last_reading_temp; ?>; //get last temp reading from db
            var value2 = <?php echo $last_reading_humi; ?>; //get last humidity reading from db
            setTemperature(value1);                         //function to set temp reading in gauge
            setHumidity(value2);                            //function to set humidity reading in gauge

            //update the current temp value on gauge
            function setTemperature(curVal){    //updates the current temp value on the curve
                var minTemp = -10.0;            //min value for Temperature in Celsius
                var maxTemp = 50.0;             //max value for Temperature in Celsius

                var newVal = scaleValue(curVal, [minTemp, maxTemp], [0, 180]);
                $('.gauge--1 .semi-circle--mask').attr({
                    style: '-webkit-transform: rotate(' + newVal + 'deg);' +
                    '-moz-transform: rotate(' + newVal + 'deg);' +
                    'transform: rotate(' + newVal + 'deg);'
                });
                $("#temp").text(curVal + ' ºC');
            }

            //update the current humidity value on gauge
            function setHumidity(curVal){
                var minHumi = 0;    //min value for Temperature in %
                var maxHumi = 100;  //max value for Temperature in %

                var newVal = scaleValue(curVal, [minHumi, maxHumi], [0, 180]);
                $('.gauge--2 .semi-circle--mask').attr({
                    style: '-webkit-transform: rotate(' + newVal + 'deg);' +
                    '-moz-transform: rotate(' + newVal + 'deg);' +
                    'transform: rotate(' + newVal + 'deg);'
                });
                $("#humi").text(curVal + ' %');
            }
            function scaleValue(value, from, to) {
                var scale = (to[1] - to[0]) / (from[1] - from[0]);
                var capped = Math.min(from[1], Math.max(from[0], value)) - from[0];
                return ~~(capped * scale + to[0]);
            }
        </script>
<?php
    }else{
        header('Location: loginform.php');  //redirect user to login page if user not logged in
    }
?>
