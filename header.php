<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (($profile['status'] ?? 'active') === 'banned') {
    session_destroy();
    header("Location: login.php");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>StudentJob Finder</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background: #ffffff; color: #0f172a; }
        header { background: #ffffff; padding: 14px 50px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1.5px solid #e2e8f0; position: sticky; top: 0; z-index: 100; }
        .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: #1d4ed8; font-size: 20px; font-weight: 700; }
        .nav-links { display: flex; align-items: center; gap: 34px; }
        .nav-links a { text-decoration: none; color: #0f172a; font-weight: 800; font-size: 14px; padding-bottom: 4px; border-bottom: 2.5px solid transparent; }
        .nav-links a.active { color: #1d4ed8; border-bottom-color: #1d4ed8; }
        .nav-right { display: flex; align-items: center; gap: 16px; }
        .avatar-link { display: flex; align-items: center; gap: 8px; text-decoration: none; color: #0f172a; font-weight: 700; font-size: 13px; padding: 4px 10px; border-radius: 20px; transition: background 0.2s; }
        .avatar-link:hover { background: #f1f5f9; }
        .avatar-circle { width: 36px; height: 36px; border-radius: 50%; background: #bae6fd; border: 2px solid #0284c7; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #0369a1; font-size: 15px; overflow: hidden; }
        .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }
        .logout-btn { font-size: 12px; color: #dc2626; text-decoration: none; font-weight: 700; border: 1px solid #fecaca; padding: 5px 10px; border-radius: 6px; }
        .toast { padding: 14px; text-align: center; font-weight: 700; font-size: 15px; background: #dcfce7; color: #166534; border-bottom: 1px solid #86efac; }
        .page-wrap { max-width: 960px; margin: 30px auto; padding: 0 25px; }
        .figma-job-card { display: flex; align-items: center; justify-content: space-between; border: 1.5px solid #94a3b8; border-radius: 12px; padding: 18px 26px; margin-bottom: 16px; background: #ffffff; box-shadow: 0 3px 8px rgba(0,0,0,0.05); }
        .job-left { display: flex; align-items: center; gap: 20px; }
        .job-icon-box { width: 58px; height: 58px; border-radius: 10px; border: 1.5px solid #cbd5e1; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 800; flex-shrink: 0; }
        .job-meta h3 { margin: 0 0 3px; font-size: 17px; font-weight: 800; }
        .job-meta .company { font-size: 13px; font-weight: 600; color: #334155; }
        .job-meta .location { font-size: 11.5px; font-weight: 700; color: #475569; margin-bottom: 8px; }
        .setup-pill { background: #e2e8f0; color: #1e293b; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 4px; margin-right: 10px; }
        .salary-text { font-size: 12.5px; font-weight: 700; color: #334155; }
        .btn-apply-figma { display: inline-flex; align-items: center; justify-content: center; width: 145px; height: 36px; background: #5b84d7; color: white; border: none; border-radius: 6px; font-size: 15px; font-weight: 700; text-decoration: none; cursor: pointer; }
        .btn-apply-figma:hover { background: #3b66c4; }
        .btn-applied { width: 145px; height: 36px; background: #cbd5e1; color: #475569; border: none; border-radius: 6px; font-size: 14px; font-weight: 700; }
    </style>
</head>
<body>
<header>
    <a href="client_dashboard.php" class="brand"><span>🎓</span><span>StudentJob Finder</span></a>
    <nav class="nav-links">
        <a href="client_dashboard.php" class="<?= $current_page === 'client_dashboard.php' ? 'active' : '' ?>">HOME</a>
        <a href="jobs.php" class="<?= $current_page === 'jobs.php' ? 'active' : '' ?>">JOBS</a>
        <a href="applications.php" class="<?= $current_page === 'applications.php' ? 'active' : '' ?>">APPLICATIONS</a>
    </nav>
    <div class="nav-right">
        <a href="applications.php" style="text-decoration:none; font-size:20px;">🔔</a>
        <a href="profile.php" class="avatar-link" title="Edit Personal Info">
            <div class="avatar-circle">
                <?php if (!empty($profile['profile_pic']) && file_exists($profile['profile_pic'])): ?>
                    <img src="<?= htmlspecialchars($profile['profile_pic']) ?>" alt="Profile">
                <?php else: ?>
                    <?= strtoupper(substr($profile['full_name'], 0, 1)) ?>
                <?php endif; ?>
            </div>
            <span><?= htmlspecialchars($profile['full_name']) ?></span>
        </a>
        <a href="login.php?logout=1" class="logout-btn">Log out</a>
    </div>
</header>