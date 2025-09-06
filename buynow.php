<?php
session_start(); // needed for auth check
$title = "Products - JKKNIU Marketplace";

require_once './config.php';  // $pdo is PDO

// Fetch latest products ordered by stock count
$product_stmt = $pdo->prepare("
    SELECT *
    FROM products p
    ORDER BY p.stock DESC
    LIMIT 12
");
$product_stmt->execute();
$products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);


ob_start();
?>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-center text-gray-900 mb-4">The Buy Now Page</h2>
        <p class="text-center text-gray-600 mb-8">Here are the newest and hottest items from our stores.</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
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
                    // Your 'image' column already stores the base64 string, no need to encode again.
                    $image_data = $img_res['image'] ?? ''; // Get the base64 string or an empty string
                    $image_url = $image_data ? 'data:image/png;base64,' . $image_data : ''; // Prefix with data URI if image data exists
                    ?>
                    <div class="bg-white rounded-xl shadow-lg p-4 flex flex-col items-center text-center transition-transform duration-300 hover:scale-105">
                        <?php if ($image_url): ?>
                            <img src="<?= htmlspecialchars($image_url) ?>" class="w-full h-40 object-cover rounded-lg mb-4 shadow-sm" alt="<?= htmlspecialchars($p['name']) ?>">
                        <?php else: ?>
                            <div class="w-full h-40 bg-gray-200 rounded-lg mb-4 flex items-center justify-center text-gray-500">No Image</div>
                        <?php endif; ?>
                        <h4 class="font-bold text-lg text-gray-800 mb-2 truncate w-full"><?= htmlspecialchars($p['name']) ?></h4>
                        <p class="text-green-600 font-extrabold text-xl mb-4">Price: $<?= htmlspecialchars($p['price']) ?></p>
                        <a href="product_details.php?id=<?= $p['id'] ?>" class="inline-block w-full bg-indigo-600 text-white px-4 py-2 rounded-lg font-semibold transition-transform hover:scale-105 hover:bg-indigo-700">View Details</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-500 col-span-full text-center">No products available. Check back later!</p>
            <?php endif; ?>
        </div>
    </div>
<?php
$content = ob_get_clean();

include './lib/layout.php';
?>
