<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

require_once __DIR__ . '/../../backend/db.php';

$error = '';
$success = '';
$animal = null;

$animalId = filter_input(INPUT_GET, 'animal_id', FILTER_VALIDATE_INT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $animalId = filter_input(INPUT_POST, 'animal_id', FILTER_VALIDATE_INT);
    $reason = trim($_POST['reason'] ?? '');
    $livingSituation = trim($_POST['living_situation'] ?? '');
    $experience = trim($_POST['experience'] ?? '');

    if (!$animalId || $reason === '' || $livingSituation === '') {
        $error = 'Please complete all required fields.';
    } else {
        try {
            $check = $conn->prepare(
                "SELECT animal_id, name FROM animals
                 WHERE animal_id = ? AND status = 'Available'"
            );
            $check->execute([$animalId]);
            $animal = $check->fetch();

            if (!$animal) {
                $error = 'This animal is no longer available for adoption.';
            } else {
                $stmt = $conn->prepare(
                    "INSERT INTO adoption_applications
                    (user_id, animal_id, reason, living_situation, experience)
                    VALUES (?, ?, ?, ?, ?)"
                );

                $stmt->execute([
                    $_SESSION['user_id'],
                    $animalId,
                    $reason,
                    $livingSituation,
                    $experience !== '' ? $experience : null
                ]);

                $success = 'Your adoption application has been submitted successfully!';
            }
        } catch (PDOException $e) {
            error_log('Adoption application failed: ' . $e->getMessage());
            $error = 'We could not submit your application. Please try again.';
        }
    }
}

if (!$animal && $animalId) {
    try {
        $stmt = $conn->prepare(
            "SELECT animal_id, name FROM animals
             WHERE animal_id = ? AND status = 'Available'"
        );
        $stmt->execute([$animalId]);
        $animal = $stmt->fetch();
    } catch (PDOException $e) {
        error_log('Animal lookup failed: ' . $e->getMessage());
        $error = 'We could not load the animal details.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/frontend/css/common.css">
    <link rel="stylesheet" href="/frontend/css/customer.css">
    <title>Adoption Application</title>
</head>
<body>
<header class="page-header">
    <h1>Pawsitive</h1>
    <nav>
        <a href="/frontend/pages/animals.php">Animals</a>
        <a href="/frontend/pages/dashboard.php">Dashboard</a>
        <a href="/frontend/pages/signout.php">Sign Out</a>
    </nav>
</header>

<main class="page-container">
    <section class="form-card">
        <h2>Adoption Application</h2>
        <p class="help">Tell us a little about yourself and the home you can offer an animal.</p>

        <?php if ($success !== ''): ?>
            <div class="message success" role="status">
                <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <a class="button" href="applications.php">View My Applications</a>
            <a class="button" href="animals.php">Browse More Animals</a>

        <?php elseif (!$animal): ?>
            <?php if ($error !== ''): ?>
                <div class="message error" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>
            <p>We couldn't find an available animal for this application.</p>
            <a class="button" href="animals.php">Browse Available Animals</a>

        <?php else: ?>
            <?php if ($error !== ''): ?>
                <div class="message error" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <p>You are applying to adopt:
                <strong><?= htmlspecialchars($animal['name'], ENT_QUOTES, 'UTF-8') ?></strong>
            </p>

            <form class="customer-form" method="POST" action="apply.php">
                <input type="hidden" name="animal_id"
                       value="<?= (int) $animal['animal_id'] ?>">

                <label for="reason">Why would you like to adopt this animal? *</label>
                <textarea id="reason" name="reason" required maxlength="5000">
                    <?= htmlspecialchars($_POST['reason'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </textarea>

                <label for="living_situation">What is your living situation? *</label>
                <select id="living_situation" name="living_situation" required>
                    <option value="">Choose an option</option>
                    <?php
                    $situations = ['House with a yard', 'House without a yard', 'Apartment', 'Farm', 'Other'];
                    $selectedSituation = $_POST['living_situation'] ?? '';
                    foreach ($situations as $situation):
                    ?>
                        <option value="<?= htmlspecialchars($situation, ENT_QUOTES, 'UTF-8') ?>"
                            <?= $selectedSituation === $situation ? 'selected' : '' ?>>
                            <?= htmlspecialchars($situation, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="experience">Have you cared for animals before? (Optional)</label>
                <textarea id="experience" name="experience" maxlength="5000">
                    <?= htmlspecialchars($_POST['experience'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </textarea>

                <button type="submit">Submit Application</button>
            </form>
        <?php endif; ?>
    </section>
</main>
</body>
</html>