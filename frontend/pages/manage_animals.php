<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

if (($_SESSION['user_role'] ?? '') !== 'staff') {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/../../backend/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $species = trim($_POST['species'] ?? '');
        $breed = trim($_POST['breed'] ?? '');
        $age = trim($_POST['age'] ?? '');
        $gender = trim($_POST['gender'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $imageUpload = $_FILES['image'] ?? null;
        $imagePath = null;
        $imageFilePath = null;

        $validSpecies = ['Dog', 'Cat', 'Other'];
        $validGenders = ['', 'Male', 'Female', 'Unknown'];
        $maxImageSize = 5 * 1024 * 1024;

        if ($name === '' || strlen($name) > 100) {
            $error = 'Enter an animal name of no more than 100 characters.';
        } elseif (!in_array($species, $validSpecies, true)) {
            $error = 'Choose a valid species.';
        } elseif (strlen($breed) > 100 || strlen($age) > 50) {
            $error = 'The breed or age entry is too long.';
        } elseif (!in_array($gender, $validGenders, true)) {
            $error = 'Choose a valid gender.';
        } elseif ($imageUpload && $imageUpload['error'] !== UPLOAD_ERR_NO_FILE &&
            $imageUpload['error'] !== UPLOAD_ERR_OK) {
            $error = 'The image could not be uploaded. Choose another image and try again.';
        } elseif ($imageUpload && $imageUpload['error'] === UPLOAD_ERR_OK) {
            if ($imageUpload['size'] > $maxImageSize) {
                $error = 'The image must be 5 MB or smaller.';
            } else {
                $fileInfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $fileInfo->file($imageUpload['tmp_name']);
                $imageTypes = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp',
                ];

                if (!isset($imageTypes[$mimeType]) || !getimagesize($imageUpload['tmp_name'])) {
                    $error = 'Choose a valid JPG, PNG, or WebP image.';
                } else {
                    $uploadDirectory = __DIR__ . '/../assets/animals';
                    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true)) {
                        $error = 'The image upload folder is unavailable. Please contact the site administrator.';
                    } elseif (!is_writable($uploadDirectory)) {
                        $error = 'The image upload folder is not writable. Please contact the site administrator.';
                    } else {
                        $fileName = bin2hex(random_bytes(16)) . '.' . $imageTypes[$mimeType];
                        $imageFilePath = $uploadDirectory . DIRECTORY_SEPARATOR . $fileName;
                        $imagePath = '/frontend/assets/animals/' . $fileName;

                        if (!move_uploaded_file($imageUpload['tmp_name'], $imageFilePath)) {
                            $error = 'The image could not be saved. Please try again.';
                            $imageFilePath = null;
                            $imagePath = null;
                        }
                    }
                }
            }
        }

        if ($error === '') {
            try {
                $stmt = $conn->prepare(
                    "INSERT INTO animals
                    (name, species, breed, age, gender, description, image_url, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'Available')"
                );

                $stmt->execute([
                    $name,
                    $species,
                    $breed !== '' ? $breed : null,
                    $age !== '' ? $age : null,
                    $gender !== '' ? $gender : null,
                    $description !== '' ? $description : null,
                    $imagePath
                ]);

                $success = 'Animal added successfully.';
            } catch (PDOException $e) {
                if ($imageFilePath && is_file($imageFilePath)) {
                    unlink($imageFilePath);
                }
                error_log('Adding animal failed: ' . $e->getMessage());
                $error = 'We could not add the animal. Please check the database and try again.';
            } catch (Throwable $e) {
                if ($imageFilePath && is_file($imageFilePath)) {
                    unlink($imageFilePath);
                }
                error_log('Saving uploaded animal image failed: ' . $e->getMessage());
                $error = 'We could not save the animal. Please try again.';
            }
        }
    } elseif ($action === 'status') {
        $animalId = filter_input(INPUT_POST, 'animal_id', FILTER_VALIDATE_INT);
        $newStatus = $_POST['status'] ?? '';
        $allowedStatuses = ['Available', 'Adopted', 'Unavailable'];

        if (!$animalId || !in_array($newStatus, $allowedStatuses, true)) {
            $error = 'Invalid animal or status.';
        } else {
            try {
                $stmt = $conn->prepare(
                    "UPDATE animals SET status = ? WHERE animal_id = ?"
                );
                $stmt->execute([$newStatus, $animalId]);

                $success = $stmt->rowCount() > 0
                    ? 'Animal status updated.'
                    : 'No change was made. The animal may already have that status.';
            } catch (PDOException $e) {
                error_log('Updating animal status failed: ' . $e->getMessage());
                $error = 'We could not update the animal status.';
            }
        }
    } else {
        $error = 'Invalid request.';
    }
}

try {
    $stmt = $conn->query(
        "SELECT animal_id, name, species, breed, age, gender, status, created_at
         FROM animals
         ORDER BY created_at DESC"
    );
    $animals = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Loading animals for the staff failed: ' . $e->getMessage());
    $animals = [];
    $error = $error ?: 'Animal list could not be loaded';
}

function escapeAnimalAdmin($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/frontend/css/common.css">
    <link rel="stylesheet" href="/frontend/css/staff.css">
    <title>Manage Animals</title>
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
            <a class="active" href="/frontend/pages/manage_animals.php" aria-current="page">
                Animals
            </a>
            <a href="/frontend/pages/manage_applications.php">
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
            <h1>Manage Animals</h1>
            <p class="muted">Add animals and update their adoption status.</p>
        </div>
        <span class="staff-badge">STAFF</span>
    </div>

    <?php if ($success !== ''): ?>
        <div class="message success" role="status">
            <?= escapeAnimalAdmin($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="message error" role="alert">
            <?= escapeAnimalAdmin($error) ?>
        </div>
    <?php endif; ?>

    <section class="staff-panel">
        <h2>Add a New Animal</h2>
        <p class="muted">New animals are listed as available for adoption.</p>

        <form class="staff-form" method="POST" action="manage_animals.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">

            <div class="staff-form-grid">
                <div>
                    <label for="name">Animal name *</label>
                    <input id="name" name="name" maxlength="100" required>
                </div>

                <div>
                    <label for="species">Species *</label>
                    <select id="species" name="species" required>
                        <option value="">Choose species</option>
                        <option value="Dog">Dog</option>
                        <option value="Cat">Cat</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div>
                    <label for="breed">Breed</label>
                    <input id="breed" name="breed" maxlength="100">
                </div>

                <div>
                    <label for="age">Age</label>
                    <input id="age" name="age" maxlength="50" placeholder="e.g. 2 years">
                </div>

                <div>
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender">
                        <option value="">Not specified</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Unknown">Unknown</option>
                    </select>
                </div>

                <div>
                          <label for="image">Animal image (optional, JPG/PNG/WebP; max 5 MB)</label>
                          <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp">
                </div>
            </div>

            <label for="description">Description</label>
            <textarea id="description" name="description" maxlength="10000"></textarea>

            <button type="submit">Add Animal</button>
        </form>
    </section>

    <section class="staff-panel">
        <h2>Existing Animals</h2>

        <?php if (empty($animals)): ?>
            <p class="muted">No animals were found, or the list could not be loaded.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="staff-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Species</th>
                            <th>Breed</th>
                            <th>Status</th>
                            <th>Update Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($animals as $animal): ?>
                        <tr>
                            <td><?= (int)$animal['animal_id'] ?></td>
                            <td><?= escapeAnimalAdmin($animal['name']) ?></td>
                            <td><?= escapeAnimalAdmin($animal['species']) ?></td>
                            <td><?= escapeAnimalAdmin($animal['breed']) ?></td>
                            <td><?= escapeAnimalAdmin($animal['status']) ?></td>
                            <td>
                                <form class="staff-status-form" method="POST" action="manage_animals.php">
                                    <input type="hidden" name="action" value="status">
                                    <input type="hidden" name="animal_id"
                                           value="<?= (int)$animal['animal_id'] ?>">
                                    <select name="status" aria-label="New status for <?= escapeAnimalAdmin($animal['name']) ?>">
                                        <?php foreach (['Available', 'Adopted', 'Unavailable'] as $status): ?>
                                            <option value="<?= $status ?>"
                                                <?= $animal['status'] === $status ? 'selected' : '' ?>>
                                                <?= $status ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit">Save</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>
</div>
</body>
</html>