<?php
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$job_id = (int)($_GET['job_id'] ?? $_POST['job_id'] ?? 0);
$error = '';

$stmt = $pdo->prepare("SELECT * FROM jobs WHERE job_id = ?");
$stmt->execute([$job_id]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    header("Location: jobs.php");
    exit;
}

if (isset($_POST['submit_application'])) {
    if (!isset($_FILES['resume']) || $_FILES['resume']['error'] !== UPLOAD_ERR_OK) {
        $error = "Please upload your resume file (PDF, DOC, or DOCX).";
    } else {
        $ext = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['pdf', 'doc', 'docx'])) {
            $error = "Only PDF, DOC, or DOCX files are allowed.";
        } else {
            if (!is_dir('uploads')) mkdir('uploads', 0777, true);
            $dest = 'uploads/resume_' . $user_id . '_' . $job_id . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['resume']['tmp_name'], $dest)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO applications (user_id, job_id, resume_file, status) VALUES (?, ?, ?, 'Pending')");
                    $stmt->execute([$user_id, $job_id, $dest]);
                    header("Location: applications.php?applied=1");
                    exit;
                } catch (PDOException $e) {
                    $error = "You have already applied for this job.";
                }
            }
        }
    }
}

require 'header.php';
?>
<div class="page-wrap" style="max-width:560px;">
    <div style="border:1.5px solid #cbd5e1; border-radius:14px; padding:30px;">
        <div style="background:#eff6ff; padding:16px; border-radius:10px; margin-bottom:20px;">
            <h2 style="margin:0 0 4px; color:#1d4ed8;"><?= htmlspecialchars($job['title']) ?></h2>
            <p style="margin:0; font-size:13px; font-weight:600;"><?= htmlspecialchars($job['company_name']) ?> • <?= htmlspecialchars($job['location']) ?></p>
        </div>
        <?php if ($error): ?>
            <div style="background:#fee2e2; color:#b91c1c; padding:10px; border-radius:8px; margin-bottom:14px; text-align:center;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="job_id" value="<?= $job['job_id'] ?>">
            <label style="font-weight:700; font-size:13px;">Upload Your Resume (PDF, DOC, DOCX)</label>
            <div style="border:2px dashed #94a3b8; border-radius:10px; padding:24px; text-align:center; margin:10px 0 20px; background:#f8fafc;">
                <input type="file" name="resume" accept=".pdf,.doc,.docx" required>
            </div>
            <button type="submit" name="submit_application" class="btn-apply-figma" style="width:100%; height:42px;">Submit Application</button>
        </form>
    </div>
</div>
</body>
</html>