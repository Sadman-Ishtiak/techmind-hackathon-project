<?php
session_start();
require 'config.php'; // $conn PDO

// Check login and role
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'store_owner') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";
$product_id = $_GET['id'] ?? null;

// Check if the store exists for this owner
$stmt = $conn->prepare("SELECT * FROM stores WHERE owner_id = :owner_id");
$stmt->execute(['owner_id' => $user_id]);
$store = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$store) {
    header('Location: create_store.php');
    exit;
}

// Fetch categories and subcategories
$categories = $conn->query("SELECT name FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$subcategories = $conn->query("SELECT name, category_name FROM subcategories")->fetchAll(PDO::FETCH_ASSOC);

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
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id AND store_id = :store_id");
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

    if (empty($name) || $price <= 0 || empty($category_name) || empty($subcategory_name)) {
        $message = "Please fill all required fields correctly.";
    } else {
        if ($product_id) {
            // UPDATE existing product
            $stmt = $conn->prepare("UPDATE products 
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
            $stmt = $conn->prepare("INSERT INTO products (store_id, name, price, description, category_name, subcategory_name, stock)
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
            $product_id = $conn->lastInsertId();
        }

        // Handle image uploads
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $tmp_name) {
                $img_data = file_get_contents($tmp_name);
                $img_base64 = base64_encode($img_data);

                $stmt_img = $conn->prepare("INSERT INTO images (image) VALUES (:image)");
                $stmt_img->execute(['image' => $img_base64]);
                $image_id = $conn->lastInsertId();

                $stmt_link = $conn->prepare("INSERT INTO product_images (product_id, image_id) VALUES (:product_id, :image_id)");
                $stmt_link->execute(['product_id' => $product_id, 'image_id' => $image_id]);
            }
        }

        header('Location: owner_dashboard.php');
        exit;
    }
}

$title = $product_id ? "Edit Product" : "Add Product";
ob_start();
?>

<div class="max-w-2xl mx-auto mt-10 p-6 bg-white border rounded-md shadow-md">
    <h2 class="text-xl font-bold mb-4"><?= $product_id ? 'Edit Product' : 'Add New Product' ?></h2>
    <?php if ($message): ?>
        <p class="text-red-600 mb-4"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="space-y-4">
        <div>
            <label class="block text-gray-700">Product Name</label>
            <input type="text" name="name" class="w-full border border-gray-300 rounded px-3 py-2" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>
        <div>
            <label class="block text-gray-700">Price</label>
            <input type="number" step="0.01" name="price" class="w-full border border-gray-300 rounded px-3 py-2" value="<?= htmlspecialchars($product['price']) ?>" required>
        </div>
        <div>
            <label class="block text-gray-700">Description</label>
            <textarea name="description" class="w-full border border-gray-300 rounded px-3 py-2"><?= htmlspecialchars($product['description']) ?></textarea>
        </div>
        <div>
            <label class="block text-gray-700">Category</label>
            <select name="category" id="category" class="w-full border border-gray-300 rounded px-3 py-2" required>
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['name']) ?>" <?= $product['category_name'] === $cat['name'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-gray-700">Subcategory</label>
            <select name="subcategory" id="subcategory" class="w-full border border-gray-300 rounded px-3 py-2" required>
                <option value="">-- Select Subcategory --</option>
            </select>
        </div>
        <div>
            <label class="block text-gray-700">Stock</label>
            <input type="number" name="stock" class="w-full border border-gray-300 rounded px-3 py-2" value="<?= htmlspecialchars($product['stock']) ?>" required>
        </div>
        <div>
            <label class="block text-gray-700">Product Images</label>
            <input type="file" name="images[]" multiple accept="image/*">
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"><?= $product_id ? 'Update Product' : 'Add Product' ?></button>
    </form>
</div>

<script>
    const categorySelect = document.getElementById('category');
    const subcategorySelect = document.getElementById('subcategory');
    const subcategories = <?= json_encode($subcategories) ?>;

    function updateSubcategories() {
        const cat = categorySelect.value;
        subcategorySelect.innerHTML = '<option value="">-- Select Subcategory --</option>';
        subcategories.forEach(sc => {
            if(sc.category_name === cat){
                const opt = document.createElement('option');
                opt.value = sc.name;
                opt.text = sc.name;
                if("<?= addslashes($product['subcategory_name']) ?>" === sc.name) opt.selected = true;
                subcategorySelect.add(opt);
            }
        });
    }

    categorySelect.addEventListener('change', updateSubcategories);
    updateSubcategories(); // initial load
</script>

<?php
$content = ob_get_clean();
include './lib/layout.php';
