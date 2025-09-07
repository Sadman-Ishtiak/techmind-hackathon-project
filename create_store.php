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
<div class="max-w-md mx-auto mt-10 p-6 bg-slate-800 rounded-xl shadow-lg border border-slate-700 text-slate-200">
    <h2 class="text-2xl font-bold mb-6 text-blue-400">Create Your Store</h2>
    <?php if ($message): ?>
        <p class="bg-red-800 border border-red-700 text-red-200 px-4 py-3 rounded-lg relative mb-6">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>
    <form method="POST" class="space-y-6">
        <div>
            <label for="store_name" class="block text-slate-400 font-medium mb-1">Store Name</label>
            <input type="text" id="store_name" name="store_name" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200" required>
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Create Store
        </button>
    </form>
</div>
<?php
$content = ob_get_clean();
include './lib/layout.php';
