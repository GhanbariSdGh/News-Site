<?php
require 'includes/db.php';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT news.*, users.username, categories.title AS cat_title
                       FROM news
                       LEFT JOIN users ON users.id = news.author_id
                       LEFT JOIN categories ON categories.id = news.category_id
                       WHERE news.id = ? AND news.status = 1");
$stmt->execute([$id]);
$news = $stmt->fetch();

if (!$news) {
    echo "خبر مورد نظر یافت نشد یا هنوز تایید نشده است.";
    exit;
}

$commentStmt = $pdo->prepare("SELECT * FROM comments WHERE news_id = ? AND is_approved = 1 ORDER BY id DESC");
$commentStmt->execute([$id]);
$comments = $commentStmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($news['title']) ?></title>
    <style>
    body {
	  font-family: Tahoma, sans-serif;
	  direction: rtl;
	  background: #f9f9f9;
	  padding: 20px;
	  line-height: 1.7;
	}
	.container {
	  max-width: 900px;
	  margin: auto;
	  background: white;
	  padding: 25px;
	  border-radius: 8px;
	  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
	}
	h2,
	h3 {
	  color: #333;
	  margin-bottom: 15px;
	}
	.meta {
	  margin-bottom: 20px;
	  color: #666;
	}
	.news-content {
	  display: flex;
	  flex-direction: row;
	  gap: 20px;
	  align-items: flex-start;
	  margin-bottom: 30px;
	}
	.news-content img {
	  width: 45%;
	  max-width: 400px;
	  height: auto;
	  border-radius: 8px;
	  margin-top: 25px;
	}
	.news-text {
	  width: 55%;
	  text-align: right;
	  color: #444;
	}
	ul {
	  list-style: none;
	  padding: 0;
	}
	ul li {
	  background: #f1f1f1;
	  padding: 10px;
	  margin-bottom: 8px;
	  border-radius: 5px;
	  text-align: right;
	}
	form {
	  margin-top: 20px;
	}
	input[type="text"],
	textarea {
	  width: 100%;
	  padding: 10px;
	  border: 1px solid #ccc;
	  border-radius: 4px;
	  margin-bottom: 15px;
	  font-family: Tahoma;
	}
	button {
	  padding: 10px 18px;
	  background: #28a745;
	  color: white;
	  border: none;
	  border-radius: 5px;
	  cursor: pointer;
	}
	button:hover {
	  background: #218838;
	}
	a.back {
	  display: inline-block;
	  background: #007bff;
	  color: white;
	  padding: 8px 16px;
	  border-radius: 4px;
	  text-decoration: none;
	  margin-top: 20px;
	}
	a.back:hover {
	  background: #0056b3;
	}
    </style>
</head>
<body>
    <div class="container">
        <h2><?= htmlspecialchars($news['title']) ?></h2>
        <p class="meta"><strong>دسته:</strong> <?= htmlspecialchars($news['cat_title']) ?> - <strong>نویسنده:</strong> <?= htmlspecialchars($news['username']) ?></p>

        <div class="news-content">
            <img src="uploads/<?= htmlspecialchars($news['image']) ?>" alt="تصویر خبر">
            <div class="news-text">
                <p><?= nl2br(htmlspecialchars($news['content'])) ?></p>
            </div>
        </div>

        <hr>

        <h3>نظرات کاربران</h3>
        <?php if (count($comments) > 0): ?>
            <ul>
                <?php foreach ($comments as $comment): ?>
                    <li><strong><?= htmlspecialchars($comment['name']) ?>:</strong> <?= htmlspecialchars($comment['content']) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>نظری ثبت نشده است.</p>
        <?php endif; ?>

        <hr>

        <h3>ارسال نظر</h3>
        <form method="post" action="submit_comment.php">
            <input type="hidden" name="news_id" value="<?= $news['id'] ?>">
            <label>نام شما:</label>
            <input type="text" name="name" required>

            <label>متن نظر:</label>
            <textarea name="content" rows="4" required></textarea>

            <button type="submit">ارسال</button>
        </form>

        <?php
		$back_url = $_SERVER['HTTP_REFERER'] ?? 'admin/news.php';
		?>
		<div style="text-align: center; margin-top: 20px;">
			<a class="back" href="<?= htmlspecialchars($back_url) ?>">بازگشت</a>
		</div>
    </div>
</body>
</html>
