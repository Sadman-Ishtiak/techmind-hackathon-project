<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

session_start();
require_once 'config.php';


// Only admin can access
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Handle delete actions
if (isset($_GET['delete_user'])) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $_GET['delete_user']);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit;
}

if (isset($_GET['delete_store'])) {
    $stmt = $conn->prepare("DELETE FROM stores WHERE id=?");
    $stmt->bind_param("i", $_GET['delete_store']);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit;
}

if (isset($_GET['delete_product'])) {
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $_GET['delete_product']);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit;
}

// Fetch all data
$users = $conn->query("SELECT id, name, email, role, store_request FROM users");
$stores = $conn->query("SELECT s.id, s.name, u.name AS owner_name, s.approved 
                        FROM stores s 
                        JOIN users u ON u.id = s.owner_id");
$products = $conn->query("SELECT p.id, p.name, p.price, p.stock, s.name AS store_name 
                          FROM products p 
                          JOIN stores s ON s.id = p.store_id");

ob_start();
?>

<h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>

<!-- Users Table -->
<h2 class="text-2xl font-semibold mb-2">Users</h2>
<table class="min-w-full table-auto mb-6 border">
    <thead>
        <tr class="bg-gray-100">
            <th class="px-4 py-2 border">ID</th>
            <th class="px-4 py-2 border">Name</th>
            <th class="px-4 py-2 border">Email</th>
            <th class="px-4 py-2 border">Role</th>
            <th class="px-4 py-2 border">Store Request</th>
            <th class="px-4 py-2 border">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($users && $users->num_rows > 0): ?>
            <?php while ($u = $users->fetch_assoc()): ?>
                <tr class="text-center">
                    <td class="px-4 py-2 border"><?= $u['id'] ?></td>
                    <td class="px-4 py-2 border"><?= htmlspecialchars($u['name']) ?></td>
                    <td class="px-4 py-2 border"><?= htmlspecialchars($u['email']) ?></td>
                    <td class="px-4 py-2 border"><?= htmlspecialchars($u['role']) ?></td>
                    <td class="px-4 py-2 border"><?= $u['store_request'] ? "Pending" : "No" ?></td>
                    <td class="px-4 py-2 border">
                        <a href="edit_user.php?id=<?= $u['id'] ?>" class="text-indigo-600 hover:underline mr-2">Edit</a>
                        <a href="?delete_user=<?= $u['id'] ?>" onclick="return confirm('Delete this user?')" class="text-red-600 hover:underline">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-gray-500 py-2">No users found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Stores Table -->
<h2 class="text-2xl font-semibold mb-2">Stores</h2>
<table class="min-w-full table-auto mb-6 border">
    <thead>
        <tr class="bg-gray-100">
            <th class="px-4 py-2 border">ID</th>
            <th class="px-4 py-2 border">Store Name</th>
            <th class="px-4 py-2 border">Owner</th>
            <th class="px-4 py-2 border">Approved</th>
            <th class="px-4 py-2 border">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($stores && $stores->num_rows > 0): ?>
            <?php while ($s = $stores->fetch_assoc()): ?>
                <tr class="text-center">
                    <td class="px-4 py-2 border"><?= $s['id'] ?></td>
                    <td class="px-4 py-2 border"><?= htmlspecialchars($s['name']) ?></td>
                    <td class="px-4 py-2 border"><?= htmlspecialchars($s['owner_name']) ?></td>
                    <td class="px-4 py-2 border"><?= $s['approved'] ? "Yes" : "No" ?></td>
                    <td class="px-4 py-2 border">
                        <?php if (!$s['approved']): ?>
                            <a href="approve_store.php?store_id=<?= $s['id'] ?>" class="text-green-600 hover:underline mr-2">Approve</a>
                        <?php endif; ?>
                        <a href="edit_store.php?id=<?= $s['id'] ?>" class="text-indigo-600 hover:underline mr-2">Edit</a>
                        <a href="?delete_store=<?= $s['id'] ?>" onclick="return confirm('Delete this store?')" class="text-red-600 hover:underline">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5" class="text-gray-500 py-2">No stores found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Products Table -->
<h2 class="text-2xl font-semibold mb-2">Products</h2>
<table class="min-w-full table-auto mb-6 border">
    <thead>
        <tr class="bg-gray-100">
            <th class="px-4 py-2 border">ID</th>
            <th class="px-4 py-2 border">Product Name</th>
            <th class="px-4 py-2 border">Price</th>
            <th class="px-4 py-2 border">Stock</th>
            <th class="px-4 py-2 border">Store</th>
            <th class="px-4 py-2 border">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($products && $products->num_rows > 0): ?>
            <?php while ($p = $products->fetch_assoc()): ?>
                <tr class="text-center">
                    <td class="px-4 py-2 border"><?= $p['id'] ?></td>
                    <td class="px-4 py-2 border"><?= htmlspecialchars($p['name']) ?></td>
                    <td class="px-4 py-2 border">$<?= $p['price'] ?></td>
                    <td class="px-4 py-2 border"><?= $p['stock'] ?></td>
                    <td class="px-4 py-2 border"><?= htmlspecialchars($p['store_name']) ?></td>
                    <td class="px-4 py-2 border">
                        <a href="edit_product.php?id=<?= $p['id'] ?>" class="text-indigo-600 hover:underline mr-2">Edit</a>
                        <a href="?delete_product=<?= $p['id'] ?>" onclick="return confirm('Delete this product?')" class="text-red-600 hover:underline">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-gray-500 py-2">No products found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
$title = "Admin Dashboard";
include './lib/layout.php';
