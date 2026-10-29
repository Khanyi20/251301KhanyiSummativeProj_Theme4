<?php

session_start();

require_once __DIR__ . '/../../backend/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter your email and password.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email or password.';
    } else {
        try {
            $stmt = $conn->prepare(
                'SELECT my_row_id AS id, full_name, email, password_hash, role
                FROM users
                WHERE email = ?
                LIMIT 1'
            );
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                if ($user['role'] === 'staff') {
                    header('Location: staff_dashboard.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit;
            }

            $error = 'Invalid email or password.';
        } catch (PDOException $e) {
            error_log('Signin failed: ' . $e->getMessage());
            $error = 'Unable to sign in right now. Please try again.';
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
    <title>Sign In</title>
</head>
<body>
    <h1>Welcome back to <img src="/frontend/assets/Pawsitive Animal Shelter_logo_cutout 2.png" alt="Pawsitive"></h1>
    <p>Sign in to continue.</p>

    <div class="form-container">
    <?php if ($error !== ''): ?>
        <p role="alert">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>
    

    <form method="POST" action="/frontend/pages/signin.php">
        <div class="form-fields">
        <label for="email">Email address</label>
        <input
            id="email"
            name="email"
            type="email"
            required
            autocomplete="email"
        >
        </div>


        <div class="form-fields">
            <label for="password">Password</label>
            <input
                id="password"
                name="password"
                type="password"
                required
                autocomplete="current-password"
            >
        </div>

        <button class="btn-submit" type="submit">Sign In</button>

        <p class="form-text">
        Don't have an account? <a href="/frontend/pages/signup.php">Create an account</a>
        </p>
    </form>
    </div>

    
</body>
</html>