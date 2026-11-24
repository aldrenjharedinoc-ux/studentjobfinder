<?php
require 'db.php';
$saved = false;
$error = '';

if (isset($_POST['update_profile']) && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $birth_date = !empty($_POST['birth_date']) ? $_POST['birth_date'] : null;

    // Fetch current profile pic path
    $stmt = $pdo->prepare("SELECT profile_pic FROM users WHERE user_id = ?");
    $stmt->execute([$uid]);
    $pic_path = $stmt->fetchColumn();

    // Handle Profile Picture Upload if selected
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            if (!is_dir('uploads')) mkdir('uploads', 0777, true);
            $new_pic = 'uploads/avatar_' . $uid . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $new_pic)) {
                $pic_path = $new_pic;
            }
        } else {
            $error = "Profile photo must be JPG, PNG, GIF, or WEBP.";
        }
    }

    if (!$error) {
        $stmt = $pdo->prepare("UPDATE users SET full_name=?, school=?, phone_number=?, birth_date=?, address=?, course_major=?, year_level=?, skills=?, bio=?, profile_pic=? WHERE user_id=?");
        $stmt->execute([
            $_POST['full_name'], $_POST['school'], $_POST['phone_number'],
            $birth_date, $_POST['address'], $_POST['course_major'],
            $_POST['year_level'], $_POST['skills'], $_POST['bio'],
            $pic_path, $uid
        ]);
        $_SESSION['full_name'] = $_POST['full_name'];
        $saved = true;
    }
}

require 'header.php';
?>
<style>
    .profile-box { border: 1.5px solid #cbd5e1; border-radius: 14px; padding: 30px; background: #ffffff; box-shadow: 0 4px 14px rgba(0,0,0,0.04); }
    .photo-header { display: flex; align-items: center; gap: 20px; padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; }
    .big-avatar { width: 82px; height: 82px; border-radius: 50%; background: #bae6fd; border: 3px solid #0284c7; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 800; color: #0369a1; overflow: hidden; flex-shrink: 0; }
    .big-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .field-lbl { display: block; font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 4px; }
    .field-inp { width: 100%; padding: 10px 12px; border: 1px solid #94a3b8; border-radius: 8px; font-size: 14px; margin-bottom: 12px; }
</style>

<?php if ($saved): ?>
    <div class="toast">Personal information updated successfully!</div>
<?php endif; ?>
<?php if ($error): ?>
    <div style="background:#fee2e2; color:#b91c1c; padding:12px; text-align:center; font-weight:700;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="page-wrap" style="max-width:680px;">
    <h2 style="margin:0 0 18px; font-size:24px; font-weight:800;">Edit Personal Information</h2>

    <form method="POST" enctype="multipart/form-data" class="profile-box">
        <div class="photo-header">
            <div class="big-avatar">
                <?php if (!empty($profile['profile_pic']) && file_exists($profile['profile_pic'])): ?>
                    <img src="<?= htmlspecialchars($profile['profile_pic']) ?>" alt="Profile Photo">
                <?php else: ?>
                    <?= strtoupper(substr($profile['full_name'], 0, 1)) ?>
                <?php endif; ?>
            </div>
            <div>
                <label class="field-lbl">Profile Picture (Choose File)</label>
                <input type="file" name="profile_pic" accept="image/*" style="font-size:13px;">
                <p style="margin:4px 0 0; font-size:11.5px; color:#64748b;">JPG, PNG, or WEBP</p>
            </div>
        </div>

        <div class="form-grid">
            <div>
                <label class="field-lbl">Full Name</label>
                <input class="field-inp" type="text" name="full_name" value="<?= htmlspecialchars($profile['full_name']) ?>" required>
            </div>
            <div>
                <label class="field-lbl">School</label>
                <input class="field-inp" type="text" name="school" value="<?= htmlspecialchars($profile['school']) ?>" required>
            </div>
            <div>
                <label class="field-lbl">Phone Number</label>
                <input class="field-inp" type="text" name="phone_number" value="<?= htmlspecialchars($profile['phone_number'] ?? '') ?>" placeholder="09XX-XXX-XXXX">
            </div>
            <div>
                <label class="field-lbl">Date of Birth (MM/DD/YYYY)</label>
                <input class="field-inp" type="date" name="birth_date" value="<?= htmlspecialchars($profile['birth_date'] ?? '') ?>">
            </div>
        </div>

        <label class="field-lbl">Address</label>
        <input class="field-inp" type="text" name="address" value="<?= htmlspecialchars($profile['address'] ?? '') ?>" placeholder="e.g. Cordova, Cebu">

        <div class="form-grid">
            <div>
                <label class="field-lbl">Course / Major</label>
                <input class="field-inp" type="text" name="course_major" value="<?= htmlspecialchars($profile['course_major'] ?? '') ?>" placeholder="e.g. BS Information Technology">
            </div>
            <div>
                <label class="field-lbl">Year Level</label>
                <input class="field-inp" type="text" name="year_level" value="<?= htmlspecialchars($profile['year_level'] ?? '') ?>" placeholder="e.g. 3rd Year">
            </div>
        </div>

        <label class="field-lbl">Skills</label>
        <input class="field-inp" type="text" name="skills" value="<?= htmlspecialchars($profile['skills'] ?? '') ?>" placeholder="HTML, CSS, PHP, Figma">

        <label class="field-lbl">Short Bio</label>
        <textarea class="field-inp" name="bio" rows="3" placeholder="Tell employers about yourself..."><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>

        <button type="submit" name="update_profile" class="btn-apply-figma" style="width:100%; height:42px; font-size:15px;">Save Personal Info</button>
    </form>
</div>
</body>
</html>