<?php
session_start();
require_once 'db.php';

$secretKey = 'CHURCH2026';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminKey = trim($_POST['admin_key'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($adminKey !== $secretKey) {
        $error = 'The admin setup key is not correct.';
    } elseif ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $conn->prepare('INSERT INTO admins (username, password) VALUES (?, ?)');
            $stmt->bind_param('ss', $username, $hashedPassword);
            $stmt->execute();
            $stmt->close();
            $conn->close();

            header('Location: login.php?registered=1');
            exit();
        } catch (mysqli_sql_exception $exception) {
            $error = 'That username is already in use.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-panel auth-panel-copy">
            <span class="eyebrow">Admin Access</span>
            <h1>Set up a trusted account for message review.</h1>
            <p>
                Admin accounts can sign in and view attendee messages from one simple dashboard.
                Use the registration key once, then manage messages securely from the login page.
            </p>
            <ul class="feature-list">
                <li>Clean admin-only access</li>
                <li>Messages sorted by newest first</li>
                <li>Built around the new `admins` and `messages` tables</li>
            </ul>
        </section>

        <section class="auth-panel auth-panel-form">
            <div class="auth-card-header">
                <h2>Create Admin Account</h2>
                <p>Register an account for church staff or leadership.</p>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="field-group">
                    <label for="admin_key">Admin setup key</label>
                    <input type="password" id="admin_key" name="admin_key" required>
                </div>

                <div class="field-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" maxlength="100" required>
                </div>

                <div class="field-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="primary-btn">Create Account</button>
            </form>

            <p class="auth-footer-link">
                Already have an account? <a href="login.php">Sign in here</a>.
            </p>
        </section>
    </main>
</body>
</html>
