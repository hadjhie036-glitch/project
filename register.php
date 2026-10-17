<?php
require "connection.php";
require "includes/auth.php";

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($name === "" || $username === "" || $password === "") {
        $error = "Please fill in all fields.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "That username is already taken.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->bind_param("sss", $name, $username, $hash);
            if ($stmt->execute()) {
                header("Location: login.php?registered=1");
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/style.css" rel="stylesheet">
</head>
<body class="auth-page">
<div class="auth-card">
    <h2 class="mb-1">Create an Account</h2>
    <p class="text-muted mb-4">Inventory Management System</p>
    <?php if ($error !== ""): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <form method="POST">
        <label class="form-label">Name</label>
        <input class="form-control mb-3" type="text" name="name" maxlength="100" required>
        <label class="form-label">Username</label>
        <input class="form-control mb-3" type="text" name="username" maxlength="50" required>
        <label class="form-label">Password (at least 6 characters)</label>
        <input class="form-control mb-3" type="password" name="password" minlength="6" required>
        <button class="btn btn-primary w-100" type="submit">Register</button>
    </form>
    <p class="mt-3 mb-0">Have an account? <a href="login.php">Log in</a></p>
</div>
</body>
</html>