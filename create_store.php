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

// Check if the user already has a store
$stmt = $conn->prepare("SELECT * FROM stores WHERE owner_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$store_exists = $stmt->get_result()->fetch_assoc();

if ($store_exists) {
    header('Location: owner_dashboard.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $store_name = trim($_POST['store_name'] ?? '');
    
    if (empty($store_name)) {
        $message = "Store name cannot be empty.";
    } else {
        // Insert new store
        $stmt = $conn->prepare("INSERT INTO stores (owner_id, name) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $store_name);
        if ($stmt->execute()) {
            header('Location: owner_dashboard.php');
            exit;
        } else {
            $message = "Error creating store. Please try again.";
        }
    }
}

$title = "Create Store";
ob_start();
?>
<div class="max-w-md mx-auto mt-10 p-6 bg-white border rounded-md shadow-md">
    <h2 class="text-xl font-bold mb-4">Create Your Store</h2>
    <?php if ($message): ?>
        <p class="text-red-600 mb-4"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-gray-700">Store Name</label>
            <input type="text" name="store_name" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500" required>
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Create Store</button>
    </form>
</div>
<?php
$content = ob_get_clean();
include './lib/layout.php';
