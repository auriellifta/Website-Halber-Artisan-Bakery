<?php
include '../backend/koneksi.php';

$pesan = "";

if (isset($_POST['kirim_ulasan'])) {
    $nama    = trim($_POST['nama']);
    $rating  = intval($_POST['rating']);
    $komentar = trim($_POST['komentar']);

    if (!empty($nama) && !empty($rating) && !empty($komentar)) {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO ulasan (nama, rating, komentar) VALUES (?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sis", $nama, $rating, $komentar);
            if (mysqli_stmt_execute($stmt)) {
                $pesan = "sukses";
            } else {
                $pesan = "gagal:" . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt);
        } else {
            $pesan = "gagal:Gagal menyiapkan sistem database.";
        }
    } else {
        $pesan = "validasi";
    }
}

$ambil_ulasan = mysqli_query($koneksi, "SELECT * FROM ulasan ORDER BY tanggal DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ulasan Pelanggan – Halber Artisan Bakery</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="page-transition" id="pageTransition"></div>

<!-- ===== NAVBAR ===== -->
<nav id="navbar" class="scrolled">
  <div class="nav-logo">
    <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M9 24 Q9 32 18 32 Q27 32 27 24" stroke="#f0e8d0" stroke-width="2.5" fill="none" stroke-linecap="round"/>
      <path d="M13 22 Q13 14 18 13 Q23 14 23 22" fill="#f0e8d0"/>
      <path d="M14 10 Q14 6 18 6 Q22 6 22 10" stroke="#f0e8d0" stroke-width="1.5" fill="none"/>
      <line x1="13" y1="5" x2="12" y2="2" stroke="#c9a84c" stroke-width="1.5" stroke-linecap="round"/>
      <line x1="18" y1="4" x2="18" y2="1" stroke="#c9a84c" stroke-width="1.5" stroke-linecap="round"/>
      <line x1="23" y1="5" x2="24" y2="2" stroke="#c9a84c" stroke-width="1.5" stroke-linecap="round"/>
    </svg>
    <div>
      <div class="nav-logo-text"><a href="index.html" style="text-decoration:none;color:inherit">HALBER</a></div>
      <span class="nav-logo-sub">Artisan Bakery</span>
    </div>
  </div>
  <div class="nav-links">
    <a href="index.html">Beranda</a>
    <a href="menu.html">Menu</a>
    <a href="ulasan.php" class="active">Ulasan</a>
    <a href="tentang.html">Tentang</a>
    <a href="lokasi.html">Lokasi</a>
  </div>
  <a href="menu.html" class="nav-order-btn">Pesan Sekarang</a>
  <button class="hamburger" id="hamburger" onclick="toggleMobileMenu()">
    <span></span><span></span><span></span>
  </button>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
  <a href="index.html" onclick="toggleMobileMenu()">Beranda</a>
  <a href="menu.html" onclick="toggleMobileMenu()">Menu</a>
  <a href="ulasan.php" onclick="toggleMobileMenu()">Ulasan</a>
  <a href="tentang.html" onclick="toggleMobileMenu()">Tentang</a>
  <a href="lokasi.html" onclick="toggleMobileMenu()">Lokasi</a>
  <a href="menu.html" class="mobile-order-btn" onclick="toggleMobileMenu()">Pesan Sekarang</a>
</div>

<!-- ===== PAGE HEADER ===== -->
<div class="page-header">
  <div class="page-header-content">
    <div class="hero-badge">Testimoni</div>
    <h1 class="page-title">Ulasan <span>Pelanggan</span></h1>
    <p>Bagikan pengalaman berharga Anda bersama kelembutan produk premium kami.</p>
  </div>
</div>

<!-- ===== MAIN CONTENT ===== -->
<div class="review-container">

  <?php if ($pesan === 'sukses'): ?>
  <div style="background:rgba(201,168,76,0.1);border:1px solid rgba(201,168,76,0.4);border-radius:12px;padding:16px 22px;margin-bottom:28px;color:#7a5c00;font-size:14px;font-weight:600;display:flex;align-items:center;gap:10px;">
    <span>&#10022;</span> Terima kasih! Ulasan Anda berhasil disimpan.
  </div>
  <?php elseif ($pesan === 'validasi'): ?>
  <div style="background:#fff5f5;border:1px solid #fcc;border-radius:12px;padding:16px 22px;margin-bottom:28px;color:#c53030;font-size:14px;font-weight:600;">
    &#9888; Semua kolom formulir wajib diisi!
  </div>
  <?php elseif (strpos($pesan, 'gagal') === 0): ?>
  <div style="background:#fff5f5;border:1px solid #fcc;border-radius:12px;padding:16px 22px;margin-bottom:28px;color:#c53030;font-size:14px;font-weight:600;">
    &#10007; <?php echo htmlspecialchars(str_replace('gagal:', '', $pesan)); ?>
  </div>
  <?php endif; ?>

  <!-- FORM ULASAN -->
  <div class="ulasan-form-box">
    <h2>Tulis Ulasan Anda</h2>
    <p>Ceritakan pengalaman Anda menikmati produk Halber Artisan Bakery.</p>
    <form action="ulasan.php" method="POST">
      <div class="form-group">
        <label for="nama">Nama Lengkap *</label>
        <input type="text" id="nama" name="nama" placeholder="Contoh: Budi Santoso" required maxlength="100">
      </div>
      <div class="form-group">
        <label for="rating">Berikan Nilai Bintang *</label>
        <select id="rating" name="rating" required>
          <option value="5">&#11088;&#11088;&#11088;&#11088;&#11088; </option>
          <option value="4">&#11088;&#11088;&#11088;&#11088; </option>
          <option value="3">&#11088;&#11088;&#11088; </option>
          <option value="2">&#11088;&#11088; </option>
          <option value="1">&#11088; </option>
        </select>
      </div>
      <div class="form-group">
        <label for="komentar">Tuliskan Ulasan Anda *</label>
        <textarea id="komentar" name="komentar" rows="4" placeholder="Ceritakan bagaimana rasa roti sisir atau produk Halber yang Anda nikmati..." required></textarea>
      </div>
      <button type="submit" name="kirim_ulasan" class="btn-submit">Kirim Ulasan</button>
    </form>
  </div>

  <!-- DAFTAR ULASAN -->
  <h3 class="ulasan-list-title">Ulasan Terbaru Pembeli</h3>

  <?php if(mysqli_num_rows($ambil_ulasan) > 0): ?>
    <?php while($row = mysqli_fetch_assoc($ambil_ulasan)): ?>
      <?php $bintang = intval($row['rating']); ?>
      <div class="ulasan-card">
        <div class="ulasan-star">
          <?php echo str_repeat("&#9733;", $bintang) . str_repeat("&#9734;", 5 - $bintang); ?>
        </div>
        <p class="ulasan-text">"<?php echo nl2br(htmlspecialchars($row['komentar'])); ?>"</p>
        <div class="ulasan-name">&mdash; <?php echo htmlspecialchars($row['nama']); ?></div>
        <div class="ulasan-date">Diposting: <?php echo date('d M Y, H:i', strtotime($row['tanggal'])); ?> WIB</div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <div class="no-ulasan">
      Belum ada ulasan di sistem. Jadilah yang pertama memberikan penilaian!
    </div>
  <?php endif; ?>

</div>

<!-- ===== FOOTER ===== -->
<footer class="footer-v1">
    <div class="footer-v1-container">
        <!-- Kolom 1: Brand -->
        <div class="footer-v1-brand">
            <div class="brand-name">HALBER</div>
            <p>Menyajikan roti artisan buatan tangan dengan bahan berkualitas premium. Dipanggang dengan cinta setiap hari untuk menemani momen spesial Anda.</p>
            <div class="footer-v1-social">
                <a href="https://www.instagram.com/halber.bakery?igsh=eXBrZzNwNzhwYWN6" aria-label="Instagram">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                </a>
                <a href="#" aria-label="Facebook">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                </a>
            </div>
        </div>

        <!-- Kolom 2: Tautan Cepat -->
        <div class="footer-v1-col">
            <h4>Jelajahi</h4>
            <ul class="footer-v1-links">
                <li><a href="index.html">Beranda</a></li>
                <li><a href="menu.html">Menu</a></li>
                <li><a href="ulasan.php">Ulasan</a></li>
                <li><a href="tentang.html">Tentang</a></li>
                <li><a href="lokasi.html">Lokasi</a></li>
            </ul>
        </div>

        <!-- Kolom 3: Kontak & Info -->
        <div class="footer-v1-col">
            <h4>Kunjungi Kami</h4>
            <div class="footer-v1-info">
                <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                <p>Tanjungpinang, Kepulauan Riau<br>Indonesia</p>
            </div>
            <div class="footer-v1-info">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <p>Setiap Hari<br>06:00 - 22:00 WIB</p>
            </div>
            <div class="footer-v1-info">
                <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                <p>085156986696</p>
            </div>
        </div>
    </div>

    <div class="footer-v1-bottom">
        <p>Copyright &copy; 2026 Halber Artisan Bakery. All rights reserved.</p>
    </div>
</footer>

<script src="script.js"></script>
</body>
</html>
