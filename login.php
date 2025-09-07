<?php
session_start();
require_once './config.php';
require_once './lib/helpers.php';

if(isset($_SESSION['user_id'])) {
    // User is already logged in, redirect based on role
    if ($_SESSION['user_role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } elseif ($_SESSION['user_role'] === 'store_owner') {
        header("Location: owner_dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$error = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $pdo->prepare("SELECT id, name, password_hash, role, email_verified FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = "Invalid email or password.";
        } elseif ((int)$user['email_verified'] !== 1) {
            $error = "Please verify your email first.";
        } else {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // Role-based redirection
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($user['role'] === 'store_owner') {
                header("Location: owner_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        }
    }
}

$title = "Login - Nazrul Bazar";
ob_start();
?>

<div class="max-w-md mx-auto mt-20 p-6 bg-slate-800 rounded-xl shadow-lg border border-slate-700 text-slate-200">
    <h1 class="text-2xl font-bold mb-6 text-center text-blue-400">Login</h1>

    <?php if($error): ?>
        <p class="bg-red-800 border border-red-700 text-red-200 px-4 py-3 rounded-lg relative mb-6 text-center">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST" class="space-y-6">
        <?= csrf_field() ?>
        <div>
            <label for="email" class="block text-slate-400 font-semibold mb-2">Email</label>
            <input type="email" name="email" id="email" 
                class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200" 
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>

        <div>
            <label for="password" class="block text-slate-400 font-semibold mb-2">Password</label>
            <input type="password" name="password" id="password" 
                class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200" required>
        </div>

        <button type="submit" 
            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Login
        </button>
    </form>

    <p class="mt-6 text-center text-slate-400">
        Don't have an account? <a href="register.php" class="text-blue-400 hover:underline">Register here</a>
    </p>
</div>

<?php
$content = ob_get_clean();
include './lib/layout.php';
