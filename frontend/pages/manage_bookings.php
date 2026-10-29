<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}

if (($_SESSION['user_role'] ?? '') !== 'staff') {
    header("Location: dashboard.php");
    exit;
}

require_once __DIR__ . '/../../backend/db.php';

$message = '';
$loadError = '';

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = filter_input(
        INPUT_POST,
        'booking_id',
        FILTER_VALIDATE_INT
    );

    $status = $_POST['status'] ?? '';

    $allowedStatuses = ['Pending', 'Confirmed', 'Cancelled'];

    if (!$bookingId || !in_array($status, $allowedStatuses, true)) {
        $message = 'Invalid booking or status.';
    } else {
        try {
            $stmt = $conn->prepare(
                "UPDATE visit_bookings
                 SET status = :status
                 WHERE booking_id = :id"
            );

            $stmt->execute([
                ':status' => $status,
                ':id' => $bookingId
            ]);

            $message = 'Booking status updated successfully.';
        } catch (PDOException $e) {
            error_log('Updating visit booking failed: ' . $e->getMessage());
            $loadError = 'The booking status could not be updated. Please try again.';
        }
    }
}

$bookings = [];
try {
    $stmt = $conn->query(
        "SELECT
            vb.booking_id,
            vb.visit_date,
            vb.visit_time,
            vb.number_of_visitors,
            vb.notes,
            vb.status,
            vb.created_at,
            u.full_name,
            u.email
         FROM visit_bookings vb
         LEFT JOIN users u ON vb.user_id = u.id
         ORDER BY vb.visit_date DESC, vb.visit_time DESC"
    );

    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Loading visit bookings failed: ' . $e->getMessage());
    $loadError = 'Booking records could not be loaded. Check the database connection and table setup.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/frontend/css/common.css">
    <link rel="stylesheet" href="/frontend/css/staff.css">
    <title>Manage Bookings</title>
</head>

<body>
<div class="staff-layout">

    <aside class="staff-sidebar">
        <a class="staff-brand" href="/frontend/pages/staff_dashboard.php">
           <img src="/frontend/assets/Pawsitive Animal Shelter_logo_cutout 2.png"> Pawsitive
        </a>

        <p class="sidebar-label">STAFF PORTAL</p>

        <nav class="staff-nav">
            <a href="/frontend/pages/staff_dashboard.php">
                Dashboard
            </a>
            <a href="/frontend/pages/manage_animals.php">
                Animals
            </a>
            <a href="/frontend/pages/manage_applications.php">
                Applications
            </a>
            <a class="active" href="/frontend/pages/manage_bookings.php" aria-current="page">
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
            <h1>Manage Shelter Visit Bookings</h1>
            <p class="muted">Review visit requests and update booking status.</p>
        </div>
        <span class="staff-badge">STAFF</span>
    </div>

    <?php if ($message !== ''): ?>
        <p class="message success" role="status"><?= e($message) ?></p>
    <?php endif; ?>

    <?php if ($loadError !== ''): ?>
        <p class="message error" role="alert"><?= e($loadError) ?></p>
    <?php endif; ?>

    <?php if ($loadError === '' && empty($bookings)): ?>

        <section class="staff-panel">
            <p class="muted">No visit bookings have been submitted yet.</p>
        </section>

    <?php elseif (!empty($bookings)): ?>

        <?php foreach ($bookings as $booking): ?>

            <section class="staff-panel">

                <h2>Booking #<?= e($booking['booking_id']) ?></h2>

                <div class="staff-details">
                    <strong>Customer:</strong>
                    <?= e($booking['full_name']) ?><br>

                    <strong>Email:</strong>
                    <?= e($booking['email']) ?><br>

                    <strong>Visit date:</strong>
                    <?= e($booking['visit_date']) ?><br>

                    <strong>Visit time:</strong>
                    <?= e(substr($booking['visit_time'], 0, 5)) ?><br>

                    <strong>Number of visitors:</strong>
                    <?= e($booking['number_of_visitors']) ?><br>

                    <strong>Notes:</strong><br>
                    <?= nl2br(e($booking['notes'] ?: 'No notes provided')) ?><br>

                    <strong>Current status:</strong>
                    <span class="staff-inline-status is-<?= e(strtolower($booking['status'])) ?>">
                        <?= e($booking['status']) ?>
                    </span>
                </div>

                <form class="staff-form" method="POST">
                    <input
                        type="hidden"
                        name="booking_id"
                        value="<?= e($booking['booking_id']) ?>"
                    >

                    <label for="status-<?= e($booking['booking_id']) ?>">
                        Update status:
                    </label>

                    <select
                        id="status-<?= e($booking['booking_id']) ?>"
                        name="status"
                        required
                    >
                        <?php foreach (['Pending', 'Confirmed', 'Cancelled'] as $option): ?>
                            <option
                                value="<?= e($option) ?>"
                                <?= $booking['status'] === $option ? 'selected' : '' ?>
                            >
                                <?= e($option) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit">Save Status</button>
                </form>

            </section>

        <?php endforeach; ?>

    <?php endif; ?>

    </main>
</div>
</body>
</html>