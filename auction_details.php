<?php
session_start();
require_once './config.php'; // $pdo is PDO

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view auctions.");
}

$auction_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $pdo->prepare("SELECT * FROM auctions WHERE id = ?");
$stmt->execute([$auction_id]);
$auction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$auction) {
    die("Auction not found.");
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bid_amount'])) {
    $bid_amount = floatval($_POST['bid_amount']);
    $user_id = $_SESSION['user_id'];

    $stmt2 = $pdo->prepare("SELECT MAX(amount) as highest_bid FROM bids WHERE auction_id = ?");
    $stmt2->execute([$auction_id]);
    $highest = $stmt2->fetch(PDO::FETCH_ASSOC);
    $highest_bid = $highest['highest_bid'] ?? 0;

    $min_required = max($auction['minimum_price'], $highest_bid + 1);

    if ($bid_amount < $min_required) {
        $message = "Your bid must be at least $" . number_format($min_required, 2);
    } else {
        $stmt3 = $pdo->prepare("INSERT INTO bids (auction_id, bidder_id, amount) VALUES (?, ?, ?)");
        $stmt3->execute([$auction_id, $user_id, $bid_amount]);

        $stmt4 = $pdo->prepare("UPDATE auctions SET current_price = ?, current_holder_id = ? WHERE id = ?");
        $stmt4->execute([$bid_amount, $user_id, $auction_id]);

        $message = "✅ Bid placed successfully!";
        $stmt->execute([$auction_id]);
        $auction = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

$stmt5 = $pdo->prepare("SELECT MAX(amount) as highest_bid FROM bids WHERE auction_id = ?");
$stmt5->execute([$auction_id]);
$highest = $stmt5->fetch(PDO::FETCH_ASSOC);
$highest_bid = $highest['highest_bid'] ?? 0;

$title = $auction['product_name'] . " - JKKNIU Marketplace";
ob_start();
?>

<div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8">
        <h2 class="text-3xl font-extrabold text-gray-900 mb-4"><?= htmlspecialchars($auction['product_name']) ?></h2>

        <p class="mb-4 text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($auction['description'])) ?></p>

        <p class="mb-2 text-green-600 font-semibold text-lg">
            Starting Price: <span class="text-green-800">$<?= number_format($auction['minimum_price'], 2) ?></span>
        </p>
        <p class="mb-4 text-blue-600 font-semibold text-lg">
            Current Highest Bid: <span class="text-blue-800">$<?= number_format($highest_bid > 0 ? $highest_bid : $auction['minimum_price'], 2) ?></span>
        </p>

        <!-- Countdown Timer -->
        <p class="text-red-600 font-bold mb-4 text-lg">
            Time Remaining: <span id="countdown" class="text-red-800 font-extrabold"></span>
        </p>

        <?php if (!empty($message)): ?>
            <div class="mb-4 p-4 rounded-lg
                <?= strpos($message, '✅') !== false ? 'bg-green-100 text-green-800 border-green-400' : 'bg-red-100 text-red-800 border-red-400' ?> border-l-4">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="mt-4 flex flex-col sm:flex-row gap-4">
            <input type="number" name="bid_amount" step="0.01" min="0"
                   placeholder="Enter your bid amount"
                   class="border border-gray-300 rounded-lg px-4 py-3 flex-1 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-200" required>
            <button type="submit"
                    class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold transition-transform hover:scale-105 hover:bg-indigo-700">
                Place Bid
            </button>
        </form>
    </div>
</div>

<!-- Countdown Script -->
<script>
    const countdownElem = document.getElementById("countdown");
    const endTime = new Date("<?= $auction['ends_at'] ?>").getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = endTime - now;

        if (distance <= 0) {
            countdownElem.innerHTML = "Auction Ended";
            const form = document.querySelector("form");
            if (form) {
                form.style.display = "none";
            }
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        countdownElem.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
    }

    updateCountdown(); // initial call
    setInterval(updateCountdown, 1000);
</script>

<?php
$content = ob_get_clean();
include './lib/layout.php';
?>
