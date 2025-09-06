<?php
session_start();

// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Hackathon";
$conn = new mysqli($servername, $username, $password, $dbname);

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

// Handle Add to Cart / Buy Now form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    // Fetch product for price/store_id
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    if (!$product) {
        die("Product not found.");
    }

    if ($action === 'add_to_cart') {
        // Check if cart exists for this user
        $cart_sql = "SELECT cart_id FROM carts WHERE user_id = ?";
        $stmt = $conn->prepare($cart_sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $cart_result = $stmt->get_result();

        if ($cart_result->num_rows > 0) {
            $cart_id = $cart_result->fetch_assoc()['cart_id'];
        } else {
            // Create a new cart
            $stmt = $conn->prepare("INSERT INTO carts (user_id) VALUES (?)");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $cart_id = $stmt->insert_id;
        }

        // Add product to cart_items (or update quantity if exists)
        $stmt = $conn->prepare("SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $cart_id, $product_id);
        $stmt->execute();
        $existing = $stmt->get_result();

        if ($existing->num_rows > 0) {
            // Update quantity
            $stmt = $conn->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE cart_id = ? AND product_id = ?");
            $stmt->bind_param("iii", $quantity, $cart_id, $product_id);
        } else {
            // Insert new item
            $stmt = $conn->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $cart_id, $product_id, $quantity);
        }
        $stmt->execute();

        echo "<p class='text-green-600'>Added to cart successfully!</p>";

    } elseif ($action === 'buy_now') {
        // Directly create transaction
        $total = $product['price'] * $quantity;
        $stmt = $conn->prepare("INSERT INTO transactions (store_id, product_id, buyer_id, cost, quantity, total, time)
                                VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiidid", $product['store_id'], $product_id, $user_id, $product['price'], $quantity, $total);
        $stmt->execute();

        echo "<p class='text-green-600'>Purchase successful!</p>";
    }
}

// Fetch product details for display
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
