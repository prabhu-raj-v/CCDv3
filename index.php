<?php
require_once 'db.php';

// Fetch all general site settings as a key-value dictionary
$settings = $pdo->query("SELECT setting_key, setting_value FROM site_settings")->fetchAll(PDO::FETCH_KEY_PAIR);

// Fetch all navigation items ordered by category and sequence
$navStmt = $pdo->query("SELECT * FROM nav_items ORDER BY display_order ASC");
$nav_items = [];
while ($row = $navStmt->fetch()) {
    $nav_items[$row['category']][] = $row;
}

// Fetch dynamic slider and carousel items
$portrait_slides = $pdo->query("SELECT * FROM portrait_slides ORDER BY display_order ASC, id ASC")->fetchAll();
$hero_slides     = $pdo->query("SELECT * FROM hero_slides ORDER BY display_order ASC, id ASC")->fetchAll();
$activities      = $pdo->query("SELECT * FROM activities ORDER BY display_order ASC, id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <title><?= htmlspecialchars($settings['org_name'] ?? 'NLC India Limited') ?></title>
  <link rel="icon" type="image/png" href="images/logo2.png">
</head>

<body>

  <!-- =============================================
       HEADER: ROW 1 = LOGO + TITLE (brand)
       HEADER: ROW 2 = NAVIGATION MENU
  ============================================== -->
  <header>

    <!-- ROW 1: Brand (logo + title), independent of menu -->
    <div class="topbar">
      <div class="container topbar-inner">
        <div class="brand">
          <img src="<?= htmlspecialchars($settings['logo_img'] ?? 'images/logo.png') ?>" alt="NLC Logo" class="logoimg" loading="lazy">
          <div class="brand-text">
            <h1 class="p1">
              <?= htmlspecialchars($settings['org_name'] ?? 'NLC India Limited') ?>
              <span class="p2"><?= htmlspecialchars($settings['org_subtitle'] ?? "('Navratna' - Government of India Enterprise)") ?></span>
            </h1>
            <p class="p3"><?= htmlspecialchars($settings['org_dept'] ?? 'CORPORATE COMMUNICATIONS DEPARTMENT') ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- ROW 2: Navigation menu (own bar, full width) -->
    <nav class="navbar">
      <div class="container navbar-inner">
        <ul class="nav-links">
          <li><a href="#portrait-slider">HOME</a></li>
          <li><a href="#about">ABOUT</a></li>

          <!-- DIVISIONS DROPDOWN -->
          <li class="dropdown">
            <a href="#services" class="dropdown-toggle">DIVISIONS<span class="arrow"></span></a>
            <ul class="submenu">
              <?php foreach ($nav_items['divisions'] ?? [] as $item): ?>
                <li><a href="<?= htmlspecialchars($item['url']) ?>" target="<?= htmlspecialchars($item['target']) ?>"><?= htmlspecialchars($item['title']) ?></a></li>
              <?php endforeach; ?>
            </ul>
          </li>

          <!-- MEDIA DROPDOWN -->
          <li class="dropdown">
            <!-- <a href="javascript:void(0)" class="dropdown-toggle">MEDIA<span class="arrow"></span></a> -->
            <ul class="submenu">
              <?php foreach ($nav_items['media'] ?? [] as $item): ?>
                <li><a href="<?= htmlspecialchars($item['url']) ?>" target="<?= htmlspecialchars($item['target']) ?>"><?= htmlspecialchars($item['title']) ?></a></li>
              <?php endforeach; ?>
            </ul>
          </li>

          <!-- QUICK LINKS & DOWNLOADS -->
          <li class="dropdown">
            <a href="javascript:void(0)" class="dropdown-toggle">QUICK LINKS & DOWNLOADS<span class="arrow"></span></a>
            <ul class="submenu">
              <?php foreach ($nav_items['quick_links'] ?? [] as $item): ?>
                <li><a href="<?= htmlspecialchars($item['url']) ?>" target="<?= htmlspecialchars($item['target']) ?>"><?= htmlspecialchars($item['title']) ?></a></li>
              <?php endforeach; ?>
            </ul>
          </li>

          <!-- OTHER LINKS (FIXED STRUCTURE) -->
          <li class="dropdown">
            <a href="javascript:void(0)" class="dropdown-toggle">OTHER LINKS<span class="arrow"></span></a>
            <ul class="submenu">
              <?php foreach ($nav_items['other_links'] ?? [] as $item): ?>
                <li><a class="dropdown-item" href="<?= htmlspecialchars($item['url']) ?>" target="<?= htmlspecialchars($item['target']) ?>" rel="noopener noreferrer"><?= htmlspecialchars($item['title']) ?></a></li>
              <?php endforeach; ?>
            </ul>
          </li>

          <li><a href="#contact-container">CONTACT US</a></li>
          <li><a href="<?= htmlspecialchars($settings['teledir_link'] ?? 'teledir.pdf') ?>" target="_blank" rel="noopener noreferrer">TELEPHONE DIRECTORY</a></li>
          <li><a href="admin/login.php" style="color: var(--accent-gold);"><i class="fas fa-lock"></i> ADMIN</a></li>
        </ul>

        <div class="menu-toggle" aria-label="Toggle menu">☰</div>
      </div>
    </nav>
  </header>

  <!-- =============================================
       HERO
  ============================================== -->
  <section id="home" class="hero">
    <section class="nlc-section">

      <!-- LEFT: Dynamic Portrait Slider -->
      <div class="profile-area">
        <div id="portrait-slider" class="portrait-slider">
          <?php foreach ($portrait_slides as $index => $pslide): ?>
            <div class="p-slide <?= $index === 0 ? 'active' : '' ?>">
              <img src="<?= htmlspecialchars($pslide['image_path']) ?>" alt="<?= htmlspecialchars($pslide['title']) ?>" loading="lazy">
            </div>
          <?php endforeach; ?>

          <div class="portrait-dots">
            <?php foreach ($portrait_slides as $index => $pslide): ?>
              <span class="p-dot <?= $index === 0 ? 'active' : '' ?>" onclick="currentPortrait(<?= $index ?>)"></span>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- CENTER: Dynamic 16:9 Card Slider -->
      <div class="slider-container">
        <div class="slider">
          <?php foreach ($hero_slides as $index => $hslide): ?>
            <div class="slide <?= $index === 0 ? 'active' : '' ?>">
              <img src="<?= htmlspecialchars($hslide['image_path']) ?>" alt="<?= htmlspecialchars($hslide['title'] ?: 'NLC Event') ?>" loading="lazy">
              <div class="slide-content">
                <?php if (!empty($hslide['caption'])): ?>
                  <h2><?= htmlspecialchars($hslide['caption']) ?></h2>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="dots">
          <?php foreach ($hero_slides as $index => $hslide): ?>
            <span class="dot <?= $index === 0 ? 'active' : '' ?>" onclick="currentSlide(<?= $index ?>)"></span>
          <?php endforeach; ?>
        </div>
      </div>

    </section>
  </section>

  <!-- =============================================
       ABOUT
  ============================================== -->
  <section class="about">
    <h2 id="about"><?= htmlspecialchars($settings['about_heading'] ?? 'About Corporate Communications') ?></h2>
    <div class="about-content">
      <img class="action-figure" src="<?= htmlspecialchars($settings['about_mascot'] ?? 'images/Lion_Mascot.png') ?>" alt="Mascot" loading="lazy">
      <p class="about-text">
        <?= nl2br(htmlspecialchars($settings['about_text'] ?? '')) ?>
      </p>
    </div>
  </section>

  <!-- =============================================
       SERVICES / ACTIVITIES
  ============================================== -->
  <section class="services">
    <h2 id="services">Activities</h2>
    <div class="service-container">
      
      <!-- Ultra Modern Carousel Engine -->
      <div class="carousel-engine" id="carouselEngine">
        <div class="carousel-track" id="carouselTrack">
          <?php foreach ($activities as $index => $act): ?>
            <div class="card <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>">
              <div class="card-glow"></div>
              <div class="card-content">
                <h3><?= htmlspecialchars($act['title']) ?></h3>
                <p><?= nl2br(htmlspecialchars($act['description'])) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- UX Controls -->
        <div class="ux-controls">
          <button class="nav-arrow prev" id="prevBtn" aria-label="Previous Activity">
            <svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
          </button>

          <div class="indicators" id="indicators"></div>

          <button class="nav-arrow next" id="nextBtn" aria-label="Next Activity">
            <svg viewBox="0 0 24 24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
          </button>
        </div>
      </div>

      <!-- Media Showcase Wrapper -->
      <div class="media-frame">
        <div class="glow-orb"></div>
        <img class="action-figure2" src="images/qr.png" alt="Portal QR" loading="lazy">
      </div>

    </div>
  </section>

  <!-- =============================================
       CONTACT US
  ============================================== -->
  <section id="contact" class="contact">
    <h2 id="contact-container">Contact Us</h2>
    <div class="contact-container">

      <!-- Combined Phone & Email Card -->
      <div class="contact-item">
        <div class="contact-sub-group">
          <i class="fas fa-phone-alt"></i>
          <div>
            <h4>Phone</h4>
            <p>
              <strong>BSNL Number:</strong> <a href="tel:<?= preg_replace('/[^0-9]/', '', $settings['contact_bsnl'] ?? '') ?>" style="text-decoration: none;"><?= htmlspecialchars($settings['contact_bsnl'] ?? '') ?></a><br>
              <strong>NLCIL Intercom:</strong> <a href="tel:<?= htmlspecialchars($settings['contact_intercom'] ?? '') ?>" style="text-decoration: none;"><?= htmlspecialchars($settings['contact_intercom'] ?? '') ?></a>
            </p>
          </div>
        </div>

        <hr class="contact-divider">

        <div class="contact-sub-group">
          <i class="fas fa-envelope"></i>
          <div>
            <h4>Email</h4>
            <p><a href="mailto:<?= htmlspecialchars($settings['contact_email'] ?? '') ?>"><?= htmlspecialchars($settings['contact_email'] ?? '') ?> <span style="color: #f59e0b;">- click to mail</span></a></p>
          </div>
        </div>
      </div>

      <!-- Address Card -->
      <div class="contact-item">
        <div class="contact-sub-group">
          <i class="fas fa-map-marker-alt"></i>
          <div>
            <h4>Address</h4>
            <p><a href="<?= htmlspecialchars($settings['contact_map_url'] ?? '#') ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($settings['contact_address'] ?? '') ?> <span style="color: #f59e0b;">- click to view on map</span></a></p>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- =============================================
       FOOTER
  ============================================== -->
  <footer>
    <p style="font-weight: 600; font-size: larger;">Follow us on social media</p>
    <div class="social-media">
      <a href="<?= htmlspecialchars($settings['footer_social_fb'] ?? 'https://www.facebook.com/nlcindialtd/') ?>" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook"></i></a>
      <a href="<?= htmlspecialchars($settings['footer_social_li'] ?? 'https://in.linkedin.com/in/nlc-india-limited-nlcil-148a53210?trk=public_post_feed-actor-name') ?>" target="_blank" rel="noopener noreferrer"><i class="fab fa-linkedin"></i></a>
      <a href="<?= htmlspecialchars($settings['footer_social_ig'] ?? 'https://www.instagram.com/nlcindialtd') ?>" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>
      <a href="<?= htmlspecialchars($settings['footer_social_yt'] ?? 'https://www.youtube.com/@NLCIndiaLimited') ?>" target="_blank" rel="noopener noreferrer"><i class="fab fa-youtube"></i></a>
      <a href="<?= htmlspecialchars($settings['footer_social_x'] ?? 'https://x.com/nlcindialimited') ?>" target="_blank" rel="noopener noreferrer"><i class="fab fa-x-twitter"></i></a>
      <a href="<?= htmlspecialchars($settings['footer_social_th'] ?? 'https://www.threads.com/@nlcindialtd') ?>" target="_blank" rel="noopener noreferrer"><i class="fab fa-threads"></i></a>
      <a href="<?= htmlspecialchars($settings['footer_social_wa'] ?? 'https://whatsapp.com/channel/0029Vb6TTz0FCCobtT1YD617') ?>" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i></a>
    </div>
    <p><?= htmlspecialchars($settings['contact_address'] ?? '') ?></p>
    <p><?= htmlspecialchars($settings['footer_copyright'] ?? '© 2026 NLCIL Corporate Communications Department') ?></p>
  </footer>

  <script src="script.js"></script>

</body>
</html>
