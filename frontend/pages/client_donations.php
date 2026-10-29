<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

require_once __DIR__ . '/../../backend/db.php';

$donations = [];
$error = '';

try {
    $stmt = $conn->prepare(
        "SELECT donation_id, amount, donation_type, message, status, donation_date
         FROM donations
         WHERE user_id = ?
         ORDER BY donation_date DESC"
    );

    $stmt->execute([$_SESSION['user_id']]);
    $donations = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log('Loading donations failed: ' . $e->getMessage());
    $error = 'We could not load your donation history. Please try again later.';
}

function escapeDonationHistory($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$total = 0;
foreach ($donations as $donation) {
    $total += (float)$donation['amount'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/frontend/css/common.css">
    <link rel="stylesheet" href="/frontend/css/customer.css">
    <title>My Donations</title>
    <style>
       
        .summary, .donation-card, .message {
            background: white;
            padding: 22px;
            margin-bottom: 20px;
            border-radius: 12px;
            border: 1px solid #e3e9e3;
            box-shadow: 0 3px 12px #0000000a;
        }

        .summary strong {
            color: #5c3131;
            font-size: 25px;
        }

        .donation-card h3 {
            margin-top: 0;
            color: #5c3131;
        }

        

        

        
    </style>
</head>
<body>
<header>
    <h1>Pawsitive</h1>
    <nav>
        <a href="/frontend/pages/dashboard.php">Dashboard</a> &nbsp;|&nbsp;
        <a href="/frontend/pages/donate.php">Donate</a> &nbsp;|&nbsp;
        <a href="/frontend/pages/signout.php">Sign Out</a>
    </nav>
</header>

<main>
    <h2>My Donations</h2>
    <p class="muted">Thank you for helping us care for animals in need.</p>

    <?php if ($error !== ''): ?>

        <div class="message error" role="alert">
            <?= escapeDonationHistory($error) ?>
        </div>

    <?php elseif (empty($donations)): ?>

        <div class="message">
            <p>You haven't recorded any donations yet.</p>
            <a class="button" href="/frontend/pages/donate.php">Make a Donation Pledge</a>
        </div>

    <?php else: ?>

        <section class="summary">
            <p>Total recorded donation pledges</p>
            <strong>R<?= number_format($total, 2) ?></strong>
            <p class="muted">
                This total reflects recorded pledges, not verified or processed payments.
            </p>
        </section>

        <?php foreach ($donations as $donation): ?>
            <article class="donation-card">
                <h3>Donation #<?= (int)$donation['donation_id'] ?></h3>

                <p>
                    <strong>Amount:</strong>
                    R<?= number_format((float)$donation['amount'], 2) ?>
                </p>

                <p>
                    <strong>Frequency:</strong>
                    <?= escapeDonationHistory($donation['donation_type']) ?>
                </p>

                <p>
                    <strong>Date recorded:</strong>
                    <?= escapeDonationHistory(
                        date('d M Y, g:i A', strtotime($donation['donation_date']))
                    ) ?>
                </p>

                <p>
                    <strong>Status:</strong>
                    <span class="status">
                        <?= escapeDonationHistory($donation['status']) ?>
                    </span>
                </p>

                <?php if (!empty($donation['message'])): ?>
                    <p>
                        <strong>Your message:</strong><br>
                        <?= nl2br(escapeDonationHistory($donation['message'])) ?>
                    </p>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>

        <a class="button" href="/frontend/pages/donate.php">Record Another Donation</a>

    <?php endif; ?>

    <a class="button" href="/frontend/pages/dashboard.php">Back to Dashboard</a>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> Pawsitive Animal Shelter</p>
</footer>
</body>
</html>