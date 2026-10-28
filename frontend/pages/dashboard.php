
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

    <title>My Dashboard | Pawsitive</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f7f4;
            color: #26352d;
        }

        header {
            background: #315c45;
            color: white;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        header h1 {
            margin: 0;
            font-size: 27px;
        }

        header a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .logout {
            border: 1px solid white;
            padding: 9px 15px;
            border-radius: 7px;
        }

        main {
            max-width: 1100px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .welcome {
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 3px 12px #0000000b;
            margin-bottom: 28px;
        }

        .welcome h2 {
            margin-top: 0;
            color: #315c45;
            font-size: 29px;
        }

        .welcome p {
            line-height: 1.6;
            color: #59655d;
        }

        .section-title {
            margin: 28px 0 16px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 23px;
            border: 1px solid #e3e9e3;
        }

        .card .emoji {
            font-size: 32px;
        }

        .card h3 {
            color: #315c45;
            margin-bottom: 10px;
        }

        .card p {
            line-height: 1.5;
            color: #59655d;
            min-height: 45px;
        }

        .card a {
            display: inline-block;
            background: #315c45;
            color: white;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 7px;
            margin-top: 8px;
        }

        .card a:hover {
            background: #244533;
        }

        .account {
            margin-top: 28px;
            background: #e8f0e9;
            padding: 22px;
            border-radius: 12px;
        }

        .account p {
            overflow-wrap: anywhere;
        }

        footer {
            text-align: center;
            padding: 25px 15px;
            color: #68736b;
        }
    </style>
</head>

<body>

<header>
    <h1>Pawsitive</h1>
    <a class="logout" href="/frontend/pages/signout.php">Sign Out</a>
</header>

<main>
    <section class="welcome">
        <h2>
            Welcome, <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>!
        </h2>

        <p>
            Welcome to your Pawsitive dashboard. We're glad you're here!
            Explore animals looking for loving homes, manage your adoption
            journey, and help us care for animals in need.
        </p>
    </section>

    <h2 class="section-title">What would you like to do?</h2>

    <section class="cards">

        <article class="card">
            <!-- <div class="emoji"></div> -->
            <h3>Meet the Animals</h3>
            <p>
                Discover animals waiting for a safe, loving home.
            </p>
            <a href="animals.php">Browse Animals</a>
        </article>

        <article class="card">
            <!-- <div class="emoji"></div> -->
            <h3>My Applications</h3>
            <p>
                Keep track of your adoption applications and their progress.
            </p>
            <a href="applications.php">My Applications</a>
        </article>

        <article class="card">
            <!-- <div class="emoji"></div> -->
            <h3>Book a Visit</h3>
            <p>
                Arrange a visit to meet the animals at the shelter.
            </p>
            <a href="book-visit.php">Book a Visit</a>
        </article>

        <article class="card">
            <!-- <div class="emoji"></div> -->
            <h3>Make a Donation</h3>
            <p>
                Support animal care, food, shelter, and veterinary needs.
            </p>
            <a href="/frontend/pages/donate.php">Donate</a>
        </article>

    </section>

    <section class="account">
        <h2>My Account</h2>

        <p>
            <strong>Name:</strong>
            <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>
        </p>

        <p>
            <strong>Email:</strong>
            <?= htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') ?>
        </p>

        <p>
            <strong>Account status:</strong> Signed in
        </p>
    </section>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> Pawsitive Animal Shelter. Every paw deserves a home.</p>
</footer>

</body>
</html>
