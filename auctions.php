<?php
session_start(); // needed for auth check
$title = "Auctions - Nazrul Bazar";

require_once './config.php'; 

// Use a prepared statement to fetch auctions
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
<<<<<<< HEAD
<div class="container mx-auto p-4 md:p-8">
    <h2 class="text-3xl font-extrabold mb-8 text-blue-400 animate-fade-in-down">Latest Auctions</h2>
    <p class="text-slate-300 mb-6">Here are the newest items for auction from students. Dive in and place your bid!</p>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php if ($auctions): ?>
            <?php foreach ($auctions as $a): ?>
                <?php
                // Use a prepared statement to fetch a single image for the auction
                $aimg_stmt = $pdo->prepare("
                    SELECT i.image 
                    FROM images i
                    JOIN auction_images ai ON ai.image_id = i.id
                    WHERE ai.auction_id = :aid 
                    LIMIT 1
                ");
                $aimg_stmt->execute(['aid' => $a['id']]);
                $aimg_res = $aimg_stmt->fetch(PDO::FETCH_ASSOC);

                if ($aimg_res && isset($aimg_res['image'])) {
                    $a_image_url = 'data:image/jpeg;base64,' . base64_encode($aimg_res['image']);
                } else {
                    $a_image_url = './assets/placeholder.jpg';
                }
                ?>
                <div class="bg-slate-800 rounded-xl shadow-lg p-4 transition-all duration-300 hover:shadow-2xl hover:scale-105">
                    <?php if ($a_image_url): ?>
                        <img src="<?= htmlspecialchars($a_image_url) ?>" class="w-full h-40 object-cover rounded-lg mb-4 border border-slate-700">
                    <?php else: ?>
                        <div class="w-full h-40 bg-slate-700 flex items-center justify-center rounded-lg mb-4">
                            <span class="text-slate-500">No Image</span>
                        </div>
                    <?php endif; ?>
                    <h4 class="font-bold text-xl text-blue-400 mb-1 truncate"><?= htmlspecialchars($a['product_name']) ?></h4>
                    <p class="text-slate-400 text-sm mb-2">Current Bid: <span class="text-green-400 font-bold">$<?= htmlspecialchars($a['current_bid'] ?? $a['minimum_price']) ?></span></p>
                    <p class="text-slate-400 text-sm mb-4">Time Left: <span class="text-red-400 font-bold">~2d 15h</span></p>

                    <a href="auction_details.php?id=<?= $a['id'] ?>" class="block w-full text-center bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        View Auction
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-slate-400 col-span-full text-center py-10">No auctions available at the moment. Please check back later!</p>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();

include './lib/layout.php';
=======
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
>>>>>>> a8d1043f7a1709163c59375c9b5b325189b71467
