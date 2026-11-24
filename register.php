<?php
require 'db.php';
$message = '';

if (isset($_POST['signup'])) {
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
            header("Location: login.php?registered=1");
            exit;
        } catch (PDOException $e) {
            $message = "Email is already registered.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>StudentJob Finder - Create Account</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffffff;
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
            gap: 65px;
            max-width: 1050px;
            width: 100%;
        }
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
        .left-card svg { width: 95px; height: 75px; margin-bottom: 10px; }
        .left-card h1 { font-size: 23px; font-weight: 700; margin: 5px 0 8px; }
        .left-card .tagline { font-family: Georgia, serif; font-style: italic; font-size: 13px; margin: 0 0 42px; }
        .left-card .desc { font-family: Georgia, serif; font-size: 13.5px; line-height: 1.45; max-width: 235px; }
        .register-box { width: 340px; }
        label.field-label { display: block; font-size: 11px; font-weight: 700; color: #1e293b; margin-bottom: 3px; padding-left: 4px; }
        .pill-input {
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
        .btn-figma {
            display: block;
            width: 175px;
            height: 36px;
            margin: 16px auto 0;
            background-color: #7882c8;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-figma:hover { background-color: #636eb8; }
        .register-footer { text-align: center; margin-top: 12px; font-size: 11.5px; color: #1e293b; }
        .register-footer a { color: #2563eb; font-weight: 600; text-decoration: none; }
        .alert { background: #fee2e2; color: #b91c1c; padding: 8px; border-radius: 6px; margin-bottom: 14px; font-size: 12.5px; text-align: center; }
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

    <div class="register-box">
        <?php if ($message): ?>
            <div class="alert"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <label class="field-label">Full Name</label>
            <input class="pill-input" type="text" name="full_name" required placeholder="Enter your name">

            <label class="field-label">Email address</label>
            <input class="pill-input" type="email" name="email" required placeholder="you@example.com">

            <label class="field-label">Password</label>
            <input class="pill-input" type="password" name="password" required placeholder="Enter your password">

            <label class="field-label">Confirm Password</label>
            <input class="pill-input" type="password" name="confirm_password" required placeholder="Confirm your password">

            <label class="field-label">Select your School</label>
            <select class="pill-input" name="school" required>
                <option value="">Select an option</option>
                <option value="University of Cebu">University of Cebu</option>
                <option value="University of San Carlos">University of San Carlos</option>
                <option value="Cebu Technological University">Cebu Technological University</option>
                <option value="Southwestern University">Southwestern University</option>
                <option value="Cordova Public College">Cordova Public College</option>
            </select>

            <button type="submit" name="signup" class="btn-figma">Create Account</button>
        </form>

        <div class="register-footer">
            Already have an account? <a href="login.php">Log in here</a>
        </div>
    </div>
</div>
</body>
</html>