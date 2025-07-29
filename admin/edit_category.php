<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth.php');
    exit;
}
require '../includes/db.php';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) {
    echo "دسته‌بندی پیدا نشد.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    if ($title) {
        $stmt = $pdo->prepare("UPDATE categories SET title = ? WHERE id = ?");
        $stmt->execute([$title, $id]);
        header("Location: categories.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ویرایش دسته‌بندی‌</title>
    <style>
    body {
	  font-family: Tahoma;
	  direction: rtl;
	  margin: 0;
	  padding: 0;
	  background: #f9f9f9;
	  height: 100vh;
	  display: flex;
	  justify-content: center;
	  align-items: center;
	}
	.container {
	  text-align: center;
	  background: white;
	  padding: 30px;
	  border-radius: 10px;
	  box-shadow: 0 0 10px #ccc;
	  min-width: 300px;
	}
	input[type="text"] {
	  padding: 8px;
	  width: 80%;
	  margin-bottom: 10px;
	}
	.btn {
	  margin-top: 10px;
	  display: inline-block;
	  background: #007bff;
	  color: white;
	  padding: 8px 16px;
	  border-radius: 4px;
	  text-decoration: none;
	  border: none;
	  cursor: pointer;
	  font-size: 14px;
	}
	.btn:hover {
	  background: #0056b3;
	}

    @media (max-width: 768px) {
    .container {
        width: 80%;
        padding: 15px;
        min-width: auto;
    }

    input[type="text"] {
        width: 90%;
        font-size: 0.85em;
        padding: 6px 8px;
    }

    .btn {
        width: 20%;
        font-size: 0.85em;
        padding: 6px 10px;
        margin-top: 6px;
    }

    h2 {
        font-size: 1.1em;
        margin-bottom: 12px;
    }
    }
    </style>
</head>
<body>
    <div class="container">
        <h2>ویرایش عنوان</h2>
        <form method="post">
            <input type="text" name="title" value="<?= htmlspecialchars($category['title']) ?>" required>
            <br>
            <button type="submit" class="btn">ذخیره</button>
        </form>
        <a class="btn" href="categories.php">بازگشت</a>
    </div>
</body>
</html>

