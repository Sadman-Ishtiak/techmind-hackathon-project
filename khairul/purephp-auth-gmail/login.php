<?php
require __DIR__.'/config.php';
require __DIR__.'/lib/helpers.php';

$errors = [];
if (is_post()) {
  verify_csrf();
  $email = strtolower(trim($_POST['email'] ?? ''));
  $password = $_POST['password'] ?? '';

  $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
  $stmt->execute([$email]);
  $user = $stmt->fetch();

  if (!$user || !password_verify($password, $user['password_hash'])) {
    $errors[] = 'Invalid email or password.';
  } elseif ((int)$user['email_verified'] !== 1) {
    $errors[] = 'Please verify your email first.';
  } else {
    $_SESSION['user'] = ['id'=>$user['id'], 'name'=>$user['name'], 'email'=>$user['email']];
    redirect('dashboard.php');
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login - <?= e(APP_NAME) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Helvetica,Arial,sans-serif;padding:24px;max-width:720px;margin:auto">
  <h2>Login</h2>
  <?php if ($errors): ?>
    <div class="error">
      <ul><?php foreach ($errors as $er) echo '<li>'.e($er).'</li>'; ?></ul>
    </div>
  <?php endif; ?>

  <form method="post" action="">
    <?= csrf_field() ?>
    <label>Email<br><input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required></label>
    <label>Password<br><input type="password" name="password" required></label>
    <button type="submit">Login</button>
  </form>
  <p>No account? <a href="register.php">Register</a></p>
</body>
</html>
