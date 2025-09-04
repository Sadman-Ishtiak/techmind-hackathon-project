<?php
session_start();
require_once './config.php';

$title = "Register - JKKNIU Marketplace";
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if (!$name) $errors[] = "Name is required.";
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required.";
    if (!$password || strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email already exists.";
        } else {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            // Role is ALWAYS 'user' by default
            $default_role = 'user';

            $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $password_hash, $default_role);

            if ($stmt->execute()) {
                // Log in the user after registration
                $_SESSION['user'] = ['id'=>$stmt->insert_id,'name'=>$name,'email'=>$email];
                $_SESSION['user_role'] = $default_role;
                $success = true;
            } else {
                $errors[] = "Database error: ".$stmt->error;
            }
        }
        $stmt->close();
    }
}

ob_start();
?>

<div class="max-w-md mx-auto py-12">
    <h1 class="text-3xl font-bold mb-6">Register</h1>

    <?php if ($success): ?>
        <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-4">
            Registration successful! <a href="index.php" class="underline">Go to Home</a>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 text-red-800 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-white shadow-md rounded px-8 py-6">
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Full Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" 
                   class="border border-gray-300 rounded w-full px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                   class="border border-gray-300 rounded w-full px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">Password</label>
            <input type="password" name="password" 
                   class="border border-gray-300 rounded w-full px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>

        <button type="submit" 
                class="bg-indigo-600 text-white w-full px-4 py-2 rounded hover:bg-indigo-700 transition-colors">
            Register
        </button>
    </form>

    <p class="mt-4 text-sm text-gray-600">Already have an account? <a href="login.php" class="underline text-indigo-600">Log in</a></p>
</div>

<?php
$content = ob_get_clean();
include './lib/layout.php';
?>
