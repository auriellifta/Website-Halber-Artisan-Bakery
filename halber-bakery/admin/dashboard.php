<?php
require '../backend/koneksi.php';
require '../backend/auth.php';
cek_login_admin();

// ===== Filter & pencarian =====
$cari             = trim($_GET['cari'] ?? '');
$filter_rating_raw = $_GET['rating'] ?? '';
$filter_rating    = in_array($filter_rating_raw, ['1','2','3','4','5'], true) ? intval($filter_rating_raw) : 0;
$cari_param       = $cari !== '' ? "%$cari%" : '%';

// ===== Pagination =====
$per_halaman = 10;
$halaman     = max(1, intval($_GET['halaman'] ?? 1));
$offset      = ($halaman - 1) * $per_halaman;

$where = "WHERE (nama LIKE ? OR komentar LIKE ?) AND (rating = ? OR ? = 0)";

$stmt_total = mysqli_prepare($koneksi, "SELECT COUNT(*) AS total FROM ulasan $where");
mysqli_stmt_bind_param($stmt_total, "ssii", $cari_param, $cari_param, $filter_rating, $filter_rating);
mysqli_stmt_execute($stmt_total);
$total_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_total))['total'];
mysqli_stmt_close($stmt_total);

$total_halaman = max(1, (int) ceil($total_data / $per_halaman));
$halaman       = min($halaman, $total_halaman);
$offset        = ($halaman - 1) * $per_halaman;

$stmt = mysqli_prepare($koneksi, "SELECT * FROM ulasan $where ORDER BY tanggal DESC LIMIT ? OFFSET ?");
mysqli_stmt_bind_param($stmt, "ssiiii", $cari_param, $cari_param, $filter_rating, $filter_rating, $per_halaman, $offset);
mysqli_stmt_execute($stmt);
$data_ulasan = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

// ===== Statistik ringkas =====
$stat = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total, AVG(rating) AS rata FROM ulasan"));
$total_ulasan = (int) $stat['total'];
$rata_rating  = $stat['rata'] ? round((float) $stat['rata'], 1) : 0;

$distribusi = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
$hasil_distribusi = mysqli_query($koneksi, "SELECT rating, COUNT(*) AS jumlah FROM ulasan GROUP BY rating");
while ($d = mysqli_fetch_assoc($hasil_distribusi)) {
    $distribusi[intval($d['rating'])] = intval($d['jumlah']);
}

function buat_link_halaman($h, $cari, $filter_rating_raw) {
    $params = ['halaman' => $h];
    if ($cari !== '') $params['cari'] = $cari;
    if ($filter_rating_raw !== '') $params['rating'] = $filter_rating_raw;
    return 'dashboard.php?' . http_build_query($params);
}

$status = $_GET['status'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin – Halber Artisan Bakery</title>
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

<main class="admin-main">

  <div style="display:flex;gap:6px;margin-bottom:24px;border-bottom:2px solid var(--cream);padding-bottom:0;">
    <a href="dashboard.php" style="padding:10px 18px;font-size:13.5px;font-weight:600;color:var(--blue);border-radius:8px 8px 0 0;text-decoration:none;border-bottom:2px solid var(--blue);margin-bottom:-2px;background:rgba(26,63,160,0.04);">Ulasan</a>
    <a href="pesanan.php"   style="padding:10px 18px;font-size:13.5px;font-weight:600;color:var(--text-mid);border-radius:8px 8px 0 0;text-decoration:none;border-bottom:2px solid transparent;margin-bottom:-2px;">Pesanan</a>
  </div>

  <h1 class="admin-title">Kelola Ulasan Pelanggan</h1>

  <?php if ($status === 'deleted'): ?>
    <div class="alert alert-success">Ulasan berhasil dihapus.</div>
  <?php elseif ($status === 'updated'): ?>
    <div class="alert alert-success">Ulasan berhasil diperbarui.</div>
  <?php endif; ?>

  <!-- ===== KARTU STATISTIK ===== -->
  <section class="stat-grid">
    <div class="stat-card">
      <span class="stat-label">Total Ulasan</span>
      <span class="stat-value"><?php echo $total_ulasan; ?></span>
    </div>
    <div class="stat-card">
      <span class="stat-label">Rata-rata Rating</span>
      <span class="stat-value"><?php echo $rata_rating; ?> <span class="stat-unit">/ 5</span></span>
    </div>
    <div class="stat-card stat-card-wide">
      <span class="stat-label">Distribusi Rating</span>
      <div class="stat-bars">
        <?php for ($b = 5; $b >= 1; $b--):
          $jumlah = $distribusi[$b];
          $persen = $total_ulasan > 0 ? round(($jumlah / $total_ulasan) * 100) : 0;
        ?>
        <div class="stat-bar-row">
          <span class="stat-bar-label"><?php echo $b; ?>&#9733;</span>
          <div class="stat-bar-track"><div class="stat-bar-fill" style="width:<?php echo $persen; ?>%"></div></div>
          <span class="stat-bar-count"><?php echo $jumlah; ?></span>
        </div>
        <?php endfor; ?>
      </div>
    </div>
  </section>

  <!-- ===== FILTER & PENCARIAN ===== -->
  <section class="filter-bar">
    <form method="GET" action="dashboard.php" class="filter-form">
      <input type="text" name="cari" placeholder="Cari nama atau isi ulasan..." value="<?php echo htmlspecialchars($cari); ?>">
      <select name="rating">
        <option value="">Semua Rating</option>
        <?php for ($b = 5; $b >= 1; $b--): ?>
          <option value="<?php echo $b; ?>" <?php echo $filter_rating === $b ? 'selected' : ''; ?>><?php echo $b; ?> Bintang</option>
        <?php endfor; ?>
      </select>
      <button type="submit" class="btn-secondary">Terapkan</button>
      <?php if ($cari !== '' || $filter_rating_raw !== ''): ?>
        <a href="dashboard.php" class="btn-reset">Reset</a>
      <?php endif; ?>
    </form>
  </section>

  <!-- ===== TABEL ULASAN ===== -->
  <section class="table-wrap">
    <table class="ulasan-table">
      <thead>
        <tr>
          <th>Nama</th>
          <th>Rating</th>
          <th>Ulasan</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($data_ulasan) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($data_ulasan)): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['nama']); ?></td>
              <td class="td-rating"><?php echo str_repeat('&#9733;', intval($row['rating'])) . str_repeat('&#9734;', 5 - intval($row['rating'])); ?></td>
              <td class="td-komentar"><?php echo htmlspecialchars($row['komentar']); ?></td>
              <td class="td-tanggal"><?php echo date('d M Y, H:i', strtotime($row['tanggal'])); ?></td>
              <td class="td-aksi">
                <a href="edit_ulasan.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                <form method="POST" action="hapus_ulasan.php" onsubmit="return confirm('Yakin ingin menghapus ulasan dari <?php echo htmlspecialchars(addslashes($row['nama'])); ?>?');">
                  <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                  <button type="submit" class="btn-delete">Hapus</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="5" class="td-empty">Tidak ada ulasan yang cocok dengan filter ini.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </section>

  <!-- ===== PAGINATION ===== -->
  <?php if ($total_halaman > 1): ?>
  <nav class="pagination">
    <?php for ($p = 1; $p <= $total_halaman; $p++): ?>
      <a href="<?php echo buat_link_halaman($p, $cari, $filter_rating_raw); ?>" class="<?php echo $p === $halaman ? 'active' : ''; ?>"><?php echo $p; ?></a>
    <?php endfor; ?>
  </nav>
  <?php endif; ?>

</main>

</body>
</html>
