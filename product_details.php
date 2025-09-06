<?php
session_start();
require_once './config.php'; // DB connection with PDO

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Validate product_id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID.");
}

$product_id = (int)$_GET['id'];
$message = ''; // To display feedback to the user

// Handle Add to Cart / Buy Now form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    // Fetch product for price/store_id (using PDO now)
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("Product not found.");
    }

    // Check if cart exists for this user (using PDO now)
    $stmt = $pdo->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart_result) {
        $cart_id = $cart_result['cart_id'];
    } else {
        // Create a new cart
        $stmt = $pdo->prepare("INSERT INTO carts (user_id) VALUES (?)");
        $stmt->execute([$user_id]);
        $cart_id = $pdo->lastInsertId();
    }

    // Add product to cart_items (or update quantity if exists)
    $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?");
    $stmt->execute([$cart_id, $product_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Update quantity
        $stmt = $pdo->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE cart_id = ? AND product_id = ?");
        $stmt->execute([$quantity, $cart_id, $product_id]);
    } else {
        // Insert new item
        $stmt = $pdo->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$cart_id, $product_id, $quantity]);
    }

    if ($action === 'add_to_cart') {
        $message = "<p class='text-green-600'>Added to cart successfully!</p>";
    } elseif ($action === 'buy_now') {
        // For 'Buy Now', add to cart and then redirect to checkout
        header("Location: checkout.php");
        exit();
    }
}

// Fetch product details for display (using PDO now)
$stmt = $pdo->prepare("
    SELECT p.*, s.name AS store_name, u.name AS owner_name
    FROM products p
    JOIN stores s ON p.store_id = s.id
    JOIN users u ON s.owner_id = u.id
    WHERE p.id = ?
");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) { // Changed from num_rows == 0 to !product for PDO fetch
    die("Product not found.");
}

$title = htmlspecialchars($product['name']);
ob_start();
?>

<div class="max-w-5xl mx-auto py-8">
    <?php echo $message; // Display message here ?>
    <h1 class="text-3xl font-bold mb-4"><?php echo htmlspecialchars($product['name']); ?></h1>
    <p class="text-gray-600 mb-2">Store: <?php echo htmlspecialchars($product['store_name']); ?> (Owner: <?php echo htmlspecialchars($product['owner_name']); ?>)</p>
    <p class="text-gray-800 font-semibold mb-4">Price: $<?php echo number_format($product['price'], 2); ?></p>
    <p class="text-gray-700 mb-6"><?php echo htmlspecialchars($product['description']); ?></p>

    <!-- Product images -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <?php
        // Using PDO for image fetching
        $stmt_img = $pdo->prepare("SELECT i.image FROM images i
                                   JOIN product_images pi ON i.id = pi.image_id
                                   WHERE pi.product_id = ?");
        $stmt_img->execute([$product_id]);
        $img_results = $stmt_img->fetchAll(PDO::FETCH_ASSOC);

        if ($img_results):
            foreach ($img_results as $img):
                // CORRECTED: Use the base64 string directly from the database
                $base64 = $img['image'];
        ?>
            <img class="rounded shadow" src="data:image/png;base64,<?php echo $base64; ?>" alt="Product Image">
        <?php
            endforeach;
        else:
            echo "<p class='text-gray-500'>No images available.</p>";
        endif;
        ?>
    </div>

    <!-- Buy Now / Add to Cart Form -->
    <form method="POST" class="flex gap-4">
        <input type="number" name="quantity" value="1" min="1" class="border rounded p-2 w-20">
        <button type="submit" name="action" value="buy_now" class="bg-green-500 text-white px-6 py-3 rounded hover:bg-green-600 transition">Buy Now</button>
        <button type="submit" name="action" value="add_to_cart" class="bg-yellow-500 text-white px-6 py-3 rounded hover:bg-yellow-600 transition">Add to Cart</button>
    </form>
</div>

<?php
$content = ob_get_clean();
include './lib/layout.php';
?>