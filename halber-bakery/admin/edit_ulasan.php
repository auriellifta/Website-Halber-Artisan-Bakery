<?php
require '../backend/koneksi.php';
require '../backend/auth.php';
cek_login_admin();

$id = intval($_GET['id'] ?? 0);
$pesan = '';

$stmt = mysqli_prepare($koneksi, "SELECT * FROM ulasan WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$ulasan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$ulasan) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $rating   = intval($_POST['rating'] ?? 0);
    $komentar = trim($_POST['komentar'] ?? '');

    if ($nama !== '' && $rating >= 1 && $rating <= 5 && $komentar !== '') {
        $stmt2 = mysqli_prepare($koneksi, "UPDATE ulasan SET nama = ?, rating = ?, komentar = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt2, "sisi", $nama, $rating, $komentar, $id);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);
        header('Location: dashboard.php?status=updated');
        exit;
    } else {
        $pesan = 'Semua kolom wajib diisi dengan benar (rating 1-5).';
        $ulasan['nama']     = $nama;
        $ulasan['rating']   = $rating;
        $ulasan['komentar'] = $komentar;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Ulasan – Halber Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin-style.css">
</head>
<body>

<header class="admin-topbar">
  <div class="admin-brand">
    <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" width="28" height="28">
      <path d="M9 24 Q9 32 18 32 Q27 32 27 24" stroke="#c9a84c" stroke-width="2.5" fill="none" stroke-linecap="round"/>
      <path d="M13 22 Q13 14 18 13 Q23 14 23 22" fill="#c9a84c"/>
      <path d="M14 10 Q14 6 18 6 Q22 6 22 10" stroke="#c9a84c" stroke-width="1.5" fill="none"/>
    </svg>
    <div>
      <div class="admin-brand-text">HALBER</div>
      <span>Admin Dashboard</span>
    </div>
  </div>
  <div class="admin-user">
    <span>Halo, <strong><?php echo htmlspecialchars($_SESSION['admin_nama']); ?></strong></span>
    <a href="logout.php" class="btn-logout" onclick="return confirm('Keluar dari dashboard?');">Logout</a>
  </div>
</header>

<main class="admin-main admin-main-narrow">

  <a href="dashboard.php" class="back-link">Kembali ke daftar ulasan</a>
  <h1 class="admin-title">Edit Ulasan</h1>

  <?php if ($pesan): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($pesan); ?></div>
  <?php endif; ?>

  <div class="form-box">
    <form method="POST" action="edit_ulasan.php?id=<?php echo $id; ?>">
      <div class="form-group">
        <label for="nama">Nama Pelanggan</label>
        <input type="text" id="nama" name="nama" maxlength="100" required value="<?php echo htmlspecialchars($ulasan['nama']); ?>">
      </div>
      <div class="form-group">
        <label for="rating">Rating</label>
        <select id="rating" name="rating" required>
          <?php for ($b = 5; $b >= 1; $b--): ?>
            <option value="<?php echo $b; ?>" <?php echo intval($ulasan['rating']) === $b ? 'selected' : ''; ?>><?php echo str_repeat('★', $b) . str_repeat('☆', 5 - $b); ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="komentar">Isi Ulasan</label>
        <textarea id="komentar" name="komentar" rows="5" required><?php echo htmlspecialchars($ulasan['komentar']); ?></textarea>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn-primary">Simpan Perubahan</button>
        <a href="dashboard.php" class="btn-cancel">Batal</a>
      </div>
    </form>
  </div>

</main>

</body>
</html>
