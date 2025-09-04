<?php
session_start();
require 'config.php'; // DB connection

// Check login and role
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'store_owner') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Check if the store exists for this owner
$stmt = $conn->prepare("SELECT * FROM stores WHERE owner_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$store = $stmt->get_result()->fetch_assoc();

if (!$store) {
    header('Location: create_store.php');
    exit;
}

// Fetch categories and subcategories
$categories_result = $conn->query("SELECT name FROM categories");
$categories = $categories_result->fetch_all(MYSQLI_ASSOC);

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
        // Insert product
        $stmt = $conn->prepare("INSERT INTO products (store_id, name, price, description, category_name, subcategory_name, stock) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isdsssi", $store['id'], $name, $price, $description, $category_name, $subcategory_name, $stock);
        if ($stmt->execute()) {
            $product_id = $stmt->insert_id;

            // Handle image uploads
            if (!empty($_FILES['images']['name'][0])) {
                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    $img_data = file_get_contents($tmp_name);
                    $img_base64 = base64_encode($img_data);

                    // Insert into images table
                    $stmt_img = $conn->prepare("INSERT INTO images (image) VALUES (?)");
                    $stmt_img->bind_param("s", $img_base64);
                    $stmt_img->execute();
                    $image_id = $stmt_img->insert_id;

                    // Link to product_images
                    $stmt_link = $conn->prepare("INSERT INTO product_images (product_id, image_id) VALUES (?, ?)");
                    $stmt_link->bind_param("ii", $product_id, $image_id);
                    $stmt_link->execute();
                }
            }

            header('Location: owner_dashboard.php');
            exit;
        } else {
            $message = "Error creating product.";
        }
    }
}

$title = "Add Product";
ob_start();
?>

<div class="max-w-2xl mx-auto mt-10 p-6 bg-white border rounded-md shadow-md">
    <h2 class="text-xl font-bold mb-4">Add New Product</h2>
    <?php if ($message): ?>
        <p class="text-red-600 mb-4"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="space-y-4">
        <div>
            <label class="block text-gray-700">Product Name</label>
            <input type="text" name="name" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block text-gray-700">Price</label>
            <input type="number" step="0.01" name="price" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block text-gray-700">Description</label>
            <textarea name="description" class="w-full border border-gray-300 rounded px-3 py-2"></textarea>
        </div>

        <div>
            <label class="block text-gray-700">Category</label>
            <select name="category" id="category" class="w-full border border-gray-300 rounded px-3 py-2" required>
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
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
            <input type="number" name="stock" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block text-gray-700">Product Images</label>
            <input type="file" name="images[]" multiple accept="image/*">
        </div>

        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Add Product</button>
    </form>
</div>

<script>
    const categorySelect = document.getElementById('category');
    const subcategorySelect = document.getElementById('subcategory');

    categorySelect.addEventListener('change', function() {
        const cat = this.value;
        subcategorySelect.innerHTML = '<option value="">-- Select Subcategory --</option>';

        <?php
        // Fetch subcategories from DB
        $subcats = $conn->query("SELECT name, category_name FROM subcategories")->fetch_all(MYSQLI_ASSOC);
        ?>
        const subcategories = <?php echo json_encode($subcats); ?>;
        subcategories.forEach(sc => {
            if(sc.category_name === cat){
                const opt = document.createElement('option');
                opt.value = sc.name;
                opt.text = sc.name;
                subcategorySelect.add(opt);
            }
        });
    });
</script>

<?php
$content = ob_get_clean();
include './lib/layout.php';
