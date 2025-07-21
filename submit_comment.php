<?php
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $news_id = $_POST['news_id'] ?? 0;
    $name = $_POST['name'] ?? '';
    $content = $_POST['content'] ?? '';

    if ($name && $content && $news_id) {
        $stmt = $pdo->prepare("INSERT INTO comments (news_id, name, content, is_approved) VALUES (?, ?, ?, 0)");
        $stmt->execute([$news_id, $name, $content]);
    }
    header("Location: news.php?id=" . $news_id);
    exit;
}
