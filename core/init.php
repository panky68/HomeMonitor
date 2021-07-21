<?php
    session_start();

    //Google ReCaptcha V3 Keys
    define('SITE_KEY', '6LfBwiQaAAAAAGbVEFJtlzARrGVLxVjOQqWTkTKo'); //site key for
    define('SECRET_KEY', '6LfBwiQaAAAAAFyIbKfjYSG_A45upsrh8NRr9BJL'); //secret key for goggle reCaptcha v

    //error_reporting(0);
    require 'database/connect.php';   //connection to db
    require 'functions/general.php';  //include general functions to be used
    require 'functions/espdata.php';    //not sure what this is yet
    require 'functions/users.php';     //general functions for the user

    $errors = array();  //array to keep all possible errors
?>