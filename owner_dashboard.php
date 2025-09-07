<?php
session_start();
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'store_owner') {
    header('Location: login.php');
    exit;
}

require 'config.php'; // $conn is PDO

$user_id = $_SESSION['user_id'];

// Fetch the store for this owner
$stmt = $pdo->prepare("SELECT * FROM stores WHERE owner_id = :owner_id");
$stmt->execute(['owner_id' => $user_id]);
$store = $stmt->fetch(PDO::FETCH_ASSOC);

// If no store exists, prompt to create one
if (!$store) {
    $content = '<div class="p-6 bg-slate-800 rounded-xl shadow-lg border border-slate-700 mb-6 text-center text-slate-200">
                    <p class="mb-4">You do not have a store yet.</p>
                    <a href="./create_store.php" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">Create Store</a>
                </div>';
} else {
    $store_id = $store['id'];

    // Handle product deletion
    if (isset($_GET['delete_product_id'])) {
        $delete_id = intval($_GET['delete_product_id']);
        // Delete product and related images
        $deleteStmt = $pdo->prepare("
            DELETE FROM products 
            WHERE id = :pid AND store_id = :sid
        ");
        $deleteStmt->execute(['pid' => $delete_id, 'sid' => $store_id]);
        header("Location: owner_dashboard.php");
        exit;
    }

    // Fetch products for this store with main image
    $stmt = $pdo->prepare("
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
    echo "<div class='flex justify-between items-center mb-6'>
            <h2 class='text-3xl font-extrabold text-blue-400'>My Store: ".htmlspecialchars($store['name'])."</h2>
            <a href=\"./product_form.php\" class='bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200'>Add New Product</a>
          </div>";

    if ($products) {
        echo "<div class='grid grid-cols-1 md:grid-cols-3 gap-6'>";
        foreach ($products as $row) {
            $img_src = $row['main_image'] ? "data:image/png;base64," . $row['main_image'] : "";
            echo "<div class='bg-slate-800 rounded-xl shadow-lg p-6 flex flex-col transition-transform duration-300 hover:scale-105 border border-slate-700'>
                    ".($img_src ? "<img src='$img_src' class='w-full h-40 object-cover rounded-lg mb-4 border border-slate-700'>" : "<div class='w-full h-40 bg-slate-700 rounded-lg mb-4 flex items-center justify-center text-slate-400'>No Image</div>")."
                    <h3 class='font-semibold text-xl text-blue-400 mb-1'>".htmlspecialchars($row['name'])."</h3>
                    <p class='text-green-400 font-bold text-lg mb-1'>Price: $".$row['price']."</p>
                    <p class='text-slate-400 mb-2'>Stock: ".$row['stock']."</p>
                    <p class='text-slate-400 mb-2'>Category: ".htmlspecialchars($row['category_name'])."</p>
                    <div class='flex justify-between mt-auto'>
                        <a href='./product_form.php?id=".$row['id']."' class='text-blue-400 hover:underline font-semibold'>Edit</a>
                        <a href='?delete_product_id=".$row['id']."' class='text-red-400 hover:underline font-semibold' onclick='return confirm(\"Are you sure you want to delete this product?\");'>Delete</a>
                    </div>
                </div>";
        }
        echo "</div>";
    } else {
        echo "<p class='mt-4 text-center text-slate-400'>No products found. Click 'Add New Product' to create one.</p>";
    }

    $content = ob_get_clean();
}

$title = "Store Dashboard";
include './lib/layout.php';
