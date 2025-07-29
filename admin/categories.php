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
        flex-direction: column;
        gap: 8px;
    }

    input[type="text"] {
        width: 100%;
        padding: 6px;
        font-size: 0.85em;
        box-sizing: border-box;
    }

    button.button {
        padding: 6px 10px;
        font-size: 0.85em;
        width: 100%;
        box-sizing: border-box;
        margin-top: 4px;
    }

    table {
        width: 100%;
        font-size: 0.85em;
        overflow-x: hidden;
        display: block;
        background: white;
        border-collapse: separate;
        border-spacing: 0 10px;
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
    table td:nth-of-type(3):before { content: "تاریخ ایجاد"; }
    table td:nth-of-type(4):before { content: "عملیات"; }

    a.button {
        display: inline-block;
        padding: 6px 10px;
        font-size: 0.85em;
        margin: 3px 3px 3px 0;
        border-radius: 3px;
    }

    a.back {
        margin-top: 20px;
        display: block;
        background: #007bff;
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
        text-decoration: none;
        text-align: center;
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
