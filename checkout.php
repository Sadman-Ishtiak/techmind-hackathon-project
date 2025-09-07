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

<div class="max-w-3xl mx-auto py-8 px-4 text-slate-200">
    <h1 class="text-3xl font-extrabold mb-8 text-blue-400">Checkout</h1>

    <?php if ($checkout_message): ?>
        <div class="bg-green-800 border border-green-700 text-green-200 px-4 py-3 rounded-lg relative mb-6" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline"><?= htmlspecialchars($checkout_message) ?></span>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="bg-red-800 border border-red-700 text-red-200 px-4 py-3 rounded-lg relative mb-6" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline"><?= htmlspecialchars($error_message) ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($cart_items)): ?>
        <div class="bg-slate-800 shadow-xl overflow-hidden rounded-xl border border-slate-700 mb-6">
            <div class="px-4 py-5 sm:px-6 border-b border-slate-700">
                <h2 class="text-xl font-semibold text-slate-200">Order Summary</h2>
            </div>
            <div class="border-t border-slate-700">
                <dl>
                    <?php
                    $overall_total = 0;
                    foreach ($cart_items as $item):
                        $item_total = $item['price'] * $item['quantity'];
                        $overall_total += $item_total;
                    ?>
                        <div class="bg-slate-900 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-slate-400">
                                Product Name
                            </dt>
                            <dd class="mt-1 text-sm text-slate-200 sm:mt-0 sm:col-span-2">
                                <?= htmlspecialchars($item['product_name']) ?>
                            </dd>
                        </div>
                        <div class="bg-slate-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-slate-400">
                                Price per unit
                            </dt>
                            <dd class="mt-1 text-sm text-slate-200 sm:mt-0 sm:col-span-2">
                                $<?= number_format($item['price'], 2) ?>
                            </dd>
                        </div>
                        <div class="bg-slate-900 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-slate-400">
                                Quantity
                            </dt>
                            <dd class="mt-1 text-sm text-slate-200 sm:mt-0 sm:col-span-2">
                                <?= $item['quantity'] ?>
                            </dd>
                        </div>
                        <div class="bg-slate-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-slate-400">
                                Item Total
                            </dt>
                            <dd class="mt-1 text-sm font-bold text-slate-200 sm:mt-0 sm:col-span-2">
                                $<?= number_format($item_total, 2) ?>
                            </dd>
                        </div>
                    <?php endforeach; ?>
                    <div class="bg-slate-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 border-t border-slate-600">
                        <dt class="text-lg font-bold text-slate-200">
                            Overall Total
                        </dt>
                        <dd class="mt-1 text-lg font-extrabold text-blue-400 sm:mt-0 sm:col-span-2">
                            $<?= number_format($overall_total, 2) ?>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <form method="POST">
            <button type="submit" name="checkout"
                class="w-full bg-blue-600 text-white px-6 py-3 rounded-md text-lg font-semibold
                    hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Confirm Purchase
            </button>
        </form>
    <?php else: ?>
        <p class="text-slate-400 text-center">No items to checkout. Your cart is empty or has been processed.</p>
        <div class="text-center mt-6">
            <a href="./buynow.php" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors duration-200 font-semibold">
                Continue Shopping
            </a>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include './lib/layout.php';
?>