<?php
session_start();
require_once './config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Fetch user name for navbar
$stmt = $conn->prepare("SELECT name FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
$_SESSION['user_name'] = $user_data['name'];

ob_start();
?>

<h1 class="text-3xl font-bold mb-6">Dashboard</h1>

<?php if ($user_role === 'store_owner'): 
    // Fetch store info
    $stmt = $conn->prepare("SELECT * FROM stores WHERE owner_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $store = $stmt->get_result()->fetch_assoc();
    
    if ($store):
        $store_id = $store['id'];
?>
<div class="p-6 border rounded shadow bg-white mb-6">
    <h2 class="text-2xl font-semibold mb-2">My Store: <?= htmlspecialchars($store['name']) ?></h2>
    <a href="create_product.php?store_id=<?= $store_id ?>" 
       class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Add Product</a>
</div>

<h3 class="text-xl font-semibold mb-4">My Products</h3>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
<?php
    $stmt = $conn->prepare("SELECT p.*, i.image AS main_image 
                            FROM products p 
                            LEFT JOIN product_images pi ON pi.product_id = p.id
                            LEFT JOIN images i ON i.id = pi.image_id
                            WHERE p.store_id = ?
                            GROUP BY p.id");
    $stmt->bind_param("i", $store_id);
    $stmt->execute();
    $products = $stmt->get_result();

    if ($products->num_rows > 0):
        while ($p = $products->fetch_assoc()):
            $img_src = $p['main_image'] ? "data:image/jpeg;base64," . base64_encode($p['main_image']) : "";
?>
    <div class="border rounded shadow p-4 bg-white">
        <?php if ($img_src): ?>
            <img src="<?= $img_src ?>" class="w-full h-40 object-cover rounded mb-2">
        <?php else: ?>
            <div class="w-full h-40 bg-gray-200 rounded mb-2"></div>
        <?php endif; ?>
        <h4 class="font-semibold text-lg"><?= htmlspecialchars($p['name']) ?></h4>
        <p>Price: $<?= $p['price'] ?></p>
        <p>Stock: <?= $p['stock'] ?></p>
        <div class="mt-2">
            <a href="edit_product.php?id=<?= $p['id'] ?>" class="text-indigo-600 hover:underline mr-2">Edit</a>
            <a href="delete_product.php?id=<?= $p['id'] ?>" class="text-red-600 hover:underline">Delete</a>
        </div>
    </div>
<?php
        endwhile;
    else:
        echo "<p class='text-gray-500 col-span-3'>No products yet.</p>";
    endif;
?>
</div>

<?php else: ?>
<div class="p-6 bg-yellow-100 border rounded mb-6">
    <p>You do not have a store yet.</p>
    <a href="create_store.php" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Create Store</a>
</div>
<?php endif; ?>

<?php elseif ($user_role === 'user'): 
    // Normal user
    $stmt = $conn->prepare("SELECT name, email FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
?>
<div class="p-6 border rounded shadow bg-white mb-6">
    <h2 class="text-2xl font-semibold mb-2">Profile Info</h2>
    <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <a href="edit_profile.php" class="inline-block mt-2 bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Edit Profile</a>
</div>

<h3 class="text-xl font-semibold mb-4">My Cart</h3>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
<?php
$stmt = $conn->prepare("SELECT c.id AS cart_id, c.quantity, p.*, i.image AS main_image
                        FROM cart c
                        JOIN products p ON p.id = c.product_id
                        LEFT JOIN product_images pi ON pi.product_id = p.id
                        LEFT JOIN images i ON i.id = pi.image_id
                        WHERE c.user_id = ?
                        GROUP BY c.id");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result();

if ($cart_items->num_rows > 0):
    while ($item = $cart_items->fetch_assoc()):
        $img_src = $item['main_image'] ? "data:image/jpeg;base64," . base64_encode($item['main_image']) : "";
?>
<div class="border rounded shadow p-4 bg-white">
    <?php if ($img_src): ?>
        <img src="<?= $img_src ?>" class="w-full h-40 object-cover rounded mb-2">
    <?php else: ?>
        <div class="w-full h-40 bg-gray-200 rounded mb-2"></div>
    <?php endif; ?>
    <h4 class="font-semibold text-lg"><?= htmlspecialchars($item['name']) ?></h4>
    <p>Price: $<?= $item['price'] ?></p>
    <p>Quantity: <?= $item['quantity'] ?></p>
    <a href="remove_from_cart.php?id=<?= $item['cart_id'] ?>" class="text-red-600 hover:underline mt-2 inline-block">Remove</a>
</div>
<?php
    endwhile;
else:
    echo "<p class='text-gray-500 col-span-3'>Your cart is empty.</p>";
endif;
?>
</div>

<?php endif; ?>

<?php
$content = ob_get_clean();
$title = "User Dashboard";
include './lib/layout.php';
