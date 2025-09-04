<?php
require __DIR__.'/config.php';
require __DIR__.'/lib/helpers.php';
require __DIR__.'/lib/smtp.php';

$errors = [];
$sent = false;

if (is_post()) {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($name === '') $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $password2) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(32));
            $pdo->prepare('INSERT INTO users (name,email,password_hash,verification_token) VALUES (?,?,?,?)')
                ->execute([$name, $email, $hash, $token]);

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
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Register - <?= e(APP_NAME) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, Ubuntu, Helvetica, Arial, sans-serif;
      background: #f9f9f9;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
    }
    .container {
      background: #fff;
      padding: 32px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }
    h2 {
      text-align: center;
      margin-bottom: 24px;
      color: #333;
    }
    form label {
      display: block;
      margin-bottom: 12px;
      font-weight: 500;
      color: #555;
    }
    form input {
      width: 100%;
      padding: 10px 12px;
      margin-top: 4px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
      box-sizing: border-box;
    }
    button {
      width: 100%;
      padding: 12px;
      margin-top: 16px;
      background: #4CAF50;
      color: white;
      font-size: 16px;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s;
    }
    button:hover {
      background: #45a049;
    }
    .success {
      background: #e6ffed;
      border: 1px solid #4CAF50;
      color: #2d7a36;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 16px;
    }
    .error {
      background: #ffe6e6;
      border: 1px solid #ff4d4d;
      color: #b30000;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 16px;
    }
    .error ul {
      padding-left: 20px;
      margin: 0;
    }
    .footer {
      text-align: center;
      margin-top: 16px;
      font-size: 14px;
      color: #666;
    }
    .footer a {
      color: #4CAF50;
      text-decoration: none;
    }
    .footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Create Account</h2>

    <?php if ($sent): ?>
      <div class="success">We've sent a verification link to <strong><?= e($_POST['email']) ?></strong>. Please check your inbox.</div>
    <?php endif; ?>

    <?php if ($errors): ?>
      <div class="error">
        <ul><?php foreach ($errors as $er) echo '<li>'.e($er).'</li>'; ?></ul>
      </div>
    <?php endif; ?>

    <form method="post" action="">
      <?= csrf_field() ?>
      <label>Name
        <input type="text" name="name" value="<?= e($_POST['name'] ?? '') ?>" required>
      </label>
      <label>Email
        <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>
      </label>
      <label>Password (min 8)
        <input type="password" name="password" required>
      </label>
      <label>Confirm Password
        <input type="password" name="password2" required>
      </label>
      <button type="submit">Register</button>
    </form>

    <div class="footer">
      Already have an account? <a href="login.php">Login</a>
    </div>
  </div>
</body>
</html>
