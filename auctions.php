<?php
session_start(); // needed for auth check
$title = "Auctions - JKKNIU Marketplace";

require_once './config.php';  // $pdo is PDO

$auction_stmt = $pdo->prepare("
    SELECT *
    FROM auctions a
    ORDER BY a.id DESC
    LIMIT 24
");
$auction_stmt->execute();
$auctions = $auction_stmt->fetchAll(PDO::FETCH_ASSOC);
ob_start();
?>
    <h2 class="text-xl font-bold mb-4">Latest Auctions</h2>
    <p>Here are the newest items for auction from students.</p>
    
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
            if ($aimg_res && isset($aimg_res['image'])) {
                $a_image_url = 'data:image/png;base64,' . base64_encode($aimg_res['image']);
            } else {
                $a_image_url = '';
            }

            ?>
            <div class="border rounded shadow p-4 bg-white">
                <?php if ($a_image_url): ?>
                    <img src="<?= htmlspecialchars($a_image_url) ?>" class="w-full h-40 object-cover rounded mb-2">
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
