<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'db.php';

$allowedViews = ['active', 'archived', 'all'];
$view = $_GET['view'] ?? 'active';
$view = in_array($view, $allowedViews, true) ? $view : 'active';
$search = trim($_GET['search'] ?? '');

$conditions = [];
$types = '';
$params = [];

if ($search !== '') {
    $conditions[] = '(full_name LIKE ? OR telephone LIKE ? OR message LIKE ? OR member_visitor LIKE ?)';
    $searchTerm = '%' . $search . '%';
    $types .= 'ssss';
    array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
}

$selectFields = 'id, full_name, telephone, member_visitor, message, created_at';

if ($view === 'active') {
    $query = 'SELECT ' . $selectFields . ', 0 AS is_archived FROM messages';
    if ($conditions !== []) {
        $query .= ' WHERE ' . implode(' AND ', $conditions);
    }
    $query .= ' ORDER BY created_at DESC';
} elseif ($view === 'archived') {
    $query = 'SELECT ' . $selectFields . ', 1 AS is_archived FROM archived_messages';
    if ($conditions !== []) {
        $query .= ' WHERE ' . implode(' AND ', $conditions);
    }
    $query .= ' ORDER BY created_at DESC';
} else {
    $baseWhere = '';
    if ($conditions !== []) {
        $baseWhere = ' WHERE ' . implode(' AND ', $conditions);
    }

    $query = 'SELECT ' . $selectFields . ', 0 AS is_archived FROM messages' . $baseWhere
           . ' UNION ALL '
           . 'SELECT ' . $selectFields . ', 1 AS is_archived FROM archived_messages' . $baseWhere
           . ' ORDER BY created_at DESC';
}

if ($types !== '') {
    $stmt = $conn->prepare($query);

    if ($view === 'all') {
        $combinedTypes = $types . $types;
        $combinedParams = array_merge($params, $params);
        $stmt->bind_param($combinedTypes, ...$combinedParams);
    } else {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}

// Set headers for CSV download
$filename = 'messages_export_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Open output stream
$output = fopen('php://output', 'w');

// Write CSV header
fputcsv($output, ['Name', 'Type', 'Phone', 'Message', 'Date Submitted', 'Status']);

// Write data rows
while ($row = $result->fetch_assoc()) {
    $fullName = $row['full_name'] ?: 'Anonymous attendee';
    $telephone = $row['telephone'] ?: 'Not provided';
    $memberVisitor = $row['member_visitor'] ?: 'Unspecified';
    $message = $row['message'];
    $createdAt = $row['created_at'];
    $status = ((int) ($row['is_archived'] ?? 0) === 1) ? 'Archived' : 'Active';

    fputcsv($output, [$fullName, $memberVisitor, $telephone, $message, $createdAt, $status]);
}

// Close output stream
fclose($output);

// Close database connections
if (isset($stmt) && $stmt instanceof mysqli_stmt) {
    $stmt->close();
}
$conn->close();
exit();
?></content>
<parameter name="filePath">c:\xampp\htdocs\web-project\export.php