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

<div class="max-w-3xl mx-auto py-8">
    <h1 class="text-3xl font-bold mb-6">Checkout</h1>

    <?php if ($checkout_message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline"><?= htmlspecialchars($checkout_message) ?></span>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline"><?= htmlspecialchars($error_message) ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($cart_items)): ?>
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Order Summary</h2>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <?php
                    $overall_total = 0;
                    foreach ($cart_items as $item):
                        $item_total = $item['price'] * $item['quantity'];
                        $overall_total += $item_total;
                    ?>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Product Name
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <?= htmlspecialchars($item['product_name']) ?>
                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Price per unit
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                $<?= number_format($item['price'], 2) ?>
                            </dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Quantity
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <?= $item['quantity'] ?>
                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Item Total
                            </dt>
                            <dd class="mt-1 text-sm font-bold text-gray-900 sm:mt-0 sm:col-span-2">
                                $<?= number_format($item_total, 2) ?>
                            </dd>
                        </div>
                    <?php endforeach; ?>
                    <div class="bg-gray-100 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 border-t border-gray-200">
                        <dt class="text-lg font-bold text-gray-700">
                            Overall Total
                        </dt>
                        <dd class="mt-1 text-lg font-extrabold text-indigo-600 sm:mt-0 sm:col-span-2">
                            $<?= number_format($overall_total, 2) ?>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <form method="POST">
            <button type="submit" name="checkout"
                    class="w-full bg-indigo-600 text-white px-6 py-3 rounded-md text-lg font-semibold
                           hover:bg-indigo-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Confirm Purchase
            </button>
        </form>
    <?php else: ?>
        <p class="text-gray-600">No items to checkout. Your cart is empty or has been processed.</p>
        <a href="./buynow.php" class="inline-block mt-4 bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Continue Shopping</a>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include './lib/layout.php';
?>