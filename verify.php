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
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Email Verification - <?= e(APP_NAME) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      font-family: system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Helvetica,Arial,sans-serif;
      padding: 24px;
      max-width: 720px;
      margin: auto;
      text-align: center;
    }
    h2 {
      margin-bottom: 32px;
    }
    .success, .error {
      display: inline-block;
      padding: 20px 40px;
      border-radius: 12px;
      font-size: 18px;
      margin-top: 20px;
    }
    .success {
      background-color: #e6ffed;
      color: #2d7a36;
      border: 1px solid #4CAF50;
      animation: pop 0.5s ease forwards;
    }
    .error {
      background-color: #ffe6e6;
      color: #b30000;
      border: 1px solid #ff4d4d;
    }

    /* Tick mark animation */
    .tick {
      width: 50px;
      height: 25px;
      border-left: 4px solid #4CAF50;
      border-bottom: 4px solid #4CAF50;
      transform: rotate(-45deg) scale(0);
      margin: 0 auto 16px;
      animation: drawTick 0.5s ease forwards;
    }

    @keyframes drawTick {
      from { transform: rotate(-45deg) scale(0); opacity: 0; }
      to { transform: rotate(-45deg) scale(1); opacity: 1; }
    }

    @keyframes pop {
      0% { transform: scale(0.8); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }

    a {
      color: #4CAF50;
      text-decoration: none;
      font-weight: bold;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <h2>Email Verification</h2>
  <?php if ($ok): ?>
    <div class="tick"></div>
    <div class="success">Your email has been verified. You can now <a href="login.php">log in</a>.</div>
  <?php else: ?>
    <div class="error">Invalid or expired verification link.</div>
  <?php endif; ?>
</body>
</html>
