<?php
session_start();

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'db.php';

$allowedViews = ['active', 'archived', 'all'];
$view = $_GET['view'] ?? 'active';
$view = in_array($view, $allowedViews, true) ? $view : 'active';
$search = trim($_GET['search'] ?? '');
$flash = $_GET['status'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $messageId = (int) ($_POST['message_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $redirectView = $_POST['view'] ?? 'active';
    $redirectView = in_array($redirectView, $allowedViews, true) ? $redirectView : 'active';
    $redirectSearch = trim($_POST['search'] ?? '');
    $redirectParams = ['view' => $redirectView];

    if ($redirectSearch !== '') {
        $redirectParams['search'] = $redirectSearch;
    }

    if ($messageId > 0) {
        if ($action === 'archive') {
            $stmt = $conn->prepare('UPDATE messages SET is_archived = 1, archived_at = CURRENT_TIMESTAMP WHERE id = ?');
            $stmt->bind_param('i', $messageId);
            $stmt->execute();
            $stmt->close();
            $redirectParams['status'] = 'archived';
        } elseif ($action === 'restore') {
            $stmt = $conn->prepare('UPDATE messages SET is_archived = 0, archived_at = NULL WHERE id = ?');
            $stmt->bind_param('i', $messageId);
            $stmt->execute();
            $stmt->close();
            $redirectParams['status'] = 'restored';
        } elseif ($action === 'delete') {
            $stmt = $conn->prepare('DELETE FROM messages WHERE id = ?');
            $stmt->bind_param('i', $messageId);
            $stmt->execute();
            $stmt->close();
            $redirectParams['status'] = 'deleted';
        }
    }

    header('Location: submissions.php?' . http_build_query($redirectParams));
    exit();
}

$conditions = [];
$types = '';
$params = [];

if ($view === 'active') {
    $conditions[] = 'is_archived = 0';
} elseif ($view === 'archived') {
    $conditions[] = 'is_archived = 1';
}

if ($search !== '') {
    $conditions[] = '(full_name LIKE ? OR telephone LIKE ? OR message LIKE ? OR member_visitor LIKE ?)';
    $searchTerm = '%' . $search . '%';
    $types .= 'ssss';
    array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
}

$query = 'SELECT id, full_name, telephone, member_visitor, message, is_archived, archived_at, created_at FROM messages';

if ($conditions !== []) {
    $query .= ' WHERE ' . implode(' AND ', $conditions);
}

$query .= ' ORDER BY created_at DESC';

if ($types !== '') {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}

$activeCount = (int) ($conn->query('SELECT COUNT(*) AS total FROM messages WHERE is_archived = 0')->fetch_assoc()['total'] ?? 0);
$archivedCount = (int) ($conn->query('SELECT COUNT(*) AS total FROM messages WHERE is_archived = 1')->fetch_assoc()['total'] ?? 0);
$messageCount = (int) ($conn->query('SELECT COUNT(*) AS total FROM messages')->fetch_assoc()['total'] ?? 0);

$flashMessages = [
    'archived' => 'Message archived successfully.',
    'restored' => 'Message restored successfully.',
    'deleted' => 'Message deleted permanently.',
];
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
                    Review, archive, or delete messages from members and visitors below.
                </p>
            </div>

            <div class="dashboard-actions">
                <div class="stat-card">
                    <span>Total messages</span>
                    <strong><?= $messageCount ?></strong>
                </div>
                <a href="logout.php" class="secondary-btn compact-btn">Logout</a>
            </div>
        </header>

        <?php if (isset($flashMessages[$flash])): ?>
            <div class="alert success dashboard-alert"><?= htmlspecialchars($flashMessages[$flash]) ?></div>
        <?php endif; ?>

        <section class="toolbar-card">
            <div class="toolbar-topline">
                <div class="filter-pills">
                    <a href="submissions.php?view=active" class="filter-pill<?= $view === 'active' ? ' is-active' : '' ?>">
                        Active <span><?= $activeCount ?></span>
                    </a>
                    <a href="submissions.php?view=archived" class="filter-pill<?= $view === 'archived' ? ' is-active' : '' ?>">
                        Archived <span><?= $archivedCount ?></span>
                    </a>
                    <a href="submissions.php?view=all" class="filter-pill<?= $view === 'all' ? ' is-active' : '' ?>">
                        All <span><?= $messageCount ?></span>
                    </a>
                </div>
            </div>

            <form method="GET" class="search-form">
                <input type="hidden" name="view" value="<?= htmlspecialchars($view) ?>">
                <input
                    type="text"
                    name="search"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Search name, phone, role, or message"
                >
                <button type="submit" class="primary-btn compact-btn">Search</button>
                <?php if ($search !== ''): ?>
                    <a href="submissions.php?view=<?= urlencode($view) ?>" class="secondary-btn compact-btn">Clear</a>
                <?php endif; ?>
            </form>
        </section>

        <section class="table-card">
            <?php if ($result->num_rows === 0): ?>
                <div class="empty-state">
                    <h2>No messages found</h2>
                    <p>This view does not have any messages yet.</p>
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
                                <th scope="col">Status</th>
                                <th scope="col" class="actions-col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php
                                $fullName = $row['full_name'] ?: 'Anonymous attendee';
                                $telephone = $row['telephone'] ?: 'Not provided';
                                $memberVisitor = $row['member_visitor'] ?: 'Unspecified';
                                $preview = mb_strimwidth($row['message'], 0, 70, '...');
                                $isArchived = (int) $row['is_archived'] === 1;
                                $statusLabel = $isArchived ? 'Archived' : 'Active';
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
                                    <td data-label="Status">
                                        <span class="status-pill<?= $isArchived ? ' archived' : ' active' ?>">
                                            <?= $statusLabel ?>
                                        </span>
                                    </td>
                                    <td data-label="Actions" class="table-actions">
                                        <div class="action-stack">
                                            <button
                                                type="button"
                                                class="icon-btn"
                                                data-open-message
                                                data-name="<?= htmlspecialchars($fullName) ?>"
                                                data-type="<?= htmlspecialchars($memberVisitor) ?>"
                                                data-phone="<?= htmlspecialchars($telephone) ?>"
                                                data-date="<?= htmlspecialchars($row['created_at']) ?>"
                                                data-status="<?= htmlspecialchars($statusLabel) ?>"
                                                data-message="<?= htmlspecialchars($row['message']) ?>"
                                                aria-label="View full message"
                                                title="View full message"
                                            >
                                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M12 5c5.23 0 9.27 3.34 11 7-1.73 3.66-5.77 7-11 7S2.73 15.66 1 12c1.73-3.66 5.77-7 11-7Zm0 2C8.39 7 5.33 9.11 3.53 12 5.33 14.89 8.39 17 12 17s6.67-2.11 8.47-5C18.67 9.11 15.61 7 12 7Zm0 2.5A2.5 2.5 0 1 1 9.5 12 2.5 2.5 0 0 1 12 9.5Z"/>
                                                </svg>
                                            </button>

                                            <form method="POST" class="inline-action-form">
                                                <input type="hidden" name="message_id" value="<?= (int) $row['id'] ?>">
                                                <input type="hidden" name="view" value="<?= htmlspecialchars($view) ?>">
                                                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                                                <input type="hidden" name="action" value="<?= $isArchived ? 'restore' : 'archive' ?>">
                                                <button
                                                    type="submit"
                                                    class="icon-btn archive-btn"
                                                    aria-label="<?= $isArchived ? 'Restore message' : 'Archive message' ?>"
                                                    title="<?= $isArchived ? 'Restore message' : 'Archive message' ?>"
                                                >
                                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                                        <path d="M20 6.91V19a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6.91L5.91 5h12.18ZM12 9l-3 3h2v4h2v-4h2Zm6.24-6L20 4.76V7H4V4.76L5.76 3Z"/>
                                                    </svg>
                                                </button>
                                            </form>

                                            <form method="POST" class="inline-action-form" onsubmit="return confirm('Delete this message permanently?');">
                                                <input type="hidden" name="message_id" value="<?= (int) $row['id'] ?>">
                                                <input type="hidden" name="view" value="<?= htmlspecialchars($view) ?>">
                                                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <button
                                                    type="submit"
                                                    class="icon-btn delete-btn"
                                                    aria-label="Delete message"
                                                    title="Delete message"
                                                >
                                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                                        <path d="M9 3h6l1 2h5v2H3V5h5Zm1 6h2v8h-2Zm4 0h2v8h-2ZM6 9h2v8H6Zm1 12a2 2 0 0 1-2-2V8h14v11a2 2 0 0 1-2 2Z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
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
            <div>
                <span>Status</span>
                <strong id="modalStatus">Active</strong>
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
            const modalStatus = document.getElementById('modalStatus');
            const modalMessage = document.getElementById('modalMessage');

            openButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    modalName.textContent = button.dataset.name || 'Anonymous attendee';
                    modalType.textContent = button.dataset.type || 'Unspecified';
                    modalPhone.textContent = button.dataset.phone || 'Not provided';
                    modalDate.textContent = button.dataset.date || '';
                    modalStatus.textContent = button.dataset.status || 'Active';
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
