<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth.php');
    exit;
}

require '../includes/db.php';

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: comments.php");
    exit;
}

if (isset($_GET['approve'])) {
    $stmt = $pdo->prepare("UPDATE comments SET is_approved = 1 WHERE id = ?");
    $stmt->execute([$_GET['approve']]);
    header("Location: comments.php");
    exit;
}

$search = $_GET['search'] ?? '';
$query = "SELECT comments.*, news.title AS news_title FROM comments 
          LEFT JOIN news ON news.id = comments.news_id";
$params = [];

if ($search) {
    $query .= " WHERE comments.name LIKE ? OR comments.content LIKE ?";
    $params = ["%$search%", "%$search%"];
}

$query .= " ORDER BY comments.id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$comments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت کامنت‌ها</title>
    <style>
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
	  background-color: #007bff;
	  color: white;
	  border: none;
	  cursor: pointer;
	  text-decoration: none;
	  border-radius: 3px;
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

    @media (max-width: 768px) {
    body {
        padding: 10px;
    }

    form {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        gap: 6px;
        justify-content: center;
        align-items: center;
        margin-bottom: 10px;
    }

    input[type="text"] {
        flex: 1;
        max-width: 180px;
        padding: 6px 8px;
        font-size: 0.85em;
    }

    .button {
        padding: 6px 12px;
        font-size: 0.85em;
        white-space: nowrap;
    }

    table {
        width: 100%;
        font-size: 0.85em;
        overflow-x: auto;
        display: block;
    }

    table thead,
    table tbody,
    table th,
    table td,
    table tr {
        display: block;
    }

    table tr {
        margin-bottom: 14px;
        border: 1px solid #ccc;
        border-radius: 6px;
        padding: 10px;
        background: #fff;
    }

    table td {
        text-align: right;
        padding: 6px 10px;
        border: none;
        border-bottom: 1px solid #eee;
        word-wrap: break-word;
    }

    table td:last-child {
        border-bottom: none;
    }

    table th {
        display: none;
    }

    h2 {
        font-size: 1.3em;
        text-align: center;
    }

    a.back {
        font-size: 0.85em;
        padding: 6px 10px;
        display: block;
        margin: 20px auto 0;
    }
    }
    </style>
</head>
<body>
    <h2>مدیریت کامنت‌ها</h2>

    <form method="get">
        <input type="text" name="search" placeholder="جستجو بر اساس نام یا متن..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="button">جستجو</button>
    </form>

    <br>

    <table>
        <tr>
            <th>ردیف</th>
            <th>نام کاربری</th>
            <th>متن کامنت</th>
            <th>خبر مرتبط</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
        <?php foreach ($comments as $index => $comment): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($comment['name']) ?></td>
                <td><?= htmlspecialchars($comment['content']) ?></td>
                <td><?= htmlspecialchars($comment['news_title']) ?></td>
                <td><?= $comment['is_approved'] ? '✅ تایید شده' : '⏳ در انتظار' ?></td>
                <td>
                    <?php if (!$comment['is_approved']): ?>
                        <a class="button" href="?approve=<?= $comment['id'] ?>">✔️ تایید</a>
                    <?php endif; ?>
                    <a class="button" href="?delete=<?= $comment['id'] ?>" onclick="return confirm('کامنت حذف شود؟')">حذف</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

<div style="text-align: center; margin-top: 20px;">
    <a class="back" href="index.php">بازگشت به پنل</a>
</div>
</body>
</html>
