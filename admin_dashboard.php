<?php
require 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit; }

$alert = '';
if (isset($_GET['ban_user'])) {
    $pdo->prepare("UPDATE users SET status = 'banned' WHERE user_id = ? AND role = 'student'")->execute([(int)$_GET['ban_user']]);
    $alert = "User has been banned!";
}
if (isset($_GET['unban_user'])) {
    $pdo->prepare("UPDATE users SET status = 'active' WHERE user_id = ? AND role = 'student'")->execute([(int)$_GET['unban_user']]);
    $alert = "User has been unbanned!";
}
if (isset($_POST['update_status'])) {
    $pdo->prepare("UPDATE applications SET status = ? WHERE application_id = ?")->execute([$_POST['status'], $_POST['application_id']]);
    $alert = "Application status updated to " . $_POST['status'] . "!";
}
if (isset($_POST['add_job'])) {
    $pdo->prepare("INSERT INTO jobs (title, company_name, location, category, work_setup, salary_range, approval_status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')")
        ->execute([trim($_POST['title']), trim($_POST['company_name']), strtoupper(trim($_POST['location'])), $_POST['category'], $_POST['work_setup'], trim($_POST['salary_range'])]);
    $alert = "Job added as Pending! Click 'Approve & Publish' to put it on the website.";
}
if (isset($_GET['approve_job'])) {
    $pdo->prepare("UPDATE jobs SET approval_status = 'Approved' WHERE job_id = ?")->execute([(int)$_GET['approve_job']]);
    $alert = "Job approved and published to the student website!";
}
if (isset($_GET['unpublish_job'])) {
    $pdo->prepare("UPDATE jobs SET approval_status = 'Pending' WHERE job_id = ?")->execute([(int)$_GET['unpublish_job']]);
    $alert = "Job hidden from student website.";
}
if (isset($_GET['delete_job'])) {
    $pdo->prepare("DELETE FROM jobs WHERE job_id = ?")->execute([(int)$_GET['delete_job']]);
    $alert = "Job deleted.";
}

$students = $pdo->query("SELECT user_id, full_name, email, school FROM users WHERE role = 'student' ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$jobs = $pdo->query("SELECT j.*, (SELECT COUNT(*) FROM applications a WHERE a.job_id = j.job_id) AS total_applicants FROM jobs j ORDER BY j.job_id DESC")->fetchAll(PDO::FETCH_ASSOC);

$selected_user = null;
$user_applications = [];
if (isset($_GET['view_user'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ? AND role = 'student'");
    $stmt->execute([(int)$_GET['view_user']]);
    $selected_user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($selected_user) {
        $stmt = $pdo->prepare("SELECT a.*, j.title, j.company_name, j.category, j.salary_range FROM applications a JOIN jobs j ON a.job_id = j.job_id WHERE a.user_id = ? ORDER BY a.applied_at DESC");
        $stmt->execute([(int)$_GET['view_user']]);
        $user_applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - StudentJob Finder</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f1f5f9; color: #0f172a; }
        nav { background: #1e293b; color: white; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; }
        .main-wrap { max-width: 1200px; margin: 25px auto; padding: 0 20px; }
        .top-grid { display: grid; grid-template-columns: 1fr 1.6fr; gap: 25px; margin-bottom: 25px; }
        .card { background: white; padding: 22px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .user-row { display: block; padding: 12px; border-bottom: 1px solid #e2e8f0; text-decoration: none; color: #0f172a; border-radius: 6px; }
        .user-row:hover, .active-user { background: #eff6ff; border-left: 4px solid #2563eb; }
        .app-box { border: 1px solid #cbd5e1; padding: 15px; border-radius: 8px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; }
        select, input { padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; }
        .btn { background: #2563eb; color: white; border: none; padding: 8px 14px; border-radius: 6px; font-weight: 700; cursor: pointer; text-decoration: none; font-size: 12px; display: inline-block; }
        .btn-approve { background: #16a34a; }
        .btn-hide { background: #f59e0b; }
        .jobs-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; margin-top: 16px; }
        .area-job-card { border: 1.5px solid #cbd5e1; border-radius: 10px; padding: 16px; background: #f8fafc; display: flex; flex-direction: column; justify-content: space-between; }
        .pending-card { border-color: #f59e0b; background: #fffbeb; }
        .loc-tag { display: inline-block; background: #dbeafe; color: #1d4ed8; font-size: 11px; font-weight: 800; padding: 3px 8px; border-radius: 4px; }
        .status-pill { font-size: 11px; font-weight: 800; padding: 3px 8px; border-radius: 4px; float: right; }
        .pill-Approved { background: #dcfce7; color: #166534; }
        .pill-Pending { background: #fef3c7; color: #b45309; }
        .add-job-form { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; background: #eff6ff; padding: 15px; border-radius: 10px; margin-top: 15px; }
    </style>
</head>
<body>
<nav>
    <h2 style="margin:0;">StudentJob Finder — Admin Portal</h2>
    <a href="login.php?logout=1" style="color: #fca5a5; text-decoration: none; font-weight: 600;">Log out</a>
</nav>
<?php if ($alert): ?>
    <div style="background: #dcfce7; color: #166534; padding: 12px; text-align: center; font-weight: bold;"><?= htmlspecialchars($alert) ?></div>
<?php endif; ?>
<div class="main-wrap">
    <div class="top-grid">
        <div class="card">
            <h3 style="margin-top:0;">All Registered Students (<?= count($students) ?>)</h3>
            <?php foreach ($students as $s): ?>
                <a href="?view_user=<?= $s['user_id'] ?>" class="user-row <?= (isset($_GET['view_user']) && $_GET['view_user'] == $s['user_id']) ? 'active-user' : '' ?>">
                    <strong><?= htmlspecialchars($s['full_name']) ?></strong><br>
                    <small style="color: #475569;"><?= htmlspecialchars($s['school']) ?> • <?= htmlspecialchars($s['email']) ?></small>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="card">
            <?php if (!$selected_user): ?>
                <h3 style="margin-top:0;">Select a User</h3>
                <p style="color: #64748b;">Click on any student on the left to inspect their personal info and manage their applications.</p>
            <?php else: ?>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
    <h2 style="margin:0; color:#1d4ed8;">
        <?= htmlspecialchars($selected_user['full_name']) ?>
        <?php if (($selected_user['status'] ?? 'active') === 'banned'): ?>
            <span style="background:#fee2e2; color:#b91c1c; font-size:12px; padding:3px 10px; border-radius:10px;">BANNED</span>
        <?php endif; ?>
    </h2>
    <?php if (($selected_user['status'] ?? 'active') !== 'banned'): ?>
        <a href="?view_user=<?= $selected_user['user_id'] ?>&ban_user=<?= $selected_user['user_id'] ?>" onclick="return confirm('Ban this user from logging in?');" class="btn" style="background:#dc2626;">🚫 Ban User</a>
    <?php else: ?>
        <a href="?view_user=<?= $selected_user['user_id'] ?>&unban_user=<?= $selected_user['user_id'] ?>" class="btn" style="background:#16a34a;">✓ Unban User</a>
    <?php endif; ?>
</div>
                <p style="line-height:1.6; font-size:14px;">
                    <strong>Email:</strong> <?= htmlspecialchars($selected_user['email']) ?> | <strong>Phone:</strong> <?= htmlspecialchars($selected_user['phone_number'] ?: 'Not set') ?><br>
                    <strong>DOB:</strong> <?= !empty($selected_user['birth_date']) ? date('m/d/Y', strtotime($selected_user['birth_date'])) : 'Not set' ?> | <strong>School:</strong> <?= htmlspecialchars($selected_user['school']) ?><br>
                    <strong>Course/Year:</strong> <?= htmlspecialchars(($selected_user['course_major'] ?: 'N/A') . ' - ' . ($selected_user['year_level'] ?: 'N/A')) ?> | <strong>Address:</strong> <?= htmlspecialchars($selected_user['address'] ?: 'Not set') ?><br>
                    <strong>Skills:</strong> <?= htmlspecialchars($selected_user['skills'] ?: 'None listed') ?> | <strong>Bio:</strong> <?= htmlspecialchars($selected_user['bio'] ?: 'No bio') ?>
                </p>
                <hr>
                <h3>Jobs Applied For (<?= count($user_applications) ?>)</h3>
                <?php foreach ($user_applications as $app): ?>
                    <div class="app-box">
                        <div>
                            <strong><?= htmlspecialchars($app['title']) ?></strong> — <?= htmlspecialchars($app['company_name']) ?><br>
                            <small>Applied: <?= date('M d, Y h:i A', strtotime($app['applied_at'])) ?></small>
                            <?php if (!empty($app['resume_file'])): ?>
                                <br><a href="<?= htmlspecialchars($app['resume_file']) ?>" target="_blank" style="font-size:12px; font-weight:700; color:#0284c7;">📄 View Resume</a>
                            <?php endif; ?>
                        </div>
                        <form method="POST" action="?view_user=<?= $selected_user['user_id'] ?>">
                            <input type="hidden" name="application_id" value="<?= $app['application_id'] ?>">
                            <select name="status">
                                <?php foreach (['Pending', 'Reviewed', 'Accepted', 'Rejected'] as $st): ?>
                                    <option value="<?= $st ?>" <?= $app['status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="update_status" class="btn">Update</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php require 'admin_jobs.php'; ?>
</div>
</body>
</html> 