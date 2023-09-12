<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Event Registry</title>

        <!--Stylesheets-->
        <link rel="stylesheet" href="styles/masterMain.css" />
        <link rel="stylesheet" href="styles/media-queries.css" /> 
        <link rel="stylesheet" href="styles/nav.css" />
        <link rel="stylesheet" href="styles/footer.css" />

        <!--For mainpage-->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap" rel="stylesheet">

        <!--Stylesheets-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">       
        <script src="https://kit.fontawesome.com/1d089da2a3.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <main>
        <!-- Including Our Nav for the unsigned user  -->
        <?php include "includes/nav.php" ;?>
        <div class="bg">
            <!--Heading Lines-->
            <div class="container center-content">
                <!-- Your existing HTML content goes here -->
                <h1>Streamline Your Life with <br/><span class="impact-line">Plan-To-Do</span></h1>
                <h2>Join Us or Log In to Start Crafting Your Wishlist Today</h2>
                
                <a href="login.php"><button class="button-52" role="button">Get Started</button></a>
                <!-- HTML !-->

            </div>
        </div>
        </main>
        <!-- Including Footer -->
        <?php include "includes/footer.php" ?>
    </body>
</html>
<!-- End -->
