<?php 
session_start();
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'store_owner') {
    header('Location: login.php');
    exit;
}

require 'config.php'; // $conn is PDO

$user_id = $_SESSION['user_id'];

// Fetch the store for this owner
$stmt = $conn->prepare("SELECT * FROM stores WHERE owner_id = :owner_id");
$stmt->execute(['owner_id' => $user_id]);
$store = $stmt->fetch(PDO::FETCH_ASSOC);

// If no store exists, prompt to create one
if (!$store) {
    $content = '<div class="p-4 bg-yellow-100 rounded-md">
                    <p>You do not have a store yet.</p>
                    <a href="./create_store.php" class="mt-2 inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Create Store</a>
                </div>';
} else {
    $store_id = $store['id'];

    // Handle product deletion
    if (isset($_GET['delete_product_id'])) {
        $delete_id = intval($_GET['delete_product_id']);
        // Delete product and related images
        $deleteStmt = $conn->prepare("
            DELETE p, pi FROM products p
            LEFT JOIN product_images pi ON pi.product_id = p.id
            WHERE p.id = :pid AND p.store_id = :sid
        ");
        $deleteStmt->execute(['pid' => $delete_id, 'sid' => $store_id]);
        header("Location: owner_dashboard.php");
        exit;
    }

    // Fetch products for this store with main image
    $stmt = $conn->prepare("
        SELECT p.*, i.image AS main_image
        FROM products p
        LEFT JOIN product_images pi ON pi.product_id = p.id
        LEFT JOIN images i ON i.id = pi.image_id
        WHERE p.store_id = :store_id
        GROUP BY p.id
    ");
    $stmt->execute(['store_id' => $store_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ob_start();
    echo "<div class='flex justify-between items-center mb-4'>
            <h2 class='text-xl font-bold'>My Store: ".htmlspecialchars($store['name'])."</h2>
            <a href=\"./product_form.php\" class='bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700'>Add New Product</a>
          </div>";

    if ($products) {
        echo "<div class='grid grid-cols-1 md:grid-cols-3 gap-4'>";
        foreach ($products as $row) {
            $img_src = $row['main_image'] ? "data:image/png;base64," . $row['main_image'] : "";
            echo "<div class='border p-2 rounded-md'>
                    ".($img_src ? "<img src='$img_src' class='w-full h-40 object-cover rounded-md mb-2'>" : "<div class='w-full h-40 bg-gray-200 mb-2'></div>")."
                    <h3 class='font-semibold'>".htmlspecialchars($row['name'])."</h3>
                    <p>Price: $".$row['price']."</p>
                    <p>Stock: ".$row['stock']."</p>
                    <p>Category: ".htmlspecialchars($row['category_name'])."</p>
                    <div class='flex justify-between mt-2'>
                        <a href='./product_form.php?id=".$row['id']."' class='text-indigo-600 hover:underline'>Edit</a>
                        <a href='?delete_product_id=".$row['id']."' class='text-red-600 hover:underline' onclick='return confirm(\"Are you sure you want to delete this product?\");'>Delete</a>
                    </div>
                </div>";
        }
        echo "</div>";
    } else {
        echo "<p class='mt-4 text-gray-600'>No products found. Click 'Add New Product' to create one.</p>";
    }

    $content = ob_get_clean();
}

$title = "Store Dashboard";
include './lib/layout.php';
