<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth.php');
    exit;
}

require '../includes/db.php';

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: categories.php");
    exit;
}

$search = $_GET['search'] ?? '';
$query = "SELECT * FROM categories";
$params = [];

if ($search) {
    $query .= " WHERE title LIKE ?";
    $params[] = "%$search%";
}
$query .= " ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت دسته‌بندی‌ها</title>
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

    form {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
        gap: 6px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    input[type="text"] {
        flex: 1;
        padding: 6px 8px;
        font-size: 0.85em;
        max-width: 180px;
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
        border-radius: 5px;
        padding: 10px;
        background: #fff;
    }

    table td {
        text-align: right;
        padding: 6px 10px;
        border: none;
        border-bottom: 1px solid #eee;
    }

    table td:last-child {
        border-bottom: none;
    }

    table th {
        display: none;
    }

    #create-form form {
        display: flex;
        flex-direction: column;
        gap: 6px;
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
    <h2>مدیریت دسته‌بندی‌ها</h2>

    <form method="get">
        <input type="text" name="search" placeholder="جستجوی عنوان..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="button">جستجو</button>
    </form>

    <br>

    <button type="button" class="button" id="show-create-form">+ دسته جدید</button>

    <div id="create-form">
        <form method="post" action="create_category.php">
            <input type="text" name="title" placeholder="عنوان دسته" required>
            <button type="submit" class="button">ایجاد</button>
        </form>
    </div>

    <br><br>

    <table>
        <tr>
            <th>ردیف</th>
            <th>عنوان</th>
            <th>تاریخ ایجاد</th>
            <th>عملیات</th>
        </tr>
        <?php foreach ($categories as $index => $cat): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($cat['title']) ?></td>
                <td><?= $cat['created_at'] ?></td>
                <td>
                    <a class="button" href="edit_category.php?id=<?= $cat['id'] ?>">ویرایش</a>
                    <a class="button" href="?delete=<?= $cat['id'] ?>" onclick="return confirm('حذف شود؟')">حذف</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
	
	<div style="text-align: center; margin-top: 20px;">
		<a class="back" href="index.php">بازگشت به پنل</a>
	</div>

    <script>
        document.getElementById('show-create-form').addEventListener('click', function () {
            document.getElementById('create-form').style.display = 'block';
        });
    </script>
</body>
</html>
