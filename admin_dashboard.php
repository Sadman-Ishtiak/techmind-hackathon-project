<?php
// admin_dashboard.php

// Ensure error reporting is off in production or configured appropriately
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

session_start();

// Redirect if user is not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php"); // Or a login page
    exit;
}

// Require database configuration and establish connection
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'hackathon');
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME); // Using constants from config.php

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Handle Delete Actions ---

// Delete User
if (isset($_GET['delete_user'])) {
    $user_id = filter_var($_GET['delete_user'], FILTER_SANITIZE_NUMBER_INT);

    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "User deleted successfully.";
    } else {
        $_SESSION['error'] = "Error deleting user: " . $stmt->error;
    }
    $stmt->close();
    header("Location: admin_dashboard.php");
    exit;
}

// Delete Store
if (isset($_GET['delete_store'])) {
    $store_id = filter_var($_GET['delete_store'], FILTER_SANITIZE_NUMBER_INT);

    $stmt = $conn->prepare("DELETE FROM stores WHERE id=?");
    $stmt->bind_param("i", $store_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Store deleted successfully.";
    } else {
        $_SESSION['error'] = "Error deleting store: " . $stmt->error;
    }
    $stmt->close();
    header("Location: admin_dashboard.php");
    exit;
}

// Delete Product
if (isset($_GET['delete_product'])) {
    $product_id = filter_var($_GET['delete_product'], FILTER_SANITIZE_NUMBER_INT);

    try {
        $conn->begin_transaction();

        $stmt_cart_items = $conn->prepare("DELETE FROM cart_items WHERE product_id = ?");
        $stmt_cart_items->bind_param("i", $product_id);
        $stmt_cart_items->execute();
        $stmt_cart_items->close();

        $stmt_product_images = $conn->prepare("DELETE FROM product_images WHERE product_id = ?");
        $stmt_product_images->bind_param("i", $product_id);
        $stmt_product_images->execute();
        $stmt_product_images->close();

        $stmt_transactions = $conn->prepare("DELETE FROM transactions WHERE product_id = ?");
        $stmt_transactions->bind_param("i", $product_id);
        $stmt_transactions->execute();
        $stmt_transactions->close();

        $stmt_product = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt_product->bind_param("i", $product_id);
        if ($stmt_product->execute()) {
            $_SESSION['message'] = "Product and its related data deleted successfully.";
            $conn->commit();
        } else {
            throw new Exception("Error deleting product: " . $stmt_product->error);
        }
        $stmt_product->close();

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = $e->getMessage();
    }

    header("Location: admin_dashboard.php");
    exit;
}

// --- Fetch all data for display ---
$users = $conn->query("SELECT id, name, email, role, store_request FROM users ORDER BY id DESC");
$stores = $conn->query("SELECT s.id, s.name, u.name AS owner_name, s.approved
                        FROM stores s
                        JOIN users u ON u.id = s.owner_id
                        ORDER BY s.id DESC");
$products = $conn->query("SELECT p.id, p.name, p.price, p.stock, s.name AS store_name
                          FROM products p
                          JOIN stores s ON s.id = p.store_id
                          ORDER BY p.id DESC");

// Start output buffering to capture content for the layout
ob_start();
?>

<div class="container mx-auto p-4 md:p-8">
    <h1 class="text-4xl font-extrabold mb-8 text-center text-blue-400 animate-fade-in-down">Admin Dashboard</h1>

    <?php
    if (isset($_SESSION['message'])) {
        echo '<div class="bg-green-700 bg-opacity-30 border border-green-500 text-green-300 px-4 py-3 rounded relative mb-4 transition-opacity duration-500 animate-fade-in" role="alert">' . htmlspecialchars($_SESSION['message']) . '</div>';
        unset($_SESSION['message']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="bg-red-700 bg-opacity-30 border border-red-500 text-red-300 px-4 py-3 rounded relative mb-4 transition-opacity duration-500 animate-fade-in" role="alert">' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }
    ?>

    <div class="bg-slate-800 rounded-xl shadow-2xl p-6 mb-8 transform transition-all duration-500 hover:scale-[1.01]">
        <h2 class="text-2xl font-bold mb-4 text-blue-400">Users</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full rounded-lg overflow-hidden">
                <thead class="bg-blue-900 text-blue-200">
                    <tr class="text-sm font-semibold uppercase tracking-wider">
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Role</th>
                        <th class="px-4 py-3 text-left">Store Request</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    <?php if ($users && $users->num_rows > 0): ?>
                        <?php while ($u = $users->fetch_assoc()): ?>
                            <tr class="hover:bg-slate-700 transition-colors duration-200">
                                <td class="px-4 py-3 whitespace-nowrap"><?= $u['id'] ?></td>
                                <td class="px-4 py-3 whitespace-nowrap"><?= htmlspecialchars($u['name']) ?></td>
                                <td class="px-4 py-3 whitespace-nowrap"><?= htmlspecialchars($u['email']) ?></td>
                                <td class="px-4 py-3 whitespace-nowrap"><span class="px-2 py-1 rounded-full text-xs font-semibold <?= $u['role'] === 'admin' ? 'bg-red-500' : 'bg-blue-600' ?> text-white"><?= htmlspecialchars(ucfirst($u['role'])) ?></span></td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <?= $u['store_request'] ? "<span class='px-2 py-1 rounded-full text-xs font-semibold bg-yellow-600 text-white'>Pending</span>" : "<span class='px-2 py-1 rounded-full text-xs font-semibold bg-green-600 text-white'>No</span>" ?>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <a href="edit_user.php?id=<?= $u['id'] ?>" class="text-blue-400 hover:text-blue-200 font-medium transition-colors duration-200 mr-3">Edit</a>
                                    <a href="?delete_user=<?= $u['id'] ?>" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone and will delete all associated data.')" class="text-red-400 hover:text-red-200 font-medium transition-colors duration-200">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="px-4 py-3 text-center text-slate-500">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-slate-800 rounded-xl shadow-2xl p-6 mb-8 transform transition-all duration-500 hover:scale-[1.01]">
        <h2 class="text-2xl font-bold mb-4 text-blue-400">Stores</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full rounded-lg overflow-hidden">
                <thead class="bg-blue-900 text-blue-200">
                    <tr class="text-sm font-semibold uppercase tracking-wider">
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Store Name</th>
                        <th class="px-4 py-3 text-left">Owner</th>
                        <th class="px-4 py-3 text-left">Approved</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    <?php if ($stores && $stores->num_rows > 0): ?>
                        <?php while ($s = $stores->fetch_assoc()): ?>
                            <tr class="hover:bg-slate-700 transition-colors duration-200">
                                <td class="px-4 py-3 whitespace-nowrap"><?= $s['id'] ?></td>
                                <td class="px-4 py-3 whitespace-nowrap"><?= htmlspecialchars($s['name']) ?></td>
                                <td class="px-4 py-3 whitespace-nowrap"><?= htmlspecialchars($s['owner_name']) ?></td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <?= $s['approved'] ? "<span class='px-2 py-1 rounded-full text-xs font-semibold bg-green-600 text-white'>Yes</span>" : "<span class='px-2 py-1 rounded-full text-xs font-semibold bg-red-600 text-white'>No</span>" ?>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <?php if (!$s['approved']): ?>
                                        <a href="approve_store.php?store_id=<?= $s['id'] ?>" class="text-green-400 hover:text-green-200 font-medium transition-colors duration-200 mr-3">Approve</a>
                                    <?php endif; ?>
                                    <a href="edit_store.php?id=<?= $s['id'] ?>" class="text-blue-400 hover:text-blue-200 font-medium transition-colors duration-200 mr-3">Edit</a>
                                    <a href="?delete_store=<?= $s['id'] ?>" onclick="return confirm('Are you sure you want to delete this store? This will also delete all products associated with it.')" class="text-red-400 hover:text-red-200 font-medium transition-colors duration-200">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="px-4 py-3 text-center text-slate-500">No stores found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-slate-800 rounded-xl shadow-2xl p-6 mb-8 transform transition-all duration-500 hover:scale-[1.01]">
        <h2 class="text-2xl font-bold mb-4 text-blue-400">Products</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full rounded-lg overflow-hidden">
                <thead class="bg-blue-900 text-blue-200">
                    <tr class="text-sm font-semibold uppercase tracking-wider">
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Product Name</th>
                        <th class="px-4 py-3 text-left">Price</th>
                        <th class="px-4 py-3 text-left">Stock</th>
                        <th class="px-4 py-3 text-left">Store</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    <?php if ($products && $products->num_rows > 0): ?>
                        <?php while ($p = $products->fetch_assoc()): ?>
                            <tr class="hover:bg-slate-700 transition-colors duration-200">
                                <td class="px-4 py-3 whitespace-nowrap"><?= $p['id'] ?></td>
                                <td class="px-4 py-3 whitespace-nowrap"><?= htmlspecialchars($p['name']) ?></td>
                                <td class="px-4 py-3 whitespace-nowrap">$<?= number_format($p['price'], 2) ?></td>
                                <td class="px-4 py-3 whitespace-nowrap"><?= $p['stock'] ?></td>
                                <td class="px-4 py-3 whitespace-nowrap"><?= htmlspecialchars($p['store_name']) ?></td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <a href="edit_product.php?id=<?= $p['id'] ?>" class="text-blue-400 hover:text-blue-200 font-medium transition-colors duration-200 mr-3">Edit</a>
                                    <a href="?delete_product=<?= $p['id'] ?>" onclick="return confirm('Are you sure you want to delete this product? This will also delete all associated images, cart items, and transactions.')" class="text-red-400 hover:text-red-200 font-medium transition-colors duration-200">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="px-4 py-3 text-center text-slate-500">No products found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Get content from output buffer
$content = ob_get_clean();
$title = "Admin Dashboard";

// Include your layout file
include './lib/layout.php';

// Close the database connection
$conn->close();
?>