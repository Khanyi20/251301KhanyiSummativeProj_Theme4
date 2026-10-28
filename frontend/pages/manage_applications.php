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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicationId = filter_input(
        INPUT_POST,
        'application_id',
        FILTER_VALIDATE_INT
    );

    $status = $_POST['status'] ?? '';

    $allowedStatuses = ['Pending', 'Approved', 'Rejected'];

    if (!$applicationId || !in_array($status, $allowedStatuses, true)) {
        $message = 'Invalid application or status.';
    } else {
        $stmt = $conn->prepare(
            "UPDATE adoption_applications
             SET status = :status
             WHERE application_id = :id"
        );

        $stmt->execute([
            ':status' => $status,
            ':id' => $applicationId
        ]);

        $message = 'Application status updated successfully.';
    }
}

$stmt = $conn->query(
    "SELECT
        aa.application_id,
        aa.reason,
        aa.living_situation,
        aa.experience,
        aa.status,
        aa.application_date,
        u.full_name,
        u.email,
        a.name AS animal_name
     FROM adoption_applications aa
     JOIN users u ON aa.user_id = u.id
     JOIN animals a ON aa.animal_id = a.animal_id
     ORDER BY aa.application_date DESC"
);

$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Adoption Applications</title>
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

        .application {
            background: white;
            padding: 20px;
            margin-bottom: 18px;
            border-radius: 10px;
            box-shadow: 0 2px 8px #00000010;
        }

        h1, h2 {
            color: #286443;
        }

        .message {
            padding: 12px;
            background: #e3f4e8;
            border-radius: 6px;
        }

        .details {
            line-height: 1.7;
            overflow-wrap: anywhere;
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

    <a class="back" href="staff-dashboard.php">
        Back to Staff Dashboard
    </a>

    <h1>Manage Adoption Applications</h1>

    <?php if ($message !== ''): ?>
        <p class="message"><?= e($message) ?></p>
    <?php endif; ?>

    <?php if (empty($applications)): ?>
        <p>No adoption applications have been submitted yet.</p>
    <?php else: ?>

        <?php foreach ($applications as $application): ?>
            <section class="application">
                <h2>
                    Application #<?= e($application['application_id']) ?>
                    — <?= e($application['animal_name']) ?>
                </h2>

                <div class="details">
                    <strong>Applicant:</strong>
                    <?= e($application['full_name']) ?><br>

                    <strong>Email:</strong>
                    <?= e($application['email']) ?><br>

                    <strong>Application date:</strong>
                    <?= e($application['application_date']) ?><br>

                    <strong>Living situation:</strong>
                    <?= e($application['living_situation']) ?><br>

                    <strong>Reason for adoption:</strong><br>
                    <?= nl2br(e($application['reason'])) ?><br>

                    <strong>Previous experience:</strong><br>
                    <?= nl2br(e($application['experience'])) ?><br>

                    <strong>Current status:</strong>
                    <?= e($application['status']) ?>
                </div>

                <form method="POST">
                    <input
                        type="hidden"
                        name="application_id"
                        value="<?= e($application['application_id']) ?>"
                    >

                    <label for="status-<?= e($application['application_id']) ?>">
                        Update status:
                    </label>

                    <select
                        id="status-<?= e($application['application_id']) ?>"
                        name="status"
                        required
                    >
                        <?php foreach (['Pending', 'Approved', 'Rejected'] as $option): ?>
                            <option
                                value="<?= e($option) ?>"
                                <?= $application['status'] === $option ? 'selected' : '' ?>
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