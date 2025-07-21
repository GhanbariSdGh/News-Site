<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>داشبورد ادمین</title>
    <style>
    body {
	  font-family: Tahoma;
	  direction: rtl;
	  padding: 20px;
	  background: #f9f9f9;
	}

	.card-list {
	  display: flex;
	  flex-wrap: wrap;
	  gap: 20px;
	  justify-content: center;
	  padding: 30px 0;
	}

	.card-list ul {
	  list-style: none;
	  background: white;
	  border: 1px solid #ddd;
	  border-radius: 8px;
	  padding: 20px;
	  width: 220px;
	  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
	  transition: 0.3s ease;
	}

	.card-list ul:hover {
	  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.15);
	}

	.card-list li {
	  margin: 10px 0;
	  text-align: center;
	}

	.card-list a {
	  display: block;
	  padding: 10px;
	  background-color: #007bff;
	  color: white;
	  border-radius: 5px;
	  text-decoration: none;
	  transition: background-color 0.2s ease;
	}

	.card-list a:hover {
	  background-color: #0056b3;
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
    </style>
</head>
<body>
    <h2>پنل مدیریت ادمین</h2>

    <div class="card-list">
        <ul>
            <li><a href="users.php">مدیریت نویسندگان</a></li>
        </ul>
        <ul>
            <li><a href="news.php">مدیریت اخبار</a></li>
        </ul>
        <ul>
            <li><a href="categories.php">مدیریت دسته‌بندی‌ها</a></li>
        </ul>
        <ul>
            <li><a href="comments.php">مدیریت کامنت‌ها</a></li>
        </ul>
        <ul>
            <li><a href="../auth.php">خروج</a></li>
        </ul>
    </div>
</body>
</html>
