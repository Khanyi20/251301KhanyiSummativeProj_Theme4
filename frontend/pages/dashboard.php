
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

$userName = $_SESSION['user_name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Pawsitive</title>
</head>
<body>
    <h1>Welcome to your Pawsitive dashboard,
        <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>!
    </h1>

    <p>You are successfully signed in.</p>

    <a href="signout.php">Sign out</a>
</body>
</html>
