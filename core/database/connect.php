<?php
    $servername = "localhost:3307";     
    $dbname = "esp_data";               //Database name
    $username = "root";                 //Database user
    $password = "Liam_Par_68";          //Database user password
    $dbConnError  = 'DataBase Connection Error';

    $conn = mysqli_connect($servername, $username, $password) or die($dbConnError);
    mysqli_select_db($conn, $dbname) or die($dbConnError);
?>