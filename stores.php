<?php
session_start(); // needed for auth check
// if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'store_owner' || $_SESSION['user_role'] !== 'admin') {
//     header("Location: login.php");
//     exit;
// }

$title = "Stores - JKKNIU Marketplace";

ob_start();

require_once './config.php'; // DB connection

// Fetch stores with owner info
$sql = "SELECT stores.id AS store_id, stores.name AS store_name, users.name AS owner_name
        FROM stores
        JOIN users ON stores.owner_id = users.id
        ORDER BY stores.id ASC";
$result = $conn->query($sql);

ob_start();
?>

<?php

// Optional: authentication check
// if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'store_owner' && $_SESSION['user_role'] !== 'admin')) {
//     header("Location: login.php");
//     exit;
// }

$title = "Stores - JKKNIU Marketplace";

require_once './config.php'; // This uses $conn (PDO) from config.php

try {
    // Fetch stores with owner info
    $sql = "SELECT stores.id AS store_id, stores.name AS store_name, users.name AS owner_name
            FROM stores
            JOIN users ON stores.owner_id = users.id
            ORDER BY stores.id ASC";
    
    $stmt = $conn->query($sql); // using PDO connection
    $stores = $stmt->fetchAll(PDO::FETCH_ASSOC); // fetch all rows
} catch (PDOException $e) {
    die("Error fetching stores: " . $e->getMessage());
}
?>

<h2 class="text-2xl font-bold mb-4">Popular Stores</h2>
<p class="text-gray-600 mb-6">Here are the recommended stores from our users.</p>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    <?php if (!empty($stores)): ?>
        <?php foreach ($stores as $store): ?>
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition duration-300">
                <h3 class="text-lg font-semibold mb-2"><?= htmlspecialchars($store['store_name']); ?></h3>
                <p class="text-gray-500 mb-4">Owner: <?= htmlspecialchars($store['owner_name']); ?></p>
                <a href="store_products.php?store_id=<?= $store['store_id']; ?>"
                   class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                    View Products
                </a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="col-span-full text-center text-gray-500">No stores found.</p>
    <?php endif; ?>
</div>






<?php
$content = ob_get_clean();

include './lib/layout.php';
