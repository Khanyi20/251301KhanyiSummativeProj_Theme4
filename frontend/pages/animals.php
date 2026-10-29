<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: /frontend/pages/signin.php');
    exit;
}

require_once __DIR__ . '/../../backend/db.php';

$error = '';
$animals = [];

try {
    $stmt = $conn->prepare(
        "SELECT animal_id, name, species, breed, age, gender,
                description, image_url
         FROM animals
         WHERE status = ?
         ORDER BY created_at DESC"
    );

    $stmt->execute(['Available']);
    $animals = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Animal listing failed: ' . $e->getMessage());
    $error = 'We could not load the animals right now. Please try again later.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/frontend/css/common.css">
    <link rel="stylesheet" href="/frontend/css/customer.css">
    <title>Animals Available for Adoption</title>

</head>
<body>

<header class="page-header">
    <h1>Pawsitive</h1>

    <nav>
        <a href="/frontend/pages/dashboard.php">Home</a>
        <a href="/frontend/pages/animals.php">Adopt</a>
        <a href="/frontend/pages/applications.php">Applications</a>
        <a href="/frontend/pages/mybookings.php">Bookings</a>
        <a href="/frontend/pages/signout.php">Sign Out</a>
    </nav>
</header>

<main>
    <section class="intro">
        <h2>Meet Your New Best Friend</h2>
        <p>Every animal deserves a loving home. Discover our animals waiting for adoption.</p>
    </section>

    <?php if ($error !== ''): ?>
        <p class="error" role="alert">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php elseif (count($animals) === 0): ?>
        <p class="empty">There are no animals listed for adoption right now. Please check again soon.</p>
    <?php else: ?>
        <div class="animal-filters">
            <input
                type="search"
                id="animal-search"
                placeholder="Search animals by name or breed..."
                aria-label="Search animals"
            >

            <select id="species-filter" aria-label="Filter by species">
                <option value="">All animals</option>
                <option value="dog">Dogs</option>
                <option value="cat">Cats</option>
            </select>
        </div>
        <section class="animal-grid">
            <?php foreach ($animals as $animal): ?>
                <article class="animal-card" 
                    data-species="<?= htmlspecialchars(
                    strtolower($animal['species']),
                    ENT_QUOTES,
                    'UTF-8'
                    ) ?>" 
                > 
                    <?php if (!empty($animal['image_url'])): ?>
                        <img
                            class="animal-image"
                            src="<?= htmlspecialchars($animal['image_url'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($animal['name'], ENT_QUOTES, 'UTF-8') ?>"
                            loading="lazy"
                        >
                    <?php else: ?>
                        <div class="image-placeholder" aria-hidden="true">
                            <?= $animal['species'] === 'cat' ? 'cat' : 'dog' ?>
                        </div>
                    <?php endif; ?>

                    <div class="animal-details">
                        <h3><?= htmlspecialchars($animal['name'], ENT_QUOTES, 'UTF-8') ?></h3>

                        <p>
                            <strong>Species:</strong>
                            <?= htmlspecialchars($animal['species'], ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <?php if (!empty($animal['breed'])): ?>
                            <p><strong>Breed:</strong>
                                <?= htmlspecialchars($animal['breed'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($animal['age'])): ?>
                            <p><strong>Age:</strong>
                                <?= htmlspecialchars($animal['age'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($animal['gender'])): ?>
                            <p><strong>Gender:</strong>
                                <?= htmlspecialchars($animal['gender'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        <?php endif; ?>

                        <p>
                            <?= htmlspecialchars($animal['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <span class="status">Available for adoption</span>
                        <a href="/frontend/pages/apply.php?animal_id=<?= (int) $animal['animal_id'] ?>"
                            style="display:inline-block;margin-top:15px;padding:10px 15px;
                            background:#315c45;color:white;text-decoration:none;border-radius:6px;">
                            Apply to Adopt
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <a class="back-link" href="/frontend/pages/dashboard.php">← Back to Dashboard</a>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> Pawsitive Animal Shelter</p>
</footer>

<script src="/frontend/Javascript/script.js"></script>
</body>
</html>
