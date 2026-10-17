<?php
require "connection.php";
require "includes/auth.php";
requireLogin();

$totalProducts = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()["total"];
$totalStock = $conn->query("SELECT COALESCE(SUM(quantity), 0) AS total FROM products")->fetch_assoc()["total"];
$lowStock = $conn->query("SELECT COUNT(*) AS total FROM products WHERE quantity <= 5")->fetch_assoc()["total"];
$pageTitle = "Dashboard";
require "includes/header.php";
?>
<h1 class="page-heading">Dashboard</h1>
<p class="text-muted">Quick summary of your inventory.</p>
<div class="row g-3 mt-2">
    <div class="col-12 col-md-4"><div class="stat-card"><p> Products</p><h2><?= e($totalProducts) ?></h2></div></div>
    <div class="col-12 col-md-4"><div class="stat-card"><p>Items in Stock</p><h2><?= e($totalStock) ?></h2></div></div>
    <div class="col-12 col-md-4"><div class="stat-card"><p>Low-Stock Products</p><h2><?= e($lowStock) ?></h2><small>5 items or fewer</small></div></div>
</div>
<div class="panel mt-4">
    <h4>Quick Actions</h4>
    <p class="text-muted">Manage the items stored in your inventory.</p>
    <a class="btn btn-primary me-2" href="products.php">View </a>
    <a class="btn btn-outline-primary" href="products.php?action=add">Add</a>
</div>
<?php require "includes/footer.php"; ?>