<?php
session_start();
// require_once './config.php'; // DB connection

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Hackathon"; // Your DB name

$conn = new mysqli($servername, $username, $password, $dbname);
// Validate store_id
if (!isset($_GET['store_id']) || !is_numeric($_GET['store_id'])) {
    die("Invalid store ID.");
}
$store_id = (int)$_GET['store_id'];

// Fetch store info
$store_sql = "SELECT * FROM stores WHERE id = $store_id";
$store_result = $conn->query($store_sql);
if ($store_result->num_rows == 0) {
    die("Store not found.");
}
$store = $store_result->fetch_assoc();

$title = "Products of " . htmlspecialchars($store['name']);
ob_start();
?>

<h2 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($store['name']); ?></h2>
<p class="text-gray-600 mb-6">Products available in this store.</p>

<?php
// Fetch products for this store
$product_sql = "SELECT * FROM products WHERE store_id = $store_id";
$product_result = $conn->query($product_sql);
?>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    <?php if ($product_result->num_rows > 0): ?>
        <?php while ($product = $product_result->fetch_assoc()): ?>
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition duration-300">
                <h3 class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                <p class="text-gray-500 mb-2">Price: $<?php echo number_format($product['price'], 2); ?></p>
                <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($product['description']); ?></p>
                <a href="product_details.php?product_id=<?php echo $product['id']; ?>"
                   class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                    View Details
                </a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="col-span-full text-center text-gray-500">No products found in this store.</p>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include './lib/layout.php';
