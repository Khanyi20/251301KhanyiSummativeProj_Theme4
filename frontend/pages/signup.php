<?php
session_start();

require_once __DIR__ . '/../../backend/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($fullName === '' || $email === '' || $password === '') {
        $error = 'Please complete all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Your password must be at least 8 characters.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Your passwords do not match.';
    } else {
        try {
            $check = $conn->prepare(
                'SELECT id FROM users WHERE email = ?'
            );
            $check->execute([$email]);

            if ($check->fetch()) {
                $error = 'An account with this email already exists.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $insert = $conn->prepare(
                    'INSERT INTO users (full_name, email, password_hash)
                     VALUES (?, ?, ?)'
                );
                $insert->execute([$fullName, $email, $hash]);

                $success = 'Your account was created successfully. You can now sign in.';
            }
        } catch (PDOException $e) {
             error_log('Signup failed: ' . $e->getMessage());
             error_log('Signup SQL error code: ' . $e->getCode());
            $error = 'We could not create your account. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/frontend/css/login_style.css">
    <title>Sign Up</title>
</head>
<body>

    <div class="container">
        
    <div class="login-text">
    <h1>Create your <img class="logo" src="/frontend/assets/Pawsitive Animal Shelter_logo_cutout 2.png" alt="Pawsitive"> account</h1>
    <!-- <h2><a href="/frontend/pages/landingpage.php">back Home</a></h2> -->
    </div>
    
    <?php if ($error !== ''): ?>
        <p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <?php if ($success !== ''): ?>
        <p role="status"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
        <p><a href="/frontend/pages/signin.php">Go to sign in</a></p>
    <?php else: ?>
    
        <form class="form" method="POST" action="/frontend/pages/signup.php">
        <div class="form-container">

            <div class="separate-fields">
                <div class="form-fields">
                    <label for="full_name">Full name</label>
                    <input id="full_name" name="full_name" type="text"
                        maxlength="100" required autocomplete="name">
                </div>

                <div class="form-fields">
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email"
                        maxlength="255" required autocomplete="email">
                </div>
            </div>

            <div class="separate-fields">
                <div class="form-fields">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password"
                        minlength="8" required autocomplete="new-password">
                </div>

                <div class="form-fields">
                    <label for="confirm_password">Confirm password</label>
                    <input id="confirm_password" name="confirm_password"
                        type="password" minlength="8" required
                        autocomplete="new-password">
                </div>
            </div>
            </div>
            <button class="btn-submit" type="submit">Create account</button>

            <p class="form-text">
                Already have an account? <a href="/frontend/pages/signin.php">Sign in</a>
            </p>
       
        </form>
    
                

        
    <?php endif; ?>
    </div>
</body>
</html>