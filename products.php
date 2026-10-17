<?php
require "connection.php";
require "includes/auth.php";
requireLogin();

$error = "";
$editProduct = null;
$action = $_GET["action"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)($_POST["id"] ?? 0);
    $name = trim($_POST["name"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $quantity = filter_var($_POST["quantity"] ?? null, FILTER_VALIDATE_INT);
    $price = filter_var($_POST["price"] ?? null, FILTER_VALIDATE_FLOAT);

    if ($name === "" || $category === "" || $quantity === false || $quantity === null || $quantity < 0 || $price === false || $price === null || $price < 0) {
        $error = "Enter a product name, category, non-negative whole quantity, and valid non-negative price.";
        $action = $id > 0 ? "edit" : "add";
        $editProduct = ["id"=>$id, "name"=>$name, "category"=>$category, "quantity"=>$_POST["quantity"] ?? "", "price"=>$_POST["price"] ?? ""];
    } elseif ($id > 0) {
        $stmt = $conn->prepare("UPDATE products SET name = ?, category = ?, quantity = ?, price = ? WHERE id = ?");
        $stmt->bind_param("ssidi", $name, $category, $quantity, $price, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: products.php?updated=1");
        exit();
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, category, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssid", $name, $category, $quantity, $price);
        $stmt->execute();
        $stmt->close();
        header("Location: products.php?added=1");
        exit();
    }
}

if (isset($_GET["delete"])) {
    $id = (int)$_GET["delete"];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: products.php?deleted=1");
    exit();
}

if ($action === "edit" && isset($_GET["id"]) && $editProduct === null) {
    $id = (int)$_GET["id"];
    $stmt = $conn->prepare("SELECT id, name, category, quantity, price FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editProduct = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$editProduct) $action = "";
}

$search = trim($_GET["search"] ?? "");
if ($search !== "") {
    $like = "%" . $search . "%";
    $stmt = $conn->prepare("SELECT id, name, category, quantity, price FROM products WHERE name LIKE ? OR category LIKE ? ORDER BY id DESC");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $products = $stmt->get_result();
} else {
    $products = $conn->query("SELECT id, name, category, quantity, price FROM products ORDER BY id DESC");
}

$pageTitle = "Products";
require "includes/header.php";
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div><h1 class="page-heading mb-1">Products</h1><p class="text-muted mb-0">Add and manage inventory products.</p></div>
    <a class="btn btn-primary" href="products.php?action=add">+ Add Product</a>
</div>

<?php if (isset($_GET["added"])): ?><div class="alert alert-success">Product added successfully.</div><?php endif; ?>
<?php if (isset($_GET["updated"])): ?><div class="alert alert-success">Product updated successfully.</div><?php endif; ?>
<?php if (isset($_GET["deleted"])): ?><div class="alert alert-success">Product deleted successfully.</div><?php endif; ?>
<?php if ($error !== ""): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<?php if ($action === "add" || ($action === "edit" && $editProduct)): ?>
<div class="panel mb-4">
    <h4><?= $action === "edit" ? "Edit Product" : "Add Product" ?></h4>
    <form method="POST" class="row g-3">
        <input type="hidden" name="id" value="<?= e($editProduct["id"] ?? 0) ?>">
        <div class="col-12 col-md-6">
            <label class="form-label">Product Name</label>
            <input class="form-control" type="text" name="name" maxlength="120" value="<?= e($editProduct["name"] ?? "") ?>" required>
        </div>
        <div class="col-12 col-md-6">
            <label class="form-label">Category</label>
            <input class="form-control" type="text" name="category" maxlength="80" value="<?= e($editProduct["category"] ?? "") ?>" required>
        </div>
        <div class="col-12 col-md-6">
            <label class="form-label">Quantity</label>
            <input class="form-control" type="number" name="quantity" min="0" step="1" value="<?= e($editProduct["quantity"] ?? "0") ?>" required>
        </div>
        <div class="col-12 col-md-6">
            <label class="form-label">Price (₱)</label>
            <input class="form-control" type="number" name="price" min="0" step="0.01" value="<?= e($editProduct["price"] ?? "0.00") ?>" required>
        </div>
        <div class="col-12">
            <button class="btn btn-primary" type="submit">Save Product</button>
            <a class="btn btn-outline-secondary" href="products.php">Cancel</a>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="panel">
    <form method="GET" class="row g-2 mb-3">
        <div class="col"><input class="form-control" type="search" name="search" placeholder="Search name or category..." value="<?= e($search) ?>"></div>
        <div class="col-auto"><button class="btn btn-outline-primary" type="submit">Search</button></div>
        <?php if ($search !== ""): ?><div class="col-auto"><a class="btn btn-outline-secondary" href="products.php">Clear</a></div><?php endif; ?>
    </form>
    <div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead><tr><th>ID</th><th>Product</th><th>Category</th><th>Quantity</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php if ($products->num_rows === 0): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">No products found.</td></tr>
        <?php else: while ($product = $products->fetch_assoc()): ?>
            <tr>
                <td><?= e($product["id"]) ?></td>
                <td><?= e($product["name"]) ?></td>
                <td><?= e($product["category"]) ?></td>
                <td><?= e($product["quantity"]) ?></td>
                <td>₱<?= number_format((float)$product["price"], 2) ?></td>
                <td><?php if ((int)$product["quantity"] <= 5): ?><span class="badge text-bg-warning">Low Stock</span><?php else: ?><span class="badge text-bg-success">In Stock</span><?php endif; ?></td>
                <td class="text-nowrap">
                    <a class="btn btn-sm btn-outline-primary" href="products.php?action=edit&id=<?= e($product["id"]) ?>">Edit</a>
                    <a class="btn btn-sm btn-outline-danger" href="products.php?delete=<?= e($product["id"]) ?>" onclick="return confirmDelete()">Delete</a>
                </td>
            </tr>
        <?php endwhile; endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?php require "includes/footer.php"; ?>