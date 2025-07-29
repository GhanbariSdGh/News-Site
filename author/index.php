<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'author') {
    header("Location: ../auth.php");
    exit;
}

$author_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT news.*, categories.title AS category_title FROM news 
                       LEFT JOIN categories ON news.category_id = categories.id 
                       WHERE news.author_id = ? ORDER BY news.created_at DESC");
$stmt->execute([$author_id]);
$news_list = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <title>داشبورد نویسنده</title>
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

    @media (max-width: 768px) {
    body {
        padding: 10px;
    }

    h2 {
        font-size: 1.3em;
        text-align: center;
    }

    a.button {
        display: block;
        width: 100%;
        font-size: 0.85em;
        padding: 8px;
        margin-bottom: 10px;
        box-sizing: border-box;
        text-align: center;
    }

    table {
        width: 100%;
        font-size: 0.85em;
        overflow-x: hidden;
        display: block;
        background: white;
        border-collapse: separate;
        border-spacing: 0 10px;
        margin-top: 10px;
    }

    table thead,
    table tbody,
    table th,
    table td,
    table tr {
        display: block;
    }

    table tr {
        margin-bottom: 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        padding: 8px 12px;
        background: #fff;
    }

    table th {
        display: none;
    }

    table td {
        position: relative;
        text-align: left;
        padding-left: 10px;
        padding-right: 50%;
        border: none;
        border-bottom: 1px solid #eee;
        white-space: normal;
        word-break: break-word;
    }

    table td:last-child {
        border-bottom: none;
    }

    table td:before {
        position: absolute;
        right: 12px;
        top: 8px;
        width: 45%;
        font-weight: bold;
        white-space: nowrap;
        text-align: right;
        content: attr(data-label);
    }

    table td:nth-of-type(1):before { content: "ردیف"; }
    table td:nth-of-type(2):before { content: "عنوان"; }
    table td:nth-of-type(3):before { content: "دسته‌بندی"; }
    table td:nth-of-type(4):before { content: "تاریخ"; }
    table td:nth-of-type(5):before { content: "وضعیت"; }
    table td:nth-of-type(6):before { content: "عملیات"; }

    td:last-child {
        display: flex;
        justify-content: flex-start;
        flex-wrap: wrap;
        gap: 6px;
        padding-top: 12px;
    }

    td:last-child a.button {
        font-size: 0.75em;
        padding: 5px 8px;
        flex: 1 1 auto;
        text-align: center;
        min-width: 70px;
    }

    .back {
        display: block;
        width: 100%;
        margin-top: 20px;
        font-size: 0.85em;
        padding: 10px;
        text-align: center;
        box-sizing: border-box;
    }
    }
    </style>
</head>
<body>

<h2>پنل نویسنده</h2>

<a class="button" href="create_news.php">✚ افزودن خبر جدید</a>

<table>
    <thead>
        <tr>
            <th>ردیف</th>
            <th>عنوان</th>
            <th>دسته‌بندی</th>
            <th>تاریخ</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($news_list as $index => $news): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($news['title']) ?></td>
                <td><?= htmlspecialchars($news['category_title']) ?></td>
                <td><?= $news['created_at'] ?></td>
                <td><?= $news['status'] ? '✔️ تأیید شده' : '⏳ در انتظار' ?></td>
                <td>
                    <a class="button" href="../news.php?id=<?= $news['id'] ?>" target="_blank">مشاهده</a>
                    <a class="button" href="edit_news.php?id=<?= $news['id'] ?>">ویرایش</a>
                    <a class="button" href="delete_news.php?id=<?= $news['id'] ?>" onclick="return confirm('آیا از حذف خبر مطمئن هستید؟')">حذف</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div style="text-align: center; margin-top: 20px;">
	<a class="back" href="../auth.php">خروج</a>
</div>
	
</body>
</html>
