<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$fullName = trim($_POST['full_name'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');
$memberVisitor = trim($_POST['member_visitor'] ?? '');
$message = trim($_POST['message'] ?? '');

$allowedTypes = ['Member', 'Visitor', 'Other'];
$errors = [];

if ($memberVisitor === '' || !in_array($memberVisitor, $allowedTypes, true)) {
    $errors[] = 'Please choose whether you are a member, visitor, or other.';
}

if ($message === '') {
    $errors[] = 'Message is required.';
}

if (mb_strlen($fullName) > 100) {
    $errors[] = 'Full name is too long.';
}

if (mb_strlen($telephone) > 20) {
    $errors[] = 'Telephone is too long.';
}

if (!empty($errors)) {
    header('Location: index.php?status=error');
    exit();
}

$stmt = $conn->prepare(
    'INSERT INTO messages (full_name, telephone, member_visitor, message) VALUES (?, ?, ?, ?)'
);
$stmt->bind_param('ssss', $fullName, $telephone, $memberVisitor, $message);
$stmt->execute();
$stmt->close();
$conn->close();

header('Location: index.php?status=success');
exit();
?>
