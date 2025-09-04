<?php require __DIR__.'/config.php'; require __DIR__.'/lib/helpers.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?= e(APP_NAME) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Helvetica,Arial,sans-serif;padding:24px;max-width:720px;margin:auto}
    nav a{margin-right:12px}
    form{display:grid;gap:10px;max-width:420px}
    input,button{padding:10px;font-size:16px}
    .card{border:1px solid #ddd;border-radius:12px;padding:18px;margin-top:16px}
    .success{color:green}.error{color:#b00020}
  </style>
</head>
<body>
<nav>
  <?php if (current_user()): ?>
    <a href="dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>
  <?php else: ?>
    <a href="register.php">Register</a>
    <a href="login.php">Login</a>
  <?php endif; ?>
</nav>

<div class="card">
  <h2>Welcome to <?= e(APP_NAME) ?></h2>
  <p>Simple PHP auth with email verification via Gmail SMTP.</p>
</div>
</body>
</html>
