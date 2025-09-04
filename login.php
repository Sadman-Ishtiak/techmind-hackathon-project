<?php
session_start();
require_once './config.php'; // DB connection

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, password_hash, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password_hash'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } elseif ($user['role'] === 'store_owner') {
                    header("Location: owner_dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No user found with this email.";
        }
    }
}

$title = "Login - JKKNIU Marketplace";
ob_start();
?>

<div class="max-w-md mx-auto mt-20 p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-6 text-center">Login</h1>

    <?php if($error): ?>
        <p class="text-red-500 mb-4 text-center"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
            <input type="email" name="email" id="email" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        <div>
            <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
            <input type="password" name="password" id="password" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition">
            Login
        </button>
    </form>

    <p class="mt-4 text-center text-gray-600 text-sm">
        Don't have an account? <a href="register.php" class="text-blue-500 hover:underline">Register here</a>
    </p>
</div>

<?php
$content = ob_get_clean();
include './lib/layout.php';
?>
