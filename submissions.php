<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'db.php';

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
                <a href="logout.php" class="secondary-btn compact-btn">Logout</a>
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
                <button type="submit" class="primary-btn compact-btn">Search</button>
                <?php if ($search !== ''): ?>
                    <a href="submissions.php" class="secondary-btn compact-btn">Clear</a>
                <?php endif; ?>
            </form>
        </section>

        <section class="table-card">
            <?php if ($result->num_rows === 0): ?>
                <div class="empty-state">
                    <h2>No messages found</h2>
                    <p>When attendees submit the form, their messages will appear here.</p>
                </div>
            <?php else: ?>
                <div class="table-scroll">
                    <table class="messages-table">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Type</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Preview</th>
                                <th scope="col">Date</th>
                                <th scope="col" class="actions-col">View</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php
                                $fullName = $row['full_name'] ?: 'Anonymous attendee';
                                $telephone = $row['telephone'] ?: 'Not provided';
                                $memberVisitor = $row['member_visitor'] ?: 'Unspecified';
                                $preview = mb_strimwidth($row['message'], 0, 70, '...');
                                ?>
                                <tr>
                                    <td data-label="Name">
                                        <div class="table-primary"><?= htmlspecialchars($fullName) ?></div>
                                    </td>
                                    <td data-label="Type">
                                        <span class="badge"><?= htmlspecialchars($memberVisitor) ?></span>
                                    </td>
                                    <td data-label="Phone"><?= htmlspecialchars($telephone) ?></td>
                                    <td data-label="Preview" class="message-preview-cell">
                                        <?= htmlspecialchars($preview) ?>
                                    </td>
                                    <td data-label="Date"><?= htmlspecialchars($row['created_at']) ?></td>
                                    <td data-label="View" class="table-actions">
                                        <button
                                            type="button"
                                            class="icon-btn"
                                            data-open-message
                                            data-name="<?= htmlspecialchars($fullName) ?>"
                                            data-type="<?= htmlspecialchars($memberVisitor) ?>"
                                            data-phone="<?= htmlspecialchars($telephone) ?>"
                                            data-date="<?= htmlspecialchars($row['created_at']) ?>"
                                            data-message="<?= htmlspecialchars($row['message']) ?>"
                                            aria-label="View full message"
                                            title="View full message"
                                        >
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M12 5c5.23 0 9.27 3.34 11 7-1.73 3.66-5.77 7-11 7S2.73 15.66 1 12c1.73-3.66 5.77-7 11-7Zm0 2C8.39 7 5.33 9.11 3.53 12 5.33 14.89 8.39 17 12 17s6.67-2.11 8.47-5C18.67 9.11 15.61 7 12 7Zm0 2.5A2.5 2.5 0 1 1 9.5 12 2.5 2.5 0 0 1 12 9.5Z"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <dialog class="message-modal" id="messageModal">
        <div class="modal-header">
            <div>
                <p class="modal-kicker">Full Message</p>
                <h2 id="modalName">Message details</h2>
            </div>
            <button type="button" class="icon-btn modal-close-btn" id="closeMessageModal" aria-label="Close modal">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6.4 5 12 10.6 17.6 5 19 6.4 13.4 12 19 17.6 17.6 19 12 13.4 6.4 19 5 17.6 10.6 12 5 6.4Z"/>
                </svg>
            </button>
        </div>

        <div class="modal-meta-grid">
            <div>
                <span>Type</span>
                <strong id="modalType">Member</strong>
            </div>
            <div>
                <span>Phone</span>
                <strong id="modalPhone">Not provided</strong>
            </div>
            <div>
                <span>Received</span>
                <strong id="modalDate"></strong>
            </div>
        </div>

        <div class="modal-message-box">
            <p id="modalMessage"></p>
        </div>
    </dialog>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('messageModal');
            const closeButton = document.getElementById('closeMessageModal');
            const openButtons = document.querySelectorAll('[data-open-message]');

            if (!modal || !closeButton || openButtons.length === 0) {
                return;
            }

            const modalName = document.getElementById('modalName');
            const modalType = document.getElementById('modalType');
            const modalPhone = document.getElementById('modalPhone');
            const modalDate = document.getElementById('modalDate');
            const modalMessage = document.getElementById('modalMessage');

            openButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    modalName.textContent = button.dataset.name || 'Anonymous attendee';
                    modalType.textContent = button.dataset.type || 'Unspecified';
                    modalPhone.textContent = button.dataset.phone || 'Not provided';
                    modalDate.textContent = button.dataset.date || '';
                    modalMessage.textContent = button.dataset.message || '';
                    modal.showModal();
                });
            });

            closeButton.addEventListener('click', function () {
                modal.close();
            });

            modal.addEventListener('click', function (event) {
                const bounds = modal.getBoundingClientRect();
                const isOutside =
                    event.clientX < bounds.left ||
                    event.clientX > bounds.right ||
                    event.clientY < bounds.top ||
                    event.clientY > bounds.bottom;

                if (isOutside) {
                    modal.close();
                }
            });
        });
    </script>
</body>
</html>
<?php
if (isset($stmt) && $stmt instanceof mysqli_stmt) {
    $stmt->close();
}
$conn->close();
?>
