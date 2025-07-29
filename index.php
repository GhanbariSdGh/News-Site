<?php
require 'includes/db.php';

$catStmt = $pdo->query("SELECT * FROM categories");
$categories = $catStmt->fetchAll();

$sliderStmt = $pdo->query("SELECT id, title, image FROM news WHERE status = 1 ORDER BY created_at DESC LIMIT 3");
$sliderNews = $sliderStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نیوز میوز</title>
    <style>
    * {
	box-sizing: border-box;
	}
	body {
	  font-family: Tahoma;
	  direction: rtl;
	  margin: 0;
	  padding: 0;
	  background: #f5f5f5;
	}

	nav {
	  background: #007bff;
	  padding: 10px;
	  color: white;
	  display: flex;
	  align-items: center;
	  position: relative;
	  z-index: 10;
	}
	nav .logo {
	  margin-left: auto;
	  font-weight: bold;
	  font-size: 1.4em;
	}
	nav ul {
	  list-style: none;
	  margin: 10px;
	  padding: 0;
	  display: flex;
	}
	nav ul li {
	  position: relative;
	  margin-left: 20px;
	}
	nav ul li a {
	  color: black;
	  text-decoration: none;
	  padding: 5px 10px;
	  display: block;
	  background: white;
	  border-radius: 10px;
	}

	nav ul li a:hover {
	  color: #007bff;
	  color: #007bff;
	}

	nav ul li:hover > ul {
	  display: block;
	}
	nav ul ul {
	  display: none;
	  position: absolute;
	  top: 25px;
	  left: -9px;
	  background: white;
	  padding: 5px;
	  min-width: 125px;
	  border-radius: 3px;
	  z-index: 999;
	}

	nav ul ul a {
	  color: black;
	}
	nav ul ul a:hover {
	  color: #007bff;
	}

	.slider {
	  width: 100%;
	  max-height: 300px;
	  overflow: hidden;
	  position: relative;
	  margin: 0 auto 30px auto;
	  background: #007bff;
	  background: linear-gradient(
		90deg,
		rgba(0, 123, 255, 1) 0%,
		rgba(0, 123, 255, 1) 35%,
		rgba(255, 255, 255, 1) 70%
	  );
	}
	.slider-title {
	  position: absolute;
	  top: 10px;
	  left: 20px;
	  background-color: #dc3545;
	  color: white;
	  padding: 6px 15px;
	  border-radius: 20px;
	  font-weight: bold;
	  font-size: 1em;
	  z-index: 2;
	}
	.slide {
	  display: flex;
	  align-items: center;
	  padding: 15px;
	  min-height: 250px;
	  transition: opacity 0.5s ease-in-out;
	}
	.slide img {
	  width: 300px;
	  height: 200px;
	  object-fit: cover;
	  border-radius: 5px;
	  margin-left: 20px;
	}
	.slide h3 {
	  font-size: 1.3em;
	  color: #333;
	}

	.slider-controls {
	  text-align: center;
	  margin-top: -20px;
	}
	.dot {
	  display: inline-block;
	  width: 10px;
	  height: 10px;
	  background: #ccc;
	  margin: 5px;
	  border-radius: 50%;
	  cursor: pointer;
	}
	.dot.active {
	  background: #007bff;
	}

	.category-section {
	  padding: 0 30px;
	  margin-bottom: 40px;
	}
	.category-header {
	  display: flex;
	  justify-content: space-between;
	  align-items: center;
	}
	.news-cards {
	  display: flex;
	  gap: 15px;
	  margin-top: 10px;
	  flex-wrap: wrap;
	  margin-right: 50px;
	}
	.news-card {
	  border: 1px solid #ddd;
	  padding: 10px;
	  width: 23%;
	  box-sizing: border-box;
	  border-radius: 4px;
	  background: #fff;
	}
	.news-card a {
	  text-decoration: none;
	  color: #333;
	}
	.news-card a:hover {
	  text-decoration: underline;
	}
	.button {
	  background-color: #007bff;
	  color: white;
	  padding: 6px 12px;
	  text-decoration: none;
	  border-radius: 4px;
	  font-size: 0.9em;
	}

	footer {
	  margin-top: 50px;
	  padding: 15px;
	  background: #007bff;
	  text-align: center;
	  color: white;
	  font-size: 0.9em;
	}

    @media (max-width: 768px) {
    nav {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        padding: 5px 10px;
    }

    nav .logo {
        font-size: 0.9em;
        margin: 0;
        padding: 0;
        white-space: nowrap;
    }

    nav ul {
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        gap: 5px;
        margin: 0;
        padding: 0;
    }

    nav ul li {
        margin-left: 0;
    }

    nav ul li a {
        font-size: 0.75em;
        padding: 4px 6px;
        white-space: nowrap;
    }

    .slider,
    .slider-controls {
        display: none !important;
    }

    .news-cards {
        flex-wrap: nowrap;
        overflow-x: auto;
        gap: 10px;
        padding-bottom: 10px;
        margin-right: 0;
        scrollbar-width: none;
    }

    .news-cards::-webkit-scrollbar {
        display: none;
    }

    .news-card {
        min-width: 45vw;
        flex: 0 0 auto;
        font-size: 0.8em;
        padding: 8px;
        border-radius: 5px;
    }

    .news-card img {
        width: 100%;
        height: 100px;
        object-fit: cover;
        border-radius: 3px;
        margin-top: 5px;
    }

    .news-card h4 {
        font-size: 0.85em;
        margin: 5px 0;
    }

    .news-card p {
        font-size: 0.75em;
        margin-top: 5px;
    }

    .category-header h2 {
        font-size: 1em;
    }

    .button {
        font-size: 0.75em;
        padding: 4px 8px;
    }

    .category-section {
        padding: 0 15px;
    }

    footer {
        font-size: 0.75em;
        padding: 10px;
        line-height: 1.5;
    }
    }
    </style>
</head>
<body>

<nav>
    <div class="logo">نیوز میوز</div>
    <ul>
        <li>
            <a href="#">▼ دسته‌بندی‌ها</a>
            <ul>
                <?php foreach ($categories as $category): ?>
                    <li><a href="category_news.php?id=<?= $category['id'] ?>"><?= htmlspecialchars($category['title']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li><a href="auth.php" class="btn-login">ورود / ثبت نام</a></li>
    </ul>
</nav>

<div class="slider" id="slider">
    <div class="slider-title">اخبار داغ</div>
    <?php foreach ($sliderNews as $index => $news): ?>
        <a href="news.php?id=<?= $news['id'] ?>" style="text-decoration: none;">
            <div class="slide" style="display: <?= $index === 0 ? 'flex' : 'none' ?>;">
                <img src="uploads/<?= htmlspecialchars($news['image']) ?>" alt="تصویر خبر">
                <h3><?= htmlspecialchars($news['title']) ?></h3>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<div class="slider-controls">
    <?php foreach ($sliderNews as $index => $news): ?>
        <span class="dot <?= $index === 0 ? 'active' : '' ?>" onclick="showSlide(<?= $index ?>)"></span>
    <?php endforeach; ?>
</div>

<?php foreach ($categories as $category): ?>
    <section class="category-section">
        <div class="category-header">
            <h2><?= htmlspecialchars($category['title']) ?></h2>
            <a href="category_news.php?id=<?= $category['id'] ?>" class="button">مشاهده همه اخبار</a>
        </div>

        <div class="news-cards">
            <?php
            $stmt = $pdo->prepare("SELECT * FROM news WHERE category_id = ? AND status = 1 ORDER BY created_at DESC LIMIT 4");
            $stmt->execute([$category['id']]);
            $news_list = $stmt->fetchAll();
            ?>

            <?php if (count($news_list) > 0): ?>
                <?php foreach ($news_list as $news): ?>
                    <div class="news-card">
                        <a href="news.php?id=<?= $news['id'] ?>">
                            <h4><?= htmlspecialchars($news['title']) ?></h4>
                            <?php if (!empty($news['image'])): ?>
                                <img src="uploads/<?= htmlspecialchars($news['image']) ?>" alt="تصویر خبر" style="width: 100%; height: auto; margin-top: 8px; border-radius: 3px;">
                            <?php endif; ?>
                            <p style="margin-top: 8px; font-size: 0.9em; color: #555;">
                                <?= htmlspecialchars(mb_strimwidth(strip_tags($news['content']), 0, 100, "...")) ?>
                            </p>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>خبر جدیدی برای این دسته‌بندی وجود ندارد.</p>
            <?php endif; ?>
        </div>
    </section>
<?php endforeach; ?>

<footer>
    تمامی حقوق متعلق به سایت "نیوز میوز" میباشد.
	<br>
	<br>
	انتشار مطالب تنها با ذکر منبع مجاز است.
</footer>

<script>
    let current = 0;
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');

    function showSlide(index) {
        slides.forEach((s, i) => {
            s.style.display = (i === index) ? 'flex' : 'none';
            dots[i].classList.toggle('active', i === index);
        });
        current = index;
    }

    setInterval(() => {
        let next = (current + 1) % slides.length;
        showSlide(next);
    }, 5000);
</script>

</body>
</html>
