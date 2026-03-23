<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'db.php';

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare('DELETE FROM messages WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    header('Location: submissions.php');
    exit();
}

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $searchTerm = '%' . $search . '%';
    $stmt = $conn->prepare(
        'SELECT id, full_name, telephone, member_visitor, message, created_at
         FROM messages
         WHERE full_name LIKE ? OR telephone LIKE ? OR message LIKE ? OR member_visitor LIKE ?
         ORDER BY created_at DESC'
    );
    $stmt->bind_param('ssss', $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query(
        'SELECT id, full_name, telephone, member_visitor, message, created_at
         FROM messages
         ORDER BY created_at DESC'
    );
}

$messageCount = $conn->query('SELECT COUNT(*) AS total FROM messages')->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-page">
    <main class="dashboard-shell">
        <header class="dashboard-header">
            <div>
                <span class="eyebrow">Admin Dashboard</span>
                <h1>Attendee Messages</h1>
                <p>
                    Welcome, <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>.
                    Review the latest messages from members and visitors below.
                </p>
            </div>

            <div class="dashboard-actions">
                <div class="stat-card">
                    <span>Total messages</span>
                    <strong><?= (int) $messageCount ?></strong>
                </div>
                <a href="logout.php" class="secondary-btn">Logout</a>
            </div>
        </header>

        <section class="toolbar-card">
            <form method="GET" class="search-form">
                <input
                    type="text"
                    name="search"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Search name, phone, role, or message"
                >
                <button type="submit" class="primary-btn">Search</button>
                <?php if ($search !== ''): ?>
                    <a href="submissions.php" class="secondary-btn">Clear</a>
                <?php endif; ?>
            </form>
        </section>

        <section class="messages-grid">
            <?php if ($result->num_rows === 0): ?>
                <article class="message-card empty-state">
                    <h2>No messages found</h2>
                    <p>When attendees submit the form, their messages will appear here.</p>
                </article>
            <?php endif; ?>

            <?php while ($row = $result->fetch_assoc()): ?>
                <article class="message-card">
                    <div class="message-card-top">
                        <span class="badge"><?= htmlspecialchars($row['member_visitor'] ?: 'Unspecified') ?></span>
                        <time><?= htmlspecialchars($row['created_at']) ?></time>
                    </div>

                    <h2><?= htmlspecialchars($row['full_name'] ?: 'Anonymous attendee') ?></h2>

                    <?php if (!empty($row['telephone'])): ?>
                        <p class="meta-line">Phone: <?= htmlspecialchars($row['telephone']) ?></p>
                    <?php endif; ?>

                    <p class="message-body"><?= nl2br(htmlspecialchars($row['message'])) ?></p>

                    <div class="message-card-actions">
                        <a
                            href="submissions.php?delete=<?= (int) $row['id'] ?>"
                            class="danger-link"
                            onclick="return confirm('Delete this message?');"
                        >
                            Delete
                        </a>
                    </div>
                </article>
            <?php endwhile; ?>
        </section>
    </main>
</body>
</html>
<?php
if (isset($stmt) && $stmt instanceof mysqli_stmt) {
    $stmt->close();
}
$conn->close();
?>
