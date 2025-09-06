<?php
session_start();
require_once './config.php';
$title = "Categories - JKKNIU Marketplace";

// Get current filter parameters
$selected_category = $_GET['category'] ?? null;
$selected_subcategory = $_GET['subcategory'] ?? null;
$selected_subsubcategory = $_GET['subsubcategory'] ?? null;

ob_start();

?>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-4xl sm:text-5xl font-extrabold text-center text-gray-900 mb-2">Browse by Categories</h1>
    <p class="text-center text-gray-600 mb-8 max-w-2xl mx-auto">Find what you're looking for by exploring our curated categories.</p>

    <?php
    // Determine current level and breadcrumbs
    $breadcrumbs = [];
    $current_level_heading = "Top Level Categories";
    $back_link_html = '';

    if ($selected_category) {
        $breadcrumbs[] = ['name' => htmlspecialchars($selected_category), 'link' => '?category=' . urlencode($selected_category)];
        $current_level_heading = "Subcategories in " . htmlspecialchars($selected_category);
        $back_link_html = '<a href="categories.php" class="text-indigo-600 hover:text-indigo-800 font-semibold flex items-center mb-4 transition-colors duration-200"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> Back to Categories</a>';
    }
    if ($selected_subcategory) {
        $breadcrumbs[] = ['name' => htmlspecialchars($selected_subcategory), 'link' => '?category=' . urlencode($selected_category) . '&subcategory=' . urlencode($selected_subcategory)];
        $current_level_heading = "Subsubcategories in " . htmlspecialchars($selected_subcategory);
        $back_link_html = '<a href="categories.php?category=' . urlencode($selected_category) . '" class="text-indigo-600 hover:text-indigo-800 font-semibold flex items-center mb-4 transition-colors duration-200"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> Back to ' . htmlspecialchars($selected_category) . '</a>';
    }
    if ($selected_subsubcategory) {
        $breadcrumbs[] = ['name' => htmlspecialchars($selected_subsubcategory), 'link' => '?category=' . urlencode($selected_category) . '&subcategory=' . urlencode($selected_subcategory) . '&subsubcategory=' . urlencode($selected_subsubcategory)];
        $current_level_heading = "Items in " . htmlspecialchars($selected_subsubcategory);
        $back_link_html = '<a href="categories.php?category=' . urlencode($selected_category) . '&subcategory=' . urlencode($selected_subcategory) . '" class="text-indigo-600 hover:text-indigo-800 font-semibold flex items-center mb-4 transition-colors duration-200"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> Back to ' . htmlspecialchars($selected_subcategory) . '</a>';
    }

    // Display breadcrumbs
    if (!empty($breadcrumbs)):
        echo '<nav class="text-sm font-medium text-gray-500 mb-4">';
        echo '<ol class="list-none p-0 inline-flex items-center">';
        echo '<li class="flex items-center"><a href="categories.php" class="text-indigo-600 hover:text-indigo-800 transition-colors duration-200">Categories</a><svg class="flex-shrink-0 mx-2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg></li>';
        foreach ($breadcrumbs as $index => $crumb) {
            echo '<li class="flex items-center">';
            if ($index < count($breadcrumbs) - 1) {
                echo '<a href="' . $crumb['link'] . '" class="text-indigo-600 hover:text-indigo-800 transition-colors duration-200">' . $crumb['name'] . '</a>';
                echo '<svg class="flex-shrink-0 mx-2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>';
            } else {
                echo '<span class="text-gray-800">' . $crumb['name'] . '</span>';
            }
            echo '</li>';
        }
        echo '</ol>';
        echo '</nav>';
    endif;

    echo $back_link_html; // Display the back link

    echo '<h2 class="text-3xl font-bold mb-6 text-gray-800">' . $current_level_heading . '</h2>';


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
                echo '<a href="' . $link . '" class="block p-6 bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105">';
                echo '<h3 class="text-xl font-bold text-gray-800">' . htmlspecialchars($cat['name']) . '</h3>';
                echo '<p class="text-gray-600 mt-2">Browse items in this category.</p>';
                echo '</a>';
            }
            echo '</div>';
        else:
            echo '<p class="text-gray-600">No categories found.</p>';
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
                echo '<a href="' . $link . '" class="block p-6 bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105">';
                echo '<h3 class="text-xl font-bold text-gray-800">' . htmlspecialchars($sub['name']) . '</h3>';
                echo '<p class="text-gray-600 mt-2">Explore subcategories or items.</p>';
                echo '</a>';
            }
            echo '</div>';
        else:
            echo '<p class="text-gray-600">No subcategories found for ' . htmlspecialchars($selected_category) . '.</p>';
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
                echo '<a href="' . $link . '" class="block p-6 bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105">';
                echo '<h3 class="text-xl font-bold text-gray-800">' . htmlspecialchars($subsub['name']) . '</h3>';
                echo '<p class="text-gray-600 mt-2">View items in this sub-sub category.</p>';
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
 * @param string $subcategory_name
 * @param string|null $subsubcategory_name
 */
function displayProductsAndAuctions(PDO $pdo, string $category_name, string $subcategory_name, ?string $subsubcategory_name) {
    echo '<h3 class="text-2xl font-bold mt-8 mb-4 text-gray-800">Products</h3>';
    $product_sql = "
        SELECT p.*
        FROM products p
        WHERE p.category_name = ? AND p.subcategory_name = ?
    ";
    $params = [$category_name, $subcategory_name];

    if ($subsubcategory_name) {
        $product_sql .= " AND p.subsubcategory_name = ?";
        $params[] = $subsubcategory_name;
    }
    $product_sql .= " ORDER BY p.id DESC LIMIT 20";

    $product_stmt = $pdo->prepare($product_sql);
    $product_stmt->execute($params);
    $products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($products):
        echo '<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">';
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
            $image_url = $image_data ? 'data:image/png;base64,' . $image_data : '';
            ?>
            <div class="bg-white rounded-xl shadow-lg p-4 flex flex-col items-center text-center transition-transform duration-300 hover:scale-105">
                <a href="product_details.php?id=<?= $p['id'] ?>" class="w-full">
                    <?php if ($image_url): ?>
                        <img src="<?= htmlspecialchars($image_url) ?>" class="w-full h-40 object-cover rounded-lg mb-4 shadow-sm" alt="<?= htmlspecialchars($p['name']) ?>">
                    <?php else: ?>
                        <div class="w-full h-40 bg-gray-200 rounded-lg mb-4 flex items-center justify-center text-gray-500">No Image</div>
                    <?php endif; ?>
                </a>
                <h4 class="font-bold text-lg text-gray-800 mb-2 truncate w-full"><a href="product_details.php?id=<?= $p['id'] ?>" class="hover:text-indigo-600"><?= htmlspecialchars($p['name']) ?></a></h4>
                <p class="text-green-600 font-extrabold text-xl mb-4">Price: $<?= htmlspecialchars($p['price']) ?></p>
                <a href="product_details.php?id=<?= $p['id'] ?>" class="inline-block w-full bg-indigo-600 text-white px-4 py-2 rounded-lg font-semibold transition-transform hover:scale-105 hover:bg-indigo-700">View Details</a>
            </div>
        <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-600">No products found in this category.</p>
    <?php endif; ?>

    <h3 class="text-2xl font-bold mt-8 mb-4 text-gray-800">Auctions</h3>
    <?php
    $auction_sql = "
        SELECT a.*
        FROM auctions a
        WHERE a.category_name = ? AND a.subcategory_name = ?
    ";
    $params = [$category_name, $subcategory_name];

    if ($subsubcategory_name) {
        $auction_sql .= " AND a.subsubcategory_name = ?";
        $params[] = $subsubcategory_name;
    }
    $auction_sql .= " ORDER BY a.id DESC LIMIT 20";

    $auction_stmt = $pdo->prepare($auction_sql);
    $auction_stmt->execute($params);
    $auctions = $auction_stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($auctions):
        echo '<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">';
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
            $a_image_url = $a_image_data ? 'data:image/png;base64,' . $a_image_data : '';
            ?>
            <div class="bg-white rounded-xl shadow-lg p-4 flex flex-col items-center text-center transition-transform duration-300 hover:scale-105">
                <a href="auction_details.php?id=<?= $a['id'] ?>" class="w-full">
                    <?php if ($a_image_url): ?>
                        <img src="<?= htmlspecialchars($a_image_url) ?>" class="w-full h-40 object-cover rounded-lg mb-4 shadow-sm" alt="<?= htmlspecialchars($a['product_name']) ?>">
                    <?php else: ?>
                        <div class="w-full h-40 bg-gray-200 rounded-lg mb-4 flex items-center justify-center text-gray-500">No Image</div>
                    <?php endif; ?>
                </a>
                <h4 class="font-bold text-lg text-gray-800 mb-2 truncate w-full"><a href="auction_details.php?id=<?= $a['id'] ?>" class="hover:text-indigo-600"><?= htmlspecialchars($a['product_name']) ?></a></h4>
                <p class="text-green-600 font-extrabold text-xl mb-4">Starting at: $<?= htmlspecialchars($a['minimum_price']) ?></p>
                <a href="auction_details.php?id=<?= $a['id'] ?>" class="inline-block w-full bg-indigo-600 text-white px-4 py-2 rounded-lg font-semibold transition-transform hover:scale-105 hover:bg-indigo-700">View Auction</a>
            </div>
        <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-600">No auctions found in this category.</p>
    <?php endif;
}
?>
