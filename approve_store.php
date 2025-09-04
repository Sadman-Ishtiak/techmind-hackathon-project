<?php
session_start();
require_once './config.php';

if ($_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$user_id = intval($_GET['user_id'] ?? 0);

if ($user_id) {
    $stmt = $conn->prepare("UPDATE user SET role='store_owner', store_request=0 WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

header("Location: admin_dashboard.php");
exit;