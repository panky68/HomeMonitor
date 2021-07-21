<?php
  include 'core/init.php';
  include_once('dB.php');
  //script used to read in values and insert into database


  // Keep this API Key value to be compatible with the ESP code provided in the project page. If you change this value, the ESP sketch needs to match
  $api_key_value = "tPmAT5Ab3j7F9";

  $api_key = $sensor = $location = $value1 = $value2 = $value3 = ""; //initialise the vars

  if ($_SERVER["REQUEST_METHOD"] == "POST") {   //if there is POST request
    $api_key = testInput($_POST["api_key"]);    //get the api key
    if($api_key == $api_key_value) {            //if the api key verifies with our api key
      $sensor = testInput($_POST["sensor"]);    //read in the sensor type
      $location = testInput($_POST["location"]);  //read in location
      $value1 = testInput($_POST["value1"]);    //read in temp value
      $value2 = testInput($_POST["value2"]);    //read in humidity value
      $value3 = testInput($_POST["value3"]);    //read in altitude value

      $result = insertReading($sensor, $location, $value1, $value2, $value3, 'officeSensorData'); //save data to dB
      echo $result;
    }else {
      echo "Wrong API Key provided.";       //error message for incorrect api key
    }
  }else {
    echo "No data posted with HTTP POST.";  //error msg for No POST data
  }
?>