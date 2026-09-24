<?php
// admin/login.php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once '../db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username !== '' && $password !== '') {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please enter both credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../style.css">
  <title>CMS Authentication - NLCIL</title>
</head>
<body style="display:flex; justify-content:center; align-items:center; min-height:100vh; background: var(--bg-light);">
  <div class="gh-form-card" style="width: 100%; max-width: 400px;">
    <div class="gh-form-header" style="text-align: center;">
      <h3>Portal Administration</h3>
      <p>Enter your credentials to manage content</p>
    </div>
    <?php if ($error): ?>
      <div style="background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; padding:10px; border-radius:6px; margin-bottom:18px; font-size:13px; text-align:center;">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>
    <form method="POST">
      <div class="gh-field" style="margin-bottom:16px;">
        <label>Username</label>
        <input type="text" name="username" required autocomplete="username">
      </div>
      <div class="gh-field" style="margin-bottom:24px;">
        <label>Password</label>
        <input type="password" name="password" required autocomplete="current-password">
      </div>
      <button type="submit" class="gh-submit-btn" style="width: 100%;">Sign In</button>
      <div style="text-align:center; margin-top: 15px;">
        <a href="../index.php" style="color:var(--text-muted); font-size:13px; text-decoration:none;">← Return to Main Portal</a>
      </div>
    </form>
  </div>
</body>
</html>