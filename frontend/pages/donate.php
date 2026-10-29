<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

require_once __DIR__ . '/../../backend/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amountInput = trim($_POST['amount'] ?? '');
    $amount = filter_var($amountInput, FILTER_VALIDATE_FLOAT);
    $type = $_POST['donation_type'] ?? '';
    $message = trim($_POST['message'] ?? '');

    $validTypes = ['Once-off', 'Monthly'];

    if ($amount === false || !is_finite((float)$amount) ||
        $amount < 1 || $amount > 1000000) {
        $error = 'Enter a donation amount between R1 and R1,000,000.';
    } elseif (!in_array($type, $validTypes, true)) {
        $error = 'Please select a valid donation frequency.';
    } else {
        try {
            $stmt = $conn->prepare(
                "INSERT INTO donations
                    (user_id, amount, donation_type, message)
                 VALUES (?, ?, ?, ?)"
            );

            $stmt->execute([
                $_SESSION['user_id'],
                number_format((float)$amount, 2, '.', ''),
                $type,
                $message !== '' ? $message : null
            ]);

            $success = 'Thank you! Your donation has been recorded.';
            $_POST = [];
        } catch (PDOException $e) {
            error_log('Donation recording failed: ' . $e->getMessage());
            $error = 'We could not record your donation. Please try again.';
        }
    }
}

function escapeDonation($value) {
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
    <title>Donate</title>
</head>
<body>
<header class="page-header">
    <h1>Pawsitive</h1>
    <nav>
        <a href="/frontend/pages/dashboard.php">Home</a>
        <a href="/frontend/pages/animals.php">Animals</a>
        <a href="/frontend/pages/client_donations.php">My Donations</a>
        <a href="/frontend/pages/signout.php">Sign Out</a>
    </nav>
</header>

<main class="page-container">
    <section class="card">
        <h2>Support Our Animals</h2>
        <p class="help">
            Your support can help provide food, shelter, and care for animals
            waiting for their forever homes.
        </p>

        <p class="help">
            This form records a donation pledge in the Pawsitive system.
            It does not process a real payment.
        </p>

        <?php if ($success !== ''): ?>
            <div class="message success" role="status">
                <?= escapeDonation($success) ?>
            </div>
            <a class="button" href="/frontend/pages/client_donations.php">View My Donations</a>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="message error" role="alert">
                <?= escapeDonation($error) ?>
            </div>
        <?php endif; ?>

        <form class="customer-form" method="POST" action="/frontend/pages/donate.php">
            <label for="amount">Donation amount (ZAR) *</label>
            <input
                type="number"
                id="amount"
                name="amount"
                min="1"
                max="1000000"
                step="0.01"
                value="<?= escapeDonation($_POST['amount'] ?? '') ?>"
                required
            >

            <label for="donation_type">Donation frequency *</label>
            <select id="donation_type" name="donation_type" required>
                <option value="">Choose frequency</option>
                <option value="Once-off"
                    <?= ($_POST['donation_type'] ?? '') === 'Once-off' ? 'selected' : '' ?>>
                    Once-off
                </option>
                <option value="Monthly"
                    <?= ($_POST['donation_type'] ?? '') === 'Monthly' ? 'selected' : '' ?>>
                    Monthly
                </option>
            </select>

            <label for="message">Message (optional)</label>
            <textarea id="message" name="message" maxlength="3000"><?= escapeDonation($_POST['message'] ?? '') ?></textarea>

            <button type="submit">Record Donation</button>
        </form>

        <a class="button" href="/frontend/pages/dashboard.php">Back to Dashboard</a>
    </section>
</main>
</body>
</html>