<?php
require 'db.php';
require 'header.php';

$user_id = $_SESSION['user_id'];
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');

$sql = "SELECT * FROM jobs WHERE approval_status = 'Approved'";
$params = [];
if ($search !== '') {
    $sql .= " AND (title LIKE ? OR company_name LIKE ? OR location LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($category !== '') {
    $sql .= " AND category = ?";
    $params[] = $category;
}
$sql .= " ORDER BY job_id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$applied_ids = $pdo->query("SELECT job_id FROM applications WHERE user_id = $user_id")->fetchAll(PDO::FETCH_COLUMN);
?>
<div class="page-wrap">
    <h2 style="margin:0 0 18px; font-size:24px; font-weight:800;">All Jobs</h2>

    <form method="GET" style="display:flex; gap:12px; margin-bottom:24px; flex-wrap:wrap;">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search for jobs, skills, or companies..." style="flex:1; min-width:250px; height:40px; padding:0 16px; border:1.5px solid #94a3b8; border-radius:20px;">
        <select name="category" onchange="this.form.submit()" style="height:40px; padding:0 16px; border:1.5px solid #94a3b8; border-radius:20px; font-weight:700;">
            <option value="">Job Type (All)</option>
            <option value="Web Developer" <?= $category === 'Web Developer' ? 'selected' : '' ?>>Web Developer</option>
            <option value="Virtual Assistant" <?= $category === 'Virtual Assistant' ? 'selected' : '' ?>>Virtual Assistant</option>
            <option value="IT Support" <?= $category === 'IT Support' ? 'selected' : '' ?>>IT Support</option>
            <option value="Designer" <?= $category === 'Designer' ? 'selected' : '' ?>>Designer</option>
        </select>
        <button type="submit" style="height:40px; padding:0 20px; background:#5b84d7; color:white; border:none; border-radius:20px; font-weight:700; cursor:pointer;">Search</button>
    </form>

    <?php foreach ($jobs as $job): ?>
        <div class="figma-job-card">
            <div class="job-left">
                <div class="job-icon-box" style="background:#dbeafe; color:#1d4ed8;">💼</div>
                <div class="job-meta">
                    <h3><?= htmlspecialchars($job['title']) ?></h3>
                    <div class="company"><?= htmlspecialchars($job['company_name']) ?></div>
                    <div class="location"><?= htmlspecialchars($job['location']) ?></div>
                    <div>
                        <span class="setup-pill"><?= htmlspecialchars($job['work_setup']) ?></span>
                        <span class="salary-text"><?= htmlspecialchars($job['salary_range']) ?></span>
                    </div>
                </div>
            </div>
            <?php if (in_array($job['job_id'], $applied_ids)): ?>
                <button class="btn-applied" disabled>Applied</button>
            <?php else: ?>
                <a href="apply_job.php?job_id=<?= $job['job_id'] ?>" class="btn-apply-figma">Apply</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>