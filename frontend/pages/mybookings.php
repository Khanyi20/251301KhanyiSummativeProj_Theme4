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
    <title>My Visit Bookings</title>
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
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        header h1 { margin: 0; font-size: 25px; }
        header a { color: white; text-decoration: none; }

        main {
            max-width: 950px;
            margin: 35px auto;
            padding: 0 20px;
        }

        h2 { color: #315c45; }

        .booking-card {
            background: white;
            padding: 22px;
            margin-bottom: 20px;
            border-radius: 12px;
            border: 1px solid #e3e9e3;
            box-shadow: 0 3px 12px #0000000a;
        }

        .booking-card h3 {
            color: #315c45;
            margin-top: 0;
        }

        .status {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            background: #fff1c9;
            color: #755600;
            font-size: 13px;
            font-weight: bold;
        }

        .status.confirmed {
            background: #e3f3e6;
            color: #245c35;
        }

        .status.cancelled {
            background: #fde8e8;
            color: #a12626;
        }

        .message {
            background: white;
            padding: 18px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .error { color: #a12626; }

        .button {
            display: inline-block;
            margin-top: 15px;
            padding: 11px 16px;
            border-radius: 7px;
            background: #315c45;
            color: white;
            text-decoration: none;
        }

        .muted { color: #657168; line-height: 1.6; }

        footer {
            text-align: center;
            padding: 25px;
            color: #68736b;
        }
    </style>
</head>
<body>
<header>
    <h1>Pawsitive</h1>
    <nav>
        <a href="/frontend/pages/dashboard.php">Dashboard</a>
        &nbsp;|&nbsp;
        <a href="/frontend/pages/book-visit.php">Book a Visit</a>
        &nbsp;|&nbsp;
        <a href="/frontend/pages/signout.php">Sign Out</a>
    </nav>
</header>

<main>
    <h2>My Visit Bookings</h2>
    <p class="muted">View your shelter visit requests and their current status.</p>

    <?php if ($error !== ''): ?>

        <div class="message error" role="alert">
            <?= escapeBookingPage($error) ?>
        </div>

    <?php elseif (empty($bookings)): ?>

        <div class="message">
            <p>You haven't requested a shelter visit yet.</p>
            <a class="button" href="/frontend/pages/book-visit.php">Book a Visit</a>
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

        <a class="button" href="book-visit.php">Request Another Visit</a>

    <?php endif; ?>

    <a class="button" href="/frontend/pages/dashboard.php">Back to Dashboard</a>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> Pawsitive Animal Shelter</p>
</footer>
</body>
</html>
```