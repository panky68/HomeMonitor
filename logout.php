<?php
    session_start();
    session_destroy();  //destroy all sessions
    header('Location: index.php');  //redirect to home page
?>