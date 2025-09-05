<?php
session_start();
require_once './config.php'; // DB connection

// Validate product_id
if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    die("Invalid product ID.");
}

$product_id = (int)$_GET['product_id'];

// Fetch product info
$stmt = $conn->prepare("
    SELECT p.*, s.name AS store_name, u.name AS owner_name
    FROM products p
    JOIN stores s ON p.store_id = s.id
    JOIN users u ON s.owner_id = u.id
    WHERE p.id = ?
");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();


if ($product_result->num_rows == 0) {
    die("Product not found.");
}

$product = $product_result->fetch_assoc();

$title = htmlspecialchars($product['name']);
ob_start();
?>

<div class="max-w-5xl mx-auto py-8">
    <h1 class="text-3xl font-bold mb-4"><?php echo htmlspecialchars($product['name']); ?></h1>
    <p class="text-gray-600 mb-2">Store: <?php echo htmlspecialchars($product['store_name']); ?> (Owner: <?php echo htmlspecialchars($product['owner_name']); ?>)</p>
    <p class="text-gray-800 font-semibold mb-4">Price: $<?php echo number_format($product['price'], 2); ?></p>
    <p class="text-gray-700 mb-6"><?php echo htmlspecialchars($product['description']); ?></p>

    <!-- Product images -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <?php
        $img_sql = "SELECT i.image FROM images i
                    JOIN product_images pi ON i.id = pi.image_id
                    WHERE pi.product_id = $product_id";
        $img_result = $conn->query($img_sql);

        if ($img_result && $img_result->num_rows > 0):
            while ($img = $img_result->fetch_assoc()):
                // Use the base64 string directly (already stored in DB)
                $base64 = $img['image'];
        ?>
            <img class="rounded shadow" src="data:image/png;base64,<?php echo $base64; ?>" alt="Product Image">
        <?php
            endwhile;
        else:
            echo "<p class='text-gray-500'>No images available.</p>";
        endif;
        ?>
    </div>

    <div class="flex gap-4">
        <a href="buynow.php?product_id=<?php echo $product_id; ?>" 
           class="inline-block bg-green-500 text-white px-6 py-3 rounded hover:bg-green-600 transition">
           Buy Now
        </a>
        <a href="buynow.php?product_id=<?php echo $product_id; ?>" 
           class="inline-block bg-yellow-500 text-white px-6 py-3 rounded hover:bg-yellow-600 transition">
           Add to Cart
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include './lib/layout.php';
?>
