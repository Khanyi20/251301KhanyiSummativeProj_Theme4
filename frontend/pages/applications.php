```php
<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: signin.php');
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
    <title>My Applications</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #26352d;
            background: #f5f7f4;
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
            max-width: 1050px;
            margin: 35px auto;
            padding: 0 20px;
        }

        h2 { color: #315c45; }

        .application-card {
            background: white;
            padding: 22px;
            margin-bottom: 20px;
            border-radius: 12px;
            border: 1px solid #e3e9e3;
            box-shadow: 0 3px 12px #0000000a;
        }

        .application-card h3 {
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

        .status.approved {
            background: #e3f3e6;
            color: #245c35;
        }

        .status.rejected {
            background: #fde8e8;
            color: #a12626;
        }

        .message {
            padding: 15px;
            border-radius: 8px;
            background: white;
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
```