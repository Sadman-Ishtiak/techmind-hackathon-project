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
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-center text-gray-900 mb-4">Latest Auctions</h2>
        <p class="text-center text-gray-600 mb-8">Discover the newest items available for auction from our student community.</p>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
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
                    <div class="bg-white rounded-xl shadow-lg p-4 flex flex-col items-center text-center transition-transform duration-300 hover:scale-105">
                        <?php if ($a_image_url): ?>
                            <img src="<?= htmlspecialchars($a_image_url) ?>" class="w-full h-40 object-cover rounded-lg mb-4 shadow-sm">
                        <?php else: ?>
                            <div class="w-full h-40 bg-gray-200 rounded-lg mb-4 flex items-center justify-center text-gray-500">No Image</div>
                        <?php endif; ?>
                        <h4 class="font-bold text-lg text-gray-800 mb-2 truncate w-full"><?= htmlspecialchars($a['product_name']) ?></h4>
                        <p class="text-green-600 font-extrabold text-xl mb-4">Starts at: $<?= htmlspecialchars($a['minimum_price']) ?></p>
                        <a href="auction_details.php?id=<?= $a['id'] ?>" class="inline-block w-full bg-indigo-600 text-white px-4 py-2 rounded-lg font-semibold transition-transform hover:scale-105 hover:bg-indigo-700">View Auction</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-500 col-span-full text-center">No auctions available at the moment. Check back later!</p>
            <?php endif; ?>
        </div>
    </div>
<?php
$content = ob_get_clean();

include './lib/layout.php';
?>
