<?php
// =====================
// ===== DATABASE ======
// =====================
define('DB_HOST', '127.0.0.1');  // or 'localhost'
define('DB_NAME', 'Hackathon');  // choose your DB name
define('DB_USER', 'root');
define('DB_PASS', '');

// =====================
// ===== APP CONFIG ====
// =====================
define('APP_NAME', 'Techmind Hackathon');
define('BASE_URL', 'http://localhost/techmind'); // no trailing slash

// =====================
// ===== Gmail SMTP ====
// =====================
// Use App Password, not your normal Gmail password
$SMTP_CONFIG = [
    'host' => 'smtp.gmail.com',
    'port' => 587,  // STARTTLS
    'username' => 'jkkniu.project.mail@gmail.com',
    'password' => 'vlvwilndoejmmhgc', // Google App Password
    'from_email' => 'jkkniu.project.mail@gmail.com',
    'from_name'  => 'Bot Mail'
];

// =====================
// ===== SESSION =======
// =====================
if(!isset($_SESSION)) {
    session_name('ppag_session');
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}
// =====================
// ===== PDO SETUP =====
// =====================
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die('DB connection failed: ' . htmlspecialchars($e->getMessage()));
}
