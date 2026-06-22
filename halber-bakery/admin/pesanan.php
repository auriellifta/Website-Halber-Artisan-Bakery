<?php
require '../backend/koneksi.php';
require '../backend/auth.php';
cek_login_admin();

// ===== Update status =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id     = intval($_POST['id']);
    $status = in_array($_POST['status'], ['pending','diproses','selesai']) ? $_POST['status'] : 'pending';
    $stmt   = mysqli_prepare($koneksi, "UPDATE pesanan SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'si', $status, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header('Location: pesanan.php?status=updated');
    exit;
}

// ===== Filter =====
$cari          = trim($_GET['cari'] ?? '');
$filter_status = in_array($_GET['filter'] ?? '', ['pending','diproses','selesai']) ? $_GET['filter'] : '';
$cari_param    = $cari !== '' ? "%$cari%" : '%';

$where = "WHERE (nama LIKE ? OR no_wa LIKE ? OR produk LIKE ?) AND (status = ? OR ? = '')";

// Total
$st = mysqli_prepare($koneksi, "SELECT COUNT(*) AS total FROM pesanan $where");
mysqli_stmt_bind_param($st, 'sssss', $cari_param, $cari_param, $cari_param, $filter_status, $filter_status);
mysqli_stmt_execute($st);
$total = mysqli_fetch_assoc(mysqli_stmt_get_result($st))['total'];
mysqli_stmt_close($st);

// Pagination
$per = 10;
$hal = max(1, intval($_GET['hal'] ?? 1));
$off = ($hal - 1) * $per;
$total_hal = max(1, (int) ceil($total / $per));
$hal = min($hal, $total_hal);
$off = ($hal - 1) * $per;

$stmt = mysqli_prepare($koneksi, "SELECT * FROM pesanan $where ORDER BY tanggal_pesan DESC LIMIT ? OFFSET ?");
mysqli_stmt_bind_param($stmt, 'sssssii', $cari_param, $cari_param, $cari_param, $filter_status, $filter_status, $per, $off);
mysqli_stmt_execute($stmt);
$data = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

// Stat
$stat_r = mysqli_query($koneksi, "SELECT status, COUNT(*) AS jml FROM pesanan GROUP BY status");
$stat   = ['pending' => 0, 'diproses' => 0, 'selesai' => 0];
while ($s = mysqli_fetch_assoc($stat_r)) $stat[$s['status']] = intval($s['jml']);

function link_hal($h, $cari, $filter_status) {
    $p = ['hal' => $h];
    if ($cari !== '') $p['cari'] = $cari;
    if ($filter_status !== '') $p['filter'] = $filter_status;
    return 'pesanan.php?' . http_build_query($p);
}

$notif = $_GET['status'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Pesanan – Halber Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin-style.css">
  <style>
    .badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 11.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .badge-pending  { background: #fff8e1; color: #856404; border: 1px solid #ffe082; }
    .badge-diproses { background: #e3f0ff; color: #0c447c; border: 1px solid #90caf9; }
    .badge-selesai  { background: rgba(47,125,79,0.10); color: #2f7d4f; border: 1px solid rgba(47,125,79,0.35); }

    .status-select {
      font-size: 12px;
      padding: 5px 8px;
      border-radius: 8px;
      border: 1.5px solid var(--cream-dark);
      cursor: pointer;
      background: var(--white);
    }
    .btn-status-save {
      font-size: 12px;
      padding: 6px 12px;
      border-radius: 8px;
      border: none;
      background: var(--blue);
      color: white;
      cursor: pointer;
      font-weight: 600;
    }
    .td-produk { max-width: 240px; font-size: 13px; color: var(--text-mid); }
    .admin-tabs {
      display: flex;
      gap: 6px;
      margin-bottom: 24px;
      border-bottom: 2px solid var(--cream);
      padding-bottom: 0;
    }
    .admin-tab {
      padding: 10px 18px;
      font-size: 13.5px;
      font-weight: 600;
      color: var(--text-mid);
      border-radius: 8px 8px 0 0;
      text-decoration: none;
      border: none;
      background: none;
      cursor: pointer;
      border-bottom: 2px solid transparent;
      margin-bottom: -2px;
    }
    .admin-tab.aktif {
      color: var(--blue);
      border-bottom-color: var(--blue);
      background: rgba(26,63,160,0.04);
    }
  </style>
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
    <a href="logout.php" class="btn-logout" onclick="return confirm('Keluar?');">Logout</a>
  </div>
</header>

<main class="admin-main">

  <!-- Tab navigasi -->
  <div class="admin-tabs">
    <a href="dashboard.php" class="admin-tab">Ulasan</a>
    <a href="pesanan.php"   class="admin-tab aktif">Pesanan</a>
  </div>

  <h1 class="admin-title">Kelola Pesanan</h1>

  <?php if ($notif === 'updated'): ?>
    <div class="alert alert-success">Status pesanan berhasil diperbarui.</div>
  <?php endif; ?>

  <!-- Statistik -->
  <section class="stat-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px;">
    <div class="stat-card">
      <span class="stat-label">Pending</span>
      <span class="stat-value" style="color:#856404;"><?php echo $stat['pending']; ?></span>
    </div>
    <div class="stat-card">
      <span class="stat-label">Diproses</span>
      <span class="stat-value" style="color:var(--blue);"><?php echo $stat['diproses']; ?></span>
    </div>
    <div class="stat-card">
      <span class="stat-label">Selesai</span>
      <span class="stat-value" style="color:var(--success);"><?php echo $stat['selesai']; ?></span>
    </div>
  </section>

  <!-- Filter -->
  <section class="filter-bar">
    <form method="GET" action="pesanan.php" class="filter-form">
      <input type="text" name="cari" placeholder="Cari nama, no. WA, atau produk..." value="<?php echo htmlspecialchars($cari); ?>">
      <select name="filter">
        <option value="">Semua Status</option>
        <option value="pending"  <?php echo $filter_status === 'pending'  ? 'selected' : ''; ?>>Pending</option>
        <option value="diproses" <?php echo $filter_status === 'diproses' ? 'selected' : ''; ?>>Diproses</option>
        <option value="selesai"  <?php echo $filter_status === 'selesai'  ? 'selected' : ''; ?>>Selesai</option>
      </select>
      <button type="submit" class="btn-secondary">Terapkan</button>
      <?php if ($cari !== '' || $filter_status !== ''): ?>
        <a href="pesanan.php" class="btn-reset">Reset</a>
      <?php endif; ?>
    </form>
  </section>

  <!-- Tabel -->
  <section class="table-wrap">
    <table class="ulasan-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Nama</th>
          <th>No. WA</th>
          <th>Produk</th>
          <th>Catatan</th>
          <th>Tanggal</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($data) > 0): ?>
          <?php $no = $off + 1; while ($row = mysqli_fetch_assoc($data)): ?>
            <tr>
              <td><?php echo $no++; ?></td>
              <td><?php echo htmlspecialchars($row['nama']); ?></td>
              <td>
                <a href="https://wa.me/<?php echo htmlspecialchars($row['no_wa']); ?>"
                   target="_blank"
                   style="color:var(--blue);font-size:13px;">
                  <?php echo htmlspecialchars($row['no_wa']); ?>
                </a>
              </td>
              <td class="td-produk"><?php echo htmlspecialchars($row['produk']); ?></td>
              <td class="td-komentar"><?php echo $row['catatan'] ? htmlspecialchars($row['catatan']) : '<span style="color:var(--cream-dark)">—</span>'; ?></td>
              <td class="td-tanggal"><?php echo date('d M Y, H:i', strtotime($row['tanggal_pesan'])); ?></td>
              <td>
                <form method="POST" action="pesanan.php" style="display:flex;gap:6px;align-items:center;">
                  <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                  <select name="status" class="status-select">
                    <option value="pending"  <?php echo $row['status'] === 'pending'  ? 'selected' : ''; ?>>Pending</option>
                    <option value="diproses" <?php echo $row['status'] === 'diproses' ? 'selected' : ''; ?>>Diproses</option>
                    <option value="selesai"  <?php echo $row['status'] === 'selesai'  ? 'selected' : ''; ?>>Selesai</option>
                  </select>
                  <button type="submit" name="update_status" class="btn-status-save">Simpan</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="7" class="td-empty">Tidak ada pesanan yang cocok.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </section>

  <!-- Pagination -->
  <?php if ($total_hal > 1): ?>
  <nav class="pagination">
    <?php for ($p = 1; $p <= $total_hal; $p++): ?>
      <a href="<?php echo link_hal($p, $cari, $filter_status); ?>" class="<?php echo $p === $hal ? 'active' : ''; ?>"><?php echo $p; ?></a>
    <?php endfor; ?>
  </nav>
  <?php endif; ?>

</main>

</body>
</html>
