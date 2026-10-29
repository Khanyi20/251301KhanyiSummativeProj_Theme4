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
    <link rel="stylesheet" href="/frontend/css/common.css">
    <link rel="stylesheet" href="/frontend/css/staff.css">
    <title>Staff Dashboard</title>
    
</head>
<body>

<div class="staff-layout">

    <aside class="staff-sidebar">
        <a class="staff-brand" href="/frontend/pages/staff_dashboard.php">
           <img src="/frontend/assets/Pawsitive Animal Shelter_logo_cutout 2.png"> Pawsitive
        </a>

        <p class="sidebar-label">STAFF PORTAL</p>

        <nav class="staff-nav">
            <a class="active" href="/frontend/pages/staff_dashboard.php">
                Dashboard
            </a>
            <a href="/frontend/pages/manage_animals.php">
                Animals
            </a>
            <a href="/frontend/pages/manage_applications.php">
                Applications
            </a>
            <a href="/frontend/pages/manage_bookings.php">
                 Bookings
            </a>
            <a href="/frontend/pages/manage_donations.php">
                Donations
            </a>
        </nav>

        <a class="staff-signout"
           href="/frontend/pages/signout.php">
            Sign Out
        </a>
    </aside>

    <main class="staff-main">
        <div class="staff-topbar">
            <div>
                <h1>Dashboard</h1>
                <p class="muted">Welcome back to your shelter overview.</p>
            </div>

            <span class="staff-badge">STAFF</span>
        </div>

        <!-- Add your database-driven statistics here. -->
        <section class="staff-stats">
            <article class="stat-card">
                <p>Total Animals</p>
                <h2><!-- PHP: animal count --></h2>
            </article>

            <article class="stat-card">
                <p>Pending Applications</p>
                <h2><!-- PHP: pending count --></h2>
            </article>

            <article class="stat-card">
                <p>Visit Bookings</p>
                <h2><!-- PHP: booking count --></h2>
            </article>

            <article class="stat-card">
                <p>Total Donations</p>
                <h2><!-- PHP: donation total --></h2>
            </article>
        </section>

        <section class="staff-panel">
            <h2>Upcoming Activities</h2>
            <!-- calendar component goes here. -->
        </section>

        <section class="staff-panel">
            <h2>Recent Donations</h2>
            <!-- Reuse existing PHP donation loop. -->
        </section>
    </main>

</div>


<footer>
    <p>&copy; <?= date('Y') ?> Pawsitive Animal Shelter</p>
</footer>
</body>
</html>