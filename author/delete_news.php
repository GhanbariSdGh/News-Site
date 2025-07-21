<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'author') {
    header("Location: ../auth.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$author_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("DELETE FROM news WHERE id = ? AND author_id = ?");
$stmt->execute([$id, $author_id]);

header("Location: index.php");
exit;
