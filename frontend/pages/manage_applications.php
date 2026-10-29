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
    <link rel="stylesheet" href="/frontend/css/common.css">
    <link rel="stylesheet" href="/frontend/css/staff.css">
    <title>Manage Adoption Applications</title>
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
            <a class="active" href="/frontend/pages/manage_applications.php" aria-current="page">
                Applications
            </a>
            <a href="/frontend/pages/manage_bookings.php">
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
            <h1>Manage Adoption Applications</h1>
            <p class="muted">Review adoption applications and update their status.</p>
        </div>
        <span class="staff-badge">STAFF</span>
    </div>

    <?php if ($message !== ''): ?>
        <p class="message success" role="status"><?= e($message) ?></p>
    <?php endif; ?>

    <?php if (empty($applications)): ?>
        <section class="staff-panel">
            <p class="muted">No adoption applications have been submitted yet.</p>
        </section>
    <?php else: ?>

        <?php foreach ($applications as $application): ?>
            <section class="staff-panel">
                <h2>
                    Application #<?= e($application['application_id']) ?>
                    — <?= e($application['animal_name']) ?>
                </h2>

                <div class="staff-details">
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

                <form class="staff-form" method="POST">
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

    </main>
</div>
</body>
</html>