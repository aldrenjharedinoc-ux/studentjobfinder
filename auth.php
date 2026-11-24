<?php
require 'db.php';
$message = '';
$msg_type = 'error';

$page = $_GET['page'] ?? 'login';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: auth.php");
    exit;
}

if (isset($_POST['signup'])) {
    $page = 'register';
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $school = trim($_POST['school']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, school) VALUES (?, ?, ?, ?)");
            $stmt->execute([$full_name, $email, $hash, $school]);
            $message = "Account created! Please log in below.";
            $msg_type = 'success';
            $page = 'login';
        } catch (PDOException $e) {
            $message = "Email is already registered.";
        }
    }
}

if (isset($_POST['login'])) {
    $page = 'login';
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: client_dashboard.php");
        }
        exit;
    } else {
        $message = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>StudentJob Finder - <?= $page === 'register' ? 'Create Account' : 'Login' ?></title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffffff;
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }
        .main-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 55px;
            max-width: 1050px;
            width: 100%;
        }

        /* Left Blue-to-Beach Card */
        .left-card {
            width: 310px;
            height: 540px;
            border-radius: 14px;
            background: 
                linear-gradient(180deg, rgba(2, 132, 199, 0.96) 0%, rgba(14, 165, 233, 0.88) 52%, rgba(56, 189, 248, 0.25) 75%, rgba(0, 0, 0, 0.15) 100%),
                url('https://unsplash.com') bottom center / cover no-repeat;
            color: white;
            padding: 55px 28px 35px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            box-shadow: 0 8px 24px rgba(2, 132, 199, 0.18);
            flex-shrink: 0;
        }
        .left-card svg {
            width: 95px;
            height: 75px;
            margin-bottom: 10px;
        }
        .left-card h1 {
            font-size: 23px;
            font-weight: 700;
            margin: 5px 0 8px;
            letter-spacing: -0.3px;
        }
        .left-card .tagline {
            font-family: Georgia, serif;
            font-style: italic;
            font-size: 13px;
            opacity: 0.95;
            margin: 0 0 42px;
        }
        .left-card .desc {
            font-family: Georgia, serif;
            font-size: 13.5px;
            line-height: 1.45;
            opacity: 0.95;
            max-width: 235px;
        }

        /* Right Side: Login Box */
        .login-box {
            width: 560px;
            border: 1.5px solid #334155;
            border-radius: 8px;
            padding: 36px 45px 25px;
            background: #ffffff;
        }
        .login-header h2 {
            color: #1d4ed8;
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 4px;
        }
        .login-header p {
            color: #0f172a;
            font-size: 21px;
            font-weight: 700;
            margin: 0 0 28px;
        }
        .login-form-inner {
            max-width: 310px;
            margin: 0 auto;
        }

        /* Right Side: Register Box */
        .register-box {
            width: 460px;
            padding: 10px 20px;
        }
        .register-form-inner {
            max-width: 320px;
            margin: 0 auto;
        }

        label.field-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 3px;
            padding-left: 4px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            height: 38px;
            padding: 0 16px;
            margin-bottom: 14px;
            border: 1px solid #94a3b8;
            border-radius: 20px;
            font-size: 14px;
            color: #1e293b;
            background: #ffffff;
            outline: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.06);
        }
        .login-box input[type="email"],
        .login-box input[type="password"] {
            text-align: center;
            font-size: 16px;
            color: #475569;
        }
        input:focus, select:focus {
            border-color: #2563eb;
        }

        .login-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            font-weight: 600;
            color: #1e293b;
            margin: -4px 4px 18px;
        }
        .login-options label {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }
        .login-options a {
            color: #1e293b;
            text-decoration: none;
        }
        .login-options a:hover { text-decoration: underline; }

        .btn-figma {
            display: block;
            width: 175px;
            height: 36px;
            margin: 8px auto 0;
            background-color: #7882c8;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-figma:hover {
            background-color: #636eb8;
        }

        .login-footer {
            text-align: right;
            margin-top: 38px;
            font-size: 12px;
            color: #1e293b;
        }
        .login-footer a {
            color: #0f172a;
            font-weight: 700;
            text-decoration: underline;
        }
        .register-footer {
            text-align: center;
            margin-top: 12px;
            font-size: 11.5px;
            color: #1e293b;
        }
        .register-footer a {
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
        }
        .register-footer a:hover { text-decoration: underline; }

        .alert-error { background: #fee2e2; color: #b91c1c; padding: 8px 12px; border-radius: 6px; margin-bottom: 14px; font-size: 12.5px; text-align: center; }
        .alert-success { background: #dcfce7; color: #15803d; padding: 8px 12px; border-radius: 6px; margin-bottom: 14px; font-size: 12.5px; text-align: center; }

        @media (max-width: 850px) {
            .main-container { flex-direction: column; gap: 25px; }
            .login-box, .register-box { width: 100%; }
        }
    </style>
</head>
<body>

<div class="main-container">
    <div class="left-card">
        <svg viewBox="0 0 100 75" fill="none" stroke="#0f172a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="50,10 10,28 50,46 90,28" />
            <path d="M26,35 L26,53 C26,53 37,61 50,61 C63,61 74,53 74,53 L74,35" />
            <line x1="84" y1="31" x2="84" y2="54" />
            <polygon points="84,54 80,64 88,64" />
        </svg>

        <h1>StudentJob Finder</h1>
        <p class="tagline">Your future,Our Priority</p>
        <p class="desc">Find the right job, gain experience, and build your career.</p>
    </div>

    <?php if ($page === 'login'): ?>
        <div class="login-box">
            <div class="login-header">
                <h2>Welcome Back!</h2>
                <p>Login to your account.</p>
            </div>

            <div class="login-form-inner">
                <?php if ($message): ?>
                    <div class="<?= $msg_type === 'success' ? 'alert-success' : 'alert-error' ?>">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="auth.php?page=login">
                    <label class="field-label">Email address</label>
                    <input type="email" name="email" required placeholder="Email">

                    <label class="field-label">Password</label>
                    <input type="password" name="password" required placeholder="Password">

                    <div class="login-options">
                        <label><input type="checkbox" name="remember"> Remember me</label>
                        <a href="#" onclick="alert('Contact admin@studentjobfinder.com to reset your password.'); return false;">Forgot Password?</a>
                    </div>

                    <button type="submit" name="login" class="btn-figma">Login</button>
                </form>
            </div>

