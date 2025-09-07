<?php
session_start();
// Optional: authentication check
// if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'store_owner' && $_SESSION['user_role'] !== 'admin')) {
//     header("Location: login.php");
//     exit;
// }

$title = "Stores - Nazrul Bazar";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Hackathon"; // Your DB name

// Create a new mysqli connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

ob_start();

// Fetch stores with owner info
$sql = "SELECT stores.id AS store_id, stores.name AS store_name, users.name AS owner_name
        FROM stores
        JOIN users ON stores.owner_id = users.id
        ORDER BY stores.id ASC";
$result = $conn->query($sql);
$stores = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $stores[] = $row;
    }
} else {
    // Optionally handle the case where the query fails, e.g., log the error
    error_log("Error fetching stores: " . $conn->error);
}

?>
<div class="container mx-auto p-6">
    <div class="bg-slate-800 rounded-xl shadow-lg border border-slate-700 p-8">
        <h2 class="text-2xl font-bold mb-4 text-blue-400">Popular Stores</h2>
        <p class="text-slate-400 mb-6">Here are the recommended stores from our users.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php if (!empty($stores)): ?>
                <?php foreach ($stores as $store): ?>
                    <div class="bg-slate-900 rounded-lg shadow-lg border border-slate-700 p-6 hover:shadow-xl transition duration-300">
                        <h3 class="text-lg font-semibold mb-2 text-blue-400"><?= htmlspecialchars($store['store_name']); ?></h3>
                        <p class="text-slate-400 mb-4">Owner: <?= htmlspecialchars($store['owner_name']); ?></p>
                        <a href="store_products.php?store_id=<?= $store['store_id']; ?>"
                           class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            View Products
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="col-span-full text-center text-slate-400">No stores found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include './lib/layout.php';
?>
