<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'author') {
    header("Location: ../auth.php");
    exit;
}

$author_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM news WHERE id = ? AND author_id = ?");
$stmt->execute([$id, $author_id]);
$news = $stmt->fetch();

if (!$news) {
    die("خبر یافت نشد یا مجاز به ویرایش نیستید.");
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY title")->fetchAll();

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];

    if (empty($title) || empty($content)) {
        $error = "تمام فیلدها الزامی هستند.";
    } else {
        $stmt = $pdo->prepare("UPDATE news SET title = ?, content = ?, category_id = ?, is_approved = 0, status = 0 WHERE id = ? AND author_id = ?");
        $stmt->execute([$title, $content, $category_id, $id, $author_id]);
        $success = "خبر با موفقیت ویرایش شد. منتظر تأیید ادمین باشید.";
        $news['title'] = $title;
        $news['content'] = $content;
        $news['category_id'] = $category_id;
    }
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ویرایش خبر</title>
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
	  border-radius: 5px;
	  max-width: 600px;
	  margin: auto;
	  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
	}
	input,
	select,
	textarea {
	  width: 90%;
	  margin-bottom: 15px;
	  padding: 10px;
	  margin-right: 20px;
	  margin-top: 10px;
	}
	button {
	  padding: 10px 20px;
	  background: #28a745;
	  color: white;
	  border: none;
	  border-radius: 4px;
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
	a.back {
	  display: inline-block;
	  margin-top: 20px;
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

    @media (max-width: 768px) {
    form {
        padding: 15px;
        margin: 10px auto;
        max-width: 100%;
        box-shadow: none;
    }

    input,
    textarea,
    select {
        width: 95%;
        font-size: 0.95em;
        padding: 8px;
        margin: 10px 0;
    }

    label {
        font-size: 0.95em;
        display: block;
        margin-top: 10px;
    }

    button,
    .back {
        width: 100%;
        font-size: 0.95em;
        padding: 10px;
        box-sizing: border-box;
    }

    h2 {
        font-size: 1.3em;
        margin-bottom: 10px;
    }

    .msg {
        font-size: 0.9em;
        padding: 5px;
        margin-top: 10px;
    }

    .error,
    .success {
        font-size: 0.9em;
        text-align: center;
    }

    table {
        font-size: 0.85em;
    }
    }
    </style>
</head>
<body>

<h2 style="text-align: center;">ویرایش خبر</h2>

<form method="post">
    <label>عنوان خبر</label>
    <input type="text" name="title" value="<?= htmlspecialchars($news['title']) ?>">

    <label>محتوای خبر</label>
    <textarea name="content" rows="8"><?= htmlspecialchars($news['content']) ?></textarea>

    <label>دسته‌بندی</label>
    <select name="category_id">
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $news['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['title']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <div style="text-align: center; margin-top: 20px;">
		<button type="submit" class="back">ذخیره تغییرات</button>
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