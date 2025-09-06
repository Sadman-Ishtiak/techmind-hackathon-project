<?php
session_start();
$conn = mysqli_connect("localhost","root","","Hackathon");
$title = "Settings - JKKNIU Marketplace";
require_once './config.php'; // $pdo PDO
ob_start();
?>

<?php
if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "store_owner") {
    echo "<h2 class='text-2xl font-bold mb-4'>Store Owner Settings</h2>";
    $sql = "SELECT * FROM stores WHERE owner_id = " . $_SESSION['user_id'];
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        print_r($row);
        // Array ( [id] => 3 [owner_id] => 5 [name] => Khairul [approved] => 1 )
        $sql2 = "SELECT * FROM transactions WHERE store_id = " . $row['id'];
        $result2 = mysqli_query($conn, $sql2);
        while ($row2 = mysqli_fetch_assoc($result2)) {
            print_r($row2);
            echo "<hr>";
            // Array ( [id] => 1 [store_id] => 3 [product_id] => 1 [quantity] => 2 [total_price] => 2000 [transaction_date] => 2024-06-15 10:00:00 )
        }
    }
}

?>
<?php
if ($_SESSION['user_role'] === 'user') {
    $user_id = $_SESSION["user_id"];

    // Auction form submission handling
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["auction_submit"])) {
        $item_name = trim($_POST["item_name"]);
        $description = trim($_POST["description"]);
        $minimum_price = floatval($_POST["minimum_price"]);
        $end_date = $_POST["end_date"]; // Date format: yyyy-mm-dd

        if (!empty($item_name) && !empty($minimum_price) && !empty($end_date)) {
            $stmt = $pdo->prepare("
                INSERT INTO auctions (seller_id, product_name, description, minimum_price, ends_at) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$user_id, $item_name, $description, $minimum_price, $end_date . " 23:59:59"]);
            echo "<p class='bg-green-100 text-green-800 p-2 rounded mb-4'>Auction item added successfully!</p>";
        } else {
            echo "<p class='bg-red-100 text-red-800 p-2 rounded mb-4'>Please fill in all required fields.</p>";
        }
    }
    ?>

    <!-- Auction Form -->
    <div class="p-6 border rounded shadow bg-white mb-6">
        <h2 class="text-2xl font-semibold mb-4">Put Item for Auction</h2>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block mb-1 font-medium">Item Name *</label>
                <input type="text" name="item_name" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block mb-1 font-medium">Description</label>
                <textarea name="description" class="w-full border rounded px-3 py-2"></textarea>
            </div>
            <div>
                <label class="block mb-1 font-medium">Minimum Price *</label>
                <input type="number" name="minimum_price" step="0.01" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block mb-1 font-medium">End Date *</label>
                <input type="date" name="end_date" class="w-full border rounded px-3 py-2" required>
            </div>
            <button type="submit" name="auction_submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Submit Auction
            </button>
        </form>
    </div>

    <!-- Auction Items Table -->
    <h3 class="text-xl font-semibold mb-4">My Auction Items</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full border bg-white rounded shadow">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="px-4 py-2 text-left">Item Name</th>
                    <th class="px-4 py-2 text-left">Description</th>
                    <th class="px-4 py-2 text-left">Current Price</th>
                    <th class="px-4 py-2 text-left">End Date</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Time Left</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->prepare("SELECT * FROM auctions WHERE seller_id = ?");
                $stmt->execute([$user_id]);
                $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($auctions) {
                    foreach ($auctions as $auction) {
                        // Determine current price
                        $stmt2 = $pdo->prepare("SELECT MAX(amount) as highest_bid FROM bids WHERE auction_id = ?");
                        $stmt2->execute([$auction['id']]);
                        $bid = $stmt2->fetch(PDO::FETCH_ASSOC);
                        $current_price = $bid && $bid['highest_bid'] ? $bid['highest_bid'] : $auction['minimum_price'];

                        // Determine status
                        $status = (new DateTime() < new DateTime($auction['ends_at'])) ? 'Active' : 'Ended';

                        echo "<tr class='border-b'>
                                <td class='px-4 py-2'>" . htmlspecialchars($auction['product_name']) . "</td>
                                <td class='px-4 py-2'>" . htmlspecialchars($auction['description']) . "</td>
                                <td class='px-4 py-2'>$" . htmlspecialchars($current_price) . "</td>
                                <td class='px-4 py-2'>" . htmlspecialchars($auction['ends_at']) . "</td>
                                <td class='px-4 py-2'>$status</td>
                                <td class='px-4 py-2'><span class='countdown' data-end='" . $auction['ends_at'] . "'></span></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='px-4 py-2 text-center text-gray-500'>No auction items found.</td></tr>";
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

                if (distance < 0) {
                    span.innerHTML = "Expired";
                } else {
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    span.innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s";
                }
            });
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>
<?php } ?>


<?php
$content = ob_get_clean();

include "./lib/layout.php";
?>