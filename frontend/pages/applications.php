<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: /frontend/pages/signin.php');
    exit;
}

require_once __DIR__ . '/../../backend/db.php';

$applications = [];
$error = '';

try {
    $stmt = $conn->prepare(
        "SELECT
            aa.application_id,
            aa.reason,
            aa.living_situation,
            aa.experience,
            aa.status,
            aa.application_date,
            a.name AS animal_name,
            a.species,
            a.breed
         FROM adoption_applications aa
         INNER JOIN animals a ON aa.animal_id = a.animal_id
         WHERE aa.user_id = ?
         ORDER BY aa.application_date DESC"
    );

    $stmt->execute([$_SESSION['user_id']]);
    $applications = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log('Loading applications failed: ' . $e->getMessage());
    $error = 'We could not load your applications. Please try again later.';
}

function escapeHtml($value) {
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
    <title>My Applications</title>

</head>

<body>
<header>
    <h1>Pawsitive</h1>
    <nav>
        <a href="/frontend/pages/dashboard.php">Dashboard</a>
        &nbsp; | &nbsp;
        <a href="/frontend/pages/animals.php">Browse Animals</a>
        &nbsp; | &nbsp;
        <a href="/frontend/pages/signout.php">Sign Out</a>
    </nav>
</header>

<main>
    <h2>My Adoption Applications</h2>
    <p class="muted">
        Keep track of the adoption applications you have submitted.
    </p>

    <?php if ($error !== ''): ?>

        <div class="message error" role="alert">
            <?= escapeHtml($error) ?>
        </div>

    <?php elseif (empty($applications)): ?>

        <div class="message">
            <p>You haven't submitted any adoption applications yet.</p>
            <a class="button" href="/frontend/pages/animals.php">Browse Available Animals</a>
        </div>

    <?php else: ?>

        <?php foreach ($applications as $application): ?>
            <?php
                $statusClass = strtolower($application['status']);
                if (!in_array($statusClass, ['pending', 'approved', 'rejected'], true)) {
                    $statusClass = '';
                }
            ?>

            <article class="application-card">
                <h3>
                    <?= escapeHtml($application['animal_name']) ?>
                </h3>

                <p>
                    <strong>Species:</strong>
                    <?= escapeHtml($application['species']) ?>
                </p>

                <?php if (!empty($application['breed'])): ?>
                    <p>
                        <strong>Breed:</strong>
                        <?= escapeHtml($application['breed']) ?>
                    </p>
                <?php endif; ?>

                <p>
                    <strong>Application date:</strong>
                    <?= escapeHtml(date('d M Y', strtotime($application['application_date']))) ?>
                </p>

                <p>
                    <strong>Status:</strong>
                    <span class="status <?= escapeHtml($statusClass) ?>">
                        <?= escapeHtml($application['status']) ?>
                    </span>
                </p>

                <p>
                    <strong>Why I want to adopt:</strong><br>
                    <?= nl2br(escapeHtml($application['reason'])) ?>
                </p>

                <p>
                    <strong>Living situation:</strong>
                    <?= escapeHtml($application['living_situation']) ?>
                </p>

                <?php if (!empty($application['experience'])): ?>
                    <p>
                        <strong>Previous animal-care experience:</strong><br>
                        <?= nl2br(escapeHtml($application['experience'])) ?>
                    </p>
                <?php endif; ?>
            </article>

        <?php endforeach; ?>

    <?php endif; ?>

    <a class="button" href="/frontend/pages/dashboard.php">Back to Dashboard</a>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> Pawsitive Animal Shelter</p>
</footer>
</body>
</html>
