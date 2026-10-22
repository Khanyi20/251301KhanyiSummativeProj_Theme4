<?php

session_start();

// require_once __DIR__ . '/../../backend/db.php';

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

        <section class="section3">
            <div class="section3-content">
                <h2>Our Facility</h2>
                <p>Scroll through photos of our facility</p>
            </div>

            <div class="slideshow-container">

                <!-- Full-width images with number and caption text -->
                <div class="mySlides fade">
                    <div class="numbertext">1 / 4</div>
                    <img src="/frontend/assets/slidepic1.png" style="width:100%">
                    
                </div>

                <div class="mySlides fade">
                    <div class="numbertext">2 / 4</div>
                    <img src="/frontend/assets/slidepic2.png" style="width:100%">
                    
                </div>

                <div class="mySlides fade">
                    <div class="numbertext">3 / 4</div>
                    <img src="/frontend/assets/slidepic3.png" style="width:100%">
                    
                </div>

                <div class="mySlides fade">
                    <div class="numbertext">4 / 4</div>
                    <img src="/frontend/assets/slidepic4.png" style="width:100%">
                    
                </div>

                <!-- Next and previous buttons -->
                <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                <a class="next" onclick="plusSlides(1)">&#10095;</a>
            </div>
                <br>

                <!-- The dots/circles -->
            <div style="text-align:center">
                <span class="dot" onclick="currentSlide(1)"></span>
                <span class="dot" onclick="currentSlide(2)"></span>
                <span class="dot" onclick="currentSlide(3)"></span>
                <span class="dot" onclick="currentSlide(4)"></span>
            </div>

        </section>

        <section class="section4">
            <div class="section4-content">
                <h2>Our People</h2>

                <div>
                    <h3>Directors</h3>
                    <div class="row">
                    <div class="column">
                        <img src="/frontend/assets/staffimg1.png" alt="image 1" style="width:100%">
                    </div>
                    <div class="column">
                        <img src="/frontend/assets/staffimg2.png" alt="image 2" style="width:100%">
                    </div>
                    <div class="column">
                        <img src="/frontend/assets/staffimg6.png" alt="image 3" style="width:100%">
                    </div>
                    </div>
                </div>

                <div>
                    <h3>Staff</h3>
                    <div class="row">
                    <div class="column">
                        <img src="/frontend/assets/staffimg10.png" alt="image 4" style="width:100%">
                    </div>
                    <div class="column">
                        <img src="/frontend/assets/staffimg9.png" alt="image 5" style="width:100%">
                    </div>
                    <div class="column">
                        <img src="/frontend/assets/staffimg8.png" alt="image 6" style="width:100%">
                    </div>
                    <div class="column">
                        <img src="/frontend/assets/staffimg7.png" alt="image 7" style="width:100%">
                    </div>
                    </div>
                </div>

                <div>
                    <h3>Staff</h3>
                    <div class="row">
                    <div class="column">
                        <img src="/frontend/assets/staffimg5.png" alt="image 8" style="width:100%">
                    </div>
                    <div class="column">
                        <img src="/frontend/assets/staffimg4.png" alt="image 9" style="width:100%">
                    </div>
                    <div class="column">
                        <img src="/frontend/assets/staffimg3.png" alt="image 10" style="width:100%">
                    </div>
                    <div class="column">
                        <img src="/frontend/assets/staffimg1.png" alt="image 11" style="width:100%">
                    </div>
                    <div class="column">
                        <img src="/frontend/assets/staffimg2.png" alt="image 12" style="width:100%">
                    </div>
                    </div>
                </div>
                
            </div>
        </section>    

    </div>
    <script src="/frontend/Javascript/script.js"></script>    
</body>
</html>