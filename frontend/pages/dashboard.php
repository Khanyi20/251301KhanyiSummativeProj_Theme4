<?php
session_start();

// Only signed-in customers may view this page.
if (empty($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

$userName = $_SESSION['user_name'] ?? 'Pawsitive member';
$userEmail = $_SESSION['user_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/frontend/css/common.css">
    <link rel="stylesheet" href="/frontend/css/customer.css">
    <title>My Dashboard</title>
</head>
<body>

<header class="page-header">
    <h1>Pawsitive</h1>

    <nav>
        <a href="/frontend/pages/dashboard.php" aria-current="page">Home</a>
        <a href="/frontend/pages/animals.php">Adopt</a>
        <a href="/frontend/pages/applications.php">Applications</a>
        <a href="/frontend/pages/mybookings.php">Bookings</a>
        <a href="/frontend/pages/signout.php">Sign Out</a>
    </nav>
</header>

<main class="page-container">

    <section class="welcome">
        <p class="eyebrow">YOUR PAWSITIVE JOURNEY</p>

        <h2>
            Welcome,
            <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>!
        </h2>

        <p class="muted">
            Find your new best friend, follow your adoption journey,
            and help animals in need.
        </p>

        <a class="button" href="/frontend/pages/animals.php">
            Meet the Animals
        </a>
    </section>

    <h2 class="section-title">What would you like to do?</h2>

    <section class="card-grid">
        <article class="card">
            <h3>Meet the Animals</h3>
            <p>Discover animals waiting for loving homes.</p>
            <a class="button" href="/frontend/pages/animals.php">
                Browse Animals
            </a>
        </article>

        <article class="card">
            <h3>My Applications</h3>
            <p>Check the status of your adoption applications.</p>
            <a class="button"
               href="/frontend/pages/applications.php">
                View Applications
            </a>
        </article>

        <article class="card">
            <h3>Book a Visit</h3>
            <p>Arrange a visit to meet the animals.</p>
            <a class="button"
               href="/frontend/pages/book_visit.php">
                Book a Visit
            </a>
        </article>

        <article class="card">
            <h3>Make a Donation</h3>
            <p>Support shelter food, care and veterinary needs.</p>
            <a class="button" href="/frontend/pages/donate.php">
                Donate
            </a>
        </article>
    </section>

</main>


<footer>
    <p>&copy; <?= date('Y') ?> Pawsitive Animal Shelter. Every paw deserves a home.</p>
</footer>

</body>
</html>