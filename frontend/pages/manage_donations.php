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
    <title>Manage Donations</title>

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

        .donation {
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

        .message-box {
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

    <h1>Manage Donations</h1>

    <?php if ($message !== ''): ?>
        <p class="message-box"><?= e($message) ?></p>
    <?php endif; ?>

    <?php if (empty($donations)): ?>

        <p>No donation pledges have been recorded yet.</p>

    <?php else: ?>

        <?php foreach ($donations as $donation): ?>

            <section class="donation">
                <h2>Donation #<?= e($donation['donation_id']) ?></h2>

                <div class="details">
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
                    <?= e($donation['status']) ?>
                </div>

                <form method="POST">
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

</div>
</body>
</html>