<?php
// ===== Database =====
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'Hackathon');
define('DB_USER', 'root');
define('DB_PASS', '');

// ===== App =====
define('APP_NAME', 'Pure PHP Auth');
define('BASE_URL', 'http://localhost/purephp-auth-gmail'); // no trailing slash

// ===== Gmail SMTP (use App Password, not your normal password) =====
$SMTP_CONFIG = [
  'host' => 'smtp.gmail.com',
  'port' => 587,            // STARTTLS
  'username' => 'jkkniu.project.mail@gmail.com',
  'password' => 'vlvwilndoejmmhgc', // create in Google Account > Security > App passwords
  'from_email' => 'jkkniu.project.mail@gmail.com',
  'from_name'  => 'Bot Mail'
];


// ===== Session =====
session_name('ppag_session');
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// ===== PDO =====
try {
  $pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER,
    DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
  );
} catch (PDOException $e) {
  http_response_code(500);
  die('DB connection failed: ' . htmlspecialchars($e->getMessage()));
}
