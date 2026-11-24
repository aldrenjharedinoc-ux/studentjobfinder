<?php
require 'db.php';
require 'header.php';

$user_id = $_SESSION['user_id'];
$featured = $pdo->query("SELECT * FROM jobs LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
$applied_ids = $pdo->query("SELECT job_id FROM applications WHERE user_id = $user_id")->fetchAll(PDO::FETCH_COLUMN);
?>
<style>
    .hero { background: linear-gradient(180deg, rgba(224,242,254,0.92), rgba(14,165,233,0.45)), url('https://unsplash.com') center/cover; padding: 40px 65px; }
    .hero h1 { color: #0284c7; font-size: 32px; font-weight: 800; margin: 0 0 20px; }
    .hero-search { display: flex; background: white; border-radius: 10px; padding: 8px 16px; max-width: 760px; align-items: center; gap: 10px; }
    .hero-search input { border: none; outline: none; flex: 1; font-size: 14px; }
    .home-grid { max-width: 1120px; margin: 28px auto; padding: 0 30px; display: grid; grid-template-columns: 230px 1fr; gap: 28px; }
    .promo { background: #bae6fd; border-radius: 12px; padding: 20px; }
    .promo h3 { margin: 0 0 8px; font-size: 18px; font-weight: 800; }
    .featured-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
    .f-card { border: 1.5px solid #cbd5e1; border-radius: 10px; padding: 16px; display: flex; flex-direction: column; justify-content: space-between; }
</style>

<div class="hero">
    <h1>Find a Job That<br>Fits Your Future.</h1>
    <form action="jobs.php" method="GET" class="hero-search">
        <span>🔍</span>
        <input type="text" name="search" placeholder="Search for jobs, skills, or companies...">
        <span style="background:#bae6fd; color:#0369a1; font-size:11px; font-weight:800; padding:6px 12px; border-radius:6px;">CEBU CITY</span>
        <button type="submit" style="background:#0284c7; color:white; border:none; padding:8px 16px; border-radius:6px; font-weight:700; cursor:pointer;">Search</button>
    </form>
</div>

<div class="home-grid">
    <div>
        <div class="promo">
            <h3>Build Your<br>Future Today</h3>
            <p style="font-size:12px; font-weight:600;">Find the right jobs, gain experience, and grow your career.</p>
            <div style="font-size:48px; text-align:center; margin-top:12px;">💻🎓</div>
        </div>
    </div>

    <div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <h3 style="margin:0;">FEATURED JOBS</h3>
            <a href="jobs.php" style="color:#1d4ed8; font-weight:700; text-decoration:none;">View all</a>
        </div>
        <div class="featured-cards">
            <?php foreach ($featured as $j): ?>
                <div class="f-card">
                    <div>
                        <h4 style="margin:0 0 4px;"><?= htmlspecialchars($j['title']) ?></h4>
                        <div style="font-size:12px; color:#475569;"><?= htmlspecialchars($j['company_name']) ?></div>
                        <p style="font-size:11.5px; color:#1e293b;">📍 <?= htmlspecialchars($j['location']) ?><br>💼 <?= htmlspecialchars($j['work_setup']) ?> • <?= htmlspecialchars($j['salary_range']) ?></p>
                    </div>
                    <?php if (in_array($j['job_id'], $applied_ids)): ?>
                        <button class="btn-applied" style="width:100%;" disabled>Applied</button>
                    <?php else: ?>
                        <a href="apply_job.php?job_id=<?= $j['job_id'] ?>" class="btn-apply-figma" style="width:100%;">Apply</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</body>
</html>