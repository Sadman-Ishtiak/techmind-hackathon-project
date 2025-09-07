<?php
session_start();
require_once './config.php'; // $conn PDO

$title = "Edit - Nazrul Bazar";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email address.";
    } else {
        try {
            if (!empty($password)) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, password_hash = :password WHERE id = :id");
                $stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'password' => $password_hash,
                    'id' => $user_id
                ]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
                $stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'id' => $user_id
                ]);
            }
            $message = "Profile updated successfully.";
        } catch (PDOException $e) {
            $message = "Error updating profile: " . $e->getMessage();
        }
    }
}

// Fetch current user data to prefill form
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

ob_start();
?>

<div class="max-w-md mx-auto mt-10 p-6 bg-slate-800 rounded-xl shadow-lg border border-slate-700 text-slate-200">
    <h2 class="text-2xl font-bold mb-6 text-blue-400">Edit Profile</h2>
    <?php if ($message): ?>
        <?php $message_type = (strpos($message, 'successfully') !== false) ? 'success' : 'error'; ?>
        <p class="
            <?= $message_type === 'success' ? 'bg-green-800 border-green-700 text-green-200' : 'bg-red-800 border-red-700 text-red-200' ?>
            px-4 py-3 rounded-lg relative mb-6">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST" class="space-y-6">
        <div>
            <label class="block text-slate-400 font-medium mb-1" for="name">Name</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required
                class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
        </div>

        <div>
            <label class="block text-slate-400 font-medium mb-1" for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required
                class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
        </div>

        <div>
            <label class="block text-slate-400 font-medium mb-1" for="password">New Password (leave blank to keep current)</label>
            <input type="password" name="password" id="password"
                class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
        </div>

        <button type="submit"
            class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Update Profile
        </button>
    </form>
</div>

<?php
$content = ob_get_clean();
include './lib/layout.php';
