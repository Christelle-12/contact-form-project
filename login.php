<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: submissions.php');
    exit();
}

$error = '';
$registered = isset($_GET['registered']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Please enter your username and password.';
    } else {
        $stmt = $conn->prepare('SELECT id, username, password FROM admins WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        $stmt->close();

        if (!$admin || !password_verify($password, $admin['password'])) {
            $error = 'Invalid username or password.';
        } else {
            $_SESSION['admin_id'] = (int) $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];

            header('Location: submissions.php');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-panel auth-panel-copy">
            <span class="eyebrow">Church Message Desk</span>
            <h1>Review attendee messages in one focused place.</h1>
            <p>
                Sign in to view prayer requests, suggestions, and follow-up notes from members
                and visitors. The admin area is intentionally simple so the team can respond quickly.
            </p>
            <div class="hero-quote">
                Stewardship also means caring well for the words people trust us with.
            </div>
        </section>

        <section class="auth-panel auth-panel-form">
            <div class="auth-card-header">
                <h2>Admin Sign In</h2>
                <p>Use your registered admin account to continue.</p>
            </div>

            <?php if ($registered): ?>
                <div class="alert success">Registration successful. You can sign in now.</div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div class="alert error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="field-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" maxlength="100" required>
                </div>

                <div class="field-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="primary-btn">Sign In</button>
            </form>

            <p class="auth-footer-link">
                Need to create the first admin? <a href="admin_register.php">Register here</a>.
            </p>
        </section>
    </main>
</body>
</html>
