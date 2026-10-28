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
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f7f4;
            color: #26352d;
        }

        header {
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
        header a { color: white; text-decoration: none; }

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

        h2, h3 { color: #315c45; }

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
            color: #245c35;
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
    <h1>Pawsitive Staff</h1>
    <nav>
        <a href="/frontend/pages/staff_dashboard.php">Staff Dashboard</a>
        &nbsp; | &nbsp;
        <a href="/frontend/pages/signout.php">Sign Out</a>
    </nav>
</header>

<main>
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
</main>

<footer>
    <p>&copy; <?= date('Y') ?> Pawsitive Animal Shelter</p>
</footer>
</body>
</html>