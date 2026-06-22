<?php
require '../backend/koneksi.php';
require '../backend/auth.php';

// kalau sudah login, langsung lempar ke dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username !== '' && $password !== '') {
        $stmt = mysqli_prepare($koneksi, "SELECT id, username, password, nama_lengkap FROM admin WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);
        $admin = mysqli_fetch_assoc($hasil);
        mysqli_stmt_close($stmt);

        // KEMBALI KE COCOKAN BIASA (Plain-text tanpa hash)
        if ($admin && $password === $admin['password']) {
            // regenerasi session id untuk mencegah session fixation
            session_regenerate_id(true);
            $_SESSION['admin_id']       = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_nama']     = $admin['nama_lengkap'] ?: $admin['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    } else {
        $error = 'Username dan password wajib diisi.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin – Halber Artisan Bakery</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin-style.css">
</head>
<body class="login-body">

<div class="login-box">
  <div class="login-logo">
    <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" width="50" height="50">
      <path d="M9 24 Q9 32 18 32 Q27 32 27 24" stroke="#c9a84c" stroke-width="2.5" fill="none" stroke-linecap="round"/>
      <path d="M13 22 Q13 14 18 13 Q23 14 23 22" fill="#c9a84c"/>
      <path d="M14 10 Q14 6 18 6 Q22 6 22 10" stroke="#c9a84c" stroke-width="1.5" fill="none"/>
      <line x1="13" y1="5" x2="12" y2="2" stroke="#c9a84c" stroke-width="1.5" stroke-linecap="round"/>
      <line x1="18" y1="4" x2="18" y2="1" stroke="#c9a84c" stroke-width="1.5" stroke-linecap="round"/>
      <line x1="23" y1="5" x2="24" y2="2" stroke="#c9a84c" stroke-width="1.5" stroke-linecap="round"/>
    </svg>
    <div>
      <div class="login-brand">HALBER</div>
      <span class="login-sub">Admin Dashboard</span>
    </div>
  </div>

  <h1>Masuk ke Dashboard</h1>
  <p class="login-desc">Kelola ulasan pelanggan Halber Artisan Bakery.</p>

  <?php if ($error): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <form method="POST" action="login.php">
    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" required autofocus maxlength="50">
    </div>
    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required maxlength="100">
    </div>
    <button type="submit" class="btn-primary">Masuk</button>
  </form>

  <a href="../frontend/index.html" class="back-link"> Kembali ke website</a>
</div>

</body>
</html>