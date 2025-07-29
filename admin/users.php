<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth.php");
    exit;
}

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    if ($delete_id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'author'");
        $stmt->execute([$delete_id]);
        header("Location: users.php");
        exit;
    } else {
        $error = "شما نمی‌توانید خودتان را حذف کنید.";
    }
}

$search = $_GET['search'] ?? '';
$query = "SELECT id, username, role, created_at FROM users WHERE role = 'author'";
$params = [];

if (!empty($search)) {
    $query .= " AND username LIKE ?";
    $params[] = "%$search%";
}

$query .= " ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$authors = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت نویسندگان</title>
    <style>
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
    table td:nth-of-type(2):before { content: "نام کاربری"; }
    table td:nth-of-type(3):before { content: "تاریخ ثبت‌نام"; }
    table td:nth-of-type(4):before { content: "حذف"; }

    a.delete {
        font-size: 1.2em;
        padding: 6px 8px;
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
    }
    </style>
</head>
<body>

<h2>مدیریت نویسندگان</h2>

<form method="get">
    <input type="text" name="search" placeholder="جستجوی نام کاربری..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit" class="button">جستجو</button>
</form>

<br>

<?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>ردیف</th>
            <th>نام کاربری</th>
            <th>تاریخ ثبت‌نام</th>
            <th>حذف</th>
        </tr>
    </thead>
    <tbody>
        <?php $i=1; foreach ($authors as $author): ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($author['username']) ?></td>
            <td><?= htmlspecialchars($author['created_at']) ?></td>
            <td>
                <a class="delete" href="users.php?delete_id=<?= $author['id'] ?>" onclick="return confirm('آیا از حذف این کاربر مطمئن هستید؟');">❌</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (count($authors) === 0): ?>
        <tr><td colspan="5">کاربری یافت نشد.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div style="text-align: center; margin-top: 20px;">
    <a class="back" href="index.php">بازگشت به پنل</a>
</div>

</body>
</html>
