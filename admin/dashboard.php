<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once '../db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // 1. Save Site Settings & Contacts
    if ($action === 'save_all_settings') {
        $keys = [
            'org_name', 'org_subtitle', 'org_dept', 'teledir_link',
            'about_heading', 'about_text',
            'contact_bsnl', 'contact_intercom', 'contact_email', 'contact_address', 'contact_map_url',
            'footer_social_fb', 'footer_social_li', 'footer_social_ig', 'footer_social_yt', 'footer_social_x', 'footer_social_th', 'footer_social_wa',
            'footer_copyright'
        ];
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO site_settings (setting_key, setting_value) VALUES (?, ?)");
        foreach ($keys as $k) {
            if (isset($_POST[$k])) {
                $stmt->execute([$k, trim($_POST[$k])]);
            }
        }
        $message = "All settings updated successfully.";
    }

    // 2. Upload Portrait Slide (Left Hero Slider)
    if ($action === 'upload_portrait_slide' && isset($_FILES['portrait_file'])) {
        $file = $_FILES['portrait_file'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../images/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $newName = 'portrait_' . time() . '_' . bin2hex(random_bytes(2)) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
                    $stmt = $pdo->prepare("INSERT INTO portrait_slides (title, image_path, display_order) VALUES (?, ?, ?)");
                    $stmt->execute([trim($_POST['title']), 'images/uploads/' . $newName, (int)$_POST['display_order']]);
                    $message = "Portrait photo uploaded successfully.";
                }
            } else {
                $error = "Only JPG, PNG, and WebP images are allowed.";
            }
        }
    }

    // 3. Delete Portrait Slide
    if ($action === 'delete_portrait_slide') {
        $stmt = $pdo->prepare("SELECT image_path FROM portrait_slides WHERE id = ?");
        $stmt->execute([(int)$_POST['id']]);
        $slide = $stmt->fetch();
        if ($slide) {
            if (strpos($slide['image_path'], 'images/uploads/') === 0 && file_exists('../' . $slide['image_path'])) {
                @unlink('../' . $slide['image_path']);
            }
            $del = $pdo->prepare("DELETE FROM portrait_slides WHERE id = ?");
            $del->execute([(int)$_POST['id']]);
            $message = "Portrait photo deleted.";
        }
    }

    // 4. Upload Center 16:9 Banner Slide
    if ($action === 'upload_hero_slide' && isset($_FILES['slide_file'])) {
        $file = $_FILES['slide_file'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../images/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $newName = 'hero_' . time() . '_' . bin2hex(random_bytes(2)) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
                    $stmt = $pdo->prepare("INSERT INTO hero_slides (image_path, title, caption, display_order) VALUES (?, ?, ?, ?)");
                    $stmt->execute(['images/uploads/' . $newName, trim($_POST['title'] ?? ''), trim($_POST['caption'] ?? ''), (int)$_POST['display_order']]);
                    $message = "Banner slide uploaded successfully.";
                }
            } else {
                $error = "Only JPG, PNG, and WebP images are allowed.";
            }
        }
    }

    // 5. Delete Banner Slide
    if ($action === 'delete_hero_slide') {
        $stmt = $pdo->prepare("SELECT image_path FROM hero_slides WHERE id = ?");
        $stmt->execute([(int)$_POST['id']]);
        $slide = $stmt->fetch();
        if ($slide) {
            if (strpos($slide['image_path'], 'images/uploads/') === 0 && file_exists('../' . $slide['image_path'])) {
                @unlink('../' . $slide['image_path']);
            }
            $del = $pdo->prepare("DELETE FROM hero_slides WHERE id = ?");
            $del->execute([(int)$_POST['id']]);
            $message = "Banner slide removed.";
        }
    }

    // 6. Add Activity Card
    if ($action === 'add_activity') {
        $title = trim($_POST['title'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $order = (int)($_POST['display_order'] ?? 0);
        if ($title !== '' && $desc !== '') {
            $stmt = $pdo->prepare("INSERT INTO activities (title, description, display_order) VALUES (?, ?, ?)");
            $stmt->execute([$title, $desc, $order]);
            $message = "Activity card added successfully.";
        }
    }

    // 7. Delete Activity Card
    if ($action === 'delete_activity') {
        $stmt = $pdo->prepare("DELETE FROM activities WHERE id = ?");
        $stmt->execute([(int)$_POST['id']]);
        $message = "Activity card removed.";
    }

    // 8. Add Navigation Link
    if ($action === 'add_nav_item') {
        $cat = trim($_POST['category'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $target = trim($_POST['target'] ?? '_self');
        $order = (int)($_POST['display_order'] ?? 0);
        if ($cat !== '' && $title !== '' && $url !== '') {
            $stmt = $pdo->prepare("INSERT INTO nav_items (category, title, url, target, display_order) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$cat, $title, $url, $target, $order]);
            $message = "Navigation link added successfully.";
        }
    }

    // 9. Delete Navigation Link
    if ($action === 'delete_nav_item') {
        $stmt = $pdo->prepare("DELETE FROM nav_items WHERE id = ?");
        $stmt->execute([(int)$_POST['id']]);
        $message = "Navigation link removed.";
    }
}

// Fetch current records
$settings = $pdo->query("SELECT setting_key, setting_value FROM site_settings")->fetchAll(PDO::FETCH_KEY_PAIR);
$portrait_slides = $pdo->query("SELECT * FROM portrait_slides ORDER BY display_order ASC, id ASC")->fetchAll();
$hero_slides = $pdo->query("SELECT * FROM hero_slides ORDER BY display_order ASC, id ASC")->fetchAll();
$activities = $pdo->query("SELECT * FROM activities ORDER BY display_order ASC, id DESC")->fetchAll();
$nav_items = $pdo->query("SELECT * FROM nav_items ORDER BY category, display_order ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <title>CMS Management - NLCIL</title>
</head>
<body style="background: var(--bg-light); padding: 30px 15px;">
  <div class="gh-container">

    <!-- Top Admin Bar -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 25px; flex-wrap:wrap; gap:15px;">
      <div>
        <h2 style="color:var(--primary-color); margin:0;">Complete Portal Content Manager</h2>
        <p style="color:var(--text-muted); font-size:14px; margin-top:4px;">Logged in as: <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong></p>
      </div>
      <div style="display:flex; gap:10px;">
        <a href="../index.php" target="_blank" class="gh-btn-secondary"><i class="fas fa-external-link-alt"></i> Preview Portal</a>
        <a href="logout.php" class="gh-btn-secondary" style="color:#dc2626;"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </div>

    <!-- Alert Messages -->
    <?php if ($message): ?>
      <div style="background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; padding:12px; border-radius:8px; margin-bottom:20px;">
        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div style="background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; padding:12px; border-radius:8px; margin-bottom:20px;">
        <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <!-- 1. PORTRAIT SLIDER MANAGER (LEFT HERO) -->
    <div class="gh-form-card" style="margin-bottom:30px;">
      <div class="gh-form-header">
        <h3><i class="fas fa-user-tie"></i> Portrait Slider (Left Hero Profiles)</h3>
        <p>Manage dignitary portraits (CMD, Director HR, GM) displayed in the left vertical card</p>
      </div>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="upload_portrait_slide">
        <div class="gh-form-grid">
          <div class="gh-field">
            <label>Name & Designation</label>
            <input type="text" name="title" required placeholder="e.g. CMD / Director (HR)">
          </div>
          <div class="gh-field">
            <label>Display Order (1 = first)</label>
            <input type="number" name="display_order" value="1" required>
          </div>
          <div class="gh-field full-width">
            <label>Portrait Image (Vertical 12:16 aspect ratio recommended)</label>
            <input type="file" name="portrait_file" accept="image/*" required>
          </div>
        </div>
        <button type="submit" class="gh-submit-btn"><i class="fas fa-upload"></i> Upload Portrait</button>
      </form>

      <div class="gh-table-wrapper" style="margin-top:24px;">
        <table class="gh-table">
          <thead>
            <tr>
              <th style="width:15%;">Preview</th>
              <th style="width:35%;">Designation / Title</th>
              <th style="width:30%;">File Path</th>
              <th style="width:10%;">Order</th>
              <th style="width:10%; text-align:center;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($portrait_slides as $psl): ?>
              <tr>
                <td>
                  <img src="../<?= htmlspecialchars($psl['image_path']) ?>" style="width:60px; height:80px; object-fit:cover; border-radius:6px; border:1px solid #cbd5e1;">
                </td>
                <td><strong><?= htmlspecialchars($psl['title']) ?></strong></td>
                <td style="font-size:12px; color:#64748b;"><?= htmlspecialchars($psl['image_path']) ?></td>
                <td><?= (int)$psl['display_order'] ?></td>
                <td style="text-align:center;">
                  <form method="POST" onsubmit="return confirm('Remove this portrait?');" style="margin:0;">
                    <input type="hidden" name="action" value="delete_portrait_slide">
                    <input type="hidden" name="id" value="<?= $psl['id'] ?>">
                    <button type="submit" class="btn-reject"><i class="fas fa-trash"></i></button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 2. CENTER 16:9 BANNER SLIDER -->
    <div class="gh-form-card" style="margin-bottom:30px;">
      <div class="gh-form-header">
        <h3><i class="fas fa-images"></i> Center 16:9 Banner Slider</h3>
        <p>Upload landscape event banners for the central carousel</p>
      </div>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="upload_hero_slide">
        <div class="gh-form-grid">
          <div class="gh-field">
            <label>Image File (16:9 aspect ratio)</label>
            <input type="file" name="slide_file" accept="image/*" required>
          </div>
          <div class="gh-field">
            <label>Display Order</label>
            <input type="number" name="display_order" value="1">
          </div>
          <div class="gh-field full-width">
            <label>Caption / Overlay Title (Optional)</label>
            <input type="text" name="caption" placeholder="Optional banner title">
          </div>
        </div>
        <button type="submit" class="gh-submit-btn"><i class="fas fa-upload"></i> Upload Banner</button>
      </form>

      <div class="gh-table-wrapper" style="margin-top:24px;">
        <table class="gh-table">
          <thead>
            <tr>
              <th style="width:15%;">Preview</th>
              <th style="width:40%;">Path</th>
              <th style="width:25%;">Caption</th>
              <th style="width:10%;">Order</th>
              <th style="width:10%; text-align:center;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($hero_slides as $hsl): ?>
              <tr>
                <td><img src="../<?= htmlspecialchars($hsl['image_path']) ?>" style="width:100px; height:56px; object-fit:cover; border-radius:4px;"></td>
                <td style="font-size:12px; color:#64748b;"><?= htmlspecialchars($hsl['image_path']) ?></td>
                <td><?= htmlspecialchars($hsl['caption']) ?></td>
                <td><?= (int)$hsl['display_order'] ?></td>
                <td style="text-align:center;">
                  <form method="POST" onsubmit="return confirm('Delete this banner slide?');" style="margin:0;">
                    <input type="hidden" name="action" value="delete_hero_slide">
                    <input type="hidden" name="id" value="<?= $hsl['id'] ?>">
                    <button type="submit" class="btn-reject"><i class="fas fa-trash"></i></button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 3. GENERAL & CONTACT SETTINGS -->
    <div class="gh-form-card" style="margin-bottom:30px;">
      <div class="gh-form-header">
        <h3><i class="fas fa-cogs"></i> Site Identity, About & Contact Details</h3>
      </div>
      <form method="POST">
        <input type="hidden" name="action" value="save_all_settings">
        <div class="gh-form-grid">
          <div class="gh-field">
            <label>Organization Name</label>
            <input type="text" name="org_name" value="<?= htmlspecialchars($settings['org_name'] ?? '') ?>" required>
          </div>
          <div class="gh-field">
            <label>Sub-title</label>
            <input type="text" name="org_subtitle" value="<?= htmlspecialchars($settings['org_subtitle'] ?? '') ?>">
          </div>
          <div class="gh-field full-width">
            <label>Department Heading</label>
            <input type="text" name="org_dept" value="<?= htmlspecialchars($settings['org_dept'] ?? '') ?>">
          </div>
          <div class="gh-field">
            <label>Telephone Directory Link/PDF</label>
            <input type="text" name="teledir_link" value="<?= htmlspecialchars($settings['teledir_link'] ?? '') ?>">
          </div>
          <div class="gh-field">
            <label>About Heading</label>
            <input type="text" name="about_heading" value="<?= htmlspecialchars($settings['about_heading'] ?? '') ?>" required>
          </div>
          <div class="gh-field full-width">
            <label>About Department Paragraph</label>
            <textarea name="about_text" rows="3" required><?= htmlspecialchars($settings['about_text'] ?? '') ?></textarea>
          </div>
          <div class="gh-field">
            <label>BSNL Phone</label>
            <input type="text" name="contact_bsnl" value="<?= htmlspecialchars($settings['contact_bsnl'] ?? '') ?>">
          </div>
          <div class="gh-field">
            <label>Intercom</label>
            <input type="text" name="contact_intercom" value="<?= htmlspecialchars($settings['contact_intercom'] ?? '') ?>">
          </div>
          <div class="gh-field">
            <label>Official Email</label>
            <input type="email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>" required>
          </div>
          <div class="gh-field">
            <label>Google Maps Direct URL</label>
            <input type="text" name="contact_map_url" value="<?= htmlspecialchars($settings['contact_map_url'] ?? '') ?>">
          </div>
          <div class="gh-field full-width">
            <label>Physical Office Address</label>
            <input type="text" name="contact_address" value="<?= htmlspecialchars($settings['contact_address'] ?? '') ?>">
          </div>
          <div class="gh-field">
            <label>Facebook URL</label>
            <input type="text" name="footer_social_fb" value="<?= htmlspecialchars($settings['footer_social_fb'] ?? '') ?>">
          </div>
          <div class="gh-field">
            <label>LinkedIn URL</label>
            <input type="text" name="footer_social_li" value="<?= htmlspecialchars($settings['footer_social_li'] ?? '') ?>">
          </div>
          <div class="gh-field">
            <label>Instagram URL</label>
            <input type="text" name="footer_social_ig" value="<?= htmlspecialchars($settings['footer_social_ig'] ?? '') ?>">
          </div>
          <div class="gh-field">
            <label>YouTube URL</label>
            <input type="text" name="footer_social_yt" value="<?= htmlspecialchars($settings['footer_social_yt'] ?? '') ?>">
          </div>
          <div class="gh-field">
            <label>X / Twitter URL</label>
            <input type="text" name="footer_social_x" value="<?= htmlspecialchars($settings['footer_social_x'] ?? '') ?>">
          </div>
          <div class="gh-field">
            <label>Threads URL</label>
            <input type="text" name="footer_social_th" value="<?= htmlspecialchars($settings['footer_social_th'] ?? '') ?>">
          </div>
          <div class="gh-field">
            <label>WhatsApp Channel URL</label>
            <input type="text" name="footer_social_wa" value="<?= htmlspecialchars($settings['footer_social_wa'] ?? '') ?>">
          </div>
          <div class="gh-field">
            <label>Copyright Line</label>
            <input type="text" name="footer_copyright" value="<?= htmlspecialchars($settings['footer_copyright'] ?? '') ?>">
          </div>
        </div>
        <button type="submit" class="gh-submit-btn"><i class="fas fa-save"></i> Save All Settings</button>
      </form>
    </div>

    <!-- 4. ACTIVITIES CAROUSEL -->
    <div class="gh-form-card" style="margin-bottom:30px;">
      <div class="gh-form-header">
        <h3><i class="fas fa-tasks"></i> Activities & Initiatives Cards</h3>
      </div>
      <form method="POST">
        <input type="hidden" name="action" value="add_activity">
        <div class="gh-form-grid">
          <div class="gh-field">
            <label>Activity Title</label>
            <input type="text" name="title" required placeholder="e.g. Media Excellence">
          </div>
          <div class="gh-field">
            <label>Display Order</label>
            <input type="number" name="display_order" value="0">
          </div>
          <div class="gh-field full-width">
            <label>Description Details</label>
            <textarea name="description" rows="2" required></textarea>
          </div>
        </div>
        <button type="submit" class="gh-submit-btn"><i class="fas fa-plus"></i> Add Activity Card</button>
      </form>

      <div class="gh-table-wrapper" style="margin-top:20px;">
        <table class="gh-table">
          <thead>
            <tr>
              <th style="width:10%;">Order</th>
              <th style="width:30%;">Title</th>
              <th style="width:50%;">Description</th>
              <th style="width:10%; text-align:center;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($activities as $act): ?>
              <tr>
                <td><?= (int)$act['display_order'] ?></td>
                <td><strong><?= htmlspecialchars($act['title']) ?></strong></td>
                <td style="white-space:normal;"><?= htmlspecialchars($act['description']) ?></td>
                <td style="text-align:center;">
                  <form method="POST" onsubmit="return confirm('Delete this card?');" style="margin:0;">
                    <input type="hidden" name="action" value="delete_activity">
                    <input type="hidden" name="id" value="<?= $act['id'] ?>">
                    <button type="submit" class="btn-reject"><i class="fas fa-trash"></i></button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 5. NAVIGATION SUBMENUS -->
    <div class="gh-form-card">
      <div class="gh-form-header">
        <h3><i class="fas fa-compass"></i> Navigation Dropdown Menus</h3>
      </div>
      <form method="POST">
        <input type="hidden" name="action" value="add_nav_item">
        <div class="gh-form-grid">
          <div class="gh-field">
            <label>Menu Section</label>
            <select name="category" required>
              <option value="divisions">Divisions</option>
              <option value="media">Media</option>
              <option value="quick_links">Quick Links & Downloads</option>
              <option value="other_links">Other Links</option>
            </select>
          </div>
          <div class="gh-field">
            <label>Link Title</label>
            <input type="text" name="title" required placeholder="e.g. Guest Houses">
          </div>
          <div class="gh-field">
            <label>Destination URL / File Path</label>
            <input type="text" name="url" required placeholder="e.g. guest-house.html">
          </div>
          <div class="gh-field">
            <label>Target</label>
            <select name="target">
              <option value="_self">Same Tab (_self)</option>
              <option value="_blank">New Tab (_blank)</option>
            </select>
          </div>
        </div>
        <button type="submit" class="gh-submit-btn"><i class="fas fa-plus"></i> Add Navigation Link</button>
      </form>

      <div class="gh-table-wrapper" style="margin-top:20px;">
        <table class="gh-table">
          <thead>
            <tr>
              <th>Category</th>
              <th>Title</th>
              <th>Destination URL</th>
              <th style="text-align:center;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($nav_items as $nav): ?>
              <tr>
                <td><strong><?= htmlspecialchars(strtoupper(str_replace('_', ' ', $nav['category']))) ?></strong></td>
                <td><?= htmlspecialchars($nav['title']) ?></td>
                <td><?= htmlspecialchars($nav['url']) ?></td>
                <td style="text-align:center;">
                  <form method="POST" onsubmit="return confirm('Delete this link?');" style="margin:0;">
                    <input type="hidden" name="action" value="delete_nav_item">
                    <input type="hidden" name="id" value="<?= $nav['id'] ?>">
                    <button type="submit" class="btn-reject"><i class="fas fa-trash"></i></button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</body>
</html>