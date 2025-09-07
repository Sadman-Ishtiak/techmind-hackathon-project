<?php
session_start();
require_once './config.php';
require_once './lib/helpers.php';
require_once './lib/smtp.php';

$errors = [];
$sent = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    // Validation
    if ($name === '') $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $password2) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        // Check if email already exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already registered.';
        } else {
            // Hash password and generate verification token
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(32));

            // Insert user with default role 'user' and email verification token
            $insert_stmt = $pdo->prepare('INSERT INTO users (name,email,password_hash,role,verification_token,email_verified) VALUES (?,?,?,?,?,0)');
            $insert_stmt->execute([$name, $email, $hash, 'user', $token]);

            // Send verification email
            $verify_link = BASE_URL . '/verify.php?token=' . urlencode($token) . '&email=' . urlencode($email);
            $html = verification_email_html($name, $verify_link);

            try {
                smtp_send_html($email, $name, 'Verify your email', $html, $SMTP_CONFIG);
                $sent = true;
            } catch (Exception $ex) {
                $errors[] = 'Failed to send email: ' . e($ex->getMessage());
            }
        }
    }
}

$title = "Register - " . APP_NAME;
ob_start();
?>

<div class="max-w-md mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-slate-800 rounded-xl shadow-lg border border-slate-700 px-8 py-6">
        <h1 class="text-3xl font-bold mb-6 text-center text-blue-400">Register</h1>

        <?php if ($sent): ?>
            <div class="bg-emerald-900 text-emerald-300 px-4 py-3 rounded-lg mb-4 border border-emerald-700">
                We've sent a verification link to <strong><?= e($_POST['email']) ?></strong>. Please check your inbox.
            </div>
        <?php endif; ?>

        <?php if ($errors): ?>
            <div class="bg-red-900 text-red-400 px-4 py-3 rounded-lg mb-4 border border-red-700">
                <ul class="list-disc pl-5">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label class="block text-slate-400 font-medium mb-2">Full Name</label>
                <input type="text" name="name" value="<?= e($_POST['name'] ?? '') ?>" 
                       class="border border-slate-700 rounded-lg w-full px-4 py-3 bg-slate-900 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200" required>
            </div>

            <div>
                <label class="block text-slate-400 font-medium mb-2">Email</label>
                <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" 
                       class="border border-slate-700 rounded-lg w-full px-4 py-3 bg-slate-900 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200" required>
            </div>

            <div>
                <label class="block text-slate-400 font-medium mb-2">Password (min 8)</label>
                <input type="password" name="password" 
                       class="border border-slate-700 rounded-lg w-full px-4 py-3 bg-slate-900 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200" required>
            </div>

            <div>
                <label class="block text-slate-400 font-medium mb-2">Confirm Password</label>
                <input type="password" name="password2" 
                       class="border border-slate-700 rounded-lg w-full px-4 py-3 bg-slate-900 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200" required>
            </div>

            <button type="submit" 
                    class="bg-blue-600 text-white w-full px-4 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Register
            </button>
        </form>

        <p class="mt-4 text-sm text-slate-400 text-center">
            Already have an account? <a href="login.php" class="underline text-blue-400 hover:text-blue-300">Login</a>
        </p>
    </div>
</div>

<?php
$content = ob_get_clean();
include './lib/layout.php';
?>
