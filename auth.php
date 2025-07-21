<?php
require 'includes/db.php';
session_start();

$login_error = '';
$register_error = '';
$register_success = '';
$active_tab = 'login';

if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = trim($_POST['login_username'] ?? '');
    $password = trim($_POST['login_password'] ?? '');

    if ($username === '' || $password === '') {
        $login_error = 'نام کاربری و رمز عبور را وارد کنید.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: admin/index.php');
                exit;
            } elseif ($user['role'] === 'author') {
                header('Location: author/index.php');
                exit;
            } else {
                $login_error = 'نقش کاربر نامعتبر است.';
            }
        } else {
            $login_error = 'نام کاربری یا رمز عبور اشتباه است.';
        }
    }
    $active_tab = 'login';
}

if (isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = trim($_POST['register_username'] ?? '');
    $password = trim($_POST['register_password'] ?? '');
    $role = $_POST['register_role'] ?? '';
    $admin_key = trim($_POST['admin_key'] ?? '');
    $active_tab = 'register';

    if ($username === '' || $password === '' || !in_array($role, ['admin', 'author'])) {
        $register_error = 'لطفا همه فیلدها را به درستی پر کنید.';
    } else {
        if ($role === 'admin' && $admin_key !== 'adminkey') {
            $register_error = 'کلید امنیتی ادمین اشتباه است.';
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetchColumn() > 0) {
                $register_error = 'نام کاربری قبلا ثبت شده است.';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                if ($insert->execute([$username, $hashed_password, $role])) {
                    $register_success = 'ثبت نام با موفقیت انجام شد.';
                } else {
                    $register_error = 'خطایی در ثبت نام رخ داد.';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>ورود / ثبت نام</title>
    <style>
    body {
	  font-family: Tahoma, sans-serif;
	  direction: rtl;
	  background: #f7f7f7;
	}
	.container {
	  width: 380px;
	  margin: 70px auto auto auto;
	  background: white;
	  padding: 20px;
	  border-radius: 5px;
	  box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
	}
	.tabs {
	  display: flex;
	  justify-content: center;
	  margin-bottom: 20px;
	}
	.tab {
	  flex: 1;
	  text-align: center;
	  padding: 10px 0;
	  cursor: pointer;
	  border-bottom: 2px solid transparent;
	  font-weight: bold;
	  color: #555;
	}
	.tab.active {
	  border-color: #007bff;
	  color: #007bff;
	}
	form {
	  display: none;
	}
	form.active {
	  display: block;
	}
	input[type="text"],
	input[type="password"],
	select {
	  width: 100%;
	  padding: 8px;
	  margin: 10px 0;
	  box-sizing: border-box;
	  border: 1px solid #ccc;
	  border-radius: 3px;
	}
	button {
	  width: 100%;
	  padding: 10px;
	  background: #007bff;
	  color: white;
	  border: none;
	  border-radius: 3px;
	  cursor: pointer;
	}
	button:hover {
	  background: #0056b3;
	}
	.message {
	  margin-top: 15px;
	  text-align: center;
	  font-size: 0.9em;
	}
	.error {
	  color: red;
	}
	.success {
	  color: green;
	}
	label {
	  display: block;
	  margin-top: 10px;
	  font-weight: bold;
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
    </style>
</head>
<body>

<div class="container">
    <div class="tabs">
        <div id="tab-login" class="tab <?= $active_tab === 'login' ? 'active' : '' ?>">ورود</div>
        <div id="tab-register" class="tab <?= $active_tab === 'register' ? 'active' : '' ?>">ثبت نام</div>
    </div>

    <form id="form-login" class="<?= $active_tab === 'login' ? 'active' : '' ?>" method="post" action="">
        <input type="hidden" name="action" value="login">
        <input type="text" name="login_username" placeholder="نام کاربری" required>
        <input type="password" name="login_password" placeholder="رمز عبور" required>
        <button type="submit">ورود</button>
        <?php if ($login_error): ?>
            <div class="message error"><?= htmlspecialchars($login_error) ?></div>
        <?php endif; ?>
    </form>

    <form id="form-register" class="<?= $active_tab === 'register' ? 'active' : '' ?>" method="post" action="">
        <input type="hidden" name="action" value="register">
        <input type="text" name="register_username" placeholder="نام کاربری" required value="<?= htmlspecialchars($_POST['register_username'] ?? '') ?>">
        <input type="password" name="register_password" placeholder="رمز عبور" required>
        <label for="register_role">نقش کاربر</label>
        <select id="register_role" name="register_role" required>
            <option value="">نقش را انتخاب کنید</option>
            <option value="admin" <?= ($_POST['register_role'] ?? '') === 'admin' ? 'selected' : '' ?>>ادمین</option>
            <option value="author" <?= ($_POST['register_role'] ?? '') === 'author' ? 'selected' : '' ?>>نویسنده</option>
        </select>
        <label for="admin_key">کلید امنیتی</label>
        <input type="password" id="admin_key" name="admin_key" placeholder="کلید امنیتی (فقط برای ادمین)">
        <button type="submit">ثبت نام</button>
        <?php if ($register_error): ?>
            <div class="message error"><?= htmlspecialchars($register_error) ?></div>
        <?php elseif ($register_success): ?>
            <div class="message success"><?= htmlspecialchars($register_success) ?></div>
        <?php endif; ?>
    </form>
</div>
<div style="text-align: center; margin-top: 15px;">
	<a href="index.php" class="btn-back">بازگشت به صفحه اصلی</a>
</div>

<script>
    const tabLogin = document.getElementById('tab-login');
    const tabRegister = document.getElementById('tab-register');
    const formLogin = document.getElementById('form-login');
    const formRegister = document.getElementById('form-register');

    tabLogin.addEventListener('click', () => {
        tabLogin.classList.add('active');
        tabRegister.classList.remove('active');
        formLogin.classList.add('active');
        formRegister.classList.remove('active');
    });

    tabRegister.addEventListener('click', () => {
        tabRegister.classList.add('active');
        tabLogin.classList.remove('active');
        formRegister.classList.add('active');
        formLogin.classList.remove('active');
    });
</script>

</body>
</html>
