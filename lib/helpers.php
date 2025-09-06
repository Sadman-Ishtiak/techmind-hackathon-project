<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get the base path of the application
 */
function base_path($path = '') {
    return rtrim(str_replace('\\', '/', __DIR__ . '/../' . ltrim($path, '/')), '/');
}

/**
 * Escape output for HTML
 */
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to another page
 */
function redirect($to) {
    header('Location: ' . $to);
    exit;
}

/**
 * Check if the request is POST
 */
function is_post() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Generate or return CSRF token
 */
function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

/**
 * Return hidden CSRF input field
 */
function csrf_field() {
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

/**
 * Verify CSRF token from POST request
 */
function verify_csrf() {
    if (!isset($_POST['_token']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['_token'])) {
        http_response_code(419);
        die('CSRF token mismatch');
    }
}

/**
 * Get currently logged-in user
 */
function current_user() {
    return $_SESSION['user'] ?? null;
}

/**
 * Require authentication
 */
function require_auth() {
    if (!current_user()) redirect('login.php');
}

/**
 * Generate email verification HTML
 */
function verification_email_html($name, $link) {
    $app = defined('APP_NAME') ? e(APP_NAME) : 'My Application';
    $n = e($name);
    $l = e($link);
    return "
    <div style='font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Helvetica,Arial,sans-serif;max-width:600px;margin:auto'>
      <h2>{$app} – Verify your email</h2>
      <p>Hi {$n},</p>
      <p>Thanks for signing up. Please confirm your email by clicking the button below:</p>
      <p><a href='{$l}' style='display:inline-block;padding:10px 16px;text-decoration:none;border-radius:8px;border:1px solid #ccc'>Verify Email</a></p>
      <p>Or paste this link into your browser:</p>
      <p><code>{$l}</code></p>
      <p>If you didn't sign up, you can ignore this email.</p>
    </div>
    ";
}