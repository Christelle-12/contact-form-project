<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

$full_name = htmlspecialchars($_POST['full_name']);
$email = htmlspecialchars($_POST['email']);
$telephone = htmlspecialchars($_POST['telephone']);
$address = htmlspecialchars($_POST['address']);
$age = intval($_POST['age']);
$gender = htmlspecialchars($_POST['gender']);
$member_visitor = htmlspecialchars($_POST['member_visitor']);
$message = htmlspecialchars($_POST['message']);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
die("Invalid email format");
}

$stmt = $conn->prepare("INSERT INTO users
(full_name,email,telephone,address,age,gender,member_visitor,message)
VALUES (?,?,?,?,?,?,?,?)");

$stmt->bind_param(
"ssssisss",
$full_name,
$email,
$telephone,
$address,
$age,
$gender,
$member_visitor,
$message
);

if ($stmt->execute()) {

header("Location: success.html");
exit();

} else {

echo "Error: " . $stmt->error;

}

$stmt->close();
$conn->close();

} else {

header("Location: index.html");
exit();

}
?>