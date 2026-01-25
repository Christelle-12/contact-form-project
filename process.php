<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $full_name = htmlspecialchars($_POST['full_name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }
    
    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $full_name, $email, $message);
    
    // Execute and show result
    if ($stmt->execute()) {
        // Redirect to success page
        header("Location: success.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    
    // Close connections
    $stmt->close();
    $conn->close();
} else {
    // If someone tries to access directly
    header("Location: index.html");
    exit();
}
?>