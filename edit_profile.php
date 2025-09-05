<?php
session_start();
require_once './config.php'; // $conn PDO

$title = "Edit - JKKNIU Marketplace";

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
                $stmt = $conn->prepare("UPDATE users SET name = :name, email = :email, password_hash = :password WHERE id = :id");
                $stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'password' => $password_hash,
                    'id' => $user_id
                ]);
            } else {
                $stmt = $conn->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
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
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

ob_start();
?>

<h2 class="text-2xl font-bold mb-4">Edit Profile</h2>
<?php if ($message): ?>
    <p class="text-green-600 mb-4"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form method="POST" class="max-w-md bg-white p-6 rounded shadow">
    <div class="mb-4">
        <label class="block text-gray-700 mb-1" for="name">Name</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required
               class="w-full border px-3 py-2 rounded">
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 mb-1" for="email">Email</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required
               class="w-full border px-3 py-2 rounded">
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 mb-1" for="password">New Password (leave blank to keep current)</label>
        <input type="password" name="password" id="password" class="w-full border px-3 py-2 rounded">
    </div>

    <button type="submit"
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
        Update Profile
    </button>
</form>

<?php
$content = ob_get_clean();
include './lib/layout.php';
