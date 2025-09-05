<?php
session_start();
require_once './config.php';  // $conn is PDO

$title = "Home - JKKNIU Marketplace";

// Fetch latest products ordered by stock count
$product_stmt = $conn->prepare("
    SELECT *
    FROM products p
    ORDER BY p.stock DESC
    LIMIT 6
");
$product_stmt->execute();
$products = $product_stmt->fetchAll(PDO::FETCH_ASSOC); // <-- PDO fetchAll

// Fetch latest auctions ordered by ID for recency
$auction_stmt = $conn->prepare("
    SELECT *
    FROM auctions a
    ORDER BY a.id DESC
    LIMIT 6
");
$auction_stmt->execute();
$auctions = $auction_stmt->fetchAll(PDO::FETCH_ASSOC); // <-- PDO fetchAll

ob_start();
?>
<h2 class="text-xl font-bold mb-4">Latest Products</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <?php if (count($products) > 0): ?>
        <?php foreach ($products as $p): ?>
            <?php
            // Fetch first image for this product
            $img_stmt = $conn->prepare("
                SELECT i.image 
                FROM images i 
                JOIN product_images pi ON pi.image_id = i.id 
                WHERE pi.product_id = :pid LIMIT 1
            ");
            $img_stmt->execute(['pid' => $p['id']]);
            $img_res = $img_stmt->fetch(PDO::FETCH_ASSOC);
            $image_data = $img_res['image'] ?? '';
            $image_url = $image_data ? 'data:image/png;base64,' . $image_data : '';
            ?>
            <div class="border rounded shadow p-4 bg-white">
                <?php if (!empty($image_url)): ?>
                    <img src="<?= htmlspecialchars($image_url) ?>" class="w-full h-40 object-cover rounded mb-2">
                <?php else: ?>
                    <div class="w-full h-40 bg-gray-200 rounded mb-2"></div>
                <?php endif; ?>
                <h4 class="font-semibold text-lg"><?= htmlspecialchars($p['name']) ?></h4>
                <p class="text-green-600 font-bold">Price: $<?= htmlspecialchars($p['price']) ?></p>
                <a href="product_details.php?id=<?= $p['id'] ?>" class="inline-block mt-2 text-indigo-600 hover:underline">View Details</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-gray-500 col-span-3">No products available.</p>
    <?php endif; ?>
</div>

<h2 class="text-xl font-bold mb-4">Latest Auctions</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php if (count($auctions) > 0): ?>
        <?php foreach ($auctions as $a): ?>
            <?php
            // Fetch first image for this auction
            $aimg_stmt = $conn->prepare("
                SELECT i.image 
                FROM images i
                JOIN auction_images ai ON ai.image_id = i.id
                WHERE ai.auction_id = :aid LIMIT 1
            ");
            $aimg_stmt->execute(['aid' => $a['id']]);
            $aimg_res = $aimg_stmt->fetch(PDO::FETCH_ASSOC);
            $a_image_url = $aimg_res['image'] ?? '';
            ?>
            <div class="border rounded shadow p-4 bg-white">
                <?php if (!empty($a_image_url)): ?>
                    <img src="<?= htmlspecialchars('data:image/png;base64,' . $a_image_url) ?>" class="w-full h-40 object-cover rounded mb-2">
                <?php else: ?>
                    <div class="w-full h-40 bg-gray-200 rounded mb-2"></div>
                <?php endif; ?>
                <h4 class="font-semibold text-lg"><?= htmlspecialchars($a['product_name']) ?></h4>
                <p class="text-green-600 font-bold">Starting at: $<?= htmlspecialchars($a['minimum_price']) ?></p>
                <a href="auction_details.php?id=<?= $a['id'] ?>" class="inline-block mt-2 text-indigo-600 hover:underline">View Auction</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-gray-500 col-span-3">No auctions available.</p>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include './lib/layout.php';
