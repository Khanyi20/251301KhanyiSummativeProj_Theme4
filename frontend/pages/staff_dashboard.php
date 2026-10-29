<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

if (($_SESSION['user_role'] ?? '') !== 'staff') {
    header('Location: dashboard.php');
    exit;
}

function escapeStaff($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Dashboard</title>
    <style>

        :root {
        --primary-color: #FFFAF6;
        --secondary-color: #2B2B2B;
        --accent-color: #E5E5E5;
        --text-color: #292727;
        --success-color: #4F8D49;
        --error-color: #830B14;
        --warning-color: #E4981F;
        --info-color: #C9C6BC;
        --brand-color: #BD2935;
        --blush-pink: #F8E8E7;
        --charcoal: #292727;
        --warm-cream: #FFFAF6;
        }
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: var(--warm-cream);
            color: var(--charcoal);
        }


        .sidebar {
            margin: 0;
            padding: 0;
            width: 200px;
            background-color: var(--charcoal);
            position: fixed;
            height: 100%;
            overflow: auto;
        }

        /* Sidebar links */
        .sidebar a {
            display: block;
            color: var(--warm-cream);
            padding: 16px;
            text-decoration: none;
        }

        /* Active/current link */
        .sidebar a.active {
        background-color: var(--brand-color);
        color: var(var(--warm-cream));
        }

        /* Links on mouse-over */
        .sidebar a:hover:not(.active) {
        background-color: var(--brand-color);
        color: var(--warm-cream);
        }

        /* Page content. The value of the margin-left property should match the value of the sidebar's width property */
        div.content {
        margin-left: 200px;
        padding: 1px 16px;
        height: 1000px;
        }

        /* On screens that are less than 700px wide, make the sidebar into a topbar */
        @media screen and (max-width: 700px) {
        .sidebar {
            width: 100%;
            height: auto;
            position: relative;
        }
        .sidebar a {float: left;}
        div.content {margin-left: 0;}
        }

        /* On screens that are less than 400px, display the bar vertically, instead of horizontally */
        @media screen and (max-width: 400px) {
        .sidebar a {
            text-align: center;
            float: none;
        }
        }
        /* header {
            background: #315c45;
            color: white;
            padding: 20px 7%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }

        header h1 { margin: 0; font-size: 26px; }
        header a { color: white; text-decoration: none; } */

        main {
            max-width: 1100px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .welcome {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: 1px solid #e3e9e3;
        }

        h2, h3 { color: var(--brand-color); }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .card {
            display: block;
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-decoration: none;
            color: #26352d;
            border: 1px solid #e3e9e3;
            box-shadow: 0 3px 12px #0000000a;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px #00000012;
        }

        .card h3 { margin-top: 0; }
        .card p { line-height: 1.5; color: #59655d; }

        .staff-badge {
            display: inline-block;
            background: #e3f3e6;
            color: var(--brand-color);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        footer {
            text-align: center;
            padding: 25px;
            color: #68736b;
        }
    </style>
</head>
<body>
<header>

    <div class="sidebar">
    <h1>Pawsitive Staff</h1>
    <a class="active" href="#home">Dashboard</a>
    <a href="/frontend/pages/manage_animals.php">Animals</a>
    <a href="/frontend/pages/manage_applications.php">Applications</a>
    <a href="/frontend/pages/manage_bookings.php">Bookings</a>
    <a href="/frontend/pages/manage_donations.php">Donations</a>
    <div class="signout">
        <a href="/frontend/pages/signout.php">Sign Out</a>
    </div>
    </div>
</header>

<main>
    <div class="content">
    <section class="welcome">
        <span class="staff-badge">STAFF PORTAL</span>
        <h2>Welcome, <?= escapeStaff($_SESSION['user_name'] ?? 'Staff') ?>!</h2>
        <p>Manage shelter activities and help animals find their forever homes.</p>
    </section>

    <section class="cards">
        <a class="card" href="/frontend/pages/manage_animals.php">
            <h3>Manage Animals</h3>
            <p>Add new animals and update existing animal listings.</p>
        </a>

        <a class="card" href="/frontend/pages/manage_applications.php">
            <h3>Adoption Applications</h3>
            <p>Review customer applications and update their statuses.</p>
        </a>

        <a class="card" href="/frontend/pages/manage_bookings.php">
            <h3>Visit Bookings</h3>
            <p>View visit requests and confirm or cancel appointments.</p>
        </a>

        <a class="card" href="/frontend/pages/manage_donations.php">
            <h3>Donations</h3>
            <p>Review recorded donations and donation pledges.</p>
        </a>
    </section>
</div>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> Pawsitive Animal Shelter</p>
</footer>
</body>
</html>