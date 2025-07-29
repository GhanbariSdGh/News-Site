<?php
require 'includes/db.php';

$category_id = $_GET['id'] ?? null;
if (!$category_id) {
    die("دسته‌بندی نامعتبر است.");
}

$search_title = $_GET['title'] ?? '';
$search_author = $_GET['author'] ?? '';
$search_date = $_GET['date'] ?? '';

$query = "SELECT news.*, users.username AS author_name FROM news 
          JOIN users ON news.author_id = users.id
          WHERE news.category_id = ? AND news.status = 1";

$params = [$category_id];

if ($search_title) {
    $query .= " AND news.title LIKE ?";
    $params[] = "%$search_title%";
}
if ($search_author) {
    $query .= " AND users.username LIKE ?";
    $params[] = "%$search_author%";
}
if ($search_date) {
    $query .= " AND DATE(news.created_at) = ?";
    $params[] = $search_date;
}

$query .= " ORDER BY news.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$news_list = $stmt->fetchAll();

$catStmt = $pdo->prepare("SELECT title FROM categories WHERE id = ?");
$catStmt->execute([$category_id]);
$category = $catStmt->fetch();

if (!$category) {
    die("دسته‌بندی یافت نشد.");
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اخبار دسته <?= htmlspecialchars($category['title']) ?></title>
    <style>
    body {
	  font-family: Tahoma, sans-serif;
	  direction: rtl;
	  background: #f5f5f5;
	  margin: 0;
	  padding: 20px;
	}

	h2 {
	  text-align: center;
	  color: #333;
	  margin-bottom: 20px;
	}

	.search-form {
	  display: flex;
	  flex-wrap: wrap;
	  gap: 10px;
	  justify-content: center;
	  margin-bottom: 30px;
	}

	.search-form input,
	.search-form button {
	  padding: 8px 10px;
	  border: 1px solid #ccc;
	  border-radius: 4px;
	  font-family: Tahoma;
	}

	.search-form button {
	  background-color: #007bff;
	  color: white;
	  cursor: pointer;
	  border: none;
	}

	.search-form button:hover {
	  background-color: #0056b3;
	}

	.news-cards {
	  display: flex;
	  flex-wrap: wrap;
	  gap: 20px;
	  justify-content: center;
	}

	.news-card {
	  background: white;
	  border: 1px solid #ddd;
	  border-radius: 8px;
	  width: 23%;
	  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
	  padding: 15px;
	  box-sizing: border-box;
	  transition: 0.3s;
	}

	.news-card:hover {
	  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
	}

	.news-card h4 {
	  margin: 0 0 10px;
	  color: #333;
	  font-size: 1em;
	}

	.news-card img {
	  width: 100%;
	  height: 140px;
	  object-fit: cover;
	  border-radius: 5px;
	  margin-bottom: 10px;
	}

	.news-card p {
	  font-size: 0.85em;
	  color: #555;
	  margin-bottom: 10px;
	}

	.news-card a {
	  display: inline-block;
	  background-color: #28a745;
	  color: white;
	  padding: 6px 12px;
	  border-radius: 4px;
	  text-decoration: none;
	  font-size: 0.85em;
	}

	.news-card a:hover {
	  background-color: #218838;
	}

	.btn-back {
	  display: inline-block;
	  width: 15%;
	  padding: 10px;
	  margin-top: 15px;
	  background: #007bff;
	  color: white;
	  text-align: center;
	  border-radius: 3px;
	  text-decoration: none;
	  font-weight: bold;
	}
	
	.btn-back:hover {
	  background: #0056b3;
	}

    @media (max-width: 768px) {
    .search-form {
        flex-wrap: wrap;
        flex-direction: row;
        justify-content: center;
        gap: 6px;
    }

    .search-form input[type="text"],
    .search-form input[type="date"] {
        width: 30%;
        font-size: 0.8em;
        padding: 6px;
    }

    .search-form button {
        font-size: 0.8em;
        padding: 6px 10px;
    }

    .news-card {
        width: 47%;
        padding: 10px;
        height: auto;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .news-card img {
        height: 180px;
    }

    .news-card h4 {
        font-size: 0.9em;
        margin-top: 10px;
    }

    .news-card p {
        font-size: 0.75em;
        margin-bottom: 6px;
    }

    .news-card a {
        font-size: 0.75em;
        padding: 5px 10px;
        align-self: flex-start;
    }

    .btn-back {
        width: 60%;
        font-size: 0.85em;
        padding: 10px;
    }
    }

    @media (max-width: 480px) {
    .search-form input[type="text"],
    .search-form input[type="date"] {
        width: 100%;
    }

    .search-form {
        flex-direction: column;
        align-items: stretch;
    }

    .news-card {
        width: 100%;
    }

    .news-card img {
        height: 160px;
    }

    .btn-back {
        width: 80%;
    }
    }
    </style>
</head>
<body>

    <h2>اخبار دسته: <?= htmlspecialchars($category['title']) ?></h2>

    <form method="get" class="search-form">
        <input type="hidden" name="id" value="<?= $category_id ?>">
        <input type="text" name="title" placeholder="جستجوی عنوان" value="<?= htmlspecialchars($search_title) ?>">
        <input type="text" name="author" placeholder="جستجوی نویسنده" value="<?= htmlspecialchars($search_author) ?>">
        <input type="date" name="date" value="<?= htmlspecialchars($search_date) ?>">
        <button type="submit">جستجو</button>
    </form>

    <div class="news-cards">
        <?php if (count($news_list) > 0): ?>
            <?php foreach ($news_list as $news): ?>
                <div class="news-card">
                    <?php if (!empty($news['image'])): ?>
                        <img src="uploads/<?= htmlspecialchars($news['image']) ?>" alt="تصویر خبر">
                    <?php endif; ?>
                    <h4><?= htmlspecialchars($news['title']) ?></h4>
                    <p>نویسنده: <?= htmlspecialchars($news['author_name']) ?></p>
                    <p><?= date('Y-m-d', strtotime($news['created_at'])) ?></p>
                    <a href="news.php?id=<?= $news['id'] ?>">مشاهده</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align:center;">خبری یافت نشد.</p>
        <?php endif; ?>
    </div>

    <div style="text-align: center; margin-top: 15px;">
		<a href="index.php" class="btn-back">بازگشت به صفحه اصلی</a>
	</div>

</body>
</html>
