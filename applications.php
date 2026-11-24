<?php
require 'db.php';
require 'header.php';

$user_id = (int)$_SESSION['user_id'];
$status = $_GET['status'] ?? 'ALL';

$sql = "SELECT a.*, j.title, j.company_name, j.location FROM applications a JOIN jobs j ON a.job_id = j.job_id WHERE a.user_id = ?";
$params = [$user_id];
if ($status !== 'ALL') {
    $sql .= " AND a.status = ?";
    $params[] = $status;
}
$sql .= " ORDER BY a.applied_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$apps = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
    .st-tabs { display: flex; gap: 12px; margin-bottom: 22px; flex-wrap: wrap; }
    .st-tabs a { text-decoration: none; padding: 8px 24px; border-radius: 6px; font-size: 13px; font-weight: 700; border: 1.5px solid #94a3b8; color: #0f172a; }
    .st-tabs a.active { background: #5b84d7; color: white; border-color: #5b84d7; }
    .badge { padding: 6px 20px; border-radius: 6px; font-size: 13px; font-weight: 700; color: white; }
    .Pending { background: #fdba74; }
    .Reviewed { background: #60a5fa; }
    .Accepted { background: #4ade80; color: #064e3b; }
    .Rejected { background: #f87171; }
</style>

<?php if (isset($_GET['applied'])): ?>
    <div class="toast">Applied successful! Please wait for the response.</div>
<?php endif; ?>

<div class="page-wrap">
    <h2 style="margin:0 0 4px; font-size:24px; font-weight:800;">My Applications</h2>
    <p style="margin:0 0 18px; font-size:13px; color:#64748b;">Track the status of your job applications</p>

    <div class="st-tabs">
        <?php foreach (['ALL', 'Pending', 'Reviewed', 'Accepted', 'Rejected'] as $s): ?>
            <a href="applications.php?status=<?= $s ?>" class="<?= $status === $s ? 'active' : '' ?>"><?= $s ?></a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($apps)): ?>
        <p style="color:#64748b;">No applications found under <strong><?= htmlspecialchars($status) ?></strong>.</p>
    <?php else: ?>
        <?php foreach ($apps as $a): ?>
            <div class="figma-job-card">
                <div class="job-left">
                    <div class="job-icon-box" style="background:#e0e7ff; color:#4338ca;">📄</div>
                    <div class="job-meta">
                        <h3><?= htmlspecialchars($a['title']) ?></h3>
                        <div class="company"><?= htmlspecialchars($a['company_name']) ?></div>
                        <div class="location"><?= htmlspecialchars($a['location']) ?></div>
                        <small style="color:#64748b;">Applied <?= date('F j, Y', strtotime($a['applied_at'])) ?></small>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:14px;">
                    <span class="badge <?= $a['status'] ?>"><?= $a['status'] ?></span>
                    <?php if (!empty($a['resume_file'])): ?>
                        <a href="<?= htmlspecialchars($a['resume_file']) ?>" target="_blank" style="padding:6px 16px; border:1.5px solid #94a3b8; border-radius:6px; text-decoration:none; color:#0f172a; font-weight:700; font-size:13px;">View</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>