<?php
session_start();
require_once './config.php';  // DB connection

$title = "Home - JKKNIU Marketplace";

// Fetch latest products ordered by stock count
$product_stmt = $conn->prepare("
    SELECT *
    FROM products p
    ORDER BY p.stock DESC
    LIMIT 6
");
$product_stmt->execute();
$products = $product_stmt->get_result();

// Fetch latest auctions ordered by ID for recency
$auction_stmt = $conn->prepare("
    SELECT *
    FROM auctions a
    ORDER BY a.id DESC
    LIMIT 6
");
$auction_stmt->execute();
$auctions = $auction_stmt->get_result();

ob_start();
?>
    <h2 class="text-xl font-bold mb-4">Latest Products</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <?php if ($products->num_rows > 0): ?>
            <?php while ($p = $products->fetch_assoc()): ?>
                <?php
                // Fetch first image for this product
                $img_stmt = $conn->prepare("SELECT image_url FROM product_images WHERE product_id = ? LIMIT 1");
                $img_stmt->bind_param("i", $p['id']);
                $img_stmt->execute();
                $img_res = $img_stmt->get_result()->fetch_assoc();
                $image_url = $img_res['image_url'] ?? '';
                ?>
                <div class="border rounded shadow p-4 bg-white">
                    <?php if (!empty($image_url)): ?>
                        <img src="<?= htmlspecialchars($image_url) ?>" class="w-full h-40 object-cover rounded mb-2">
                    <?php else: ?>
                        <div class="w-full h-40 bg-gray-200 rounded mb-2"></div>
                    <?php endif; ?>
                    <h4 class="font-semibold text-lg"><?= htmlspecialchars($p['name']) ?></h4>
                    <p class="text-green-600 font-bold">Price: $<?= $p['price'] ?></p>
                    <a href="product_details.php?id=<?= $p['id'] ?>" class="inline-block mt-2 text-indigo-600 hover:underline">View Details</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-500 col-span-3">No products available.</p>
        <?php endif; ?>
    </div>

    <h2 class="text-xl font-bold mb-4">Latest Auctions</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php if ($auctions->num_rows > 0): ?>
            <?php while ($a = $auctions->fetch_assoc()): ?>
                <?php
                // Fetch first image for this auction
                $aimg_stmt = $conn->prepare("SELECT image_url FROM auction_images WHERE auction_id = ? LIMIT 1");
                $aimg_stmt->bind_param("i", $a['id']);
                $aimg_stmt->execute();
                $aimg_res = $aimg_stmt->get_result()->fetch_assoc();
                $a_image_url = $aimg_res['image_url'] ?? '';
                ?>
                <div class="border rounded shadow p-4 bg-white">
                    <?php if (!empty($a_image_url)): ?>
                        <img src="<?= htmlspecialchars($a_image_url) ?>" class="w-full h-40 object-cover rounded mb-2">
                    <?php else: ?>
                        <div class="w-full h-40 bg-gray-200 rounded mb-2"></div>
                    <?php endif; ?>
                    <h4 class="font-semibold text-lg"><?= htmlspecialchars($a['name']) ?></h4>
                    <p class="text-green-600 font-bold">Starting at: $<?= $a['starting_price'] ?></p>
                    <a href="auction_details.php?id=<?= $a['id'] ?>" class="inline-block mt-2 text-indigo-600 hover:underline">View Auction</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-500 col-span-3">No auctions available.</p>
        <?php endif; ?>
    </div>
<?php
$content = ob_get_clean();
include './lib/layout.php';
