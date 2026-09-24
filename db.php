<?php
// db.php
$db_file = __DIR__ . '/nlc_portal.db';

try {
    $isNewDb = !file_exists($db_file);
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    if ($isNewDb) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                full_name TEXT NOT NULL
            );

            CREATE TABLE IF NOT EXISTS site_settings (
                setting_key TEXT PRIMARY KEY,
                setting_value TEXT NOT NULL
            );

            CREATE TABLE IF NOT EXISTS nav_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                category TEXT NOT NULL,
                title TEXT NOT NULL,
                url TEXT NOT NULL,
                target TEXT DEFAULT '_self',
                display_order INTEGER DEFAULT 0
            );

            CREATE TABLE IF NOT EXISTS portrait_slides (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                image_path TEXT NOT NULL,
                display_order INTEGER DEFAULT 0
            );

            CREATE TABLE IF NOT EXISTS hero_slides (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT DEFAULT '',
                caption TEXT DEFAULT '',
                image_path TEXT NOT NULL,
                display_order INTEGER DEFAULT 0
            );

            CREATE TABLE IF NOT EXISTS activities (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT NOT NULL,
                display_order INTEGER DEFAULT 0
            );
        ");

        // Seed default admin
        $adminStmt = $pdo->prepare("INSERT INTO users (username, password_hash, full_name) VALUES (?, ?, ?)");
        $adminStmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT), 'System Administrator']);

        // Seed all index.html content
        $settings = [
            'org_name' => 'NLC India Limited',
            'org_subtitle' => "('Navratna' - Government of India Enterprise)",
            'org_dept' => 'CORPORATE COMMUNICATIONS DEPARTMENT',
            'logo_img' => 'images/logo.png',
            'teledir_link' => 'teledir.pdf',
            'about_heading' => 'About Corporate Communications',
            'about_mascot' => 'images/Lion_Mascot.png',
            'about_text' => 'The Corporate Communications Department manages internal and external communication activities of NLC India Limited. It handles media relations, public relations, corporate branding, and communication with stakeholders and the public.',
            'contact_bsnl' => '04142 – 252257',
            'contact_intercom' => '60278',
            'contact_email' => 'nlcil.ccdept@nlcindia.in',
            'contact_address' => 'Corporate Communications Department, Museum Road, Block-2, Neyveli Township, Neyveli, Cuddalore Dt., Tamil Nadu - 607801',
            'contact_map_url' => 'https://maps.app.goo.gl/rkcXg87iD3Fo8mxA7',
            'footer_social_fb' => 'https://www.facebook.com/nlcindialtd/',
            'footer_social_li' => 'https://in.linkedin.com/in/nlc-india-limited-nlcil-148a53210?trk=public_post_feed-actor-name',
            'footer_social_ig' => 'https://www.instagram.com/nlcindialtd',
            'footer_social_yt' => 'https://www.youtube.com/@NLCIndiaLimited',
            'footer_social_x' => 'https://x.com/nlcindialimited',
            'footer_social_th' => 'https://www.threads.com/@nlcindialtd',
            'footer_social_wa' => 'https://whatsapp.com/channel/0029Vb6TTz0FCCobtT1YD617',
            'footer_copyright' => '© 2026 NLCIL Corporate Communications Department'
        ];
        $sStmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)");
        foreach ($settings as $k => $v) {
            $sStmt->execute([$k, $v]);
        }

        // Seed Portrait Slides
        $portraits = [
            ['CMD', 'images/cmd.png', 1],
            ['Director HR', 'images/dhr.png', 2],
            ['General Manager', 'images/gm.png', 3]
        ];
        $pStmt = $pdo->prepare("INSERT INTO portrait_slides (title, image_path, display_order) VALUES (?, ?, ?)");
        foreach ($portraits as $p) {
            $pStmt->execute($p);
        }

        // Seed Hero Slides
        $banners = [
            ['NLC Event 1', '', 'images/ccd.jpg', 1],
            ['NLC Event 2', '', 'images/nh.jpg', 2],
            ['NLC Event 3', '', 'images/ghb25.jpg', 3],
            ['NLC Event 4', '', 'images/ghb13.jpg', 4],
            ['NLC Event 5', '', 'images/pcp.jpg', 5],
            ['NLC Event 6', '', 'images/pnm.jpg', 6]
        ];
        $bStmt = $pdo->prepare("INSERT INTO hero_slides (title, caption, image_path, display_order) VALUES (?, ?, ?, ?)");
        foreach ($banners as $b) {
            $bStmt->execute($b);
        }

        // Seed Activities
        $acts = [
            ['Brand Enhancement', 'The Corporate Communications Department plays a vital role in strengthening brand visibility by strategically communicating the organization\'s values, achievements, and initiatives to internal and external stakeholders.', 1],
            ['Public Relations', 'Maintaining relationships with stakeholders and society.', 2],
            ['Media Excellence & Reputation Management', 'Shaping a strong public image through strategic press communication, media engagement, and build credibility.', 3],
            ['Corporate Events & Exhibitions', 'Organizing conferences and official programs.', 4]
        ];
        $aStmt = $pdo->prepare("INSERT INTO activities (title, description, display_order) VALUES (?, ?, ?)");
        foreach ($acts as $a) {
            $aStmt->execute($a);
        }

        // Seed Nav Items
        $navs = [
            ['divisions', 'Advertisements & Sponsorships', 'advertisement.html', '_self', 1],
            ['divisions', 'Electronic Media', 'electronic-media.html', '_self', 2],
            ['divisions', 'Events & Exhibitions', '#', '_self', 3],
            ['divisions', 'Guest Houses', 'guest-house.html', '_self', 4],
            ['divisions', 'Holiday Homes', 'http://hrapps.nlcindia.com/holidayhomes/index.cfm', '_blank', 5],
            ['divisions', 'Industrial Visits', 'industrial-visit.html', '_self', 6],
            ['divisions', 'Photography and Videography', 'photo-videography.html', '_self', 7],
            ['divisions', 'Print Media', 'print-media.html', '_self', 8],
            ['divisions', 'Tenders', 'tender-contracts.html', '_self', 9],
            ['media', 'Photos Gallery', 'photo-gallery.html', '_self', 1],
            ['media', 'E-News Papers', 'enews-papers.html', '_self', 2],
            ['media', 'PASM', 'pasm.html', '_self', 3],
            ['quick_links', 'NLCIL LOGO', 'images/logo.png', '_blank', 1],
            ['quick_links', 'NLCIL 70th YEAR LOGO', 'images/NLCIL_Special_logo 70 Years.png', '_blank', 2],
            ['quick_links', 'NLCIL NEYON MASCOT', 'images/Lion_Mascot.png', '_blank', 3],
            ['quick_links', 'NLCIL CORPORATE IDENTITY MANUAL', 'images/NLCIL-CorporateIdentity Manual.pdf', '_blank', 4],
            ['quick_links', 'NLCIL SOCIAL MEDIA QR', 'images/Social media QR.png', '_blank', 5],
            ['quick_links', 'NLCIL VISION MISSION', 'images/Vision Mission.jpeg', '_blank', 6],
            ['quick_links', 'NLCIL CORE VALUES ENGLISH', 'images/Core Values English.jpeg', '_blank', 7],
            ['quick_links', 'NLCIL THEME SONG', 'https://nlcintra.nlcindia.com/new_intra/themesongs.html', '_blank', 8],
            ['other_links', 'NLCIL Website', 'https://www.nlcindia.in/', '_blank', 1],
            ['other_links', 'TPS 1', 'http://172.16.128.53/', '_blank', 2],
            ['other_links', 'TPS I EXP', 'http://172.16.140.54/TPS1EXP/', '_blank', 3],
            ['other_links', 'TPS II', 'http://172.16.164.68/', '_blank', 4],
            ['other_links', 'TPS II EXP', 'http://172.16.170.234/', '_blank', 5],
            ['other_links', 'MINE 1 & 1A', 'http://172.16.92.52:8080/web/', '_blank', 6],
            ['other_links', 'MINE 2', 'http://172.18.6.154/', '_blank', 7],
            ['other_links', 'TA OFFICE', 'http://172.16.78.105:9090/ta/mainpage.asp', '_blank', 8],
            ['other_links', 'MM COMPLEX', 'http://172.16.104.51/', '_blank', 9],
            ['other_links', 'GENERAL HOSPITAL', 'http://172.16.120.39/hospital/', '_blank', 10],
            ['other_links', 'CSR', 'http://nlcintra.nlcindia.com/csr/csr.php', '_blank', 11],
            ['other_links', 'CORPORATE OFFICE', 'http://nlcintra.nlcindia.com/index.php?skip=cmdmsgrep', '_blank', 12],
            ['other_links', 'TOWNSHIP ADMINISTRATION', 'http://172.16.78.105:9090/ta/mainpage.asp', '_blank', 13],
            ['other_links', 'L&DC Library', 'http://ldc.nlcindia.com/tccoordination/libtd/DEFAULT.asp', '_blank', 14]
        ];
        $nStmt = $pdo->prepare("INSERT INTO nav_items (category, title, url, target, display_order) VALUES (?, ?, ?, ?, ?)");
        foreach ($navs as $n) {
            $nStmt->execute($n);
        }
    }
} catch (PDOException $e) {
    die("Database Connection Error: " . htmlspecialchars($e->getMessage()));
}