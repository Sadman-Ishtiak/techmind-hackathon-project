<?php
session_start();
require_once './config.php';
$title = "Categories - Nazrul Bazar";

// Get current filter parameters
$selected_category = $_GET['category'] ?? null;
$selected_subcategory = $_GET['subcategory'] ?? null;
$selected_subsubcategory = $_GET['subsubcategory'] ?? null;

ob_start();

?>

<div class="max-w-7xl mx-auto py-8 text-slate-200">
    <h1 class="text-3xl font-bold mb-6 text-blue-400">Browse by Categories</h1>

    <?php
    // Determine current level and breadcrumbs
    $breadcrumbs = [];
    $current_level_heading = "Top Level Categories";
    $back_link_html = '';

    if ($selected_category) {
        $breadcrumbs[] = ['name' => htmlspecialchars($selected_category), 'link' => '?category=' . urlencode($selected_category)];
        $current_level_heading = "Subcategories in " . htmlspecialchars($selected_category);
        $back_link_html = '<a href="categories.php" class="text-blue-400 hover:underline flex items-center mb-4"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> Back to Categories</a>';
    }
    if ($selected_subcategory) {
        $breadcrumbs[] = ['name' => htmlspecialchars($selected_subcategory), 'link' => '?category=' . urlencode($selected_category) . '&subcategory=' . urlencode($selected_subcategory)];
        $current_level_heading = "Subsubcategories in " . htmlspecialchars($selected_subcategory);
        $back_link_html = '<a href="categories.php?category=' . urlencode($selected_category) . '" class="text-blue-400 hover:underline flex items-center mb-4"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> Back to ' . htmlspecialchars($selected_category) . '</a>';
    }
    if ($selected_subsubcategory) {
        $breadcrumbs[] = ['name' => htmlspecialchars($selected_subsubcategory), 'link' => '?category=' . urlencode($selected_category) . '&subcategory=' . urlencode($selected_subcategory) . '&subsubcategory=' . urlencode($selected_subsubcategory)];
        $current_level_heading = "Items in " . htmlspecialchars($selected_subsubcategory);
        $back_link_html = '<a href="categories.php?category=' . urlencode($selected_category) . '&subcategory=' . urlencode($selected_subcategory) . '" class="text-blue-400 hover:underline flex items-center mb-4"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> Back to ' . htmlspecialchars($selected_subcategory) . '</a>';
    }

    // Display breadcrumbs
    if (!empty($breadcrumbs)):
        echo '<nav class="text-sm font-medium text-slate-400 mb-4">';
        echo '<ol class="list-none p-0 inline-flex">';
        echo '<li class="flex items-center"><a href="categories.php" class="text-blue-400 hover:text-blue-500">Categories</a><svg class="flex-shrink-0 mx-2 h-5 w-5 text-slate-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg></li>';
        foreach ($breadcrumbs as $index => $crumb) {
            echo '<li class="flex items-center">';
            if ($index < count($breadcrumbs) - 1) {
                echo '<a href="' . $crumb['link'] . '" class="text-blue-400 hover:text-blue-500">' . $crumb['name'] . '</a>';
                echo '<svg class="flex-shrink-0 mx-2 h-5 w-5 text-slate-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>';
            } else {
                echo '<span class="text-slate-200">' . $crumb['name'] . '</span>';
            }
            echo '</li>';
        }
        echo '</ol>';
        echo '</nav>';
    endif;

    echo $back_link_html; // Display the back link

    echo '<h2 class="text-2xl font-bold mb-6">' . $current_level_heading . '</h2>';


    // --- DISPLAY LOGIC ---

    if (!$selected_category) {
        // Show Top-Level Categories
        $stmt = $pdo->prepare("SELECT name FROM categories ORDER BY name");
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($categories):
            echo '<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">';
            foreach ($categories as $cat) {
                $link = 'categories.php?category=' . urlencode($cat['name']);
                echo '<a href="' . $link . '" class="block p-6 bg-slate-800 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-200 border border-slate-700 hover:scale-105">';
                echo '<h3 class="text-xl font-semibold text-blue-400">' . htmlspecialchars($cat['name']) . '</h3>';
                echo '<p class="text-slate-400 mt-2">Browse items in this category.</p>';
                echo '</a>';
            }
            echo '</div>';
        else:
            echo '<p class="text-slate-400">No categories found.</p>';
        endif;

    } elseif ($selected_category && !$selected_subcategory) {
        // Show Subcategories
        $stmt = $pdo->prepare("SELECT name FROM subcategories WHERE category_name = ? ORDER BY name");
        $stmt->execute([$selected_category]);
        $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($subcategories):
            echo '<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">';
            foreach ($subcategories as $sub) {
                $link = 'categories.php?category=' . urlencode($selected_category) . '&subcategory=' . urlencode($sub['name']);
                echo '<a href="' . $link . '" class="block p-6 bg-slate-800 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-200 border border-slate-700 hover:scale-105">';
                echo '<h3 class="text-xl font-semibold text-blue-400">' . htmlspecialchars($sub['name']) . '</h3>';
                echo '<p class="text-slate-400 mt-2">Explore subcategories or items.</p>';
                echo '</a>';
            }
            echo '</div>';
        else:
            // No subcategories, show products/auctions directly under the category
            displayProductsAndAuctions($pdo, $selected_category, null, null);
        endif;

    } elseif ($selected_category && $selected_subcategory && !$selected_subsubcategory) {
        // Show Subsubcategories (if any) or products/auctions if this is the deepest level
        $stmt = $pdo->prepare("SELECT name FROM subsubcategories WHERE subcategory_name = ? ORDER BY name");
        $stmt->execute([$selected_subcategory]);
        $subsubcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($subsubcategories):
            echo '<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">';
            foreach ($subsubcategories as $subsub) {
                $link = 'categories.php?category=' . urlencode($selected_category) . '&subcategory=' . urlencode($selected_subcategory) . '&subsubcategory=' . urlencode($subsub['name']);
                echo '<a href="' . $link . '" class="block p-6 bg-slate-800 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-200 border border-slate-700 hover:scale-105">';
                echo '<h3 class="text-xl font-semibold text-blue-400">' . htmlspecialchars($subsub['name']) . '</h3>';
                echo '<p class="text-slate-400 mt-2">View items in this sub-sub category.</p>';
                echo '</a>';
            }
            echo '</div>';
        else:
            // No subsubcategories, show products/auctions directly under the subcategory
            displayProductsAndAuctions($pdo, $selected_category, $selected_subcategory, null);
        endif;

    } elseif ($selected_category && $selected_subcategory && $selected_subsubcategory) {
        // Show Products and Auctions for the selected subsubcategory
        displayProductsAndAuctions($pdo, $selected_category, $selected_subcategory, $selected_subsubcategory);
    }
    ?>

</div>

<?php
$content = ob_get_clean();
include './lib/layout.php';


/**
 * Helper function to display products and auctions based on selected categories.
 * @param PDO $pdo
 * @param string $category_name
 * @param string|null $subcategory_name
 * @param string|null $subsubcategory_name
 */
function displayProductsAndAuctions(PDO $pdo, string $category_name, ?string $subcategory_name, ?string $subsubcategory_name) {
    echo '<h3 class="text-xl font-semibold mt-8 mb-4">Products</h3>';
    $product_sql = "
        SELECT p.*
        FROM products p
        WHERE p.category_name = ?
    ";
    $params = [$category_name];

    if ($subcategory_name) {
        $product_sql .= " AND p.subcategory_name = ?";
        $params[] = $subcategory_name;
    }
    if ($subsubcategory_name) {
        $product_sql .= " AND p.subsubcategory_name = ?";
        $params[] = $subsubcategory_name;
    }
    $product_sql .= " ORDER BY p.id DESC LIMIT 20";

    $product_stmt = $pdo->prepare($product_sql);
    $product_stmt->execute($params);
    $products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($products):
        echo '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">';
        foreach ($products as $p):
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
            <div class="bg-slate-800 rounded-xl shadow-lg p-4 transition-all duration-300 hover:shadow-2xl hover:scale-105 flex flex-col">
                <a href="product_details.php?id=<?= $p['id'] ?>">
                    <?php if ($image_url): ?>
                        <img src="<?= htmlspecialchars($image_url) ?>" class="w-full h-40 object-cover rounded-lg mb-4 border border-slate-700" alt="<?= htmlspecialchars($p['name']) ?>">
                    <?php else: ?>
                        <div class="w-full h-40 bg-slate-700 rounded-lg mb-4 flex items-center justify-center text-slate-500">No Image</div>
                    <?php endif; ?>
                </a>
                <h4 class="font-bold text-lg mt-auto text-blue-400 truncate"><a href="product_details.php?id=<?= $p['id'] ?>" class="hover:underline"><?= htmlspecialchars($p['name']) ?></a></h4>
                <p class="text-green-400 font-bold text-md">Price: $<?= number_format($p['price'], 2) ?></p>
                <a href="product_details.php?id=<?= $p['id'] ?>" class="inline-block w-full text-center bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 mt-4">View Details</a>
            </div>
        <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-slate-400">No products found in this category.</p>
    <?php endif; ?>

    <h3 class="text-xl font-semibold mt-8 mb-4">Auctions</h3>
    <?php
    $auction_sql = "
        SELECT a.*
        FROM auctions a
        WHERE a.category_name = ?
    ";
    $params = [$category_name];

    if ($subcategory_name) {
        $auction_sql .= " AND a.subcategory_name = ?";
        $params[] = $subcategory_name;
    }
    if ($subsubcategory_name) {
        $auction_sql .= " AND a.subsubcategory_name = ?";
        $params[] = $subsubcategory_name;
    }
    $auction_sql .= " ORDER BY a.id DESC LIMIT 20";

    $auction_stmt = $pdo->prepare($auction_sql);
    $auction_stmt->execute($params);
    $auctions = $auction_stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($auctions):
        echo '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">';
        foreach ($auctions as $a):
            $aimg_stmt = $pdo->prepare("
                SELECT i.image
                FROM images i
                JOIN auction_images ai ON ai.image_id = i.id
                WHERE ai.auction_id = :aid LIMIT 1
            ");
            $aimg_stmt->execute(['aid' => $a['id']]);
            $aimg_res = $aimg_stmt->fetch(PDO::FETCH_ASSOC);
            $a_image_data = $aimg_res['image'] ?? '';
            $a_image_url = $a_image_data ? 'data:image/jpeg;base64,' . $a_image_data : './assets/placeholder.jpg';
            ?>
            <div class="bg-slate-800 rounded-xl shadow-lg p-4 transition-all duration-300 hover:shadow-2xl hover:scale-105 flex flex-col">
                <a href="auction_details.php?id=<?= $a['id'] ?>">
                    <?php if ($a_image_url): ?>
                        <img src="<?= htmlspecialchars($a_image_url) ?>" class="w-full h-40 object-cover rounded-lg mb-4 border border-slate-700" alt="<?= htmlspecialchars($a['product_name']) ?>">
                    <?php else: ?>
                        <div class="w-full h-40 bg-slate-700 rounded-lg mb-4 flex items-center justify-center text-slate-500">No Image</div>
                    <?php endif; ?>
                </a>
                <h4 class="font-bold text-lg mt-auto text-blue-400 truncate"><a href="auction_details.php?id=<?= $a['id'] ?>" class="hover:underline"><?= htmlspecialchars($a['product_name']) ?></a></h4>
                <p class="text-green-400 font-bold text-md">Starting at: $<?= number_format($a['minimum_price'], 2) ?></p>
                <a href="auction_details.php?id=<?= $a['id'] ?>" class="inline-block w-full text-center bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 mt-4">View Auction</a>
            </div>
        <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-slate-400">No auctions found in this category.</p>
    <?php endif;
}
?>