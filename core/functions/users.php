<?php
    function userLoggedIn(){    //to check if a valid user logged on
        return (isset($_SESSION['user_id'])) ? true : false;    //if session for user id set, return true
    }
    
    function userExists($username){ //to check if fed in username exists in dB
        global $conn;

        $query = "SELECT * FROM `users` WHERE `username` = '$username'";    //create query to check username in dB
        $queryRun = mysqli_query($conn, $query);                            //run query
        $queryNumRows = mysqli_num_rows($queryRun);                         //count row(s) returned
        return ($queryNumRows == 1) ? true : false;                         //if a row returned, therefore username exists
    }
    function user_id_from_username($username){        //get a user id from fed in username from dB                     
        global $conn;
    
        $query = "SELECT `id` FROM `users` WHERE `username`='$username'";   //create query to get id from supplied username from dB
        $queryRun = mysqli_query($conn, $query);            //run query
        $userId = mysqli_fetch_assoc($queryRun);            //get assoc array from dB
        return $userId['id'];                               //return user id
    }
    function login($username, $password){       //checks if users credentials match and returns id if it does
        global $conn;

        $user_id = user_id_from_username($username);    //get user id from username
        $password = md5($password);                     //hash password
        $query = "SELECT * FROM `users` WHERE `username`='$username' AND `password` = '$password'"; //query to get info from username and password
        $queryRun = mysqli_query($conn, $query);    //run query 
        $numRows = mysqli_num_rows($queryRun);      //get rows returned from dB
        return ($numRows == 1) ? $user_id : false;  //if 1 row returned, send user id
    }
?>