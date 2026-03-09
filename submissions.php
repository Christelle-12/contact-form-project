<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

if (isset($_GET['delete'])) {

$id = intval($_GET['delete']);

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i",$id);
$stmt->execute();
$stmt->close();

header("Location: submissions.php");
exit();
}

$search = "";

if(isset($_GET['search']) && !empty($_GET['search'])){

$search = $_GET['search'];

$stmt = $conn->prepare("SELECT * FROM users
WHERE full_name LIKE ? OR email LIKE ?
ORDER BY created_at DESC");

$search_param = "%$search%";

$stmt->bind_param("ss",$search_param,$search_param);
$stmt->execute();

$result = $stmt->get_result();

}else{

$result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
</head>
<body>

<h2>Form Submissions</h2>

<a href="logout.php">Logout</a>

<br><br>

<form method="GET">

<input type="text" name="search" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">

<button type="submit">Search</button>

</form>

<br>

<table border="1" cellpadding="10">

<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Telephone</th>
<th>Address</th>
<th>Age</th>
<th>Gender</th>
<th>Member</th>
<th>Message</th>
<th>Date</th>
<th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>

<tr>

<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['full_name']) ?></td>
<td><?= htmlspecialchars($row['email']) ?></td>
<td><?= htmlspecialchars($row['telephone']) ?></td>
<td><?= htmlspecialchars($row['address']) ?></td>
<td><?= htmlspecialchars($row['age']) ?></td>
<td><?= htmlspecialchars($row['gender']) ?></td>
<td><?= htmlspecialchars($row['member_visitor']) ?></td>
<td><?= htmlspecialchars($row['message']) ?></td>
<td><?= $row['created_at'] ?></td>

<td>
<a href="submissions.php?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this submission?')">Delete</a>
</td>

</tr>

<?php endwhile; ?>

</table>

</body>
</html>