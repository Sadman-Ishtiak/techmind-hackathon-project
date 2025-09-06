<?php
session_start();
require_once './config.php';

// Redirect if not logged in
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$checkout_message = '';
$error_message = '';

// Check if the user has a cart
$stmt = $pdo->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cart) {
    $error_message = "Your cart is empty.";
} else {
    $cart_id = $cart['cart_id'];

    // Fetch cart items
    $stmt = $pdo->prepare("
        SELECT ci.cart_item_id, ci.quantity, p.id AS product_id, p.name AS product_name, p.price, p.stock, p.store_id
        FROM cart_items ci
        JOIN products p ON p.id = ci.product_id
        WHERE ci.cart_id = ?
    ");
    $stmt->execute([$cart_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cart_items)) {
        $error_message = "Your cart is empty.";
    } else {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
            $pdo->beginTransaction();
            try {
                $total_order_cost = 0;
                foreach ($cart_items as $item) {
                    // Check stock
                    if ($item['stock'] < $item['quantity']) {
                        throw new Exception("Not enough stock for " . htmlspecialchars($item['product_name']) . ". Available: " . $item['stock']);
                    }

                    $item_total = $item['price'] * $item['quantity'];
                    $total_order_cost += $item_total;

                    // Insert into transactions
                    $stmt = $pdo->prepare("
                        INSERT INTO transactions (store_id, product_id, buyer_id, cost, quantity, total, time)
                        VALUES (?, ?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([
                        $item['store_id'],
                        $item['product_id'],
                        $user_id,
                        $item['price'],
                        $item['quantity'],
                        $item_total
                    ]);

                    // Update product stock
                    $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                    $stmt->execute([$item['quantity'], $item['product_id']]);
                }

                // Clear cart items after successful transaction
                $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?");
                $stmt->execute([$cart_id]);

                $pdo->commit();
                $checkout_message = "Checkout successful! Your total order cost was $" . number_format($total_order_cost, 2);
                $cart_items = []; // Clear items for display
            } catch (Exception $e) {
                $pdo->rollBack();
                $error_message = "Checkout failed: " . $e->getMessage();
            }
        }
    }
}

$title = "Checkout";
ob_start();
?>

<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-4xl sm:text-5xl font-extrabold text-center text-gray-900 mb-8">Checkout</h1>

    <?php if ($checkout_message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-xl shadow-md relative mb-6" role="alert">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="font-semibold text-lg">Success!</div>
            </div>
            <span class="block mt-1 sm:inline"><?= htmlspecialchars($checkout_message) ?></span>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl shadow-md relative mb-6" role="alert">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-red-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="font-semibold text-lg">Error!</div>
            </div>
            <span class="block mt-1 sm:inline"><?= htmlspecialchars($error_message) ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($cart_items)): ?>
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-6">
            <div class="px-6 py-5 sm:px-8 border-b border-gray-200 bg-gray-50">
                <h2 class="text-2xl font-bold text-gray-900">Order Summary</h2>
            </div>
            <div class="divide-y divide-gray-200">
                <?php
                $overall_total = 0;
                foreach ($cart_items as $item):
                    $item_total = $item['price'] * $item['quantity'];
                    $overall_total += $item_total;
                ?>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 px-6 py-5">
                        <div class="text-sm font-medium text-gray-500">
                            <span class="block sm:hidden">Product:</span>
                            <span class="hidden sm:block">Product Name</span>
                        </div>
                        <div class="mt-1 text-sm font-semibold text-gray-900 sm:mt-0 sm:col-span-2">
                            <?= htmlspecialchars($item['product_name']) ?>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 px-6 py-5">
                        <div class="text-sm font-medium text-gray-500">
                            <span class="block sm:hidden">Price:</span>
                            <span class="hidden sm:block">Price per unit</span>
                        </div>
                        <div class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            $<?= number_format($item['price'], 2) ?>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 px-6 py-5">
                        <div class="text-sm font-medium text-gray-500">
                            <span class="block sm:hidden">Quantity:</span>
                            <span class="hidden sm:block">Quantity</span>
                        </div>
                        <div class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <?= $item['quantity'] ?>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 px-6 py-5">
                        <div class="text-sm font-medium text-gray-500">
                            <span class="block sm:hidden">Item Total:</span>
                            <span class="hidden sm:block">Item Total</span>
                        </div>
                        <div class="mt-1 text-sm font-bold text-gray-900 sm:mt-0 sm:col-span-2">
                            $<?= number_format($item_total, 2) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 px-6 py-5 bg-gray-100">
                    <div class="text-lg font-bold text-gray-700">
                        Overall Total
                    </div>
                    <div class="mt-1 text-lg font-extrabold text-indigo-600 sm:mt-0 sm:col-span-2">
                        $<?= number_format($overall_total, 2) ?>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST">
            <button type="submit" name="checkout"
                    class="w-full bg-indigo-600 text-white px-6 py-4 rounded-xl text-lg font-semibold
                           hover:bg-indigo-700 transition-all duration-300 transform hover:scale-105
                           focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-lg">
                Confirm Purchase
            </button>
        </form>
    <?php else: ?>
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <p class="text-gray-600 text-lg mb-4">No items to checkout. Your cart is empty or has been processed.</p>
            <a href="./buynow.php" class="inline-block mt-4 bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold transition-transform hover:scale-105 hover:bg-indigo-700 shadow-md">Continue Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include './lib/layout.php';
?>
