<?php
    include 'core/init.php'; //include all functions for db connections, user data and general functions
    if(userLoggedIn() === true){                //check user logged on?
        $page = 'home';     //define the page we are on
        include 'includes/overall/header.php';  //include the html head section and Navbar
?>
        <div class="container">                  <!-- home page container -->
            <h2>Panky's Home Monitor System</h2>
        </div>
<?php 
        include 'includes/overall/footer.php';  //include footer
    }else{                                      
        header('Location: loginform.php');      //display login page if no user has logged on
    }
?>