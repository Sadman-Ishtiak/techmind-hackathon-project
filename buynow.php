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
    <h2 class="text-xl font-bold mb-4">The buy now page</h2>
    <p>Here are the newest and hot items from stores.</p>
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
            // Your 'image' column already stores the base64 string, no need to encode again.
            $image_data = $img_res['image'] ?? ''; // Get the base64 string or an empty string
            $image_url = $image_data ? 'data:image/png;base64,' . $image_data : ''; // Prefix with data URI if image data exists
            ?>
            <div class="border rounded shadow p-4 bg-white">
                <?php if ($image_url): ?>
                    <img src="<?= htmlspecialchars($image_url) ?>" class="w-full h-40 object-cover rounded mb-2" alt="<?= htmlspecialchars($p['name']) ?>">
                <?php else: ?>
                    <div class="w-full h-40 bg-gray-200 rounded mb-2 flex items-center justify-center text-gray-500">No Image</div>
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
<?php
$content = ob_get_clean();

include './lib/layout.php';