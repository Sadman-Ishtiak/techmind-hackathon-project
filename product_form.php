<?php
session_start();
require 'config.php'; // $pdo PDO

// Check login and role
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'store_owner') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";
$product_id = $_GET['id'] ?? null;

// Check if the store exists for this owner
$stmt = $pdo->prepare("SELECT * FROM stores WHERE owner_id = :owner_id");
$stmt->execute(['owner_id' => $user_id]);
$store = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$store) {
    header('Location: create_store.php');
    exit;
}

// Fetch existing categories and subcategories
$categories = $pdo->query("SELECT name FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$subcategories = $pdo->query("SELECT name, category_name FROM subcategories")->fetchAll(PDO::FETCH_ASSOC);

// Initialize product data
$product = [
    'name' => '',
    'price' => '',
    'description' => '',
    'category_name' => '',
    'subcategory_name' => '',
    'stock' => ''
];

// If editing, fetch product info
if ($product_id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id AND store_id = :store_id");
    $stmt->execute(['id' => $product_id, 'store_id' => $store['id']]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$existing) {
        header('Location: owner_dashboard.php');
        exit;
    }
    $product = $existing;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $category_name = $_POST['category'] ?? '';
    $subcategory_name = $_POST['subcategory'] ?? '';
    $stock = intval($_POST['stock'] ?? 0);

    // Handle custom category and subcategory input
    if ($category_name === 'custom_category') {
        $category_name = trim($_POST['custom_category_name'] ?? '');
    }
    if ($subcategory_name === 'custom_subcategory') {
        $subcategory_name = trim($_POST['custom_subcategory_name'] ?? '');
    }

    if (empty($name) || $price <= 0 || empty($category_name) || empty($subcategory_name)) {
        $message = "Please fill all required fields correctly.";
    } else {
        try {
            $pdo->beginTransaction();

            // Insert new category if it doesn't exist
            $stmt_cat = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
            $stmt_cat->execute([$category_name]);

            // Insert new subcategory if it doesn't exist
            $stmt_subcat = $pdo->prepare("INSERT IGNORE INTO subcategories (name, category_name) VALUES (?, ?)");
            $stmt_subcat->execute([$subcategory_name, $category_name]);

            if ($product_id) {
                // UPDATE existing product
                $stmt = $pdo->prepare("UPDATE products 
                                         SET name = :name, price = :price, description = :description,
                                             category_name = :category_name, subcategory_name = :subcategory_name, stock = :stock
                                         WHERE id = :id AND store_id = :store_id");
                $stmt->execute([
                    'name' => $name,
                    'price' => $price,
                    'description' => $description,
                    'category_name' => $category_name,
                    'subcategory_name' => $subcategory_name,
                    'stock' => $stock,
                    'id' => $product_id,
                    'store_id' => $store['id']
                ]);
            } else {
                // INSERT new product
                $stmt = $pdo->prepare("INSERT INTO products (store_id, name, price, description, category_name, subcategory_name, stock)
                                         VALUES (:store_id, :name, :price, :description, :category_name, :subcategory_name, :stock)");
                $stmt->execute([
                    'store_id' => $store['id'],
                    'name' => $name,
                    'price' => $price,
                    'description' => $description,
                    'category_name' => $category_name,
                    'subcategory_name' => $subcategory_name,
                    'stock' => $stock
                ]);
                $product_id = $pdo->lastInsertId();
            }

            // Handle image uploads
            if (!empty($_FILES['images']['name'][0])) {
                foreach ($_FILES['images']['tmp_name'] as $tmp_name) {
                    $img_data = file_get_contents($tmp_name);
                    $img_base64 = base64_encode($img_data);

                    $stmt_img = $pdo->prepare("INSERT INTO images (image) VALUES (:image)");
                    $stmt_img->execute(['image' => $img_base64]);
                    $image_id = $pdo->lastInsertId();

                    $stmt_link = $pdo->prepare("INSERT INTO product_images (product_id, image_id) VALUES (:product_id, :image_id)");
                    $stmt_link->execute(['product_id' => $product_id, 'image_id' => $image_id]);
                }
            }

            $pdo->commit();
            header('Location: owner_dashboard.php');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "An error occurred: " . $e->getMessage();
        }
    }
}

$title = $product_id ? "Edit Product" : "Add Product";
ob_start();
?>
<div class="max-w-2xl mx-auto mt-10 p-6 bg-slate-800 rounded-xl shadow-lg border border-slate-700 text-slate-200">
    <h2 class="text-2xl font-bold mb-4 text-blue-400"><?= $product_id ? 'Edit Product' : 'Add New Product' ?></h2>
    <?php if ($message): ?>
        <p class="text-red-400 mb-4 bg-red-800 p-3 rounded-md border border-red-700"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="space-y-4">
        <div>
            <label class="block text-slate-400">Product Name</label>
            <input type="text" name="name" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>
        <div>
            <label class="block text-slate-400">Price</label>
            <input type="number" step="0.01" name="price" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200" value="<?= htmlspecialchars($product['price']) ?>" required>
        </div>
        <div>
            <label class="block text-slate-400">Description</label>
            <textarea name="description" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200"><?= htmlspecialchars($product['description']) ?></textarea>
        </div>
        <div>
            <label class="block text-slate-400">Category</label>
            <select name="category" id="category" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200" required>
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['name']) ?>" <?= $product['category_name'] === $cat['name'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
                <option value="custom_category">-- Add New Category --</option>
            </select>
        </div>
        <div id="custom_category_div" class="hidden">
            <label class="block text-slate-400">New Category Name</label>
            <input type="text" name="custom_category_name" id="custom_category_name" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
        </div>
        <div>
            <label class="block text-slate-400">Subcategory</label>
            <select name="subcategory" id="subcategory" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200" required>
                <option value="">-- Select Subcategory --</option>
            </select>
        </div>
        <div id="custom_subcategory_div" class="hidden">
            <label class="block text-slate-400">New Subcategory Name</label>
            <input type="text" name="custom_subcategory_name" id="custom_subcategory_name" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
        </div>
        <div>
            <label class="block text-slate-400">Stock</label>
            <input type="number" name="stock" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200" value="<?= htmlspecialchars($product['stock']) ?>" required>
        </div>
        <div>
            <label class="block text-slate-400">Product Images</label>
            <input type="file" name="images[]" multiple accept="image/*" class="w-full text-slate-400">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 w-full"><?= $product_id ? 'Update Product' : 'Add Product' ?></button>
    </form>
</div>

<script>
    const categorySelect = document.getElementById('category');
    const customCategoryDiv = document.getElementById('custom_category_div');
    const customCategoryNameInput = document.getElementById('custom_category_name');
    const subcategorySelect = document.getElementById('subcategory');
    const customSubcategoryDiv = document.getElementById('custom_subcategory_div');
    const customSubcategoryNameInput = document.getElementById('custom_subcategory_name');
    const subcategories = <?= json_encode($subcategories) ?>;
    const existingSubcategory = "<?= addslashes($product['subcategory_name']) ?>";

    function updateSubcategories() {
        const cat = categorySelect.value;
        subcategorySelect.innerHTML = '<option value="">-- Select Subcategory --</option>';

        // Show/hide custom category input
        if (cat === 'custom_category') {
            customCategoryDiv.classList.remove('hidden');
            customCategoryNameInput.required = true;
            // Clear subcategory options and show custom input
            subcategorySelect.innerHTML = '<option value="custom_subcategory">-- Add New Subcategory --</option>';
            subcategorySelect.value = 'custom_subcategory';
            updateCustomSubcategory();
            subcategorySelect.disabled = true;
        } else {
            customCategoryDiv.classList.add('hidden');
            customCategoryNameInput.required = false;
            subcategorySelect.disabled = false;
            
            // Filter and add existing subcategories
            subcategories.forEach(sc => {
                if (sc.category_name === cat) {
                    const opt = document.createElement('option');
                    opt.value = sc.name;
                    opt.text = sc.name;
                    if (existingSubcategory && existingSubcategory === sc.name) {
                        opt.selected = true;
                    }
                    subcategorySelect.add(opt);
                }
            });

            // Add the "Add New" option to the subcategory dropdown
            const customOpt = document.createElement('option');
            customOpt.value = 'custom_subcategory';
            customOpt.text = '-- Add New Subcategory --';
            subcategorySelect.add(customOpt);

            // Trigger subcategory change to check for existing value
            subcategorySelect.dispatchEvent(new Event('change'));
        }
    }

    function updateCustomSubcategory() {
        const subcat = subcategorySelect.value;
        if (subcat === 'custom_subcategory') {
            customSubcategoryDiv.classList.remove('hidden');
            customSubcategoryNameInput.required = true;
        } else {
            customSubcategoryDiv.classList.add('hidden');
            customSubcategoryNameInput.required = false;
        }
    }

    categorySelect.addEventListener('change', updateSubcategories);
    subcategorySelect.addEventListener('change', updateCustomSubcategory);
    
    // Initial load
    updateSubcategories();
    // Re-select the existing subcategory on edit view
    if (existingSubcategory) {
        subcategorySelect.value = existingSubcategory;
    }
</script>

<?php
$content = ob_get_clean();
include './lib/layout.php';
?>
