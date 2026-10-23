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
                'SELECT id, full_name, email, password_hash
                 FROM users
                 WHERE email = ?'
            );
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];

                header('Location: dashboard.php');
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
    <title>Sign In | Pawsitive</title>
</head>
<body>
    <h1>Welcome back to Pawsitive</h1>
    <p>Sign in to continue.</p>

    <?php if ($error !== ''): ?>
        <p role="alert">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="signin.php">
        <label for="email">Email address</label>
        <input
            id="email"
            name="email"
            type="email"
            required
            autocomplete="email"
        >

        <label for="password">Password</label>
        <input
            id="password"
            name="password"
            type="password"
            required
            autocomplete="current-password"
        >

        <button type="submit">Sign In</button>
    </form>

    <p>Don't have an account? <a href="signup.php">Create an account</a></p>
</body>
</html>
