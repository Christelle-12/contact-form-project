<?php
include 'db.php';

$SECRET_KEY = "CHURCH2026";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST['admin_key'] !== $SECRET_KEY) {
        $error = "Invalid admin key.";
    } else {

        $username = trim($_POST['username']);
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);

        if ($stmt->execute()) {
            header("Location: login.php?registered=1");
            exit();
        } else {
            $error = "Username already exists.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Registration</title>
</head>
<body>

<h2>Admin Registration</h2>

<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST">

Admin Secret Key:<br>
<input type="password" name="admin_key" required><br><br>

Username:<br>
<input type="text" name="username" required><br><br>

Password:<br>
<input type="password" name="password" required><br><br>

<button type="submit">Register</button>

</form>

<p><a href="login.php">Login here</a></p>

</body>
</html>