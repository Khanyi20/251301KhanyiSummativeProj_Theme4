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
    <title>Animals Available for Adoption</title>

    <style>
        * {
            box-sizing: border-box;
        }

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

        header h1 {
            margin: 0;
            font-size: 26px;
        }

        header a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        main {
            max-width: 1150px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .intro {
            text-align: center;
            margin-bottom: 30px;
        }

        .intro h2 {
            color: #315c45;
            font-size: 32px;
        }

        .intro p {
            color: #59655d;
            line-height: 1.6;
        }

        .animal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 22px;
        }

        .animal-card {
            background: white;
            border-radius: 13px;
            overflow: hidden;
            border: 1px solid #e3e9e3;
            box-shadow: 0 3px 12px #0000000a;
        }

        .animal-image {
            height: 190px;
            width: 100%;
            object-fit: cover;
            background: #e8f0e9;
            display: block;
        }

        .image-placeholder {
            height: 190px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e8f0e9;
            font-size: 65px;
        }

        .animal-details {
            padding: 20px;
        }

        .animal-details h3 {
            color: #315c45;
            margin-top: 0;
            font-size: 23px;
        }

        .animal-details p {
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .status {
            display: inline-block;
            background: #e3f3e6;
            color: #245c35;
            border-radius: 20px;
            padding: 6px 10px;
            font-size: 13px;
            font-weight: bold;
        }

        .empty, .error {
            text-align: center;
            padding: 25px;
            background: white;
            border-radius: 10px;
        }

        .error {
            color: #a12626;
        }

        .back-link {
            display: inline-block;
            margin-top: 25px;
            background: #315c45;
            color: white;
            padding: 11px 16px;
            border-radius: 7px;
            text-decoration: none;
        }

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
        <section class="animal-grid">
            <?php foreach ($animals as $animal): ?>
                <article class="animal-card">
                    <?php if (!empty($animal['image_url'])): ?>
                        <img
                            class="animal-image"
                            src="<?= htmlspecialchars($animal['image_url'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($animal['name'], ENT_QUOTES, 'UTF-8') ?>"
                            loading="lazy"
                        >
                    <?php else: ?>
                        <div class="image-placeholder" aria-hidden="true">
                            <?= $animal['species'] === 'Cat' ? 'cat' : 'dog' ?>
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

</body>
</html>
