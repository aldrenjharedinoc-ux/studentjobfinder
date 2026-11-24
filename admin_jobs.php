<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
        <h3 style="margin:0;">📍 Area Jobs Management & Approval (<?= count($jobs) ?>)</h3>
        <span style="font-size:12.5px; color:#64748b;">Only <strong>Approved</strong> jobs show on the student website</span>
    </div>

    <form method="POST" class="add-job-form">
        <input type="text" name="title" required placeholder="Job Title (e.g. Web Dev)">
        <input type="text" name="company_name" required placeholder="Company Name">
        <input type="text" name="location" required placeholder="Area (e.g. CORDOVA CEBU)">
        <select name="category" required>
            <option value="Web Developer">Web Developer</option>
            <option value="Virtual Assistant">Virtual Assistant</option>
            <option value="IT Support">IT Support</option>
            <option value="Designer">Designer</option>
        </select>
        <select name="work_setup" required>
            <option value="Part-time">Part-time</option>
            <option value="Full-time">Full-time</option>
            <option value="Internship">Internship</option>
        </select>
        <input type="text" name="salary_range" required placeholder="Salary (e.g. P15,000)">
        <button type="submit" name="add_job" class="btn">+ Add Job for Approval</button>
    </form>

    <div class="jobs-grid">
        <?php foreach ($jobs as $j): $is_approved = (($j['approval_status'] ?? 'Approved') === 'Approved'); ?>
            <div class="area-job-card <?= !$is_approved ? 'pending-card' : '' ?>">
                <div>
                    <span class="loc-tag">📍 <?= htmlspecialchars($j['location']) ?></span>
                    <span class="status-pill pill-<?= $is_approved ? 'Approved' : 'Pending' ?>">
                        <?= $is_approved ? '● Live on Website' : '⏳ Pending Approval' ?>
                    </span>
                    <h4 style="margin:8px 0 4px; font-size:16px;"><?= htmlspecialchars($j['title']) ?></h4>
                    <div style="font-size:13px; font-weight:600; color:#334155;"><?= htmlspecialchars($j['company_name']) ?></div>
                    <p style="font-size:12px; color:#475569; margin:8px 0;">
                        💼 <?= htmlspecialchars($j['work_setup']) ?> • <?= htmlspecialchars($j['category']) ?><br>
                        💰 <?= htmlspecialchars($j['salary_range']) ?> • <strong><?= $j['total_applicants'] ?> Applicant(s)</strong>
                    </p>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #e2e8f0; padding-top:10px; margin-top:8px;">
                    <?php if (!$is_approved): ?>
                        <a href="?approve_job=<?= $j['job_id'] ?>" class="btn btn-approve">✓ Approve & Publish</a>
                    <?php else: ?>
                        <a href="?unpublish_job=<?= $j['job_id'] ?>" class="btn btn-hide">Hide from Site</a>
                    <?php endif; ?>
                    <a href="?delete_job=<?= $j['job_id'] ?>" onclick="return confirm('Delete this job?');" style="font-size:12px; color:#dc2626; font-weight:700; text-decoration:none;">Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>