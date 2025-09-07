<?php
session_start();
// The $pdo variable is assumed to be defined in config.php
require_once './config.php';
require_once './lib/helpers.php';
require_once './lib/smtp.php';

$title = "Settings - Nazrul Bazar";
ob_start();

// Handle transaction status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deliver_transaction'])) {
    if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "store_owner") {
        $transactionId = $_POST['deliver_transaction'];
        try {
            // Verify the transaction belongs to the current store owner's store
            $stmt = $pdo->prepare("
                UPDATE transactions
                SET status = 'delivered'
                WHERE id = :id AND store_id = (
                    SELECT id FROM stores WHERE owner_id = :owner_id
                )
            ");
            $stmt->execute([
                'id' => $transactionId,
                'owner_id' => $_SESSION['user_id']
            ]);
            $message = "<p class='bg-emerald-900 text-emerald-300 p-2 rounded-lg mb-4 border border-emerald-700'>Transaction delivered successfully!</p>";
        } catch (PDOException $e) {
            $message = "<p class='bg-red-900 text-red-400 p-2 rounded-lg mb-4 border border-red-700'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}
?>

<div class="container mx-auto p-6">
    <div class="bg-slate-800 rounded-xl shadow-lg border border-slate-700 p-8">
        <?php if (isset($message)) echo $message; ?>

        <?php if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "store_owner"): ?>
            <h2 class="text-3xl font-bold mb-6 text-blue-400">Store Owner Settings</h2>
            <p class="text-slate-400 mb-8">Manage your store's recent transactions below.</p>

            <?php
            // Fetch store and its transactions
            $stmt = $pdo->prepare("SELECT id FROM stores WHERE owner_id = :owner_id");
            $stmt->execute(['owner_id' => $_SESSION['user_id']]);
            $store = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($store) {
                $stmt = $pdo->prepare("SELECT * FROM transactions WHERE store_id = :store_id ORDER BY time DESC");
                $stmt->execute(['store_id' => $store['id']]);
                $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($transactions): ?>
                    <div class="overflow-x-auto rounded-lg border border-slate-700">
                        <table class="min-w-full divide-y-2 divide-slate-700 bg-slate-800 text-sm">
                            <thead class="bg-slate-900">
                                <tr>
                                    <th class="whitespace-nowrap px-4 py-2 font-medium text-slate-200 text-left">Product ID</th>
                                    <th class="whitespace-nowrap px-4 py-2 font-medium text-slate-200 text-left">Buyer ID</th>
                                    <th class="whitespace-nowrap px-4 py-2 font-medium text-slate-200 text-left">Total</th>
                                    <th class="whitespace-nowrap px-4 py-2 font-medium text-slate-200 text-left">Time</th>
                                    <th class="whitespace-nowrap px-4 py-2 font-medium text-slate-200 text-left">Status</th>
                                    <th class="whitespace-nowrap px-4 py-2 font-medium text-slate-200 text-left">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700">
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr class="hover:bg-slate-700 transition-colors">
                                        <td class="whitespace-nowrap px-4 py-2 text-slate-400"><?= htmlspecialchars($transaction['product_id']) ?></td>
                                        <td class="whitespace-nowrap px-4 py-2 text-slate-400"><?= htmlspecialchars($transaction['buyer_id']) ?></td>
                                        <td class="whitespace-nowrap px-4 py-2 text-slate-400">$<?= htmlspecialchars($transaction['total']) ?></td>
                                        <td class="whitespace-nowrap px-4 py-2 text-slate-400"><?= htmlspecialchars($transaction['time']) ?></td>
                                        <td class="whitespace-nowrap px-4 py-2 text-slate-400">
                                            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                                                <?= $transaction['status'] === 'pending' ? 'bg-yellow-900 text-yellow-300' : 'bg-emerald-900 text-emerald-300' ?>">
                                                <?= htmlspecialchars(ucfirst($transaction['status'])) ?>
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-2 text-center">
                                            <?php if ($transaction['status'] === 'pending'): ?>
                                                <form method="POST" class="inline-block">
                                                    <button type="submit" name="deliver_transaction" value="<?= htmlspecialchars($transaction['id']) ?>" class="rounded-md bg-blue-600 px-4 py-2 text-xs font-medium text-white hover:bg-blue-700">
                                                        deliver
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-slate-400">Delivered</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-slate-400 text-center">No transactions found for your store.</p>
                <?php endif;
            } else {
                echo "<p class='text-slate-400 text-center'>You do not have a registered store. Please <a href='create_store.php' class='text-blue-400 hover:underline'>create one</a> to manage products and transactions.</p>";
            }
            ?>

        <?php elseif ($_SESSION['user_role'] === 'user'): ?>
            <h2 class="text-3xl font-bold mb-6 text-blue-400">User Settings</h2>
            <p class="text-slate-400 mb-8">Here you can manage your auction items.</p>

            <!-- Auction Form -->
            <div class="p-6 border border-slate-700 rounded-xl shadow-lg bg-slate-900 mb-6">
                <h3 class="text-2xl font-semibold mb-4 text-blue-400">Put Item for Auction</h3>
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block mb-1 font-medium text-slate-400">Item Name *</label>
                        <input type="text" name="item_name" class="w-full border border-slate-700 rounded-lg px-3 py-2 bg-slate-900 text-slate-200 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-slate-400">Description</label>
                        <textarea name="description" class="w-full border border-slate-700 rounded-lg px-3 py-2 bg-slate-900 text-slate-200 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-slate-400">Minimum Price *</label>
                        <input type="number" name="minimum_price" step="0.01" class="w-full border border-slate-700 rounded-lg px-3 py-2 bg-slate-900 text-slate-200 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-slate-400">End Date *</label>
                        <input type="date" name="end_date" class="w-full border border-slate-700 rounded-lg px-3 py-2 bg-slate-900 text-slate-200 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <button type="submit" name="auction_submit" class="w-full sm:w-auto bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                        Submit Auction
                    </button>
                </form>
            </div>

            <!-- My Auction Items Table -->
            <h3 class="text-2xl font-semibold mb-4 text-blue-400">My Auction Items</h3>
            <div class="overflow-x-auto rounded-lg border border-slate-700">
                <table class="min-w-full divide-y-2 divide-slate-700 bg-slate-800 text-sm">
                    <thead class="bg-slate-900">
                        <tr>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-slate-200 text-left">Item Name</th>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-slate-200 text-left">Description</th>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-slate-200 text-left">Current Price</th>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-slate-200 text-left">End Date</th>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-slate-200 text-left">Status</th>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-slate-200 text-left">Time Left</th>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-slate-200 text-left">Current Bidholder</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        <?php
                        $stmt = $pdo->prepare("SELECT * FROM auctions WHERE seller_id = ?");
                        $stmt->execute([$_SESSION['user_id']]);
                        $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($auctions) {
                            foreach ($auctions as $auction) {
                                // Determine current price and highest bidder
                                $stmt2 = $pdo->prepare("SELECT MAX(amount) as highest_bid, bidder_id FROM bids WHERE auction_id = ? GROUP BY bidder_id ORDER BY highest_bid DESC LIMIT 1");
                                $stmt2->execute([$auction['id']]);
                                $bid = $stmt2->fetch(PDO::FETCH_ASSOC);

                                $current_price = $bid && $bid['highest_bid'] ? $bid['highest_bid'] : $auction['minimum_price'];
                                $bidder_name = "No bids yet";
                                if ($bid) {
                                    $stmt3 = $pdo->prepare("SELECT name FROM users WHERE id = ?");
                                    $stmt3->execute([$bid['bidder_id']]);
                                    $bidder = $stmt3->fetch(PDO::FETCH_ASSOC);
                                    $bidder_name = htmlspecialchars($bidder['name']);
                                }
                                
                                // Determine status
                                $status = (new DateTime() < new DateTime($auction['ends_at'])) ? 'Active' : 'Ended';

                                echo "<tr class='hover:bg-slate-700 transition-colors'>
                                        <td class='px-4 py-2 text-slate-400'>" . htmlspecialchars($auction['product_name']) . "</td>
                                        <td class='px-4 py-2 text-slate-400'>" . htmlspecialchars($auction['description']) . "</td>
                                        <td class='px-4 py-2 font-medium text-slate-200'>$" . htmlspecialchars($current_price) . "</td>
                                        <td class='px-4 py-2 text-slate-400'>" . htmlspecialchars(date('M j, Y', strtotime($auction['ends_at']))) . "</td>
                                        <td class='px-4 py-2'>
                                            <span class='inline-block px-3 py-1 text-xs font-semibold rounded-full status-pill
                                                " . ($status === 'Active' ? 'bg-emerald-900 text-emerald-300' : 'bg-red-900 text-red-400') . "'>
                                                $status
                                            </span>
                                        </td>
                                        <td class='px-4 py-2 text-slate-400'><span class='countdown' data-end='" . $auction['ends_at'] . "'></span></td>
                                        <td class='px-4 py-2 text-slate-400'>$bidder_name</td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='px-4 py-2 text-center text-slate-400'>No auction items found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Countdown Timer Script -->
            <script>
                function updateCountdown() {
                    const countdowns = document.querySelectorAll('.countdown');
                    countdowns.forEach(span => {
                        const endTime = new Date(span.getAttribute('data-end')).getTime();
                        const now = new Date().getTime();
                        const distance = endTime - now;

                        const statusPill = span.closest('tr').querySelector('.status-pill');

                        if (distance < 0) {
                            span.innerHTML = "Expired";
                            if (statusPill) {
                                statusPill.innerHTML = 'Ended';
                                statusPill.classList.remove('bg-emerald-900', 'text-emerald-300');
                                statusPill.classList.add('bg-red-900', 'text-red-400');
                            }
                        } else {
                            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                            span.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
                        }
                    });
                }
                setInterval(updateCountdown, 1000);
                updateCountdown();
            </script>
        <?php else: ?>
            <p class="text-center text-slate-400">Please <a href="login.php" class="text-blue-400 hover:underline">log in</a> to view your settings.</p>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include "./lib/layout.php";
?>
