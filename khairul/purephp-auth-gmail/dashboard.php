<?php
require __DIR__.'/config.php';
require __DIR__.'/lib/helpers.php';
require_auth();
$user = current_user();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard - <?= e(APP_NAME) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Helvetica,Arial,sans-serif;padding:24px;max-width:720px;margin:auto">
  <h2>Hi, <?= e($user['name']) ?> 👋</h2>
  <p>Your email (<?= e($user['email']) ?>) is verified.</p>
  <p><a href="logout.php">Logout</a></p>
</body>
</html>
