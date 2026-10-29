<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

require_once __DIR__ . '/../../backend/db.php';

$bookings = [];
$error = '';

try {
    $stmt = $conn->prepare(
        "SELECT booking_id, visit_date, visit_time,
                number_of_visitors, notes, status, created_at
         FROM visit_bookings
         WHERE user_id = ?
         ORDER BY visit_date DESC, visit_time DESC"
    );

    $stmt->execute([$_SESSION['user_id']]);
    $bookings = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log('Loading visit bookings failed: ' . $e->getMessage());
    $error = 'We could not load your bookings. Please try again later.';
}

function escapeBookingPage($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/frontend/css/common.css">
    <link rel="stylesheet" href="/frontend/css/customer.css">
    <title>My Visit Bookings</title>
    
</head>
<body>

<header class="page-header">
    <h1>Pawsitive</h1>
    <nav>
        <a href="/frontend/pages/dashboard.php">Home</a>
        <a href="/frontend/pages/animals.php">Animals</a>
        <a href="/frontend/pages/applications.php">Applications</a>
        <a href="/frontend/pages/mybookings.php" aria-current="page">Bookings</a>
        <a href="/frontend/pages/signout.php">Sign Out</a>
    </nav>
</header>

<main class="page-container">
    <h2 class="customer-page-title">My Visit Bookings</h2>
    <p class="muted">View your shelter visit requests and their current status.</p>

    <?php if ($error !== ''): ?>

        <div class="message error" role="alert">
            <?= escapeBookingPage($error) ?>
        </div>

    <?php elseif (empty($bookings)): ?>

        <div class="customer-panel">
            <p>You haven't requested a shelter visit yet.</p>
            <a class="button" href="/frontend/pages/book_visit.php">Book a Visit</a>
        </div>

    <?php else: ?>

        <?php foreach ($bookings as $booking): ?>
            <?php
                $statusClass = strtolower($booking['status']);

                if (!in_array($statusClass, ['pending', 'confirmed', 'cancelled'], true)) {
                    $statusClass = '';
                }

                $formattedDate = date(
                    'd M Y',
                    strtotime($booking['visit_date'])
                );

                $formattedTime = date(
                    'g:i A',
                    strtotime($booking['visit_time'])
                );
            ?>

            <article class="booking-card">
                <h3>Shelter Visit #<?= (int)$booking['booking_id'] ?></h3>

                <p>
                    <strong>Visit date:</strong>
                    <?= escapeBookingPage($formattedDate) ?>
                </p>

                <p>
                    <strong>Visit time:</strong>
                    <?= escapeBookingPage($formattedTime) ?>
                </p>

                <p>
                    <strong>Number of visitors:</strong>
                    <?= (int)$booking['number_of_visitors'] ?>
                </p>

                <?php if (!empty($booking['notes'])): ?>
                    <p>
                        <strong>Notes:</strong><br>
                        <?= nl2br(escapeBookingPage($booking['notes'])) ?>
                    </p>
                <?php endif; ?>

                <p>
                    <strong>Status:</strong>
                    <span class="status <?= escapeBookingPage($statusClass) ?>">
                        <?= escapeBookingPage($booking['status']) ?>
                    </span>
                </p>
            </article>

        <?php endforeach; ?>

        <a class="button" href="/frontend/pages/book_visit.php">Request Another Visit</a>

    <?php endif; ?>

    <a class="button" href="/frontend/pages/dashboard.php">Back to Dashboard</a>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> Pawsitive Animal Shelter</p>
</footer>

</body>
</html>