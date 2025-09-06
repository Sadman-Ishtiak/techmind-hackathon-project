<?php
session_start();
require_once './config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
// $conn = new mysqli("localhost", "root", "", "hackathon");
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Fetch user name for navbar
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);
$_SESSION['user_name'] = $user_data['name'];

ob_start();
?>

<h1 class="text-3xl font-bold mb-6">Dashboard</h1>

<?php if ($user_role === 'store_owner'): 
    // Fetch store info
    $stmt = $pdo->prepare("SELECT * FROM stores WHERE owner_id = ?");
    $stmt->execute([$user_id]);
    $store = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($store):
        $store_id = $store['id'];
?>
<!-- My Store Section -->
<div class="p-6 border rounded shadow bg-white mb-6">
    <h2 class="text-2xl font-semibold mb-2">My Store: <?= htmlspecialchars($store['name']) ?></h2>
    <a href="create_product.php?store_id=<?= $store_id ?>" 
       class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Add Product</a>
</div>

<!-- Products Table -->
<h3 class="text-xl font-semibold mb-4">My Products</h3>
<?php
    $stmt = $pdo->prepare("SELECT p.*, i.image AS main_image 
                            FROM products p 
                            LEFT JOIN product_images pi ON pi.product_id = p.id
                            LEFT JOIN images i ON i.id = pi.image_id
                            WHERE p.store_id = ?
                            GROUP BY p.id");
    $stmt->execute([$store_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($products):
?>
<table class="min-w-full bg-white border border-gray-300 rounded shadow">
    <thead class="bg-gray-100 border-b border-gray-300">
        <tr>
            <th class="py-2 px-4">Image</th>
            <th class="py-2 px-4">Product Name</th>
            <th class="py-2 px-4">Price</th>
            <th class="py-2 px-4">Stock</th>
            <th class="py-2 px-4">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $p): 
            $img_src = $p['main_image'] ? "data:image/jpeg;base64," . base64_encode($p['main_image']) : "";
        ?>
        <tr class="border-b">
            <td class="py-2 px-4">
                <?php if ($img_src): ?>
                    <img src="<?= $img_src ?>" class="w-20 h-20 object-cover rounded">
                <?php else: ?>
                    <div class="w-20 h-20 bg-gray-200 rounded"></div>
                <?php endif; ?>
            </td>
            <td class="py-2 px-4"><?= htmlspecialchars($p['name']) ?></td>
            <td class="py-2 px-4">$<?= $p['price'] ?></td>
            <td class="py-2 px-4"><?= $p['stock'] ?></td>
            <td class="py-2 px-4">
                <a href="edit_product.php?id=<?= $p['id'] ?>" class="text-indigo-600 hover:underline mr-2">Edit</a>
                <a href="delete_product.php?id=<?= $p['id'] ?>" class="text-red-600 hover:underline">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p class='text-gray-500'>No products yet.</p>
<?php endif; ?>

<?php else: ?>
<div class="p-6 bg-yellow-100 border rounded mb-6">
    <p>You do not have a store yet.</p>
    <a href="create_store.php" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Create Store</a>
</div>
<?php endif; ?>

<?php elseif ($user_role === 'user'): ?>

<!-- Profile Info Section -->
<div class="p-6 border rounded shadow bg-white mb-6">
    <h2 class="text-2xl font-semibold mb-2">Profile Info</h2>
    <p><strong>Name:</strong> <?= htmlspecialchars($user_data['name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user_data['email']) ?></p>
    <a href="edit_profile.php" class="inline-block mt-2 bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Edit Profile</a>
</div>

<!-- Cart Section -->
<h3 class="text-xl font-semibold mb-4">My Cart</h3>
<?php
// Ensure cart exists
$stmt = $pdo->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cart) {
    $stmt = $pdo->prepare("INSERT INTO carts (user_id) VALUES (?)");
    $stmt->execute([$user_id]);
    $cart_id = $pdo->lastInsertId();
} else {
    $cart_id = $cart['cart_id'];
}

// Handle Remove from Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_id'])) {
    $remove_id = (int)$_POST['remove_id'];
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_item_id = ? AND cart_id = ?");
    $stmt->execute([$remove_id, $cart_id]);
}

// Fetch cart items
$stmt = $pdo->prepare("SELECT ci.cart_item_id, ci.quantity, p.*, i.image AS main_image
                        FROM cart_items ci
                        JOIN products p ON p.id = ci.product_id
                        LEFT JOIN product_images pi ON pi.product_id = p.id
                        LEFT JOIN images i ON i.id = pi.image_id
                        WHERE ci.cart_id = ?
                        GROUP BY ci.cart_item_id");
$stmt->execute([$cart_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($cart_items):
?>
<form method="POST">
<table class="min-w-full bg-white border border-gray-300 rounded shadow">
    <thead class="bg-gray-100 border-b border-gray-300">
        <tr>
            <th class="py-2 px-4">Sl. No.</th>
            <th class="py-2 px-4">Product Name</th>
            <th class="py-2 px-4">Price</th>
            <th class="py-2 px-4">Quantity</th>
            <th class="py-2 px-4">Total</th>
            <th class="py-2 px-4">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php $counter = 1; ?>
        <?php foreach ($cart_items as $item): 
            // $img_src = $item['main_image'] ? "data:image/jpeg;base64," . base64_encode($item['main_image']) : "";
            $total = $item['price'] * $item['quantity'];
        ?>
        <tr class="border-b">
            <td class="py-2 px-4">
                <?= $counter++; ?>
            </td>
            <td class="py-2 px-4"><?= htmlspecialchars($item['name']) ?></td>
            <td class="py-2 px-4">$<?= $item['price'] ?></td>
            <td class="py-2 px-4"><?= $item['quantity'] ?></td>
            <td class="py-2 px-4">$<?= number_format($total, 2) ?></td>
            <td class="py-2 px-4">
                <button type="submit" name="remove_id" value="<?= $item['cart_item_id'] ?>" class="text-red-600 hover:underline">Remove</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</form>
<?php else: ?>
<p class='text-gray-500'>Your cart is empty.</p>
<?php endif; ?>

<?php endif; ?>

<?php
$content = ob_get_clean();
$title = "User Dashboard";
include './lib/layout.php';
?>
