<?php
session_start(); // needed for auth check
$title = "Products - Nazrul Bazar";

require_once './config.php'; // $pdo is PDO

// Fetch latest products ordered by stock count
$product_stmt = $pdo->prepare("
    SELECT *
    FROM products p
    ORDER BY p.stock DESC
    LIMIT 24
");
$product_stmt->execute();
$products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>
<<<<<<< HEAD
<div class="container mx-auto p-4 md:p-8">
    <h2 class="text-3xl font-extrabold mb-8 text-blue-400 animate-fade-in-down">Buy Now</h2>
    <p class="text-slate-300 mb-6">Explore the newest and most popular items available from various stores.</p>

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
                
                $image_data = $img_res['image'] ?? ''; 
                $image_url = $image_data ? 'data:image/jpeg;base64,' . $image_data : './assets/placeholder.jpg'; 
                ?>
                <div class="bg-slate-800 rounded-xl shadow-lg p-4 transition-all duration-300 hover:shadow-2xl hover:scale-105">
                    <?php if ($image_url): ?>
                        <img src="<?= htmlspecialchars($image_url) ?>" class="w-full h-40 object-cover rounded-lg mb-4 border border-slate-700" alt="<?= htmlspecialchars($p['name']) ?>">
                    <?php else: ?>
                        <div class="w-full h-40 bg-slate-700 rounded-lg mb-4 flex items-center justify-center text-slate-500">No Image</div>
                    <?php endif; ?>
                    <h4 class="font-bold text-xl text-blue-400 mb-1 truncate"><?= htmlspecialchars($p['name']) ?></h4>
                    <p class="text-green-400 font-bold text-lg mb-4">
                        Price: $<?= number_format($p['price'], 2) ?>
                    </p>
                    
                    <a href="product_details.php?id=<?= $p['id'] ?>" class="block w-full text-center bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        View Details
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-slate-400 col-span-full text-center py-10">No products available for sale at the moment. Please check back later!</p>
        <?php endif; ?>
    </div>
</div>
=======
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
>>>>>>> a8d1043f7a1709163c59375c9b5b325189b71467
<?php
$content = ob_get_clean();

include './lib/layout.php';
?>
