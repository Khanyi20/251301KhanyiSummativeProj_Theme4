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

require_once __DIR__ . '/../../backend/db.php';

function escapeStaff($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$dashboardError = '';
$stats = [
    'total_animals' => 0,
    'available_animals' => 0,
    'pending_applications' => 0,
    'visit_bookings' => 0,
    'donation_total' => 0,
];
$upcomingBookings = [];
$recentApplications = [];
$recentDonations = [];
$bookingSectionTitle = 'Upcoming Visits';
$bookingLoadFailed = false;
$applicationLoadFailed = false;
$donationLoadFailed = false;

try {
    $statsStmt = $conn->query(
        "SELECT
            (SELECT COUNT(*) FROM animals) AS total_animals,
            (SELECT COUNT(*) FROM animals WHERE status = 'Available') AS available_animals,
            (SELECT COUNT(*) FROM adoption_applications WHERE status = 'Pending') AS pending_applications,
            (SELECT COUNT(*) FROM visit_bookings) AS visit_bookings,
            (SELECT COALESCE(SUM(amount), 0) FROM donations WHERE status <> 'Cancelled') AS donation_total"
    );
    $stats = array_merge($stats, $statsStmt->fetch() ?: []);
} catch (PDOException $e) {
    error_log('Loading staff dashboard statistics failed: ' . $e->getMessage());
    $dashboardError = 'Some dashboard information could not be loaded. Please try again later.';
}

try {
    $bookingsStmt = $conn->query(
        "SELECT vb.booking_id, vb.visit_date, vb.visit_time,
                vb.number_of_visitors, vb.status, u.full_name
         FROM visit_bookings vb
         LEFT JOIN users u ON vb.user_id = u.id
         WHERE vb.visit_date >= CURDATE() AND vb.status <> 'Cancelled'
         ORDER BY vb.visit_date ASC, vb.visit_time ASC
         LIMIT 5"
    );
    $upcomingBookings = $bookingsStmt->fetchAll();

    if (empty($upcomingBookings)) {
        $recentBookingsStmt = $conn->query(
            "SELECT vb.booking_id, vb.visit_date, vb.visit_time,
                    vb.number_of_visitors, vb.status, u.full_name
             FROM visit_bookings vb
             LEFT JOIN users u ON vb.user_id = u.id
             ORDER BY vb.visit_date DESC, vb.visit_time DESC
             LIMIT 5"
        );
        $upcomingBookings = $recentBookingsStmt->fetchAll();
        if (!empty($upcomingBookings)) {
            $bookingSectionTitle = 'Recent Visits (no upcoming bookings)';
        }
    }
} catch (PDOException $e) {
    error_log('Loading staff dashboard bookings failed: ' . $e->getMessage());
    $dashboardError = 'Some dashboard information could not be loaded. Please try again later.';
    $bookingLoadFailed = true;
}

try {
    $applicationsStmt = $conn->query(
        "SELECT aa.application_id, aa.status, aa.application_date,
                u.full_name, a.name AS animal_name
         FROM adoption_applications aa
         LEFT JOIN users u ON aa.user_id = u.id
         LEFT JOIN animals a ON aa.animal_id = a.animal_id
         ORDER BY aa.application_date DESC
         LIMIT 5"
    );
    $recentApplications = $applicationsStmt->fetchAll();
} catch (PDOException $e) {
    error_log('Loading staff dashboard applications failed: ' . $e->getMessage());
    $dashboardError = 'Some dashboard information could not be loaded. Please try again later.';
    $applicationLoadFailed = true;
}

try {
    $donationsStmt = $conn->query(
        "SELECT d.donation_id, d.amount, d.donation_type,
                d.status, d.donation_date, u.full_name
         FROM donations d
         LEFT JOIN users u ON d.user_id = u.id
         ORDER BY d.donation_date DESC
         LIMIT 5"
    );
    $recentDonations = $donationsStmt->fetchAll();
} catch (PDOException $e) {
    error_log('Loading staff dashboard donations failed: ' . $e->getMessage());
    $dashboardError = 'Some dashboard information could not be loaded. Please try again later.';
    $donationLoadFailed = true;
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

        <?php if ($dashboardError !== ''): ?>
            <div class="message error" role="alert">
                <?= escapeStaff($dashboardError) ?>
            </div>
        <?php endif; ?>

        <section class="staff-stats">
            <article class="stat-card">
                <p>Total Animals</p>
                <h2><?= number_format((int)$stats['total_animals']) ?></h2>
                <small><?= number_format((int)$stats['available_animals']) ?> available for adoption</small>
            </article>

            <article class="stat-card">
                <p>Pending Applications</p>
                <h2><?= number_format((int)$stats['pending_applications']) ?></h2>
            </article>

            <article class="stat-card">
                <p>Total Visit Bookings</p>
                <h2><?= number_format((int)$stats['visit_bookings']) ?></h2>
            </article>

            <article class="stat-card">
                <p>Recorded Donations</p>
                <h2>R<?= number_format((float)$stats['donation_total'], 2) ?></h2>
            </article>
        </section>

        <section class="staff-panel">
            <h2><?= escapeStaff($bookingSectionTitle) ?></h2>
            <?php if (empty($upcomingBookings)): ?>
                <p class="muted">
                    <?= $bookingLoadFailed
                        ? 'Visit booking data could not be loaded.'
                        : 'There are no upcoming or recent visit bookings.' ?>
                </p>
            <?php else: ?>
                <ul class="dashboard-list">
                    <?php foreach ($upcomingBookings as $booking): ?>
                        <li class="dashboard-list-item">
                            <div>
                                <strong><?= escapeStaff($booking['full_name'] ?: 'Customer') ?></strong>
                                <span> — <?= (int)$booking['number_of_visitors'] ?> visitor(s)</span>
                            </div>
                            <div class="dashboard-item-meta">
                                <?= escapeStaff(date('d M Y', strtotime($booking['visit_date']))) ?>
                                at <?= escapeStaff(date('g:i A', strtotime($booking['visit_time']))) ?>
                                · <?= escapeStaff($booking['status']) ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <section class="staff-panel">
            <h2>Recent Adoption Applications</h2>
            <?php if (empty($recentApplications)): ?>
                <p class="muted">
                    <?= $applicationLoadFailed
                        ? 'Adoption application data could not be loaded.'
                        : 'No adoption applications have been submitted yet.' ?>
                </p>
            <?php else: ?>
                <ul class="dashboard-list">
                    <?php foreach ($recentApplications as $application): ?>
                        <li class="dashboard-list-item">
                            <div>
                                <strong><?= escapeStaff($application['animal_name'] ?: 'Animal record') ?></strong>
                                <span> — <?= escapeStaff($application['full_name'] ?: 'Customer') ?></span>
                            </div>
                            <div class="dashboard-item-meta">
                                <?= escapeStaff(date('d M Y', strtotime($application['application_date']))) ?>
                                · <?= escapeStaff($application['status']) ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <section class="staff-panel">
            <h2>Recent Donations</h2>
            <?php if (empty($recentDonations)): ?>
                <p class="muted">
                    <?= $donationLoadFailed
                        ? 'Donation data could not be loaded.'
                        : 'No donations have been recorded yet.' ?>
                </p>
            <?php else: ?>
                <ul class="dashboard-list">
                    <?php foreach ($recentDonations as $donation): ?>
                        <li class="dashboard-list-item">
                            <div>
                                <strong><?= escapeStaff($donation['full_name'] ?: 'Donor') ?></strong>
                                <span> — R<?= number_format((float)$donation['amount'], 2) ?></span>
                            </div>
                            <div class="dashboard-item-meta">
                                <?= escapeStaff(date('d M Y', strtotime($donation['donation_date']))) ?>
                                · <?= escapeStaff($donation['donation_type']) ?>
                                · <?= escapeStaff($donation['status']) ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </main>

</div>


<footer>
    <p>&copy; <?= date('Y') ?> Pawsitive Animal Shelter</p>
</footer>
</body>
</html>