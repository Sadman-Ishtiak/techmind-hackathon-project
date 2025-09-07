<?php
session_start();
require_once './config.php'; // $pdo is PDO

$title = "Home - Nazrul Bazar";

// Fetch latest products ordered by stock count
$product_stmt = $pdo->prepare("
    SELECT *
    FROM products p
    ORDER BY p.stock DESC
    LIMIT 12
");
$product_stmt->execute();
$products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch latest auctions ordered by ID for recency
$auction_stmt = $pdo->prepare("
    SELECT *
    FROM auctions a
    ORDER BY a.id DESC
    LIMIT 12
");
$auction_stmt->execute();
$auctions = $auction_stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>
<div class="max-w-7xl mx-auto py-8 px-4 text-slate-200">
    <h2 class="text-3xl font-extrabold mb-6 text-blue-400">Latest Products</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <?php if ($products): ?>
            <?php foreach ($products as $p): ?>
                <?php
                $img_stmt = $pdo->prepare("
                    SELECT i.image
                    FROM images i
                    JOIN product_images pi ON pi.image_id = i.id
                    WHERE pi.product_id = :pid LIMIT 1
                ");
                $img_stmt->execute(['pid' => $p['id']]);
                $img_res = $img_stmt->fetch(PDO::FETCH_ASSOC);

                // CORRECTED: Use the base64 string directly from the database
                $image_data = $img_res['image'] ?? '';
                $image_url = $image_data ? 'data:image/png;base64,' . $image_data : '';
                ?>
                <div class="bg-slate-800 rounded-xl shadow-lg p-6 flex flex-col transition-transform duration-300 hover:scale-105 border border-slate-700">
                    <?php if ($image_url): ?>
                        <img src="<?= htmlspecialchars($image_url) ?>" class="w-full h-40 object-cover rounded-lg mb-4 border border-slate-700" alt="<?= htmlspecialchars($p['name']) ?>">
                    <?php else: ?>
                        <div class="w-full h-40 bg-slate-700 rounded-lg mb-4 flex items-center justify-center text-slate-400">No Image</div>
                    <?php endif; ?>
                    <h4 class="font-semibold text-xl text-blue-400 mb-1"><?= htmlspecialchars($p['name']) ?></h4>
                    <p class="text-green-400 font-bold text-lg mb-2">Price: $<?= htmlspecialchars($p['price']) ?></p>
                    <a href="product_details.php?id=<?= $p['id'] ?>" class="inline-block mt-auto text-blue-400 hover:underline font-semibold">View Details</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-slate-400 col-span-3 text-center">No products available.</p>
        <?php endif; ?>
    </div>

    <h2 class="text-3xl font-extrabold mb-6 text-blue-400">Latest Auctions</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php if ($auctions): ?>
            <?php foreach ($auctions as $a): ?>
                <?php
                $aimg_stmt = $pdo->prepare("
                    SELECT i.image
                    FROM images i
                    JOIN auction_images ai ON ai.image_id = i.id
                    WHERE ai.auction_id = :aid LIMIT 1
                ");
                $aimg_stmt->execute(['aid' => $a['id']]);
                $aimg_res = $aimg_stmt->fetch(PDO::FETCH_ASSOC);

                // CORRECTED: Use the base64 string directly from the database
                $a_image_data = $aimg_res['image'] ?? '';
                $a_image_url = $a_image_data ? 'data:image/png;base64,' . $a_image_data : '';

                ?>
                <div class="bg-slate-800 rounded-xl shadow-lg p-6 flex flex-col transition-transform duration-300 hover:scale-105 border border-slate-700">
                    <?php if ($a_image_url): ?>
                        <img src="<?= htmlspecialchars($a_image_url) ?>" class="w-full h-40 object-cover rounded-lg mb-4 border border-slate-700" alt="<?= htmlspecialchars($a['product_name']) ?>">
                    <?php else: ?>
                        <div class="w-full h-40 bg-slate-700 rounded-lg mb-4 flex items-center justify-center text-slate-400">No Image</div>
                    <?php endif; ?>
                    <h4 class="font-semibold text-xl text-blue-400 mb-1"><?= htmlspecialchars($a['product_name']) ?></h4>
                    <p class="text-green-400 font-bold text-lg mb-2">Starting at: $<?= htmlspecialchars($a['minimum_price']) ?></p>
                    <a href="auction_details.php?id=<?= $a['id'] ?>" class="inline-block mt-auto text-blue-400 hover:underline font-semibold">View Auction</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-slate-400 col-span-3 text-center">No auctions available.</p>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
include './lib/layout.php';
