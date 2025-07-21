<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth.php');
    exit;
}

require '../includes/db.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("UPDATE news SET status = NOT status WHERE id = ?");
$stmt->execute([$id]);

header("Location: news.php");
exit;
