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
    }
}

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
     JOIN users u ON vb.user_id = u.id
     ORDER BY vb.visit_date DESC, vb.visit_time DESC"
);

$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f4;
            margin: 0;
            padding: 24px;
            color: #26352b;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        h1, h2 {
            color: #286443;
        }

        .booking {
            background: white;
            padding: 20px;
            margin-bottom: 18px;
            border-radius: 10px;
            box-shadow: 0 2px 8px #00000010;
        }

        .details {
            line-height: 1.8;
            overflow-wrap: anywhere;
        }

        .message {
            padding: 12px;
            background: #e3f4e8;
            border-radius: 6px;
        }

        select, button {
            padding: 10px;
            margin-top: 8px;
            border-radius: 5px;
        }

        button {
            background: #286443;
            color: white;
            border: none;
            cursor: pointer;
        }

        .back {
            display: inline-block;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
<div class="container">

    <a class="back" href="/frontend/pages/staff_dashboard.php">
        Back to Staff Dashboard
    </a>

    <h1>Manage Shelter Visit Bookings</h1>

    <?php if ($message !== ''): ?>
        <p class="message"><?= e($message) ?></p>
    <?php endif; ?>

    <?php if (empty($bookings)): ?>

        <p>No visit bookings have been submitted yet.</p>

    <?php else: ?>

        <?php foreach ($bookings as $booking): ?>

            <section class="booking">

                <h2>Booking #<?= e($booking['booking_id']) ?></h2>

                <div class="details">
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
                    <?= e($booking['status']) ?>
                </div>

                <form method="POST">
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

</div>
</body>
</html>