<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth.php');
    exit;
}

require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    if ($title) {
        $stmt = $pdo->prepare("INSERT INTO categories (title) VALUES (?)");
        $stmt->execute([$title]);
    }
}
header("Location: categories.php");
exit;
