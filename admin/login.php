<?php
session_start();

// Already logged in → go to dashboard
if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/../api/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        try {
            $db   = getDB();
            $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
        } catch (Exception $e) {
            $error = 'Database error: ' . $e->getMessage();
            $admin = false;
        }

        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']           = $admin['id'];
            $_SESSION['admin_username']     = $admin['username'];
            $_SESSION['admin_display_name'] = $admin['display_name'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please enter your username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login — Caroline's Place</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=DM+Sans:wght@300;400&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>

<div class="admin-login">
  <div class="admin-login__card">

    <div class="admin-login__logo">
      <div class="admin-login__logo-title">Caroline's Place</div>
      <div class="admin-login__logo-sub">Admin Portal</div>
    </div>

    <?php if ($error): ?>
      <div class="alert alert--error" style="margin-bottom:24px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label for="username" class="form-label">Username</label>
        <input
          type="text"
          id="username"
          name="username"
          class="form-input"
          value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
          autocomplete="username"
          required
        />
      </div>
      <div class="form-group" style="margin-bottom:32px;">
        <label for="password" class="form-label">Password</label>
        <input
          type="password"
          id="password"
          name="password"
          class="form-input"
          autocomplete="current-password"
          required
        />
      </div>
      <button type="submit" class="btn btn--primary btn--full">Sign In</button>
    </form>

    <p style="text-align:center;margin-top:24px;">
      <a href="../index.php" style="font-size:0.75rem;color:var(--muted);">← Return to site</a>
    </p>
  </div>
</div>

</body>
</html>
