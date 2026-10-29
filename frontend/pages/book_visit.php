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
    $visitDate = trim($_POST['visit_date'] ?? '');
    $visitTime = trim($_POST['visit_time'] ?? '');
    $visitors = filter_input(INPUT_POST, 'number_of_visitors', FILTER_VALIDATE_INT);
    $notes = trim($_POST['notes'] ?? '');

    $dateObject = DateTime::createFromFormat('!Y-m-d', $visitDate);
    $validDate = $dateObject && $dateObject->format('Y-m-d') === $visitDate;

    $validTime = preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $visitTime);

    if (!$validDate || $visitDate < date('Y-m-d')) {
        $error = 'Please choose a valid date that is today or later.';
    } elseif (!$validTime) {
        $error = 'Please choose a valid visit time.';
    } elseif ($visitors === false || $visitors === null || $visitors < 1 || $visitors > 20) {
        $error = 'The number of visitors must be between 1 and 20.';
    } else {
        try {
            $stmt = $conn->prepare(
                "INSERT INTO visit_bookings
                (user_id, visit_date, visit_time, number_of_visitors, notes)
                VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $_SESSION['user_id'],
                $visitDate,
                $visitTime . ':00',
                $visitors,
                $notes !== '' ? $notes : null
            ]);

            $success = 'Your visit request has been submitted successfully!';
            $_POST = [];
        } catch (PDOException $e) {
            error_log('Visit booking failed: ' . $e->getMessage());
            $error = 'We could not save your booking. Please try again.';
        }
    }
}

function escapeBooking($value) {
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
    <title>Book a Shelter Visit</title>
</head>
<body>
<header>
    <h1>Pawsitive</h1>
    <nav>
        <a href="/frontend/pages/dashboard.php">Dashboard</a> &nbsp;|&nbsp;
        <a href="/frontend/pages/animals.php">Animals</a> &nbsp;|&nbsp;
        <a href="/frontend/pages/signout.php">Sign Out</a>
    </nav>
</header>

<main>
    <section class="card">
        <h2>Book a Shelter Visit</h2>
        <p class="help">Choose a date and time to visit the animals at our shelter.</p>

        <?php if ($success !== ''): ?>
            <div class="message success" role="status">
                <?= escapeBooking($success) ?>
            </div>
            <p>You can return to the dashboard or submit another visit request.</p>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="message error" role="alert">
                <?= escapeBooking($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/frontend/pages/book_visit.php">
            <label for="visit_date">Visit date *</label>
            <input
                type="date"
                id="visit_date"
                name="visit_date"
                min="<?= date('Y-m-d') ?>"
                value="<?= escapeBooking($_POST['visit_date'] ?? '') ?>"
                required
            >

            <label for="visit_time">Preferred time *</label>
            <select id="visit_time" name="visit_time" required>
                <option value="">Choose a time</option>
                <?php
                $times = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'];
                $selectedTime = $_POST['visit_time'] ?? '';
                foreach ($times as $time):
                ?>
                    <option value="<?= $time ?>" <?= $selectedTime === $time ? 'selected' : '' ?>>
                        <?= date('g:i A', strtotime($time)) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="number_of_visitors">Number of visitors *</label>
            <input
                type="number"
                id="number_of_visitors"
                name="number_of_visitors"
                min="1"
                max="20"
                value="<?= escapeBooking($_POST['number_of_visitors'] ?? '1') ?>"
                required
            >

            <label for="notes">Additional notes (optional)</label>
            <textarea id="notes" name="notes" maxlength="3000"><?= escapeBooking($_POST['notes'] ?? '') ?></textarea>

            <button type="submit">Submit Visit Request</button>
        </form>

        <a class="button" href="/frontend/pages/dashboard.php">Back to Dashboard</a>
    </section>
</main>
</body>
</html>