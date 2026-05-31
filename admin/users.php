<?php
session_start();

require_once("../config/Database.php");
$conn = Database::getInstance()->conn;
require_once('../includes/csrf.php');
require_once('../middleware/auth.php');
require_once('../includes/flash.php'); // ✅ FIXED (standardized)

$user = require_role(['admin']);

$search = trim($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

<?php include('../includes/navbar.php'); ?>

<div class="container">

    <?php display_flash(); ?> <!-- ✅ CLEAN STANDARD -->
    <!-- HEADER -->
    <div class="card admin-header">
        <h1>User Management</h1>
        <p>Manage system users and roles</p>
    </div>

    <!-- FLASH MESSAGE -->
    <?php if ($flash = get_flash()): ?>
        <div class="alert <?= htmlspecialchars($flash['type']) ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>

    <!-- ACTION BAR -->
    <div class="card action-bar">
        <a href="dashboard.php" class="btn btn-secondary">Back</a>
        <a href="add_user.php" class="btn btn-primary">+ Add User</a>
    </div>

    <!-- SEARCH -->
    <div class="card">
        <form method="GET">
            <input type="text"
                   name="search"
                   placeholder="Search name, student number, role..."
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>

<?php
/*
|--------------------------------------------------------------------------
| QUERY
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT user_id, student_number, first_name, last_name, role
    FROM users
    WHERE 1=1
";

$count_sql = "SELECT COUNT(*) as total FROM users WHERE 1=1";

$params = [];
$types = "";

/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/
if ($search !== '') {
    $sql .= " AND (
        student_number LIKE ?
        OR first_name LIKE ?
        OR last_name LIKE ?
        OR role LIKE ?
    )";

    $count_sql .= " AND (
        student_number LIKE ?
        OR first_name LIKE ?
        OR last_name LIKE ?
        OR role LIKE ?
    )";

    $like = "%$search%";

    for ($i = 0; $i < 4; $i++) {
        $params[] = $like;
        $types .= "s";
    }
}

/*
|--------------------------------------------------------------------------
| COUNT
|--------------------------------------------------------------------------
*/
$count_stmt = $conn->prepare($count_sql);

if ($search !== '') {
    $count_stmt->bind_param($types, ...$params);
}

$count_stmt->execute();
$total_rows = $count_stmt->get_result()->fetch_assoc()['total'] ?? 0;
$count_stmt->close();

$total_pages = max(1, ceil($total_rows / $limit));

/*
|--------------------------------------------------------------------------
| MAIN QUERY
|--------------------------------------------------------------------------
*/
$sql .= " ORDER BY user_id DESC LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();

$result = $stmt->get_result();
?>

    <!-- TABLE -->
    <div class="card">

        <table class="reg-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student No.</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($result->num_rows === 0): ?>
                <tr>
                    <td colspan="5" class="empty-state">No users found</td>
                </tr>
            <?php else: ?>

                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php $role = htmlspecialchars(strtolower($row['role'])); ?>

                    <tr>
                        <td><?= (int)$row['user_id'] ?></td>
                        <td><?= htmlspecialchars($row['student_number']) ?></td>

                        <td>
                            <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?>
                        </td>

                        <td>
                            <span class="badge <?= $role ?>">
                                <?= ucfirst($role) ?>
                            </span>
                        </td>

                        <td class="action-cell">

                            <!-- EDIT (SAFE GET) -->
                            <a href="edit_user.php?id=<?= (int)$row['user_id'] ?>"
                               class="btn btn-primary">
                                Edit
                            </a>

                            <!-- RESET PASSWORD (POST + CSRF) -->
                            <form method="POST" action="confirm_reset.php" style="display:inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int)$row['user_id'] ?>">
                                <button type="submit"
                                        class="btn btn-secondary"
                                        onclick="return confirm('Reset password for this user?')">
                                    Reset
                                </button>
                            </form>

                            <!-- DELETE USER (POST + CSRF) -->
                            <form method="POST" action="confirm_delete.php" style="display:inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int)$row['user_id'] ?>">
                                <button type="submit"
                                        class="btn btn-danger"
                                        onclick="return confirm('Delete this user? This cannot be undone.')">
                                    Delete
                                </button>
                            </form>

                        </td>
                    </tr>
                <?php endwhile; ?>

            <?php endif; ?>

            </tbody>
        </table>

    </div>

    <!-- PAGINATION -->
    <div class="card pagination">

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"
               class="btn <?= $i == $page ? 'btn-primary' : 'btn-secondary' ?>">
               <?= $i ?>
            </a>
        <?php endfor; ?>

    </div>

</div>

</body>
</html>