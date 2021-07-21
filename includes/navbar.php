<nav class="navbar navbar-expand-md navbar-custom fixed-top">             <!-- -->
    <a href="index.php" class="navbar-brand">Home Monitor</a>    <!-- Menu Logo (Brand) -->
    <button class="navbar-toggler" data-toggle="collapse" data-target="#navbarMenu">
        <span class="navbar-toggler-icon"></span> <!-- bootstrap's hamburger icon -->
    </button>
    <div class="collapse navbar-collapse" id="navbarMenu">                  <!-- make div collapsable -->
        <ul class="navbar-nav mr-auto">                     <!-- position menu items on the left -->
            <li class="nav-item <?php if($page === 'home'){echo 'active';} ?>"><a href="index.php" class="nav-link">Home</a></li>           <!-- menu item -->
            <li class="nav-item <?php if($page === 'camera'){echo 'active';} ?>"><a href="cameraPg.php" class="nav-link">Cameras</a></li>   <!-- menu item -->
            <li class="nav-item <?php if($page === 'office'){echo 'active';} ?>"><a href="office.php" class="nav-link">Office</a></li>      <!-- menu item -->
            <li class="nav-item <?php if($page === 'garage'){echo 'active';} ?>"><a href="garage.php" class="nav-link">Garage</a></li>      <!-- menu item -->
            <li class="nav-item <?php if($page === 'contact'){echo 'active';} ?>"><a href="contact.php" class="nav-link">Contact</a></li>   <!-- menu item -->
        </ul>
        <ul class="navbar-nav ml-auto">                     <!-- position menu items on the left -->
            <li class="nav-item"><a href="logout.php" class="nav-link">Log Out</a></li>          <!-- menu item -->
        </ul>
    </div>
</nav>