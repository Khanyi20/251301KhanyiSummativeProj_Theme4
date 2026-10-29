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

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donationId = filter_input(
        INPUT_POST,
        'donation_id',
        FILTER_VALIDATE_INT
    );

    $status = $_POST['status'] ?? '';

    $allowedStatuses = ['Recorded', 'Received', 'Cancelled'];

    if (!$donationId || !in_array($status, $allowedStatuses, true)) {
        $message = 'Invalid donation or status.';
    } else {
        $stmt = $conn->prepare(
            "UPDATE donations
             SET status = :status
             WHERE donation_id = :id"
        );

        $stmt->execute([
            ':status' => $status,
            ':id' => $donationId
        ]);

        $message = 'Donation status updated successfully.';
    }
}

$stmt = $conn->query(
    "SELECT
        d.donation_id,
        d.amount,
        d.donation_type,
        d.message,
        d.status,
        d.donation_date,
        u.full_name,
        u.email
     FROM donations d
     JOIN users u ON d.user_id = u.id
     ORDER BY d.donation_date DESC"
);

$donations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/frontend/css/common.css">
    <link rel="stylesheet" href="/frontend/css/staff.css">
    <title>Manage Donations</title>
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
            <a href="/frontend/pages/manage_bookings.php">
                 Bookings
            </a>
            <a class="active" href="/frontend/pages/manage_donations.php" aria-current="page">
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
            <h1>Manage Donations</h1>
            <p class="muted">Review donation records and update their status.</p>
        </div>
        <span class="staff-badge">STAFF</span>
    </div>

    <?php if ($message !== ''): ?>
        <p class="message success" role="status"><?= e($message) ?></p>
    <?php endif; ?>

    <?php if (empty($donations)): ?>

        <section class="staff-panel">
            <p class="muted">No donation pledges have been recorded yet.</p>
        </section>

    <?php else: ?>

        <?php foreach ($donations as $donation): ?>

            <section class="staff-panel">
                <h2>Donation #<?= e($donation['donation_id']) ?></h2>

                <div class="staff-details">
                    <strong>Donor:</strong>
                    <?= e($donation['full_name']) ?><br>

                    <strong>Email:</strong>
                    <?= e($donation['email']) ?><br>

                    <strong>Amount pledged:</strong>
                    R<?= number_format((float)$donation['amount'], 2) ?><br>

                    <strong>Donation type:</strong>
                    <?= e($donation['donation_type']) ?><br>

                    <strong>Date recorded:</strong>
                    <?= e($donation['donation_date']) ?><br>

                    <strong>Donor message:</strong><br>
                    <?= nl2br(e($donation['message'] ?: 'No message provided')) ?><br>

                    <strong>Current status:</strong>
                    <span class="staff-inline-status is-<?= e(strtolower($donation['status'])) ?>">
                        <?= e($donation['status']) ?>
                    </span>
                </div>

                <form class="staff-form" method="POST">
                    <input
                        type="hidden"
                        name="donation_id"
                        value="<?= e($donation['donation_id']) ?>"
                    >

                    <label for="status-<?= e($donation['donation_id']) ?>">
                        Update status:
                    </label>

                    <select
                        id="status-<?= e($donation['donation_id']) ?>"
                        name="status"
                        required
                    >
                        <?php foreach (['Recorded', 'Received', 'Cancelled'] as $option): ?>
                            <option
                                value="<?= e($option) ?>"
                                <?= $donation['status'] === $option ? 'selected' : '' ?>
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