<?php

session_start();

require __DIR__ . '/backend/db.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/frontend/css/main_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Document</title>
</head>
<body>

    <div>
        <nav class="landing-nav">

            <div class="topnav" id="myTopnav">
                <a href="#home" class="active">Home</a>
                
                <a href="/frontend/pages/donate.php">Donate</a>
                <a href="/frontend/pages/signin.php">Sign In</a>
                <a href="Javascript:void(0);" class="icon" onclick="myFunction()">
                    <i class="fa fa-bars"></i>
                </a>
            </div>


        <!-- 
        <button>Donate</button>
        <button>SignIn</button> -->
        </nav>

        <section class="landingpage">
            <div class="landingpage-content">
                <h1>Welcome to Pawsitive</h1>
                <p>We are a non-profit organization dedicated to helping animals in need. Our mission is to provide shelter, food, and medical care to abandoned and abused animals, while also promoting responsible pet ownership and animal welfare.</p>
                <button class="donatebtn"><a href="/frontend/pages/donate.php" class="donate-button">Donate Now</a></button>
            </div>
            <div class="landingpage-image">
                <img src="/frontend/assets/section1_img.png" alt="Landing Page Image">
            </div>
        </section>   
        
        <section class="section2">
            <div class="section2-content">
                <h2>About Us</h2>
                <p>Our Shelter Is A Safe Haven For Abandoned, Neglected & Forgotten Animals. 
                We Have 19 Enclosures & Currently Have 42 Dogs & 8 Cats.
                Our Vision Is Assisting Areas Directly In Our Vicinity, Diepsloot & Olievenhoutbosch. 
                We Commit In Making A Difference</p>
            </div>
            
        </section>
    </div>
    <script src="/frontend/Javascript/script.js"></script>    
</body>
</html>