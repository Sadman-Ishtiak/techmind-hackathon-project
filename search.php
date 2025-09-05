<?php
session_start();
$title = "Search - JKKNIU Marketplace";
require './config.php'; // Your PDO config

ob_start();

// Get search query
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

?>
<h2 class="text-xl font-bold mb-4">Search Results</h2>

<?php if ($search === ''): ?>
    <p>Please enter a search term.</p>
<?php else: ?>
    <p>Results for: <strong><?= htmlspecialchars($search) ?></strong></p>
    <?php
    $like = "%$search%";

    // Search products
    $stmtProducts = $conn->prepare("SELECT p.*, s.name AS store_name 
                                    FROM products p 
                                    JOIN stores s ON p.store_id = s.id
                                    WHERE p.name LIKE :search OR p.description LIKE :search");
    $stmtProducts->execute(['search' => $like]);
    $products = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

    // Search auctions
    $stmtAuctions = $conn->prepare("SELECT * FROM auctions 
                                    WHERE product_name LIKE :search OR description LIKE :search");
    $stmtAuctions->execute(['search' => $like]);
    $auctions = $stmtAuctions->fetchAll(PDO::FETCH_ASSOC);

    // Search stores
    $stmtStores = $conn->prepare("SELECT * FROM stores WHERE name LIKE :search");
    $stmtStores->execute(['search' => $like]);
    $stores = $stmtStores->fetchAll(PDO::FETCH_ASSOC);

    // Display results
    if (count($products) > 0) {
        echo "<h3>Products</h3><ul>";
        foreach ($products as $p) {
            echo "<li><strong>" . htmlspecialchars($p['name']) . "</strong> (Store: " . htmlspecialchars($p['store_name']) . ")<br>";
            echo htmlspecialchars($p['description']) . "</li>";
        }
        echo "</ul>";
    }

    if (count($auctions) > 0) {
        echo "<h3>Auctions</h3><ul>";
        foreach ($auctions as $a) {
            echo "<li><strong>" . htmlspecialchars($a['product_name']) . "</strong><br>";
            echo htmlspecialchars($a['description']) . "</li>";
        }
        echo "</ul>";
    }

    if (count($stores) > 0) {
        echo "<h3>Stores</h3><ul>";
        foreach ($stores as $s) {
            echo "<li>" . htmlspecialchars($s['name']) . "</li>";
        }
        echo "</ul>";
    }

    if (count($products) + count($auctions) + count($stores) === 0) {
        echo "<p>No results found.</p>";
    }
    ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
include './lib/layout.php';
?>
