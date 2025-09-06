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

<div class="max-w-2xl mx-auto bg-white shadow-md rounded-lg p-6 mt-6">
    <h2 class="text-2xl font-bold mb-4"><?= htmlspecialchars($auction['product_name']) ?></h2>

    <p class="mb-2 text-gray-700"><?= nl2br(htmlspecialchars($auction['description'])) ?></p>

    <p class="text-green-700 font-bold">
        Starting Price: $<?= number_format($auction['minimum_price'], 2) ?>
    </p>
    <p class="text-blue-700 font-bold">
        Current Highest Bid: $<?= number_format($highest_bid > 0 ? $highest_bid : $auction['minimum_price'], 2) ?>
    </p>

    <!-- Countdown Timer -->
    <p class="text-red-600 font-bold mb-4">
        Time Remaining: <span id="countdown"></span>
    </p>

    <?php if (!empty($message)): ?>
        <div class="mb-4 p-2 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="mt-4 flex gap-2">
        <input type="number" name="bid_amount" step="0.01" min="0"
               placeholder="Enter your bid"
               class="border rounded px-3 py-2 flex-1" required>
        <button type="submit"
                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            Place Bid
        </button>
    </form>
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
            document.querySelector("form").style.display = "none"; // disable bidding
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
