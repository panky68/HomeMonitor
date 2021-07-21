<?php
    include 'core/init.php'; //include all functions for db connections, user data and general functions
    if(userLoggedIn() === true){                //has a user logged on?
        $page = "camera";     //define the page we are on
        include "includes/overall/header.php";  //include the html head section and Navbar
?>
        <div class="container">
            <h2 class="pageHeader">Camera's</h2>    <!-- CPage Title -->
            <div class="row">
                <!-- Camera Feed for Office Room from rtsp server -->
                <div class="col-md-6">      
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="https://rtsp.me/embed/T6iQNGB8/" allowfullscreen allow="autoplay"></iframe>
                    </div>
                </div>
                <!-- Camera Feed for Garage from rtsp server -->
                <div class="col-md-6">
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="https://rtsp.me/embed/hGEsZBTS/" allowfullscreen allow="autoplay"></iframe>
                    </div>
                </div>
            </div>
        </div>
<?php 
        include 'includes/overall/footer.php';  //include footer
    }else{                                      //display login page if no user has logged on
        header("Location: loginform.php");      //re-direct to login page
    }
?>