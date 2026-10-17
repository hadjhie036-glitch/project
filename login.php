<?php
require "connection.php";
require "includes/auth.php";

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare("SELECT id, name, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        session_regenerate_id(true);
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Incorrect username or password.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/style.css" rel="stylesheet">
</head>
<body class="auth-page">
<div class="auth-card">
    <h2 class="mb-1">Login</h2>
    <p class="text-muted mb-4">Sign in to see products</p>
    <?php if (isset($_GET["registered"])): ?><div class="alert alert-success">Registration successful. You can now log in.</div><?php endif; ?>
    <?php if ($error !== ""): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <form method="POST">
        <label class="form-label">Username</label>
        <input class="form-control mb-3" type="text" name="username" required>
        <label class="form-label">Password</label>
        <input class="form-control mb-3" type="password" name="password" required>
        <button class="btn btn-primary w-100" type="submit">Log In</button>
    </form>
    <p class="mt-3 mb-0"><a href="register.php">Register</a></p>
</div>
</body>
</html>