<?php
require "connection.php";
require "includes/auth.php";
requireAdmin();

$users = $conn->query("SELECT id, name, username, role, created_at FROM users ORDER BY id DESC");
$pageTitle = "Users";
require "includes/header.php";
?>
<h1 class="page-heading">User Accounts</h1>
<p class="text-muted">Only administrators can view this page. New registrations receive the standard user role.</p>
<div class="panel table-responsive">
<table class="table table-hover align-middle">
    <thead><tr><th>ID</th><th>Name</th><th>Username</th><th>Role</th><th>Registered</th></tr></thead>
    <tbody>
    <?php while ($user = $users->fetch_assoc()): ?>
        <tr>
            <td><?= e($user["id"]) ?></td><td><?= e($user["name"]) ?></td>
            <td><?= e($user["username"]) ?></td><td><?= e(ucfirst($user["role"])) ?></td>
            <td><?= e($user["created_at"]) ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</div>
<?php require "includes/footer.php"; ?>