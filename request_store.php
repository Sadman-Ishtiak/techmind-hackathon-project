<?php
session_start();
require_once './config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Update store_request
$stmt = $conn->prepare("UPDATE user SET store_request=1 WHERE id=?");
$stmt->bind_param("i", $_SESSION['user']['id']);
$stmt->execute();

$_SESSION['message'] = "Store Owner request sent. Admin will review.";
header("Location: index.php");
exit;
