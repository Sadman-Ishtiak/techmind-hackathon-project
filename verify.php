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
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @keyframes drawTick {
      from { transform: rotate(-45deg) scale(0); opacity: 0; }
      to { transform: rotate(-45deg) scale(1); opacity: 1; }
    }

    @keyframes pop {
      0% { transform: scale(0.8); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }
  </style>
</head>
<body class="bg-slate-900 text-white font-sans flex items-center justify-center min-h-screen">
  <div class="bg-slate-800 rounded-xl shadow-lg border border-slate-700 p-8 max-w-lg w-full text-center">
    <h2 class="text-2xl font-bold mb-8 text-blue-400">Email Verification</h2>
    <?php if ($ok): ?>
      <div class="inline-block w-12 h-6 border-l-4 border-b-4 border-green-500 transform rotate-[-45deg] mx-auto mb-4" style="animation: drawTick 0.5s ease forwards;"></div>
      <div class="inline-block bg-green-900 text-green-300 border border-green-700 rounded-xl py-5 px-10 text-lg mt-5" style="animation: pop 0.5s ease forwards;">
        Your email has been verified. You can now <a href="login.php" class="text-blue-400 hover:underline font-bold">log in</a>.
      </div>
    <?php else: ?>
      <div class="inline-block bg-red-900 text-red-300 border border-red-700 rounded-xl py-5 px-10 text-lg mt-5">
        Invalid or expired verification link.
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
