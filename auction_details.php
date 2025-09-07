<?php
session_start();
require_once './config.php'; // $pdo is PDO

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit; // It's good practice to exit after a redirect
}

$auction_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $pdo->prepare("SELECT * FROM auctions WHERE id = ?");
$stmt->execute([$auction_id]);
$auction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$auction) {
    die("Auction not found.");
}

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bid_amount'])) {
    $bid_amount = floatval($_POST['bid_amount']);
    $user_id = $_SESSION['user_id'];

    $stmt2 = $pdo->prepare("SELECT MAX(amount) as highest_bid FROM bids WHERE auction_id = ?");
    $stmt2->execute([$auction_id]);
    $highest = $stmt2->fetch(PDO::FETCH_ASSOC);
    $highest_bid = $highest['highest_bid'] ?? 0;

    $min_required = max($auction['minimum_price'], $highest_bid + 1);

    if ($bid_amount < $min_required) {
        $error = "Your bid must be at least $" . number_format($min_required, 2);
    } else {
        $pdo->beginTransaction();
        try {
            $stmt3 = $pdo->prepare("INSERT INTO bids (auction_id, bidder_id, amount) VALUES (?, ?, ?)");
            $stmt3->execute([$auction_id, $user_id, $bid_amount]);

            $stmt4 = $pdo->prepare("UPDATE auctions SET current_price = ?, current_holder_id = ? WHERE id = ?");
            $stmt4->execute([$bid_amount, $user_id, $auction_id]);
            
            $pdo->commit();
            $message = "✅ Bid placed successfully!";
            
            // Re-fetch auction data to display updated bid
            $stmt->execute([$auction_id]);
            $auction = $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to place bid. Please try again. " . $e->getMessage();
        }
    }
}

// Always fetch the highest bid after any action
$stmt5 = $pdo->prepare("SELECT MAX(amount) as highest_bid FROM bids WHERE auction_id = ?");
$stmt5->execute([$auction_id]);
$highest = $stmt5->fetch(PDO::FETCH_ASSOC);
$highest_bid = $highest['highest_bid'] ?? 0;

// Fetch auction images
$img_stmt = $pdo->prepare("
    SELECT i.image 
    FROM images i
    JOIN auction_images ai ON ai.image_id = i.id
    WHERE ai.auction_id = ?
");
$img_stmt->execute([$auction_id]);
$images = $img_stmt->fetchAll(PDO::FETCH_ASSOC);


$title = $auction['product_name'] . " - Nazrul Bazar";
ob_start();
?>
<div class="container mx-auto p-4 md:p-8">
    <div class="max-w-4xl mx-auto bg-slate-800 shadow-2xl rounded-xl p-6 transform transition-all duration-500 hover:scale-[1.01]">
        <h2 class="text-3xl font-bold mb-6 text-blue-400"><?= htmlspecialchars($auction['product_name']) ?></h2>

<<<<<<< HEAD
        <div class="mb-6">
            <?php if (!empty($images)): ?>
                <div class="flex flex-wrap gap-4 justify-center">
                    <?php foreach ($images as $img): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($img['image']) ?>" 
                             class="w-48 h-48 object-cover rounded-lg shadow-lg border-2 border-slate-700 transition-transform duration-300 hover:scale-105"
                             alt="Auction image">
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="w-full h-64 bg-slate-700 rounded-lg flex items-center justify-center text-slate-500">
                    No Images Available
                </div>
            <?php endif; ?>
        </div>
        
        <p class="mb-4 text-slate-300"><?= nl2br(htmlspecialchars($auction['description'])) ?></p>

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
            <p class="text-green-400 font-bold text-xl mb-2 sm:mb-0">
                Starting Price: $<?= number_format($auction['minimum_price'], 2) ?>
            </p>
            <p class="text-blue-400 font-bold text-xl">
                Current Highest Bid: $<?= number_format($highest_bid > 0 ? $highest_bid : $auction['minimum_price'], 2) ?>
            </p>
        </div>

        <p class="text-red-400 font-bold text-lg mb-4">
            Time Remaining: <span id="countdown"></span>
        </p>

        <?php if (!empty($message)): ?>
            <div class="mb-4 p-3 bg-green-700 bg-opacity-30 border border-green-500 text-green-300 rounded-lg animate-fade-in" role="alert">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="mb-4 p-3 bg-red-700 bg-opacity-30 border border-red-500 text-red-300 rounded-lg animate-fade-in" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="mt-4 flex flex-col sm:flex-row gap-4 sm:gap-2">
            <input type="number" name="bid_amount" step="0.01" min="<?= number_format($highest_bid > 0 ? $highest_bid + 1 : $auction['minimum_price'], 2, '.', '') ?>"
                   placeholder="Enter your bid (min: $<?= number_format($highest_bid > 0 ? $highest_bid + 1 : $auction['minimum_price'], 2) ?>)"
                   class="border border-slate-600 rounded-lg px-4 py-3 bg-slate-700 text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 flex-1 transition-colors" 
                   required>
            <button type="submit"
                    class="bg-blue-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200">
=======
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
>>>>>>> a8d1043f7a1709163c59375c9b5b325189b71467
                Place Bid
            </button>
        </form>
    </div>
</div>

<script>
    const countdownElem = document.getElementById("countdown");
    const endTime = new Date("<?= $auction['ends_at'] ?>").getTime();
    const bidForm = document.querySelector("form");

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = endTime - now;

        if (distance <= 0) {
            countdownElem.innerHTML = "Auction Ended";
<<<<<<< HEAD
            if (bidForm) {
                bidForm.style.display = "none";
=======
            const form = document.querySelector("form");
            if (form) {
                form.style.display = "none";
>>>>>>> a8d1043f7a1709163c59375c9b5b325189b71467
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
<<<<<<< HEAD
include './lib/layout.php';
=======
include './lib/layout.php';
?>
>>>>>>> a8d1043f7a1709163c59375c9b5b325189b71467
