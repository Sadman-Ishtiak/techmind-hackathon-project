<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


session_start();

if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] !== "admin") {
    header("Location: index.php");
    exit;
}

require_once 'config.php';
$conn = new mysqli("localhost", "root", "", "hackathon");

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

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-4xl font-extrabold text-gray-900 mb-8 text-center">Admin Dashboard</h1>

    <!-- Users Table -->
    <div class="bg-white shadow-lg rounded-xl p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-700 mb-4">Users</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto rounded-lg overflow-hidden">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Store Request</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if ($users && $users->num_rows > 0): ?>
                        <?php while ($u = $users->fetch_assoc()): ?>
                            <tr class="bg-white hover:bg-gray-100 transition-colors duration-200">
                                <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-800"><?= $u['id'] ?></td>
                                <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($u['name']) ?></td>
                                <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($u['email']) ?></td>
                                <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($u['role']) ?></td>
                                <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-800"><?= $u['store_request'] ? "Pending" : "No" ?></td>
                                <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-800">
                                    <a href="edit_user.php?id=<?= $u['id'] ?>" class="inline-block bg-indigo-500 text-white px-4 py-1 rounded-full text-xs font-medium transition-transform hover:scale-105 hover:bg-indigo-600 mr-2">Edit</a>
                                    <a href="?delete_user=<?= $u['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')" class="inline-block bg-red-500 text-white px-4 py-1 rounded-full text-xs font-medium transition-transform hover:scale-105 hover:bg-red-600">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="px-4 py-3 text-center text-gray-500">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Stores Table -->
    <div class="bg-white shadow-lg rounded-xl p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-700 mb-4">Stores</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto rounded-lg overflow-hidden">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Store Name</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Owner</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Approved</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if ($stores && $stores->num_rows > 0): ?>
                        <?php while ($s = $stores->fetch_assoc()): ?>
                            <tr class="bg-white hover:bg-gray-100 transition-colors duration-200">
                                <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-800"><?= $s['id'] ?></td>
                                <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($s['name']) ?></td>
                                <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($s['owner_name']) ?></td>
                                <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-800"><?= $s['approved'] ? "Yes" : "No" ?></td>
                                <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-800">
                                    <?php if (!$s['approved']): ?>
                                        <a href="approve_store.php?store_id=<?= $s['id'] ?>" class="inline-block bg-green-500 text-white px-4 py-1 rounded-full text-xs font-medium transition-transform hover:scale-105 hover:bg-green-600 mr-2">Approve</a>
                                    <?php endif; ?>
                                    <a href="edit_store.php?id=<?= $s['id'] ?>" class="inline-block bg-indigo-500 text-white px-4 py-1 rounded-full text-xs font-medium transition-transform hover:scale-105 hover:bg-indigo-600 mr-2">Edit</a>
                                    <a href="?delete_store=<?= $s['id'] ?>" onclick="return confirm('Are you sure you want to delete this store?')" class="inline-block bg-red-500 text-white px-4 py-1 rounded-full text-xs font-medium transition-transform hover:scale-105 hover:bg-red-600">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="px-4 py-3 text-center text-gray-500">No stores found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white shadow-lg rounded-xl p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-700 mb-4">Products</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto rounded-lg overflow-hidden">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Product Name</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Price</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Store</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if ($products && $products->num_rows > 0): ?>
                        <?php while ($p = $products->fetch_assoc()): ?>
                            <tr class="bg-white hover:bg-gray-100 transition-colors duration-200">
                                <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-800"><?= $p['id'] ?></td>
                                <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($p['name']) ?></td>
                                <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-800">$<?= $p['price'] ?></td>
                                <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-800"><?= $p['stock'] ?></td>
                                <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($p['store_name']) ?></td>
                                <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-800">
                                    <a href="edit_product.php?id=<?= $p['id'] ?>" class="inline-block bg-indigo-500 text-white px-4 py-1 rounded-full text-xs font-medium transition-transform hover:scale-105 hover:bg-indigo-600 mr-2">Edit</a>
                                    <a href="?delete_product=<?= $p['id'] ?>" onclick="return confirm('Are you sure you want to delete this product?')" class="inline-block bg-red-500 text-white px-4 py-1 rounded-full text-xs font-medium transition-transform hover:scale-105 hover:bg-red-600">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="px-4 py-3 text-center text-gray-500">No products found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Admin Dashboard";
include './lib/layout.php';
?>
