<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'author') {
    header("Location: ../auth.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category_id = $_POST['category_id'] ?? '';
    $author_id = $_SESSION['user_id'];
    $image_path = null;

    if ($title === '' || $content === '' || $category_id === '') {
        $error = 'لطفاً همه فیلدها را پر کنید.';
    } else {
        if (!empty($_FILES['image']['name'])) {
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir);
            $filename = time() . '_' . basename($_FILES['image']['name']);
            $target = $upload_dir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image_path = $filename;
            } else {
                $error = 'آپلود تصویر با خطا مواجه شد.';
            }
        }

        if ($error === '') {
            $stmt = $pdo->prepare("INSERT INTO news (title, content, category_id, author_id, image, is_approved, created_at) VALUES (?, ?, ?, ?, ?, 0, NOW())");
            if ($stmt->execute([$title, $content, $category_id, $author_id, $image_path])) {
                $success = 'خبر با موفقیت ثبت شد و منتظر تایید مدیر است.';
            } else {
                $error = 'خطا در ثبت خبر.';
            }
        }
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY title")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>افزودن خبر جدید</title>
    <style>
    body {
	  font-family: Tahoma;
	  direction: rtl;
	  background: #f5f5f5;
	  padding: 20px;
	}
	form {
	  background: white;
	  padding: 20px;
	  max-width: 600px;
	  margin: auto;
	  border-radius: 5px;
	  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
	}
	input,
	textarea,
	select {
	  width: 100%;
	  padding: 8px;
	  margin: 10px 0;
	  border: 1px solid #ccc;
	  border-radius: 3px;
	}
	button {
	  padding: 10px 16px;
	  background: #28a745;
	  color: white;
	  border: none;
	  border-radius: 4px;
	  cursor: pointer;
	}
	.msg {
	  text-align: center;
	  margin-top: 10px;
	}
	.error {
	  color: red;
	  text-align: center;
	  margin-top: 20px;
	}
	.success {
	  color: green;
	  text-align: center;
	  margin-top: 20px;
	}
	table {
	  border-collapse: collapse;
	  width: 100%;
	}
	th,
	td {
	  border: 1px solid #aaa;
	  padding: 8px;
	  text-align: center;
	}
	th {
	  background-color: #eee;
	}
	.button {
	  padding: 4px 10px;
	  background-color: #3c8dbc;
	  color: white;
	  border: none;
	  cursor: pointer;
	  text-decoration: none;
	}
	.button:hover {
	  background-color: #367fa9;
	}
	body {
	  font-family: Tahoma;
	  direction: rtl;
	  padding: 20px;
	  background: #f9f9f9;
	}
	table {
	  border-collapse: collapse;
	  width: 100%;
	  background: white;
	  margin-top: 25px;
	}
	th,
	td {
	  border: 1px solid #ccc;
	  padding: 8px;
	  text-align: center;
	}
	th {
	  background: #eee;
	}
	a.delete {
	  color: red;
	  text-decoration: none;
	}
	a.delete:hover {
	  text-decoration: underline;
	}
	.error {
	  color: red;
	  margin-bottom: 15px;
	}
	a.back {
	  margin-top: 20px;
	  display: inline-block;
	  background: #007bff;
	  color: white;
	  padding: 8px 16px;
	  border-radius: 4px;
	  text-decoration: none;
	}
	table {
	  border-collapse: collapse;
	  width: 100%;
	}
	th,
	td {
	  border: 1px solid #aaa;
	  padding: 8px;
	  text-align: center;
	}
	th {
	  background-color: #eee;
	}
	#create-form {
	  margin-top: 10px;
	  display: none;
	}
	.button {
	  padding: 5px 10px;
	  background-color: #3c8dbc;
	  color: white;
	  border: none;
	  cursor: pointer;
	}
	.button:hover {
	  background-color: #367fa9;
	}
    </style>
</head>
<body>

<h2 style="text-align:center;">افزودن خبر جدید</h2>

<form method="post" enctype="multipart/form-data">
    <label>عنوان خبر:</label>
    <input type="text" name="title" required>

    <label>متن خبر:</label>
    <textarea name="content" rows="6" required></textarea>

    <label>دسته‌بندی:</label>
    <select name="category_id" required>
        <option value="">انتخاب دسته‌بندی</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['title']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>تصویر (اختیاری):</label>
    <input type="file" name="image" accept="image/*">
	
	<div style="text-align: center; margin-top: 20px;">
		<button type="submit">ارسال خبر</button>
	</div>

    <div class="msg">
        <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <?php if ($success): ?><div class="success"><?= $success ?></div><?php endif; ?>
    </div>
</form>
<div style="text-align: center; margin-top: 20px;">
	<a class="back" href="index.php">بازگشت به پنل</a>
</div>

</body>
</html>
