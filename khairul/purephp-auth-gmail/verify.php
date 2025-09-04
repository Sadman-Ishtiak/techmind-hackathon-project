<?php
require __DIR__.'/config.php';
require __DIR__.'/lib/helpers.php';

$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';
$ok = false;

if ($email && $token) {
  $stmt = $pdo->prepare('SELECT id, email_verified FROM users WHERE email = ? AND verification_token = ? LIMIT 1');
  $stmt->execute([$email, $token]);
  $user = $stmt->fetch();

  if ($user) {
    if ((int)$user['email_verified'] === 1) {
      $ok = true;
    } else {
      $pdo->prepare('UPDATE users SET email_verified = 1, verification_token = NULL WHERE id = ?')->execute([$user['id']]);
      $ok = true;
    }
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Email Verification - <?= e(APP_NAME) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Helvetica,Arial,sans-serif;padding:24px;max-width:720px;margin:auto">
  <h2>Email Verification</h2>
  <?php if ($ok): ?>
    <p class="success">Your email has been verified. You can now <a href="login.php">log in</a>.</p>
  <?php else: ?>
    <p class="error">Invalid or expired verification link.</p>
  <?php endif; ?>
</body>
</html>
